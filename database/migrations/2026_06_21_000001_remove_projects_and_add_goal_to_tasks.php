<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('goal_id')->nullable()->after('life_area_id')->constrained()->nullOnDelete();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('goal_id')->nullable()->after('life_area_id')->constrained()->nullOnDelete();
        });

        Schema::table('time_blocks', function (Blueprint $table) {
            $table->foreignId('goal_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        if (Schema::hasTable('projects')) {
            DB::table('tasks')
                ->whereNotNull('project_id')
                ->update([
                    'goal_id' => DB::raw('(SELECT goal_id FROM projects WHERE projects.id = tasks.project_id)'),
                ]);

            DB::table('events')
                ->whereNotNull('project_id')
                ->update([
                    'goal_id' => DB::raw('(SELECT goal_id FROM projects WHERE projects.id = events.project_id)'),
                ]);

            DB::table('time_blocks')
                ->whereNotNull('project_id')
                ->update([
                    'goal_id' => DB::raw('(SELECT goal_id FROM projects WHERE projects.id = time_blocks.project_id)'),
                ]);
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('time_blocks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::dropIfExists('projects');
    }

    public function down(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_area_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('outcome')->nullable();
            $table->date('deadline')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        foreach (['tasks', 'events', 'time_blocks'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            });

            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['goal_id']);
                $table->dropColumn('goal_id');
            });
        }
    }
};
