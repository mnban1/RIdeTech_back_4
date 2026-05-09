<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * F-014 口コミ投稿
 */
class ReviewController extends Controller
{
    public function store(Request $request, Shop $shop): RedirectResponse
    {
        $data = $request->validate([
            'reservation_id' => ['required', 'exists:reservations,id'],
            'rating'         => ['required', 'integer', 'between:1,5'],
            'title'          => ['nullable', 'string', 'max:100'],
            'content'        => ['required', 'string', 'max:2000'],
        ]);

        $reservation = Reservation::findOrFail($data['reservation_id']);

        // 自分の予約 & 完了済み & 当該店舗 のみ投稿可能
        abort_unless($reservation->user_id === Auth::id(), 403);
        abort_unless($reservation->shop_id === $shop->id, 403);
        abort_unless($reservation->status === Reservation::STATUS_COMPLETED, 403);

        try {
            $shop->reviews()->create([
                'user_id'        => Auth::id(),
                'reservation_id' => $reservation->id,
                'rating'         => $data['rating'],
                'title'          => $data['title'] ?? null,
                'content'        => $data['content'],
                'published_on'   => now()->toDateString(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // UNIQUE違反 = 既に口コミ投稿済み
            return back()->withErrors([
                'reservation_id' => 'この予約に対する口コミは既に投稿されています。',
            ]);
        }

        return redirect()
            ->route('shops.show', $shop)
            ->with('success', '口コミを投稿しました。');
    }
}
