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
            $table->unsignedBigInteger('funding_source_id');                            // nguồn vốn
            $table->unsignedBigInteger('tenderer_id');                                  // bên mời thầu
            $table->unsignedBigInteger('investor_id');                                  // nhà đầu tư
            $table->unsignedBigInteger('staff_id');                                     // người phê duyệt
            $table->unsignedBigInteger('selection_method_id');                          // hình thức lựa chọn nhà thầu : đấu thầu cạnh tranh / đấu thầu rộng rãi / ...
            $table->unsignedBigInteger('parent_id')->nullable()->default(null);   // nếu null thì là dự án, không null là gói thầu trong dự án
            $table->string('decision_number_issued');                                   // số quyết định bban hành
            $table->string('name');                                                     // tên dự án hoặc gói thầu
            $table->boolean('is_domestic');                                             // nội địa hay quốc tế
            $table->string('location');                                                 // nơi thực hiện : tỉnh - huyện - xã
            $table->decimal('amount', 20, 2);                               // giá trị gói thầu
            $table->decimal('total_amount', 20, 2);                         //tổng giá trị gói thầu
            $table->string('description')->nullable();                                  // mô tả
            $table->enum('submission_method', ['online', 'in_person']);
            $table->text('receiving_place')->nullable();                                // nơi tiếp nhận hồ sơ nếu submission_method = 2
            $table->timestamp('bid_submission_start')->nullable();                      // ngày bắt đầu nhận hồ sơ
            $table->timestamp('bid_submission_end')->nullable();                        // ngày kết thúc nhận hồ sơ
            $table->timestamp('bid_opening_date')->nullable();                          // ngày công bố kết quả ( mở thầu )
            $table->date('start_time')->nullable();                                     // ngày bắt đầu thực hiện dự án
            $table->date('end_time')->nullable();                                       // ngày kết thúc dự án
            $table->timestamp('approve_at')->nullable();                                // ngày phê duyệt
            $table->string('decision_number_approve')->nullable();                      // số quyết định phê duyệt
            $table->tinyInteger('status')->default(1);                            // trạng thái
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
