<?php

namespace App\Services;

use App\Models\Order;

/**
 * Tích hợp cổng thanh toán VNPay (phiên bản 2.1.0).
 */
class VnpayService
{
    public function isConfigured()
    {
        return config('vnpay.tmn_code') && config('vnpay.hash_secret');
    }

    /**
     * Tạo URL chuyển hướng người dùng sang trang thanh toán VNPay.
     */
    public function createPaymentUrl(Order $order, $ipAddr)
    {
        // TxnRef phải duy nhất cho mỗi lần thanh toán => thêm hậu tố thời gian (cho phép thanh toán lại)
        $txnRef = $order->order_code . '_' . now()->format('His');
        $order->vnpay_txn_ref = $txnRef;
        $order->save();

        $returnUrl = config('vnpay.return_url') ?: route('vnpay.return');

        $params = [
            'vnp_Version'    => config('vnpay.version'),
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => config('vnpay.tmn_code'),
            'vnp_Amount'     => (int) round($order->total_amount) * 100,
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $txnRef,
            'vnp_OrderInfo'  => 'Thanh toan don hang ' . $order->order_code,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => config('vnpay.locale', 'vn'),
            'vnp_ReturnUrl'  => $returnUrl,
            'vnp_IpAddr'     => $ipAddr === '::1' ? '127.0.0.1' : $ipAddr,
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_ExpireDate' => now()->addMinutes(config('vnpay.expire_minutes', 15))->format('YmdHis'),
        ];

        ksort($params);
        $query = $this->buildQuery($params);
        $secureHash = hash_hmac('sha512', $query, config('vnpay.hash_secret'));

        return config('vnpay.url') . '?' . $query . '&vnp_SecureHash=' . $secureHash;
    }

    /**
     * Kiểm tra chữ ký dữ liệu VNPay trả về (return URL hoặc IPN).
     */
    public function verify(array $input)
    {
        $secureHash = $input['vnp_SecureHash'] ?? '';
        $data = [];
        foreach ($input as $key => $value) {
            if (strpos($key, 'vnp_') === 0 && ! in_array($key, ['vnp_SecureHash', 'vnp_SecureHashType'], true)) {
                $data[$key] = $value;
            }
        }
        ksort($data);
        $hash = hash_hmac('sha512', $this->buildQuery($data), config('vnpay.hash_secret'));

        return $secureHash !== '' && hash_equals($hash, strtolower($secureHash));
    }

    public function isSuccess(array $input)
    {
        return ($input['vnp_ResponseCode'] ?? '') === '00'
            && ($input['vnp_TransactionStatus'] ?? '00') === '00';
    }

    /**
     * Cập nhật đơn hàng theo kết quả VNPay. Trả về [Order|null, string code]
     *  code: ok | not_found | invalid_amount | already | failed
     */
    public function applyResult(array $input)
    {
        $order = Order::where('vnpay_txn_ref', $input['vnp_TxnRef'] ?? '')->first();
        if (! $order) {
            return [null, 'not_found'];
        }
        if ((int) ($input['vnp_Amount'] ?? 0) !== (int) round($order->total_amount) * 100) {
            return [$order, 'invalid_amount'];
        }
        if ($order->payment_status === 'paid') {
            return [$order, 'already'];
        }
        if (! $this->isSuccess($input)) {
            return [$order, 'failed'];
        }

        $order->payment_status = 'paid';
        $order->paid_at = now();
        $order->vnpay_transaction_no = $input['vnp_TransactionNo'] ?? null;
        $order->vnpay_bank_code = $input['vnp_BankCode'] ?? null;
        $order->save();

        return [$order, 'ok'];
    }

    /** Thông báo lỗi theo mã phản hồi VNPay */
    public static function responseMessage($code)
    {
        $map = [
            '00' => 'Giao dịch thành công.',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Xác thực thông tin thẻ/tài khoản không đúng quá 3 lần.',
            '11' => 'Đã hết hạn chờ thanh toán. Vui lòng thực hiện lại giao dịch.',
            '12' => 'Thẻ/Tài khoản bị khóa.',
            '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP).',
            '24' => 'Bạn đã hủy giao dịch thanh toán.',
            '51' => 'Tài khoản không đủ số dư để thực hiện giao dịch.',
            '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Lỗi không xác định.',
        ];
        return $map[$code] ?? 'Thanh toán không thành công.';
    }

    private function buildQuery(array $params)
    {
        $parts = [];
        foreach ($params as $key => $value) {
            $parts[] = urlencode($key) . '=' . urlencode($value);
        }
        return implode('&', $parts);
    }
}
