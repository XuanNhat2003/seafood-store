@extends('shop.account.layout')

@section('title', 'Đơn hàng của tôi - Hải Sản Tươi Ngon')

@section('account')
<div class="shop-panel">
    <h1 class="shop-panel-title h5 mb-4">Đơn hàng của tôi</h1>

    @if ($orders->isEmpty())
        <div class="shop-empty text-center py-5">
            {{ icon('shopping-bag', 56, 'mb-3 text-muted') }}
            <p class="mb-3">Bạn chưa có đơn hàng nào</p>
            <a href="{{ url('/san-pham') }}" class="btn-shop-orange">Mua sắm ngay</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle shop-order-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày</th>
                        <th class="text-center">Số SP</th>
                        <th class="text-end">Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $o)
                        <tr>
                            <td class="fw-semibold">#{{ $o->id }}</td>
                            <td>{{ format_date($o->created_at) }}</td>
                            <td class="text-center">{{ $o->items_count ?? 0 }}</td>
                            <td class="text-end fw-semibold">
                                {{ format_price($o->total_amount) }}
                                @if ($o->payment_method === 'vnpay')
                                    <div><span class="badge-vnpay" style="font-size:10.5px">{{ $o->payment_status === 'paid' ? 'VNPay ✓' : 'VNPay - chưa TT' }}</span></div>
                                @endif
                            </td>
                            <td><span class="order-status-badge osb-{{ $o->status }}">{{ status_label($o->status) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('account.orders.show', $o->id) }}" class="shop-link-action">{{ icon('eye') }} Xem chi tiết</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
