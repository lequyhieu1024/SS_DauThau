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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('funding_source_id');
            $table->unsignedBigInteger('tenderer_id');
            $table->unsignedBigInteger('investor_id');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('selection_method_id');
            $table->unsignedBigInteger('parent_id')->nullable()->default(null);
            $table->string('decision_number_issued');
            $table->string('name');
            $table->boolean('is_domestic');
            $table->string('location');
            $table->decimal('amount', 20, 2);
            $table->decimal('total_amount', 20, 2);
            $table->string('description')->nullable();
            $table->enum('submission_method', ['online', 'in_person']);
            $table->text('receiving_place')->nullable();
            $table->timestamp('bid_submission_start')->nullable();
            $table->timestamp('bid_submission_end')->nullable();
            $table->timestamp('bid_opening_date')->nullable();
            $table->date('start_time')->nullable();
            $table->date('end_time')->nullable();
            $table->timestamp('approve_at')->nullable();
            $table->string('decision_number_approve')->nullable();
            $table->tinyInteger('status')->default(5);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
