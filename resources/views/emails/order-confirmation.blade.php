@extends('emails.layout')

@section('body')
    <p style="margin:0 0 12px;font-size:16px;"><b>Xin chào {{ $order->full_name }},</b></p>
    <p style="margin:0 0 12px;">
        Cảm ơn bạn đã đặt hàng tại <b>Hải Sản Tươi Ngon</b>. Đơn hàng
        <b style="color:#1577c4;">#{{ $order->order_code }}</b> trị giá
        <b style="color:#f0451f;">{{ format_price($order->total_amount) }}</b>
        đang ở trạng thái "<b>{{ status_label($order->status) }}</b>". Chúng tôi sẽ liên hệ sớm.
    </p>
    @if ($order->payment_method === 'vnpay' && $order->payment_status !== 'paid')
        <p style="margin:0 0 12px;color:#c2410c;">Đơn hàng chọn thanh toán qua VNPay. Nếu bạn chưa hoàn tất thanh toán, hãy mở chi tiết đơn hàng để thanh toán lại.</p>
    @endif
    <p style="margin:0;color:#7b8794;font-size:13px;">Đặt lúc {{ format_date($order->created_at) }}</p>
@endsection
