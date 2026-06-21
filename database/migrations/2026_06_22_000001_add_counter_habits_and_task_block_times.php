<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->unsignedInteger('target_count')->nullable()->after('target_minutes');
        });

        Schema::table('habit_logs', function (Blueprint $table) {
            $table->unsignedInteger('count')->default(0)->after('spent_minutes');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->time('block_end_time')->nullable()->after('scheduled_time');
            $table->boolean('daily_time_block')->default(false)->after('block_end_time');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE habits MODIFY type VARCHAR(20) NOT NULL DEFAULT 'checkbox'");
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['block_end_time', 'daily_time_block']);
        });

        Schema::table('habit_logs', function (Blueprint $table) {
            $table->dropColumn('count');
        });

        Schema::table('habits', function (Blueprint $table) {
            $table->dropColumn('target_count');
        });
    }
};
