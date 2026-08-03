<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * SePay Webhook Controller
 *
 * SePay gửi POST JSON đến endpoint này mỗi khi có giao dịch vào tài khoản MBBank.
 * Tài liệu: https://my.sepay.vn/tai-lieu-ky-thuat
 *
 * Payload mẫu từ SePay:
 * {
 *   "id": 12345,
 *   "gateway": "MBBank",
 *   "transactionDate": "2026-08-03 10:30:00",
 *   "accountNumber": "080717072006",
 *   "subAccount": null,
 *   "code": "VIBE12345678",
 *   "content": "VIBE12345678 chuyen khoan thanh toan",
 *   "transferType": "in",
 *   "transferAmount": 510000,
 *   "accumulated": 510000,
 *   "referenceCode": "FT26123456789",
 *   "description": "...",
 *   "id": 12345
 * }
 */
class SepayWebhookController extends Controller
{
    /**
     * Nhận webhook từ SePay và xác nhận thanh toán đơn hàng tương ứng.
     */
    public function handle(Request $request)
    {
        // ── 1. Xác thực API Token từ SePay ────────────────────────────────────
        $token = $request->bearerToken() ?? $request->query('token');
        if (config('services.sepay.webhook_token') && $token !== config('services.sepay.webhook_token')) {
            Log::warning('[SePay Webhook] Invalid token', ['ip' => $request->ip()]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // ── 2. Parse payload ───────────────────────────────────────────────────
        $payload = $request->all();

        Log::info('[SePay Webhook] Received', $payload);

        // Chỉ xử lý giao dịch TIỀN VÀO
        if (($payload['transferType'] ?? '') !== 'in') {
            return response()->json(['success' => true, 'message' => 'Ignored (not inbound)']);
        }

        $content      = $payload['content']       ?? '';
        $transferAmount = (float)($payload['transferAmount'] ?? 0);

        // ── 3. Tìm mã đơn hàng trong nội dung chuyển khoản ────────────────────
        // Nội dung CK có dạng: "VIBE12345678 ..." hoặc "... VIBE12345678 ..."
        $order = $this->matchOrderByContent($content, $transferAmount);

        if (!$order) {
            Log::warning('[SePay Webhook] No matching order found', [
                'content' => $content,
                'amount'  => $transferAmount,
            ]);
            // Vẫn trả 200 để SePay không retry vô tận với giao dịch không liên quan
            return response()->json(['success' => true, 'message' => 'No matching order']);
        }

        // ── 4. Đã thanh toán rồi → bỏ qua (tránh xử lý trùng) ────────────────
        if ($order->payment_status === 'paid') {
            Log::info('[SePay Webhook] Order already paid', ['order' => $order->order_number]);
            return response()->json(['success' => true, 'message' => 'Already paid']);
        }

        // ── 5. Cập nhật trạng thái đơn hàng ───────────────────────────────────
        $order->update([
            'payment_status' => 'paid',
            'status'         => 'confirmed',     // Chuyển từ pending → confirmed
            'paid_at'        => now(),
            'confirmed_at'   => now(),
            'sepay_data'     => json_encode($payload),
        ]);

        Log::info('[SePay Webhook] Order confirmed', [
            'order_number' => $order->order_number,
            'amount'       => $transferAmount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed',
            'order'   => $order->order_number,
        ]);
    }

    /**
     * Tìm đơn hàng khớp với nội dung chuyển khoản và số tiền.
     *
     * Logic: nội dung CK chứa mã transfer_content được lưu khi đặt hàng
     * Ví dụ: transfer_content = "VIBE85234123" → tìm trong cột orders.transfer_content
     */
    private function matchOrderByContent(string $content, float $amount): ?Order
    {
        // Tìm tất cả đơn hàng SePay/bank_transfer đang chờ thanh toán
        $pendingOrders = Order::whereIn('payment_method', ['sepay', 'bank_transfer'])
            ->where('payment_status', 'unpaid')
            ->whereNotNull('transfer_content')
            ->get();

        foreach ($pendingOrders as $order) {
            $code = $order->transfer_content; // VD: "VIBE85234123"

            // Kiểm tra nội dung CK có chứa mã không (case-insensitive)
            if ($code && stripos($content, $code) !== false) {
                // Kiểm tra số tiền (sai lệch cho phép ±1000đ do phí)
                if (abs($order->total - $amount) <= 1000) {
                    return $order;
                }

                // Nếu mã đúng nhưng số tiền khác → vẫn ghi nhận (log để xem xét)
                Log::warning('[SePay Webhook] Code matched but amount mismatch', [
                    'order'    => $order->order_number,
                    'expected' => $order->total,
                    'received' => $amount,
                ]);
                return $order; // Vẫn chấp nhận để admin review
            }
        }

        // Fallback: tìm theo prefix VIBE trong nội dung nếu không có transfer_content
        if (preg_match('/VIBE\d{6,10}/i', $content, $matches)) {
            Log::info('[SePay Webhook] Fallback match attempt', ['code' => $matches[0]]);
        }

        return null;
    }
}
