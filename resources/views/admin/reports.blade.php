@extends('layouts.admin')

@section('title', 'Báo cáo thống kê - Seafood Admin')

@php
    $statCards = [
        ['title' => 'Doanh thu thực tế (Đã giao)', 'value' => format_price($report['totalRevenue']), 'icon' => 'dollar-sign', 'bg' => 'rgba(16, 185, 129, 0.1)', 'color' => '#10b981'],
        ['title' => 'Tổng số đơn hàng', 'value' => $report['totalOrders'], 'icon' => 'shopping-cart', 'bg' => 'rgba(59, 130, 246, 0.1)', 'color' => '#3b82f6'],
        ['title' => 'Đơn giao thành công', 'value' => $report['deliveredOrders'], 'icon' => 'check-circle', 'bg' => 'rgba(14, 165, 233, 0.1)', 'color' => '#0ea5e9'],
        ['title' => 'Đơn hàng bị hủy', 'value' => $report['cancelledOrders'], 'icon' => 'x-circle', 'bg' => 'rgba(239, 68, 68, 0.1)', 'color' => '#ef4444'],
    ];
@endphp

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Báo cáo thống kê kinh doanh</h4>
            <p class="text-muted small mb-0">Phân tích sâu doanh thu, sản lượng bán ra, top mặt hàng hải sản bán chạy và thông tin chi tiêu khách hàng</p>
        </div>
        <button type="button" id="btnExportExcel" class="btn btn-outline-success d-flex align-items-center gap-2 py-2 px-3 fw-semibold" style="border-radius:12px">
            {{ icon('download') }}
            <span>Xuất Excel</span>
        </button>
    </div>

    {{-- Date Range Filter Bar --}}
    <div class="custom-table-container report-filter-panel mb-4">
        <form method="GET" id="reportFilterForm" class="row g-4 align-items-end">
            <div class="col-12 col-lg-3">
                <label class="report-field-label">{{ icon('calendar') }} Ngày bắt đầu</label>
                <input type="date" name="startDate" id="startDate" class="form-control report-date-input" value="{{ $report['startDate'] }}" required>
            </div>
            <div class="col-12 col-lg-3">
                <label class="report-field-label">{{ icon('calendar') }} Ngày kết thúc</label>
                <input type="date" name="endDate" id="endDate" class="form-control report-date-input" value="{{ $report['endDate'] }}" required>
            </div>
            <div class="col-12 col-lg-6">
                <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2"
                        style="border-radius:12px;background:var(--accent-gradient);border:none;height:45px">
                    {{ icon('trending-up') }}
                    <span>Phân tích dữ liệu</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Stats Summary Cards --}}
    <div class="row g-4 mb-4">
        @foreach ($statCards as $card)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card p-3" style="border-left:4px solid {{ $card['color'] }}">
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

    {{-- Revenue Chart over Time --}}
    <div class="custom-table-container mb-4">
        <h5 class="mb-4 fw-bold">Biểu đồ xu hướng doanh thu ({{ $report['startDate'] }} → {{ $report['endDate'] }})</h5>
        <div style="width:100%;height:350px">
            @if (count($report['dailyRevenue']) > 0)
                <canvas id="reportRevenueChart"></canvas>
            @else
                <div class="d-flex justify-content-center align-items-center h-100 text-muted">
                    Không có dữ liệu doanh thu trong khoảng thời gian này
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom Tables --}}
    <div class="row g-4">
        {{-- Top Products --}}
        <div class="col-12 col-lg-6">
            <div class="custom-table-container h-100 mb-0">
                <h5 class="mb-3 fw-bold">Top 5 hải sản bán chạy nhất</h5>
                <div class="table-responsive">
                    <table class="table custom-table align-middle">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Doanh thu mang lại</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report['topProducts'] as $p)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $p['productName'] }}</div>
                                        <span class="text-muted small">Đơn vị: {{ $p['unit'] }}</span>
                                    </td>
                                    <td class="text-center fw-semibold text-primary">{{ $p['totalQuantity'] }}</td>
                                    <td class="text-end fw-bold">{{ format_price($p['totalRevenue']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Chưa có sản phẩm bán chạy</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Customers --}}
        <div class="col-12 col-lg-6">
            <div class="custom-table-container h-100 mb-0">
                <h5 class="mb-3 fw-bold">Top 5 khách hàng chi tiêu nhiều nhất</h5>
                <div class="table-responsive">
                    <table class="table custom-table align-middle">
                        <thead>
                            <tr>
                                <th>Khách hàng</th>
                                <th>Điện thoại</th>
                                <th class="text-center">Đơn đã giao</th>
                                <th class="text-end">Tổng chi tiêu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report['topCustomers'] as $c)
                                <tr>
                                    <td class="fw-bold">{{ $c['fullName'] }}</td>
                                    <td>{{ $c['phone'] }}</td>
                                    <td class="text-center text-primary fw-semibold">{{ $c['orderCount'] }} đơn</td>
                                    <td class="text-end fw-bold text-success">{{ format_price($c['totalSpent']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có thông tin khách hàng mua sắm</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
(function () {
    var report = @json($report);

    // Validate date range before submit (giống handleFilterSubmit)
    document.getElementById('reportFilterForm').addEventListener('submit', function (e) {
        var s = document.getElementById('startDate').value;
        var en = document.getElementById('endDate').value;
        if (new Date(s) > new Date(en)) {
            e.preventDefault();
            toast.error('Ngày bắt đầu không được lớn hơn ngày kết thúc!');
        }
    });

    function vnNumber(v) { return Number(v || 0).toLocaleString('vi-VN'); }

    // Revenue area chart (recharts AreaChart → Chart.js line + fill)
    var canvas = document.getElementById('reportRevenueChart');
    if (canvas) {
        Chart.defaults.font.family = 'Inter, sans-serif';
        var ctx = canvas.getContext('2d');
        var grad = ctx.createLinearGradient(0, 0, 0, 350);
        grad.addColorStop(0.05, 'rgba(16,185,129,0.4)');
        grad.addColorStop(0.95, 'rgba(16,185,129,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: report.dailyRevenue.map(function (d) { return d.date; }),
                datasets: [{
                    label: 'Doanh thu',
                    data: report.dailyRevenue.map(function (d) { return d.revenue; }),
                    borderColor: '#10b981', borderWidth: 3, backgroundColor: grad,
                    fill: true, cubicInterpolationMode: 'monotone',
                    pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#10b981'
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { top: 10, right: 30, left: 20, bottom: 0 } },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#10b981', boxHeight: 10 } },
                    tooltip: { callbacks: { label: function (c) { return 'Doanh thu: ' + vnNumber(c.parsed.y) + ' đ'; } } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } }, border: { color: '#94a3b8' } },
                    y: {
                        grid: { color: '#f1f5f9' }, border: { display: false, dash: [3, 3] },
                        ticks: { color: '#94a3b8', font: { size: 11 }, callback: function (v) { return vnNumber(v) + ' đ'; } }
                    }
                }
            }
        });
    }

    // Export Excel (port of handleExportExcel)
    document.getElementById('btnExportExcel').addEventListener('click', function () {
        if (!report) return;
        var pad3 = function (n) { return String(n).padStart(3, '0'); };

        var styles = '<style>' +
            'body { font-family: Arial, sans-serif; }' +
            '.title { font-size: 22px; font-weight: 700; color: #0f766e; }' +
            '.subtitle { color: #64748b; }' +
            '.section { font-size: 16px; font-weight: 700; color: #111827; background: #ecfeff; }' +
            '.head { background: #0f766e; color: #ffffff; font-weight: 700; }' +
            '.label { background: #f8fafc; color: #475569; font-weight: 700; }' +
            '.money { color: #047857; font-weight: 700; }' +
            '.danger { color: #dc2626; font-weight: 700; }' +
            '.center { text-align: center; }' +
            '.right { text-align: right; }' +
            'td { border: 1px solid #dbe5e3; padding: 8px; }' +
            '</style>';

        var dailyRows = (report.dailyRevenue || []).map(function (item, i) {
            return '<tr><td class="center">' + (i + 1) + '</td><td>' + esc(item.date) + '</td>' +
                '<td class="center">' + (item.orderCount || 0) + '</td>' +
                '<td class="right money">' + vnNumber(item.revenue) + ' đ</td></tr>';
        }).join('');

        var productRows = (report.topProducts || []).map(function (item, i) {
            return '<tr><td class="center">' + (i + 1) + '</td><td>SP' + pad3(item.productId) + '</td>' +
                '<td>' + esc(item.productName) + '</td><td class="center">' + esc(item.unit) + '</td>' +
                '<td class="center">' + item.totalQuantity + '</td>' +
                '<td class="right money">' + vnNumber(item.totalRevenue) + ' đ</td></tr>';
        }).join('');

        var customerRows = (report.topCustomers || []).map(function (item, i) {
            return '<tr><td class="center">' + (i + 1) + '</td><td>KH' + pad3(item.userId) + '</td>' +
                '<td>' + esc(item.fullName) + '</td><td>' + esc(item.phone || '-') + '</td>' +
                '<td class="center">' + item.orderCount + '</td>' +
                '<td class="right money">' + vnNumber(item.totalSpent) + ' đ</td></tr>';
        }).join('');

        var empty = '<tr><td colspan="6" class="center">Không có dữ liệu</td></tr>';

        var html = '<html><head><meta charset="utf-8" />' + styles + '</head><body><table>' +
            '<tr><td colspan="6" class="title">BÁO CÁO THỐNG KÊ KINH DOANH SEAFOOD STORE</td></tr>' +
            '<tr><td colspan="6" class="subtitle">Khoảng thời gian: ' + report.startDate + ' đến ' + report.endDate + '</td></tr>' +
            '<tr></tr>' +
            '<tr><td class="label">Doanh thu thực tế</td><td class="right money">' + vnNumber(report.totalRevenue) + ' đ</td>' +
            '<td class="label">Tổng số đơn</td><td class="center">' + report.totalOrders + '</td>' +
            '<td class="label">Đơn bị hủy</td><td class="center danger">' + report.cancelledOrders + '</td></tr>' +
            '<tr><td class="label">Đơn giao thành công</td><td class="center">' + report.deliveredOrders + '</td>' +
            '<td class="label">Ngày xuất báo cáo</td><td colspan="3">' + new Date().toLocaleString('vi-VN') + '</td></tr>' +
            '<tr></tr>' +
            '<tr><td colspan="6" class="section">1. Doanh thu chi tiết theo ngày</td></tr>' +
            '<tr class="head"><td class="center">STT</td><td>Ngày</td><td class="center">Số đơn</td><td class="right">Doanh thu</td><td></td><td></td></tr>' +
            (dailyRows || empty) +
            '<tr></tr>' +
            '<tr><td colspan="6" class="section">2. Top sản phẩm bán chạy</td></tr>' +
            '<tr class="head"><td class="center">STT</td><td>Mã sản phẩm</td><td>Tên sản phẩm</td><td class="center">Đơn vị</td><td class="center">Số lượng bán</td><td class="right">Doanh thu</td></tr>' +
            (productRows || empty) +
            '<tr></tr>' +
            '<tr><td colspan="6" class="section">3. Top khách hàng chi tiêu nhiều nhất</td></tr>' +
            '<tr class="head"><td class="center">STT</td><td>Mã KH</td><td>Khách hàng</td><td>Điện thoại</td><td class="center">Số đơn</td><td class="right">Tổng chi tiêu</td></tr>' +
            (customerRows || empty) +
            '</table></body></html>';

        var blob = new Blob(['﻿', html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', 'BaoCaoKinhDoanh_SeafoodStore_' + report.startDate + '_den_' + report.endDate + '.xls');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        toast.success('Xuất file Excel thành công!');
    });
})();
</script>
@endpush
