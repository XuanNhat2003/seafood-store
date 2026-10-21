<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Mail\OrderPaidMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Gửi email thông báo đơn hàng qua SMTP (cấu hình MAIL_* trong .env).
 * Lỗi gửi mail không bao giờ làm hỏng nghiệp vụ đặt hàng (best-effort, giống bản .NET).
 */
class OrderMailer
{
    public static function sendConfirmation(Order $order, $toEmail)
    {
        self::send($toEmail, new OrderConfirmationMail($order), 'XÁC NHẬN ĐƠN HÀNG', $order);
    }

    public static function sendStatusUpdate(Order $order, $toEmail)
    {
        self::send($toEmail, new OrderStatusUpdatedMail($order), 'CẬP NHẬT ĐƠN HÀNG', $order);
    }

    public static function sendPaid(Order $order, $toEmail)
    {
        self::send($toEmail, new OrderPaidMail($order), 'THANH TOÁN THÀNH CÔNG', $order);
    }

    private static function send($toEmail, $mailable, $label, Order $order)
    {
        if (! $toEmail) {
            return;
        }
        try {
            Mail::to($toEmail)->send($mailable);
            Log::info("EMAIL [{$label}] đơn #{$order->order_code} đã gửi tới {$toEmail}");
        } catch (\Throwable $e) {
            Log::warning("EMAIL [{$label}] đơn #{$order->order_code} gửi tới {$toEmail} thất bại: " . $e->getMessage());
        }
    }
}
