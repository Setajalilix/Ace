<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('p1_total')->default(0);
            $table->unsignedSmallInteger('p1_completed')->default(0);
            $table->unsignedSmallInteger('p2_total')->default(0);
            $table->unsignedSmallInteger('p2_completed')->default(0);
            $table->string('result')->default('failed');
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_scores');
    }
};
