<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->unsignedInteger('price')->default(0)->comment('税込円');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // CHECK制約 (MySQL 8.0+)
        \DB::statement('ALTER TABLE courses ADD CONSTRAINT chk_courses_price CHECK (price >= 0)');
        \DB::statement('ALTER TABLE courses ADD CONSTRAINT chk_courses_duration CHECK (duration_minutes > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
