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
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('business_activity_type_id')->nullable();

            $table->foreign('business_activity_type_id')
                ->references('id')
                ->on('business_activity_types')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('industries', function (Blueprint $table) {
            $table->dropForeign(['business_activity_type_id']);
            $table->dropColumn('business_activity_type_id');
        });

        Schema::dropIfExists('industries');
    }
};
