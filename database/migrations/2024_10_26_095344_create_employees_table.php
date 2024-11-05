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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enterprise_id');
            $table->string('code')->unique();
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('gender');
            $table->string('taxcode')->nullable();
            $table->enum('education_level', ['primary_school', 'secondary_school', 'high_school', 'college', 'university', 'after_university']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary',20, 2)->nullable();
            $table->string('address')->nullable();
            $table->enum('status', ['doing', 'pause', 'leave']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
