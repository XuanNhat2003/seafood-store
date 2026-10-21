@extends('layouts.admin')

@section('title', 'Đơn hàng - Seafood Admin')

@section('content')
@php
    $page = $orders->currentPage();
    $totalPages = $orders->lastPage();
    $totalCount = $orders->total();
@endphp
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Quản lý đơn hàng</h4>
    </div>

    {{-- Filter and Search Bar --}}
    <div class="custom-table-container py-3 mb-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">{{ icon('search') }}</span>
                    <input type="text" name="search" class="form-control bg-light border-0"
                           placeholder="Tìm theo mã đơn, tên khách hàng, điện thoại..."
                           value="{{ $search }}" style="border-radius:0 8px 8px 0">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="status" class="form-select bg-light border-0" style="border-radius:8px" onchange="this.form.submit()">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>Tất cả trạng thái</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="shipping" {{ $status === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                    <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-secondary w-100 py-2" style="border-radius:8px">
                    Lọc &amp; Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px">STT</th>
                        <th class="text-center" style="width:130px">Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Ngày đặt</th>
                        <th class="text-center">Tổng số lượng</th>
                        <th>Tổng thanh toán</th>
                        <th class="text-center">Thanh toán</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $o)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ ($page - 1) * $pageSize + $loop->index + 1 }}</td>
                            <td class="text-center fw-bold text-primary">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $o->order_code ?: '#' . $o->id }}</span>
                            </td>
                            <td>{{ $o->full_name }}</td>
                            <td>{{ $o->phone }}</td>
                            <td>{{ format_date($o->created_at) }}</td>
                            <td class="text-center">{{ $o->items_count }} món</td>
                            <td class="fw-bold">{{ format_price($o->total_amount) }}</td>
                            <td class="text-center">
                                <span class="badge-status {{ $o->payment_status === 'paid' ? 'badge-status-paid' : 'badge-status-unpaid' }}">
                                    {{ $o->payment_status === 'paid' ? 'Đã trả' : 'Chưa trả' }}
                                </span>
                                <div class="text-muted small mt-1">{{ payment_method_label($o->payment_method) }}</div>
                            </td>
                            <td class="text-center">
                                @include('admin.partials.order-status-badge', ['status' => $o->status])
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0" style="border-radius:8px"
                                        title="Xem chi tiết đơn hàng" data-show-order="{{ $o->id }}">
                                    {{ icon('eye', 18) }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">Không tìm thấy đơn hàng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($totalPages > 1)
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="text-muted small">
                    Hiển thị {{ ($page - 1) * $pageSize + 1 }} - {{ min($page * $pageSize, $totalCount) }} trên {{ $totalCount }} đơn hàng
                </span>
                <nav>
                    <ul class="pagination mb-0 gap-1">
                        <li class="page-item {{ $page === 1 ? 'disabled' : '' }}">
                            <a class="page-link border-0" href="{{ $orders->url(max(1, $page - 1)) }}" style="border-radius:8px">Trước</a>
                        </li>
                        @for ($i = 1; $i <= $totalPages; $i++)
                            <li class="page-item {{ $page === $i ? 'active' : '' }}">
                                <a class="page-link border-0" href="{{ $orders->url($i) }}" style="border-radius:8px">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $page === $totalPages ? 'disabled' : '' }}">
                            <a class="page-link border-0" href="{{ $orders->url(min($totalPages, $page + 1)) }}" style="border-radius:8px">Sau</a>
                        </li>
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>

