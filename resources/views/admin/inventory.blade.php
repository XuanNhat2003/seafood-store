@extends('layouts.admin')

@section('title', 'Kho hàng - Seafood Admin')

@php
    $fallbackImg = 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=500';
@endphp

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý kho hàng</h4>
            <p class="text-muted small mb-0">Theo dõi số lượng tồn kho sản phẩm hải sản, cảnh báo sắp hết hàng và nhập hàng nhanh</p>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="row g-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card p-3" style="border-left:4px solid #64748b">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Tổng mặt hàng</p>
                        <h3 class="stat-value">{{ $totalCount }}</h3>
                    </div>
                    <div class="stat-icon bg-light text-secondary">
                        {{ icon('archive') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card p-3" style="border-left:4px solid #ef4444">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Hết hàng hoàn toàn</p>
                        <h3 class="stat-value text-danger">{{ $outOfStockCount }}</h3>
                    </div>
                    <div class="stat-icon bg-danger-subtle text-danger" style="background-color:rgba(239, 68, 68, 0.1)">
                        {{ icon('alert-triangle') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card p-3" style="border-left:4px solid #f59e0b">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Cảnh báo sắp hết (≤5)</p>
                        <h3 class="stat-value text-warning">{{ $lowStockCount }}</h3>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning" style="background-color:rgba(245, 158, 11, 0.1)">
                        {{ icon('alert-triangle') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card p-3" style="border-left:4px solid #10b981">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Tồn kho an toàn</p>
                        <h3 class="stat-value text-success">{{ $normalStockCount }}</h3>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success" style="background-color:rgba(16, 185, 129, 0.1)">
                        {{ icon('check-circle') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="custom-table-container py-3 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">{{ icon('search') }}</span>
                    <input type="text" id="invSearch" class="form-control bg-light border-0"
                           placeholder="Tìm theo mã SP hoặc tên hải sản..." style="border-radius:0 8px 8px 0">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <select id="invCategory" class="form-select bg-light border-0" style="border-radius:8px">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->name }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <select id="invStock" class="form-select bg-light border-0" style="border-radius:8px">
                    <option value="all">Tất cả mức tồn kho</option>
                    <option value="out">Đã hết hàng hoàn toàn (0)</option>
                    <option value="low">Sắp hết hàng (≤ 5)</option>
                    <option value="normal">Tồn kho an toàn (&gt; 5)</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="button" id="invRefresh" class="btn btn-secondary w-100 py-2" style="border-radius:8px">
                    Làm mới kho
                </button>
            </div>
        </div>
    </div>

    {{-- Inventory Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table custom-table align-middle">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên hải sản</th>
                        <th>Danh mục</th>
                        <th>Đơn giá</th>
                        <th class="text-center">Số lượng tồn</th>
                        <th>Đơn vị tính</th>
                        <th>Trạng thái tồn kho</th>
                        <th>Cập nhật cuối</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        @php
                            $isOut = $item['stockQuantity'] === 0;
                            $isLow = $item['stockQuantity'] > 0 && $item['isLowStock'];
                            $code = $item['productCode'] ?: 'SP' . str_pad($item['id'], 3, '0', STR_PAD_LEFT);
                            $img = img_url($item['imageUrl'], $fallbackImg);
                            $json = [
                                'id' => $item['id'],
                                'productName' => $item['productName'],
                                'imageUrl' => $img,
                                'stockQuantity' => $item['stockQuantity'],
                                'unit' => $item['unit'],
                            ];
                        @endphp
                        <tr data-inv-row
                            data-name="{{ mb_strtolower($item['productName'], 'UTF-8') }}"
                            data-code="{{ mb_strtolower($code, 'UTF-8') }}"
                            data-id="{{ $item['id'] }}"
                            data-category="{{ $item['categoryName'] }}"
                            data-stock="{{ $item['stockQuantity'] }}"
                            data-low="{{ $item['isLowStock'] ? '1' : '0' }}">
                            <td>
                                <img src="{{ $img }}" alt="{{ $item['productName'] }}" class="product-img-thumb">
                            </td>
                            <td>
                                <div class="fw-bold">{{ $item['productName'] }}</div>
                                <span class="text-muted small">Mã sản phẩm: {{ $code }}</span>
                            </td>
                            <td>{{ $item['categoryName'] }}</td>
                            <td class="fw-semibold">{{ format_price($item['price']) }}</td>
                            <td class="text-center">
                                <span class="fs-6 fw-bold {{ $isOut ? 'text-danger' : ($isLow ? 'text-warning' : 'text-success') }}">
                                    {{ $item['stockQuantity'] }}
                                </span>
                            </td>
                            <td>{{ $item['unit'] }}</td>
                            <td>
                                @if ($isOut)
                                    <span class="badge-low-stock" style="background:#fef2f2;color:#ef4444;border:1px solid #fee2e2">
                                        Hết hàng hoàn toàn
                                    </span>
                                @elseif ($isLow)
                                    <span class="badge-low-stock">
                                        Cảnh báo: Sắp hết hàng
                                    </span>
                                @else
                                    <span class="badge-low-stock" style="background:#f0fdf4;color:#16a34a;border:1px solid #dcfce7">
                                        Bình thường
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ format_date($item['updatedAt']) }}</td>
                            <td class="text-end">
                                <button type="button" data-import='@json($json)'
                                        class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 ms-auto py-2 px-3"
                                        style="border-radius:10px">
                                    {{ icon('plus') }}
                                    <span>Nhập thêm hàng</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="invEmpty" style="{{ $items->count() ? 'display:none' : '' }}">
                        <td colspan="9" class="text-center text-muted py-4">Không tìm thấy sản phẩm nào trong kho khớp bộ lọc</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Quick Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold">Nhập thêm hải sản vào kho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm">
                <div class="modal-body p-4">
                    <div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 mb-4">
                            <img src="" alt="" id="impImg" class="product-img-thumb" style="width:56px;height:56px">
                            <div>
                                <h6 class="fw-bold mb-1" id="impName"></h6>
                                <p class="text-muted small mb-0">
                                    Tồn kho hiện tại: <strong class="text-primary" id="impStock"></strong>
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="form-label small fw-bold">Số lượng nhập thêm vào kho (<span id="impUnit"></span>)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">{{ icon('trending-up') }}</span>
                                <input type="number" name="quantity" class="form-control bg-light border-0 py-2 fs-5 fw-bold"
                                       style="border-radius:0 12px 12px 0" required min="1" value="10">
                            </div>
                            <div class="form-text text-muted">
                                Số lượng nhập mới sẽ được cộng dồn trực tiếp vào số lượng tồn hiện có trong cơ sở dữ liệu.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:12px">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="impSubmit"
                            style="border-radius:12px;background:var(--accent-gradient);border:none">Xác nhận nhập kho</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var IMPORT_URL = @json(route('admin.inventory.import'));

    // ── Filters (client-side, như React) ───────────────────────────────────
    var searchEl = document.getElementById('invSearch');
    var catEl = document.getElementById('invCategory');
    var stockEl = document.getElementById('invStock');

    function applyFilters() {
        var keyword = searchEl.value.trim().toLowerCase();
        var cat = catEl.value;
        var stockFilter = stockEl.value;
        var n = 0;
        document.querySelectorAll('[data-inv-row]').forEach(function (tr) {
            var d = tr.dataset;
            var stock = parseInt(d.stock, 10);
            var isLow = d.low === '1';
            var matchesSearch = !keyword ||
                d.name.indexOf(keyword) !== -1 ||
                d.code.indexOf(keyword) !== -1 ||
                String(d.id).indexOf(keyword) !== -1;
            var matchesCategory = cat ? d.category === cat : true;
            var matchesStock = true;
            if (stockFilter === 'out') matchesStock = stock === 0;
            else if (stockFilter === 'low') matchesStock = stock > 0 && isLow;
            else if (stockFilter === 'normal') matchesStock = stock > 5;

            var show = matchesSearch && matchesCategory && matchesStock;
            tr.style.display = show ? '' : 'none';
            if (show) n++;
        });
        document.getElementById('invEmpty').style.display = n > 0 ? 'none' : '';

        // Giữ bộ lọc trên URL để sau khi reload (nhập kho / làm mới) bộ lọc vẫn còn như state React
        try {
            var params = new URLSearchParams(window.location.search);
            searchEl.value ? params.set('search', searchEl.value) : params.delete('search');
            cat ? params.set('category', cat) : params.delete('category');
            stockFilter !== 'all' ? params.set('stock', stockFilter) : params.delete('stock');
            var qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        } catch (e) {}
    }
    searchEl.addEventListener('input', applyFilters);
    catEl.addEventListener('change', applyFilters);
    stockEl.addEventListener('change', applyFilters);

    (function restoreFilters() {
        var params = new URLSearchParams(window.location.search);
        if (params.has('search')) searchEl.value = params.get('search');
        if (params.has('category')) catEl.value = params.get('category');
        if (params.has('stock')) stockEl.value = params.get('stock');
        if (catEl.selectedIndex < 0) catEl.value = '';
        if (stockEl.selectedIndex < 0) stockEl.value = 'all';
        if (params.has('search') || params.has('category') || params.has('stock')) applyFilters();
    })();

    document.getElementById('invRefresh').addEventListener('click', function () { window.location.reload(); });

    // ── Import modal ───────────────────────────────────────────────────────
    var modalEl = document.getElementById('importModal');
    var modal = new bootstrap.Modal(modalEl);
    var form = document.getElementById('importForm');
    var qtyInput = form.elements.quantity;
    var submitBtn = document.getElementById('impSubmit');
    var selected = null, submitting = false;

    document.querySelectorAll('[data-import]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            selected = JSON.parse(btn.dataset.import);
            document.getElementById('impImg').src = selected.imageUrl;
            document.getElementById('impImg').alt = selected.productName;
            document.getElementById('impName').textContent = selected.productName;
            document.getElementById('impStock').textContent = selected.stockQuantity + ' ' + selected.unit;
            document.getElementById('impUnit').textContent = selected.unit;
            qtyInput.value = '10';
            modal.show();
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (submitting || !selected) return;
        var qty = parseInt(qtyInput.value, 10);
        if (isNaN(qty) || qty <= 0) {
            return toast.error('Số lượng nhập hàng phải lớn hơn 0!');
        }
        submitting = true;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang cập nhật...';
        api(IMPORT_URL, { method: 'POST', data: { productId: selected.id, quantity: qty } })
            .then(function () {
                modal.hide();
                reloadWithToast('Đã nhập thêm ' + qty + ' ' + selected.unit + ' cho sản phẩm "' + selected.productName + '" thành công!');
            })
            .catch(function (err) {
                toast.error(err.message || 'Có lỗi xảy ra khi nhập thêm hàng vào kho!');
                submitting = false;
                submitBtn.disabled = false;
                submitBtn.textContent = 'Xác nhận nhập kho';
            });
    });
})();
</script>
@endpush
