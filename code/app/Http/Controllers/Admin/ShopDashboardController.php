<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * S-013 店舗側マイページ (店舗管理者ダッシュボード)
 */
class ShopDashboardController extends Controller
{
    public function index(): View
    {
        $admin = Auth::user();
        $shop = $admin->shops()->firstOrFail();

        $today = now()->toDateString();

        $todayStats = [
            'reservations' => $shop->reservations()->whereDate('reserved_at', $today)->count(),
            'completed'    => $shop->reservations()
                                  ->whereDate('reserved_at', $today)
                                  ->where('status', Reservation::STATUS_COMPLETED)
                                  ->count(),
            'pending'      => $shop->reservations()
                                  ->whereDate('reserved_at', $today)
                                  ->where('status', Reservation::STATUS_PENDING)
                                  ->count(),
            'sales'        => $shop->reservations()
                                  ->whereDate('reserved_at', $today)
                                  ->where('status', Reservation::STATUS_COMPLETED)
                                  ->with('course:id,price')
                                  ->get()
                                  ->sum(fn($r) => $r->course->price ?? 0),
        ];

        $recentActivity = $shop->reservations()
            ->latest('updated_at')
            ->with('user:id,name')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('shop', 'todayStats', 'recentActivity'));
    }
}
