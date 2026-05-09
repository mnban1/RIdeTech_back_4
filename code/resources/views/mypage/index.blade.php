@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
{{-- S-004 マイページ --}}

<div class="bg-gray-100 rounded-lg p-6 mb-6 flex items-center gap-4">
    <div class="w-16 h-16 rounded-full bg-gray-400 flex items-center justify-center text-white text-2xl font-bold">
        {{ mb_substr($user->name, 0, 1) }}
    </div>
    <div>
        <div class="text-lg font-bold">{{ $user->name }} 様</div>
        <div class="text-sm text-gray-500">{{ $user->email }}</div>
        <div class="text-sm text-gray-500">来店回数: {{ $visitCount }}回</div>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <a href="{{ route('reservations.index') }}" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md">
        <div class="text-2xl">📋</div>
        <div class="text-sm font-bold mt-1">予約一覧</div>
    </a>
    <a href="{{ route('reservations.index', ['tab' => 'past']) }}" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md">
        <div class="text-2xl">🕐</div>
        <div class="text-sm font-bold mt-1">来店履歴</div>
    </a>
    <a href="{{ route('messages.index') }}" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md">
        <div class="text-2xl">💬</div>
        <div class="text-sm font-bold mt-1">DM</div>
    </a>
    <a href="#" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md">
        <div class="text-2xl">⭐</div>
        <div class="text-sm font-bold mt-1">口コミ</div>
    </a>
</div>

@if($upcomingReservation)
<section class="mb-6">
    <h2 class="text-lg font-bold mb-3">直近の予約</h2>
    <div class="bg-amber-50 border border-amber-300 rounded-lg p-4">
        <div class="font-bold">{{ $upcomingReservation->shop->name }}</div>
        <div class="text-sm mt-1">📅 {{ $upcomingReservation->reserved_at->format('Y/m/d (D) H:i') }} 〜</div>
        <div class="text-sm text-gray-600">
            コース: {{ $upcomingReservation->course->name }} ({{ $upcomingReservation->course->duration_minutes }}分)
        </div>
        @if($upcomingReservation->staff)
        <div class="text-sm text-gray-600">担当: {{ $upcomingReservation->staff->name }}</div>
        @endif
        <a href="{{ route('reservations.show', $upcomingReservation) }}" class="inline-block mt-3 text-sm text-blue-600 hover:underline">
            詳細を見る →
        </a>
    </div>
</section>
@endif
@endsection
