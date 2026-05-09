@extends('layouts.app')

@section('title', $shop->name)

@section('content')
{{-- S-005 店舗詳細 --}}

<div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="h-48 bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center text-6xl">
        @if($shop->thumbnail_url)
            <img src="{{ $shop->thumbnail_url }}" alt="{{ $shop->name }}" class="w-full h-full object-cover">
        @else
            🏪
        @endif
    </div>

    <div class="p-6">
        <h1 class="text-2xl font-bold">{{ $shop->name }}</h1>
        <div class="text-amber-600 mt-2">
            {{ str_repeat('★', round($shop->reviews_avg_rating ?? 0)) }}{{ str_repeat('☆', 5 - round($shop->reviews_avg_rating ?? 0)) }}
            <span class="text-sm text-gray-500">
                {{ number_format($shop->reviews_avg_rating ?? 0, 1) }} ({{ $shop->reviews_count }}件の口コミ)
            </span>
        </div>

        <div class="bg-gray-50 rounded p-4 mt-4 space-y-1 text-sm">
            <div>📍 〒{{ $shop->postal_code }} {{ $shop->address }}</div>
            <div>🕐 {{ $shop->opening_time->format('H:i') }} - {{ $shop->closing_time->format('H:i') }}
                @if($shop->closed_days)（定休日: {{ $shop->closed_days }}）@endif
            </div>
            <div>📞 {{ $shop->phone_number }}</div>
            <div>🏷 {{ $shop->category->name }}</div>
        </div>

        @if($shop->description)
            <p class="mt-4 text-sm text-gray-700 whitespace-pre-line">{{ $shop->description }}</p>
        @endif
    </div>
</div>

{{-- コース一覧 --}}
<section class="mt-6">
    <h2 class="text-lg font-bold mb-3">コース一覧</h2>
    <div class="bg-white rounded-lg shadow divide-y">
        @forelse($shop->courses as $course)
            <div class="p-4 flex items-center justify-between">
                <div>
                    <div class="font-bold">{{ $course->name }}</div>
                    <div class="text-sm text-gray-500">{{ $course->formatted_price }} / {{ $course->duration_minutes }}分</div>
                    @if($course->description)
                        <div class="text-xs text-gray-500 mt-1">{{ $course->description }}</div>
                    @endif
                </div>
                @auth
                    <a href="{{ route('reservations.create', ['shop' => $shop, 'course_id' => $course->id]) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-bold">
                        予約する
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-blue-600 text-sm font-bold">
                        ログインして予約 →
                    </a>
                @endauth
            </div>
        @empty
            <p class="p-4 text-gray-500 text-sm">公開中のコースがありません。</p>
        @endforelse
    </div>
</section>

{{-- スタッフ一覧 --}}
@if($shop->staffs->isNotEmpty())
<section class="mt-6">
    <h2 class="text-lg font-bold mb-3">スタッフ</h2>
    <div class="grid gap-3 md:grid-cols-3">
        @foreach($shop->staffs as $staff)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="font-bold">{{ $staff->name }}</div>
                @if($staff->position)<div class="text-xs text-gray-500">{{ $staff->position }}</div>@endif
                @if($staff->bio)<p class="text-xs text-gray-700 mt-2">{{ $staff->bio }}</p>@endif
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- 口コミ一覧 --}}
@if($shop->reviews->isNotEmpty())
<section class="mt-6">
    <h2 class="text-lg font-bold mb-3">口コミ ({{ $shop->reviews_count }}件)</h2>
    <div class="space-y-3">
        @foreach($shop->reviews as $review)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div class="font-bold text-sm">{{ $review->user->name }}</div>
                    <div class="text-amber-600 text-sm">{{ $review->stars }}</div>
                </div>
                @if($review->title)<div class="font-bold mt-1">{{ $review->title }}</div>@endif
                <p class="text-sm text-gray-700 mt-1">{{ $review->content }}</p>
                <div class="text-xs text-gray-400 mt-2">{{ $review->published_on->format('Y/m/d') }}</div>
            </div>
        @endforeach
    </div>
</section>
@endif
@endsection
