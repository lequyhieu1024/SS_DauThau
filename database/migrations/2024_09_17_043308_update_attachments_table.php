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
        Schema::table('attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->dropColumn('package_id');
            $table->unsignedBigInteger('project_id')->after('user_id');
            $table->string('name', 255)->change();
            $table->string('path', 255)->change();
            $table->string('type', 255)->change();
            $table->unsignedBigInteger('size')->change();
            $table->boolean('is_active')->default(true)->after('size');
            $table->softDeletes()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->integer('user_id')->change();
            $table->integer('package_id')->after('user_id');
            $table->dropColumn('project_id');
            $table->string('name')->change();
            $table->string('path')->change();
            $table->string('type')->change();
            $table->integer('size')->change();
            $table->dropColumn('is_active');
            $table->dropSoftDeletes();
        });
    }
};
