<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * ユーザー (一般ユーザー / 店舗管理者を共通管理)
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property 'user'|'shop_admin' $role
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ---------- ロール判定 ---------- */

    public function isShopAdmin(): bool
    {
        return $this->role === 'shop_admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'user';
    }

    /* ---------- リレーション ---------- */

    /** 店舗管理者として所有する店舗 */
    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    /** 自分が予約者として作成した予約 */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** 自分が顧客として記録されたメモ */
    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class);
    }

    /** 自分が記入したメモ (店舗管理者として) */
    public function authoredMemos(): HasMany
    {
        return $this->hasMany(Memo::class, 'author_id');
    }

    /** 関与しているDM会話 (顧客側) */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /** 自分が送信したDMメッセージ */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
