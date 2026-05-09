<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 顧客メモ (店舗管理者が顧客対応時に残すメモ)
 */
class Memo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'shop_id',
        'author_id',
        'content',
    ];

    /** 対象顧客 */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** 記録した店舗 */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /** 記入者 (店舗管理者) */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
