<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE course_user MODIFY COLUMN status ENUM('not_started', 'in_progress', 'completed', 'revoked') DEFAULT 'not_started'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE course_user MODIFY COLUMN status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started'");
    }
};
