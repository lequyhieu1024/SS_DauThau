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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->default(null);
            $table->unsignedBigInteger('bidding_field_id');
            $table->unsignedBigInteger('funding_source_id');
            $table->unsignedBigInteger('enterprise_id');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('selection_method_id');
            $table->unsignedBigInteger('file_id');
            $table->string('name',255);
            $table->date('release_date');
            $table->string('decision_issuace');
            $table->string('owner_representative');
            $table->string('tenderer_representative');
            $table->string('location');
            $table->decimal('amount',20,2);
            $table->text('description')->nullable();
            $table->date('submission_deadline');
            $table->decimal('invest_total',20,2);
            $table->date('tender_date');
            $table->text('technical_requirements')->nullable();
            $table->date('end_bidding');
            $table->date('start_bidding');


            $table->timestamps();
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
