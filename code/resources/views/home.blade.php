@extends('layouts.app')

@section('title', 'トップ - 予約管理マネージャー')

@section('content')
{{-- S-001 トップ画面: 店舗一覧 --}}

<form method="GET" action="{{ route('home') }}" class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-wrap gap-3">
        <input type="text" name="q" value="{{ $keyword ?? '' }}"
               placeholder="🔍 店舗名・エリアで検索"
               class="flex-1 min-w-[240px] border border-gray-300 rounded-full px-4 py-2 text-sm">

        <select name="category" class="border border-gray-300 rounded-full px-3 py-2 text-sm">
            <option value="">すべてのカテゴリ</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}"
                    {{ request('category') === $category->slug ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="sort" class="border border-gray-300 rounded-full px-3 py-2 text-sm">
            <option value="recommend" {{ $sort === 'recommend' ? 'selected' : '' }}>おすすめ順</option>
            <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>評価順</option>
            <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>新着順</option>
        </select>

        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">検索</button>
    </div>
</form>

<h1 class="text-xl font-bold mb-4">店舗一覧 ({{ $shops->total() }} 件)</h1>

<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    @forelse($shops as $shop)
        <a href="{{ route('shops.show', $shop) }}"
           class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden flex flex-col">

            <div class="h-32 bg-gray-200 flex items-center justify-center text-4xl">
                @if($shop->thumbnail_url)
                    <img src="{{ $shop->thumbnail_url }}" alt="{{ $shop->name }}" class="w-full h-full object-cover">
                @else
                    🏪
                @endif
            </div>

            <div class="p-4 flex-1">
                <h2 class="font-bold mb-1">{{ $shop->name }}</h2>
                <div class="text-sm text-amber-600">
                    {{ str_repeat('★', round($shop->reviews_avg_rating ?? 0)) }}{{ str_repeat('☆', 5 - round($shop->reviews_avg_rating ?? 0)) }}
                    <span class="text-gray-500">
                        {{ number_format($shop->reviews_avg_rating ?? 0, 1) }} ({{ $shop->reviews_count }})
                    </span>
                </div>
                <div class="text-xs text-gray-500 mt-2">
                    📍 {{ $shop->address }}
                </div>
                <div class="text-xs text-gray-500">
                    🏷 {{ $shop->category->name }}
                </div>
            </div>
        </a>
    @empty
        <p class="col-span-full text-center text-gray-500 py-8">
            該当する店舗が見つかりませんでした。
        </p>
    @endforelse
</div>

<div class="mt-6">
    {{ $shops->links() }}
</div>
@endsection
