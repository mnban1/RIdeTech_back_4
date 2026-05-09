<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * S-008 カレンダー / F-009 予約日付表示
 */
class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $shop = Auth::user()->shops()->firstOrFail();

        $monthInput = $request->input('month', Carbon::now()->format('Y-m'));
        $month = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $staffId = $request->input('staff_id');

        $reservations = $shop->reservations()
            ->whereBetween('reserved_at', [$month, $monthEnd])
            ->when($staffId, fn($q) => $q->where('staff_id', $staffId))
            ->with(['user:id,name', 'course:id,name', 'staff:id,name'])
            ->get()
            ->groupBy(fn($r) => $r->reserved_at->format('Y-m-d'));

        return view('admin.calendar', compact(
            'shop', 'month', 'reservations', 'staffId'
        ));
    }
}
