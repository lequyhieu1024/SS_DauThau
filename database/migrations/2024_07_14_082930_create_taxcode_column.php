<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('code')->nullable()->change(); // Ensure the column type matches
            });
    
            DB::statement('ALTER TABLE users CHANGE code taxcode VARCHAR(255) NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('code')->nullable()->change(); // Ensure the column type matches
            });
    
            DB::statement('ALTER TABLE users CHANGE code taxcode VARCHAR(255) NULL');
        });
    }
};
