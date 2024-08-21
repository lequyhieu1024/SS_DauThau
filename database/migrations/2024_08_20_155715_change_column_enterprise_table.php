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
        Schema::table('enterprises', function (Blueprint $table) {
            $table->string('representative_name')->change();
            $table->string('avatar')->nullable()->after('representative_name');
            $table->string('phone', 15)->unique()->after('avatar');
            $table->date('registration_date')->after('avg_document_rating');
            $table->string('registration_number', 50)->after('registration_date');
            $table->enum('organization_type', ['Doanh nghiệp ngoài nhà nước', 'Doanh nghiệp nhà nước'])->after('registration_number'); // loại doanh nghiệp Nhà nước hay Ngoài nhà nước
            $table->integer('activity_id')->after('organization_type'); // lĩnh vực hoạt động
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            //
        });
    }
};
