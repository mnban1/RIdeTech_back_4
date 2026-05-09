<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * S-004 マイページ (一般ユーザーのハブ画面)
 */
class MyPageController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // 直近の予約
        $upcomingReservation = $user->reservations()
            ->upcoming()
            ->with(['shop', 'course', 'staff'])
            ->orderBy('reserved_at')
            ->first();

        // 来店回数
        $visitCount = $user->reservations()
            ->where('status', \App\Models\Reservation::STATUS_COMPLETED)
            ->count();

        return view('mypage.index', compact('user', 'upcomingReservation', 'visitCount'));
    }
}
