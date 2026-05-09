<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 予約 (本アプリの中核エンティティ)
 *
 * ダブルブッキング防止: (staff_id, reserved_at) UNIQUE制約
 * end_at > reserved_at: CHECK制約
 */
class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CHANGE_REQUESTED = 'change_requested';

    protected $fillable = [
        'user_id',
        'shop_id',
        'course_id',
        'staff_id',
        'reserved_at',
        'end_at',
        'status',
        'note',
        'visited_on',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime',
            'end_at' => 'datetime',
            'visited_on' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    /* ---------- リレーション ---------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /* ---------- スコープ ---------- */

    public function scopeUpcoming($query)
    {
        return $query->where('reserved_at', '>=', now())
                     ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function scopePast($query)
    {
        return $query->whereNotNull('visited_on')
                     ->orWhere('status', self::STATUS_COMPLETED);
    }

    public function scopeForShop($query, int $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    /* ---------- ビジネスロジック ---------- */

    /** 予約をキャンセル */
    public function cancel(): bool
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();
        return $this->save();
    }

    /** 来店完了 */
    public function complete(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        $this->visited_on = now()->toDateString();
        return $this->save();
    }
}
