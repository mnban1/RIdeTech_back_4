<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('店舗オーナー (users.role=shop_admin)');
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('postal_code', 8)->nullable();
            $table->string('address', 255);
            $table->string('phone_number', 20);
            $table->time('opening_time')->default('10:00:00');
            $table->time('closing_time')->default('20:00:00');
            $table->string('closed_days', 20)->nullable()->comment('例: tue,wed');
            $table->string('thumbnail_url', 255)->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