{{-- Details Modal --}}
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold" id="orderModalTitle">Chi tiết đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="orderModalBody"></div>
            <div class="modal-footer modal-footer-custom">
                <button type="button" class="btn btn-outline-primary" id="orderPrintBtn" style="border-radius:10px;display:none">
                    {{ icon('printer') }}
                    In / lưu PDF
                </button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var ORDERS_URL = @json(url('/admin/orders')) + '/';
    var REOPEN_KEY = 'admin_orders_reopen';
    var FALLBACK_IMG = 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=500';

    var modalEl = document.getElementById('orderModal');
    var modal = new bootstrap.Modal(modalEl);
    var titleEl = document.getElementById('orderModalTitle');
    var bodyEl = document.getElementById('orderModalBody');
    var printBtn = document.getElementById('orderPrintBtn');
    var currentOrder = null;
    var updating = false;
    var requestSeq = 0;

    function ic(name, size) {
        var o = {};
        if (size) { o.width = size; o.height = size; }
        return feather.icons[name].toSvg(o);
    }

    function getStatusBadgeClass(status) {
        switch ((status || '').toLowerCase()) {
            case 'pending': return 'badge-status-unpaid';
            case 'confirmed': return 'badge-status-processing';
            case 'shipping': return 'badge-status-shipped';
            case 'delivered': return 'badge-status-paid';
            case 'cancelled': return 'badge-status-cancelled';
            default: return 'bg-secondary text-white';
        }
    }

    function getStatusText(status) {
        switch ((status || '').toLowerCase()) {
            case 'pending': return 'Chờ xác nhận';
            case 'confirmed': return 'Đã xác nhận';
            case 'shipping': return 'Đang giao';
            case 'delivered': return 'Đã giao';
            case 'cancelled': return 'Đã hủy';
            default: return status;
        }
    }

    var SPINNER = '<div class="d-flex justify-content-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

    function renderOrder(o) {
        titleEl.textContent = 'Chi tiết đơn hàng ' + (o.orderCode || ('DH-' + (o.id || '')));
        var paid = o.paymentStatus === 'paid';
        var user = o.user || {};

        var vnpayHtml = '';
        if (o.vnpayTransactionNo) {
            vnpayHtml += '<p class="mb-1"><strong>Mã giao dịch VNPay:</strong> <span class="fw-semibold">' + esc(o.vnpayTransactionNo) + '</span></p>';
        }
        if (o.vnpayBankCode) {
            vnpayHtml += '<p class="mb-1"><strong>Ngân hàng:</strong> <span class="text-uppercase fw-semibold">' + esc(o.vnpayBankCode) + '</span></p>';
        }
        if (o.paidAt) {
            vnpayHtml += '<p class="mb-1"><strong>Thời gian thanh toán:</strong> ' + esc(o.paidAt) + '</p>';
        }

        var itemsHtml = (o.orderItems || []).map(function (item) {
            return '<tr>' +
                '<td><img src="' + esc(item.productImage || FALLBACK_IMG) + '" alt="' + esc(item.productName) + '" class="product-img-thumb" style="width:40px;height:40px"></td>' +
                '<td><div class="fw-semibold">' + esc(item.productName) + '</div><div class="text-muted small">Đơn vị: ' + esc(item.unit) + '</div></td>' +
                '<td class="text-center">' + esc(item.quantity) + '</td>' +
                '<td class="text-end">' + formatCurrency(item.price) + '</td>' +
                '<td class="text-end fw-bold">' + formatCurrency(item.price * item.quantity) + '</td>' +
                '</tr>';
        }).join('');

        // Đơn có mã giảm giá: hiện Tạm tính / Giảm giá để tổng giá trị khớp với danh sách sản phẩm
        // (bản React không có khối này; hóa đơn in vẫn giống React)
        var discountHtml = '';
        if (Number(o.discountAmount) > 0) {
            discountHtml = '<div class="ms-auto mb-4" style="max-width:340px">' +
                '<div class="d-flex justify-content-between py-1"><span class="text-muted">Tạm tính</span><span class="fw-semibold">' + formatCurrency(o.subTotal) + '</span></div>' +
                '<div class="d-flex justify-content-between py-1 text-success"><span>Giảm giá' + (o.voucherCode ? ' (' + esc(o.voucherCode) + ')' : '') + '</span><span class="fw-semibold">-' + formatCurrency(o.discountAmount) + '</span></div>' +
                '<div class="d-flex justify-content-between py-1 border-top"><span class="fw-bold">Tổng thanh toán</span><span class="fw-bold text-danger">' + formatCurrency(o.totalAmount) + '</span></div>' +
                '</div>';
        }

        var actionsHtml = '';
        if (o.status !== 'delivered' && o.status !== 'cancelled') {
            var dis = updating ? ' disabled' : '';
            actionsHtml = '<div class="d-flex flex-wrap gap-2 justify-content-end pt-3 border-top">' +
                '<button type="button" data-next-status="cancelled" class="btn btn-outline-danger d-flex align-items-center gap-1" style="border-radius:10px"' + dis + '>' + ic('x') + '<span>Hủy đơn hàng</span></button>';
            if (o.status === 'pending') {
                actionsHtml += '<button type="button" data-next-status="confirmed" class="btn btn-primary d-flex align-items-center gap-1" style="border-radius:10px;background:var(--accent-gradient);border:none"' + dis + '>' + ic('check') + '<span>Xác nhận đơn</span></button>';
            }
            if (o.status === 'confirmed') {
                actionsHtml += '<button type="button" data-next-status="shipping" class="btn btn-primary d-flex align-items-center gap-1" style="border-radius:10px;background:var(--accent-gradient);border:none"' + dis + '>' + ic('truck') + '<span>Giao hàng</span></button>';
            }
            if (o.status === 'shipping') {
                actionsHtml += '<button type="button" data-next-status="delivered" class="btn btn-success d-flex align-items-center gap-1" style="border-radius:10px"' + dis + '>' + ic('check') + '<span>Hoàn thành đơn</span></button>';
            }
            actionsHtml += '</div>';
        }

        bodyEl.innerHTML =
            '<div>' +
            // Order overview & status
            '<div class="row g-3 mb-4 p-3 bg-light rounded-4">' +
                '<div class="col-12 col-md-6">' +
                    '<p class="mb-1 text-muted small fw-semibold">TRẠNG THÁI ĐƠN HÀNG</p>' +
                    '<span class="badge-status fs-6 ' + getStatusBadgeClass(o.status) + '">' + esc(getStatusText(o.status)) + '</span>' +
                '</div>' +
                '<div class="col-12 col-md-6 text-md-end">' +
                    '<p class="mb-1 text-muted small fw-semibold">TỔNG GIÁ TRỊ</p>' +
                    '<h4 class="fw-bold text-danger mb-0">' + formatCurrency(o.totalAmount) + '</h4>' +
                '</div>' +
            '</div>' +
            // Customer info
            '<div class="row g-3 mb-4">' +
                '<div class="col-12 col-md-6">' +
                    '<h6 class="fw-bold mb-3 border-bottom pb-2">Thông tin giao hàng</h6>' +
                    '<p class="mb-1"><strong>Họ tên:</strong> ' + esc(o.fullName) + '</p>' +
                    '<p class="mb-1"><strong>Điện thoại:</strong> ' + esc(o.phone) + '</p>' +
                    '<p class="mb-1"><strong>Địa chỉ:</strong> ' + esc(o.shippingAddress) + '</p>' +
                    '<p class="mb-1 text-muted small"><strong>Ghi chú:</strong> ' + esc(o.note || 'Không có ghi chú') + '</p>' +
                '</div>' +
                '<div class="col-12 col-md-6">' +
                    '<h6 class="fw-bold mb-3 border-bottom pb-2">Thông tin thanh toán</h6>' +
                    '<p class="mb-1"><strong>Tài khoản đặt:</strong> ' + esc(user.fullName) + ' (' + esc(user.email) + ')</p>' +
                    '<p class="mb-1"><strong>Phương thức:</strong> <span class="fw-semibold">' + esc(o.paymentMethodLabel || o.paymentMethod) + '</span></p>' +
                    '<p class="mb-1"><strong>Trạng thái thanh toán:</strong> ' +
                        '<span class="badge-status ' + (paid ? 'badge-status-paid' : 'badge-status-unpaid') + '">' + (paid ? 'Đã thanh toán' : 'Chưa thanh toán') + '</span></p>' +
                    vnpayHtml +
                    '<p class="mb-0 text-muted small"><strong>Thời gian đặt:</strong> ' + esc(o.createdAt) + '</p>' +
                '</div>' +
            '</div>' +
            // Order items
            '<h6 class="fw-bold mb-3 border-bottom pb-2">Danh sách hải sản đã mua</h6>' +
            '<div class="table-responsive mb-4">' +
                '<table class="table align-middle">' +
                    '<thead class="table-light"><tr><th>Ảnh</th><th>Tên sản phẩm</th><th class="text-center">Số lượng</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr></thead>' +
                    '<tbody>' + itemsHtml + '</tbody>' +
                '</table>' +
            '</div>' +
            discountHtml +
            actionsHtml +
            '</div>';

        printBtn.style.display = '';
    }

    function handleShowDetails(id) {
        var seq = ++requestSeq;
        currentOrder = null;
        titleEl.textContent = 'Chi tiết đơn hàng ';
        bodyEl.innerHTML = SPINNER;
        printBtn.style.display = 'none';
        modal.show();
        api(ORDERS_URL + id)
            .then(function (data) {
                if (seq !== requestSeq) return;
                currentOrder = data;
                renderOrder(data);
            })
            .catch(function (err) {
                console.error(err);
                if (seq !== requestSeq) return;
                toast.error('Không thể tải chi tiết đơn hàng!');
                modal.hide();
            });
    }

    function handleStatusUpdate(id, nextStatus) {
        var statusLabels = {
            confirmed: 'Xác nhận đơn hàng',
            shipping: 'Bắt đầu giao hàng',
            delivered: 'Xác nhận hoàn thành giao hàng',
            cancelled: 'Hủy đơn hàng này'
        };
        confirmDialog(
            'Bạn có chắc chắn muốn thực hiện hành động: "' + statusLabels[nextStatus] + '"?',
            'Xác nhận trạng thái đơn hàng',
            nextStatus === 'cancelled' ? 'danger' : 'primary'
        ).then(function (ok) {
            if (!ok || updating) return;
            updating = true;
            bodyEl.querySelectorAll('[data-next-status]').forEach(function (b) { b.disabled = true; });
            api(ORDERS_URL + id + '/status', { method: 'PUT', data: { status: nextStatus } })
                .then(function () {
                    // Mở lại modal chi tiết (đã cập nhật) sau khi tải lại danh sách
                    try { sessionStorage.setItem(REOPEN_KEY, String(id)); } catch (e) {}
                    reloadWithToast('Cập nhật trạng thái đơn hàng thành công!');
                })
                .catch(function (err) {
                    console.error(err);
                    toast.error(err.message || 'Không thể cập nhật trạng thái đơn hàng!');
                    updating = false;
                    bodyEl.querySelectorAll('[data-next-status]').forEach(function (b) { b.disabled = false; });
                });
        });
    }

    function buildInvoiceHtml(order) {
        var safe = function (value) { return esc(value == null ? '' : value); };
        var code = order.orderCode || ('DH-' + order.id);
        var user = order.user || {};
        var rows = (order.orderItems || []).map(function (item, index) {
            return '<tr>' +
                '<td class="center">' + (index + 1) + '</td>' +
                '<td><strong>' + safe(item.productName) + '</strong><div class="muted">Đơn vị: ' + safe(item.unit || '-') + '</div></td>' +
                '<td class="center">' + item.quantity + '</td>' +
                '<td class="right">' + formatCurrency(item.price) + '</td>' +
                '<td class="right strong">' + formatCurrency(item.price * item.quantity) + '</td>' +
                '</tr>';
        }).join('');

        return '<!doctype html><html><head><meta charset="utf-8" />' +
            '<title>Hoa don ' + code + '</title>' +
            '<style>' +
            '* { box-sizing: border-box; }' +
            'body { font-family: Arial, sans-serif; color: #1f2937; margin: 0; background: #fff; }' +
            '.invoice { max-width: 860px; margin: 0 auto; padding: 32px; }' +
            '.top { display: flex; justify-content: space-between; gap: 24px; border-bottom: 3px solid #0d9488; padding-bottom: 18px; }' +
            '.brand h1 { margin: 0 0 6px; font-size: 26px; color: #0f766e; }' +
            '.brand p, .meta p { margin: 3px 0; font-size: 13px; color: #64748b; }' +
            '.meta { text-align: right; }' +
            '.meta h2 { margin: 0 0 8px; font-size: 22px; color: #111827; }' +
            '.pill { display: inline-block; padding: 5px 10px; border-radius: 999px; background: #ecfdf5; color: #047857; font-weight: 700; font-size: 12px; }' +
            '.section { margin-top: 22px; }' +
            '.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }' +
            '.box { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }' +
            '.box h3 { margin: 0 0 10px; font-size: 14px; text-transform: uppercase; color: #475569; }' +
            '.box p { margin: 5px 0; font-size: 13px; }' +
            'table { width: 100%; border-collapse: collapse; margin-top: 12px; }' +
            'th { background: #0f766e; color: #fff; text-align: left; font-size: 12px; padding: 10px; }' +
            'td { border: 1px solid #e5e7eb; padding: 10px; font-size: 13px; vertical-align: top; }' +
            '.center { text-align: center; }' +
            '.right { text-align: right; }' +
            '.strong { font-weight: 700; }' +
            '.muted { color: #64748b; font-size: 12px; margin-top: 3px; }' +
            '.summary { width: 340px; margin-left: auto; margin-top: 18px; }' +
            '.summary-row { display: grid; grid-template-columns: 1fr 150px; gap: 16px; align-items: center; padding: 8px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }' +
            '.summary-row span:first-child { color: #374151; font-weight: 500; }' +
            '.summary-row strong, .summary-row span:last-child { text-align: right; font-weight: 700; }' +
            '.summary-row.total { border-bottom: none; padding-top: 10px; font-size: 15px; }' +
            '.summary-row.total span:first-child { color: #111827; font-weight: 800; }' +
            '.summary-row.total span:last-child { color: #dc2626; font-size: 16px; font-weight: 800; }' +
            '.footer { margin-top: 34px; display: flex; justify-content: space-between; gap: 40px; color: #475569; font-size: 13px; }' +
            '.sign { width: 220px; text-align: center; padding-top: 8px; }' +
            '@media print { @page { size: A4; margin: 12mm; } .invoice { padding: 0; } }' +
            '</style></head><body>' +
            '<div class="invoice">' +
                '<div class="top">' +
                    '<div class="brand">' +
                        '<h1>Seafood Store</h1>' +
                        '<p>Hải sản tươi sống giao tận nơi</p>' +
                        '<p>Hotline: 0987 654 321</p>' +
                        '<p>Địa chỉ: 123 Đường Hải Sản, TP. HCM</p>' +
                    '</div>' +
                    '<div class="meta">' +
                        '<h2>Hóa đơn bán hàng</h2>' +
                        '<p><strong>Mã đơn:</strong> ' + safe(code) + '</p>' +
                        '<p><strong>Ngày đặt:</strong> ' + safe(order.createdAt) + '</p>' +
                        '<span class="pill">' + safe(getStatusText(order.status)) + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="section grid">' +
                    '<div class="box">' +
                        '<h3>Thông tin giao hàng</h3>' +
                        '<p><strong>Khách hàng:</strong> ' + safe(order.fullName || '-') + '</p>' +
                        '<p><strong>Điện thoại:</strong> ' + safe(order.phone || '-') + '</p>' +
                        '<p><strong>Địa chỉ:</strong> ' + safe(order.shippingAddress || '-') + '</p>' +
                        '<p><strong>Ghi chú:</strong> ' + safe(order.note || 'Không có') + '</p>' +
                    '</div>' +
                    '<div class="box">' +
                        '<h3>Thanh toán</h3>' +
                        '<p><strong>Phương thức:</strong> ' + safe((order.paymentMethod || '').toUpperCase()) + '</p>' +
                        '<p><strong>Trạng thái:</strong> ' + (order.paymentStatus === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán') + '</p>' +
                        (order.vnpayTransactionNo ? '<p><strong>Mã GD VNPay:</strong> ' + safe(order.vnpayTransactionNo) + '</p>' : '') +
                        '<p><strong>Tài khoản đặt:</strong> ' + safe(user.fullName || '-') + '</p>' +
                        '<p><strong>Email:</strong> ' + safe(user.email || '-') + '</p>' +
                    '</div>' +
                '</div>' +
                '<div class="section">' +
                    '<h3>Danh sách sản phẩm</h3>' +
                    '<table><thead><tr>' +
                        '<th class="center">STT</th><th>Sản phẩm</th><th class="center">Số lượng</th><th class="right">Đơn giá</th><th class="right">Thành tiền</th>' +
                    '</tr></thead><tbody>' + rows + '</tbody></table>' +
                '</div>' +
                '<div class="summary">' +
                    '<div class="summary-row"><span>Tạm tính</span><strong>' + formatCurrency(order.subTotal || order.totalAmount) + '</strong></div>' +
                    '<div class="summary-row"><span>Giảm giá</span><strong>' + formatCurrency(order.discountAmount || 0) + '</strong></div>' +
                    '<div class="summary-row total"><span>Tổng thanh toán</span><span>' + formatCurrency(order.totalAmount) + '</span></div>' +
                '</div>' +
                '<div class="footer">' +
                    '<div>Cảm ơn quý khách đã mua hàng tại Seafood Store.</div>' +
                    '<div class="sign">Người lập phiếu<br/><br/><br/>________________</div>' +
                '</div>' +
            '</div>' +
            '</body></html>';
    }

    function handlePrintOrder(order) {
        if (!order) return;
        var frame = document.createElement('iframe');
        frame.style.position = 'fixed';
        frame.style.right = '0';
        frame.style.bottom = '0';
        frame.style.width = '0';
        frame.style.height = '0';
        frame.style.border = '0';
        document.body.appendChild(frame);
        frame.contentDocument.open();
        frame.contentDocument.write(buildInvoiceHtml(order));
        frame.contentDocument.close();
        setTimeout(function () {
            if (frame.contentWindow) {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            }
            setTimeout(function () { document.body.removeChild(frame); }, 1000);
        }, 150);
    }

    document.querySelectorAll('[data-show-order]').forEach(function (btn) {
        btn.addEventListener('click', function () { handleShowDetails(btn.dataset.showOrder); });
    });

    bodyEl.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-next-status]');
        if (!btn || !currentOrder || btn.disabled) return;
        handleStatusUpdate(currentOrder.id, btn.dataset.nextStatus);
    });

    printBtn.addEventListener('click', function () { handlePrintOrder(currentOrder); });

    // Sau khi cập nhật trạng thái => tải lại trang và mở lại chi tiết đơn
    try {
        var reopenId = sessionStorage.getItem(REOPEN_KEY);
        if (reopenId) {
            sessionStorage.removeItem(REOPEN_KEY);
            handleShowDetails(reopenId);
        }
    } catch (e) {}
})();
</script>
@endpush
