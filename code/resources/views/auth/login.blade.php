@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
{{-- S-002 ログイン画面 --}}

<div class="max-w-md mx-auto bg-white rounded-lg shadow p-8 mt-8">
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-gray-300 rounded-2xl mx-auto flex items-center justify-center text-3xl mb-3">🔑</div>
        <h1 class="text-xl font-bold">予約管理マネージャー</h1>
        <p class="text-sm text-gray-500 mt-1">アカウントにログイン</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-bold mb-1">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-bold mb-1">パスワード</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember"> ログイン状態を保持
            </label>
            <a href="#" class="text-blue-600 hover:underline">パスワード忘れ?</a>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded">
            ログイン
        </button>

        <div class="text-center text-xs text-gray-400">— または —</div>

        <a href="{{ route('register') }}"
           class="block w-full text-center border border-blue-600 text-blue-600 font-bold py-2 rounded hover:bg-blue-50">
            新規登録はこちら
        </a>
    </form>
</div>
@endsection
