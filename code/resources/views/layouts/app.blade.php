<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '予約管理マネージャー')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Hiragino Sans", "Yu Gothic", sans-serif; }
    </style>
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="bg-gray-800 text-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-lg font-bold">予約管理マネージャー</a>

            <nav class="flex items-center gap-4 text-sm">
                @auth
                    @if(auth()->user()->isShopAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-300">ダッシュボード</a>
                        <a href="{{ route('admin.calendar') }}" class="hover:text-blue-300">カレンダー</a>
                        <a href="{{ route('admin.customers.index') }}" class="hover:text-blue-300">顧客管理</a>
                        <a href="{{ route('admin.sales') }}" class="hover:text-blue-300">売上</a>
                    @else
                        <a href="{{ route('mypage') }}" class="hover:text-blue-300">マイページ</a>
                        <a href="{{ route('reservations.index') }}" class="hover:text-blue-300">予約一覧</a>
                    @endif
                    <a href="{{ route('messages.index') }}" class="hover:text-blue-300">DM</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-blue-300">ログアウト</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-blue-300">ログイン</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded">新規登録</a>
                @endauth
            </nav>
        </div>
    </header>

    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-2 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main class="max-w-6xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <footer class="bg-gray-100 mt-12 py-6 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} 予約管理マネージャー
    </footer>

    @stack('scripts')
</body>
</html>
