<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing('items.product');
    }

    public function build()
    {
        return $this->subject('[Hải Sản Tươi Ngon] Đơn hàng #' . $this->order->order_code . ' đã được cập nhật')
            ->view('emails.order-status');
    }
}
