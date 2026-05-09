<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Reservation;
use App\Models\Shop;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * F-005〜F-008 予約 CRUD
 *
 * S-006 予約登録 / S-007 予約一覧
 */
class ReservationController extends Controller
{
    /** S-007 予約一覧 (一般ユーザーの自分の予約) */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'upcoming');

        $query = Auth::user()->reservations()
            ->with(['shop', 'course', 'staff'])
            ->latest('reserved_at');

        match ($tab) {
            'past'      => $query->past(),
            'cancelled' => $query->where('status', Reservation::STATUS_CANCELLED),
            default     => $query->upcoming(),
        };

        $reservations = $query->paginate(15);

        return view('reservations.index', compact('reservations', 'tab'));
    }

    /** S-006 予約登録フォーム */
    public function create(Shop $shop, Request $request): View
    {
        $course = Course::findOrFail($request->input('course_id'));
        abort_unless($course->shop_id === $shop->id, 404);

        $shop->load(['staffs' => fn($q) => $q->active()]);

        return view('reservations.create', compact('shop', 'course'));
    }

    /** F-005 予約作成 */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shop_id'     => ['required', 'exists:shops,id'],
            'course_id'   => ['required', 'exists:courses,id'],
            'staff_id'    => ['nullable', 'exists:staffs,id'],
            'reserved_at' => ['required', 'date', 'after:now'],
            'note'        => ['nullable', 'string', 'max:1000'],
        ]);

        $course = Course::findOrFail($data['course_id']);
        $data['user_id'] = Auth::id();
        $data['end_at'] = \Carbon\Carbon::parse($data['reserved_at'])
                             ->addMinutes($course->duration_minutes);

        try {
            $reservation = DB::transaction(function () use ($data) {
                return Reservation::create($data);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // UNIQUE制約違反 = ダブルブッキング
            if (str_contains($e->getMessage(), 'uq_reservations_staff_time')) {
                return back()
                    ->withInput()
                    ->withErrors(['reserved_at' => 'この時間枠は既に予約が入っています。別の時間を選択してください。']);
            }
            throw $e;
        }

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', '予約が完了しました。');
    }

    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);
        $reservation->load(['shop', 'course', 'staff']);
        return view('reservations.show', compact('reservation'));
    }

    /** F-006 予約編集 */
    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        $data = $request->validate([
            'reserved_at' => ['required', 'date'],
            'staff_id'    => ['nullable', 'exists:staffs,id'],
            'note'        => ['nullable', 'string', 'max:1000'],
        ]);

        $data['end_at'] = \Carbon\Carbon::parse($data['reserved_at'])
                             ->addMinutes($reservation->course->duration_minutes);
        $data['status'] = Reservation::STATUS_CHANGE_REQUESTED;

        $reservation->update($data);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', '変更申請を受け付けました。');
    }

    /** F-007 予約削除 (論理キャンセル) */
    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->authorize('delete', $reservation);

        $reservation->cancel();

        return redirect()
            ->route('reservations.index', ['tab' => 'cancelled'])
            ->with('success', '予約をキャンセルしました。');
    }

    /** 店舗管理者による予約承認 */
    public function confirm(Reservation $reservation): RedirectResponse
    {
        abort_unless(Auth::user()->isShopAdmin(), 403);
        abort_unless($reservation->shop->user_id === Auth::id(), 403);

        $reservation->update(['status' => Reservation::STATUS_CONFIRMED]);

        return back()->with('success', '予約を承認しました。');
    }
}
