@extends('layouts.app')

@section('title', 'ユーザー登録')

@section('content')
{{-- S-003 ユーザー登録画面 --}}

<div class="max-w-md mx-auto bg-white rounded-lg shadow p-8 mt-8">
    <h1 class="text-xl font-bold mb-6">新規アカウント作成</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="bg-gray-100 rounded p-1 flex">
            <label class="flex-1 text-center py-2 cursor-pointer rounded {{ old('role', 'user') === 'user' ? 'bg-gray-800 text-white' : '' }}">
                <input type="radio" name="role" value="user" {{ old('role', 'user') === 'user' ? 'checked' : '' }} class="hidden">
                一般ユーザー
            </label>
            <label class="flex-1 text-center py-2 cursor-pointer rounded {{ old('role') === 'shop_admin' ? 'bg-gray-800 text-white' : '' }}">
                <input type="radio" name="role" value="shop_admin" {{ old('role') === 'shop_admin' ? 'checked' : '' }} class="hidden">
                店舗管理者
            </label>
        </div>

        <div>
            <label class="block text-sm font-bold mb-1">お名前 <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="50"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-bold mb-1">メールアドレス <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-bold mb-1">電話番号</label>
            <input type="tel" name="phone_number" value="{{ old('phone_number') }}" maxlength="20"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-bold mb-1">パスワード <span class="text-red-500">*</span></label>
            <input type="password" name="password" required minlength="8"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-bold mb-1">パスワード（確認）<span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" required
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="terms" required>
            <span><a href="#" class="underline">利用規約</a>に同意する</span>
        </label>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded">
            登録する
        </button>
    </form>
</div>
@endsection
