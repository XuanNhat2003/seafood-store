@extends('layouts.admin')

@section('title', 'Khuyến mãi - Seafood Admin')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý mã khuyến mãi</h4>
            <p class="text-muted small mb-0">Thiết lập các chương trình ưu đãi và giảm giá cho đơn hàng hải sản</p>
        </div>
        <button type="button" id="btnAddVoucher"
                class="btn btn-primary d-flex align-items-center gap-2 py-2 px-3"
                style="border-radius:12px;background:var(--accent-gradient);border:none">
            {{ icon('plus') }}
            <span>Thêm mã mới</span>
        </button>
    </div>

    {{-- Mini Stats Summary --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card stat-card p-3" style="border-left:4px solid #3b82f6">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Tổng số Voucher</p>
                        <h3 class="stat-value">{{ $totalCount }}</h3>
                    </div>
                    <div class="stat-icon" style="background-color:rgba(59, 130, 246, 0.1);color:#3b82f6">
                        {{ icon('percent') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card p-3" style="border-left:4px solid #10b981">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Voucher đang hoạt động</p>
                        <h3 class="stat-value text-success">{{ $activeCount }}</h3>
                    </div>
                    <div class="stat-icon" style="background-color:rgba(16, 185, 129, 0.1);color:#10b981">
                        {{ icon('percent') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card p-3" style="border-left:4px solid #ef4444">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Voucher đã hết hạn</p>
                        <h3 class="stat-value text-danger">{{ $expiredCount }}</h3>
                    </div>
                    <div class="stat-icon" style="background-color:rgba(239, 68, 68, 0.1);color:#ef4444">
                        {{ icon('calendar') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="custom-table-container py-3 mb-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">{{ icon('search') }}</span>
                    <input type="text" id="voucherSearch" class="form-control bg-light border-0"
                           placeholder="Tìm mã voucher (VD: SEAFOOD10)..." style="border-radius:0 8px 8px 0">
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px">STT</th>
                        <th class="text-center" style="width:160px">Mã khuyến mãi</th>
                        <th class="text-center">Loại giảm giá</th>
                        <th>Giá trị giảm</th>
                        <th>Đơn tối thiểu</th>
                        <th class="text-center">Hạn sử dụng</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="voucherBody">
                    @foreach ($vouchers as $v)
                        @php
                            $isExpired = $v->expiry_date->lt($now);
                            $isOn = $v->is_active && ! $isExpired;
                            $isPct = $v->discount_type === 'percentage';
                            $json = [
                                'id' => $v->id,
                                'code' => $v->code,
                                'discountAmount' => (float) $v->discount_amount,
                                'discountType' => $v->discount_type,
                                'minOrderAmount' => (float) $v->min_order_amount,
                                'expiryDate' => $v->expiry_date->toIso8601String(),
                                'expiryDateInput' => $v->expiry_date->copy()->utc()->format('Y-m-d'),
                                'isActive' => (bool) $v->is_active,
                            ];
                        @endphp
                        <tr data-voucher-row data-code="{{ mb_strtolower($v->code, 'UTF-8') }}">
                            <td class="text-center fw-bold text-muted" data-stt>{{ $loop->iteration }}</td>
                            <td class="text-center fw-bold">
                                <span class="badge bg-light text-primary border px-2 py-1">{{ $v->code }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge-status {{ $isPct ? 'bg-info text-dark' : 'bg-primary text-white' }}">
                                    {{ $isPct ? 'Theo %' : 'Số tiền cố định' }}
                                </span>
                            </td>
                            <td class="fw-bold">
                                {{ $isPct ? $v->discount_amount . '%' : format_price($v->discount_amount) }}
                            </td>
                            <td>{{ format_price($v->min_order_amount) }}</td>
                            <td class="text-center">
                                <span class="{{ $isExpired ? 'text-danger fw-semibold' : '' }}">{{ $v->expiry_date->format('d/m/Y') }}{{ $isExpired ? ' (Hết hạn)' : '' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" data-toggle-voucher='@json($json)'
                                           {{ $isOn ? 'checked' : '' }} {{ $isExpired ? 'disabled' : '' }}
                                           style="cursor:{{ $isExpired ? 'not-allowed' : 'pointer' }}">
                                    <span class="badge-status {{ $isOn ? 'badge-status-paid' : 'badge-status-cancelled' }}">
                                        {{ $isOn ? 'Hoạt động' : ($isExpired ? 'Hết hạn' : 'Đã khóa') }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-info border-0" style="border-radius:8px"
                                            title="Sửa voucher" data-edit-voucher='@json($json)'>
                                        {{ icon('edit-2', 16) }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" style="border-radius:8px"
                                            title="Xóa voucher" data-delete-voucher="{{ $v->id }}" data-code="{{ $v->code }}">
                                        {{ icon('trash-2', 16) }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="voucherEmpty" style="{{ $vouchers->count() ? 'display:none' : '' }}">
                        <td colspan="7" class="text-center text-muted py-4">Chưa có mã khuyến mãi nào</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal fade" id="voucherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold" id="voucherModalTitle">Thêm mã khuyến mãi mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="voucherForm">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Mã khuyến mãi (Code)</label>
                            <input type="text" name="code" maxlength="50" class="form-control form-control-custom text-uppercase"
                                   placeholder="Ví dụ: SEAFOOD15" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Loại giảm giá</label>
                            <select name="discountType" class="form-select bg-light border-0 py-2" style="border-radius:12px">
                                <option value="fixed">Số tiền cố định (đ)</option>
                                <option value="percentage">Phần trăm (%)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold" id="discountLabel">Số tiền giảm (đ)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <span id="iconPercent" style="display:none">{{ icon('percent') }}</span>
                                    <span id="iconDollar">{{ icon('dollar-sign') }}</span>
                                </span>
                                <input type="number" name="discountAmount" class="form-control bg-light border-0 py-2"
                                       style="border-radius:0 12px 12px 0" placeholder="Ví dụ: 50000"
                                       required min="0" step="1000">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Giá trị đơn hàng tối thiểu (đ)</label>
                            <input type="number" name="minOrderAmount" class="form-control form-control-custom"
                                   placeholder="100000" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ngày hết hạn</label>
                            <input type="date" name="expiryDate" class="form-control form-control-custom" required>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="voucher-active-switch" name="isActive">
                                <label class="form-check-label" for="voucher-active-switch">Trạng thái kích hoạt hoạt động</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:12px">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="voucherSubmit"
                            style="border-radius:12px;background:var(--accent-gradient);border:none">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var BASE = @json(url('/admin/vouchers'));
    var modalEl = document.getElementById('voucherModal');
    var modal = new bootstrap.Modal(modalEl);
    var form = document.getElementById('voucherForm');
    var f = form.elements;
    var submitBtn = document.getElementById('voucherSubmit');
    var isEdit = false, selectedId = null, discountType = 'fixed', submitting = false;

    // ── Search filter (client-side, như React) ─────────────────────────────
    var searchInput = document.getElementById('voucherSearch');
    function applySearch() {
        var kw = searchInput.value.toLowerCase();
        var n = 0;
        document.querySelectorAll('[data-voucher-row]').forEach(function (tr) {
            var match = tr.dataset.code.indexOf(kw) !== -1;
            tr.style.display = match ? '' : 'none';
            if (match) { n++; tr.querySelector('[data-stt]').textContent = n; }
        });
        document.getElementById('voucherEmpty').style.display = n > 0 ? 'none' : '';
        // Giữ từ khóa trên URL để sau khi reload (thêm/sửa/xóa) vẫn còn như state React
        try {
            var params = new URLSearchParams(window.location.search);
            searchInput.value ? params.set('search', searchInput.value) : params.delete('search');
            var qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        } catch (e) {}
    }
    searchInput.addEventListener('input', applySearch);
    (function () {
        var params = new URLSearchParams(window.location.search);
        if (params.has('search')) { searchInput.value = params.get('search'); applySearch(); }
    })();

    // ── Discount type UI ───────────────────────────────────────────────────
    function applyDiscountTypeUI() {
        var pct = discountType === 'percentage';
        document.getElementById('discountLabel').textContent = pct ? 'Phần trăm giảm (%)' : 'Số tiền giảm (đ)';
        document.getElementById('iconPercent').style.display = pct ? '' : 'none';
        document.getElementById('iconDollar').style.display = pct ? 'none' : '';
        f.discountAmount.placeholder = pct ? 'Ví dụ: 10' : 'Ví dụ: 50000';
        // Bước nhảy HTML tính từ `min`: với min=1/step=1000 trình duyệt chỉ chấp nhận 1, 1001, 2001…
        // (chặn cả 50000). Tiền cố định dùng min=0 (JS vẫn bắt buộc > 0), phần trăm giữ min=1/step=1.
        f.discountAmount.step = pct ? '1' : '1000';
        f.discountAmount.min = pct ? '1' : '0';
        if (pct) f.discountAmount.max = '100'; else f.discountAmount.removeAttribute('max');
    }
    f.discountType.addEventListener('change', function () {
        var next = f.discountType.value;
        if (next === discountType) return;
        discountType = next;
        f.discountAmount.value = next === 'percentage' ? '10' : '50000';
        applyDiscountTypeUI();
    });

    // ── Open Add / Edit ────────────────────────────────────────────────────
    document.getElementById('btnAddVoucher').addEventListener('click', function () {
        isEdit = false; selectedId = null;
        f.code.value = ''; f.code.disabled = false;
        discountType = 'fixed'; f.discountType.value = 'fixed';
        f.discountAmount.value = '';
        f.minOrderAmount.value = '0';
        var d = new Date(); d.setDate(d.getDate() + 7);
        f.expiryDate.value = d.toISOString().split('T')[0];
        f.isActive.checked = true;
        document.getElementById('voucherModalTitle').textContent = 'Thêm mã khuyến mãi mới';
        applyDiscountTypeUI();
        modal.show();
    });

    document.querySelectorAll('[data-edit-voucher]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var v = JSON.parse(btn.dataset.editVoucher);
            isEdit = true; selectedId = v.id;
            f.code.value = v.code; f.code.disabled = true;
            discountType = v.discountType; f.discountType.value = v.discountType;
            f.discountAmount.value = v.discountAmount.toString();
            f.minOrderAmount.value = v.minOrderAmount.toString();
            f.expiryDate.value = v.expiryDateInput;
            f.isActive.checked = v.isActive;
            document.getElementById('voucherModalTitle').textContent = 'Sửa mã khuyến mãi ' + v.code;
            applyDiscountTypeUI();
            modal.show();
        });
    });

    // ── Submit ─────────────────────────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (submitting) return;
        var code = f.code.value, amount = f.discountAmount.value, expiry = f.expiryDate.value;

        if (!code.trim()) return toast.error('Vui lòng nhập mã code!');
        if (!amount || parseFloat(amount) <= 0) {
            return toast.error(discountType === 'percentage' ? 'Phần trăm giảm phải lớn hơn 0!' : 'Số tiền giảm phải lớn hơn 0!');
        }
        if (discountType === 'percentage' && parseFloat(amount) > 100) return toast.error('Phần trăm giảm tối đa là 100%!');
        if (!expiry) return toast.error('Vui lòng chọn ngày hết hạn!');

        var payload = {
            code: code.trim().toUpperCase(),
            discountAmount: parseFloat(amount),
            discountType: discountType,
            minOrderAmount: parseFloat(f.minOrderAmount.value || 0),
            expiryDate: expiry, // 'YYYY-MM-DD' — server hiểu như new Date(expiry).toISOString() (00:00 UTC)
            isActive: f.isActive.checked
        };

        submitting = true;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang lưu...';
        var req = isEdit
            ? api(BASE + '/' + selectedId, { method: 'PUT', data: payload })
            : api(BASE, { method: 'POST', data: payload });
        req.then(function () {
            modal.hide();
            reloadWithToast(isEdit ? 'Cập nhật mã khuyến mãi thành công!' : 'Thêm mã khuyến mãi thành công!');
        }).catch(function (err) {
            toast.error(err.message || 'Lỗi khi lưu mã khuyến mãi!');
            submitting = false;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Lưu lại';
        });
    });

    // ── Toggle status ──────────────────────────────────────────────────────
    document.querySelectorAll('[data-toggle-voucher]').forEach(function (input) {
        input.addEventListener('change', function () {
            var v = JSON.parse(input.dataset.toggleVoucher);
            var original = !input.checked;
            input.checked = original; // giữ nguyên tới khi server xác nhận (controlled input như React)
            input.disabled = true;
            var payload = {
                code: v.code,
                discountAmount: v.discountAmount,
                discountType: v.discountType,
                minOrderAmount: v.minOrderAmount,
                expiryDate: v.expiryDate,
                isActive: !v.isActive
            };
            api(BASE + '/' + v.id, { method: 'PUT', data: payload })
                .then(function () {
                    reloadWithToast('Đã ' + (!v.isActive ? 'kích hoạt' : 'vô hiệu hóa') + ' mã ' + v.code);
                })
                .catch(function () {
                    toast.error('Không thể cập nhật trạng thái voucher!');
                    input.disabled = false;
                });
        });
    });

    // ── Delete ─────────────────────────────────────────────────────────────
    document.querySelectorAll('[data-delete-voucher]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.dataset.deleteVoucher, code = btn.dataset.code;
            confirmDialog('Bạn có chắc chắn muốn xóa mã khuyến mãi "' + code + '"?', 'Xóa mã khuyến mãi', 'danger')
                .then(function (ok) {
                    if (!ok) return;
                    api(BASE + '/' + id, { method: 'DELETE' })
                        .then(function () { reloadWithToast('Đã xóa mã khuyến mãi "' + code + '" thành công!'); })
                        .catch(function () { toast.error('Không thể xóa mã khuyến mãi!'); });
                });
        });
    });
})();
</script>
@endpush
