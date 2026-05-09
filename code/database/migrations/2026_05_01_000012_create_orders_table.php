<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('shop_id')->constrained('shops')->restrictOnDelete();
            $table->unsignedInteger('total_price')->default(0);
            $table->enum('status', ['pending', 'paid', 'shipped', 'delivered', 'cancelled'])
                  ->default('pending');
            $table->dateTime('ordered_at')->useCurrent();
            $table->dateTime('shipped_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'shop_id']);
            $table->index('status');
        });

        \DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_total CHECK (total_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
