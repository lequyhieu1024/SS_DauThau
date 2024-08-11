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
        Schema::table('staffs', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('user_id');
            $table->date('birthday')->nullable()->after('avatar');
            $table->integer('gender')->default(1)->after('birthday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('birthday');
            $table->dropColumn('gender');
        });
    }
};
