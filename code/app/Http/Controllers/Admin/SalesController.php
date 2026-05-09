<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * S-012 売上画面 / F-015 売上見込み管理
 */
class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $shop = Auth::user()->shops()->firstOrFail();

        $period = $request->input('period', 'this_month');
        [$start, $end] = match ($period) {
            'last_month' => [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ],
            'custom' => [
                Carbon::parse($request->input('start', Carbon::now()->startOfMonth())),
                Carbon::parse($request->input('end', Carbon::now()->endOfMonth())),
            ],
            default => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
        };

        $reservations = $shop->reservations()
            ->whereBetween('reserved_at', [$start, $end])
            ->with('course:id,name,price')
            ->get();

        // 確定売上 (完了)
        $confirmedSales = $reservations
            ->where('status', Reservation::STATUS_COMPLETED)
            ->sum(fn($r) => $r->course->price ?? 0);

        // 見込み売上 (予約ベース・キャンセル除く)
        $forecastSales = $reservations
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED])
            ->sum(fn($r) => $r->course->price ?? 0);

        // 日別売上推移
        $dailySales = $reservations
            ->where('status', Reservation::STATUS_COMPLETED)
            ->groupBy(fn($r) => $r->reserved_at->format('Y-m-d'))
            ->map(fn($group) => $group->sum(fn($r) => $r->course->price ?? 0));

        // コース別売上構成
        $byCourse = $reservations
            ->where('status', Reservation::STATUS_COMPLETED)
            ->groupBy('course.name')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum(fn($r) => $r->course->price ?? 0),
            ])
            ->sortByDesc('total');

        return view('admin.sales', compact(
            'shop', 'period', 'start', 'end',
            'confirmedSales', 'forecastSales', 'dailySales', 'byCourse'
        ));
    }
}
