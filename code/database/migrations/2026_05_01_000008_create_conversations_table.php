<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->dateTime('last_message_at')->nullable()->comment('一覧の並び替え用');
            $table->timestamps();
            $table->softDeletes();

            // 1ペアにつき1会話
            $table->unique(['user_id', 'shop_id'], 'uq_conversations_pair');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
