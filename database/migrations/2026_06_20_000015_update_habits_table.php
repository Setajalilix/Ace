<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_area_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->text('description')->nullable()->after('title');
            $table->string('frequency')->default('daily')->after('repeat_every');
            $table->json('target_days')->nullable()->after('frequency');
            $table->softDeletes();
        });

        Schema::table('habit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('habit_logs', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('spent_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('habit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('habit_logs', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });

        Schema::table('habits', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('life_area_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['description', 'frequency', 'target_days']);
        });
    }
};
