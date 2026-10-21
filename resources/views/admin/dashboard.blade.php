@extends('layouts.admin')

@section('title', 'Dashboard - Seafood Admin')

@php
    $statCards = [
        ['title' => 'Doanh thu (Đã giao)', 'value' => format_price($totalRevenue), 'icon' => 'dollar-sign', 'bg' => 'rgba(16, 185, 129, 0.1)', 'color' => '#10b981'],
        ['title' => 'Tổng số đơn hàng', 'value' => $totalOrders, 'icon' => 'shopping-cart', 'bg' => 'rgba(59, 130, 246, 0.1)', 'color' => '#3b82f6'],
        ['title' => 'Sản phẩm đang bán', 'value' => $totalProducts, 'icon' => 'shopping-bag', 'bg' => 'rgba(14, 165, 233, 0.1)', 'color' => '#0ea5e9'],
        ['title' => 'Khách hàng đăng ký', 'value' => $totalUsers, 'icon' => 'users', 'bg' => 'rgba(139, 92, 246, 0.1)', 'color' => '#8b5cf6'],
    ];
@endphp

@section('content')
<div class="container-fluid p-0">
    @if ($pendingOrders > 0 || $unreadContacts > 0)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4" style="border-radius:16px;background:linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);color:#0369a1">
            <div class="d-flex align-items-center gap-3">
                {{ icon('alert-circle', 24) }}
                <div>
                    <strong>Thông báo hệ thống:</strong> Bạn đang có
                    {{-- viết liền một dòng để không có khoảng trắng thừa trước dấu chấm (giống React) --}}
                    @if ($pendingOrders > 0)<span class="fw-bold">{{ $pendingOrders }} đơn hàng chờ xử lý</span>@endif{{ $pendingOrders > 0 && $unreadContacts > 0 ? ' và ' : '' }}@if ($unreadContacts > 0)<span class="fw-bold">{{ $unreadContacts }} tin nhắn liên hệ chưa đọc</span>@endif. Hãy kiểm tra ngay!
                </div>
            </div>
            <div class="d-flex gap-2">
                @if ($pendingOrders > 0)
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary border-0" style="background:#0284c7;border-radius:8px">Xử lý đơn hàng</a>
                @endif
                @if ($unreadContacts > 0)
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-primary" style="border-color:#0284c7;color:#0284c7;border-radius:8px">Xem liên hệ</a>
                @endif
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        @foreach ($statCards as $card)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="stat-label">{{ $card['title'] }}</p>
                            <h3 class="stat-value">{{ $card['value'] }}</h3>
                        </div>
                        <div class="stat-icon" style="background-color:{{ $card['bg'] }};color:{{ $card['color'] }}">
                            {{ icon($card['icon']) }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="custom-table-container h-100 mb-0">
                <h5 class="mb-4 fw-bold">Doanh thu và số lượng đơn hàng (6 tháng gần đây)</h5>
                <div style="width:100%;height:320px"><canvas id="revenueChart"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="custom-table-container h-100 mb-0 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="mb-4 fw-bold">Biểu đồ đơn hàng</h5>
                    <div style="width:100%;height:200px"><canvas id="orderChart"></canvas></div>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-semibold text-muted mb-3">Thao tác nhanh</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary border-0 d-flex align-items-center justify-content-between p-2 text-start" style="border-radius:10px;background:#f8fafc">
                            <span>Thêm sản phẩm hải sản mới</span>{{ icon('arrow-right') }}
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary border-0 d-flex align-items-center justify-content-between p-2 text-start" style="border-radius:10px;background:#f8fafc">
                            <span>Xem tất cả đơn hàng</span>{{ icon('arrow-right') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="custom-table-container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0 fw-bold">Đơn hàng mới nhận</h5>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link text-decoration-none d-flex align-items-center gap-1">
                <span>Xem tất cả đơn hàng</span>{{ icon('arrow-right') }}
            </a>
        </div>
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Điện thoại</th>
                        <th>Thời gian</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $order->id }}</td>
                            <td>{{ $order->full_name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ format_date($order->created_at) }}</td>
                            <td class="fw-bold">{{ format_price($order->total_amount) }}</td>
                            <td>
                                <span class="badge-status {{ $order->payment_status === 'paid' ? 'badge-status-paid' : 'badge-status-unpaid' }}">
                                    {{ $order->payment_status === 'paid' ? 'Đã trả' : 'Chưa trả' }}
                                </span>
                            </td>
                            <td>@include('admin.partials.order-status-badge', ['status' => $order->status])</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Chưa có đơn hàng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
(function () {
    var data = @json($monthlyRevenue);
    var labels = data.map(function (d) { return d.month; });
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#94a3b8';

    var ctx = document.getElementById('revenueChart').getContext('2d');
    var grad = ctx.createLinearGradient(0, 0, 0, 320);
    grad.addColorStop(0.05, 'rgba(14,165,233,0.4)');
    grad.addColorStop(0.95, 'rgba(14,165,233,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu',
                data: data.map(function (d) { return d.revenue; }),
                borderColor: '#0ea5e9', borderWidth: 3, backgroundColor: grad,
                fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#fff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#0ea5e9', usePointStyle: false } },
                tooltip: { callbacks: { label: function (c) { return 'Doanh thu: ' + formatCurrency(c.parsed.y); } } }
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#f1f5f9', borderDash: [3, 3] }, ticks: { callback: function (v) { return (v / 1000000) + 'M'; } } }
            }
        }
    });

    new Chart(document.getElementById('orderChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'orderCount',
                data: data.map(function (d) { return d.orderCount; }),
                backgroundColor: '#8b5cf6', borderRadius: { topLeft: 4, topRight: 4 }, barThickness: 20
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#f1f5f9' }, beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
})();
</script>
@endpush
