<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderMailer;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VnpayController extends Controller
{
    /** POST /tai-khoan/don-hang/{id}/thanh-toan-vnpay — thanh toán (lại) một đơn VNPay chưa trả tiền */
    public function pay(Request $request, VnpayService $vnpay, $id)
    {
        $order = Order::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();

        if (! $order->can_pay_vnpay) {
            return back()->with('error', 'Đơn hàng này không thể thanh toán qua VNPay.');
        }
        if (! $vnpay->isConfigured()) {
            return back()->with('error', 'Cổng thanh toán VNPay chưa được cấu hình.');
        }

        return redirect()->away($vnpay->createPaymentUrl($order, $request->ip()));
    }

    /** GET /thanh-toan/vnpay-return — VNPay chuyển người dùng về sau khi thanh toán */
    public function return(Request $request, VnpayService $vnpay)
    {
        $input = $request->query();

        if (! $vnpay->verify($input)) {
            return redirect()->route('account.orders')->with('error', 'Chữ ký VNPay không hợp lệ. Giao dịch không được ghi nhận.');
        }

        [$order, $code] = $vnpay->applyResult($input);

        if (! $order) {
            return redirect()->route('account.orders')->with('error', 'Không tìm thấy đơn hàng tương ứng với giao dịch.');
        }

        $target = redirect()->route('account.orders.show', $order->id);

        if ($code === 'ok') {
            OrderMailer::sendPaid($order, optional($order->user)->email);
            return $target->with('success', 'Thanh toán VNPay thành công! Đơn hàng ' . $order->order_code . ' đã được thanh toán.');
        }
        if ($code === 'already') {
            return $target->with('success', 'Đơn hàng đã được thanh toán.');
        }
        if ($code === 'invalid_amount') {
            return $target->with('error', 'Số tiền thanh toán không khớp với đơn hàng.');
        }

        return $target->with('error', 'Thanh toán VNPay thất bại: ' . VnpayService::responseMessage($input['vnp_ResponseCode'] ?? '99'));
    }

    /** GET /api/vnpay-ipn — VNPay gọi server-to-server (cần URL public, ví dụ qua ngrok) */
    public function ipn(Request $request, VnpayService $vnpay)
    {
        $input = $request->query();
        try {
            if (! $vnpay->verify($input)) {
                return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
            }
            [$order, $code] = $vnpay->applyResult($input);

            switch ($code) {
                case 'not_found':
                    return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
                case 'invalid_amount':
                    return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
                case 'already':
                    return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
                case 'ok':
                    OrderMailer::sendPaid($order, optional($order->user)->email);
                    // no break
                default:
                    return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
            }
        } catch (\Throwable $e) {
            Log::error('VNPay IPN error: ' . $e->getMessage());
            return response()->json(['RspCode' => '99', 'Message' => 'Unknown error']);
        }
    }
}
