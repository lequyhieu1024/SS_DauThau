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
        Schema::create('bidding_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description');
            $table->integer('code')->unique();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->unsignedBigInteger('parent_id')->nullable(); // id_parent: int (self-referencing foreign key)

            // Foreign key constraint
            $table->foreign('parent_id')->references('id')->on('bidding_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidding_fields');
    }
};
