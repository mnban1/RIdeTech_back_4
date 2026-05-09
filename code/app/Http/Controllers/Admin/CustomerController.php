<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * S-009 顧客一覧 / S-010 顧客詳細 / F-010 顧客管理 / F-011 来店履歴 / F-012 メモ
 */
class CustomerController extends Controller
{
    /** S-009 顧客一覧 */
    public function index(Request $request): View
    {
        $shop = Auth::user()->shops()->firstOrFail();

        // 当店舗で予約したことのある顧客を抽出
        $query = User::query()
            ->whereHas('reservations', fn($q) => $q->where('shop_id', $shop->id))
            ->withCount(['reservations as visit_count' => fn($q) => $q->where('shop_id', $shop->id)])
            ->withMax(['reservations as last_visited_at' => fn($q) => $q->where('shop_id', $shop->id)], 'reserved_at');

        if ($keyword = $request->input('q')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('phone_number', 'like', "%{$keyword}%");
            });
        }

        match ($request->input('sort', 'recent')) {
            'visits'  => $query->orderByDesc('visit_count'),
            default   => $query->orderByDesc('last_visited_at'),
        };

        $customers = $query->paginate(20);

        return view('admin.customers.index', compact('customers', 'shop'));
    }

    /** S-010 顧客詳細 */
    public function show(User $user): View
    {
        $shop = Auth::user()->shops()->firstOrFail();

        // 当店舗での予約履歴
        $reservations = $user->reservations()
            ->where('shop_id', $shop->id)
            ->with(['course:id,name,price', 'staff:id,name'])
            ->latest('reserved_at')
            ->get();

        $memos = Memo::where('user_id', $user->id)
            ->where('shop_id', $shop->id)
            ->with('author:id,name')
            ->latest()
            ->get();

        $stats = [
            'visit_count'  => $reservations->where('status', 'completed')->count(),
            'total_spent'  => $reservations->where('status', 'completed')
                                           ->sum(fn($r) => $r->course->price ?? 0),
        ];

        return view('admin.customers.show', compact(
            'user', 'shop', 'reservations', 'memos', 'stats'
        ));
    }

    /** F-012 メモ追加 */
    public function storeMemo(Request $request, User $user): RedirectResponse
    {
        $shop = Auth::user()->shops()->firstOrFail();

        $data = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        Memo::create([
            'user_id'   => $user->id,
            'shop_id'   => $shop->id,
            'author_id' => Auth::id(),
            'content'   => $data['content'],
        ]);

        return back()->with('success', 'メモを追加しました。');
    }
}
