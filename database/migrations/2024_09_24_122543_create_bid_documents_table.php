<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bid_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('enterprise_id');
            $table->unsignedBigInteger('bid_bond_id');
            $table->timestamp('submission_date');
            $table->decimal('bid_price', 18, 2);
            $table->timestamp('implementation_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('validity_period')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid_documents');
    }
};
