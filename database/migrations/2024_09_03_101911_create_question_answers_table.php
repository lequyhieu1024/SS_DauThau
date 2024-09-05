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
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->default(1);
            $table->text('question_content');
            $table->text('answer_content')->nullable();
            $table->unsignedBigInteger('asked_by');
            $table->unsignedBigInteger('answered_by')->nullable();
            $table->enum('status', ['pending', 'answered'])->default('pending');
            $table->softDeletes();
            $table->timestamps();

            // FK
            $table->foreign('asked_by')->references('id')->on('users');
            $table->foreign('answered_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions_answers');
    }
};
