<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()
                  ->comment('対象顧客');
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('記入者 (店舗管理者)');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'shop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
