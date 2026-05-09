<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->nullOnDelete();
            $table->dateTime('reserved_at');
            $table->dateTime('end_at');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'change_requested'])
                  ->default('pending');
            $table->text('note')->nullable();
            $table->date('visited_on')->nullable()->comment('来店履歴用');
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // ダブルブッキング防止
            $table->unique(['staff_id', 'reserved_at'], 'uq_reservations_staff_time');
            $table->index(['shop_id', 'reserved_at']);
            $table->index('status');
        });

        \DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_time CHECK (end_at > reserved_at)');
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
