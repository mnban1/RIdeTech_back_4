<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\View\View;

/**
 * S-005 店舗詳細
 */
class ShopController extends Controller
{
    public function show(Shop $shop): View
    {
        // 非公開ショップは404
        abort_unless($shop->is_published, 404);

        $shop->load([
            'category',
            'staffs' => fn($q) => $q->active(),
            'courses' => fn($q) => $q->published(),
            'reviews' => fn($q) => $q->with('user')->latest()->limit(10),
        ]);

        $shop->loadCount('reviews');
        $shop->loadAvg('reviews', 'rating');

        return view('shops.show', compact('shop'));
    }
}
