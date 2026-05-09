<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 店舗カテゴリ (美容室 / ネイル / エステ 等)
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug'];

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    /** URL用識別子で検索するためのキー設定 */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
