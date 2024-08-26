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
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->string('representative_name');
            $table->string('address');
            $table->string('phone');
            $table->string('website');
            $table->string('description');
            $table->date('establish_date');
            $table->integer('avg_document_rating'); //điểm đánh giá hồ sơ trung bình
            $table->integer('field_active_id');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_blacklist')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};
