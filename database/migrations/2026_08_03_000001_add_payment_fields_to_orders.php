<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Trạng thái thanh toán riêng (độc lập với trạng thái đơn hàng)
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])
                  ->default('unpaid')
                  ->after('payment_method');

            // Nội dung chuyển khoản (mã VIBE + timestamp) để SePay khớp giao dịch
            $table->string('transfer_content', 50)->nullable()->after('payment_status');

            // Thời điểm xác nhận thanh toán
            $table->timestamp('paid_at')->nullable()->after('transfer_content');

            // Dữ liệu raw từ SePay webhook
            $table->json('sepay_data')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'transfer_content', 'paid_at', 'sepay_data']);
        });
    }
};
