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
        Schema::create('bid_bonds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('enterprise_id');
            $table->string('bond_number', 100)->unique();
            $table->decimal('bond_amount', 18, 2);
            $table->string('bond_amount_in_words')->nullable();
            $table->tinyInteger('bond_type');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid_bonds');
    }
};
