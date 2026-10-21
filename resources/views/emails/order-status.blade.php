@extends('emails.layout')

@section('body')
    <p style="margin:0 0 12px;font-size:16px;"><b>Xin chào {{ $order->full_name }},</b></p>
    <p style="margin:0 0 12px;">
        Đơn hàng <b style="color:#1577c4;">#{{ $order->order_code }}</b> của bạn đã chuyển sang trạng thái
    </p>
    @php
        $colors = [
            'pending' => ['#fff7ed', '#c2410c'], 'confirmed' => ['#eff6ff', '#1d4ed8'],
            'shipping' => ['#f5f3ff', '#6d28d9'], 'delivered' => ['#ecfdf5', '#059669'],
            'cancelled' => ['#fef2f2', '#dc2626'],
        ];
        $c = $colors[$order->status] ?? ['#f1f5f9', '#475569'];
    @endphp
    <p style="margin:0 0 14px;">
        <span style="display:inline-block;padding:6px 16px;border-radius:20px;font-weight:700;background:{{ $c[0] }};color:{{ $c[1] }};">{{ status_label($order->status) }}</span>
    </p>
    <p style="margin:0;color:#7b8794;font-size:13px;">Cập nhật lúc {{ format_date($order->updated_at) }}</p>
@endsection
