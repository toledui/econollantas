<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('completion_method', ['video_90', 'manual', 'document_open', 'admin_override'])->default('manual');
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']);
        });

        Schema::create('lesson_video_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('provider', ['youtube'])->default('youtube');
            $table->integer('watched_seconds')->default(0);
            $table->integer('last_position_seconds')->default(0);
            $table->integer('duration_seconds')->nullable();
            $table->decimal('percent_watched', 5, 2)->nullable();
            $table->timestamp('last_event_at')->useCurrent();
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']);
        });

        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('percent_completed', 5, 2)->default(0);
            $table->integer('lessons_completed')->default(0);
            $table->integer('lessons_total')->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_progress');
        Schema::dropIfExists('lesson_video_progress');
        Schema::dropIfExists('lesson_progress');
    }
};
