<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('color')->default('#6366f1');

            // checkbox | timer
            $table->enum('type', ['checkbox', 'timer']);

            // هر چند روز تکرار شود
            $table->integer('repeat_every')->default(1);

            // هدف اولیه تایمر
            $table->integer('target_minutes')->nullable();

            // روزانه چقدر اضافه شود
            $table->integer('daily_increment')->default(0);

            // آیا time block دارد؟
            $table->boolean('has_time_block')->default(false);

            // ساعت مشخص
            $table->time('block_time')->nullable();

            $table->date('start_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
