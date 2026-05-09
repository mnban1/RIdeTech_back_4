<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * S-001 トップ画面 (店舗一覧)
 * F-004 店舗一覧表示
 */
class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Shop::query()
            ->published()
            ->with(['category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // キーワード検索
        if ($keyword = $request->input('q')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        // カテゴリ絞り込み
        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        // 並び替え
        $sort = $request->input('sort', 'recommend');
        match ($sort) {
            'rating' => $query->orderByDesc('reviews_avg_rating'),
            'newest' => $query->orderByDesc('created_at'),
            default  => $query->orderByDesc('reviews_count'),
        };

        $shops = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('home', compact('shops', 'categories', 'keyword', 'sort'));
    }
}
