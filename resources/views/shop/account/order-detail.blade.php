@extends('shop.account.layout')

@section('title', 'Chi tiết đơn hàng - Hải Sản Tươi Ngon')

@section('account')
@if (!$order)
    <div class="shop-panel">
        <div class="shop-empty text-center py-5">
            {{ icon('x-circle', 56, 'mb-3 text-muted') }}
            <p class="mb-3">Không tìm thấy đơn hàng</p>
            <a href="{{ route('account.orders') }}" class="btn-shop-orange">Về danh sách đơn hàng</a>
        </div>
    </div>
@else
@php
    $STATUS_FLOW = ['pending', 'confirmed', 'shipping', 'delivered'];
    $STEPS = [
        ['key' => 'pending', 'label' => 'Chờ xác nhận', 'icon' => 'clock'],
        ['key' => 'confirmed', 'label' => 'Đã xác nhận', 'icon' => 'check-circle'],
        ['key' => 'shipping', 'label' => 'Đang giao', 'icon' => 'truck'],
        ['key' => 'delivered', 'label' => 'Đã giao', 'icon' => 'package'],
    ];
    $isCancelled = $order->status === 'cancelled';
    $currentIndex = array_search($order->status, $STATUS_FLOW, true);
    $currentIndex = $currentIndex === false ? -1 : $currentIndex;
    $fillPercent = $currentIndex > 0 ? ($currentIndex / 3) * 100 : 0;
    $pmLabel = $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng'
        : ($order->payment_method === 'bank' ? 'Chuyển khoản' : ($order->payment_method === 'vnpay' ? 'VNPay' : ($order->payment_method ?: '-')));
@endphp
<div class="order-detail">
    <a href="{{ route('account.orders') }}" class="shop-link-action mb-3 d-inline-flex">{{ icon('arrow-left') }} Quay lại danh sách đơn hàng</a>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h5 mb-1">Đơn hàng {{ $order->order_code ?: '#' . $order->id }}</h1>
            <div class="text-muted" style="font-size:13px">Đặt lúc {{ format_date($order->created_at) }}</div>
        </div>
        <span class="order-status-badge osb-{{ $order->status }}">{{ status_label($order->status) }}</span>
    </div>

    @if ($isCancelled)
        <div class="order-cancelled-note">{{ icon('x-circle') }} Đơn hàng đã bị hủy</div>
    @endif

    {{-- Thông báo thanh toán VNPay --}}
    @if ($order->can_pay_vnpay)
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-3" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;color:#9a3412">
            <div class="d-flex align-items-center gap-2 fw-semibold">{{ icon('alert-circle') }} Đơn hàng chưa được thanh toán qua VNPay.</div>
            <form method="POST" action="{{ route('account.orders.vnpay', $order->id) }}" class="m-0">
                @csrf
                <button type="submit" class="btn-shop-blue" style="padding:8px 18px" data-loading-text="Đang chuyển tới VNPay...">{{ icon('credit-card') }} Thanh toán ngay qua VNPay</button>
            </form>
        </div>
    @elseif ($order->payment_method === 'vnpay' && $order->payment_status === 'paid')
        <div class="d-flex align-items-center gap-2 mb-3 p-3" style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;color:#047857;font-weight:600">
            {{ icon('check-circle') }} Đã thanh toán qua VNPay
            @if ($order->vnpay_transaction_no) — Mã GD: {{ $order->vnpay_transaction_no }} @endif
            @if ($order->paid_at) ({{ format_date($order->paid_at) }}) @endif
        </div>
    @endif

    {{-- Timeline --}}
    <div class="order-timeline{{ $isCancelled ? ' cancelled' : '' }}">
        <div class="ot-line"><div class="ot-line-fill" style="width:{{ $fillPercent }}%"></div></div>
        @foreach ($STEPS as $idx => $step)
            @php
                $done = !$isCancelled && $currentIndex >= $idx;
                $current = !$isCancelled && $currentIndex === $idx;
            @endphp
            <div class="ot-step{{ $done ? ' done' : '' }}{{ $current ? ' current' : '' }}">
                <div class="ot-ico">{{ icon($step['icon']) }}</div>
                <div class="ot-label">{{ $step['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Recipient info --}}
    <div class="shop-panel mt-4">
        <h2 class="shop-panel-title h6 mb-3">Thông tin nhận hàng</h2>
        <div class="order-info-grid">
            <div class="order-info-row"><span class="oi-label">Người nhận</span><span class="oi-value">{{ $order->full_name ?: '-' }}</span></div>
            <div class="order-info-row"><span class="oi-label">SĐT</span><span class="oi-value">{{ $order->phone ?: '-' }}</span></div>
            <div class="order-info-row"><span class="oi-label">Địa chỉ</span><span class="oi-value">{{ $order->shipping_address ?: '-' }}</span></div>
            <div class="order-info-row"><span class="oi-label">Ghi chú</span><span class="oi-value">{{ $order->note ?: '-' }}</span></div>
            <div class="order-info-row"><span class="oi-label">Phương thức thanh toán</span><span class="oi-value">{{ $pmLabel }}</span></div>
            <div class="order-info-row"><span class="oi-label">Tình trạng TT</span><span class="oi-value">{{ payment_status_label($order->payment_status) }}</span></div>
        </div>
    </div>

    {{-- Items --}}
    <div class="shop-panel mt-4">
        <h2 class="shop-panel-title h6 mb-3">Sản phẩm</h2>
        <div class="order-items">
            @foreach ($order->items as $it)
                <div class="order-item">
                    <img class="order-item-img" src="{{ img_url($it->display_image, asset('images/logo.png')) }}" alt="{{ $it->product_name }}">
                    <div class="order-item-info">
                        <div class="order-item-name">{{ $it->product_name }}</div>
                        <div class="order-item-qty text-muted">x{{ $it->quantity }} {{ $it->unit }}</div>
                    </div>
                    <div class="order-item-prices text-end">
                        <div class="order-item-price text-muted">{{ format_price($it->price) }}</div>
                        <div class="order-item-subtotal fw-semibold">{{ format_price($it->subtotal) }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="order-summary mt-3">
            <div class="summary-row"><span>Tạm tính</span><span>{{ format_price($order->sub_total) }}</span></div>
            @if ($order->discount_amount > 0)
                <div class="summary-row summary-discount">
                    <span>Giảm giá{{ $order->voucher_code ? ' (' . $order->voucher_code . ')' : '' }}</span>
                    <span>-{{ format_price($order->discount_amount) }}</span>
                </div>
            @endif
            <div class="summary-row summary-total"><span>Tổng cộng</span><span>{{ format_price($order->total_amount) }}</span></div>
        </div>
    </div>

    {{-- Cancel action — only allowed while the order is still pending --}}
    @if ($order->status === 'pending')
        <div class="d-flex justify-content-end mt-2 mb-4">
            <form method="POST" action="{{ route('account.orders.cancel', $order->id) }}" class="m-0"
                  data-confirm="Bạn có chắc muốn hủy đơn hàng này?" data-confirm-title="Hủy đơn hàng" data-confirm-type="danger">
                @csrf
                <button type="submit" class="btn-shop-danger" data-loading-text="Đang hủy...">{{ icon('x-circle') }} Hủy đơn hàng</button>
            </form>
        </div>
    @endif
</div>
@endif
@endsection
