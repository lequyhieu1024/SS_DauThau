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
        Schema::table('enterprises', function (Blueprint $table) {
            $table->dropColumn('activity_id');
            $table->text('description')->nullable()->change();
            $table->decimal('avg_document_rating')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id')->after('organization_type');
        });
    }
};
