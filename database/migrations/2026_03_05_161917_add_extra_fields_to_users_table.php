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
        Schema::table('users', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('password');
            $table->foreignId('primary_branch_id')->nullable()->constrained('branches')->after('status');
            $table->foreignId('department_id')->nullable()->constrained('departments')->after('primary_branch_id');
            $table->string('position')->nullable()->after('department_id');
            $table->foreignId('created_by')->nullable()->constrained('users')->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
