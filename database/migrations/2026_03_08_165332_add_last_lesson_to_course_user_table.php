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
        Schema::table('course_user', function (Blueprint $table) {
            $table->foreignId('last_lesson_id')->nullable()->after('status')->constrained('lessons')->nullOnDelete();
            $table->foreignId('last_content_id')->nullable()->after('last_lesson_id')->constrained('lesson_contents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_content_id');
            $table->dropConstrainedForeignId('last_lesson_id');
        });
    }
};
