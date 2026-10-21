@extends('emails.layout')

@section('body')
    <p style="margin:0 0 12px;font-size:16px;"><b>Xin chào {{ $order->full_name }},</b></p>
    <p style="margin:0 0 12px;">
        Cảm ơn bạn đã đặt hàng tại <b>Hải Sản Tươi Ngon</b>. Chúng tôi đã nhận được thanh toán <b style="color:#f0451f;">{{ format_price($order->total_amount) }}</b>
        qua <b>VNPay</b> cho đơn hàng <b style="color:#1577c4;">#{{ $order->order_code }}</b>.
    </p>
    <table cellpadding="0" cellspacing="0" style="font-size:13.5px;margin:0 0 12px;">
        <tr><td style="padding:2px 12px 2px 0;color:#7b8794;">Mã giao dịch VNPay</td><td><b>{{ $order->vnpay_transaction_no ?: '-' }}</b></td></tr>
        <tr><td style="padding:2px 12px 2px 0;color:#7b8794;">Ngân hàng</td><td><b>{{ $order->vnpay_bank_code ?: '-' }}</b></td></tr>
        <tr><td style="padding:2px 12px 2px 0;color:#7b8794;">Thời gian</td><td><b>{{ format_date($order->paid_at) }}</b></td></tr>
    </table>
    <p style="margin:0;">Đơn hàng sẽ sớm được xác nhận và giao đến bạn. Cảm ơn bạn đã mua sắm!</p>
@endsection
