<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hải Sản Tươi Ngon</title>
</head>
<body style="margin:0;padding:0;background:#f5f8fb;font-family:Arial,Helvetica,sans-serif;color:#2b2f38;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f8fb;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e9eef3;">
                <tr>
                    <td style="background:linear-gradient(90deg,#2aa3f0,#1577c4);background-color:#1f8fe1;padding:22px 28px;color:#ffffff;">
                        <div style="font-size:22px;font-weight:800;">🐟 Hải Sản Tươi Ngon</div>
                        <div style="font-size:11px;letter-spacing:2.5px;font-weight:700;opacity:.85;margin-top:2px;">TƯƠI NGON MỖI NGÀY</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:26px 28px;font-size:14.5px;line-height:1.6;">
                        @yield('body')
                    </td>
                </tr>
                @isset($order)
                <tr>
                    <td style="padding:0 28px 22px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:13.5px;">
                            <tr style="background:#f5f8fb;">
                                <th align="left" style="padding:9px 10px;border-bottom:2px solid #e9eef3;color:#7b8794;font-size:12px;text-transform:uppercase;">Sản phẩm</th>
                                <th align="center" style="padding:9px 10px;border-bottom:2px solid #e9eef3;color:#7b8794;font-size:12px;text-transform:uppercase;">SL</th>
                                <th align="right" style="padding:9px 10px;border-bottom:2px solid #e9eef3;color:#7b8794;font-size:12px;text-transform:uppercase;">Thành tiền</th>
                            </tr>
                            @foreach ($order->items as $it)
                                <tr>
                                    <td style="padding:9px 10px;border-bottom:1px solid #f1f5f9;">{{ $it->product_name }}<br><span style="color:#7b8794;font-size:12px;">{{ format_price($it->price) }} / {{ $it->unit }}</span></td>
                                    <td align="center" style="padding:9px 10px;border-bottom:1px solid #f1f5f9;">{{ $it->quantity }}</td>
                                    <td align="right" style="padding:9px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;">{{ format_price($it->subtotal) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2" align="right" style="padding:8px 10px;color:#7b8794;">Tạm tính</td>
                                <td align="right" style="padding:8px 10px;">{{ format_price($order->sub_total) }}</td>
                            </tr>
                            @if ($order->discount_amount > 0)
                                <tr>
                                    <td colspan="2" align="right" style="padding:4px 10px;color:#059669;">Giảm giá{{ $order->voucher_code ? ' (' . $order->voucher_code . ')' : '' }}</td>
                                    <td align="right" style="padding:4px 10px;color:#059669;">-{{ format_price($order->discount_amount) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="2" align="right" style="padding:10px;font-weight:800;font-size:15px;">Tổng cộng</td>
                                <td align="right" style="padding:10px;font-weight:800;font-size:16px;color:#f0451f;">{{ format_price($order->total_amount) }}</td>
                            </tr>
                        </table>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:14px;font-size:13.5px;background:#f5f8fb;border-radius:10px;">
                            <tr><td style="padding:12px 14px;line-height:1.8;">
                                <b>Người nhận:</b> {{ $order->full_name }} — {{ $order->phone }}<br>
                                <b>Địa chỉ:</b> {{ $order->shipping_address }}<br>
                                <b>Thanh toán:</b> {{ payment_method_label($order->payment_method) }} ({{ payment_status_label($order->payment_status) }})
                                @if ($order->note)<br><b>Ghi chú:</b> {{ $order->note }}@endif
                            </td></tr>
                        </table>

                        <div style="text-align:center;margin-top:20px;">
                            <a href="{{ url('/tai-khoan/don-hang/' . $order->id) }}" style="display:inline-block;background:#f26a1b;color:#ffffff;text-decoration:none;font-weight:700;padding:11px 24px;border-radius:6px;">Xem chi tiết đơn hàng</a>
                        </div>
                    </td>
                </tr>
                @endisset
                <tr>
                    <td style="background:#0e63b0;color:#cfe6fa;text-align:center;padding:14px;font-size:12px;">
                        📍 123 Đường Hải Sản, Q.1, TP.HCM · 📞 0987832744<br>
                        © 2025 Hải Sản Tươi Ngon
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
