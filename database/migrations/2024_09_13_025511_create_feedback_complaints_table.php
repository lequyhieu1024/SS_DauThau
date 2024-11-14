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
        Schema::create('feedback_complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->default(1);
            $table->unsignedBigInteger('complaint_by');
            $table->unsignedBigInteger('responded_by')->nullable();
            $table->text('content');
            $table->text('response_content')->nullable();
            $table->enum('status', ['pending', 'responded'])->default('pending');
            $table->softDeletes();
            $table->timestamps();

            // FK
            $table->foreign('complaint_by')->references('id')->on('users');
            $table->foreign('responded_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_complaints');
    }
};
