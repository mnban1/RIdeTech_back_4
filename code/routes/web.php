<?php

use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\ShopDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 予約管理マネージャー — ルート定義
|--------------------------------------------------------------------------
| 画面要件表 S-001 〜 S-013 / 機能要件表 F-001 〜 F-015 に対応
*/

/* ------------------------------------------------------------------ */
/*  PUBLIC (未ログイン可)                                              */
/* ------------------------------------------------------------------ */

// S-001 トップ画面 (店舗一覧)
Route::get('/', [HomeController::class, 'index'])->name('home');

// S-005 店舗詳細
Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');

/* ------------------------------------------------------------------ */
/*  AUTH                                                              */
/* ------------------------------------------------------------------ */

Route::middleware('guest')->group(function () {
    // S-002 ログイン
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // S-003 ユーザー登録
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')->name('logout');

/* ------------------------------------------------------------------ */
/*  USER (一般ユーザー / 要ログイン)                                    */
/* ------------------------------------------------------------------ */

Route::middleware('auth')->group(function () {

    // S-004 マイページ
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage');

    // S-006 予約登録 / S-007 予約一覧 (CRUD)
    Route::resource('reservations', ReservationController::class)
        ->except(['create']);
    Route::get('/shops/{shop}/reservations/create', [ReservationController::class, 'create'])
        ->name('reservations.create');

    // S-011 DM
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])
        ->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])
        ->name('messages.store');

    // F-014 口コミ投稿
    Route::post('/shops/{shop}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');
});

/* ------------------------------------------------------------------ */
/*  ADMIN (店舗管理者専用)                                              */
/* ------------------------------------------------------------------ */

Route::middleware(['auth', 'role:shop_admin'])->prefix('admin')->name('admin.')->group(function () {

    // S-013 店舗側マイページ
    Route::get('/dashboard', [ShopDashboardController::class, 'index'])
        ->name('dashboard');

    // S-008 カレンダー
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

    // S-009 顧客一覧 / S-010 顧客詳細 (F-010 顧客管理)
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{user}', [CustomerController::class, 'show'])
        ->name('customers.show');

    // F-012 メモ機能
    Route::post('/customers/{user}/memos', [CustomerController::class, 'storeMemo'])
        ->name('customers.memos.store');

    // S-012 売上画面 (F-015)
    Route::get('/sales', [SalesController::class, 'index'])->name('sales');

    // F-008 予約管理 (店舗側からの予約承認等)
    Route::patch('/reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])
        ->name('reservations.confirm');
});
