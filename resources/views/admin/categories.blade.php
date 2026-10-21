@extends('layouts.admin')

@section('title', 'Danh mục - Seafood Admin')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Quản lý danh mục hải sản</h4>
        <button type="button" id="btnAddCategory"
                class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2"
                style="border-radius:12px;background:var(--accent-gradient);border:none">
            {{ icon('plus') }}
            <span>Thêm danh mục</span>
        </button>
    </div>

    {{-- Categories Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px">STT</th>
                        <th>Ảnh đại diện</th>
                        <th>Tên danh mục</th>
                        <th class="text-center">Loại</th>
                        <th>Danh mục cha</th>
                        <th>Đường dẫn tĩnh (Slug)</th>
                        <th>Mô tả ngắn</th>
                        <th class="text-center">Thứ tự</th>
                        <th class="text-center">Số sản phẩm</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $c)
                        @php
                            $json = ['id' => $c->id, 'name' => $c->name, 'description' => $c->description, 'imageUrl' => $c->image_url,
                                     'parentId' => $c->parent_id, 'sortOrder' => $c->sort_order, 'isActive' => $c->is_active];
                        @endphp
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ img_url($c->image_url, 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=100') }}"
                                     alt="{{ $c->name }}" class="product-img-thumb" style="width:40px;height:40px;border-radius:6px">
                            </td>
                            <td class="{{ $c->parent_id === null ? 'fw-bold' : 'cat-child-name' }}">{{ $c->name }}</td>
                            <td class="text-center">
                                @if ($c->parent_id === null)
                                    <span class="badge-parent">Danh mục gốc</span>
                                @else
                                    <span class="badge-child">Danh mục con</span>
                                @endif
                            </td>
                            <td class="text-secondary">{{ $c->parent ? $c->parent->name : '—' }}</td>
                            <td class="text-secondary">{{ $c->slug }}</td>
                            <td class="text-muted small" style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $c->description ?: '-' }}</td>
                            <td class="text-center text-muted">{{ $c->sort_order ?? 0 }}</td>
                            <td class="text-center text-muted">{{ $c->products_count ?? 0 }}</td>
                            <td class="text-center">
                                <span class="badge-status {{ $c->is_active ? 'badge-status-paid' : 'badge-status-cancelled' }}">
                                    {{ $c->is_active ? 'Hoạt động' : 'Ngưng hoạt động' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary border-0" style="border-radius:8px"
                                            title="Chỉnh sửa" data-edit-category='@json($json)'>
                                        {{ icon('edit-2', 16) }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" style="border-radius:8px"
                                            title="Ngưng hoạt động" data-delete-category="{{ $c->id }}" {{ $c->is_active ? '' : 'disabled' }}>
                                        {{ icon('trash-2', 16) }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted py-4">Chưa có danh mục nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold" id="categoryModalTitle">Thêm danh mục mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" novalidate>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="cat-name">Tên danh mục <span class="req-star">*</span></label>
                        <input type="text" id="cat-name" class="form-control" maxlength="180" placeholder="Ví dụ: Tôm các loại" required style="border-radius:10px">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="cat-parent">Danh mục cha</label>
                        <select id="cat-parent" class="form-select" style="border-radius:10px">
                            <option value="">— Danh mục gốc —</option>
                            @foreach ($parentOptions as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="cat-sort">Thứ tự hiển thị</label>
                        <input type="number" id="cat-sort" class="form-control" placeholder="0" style="border-radius:10px">
                    </div>

                    <div class="mb-3">
                        <div id="cat-image" data-image-upload data-name="image_url" data-value="" data-label="Hình ảnh danh mục"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="cat-desc">Mô tả danh mục</label>
                        <textarea id="cat-desc" class="form-control" rows="3" placeholder="Mô tả ngắn gọn về danh mục..." style="border-radius:10px"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch fw-semibold text-muted small">
                            <input class="form-check-input" type="checkbox" id="cat-active-switch" checked>
                            <label class="form-check-label" for="cat-active-switch">Cho phép hiển thị và hoạt động</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="categorySubmit"
                            style="border-radius:10px;background:var(--accent-gradient);border:none">Lưu danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var BASE_URL = @json(url('/admin/categories'));
    var modalEl = document.getElementById('categoryModal');
    var modal = new bootstrap.Modal(modalEl);
    var form = document.getElementById('categoryForm');
    var titleEl = document.getElementById('categoryModalTitle');
    var nameEl = document.getElementById('cat-name');
    var parentEl = document.getElementById('cat-parent');
    var sortEl = document.getElementById('cat-sort');
    var imageEl = document.getElementById('cat-image');
    var descEl = document.getElementById('cat-desc');
    var activeEl = document.getElementById('cat-active-switch');
    var submitBtn = document.getElementById('categorySubmit');

    var modalType = 'add';
    var current = null;
    var submitting = false;

    // Danh mục gốc làm lựa chọn cha (không thể là cha của chính nó)
    function refreshParentOptions() {
        Array.prototype.forEach.call(parentEl.options, function (opt) {
            if (opt.value === '') return;
            var hide = current && String(current.id) === opt.value;
            opt.hidden = hide;
            opt.disabled = hide;
        });
    }

    function setImage(v) {
        if (typeof imageEl.setValue === 'function') imageEl.setValue(v || '');
        else imageEl.dataset.value = v || '';
    }

    function openModal(type, cat) {
        modalType = type;
        current = cat;
        titleEl.textContent = type === 'add' ? 'Thêm danh mục mới' : 'Chỉnh sửa danh mục';
        nameEl.value = cat ? cat.name : '';
        descEl.value = cat ? (cat.description || '') : '';
        setImage(cat ? (cat.imageUrl || '') : '');
        refreshParentOptions();
        parentEl.value = cat && cat.parentId != null ? String(cat.parentId) : '';
        sortEl.value = cat ? (cat.sortOrder != null ? cat.sortOrder : 0) : 0;
        activeEl.checked = cat ? !!cat.isActive : true;
        modal.show();
    }

    document.getElementById('btnAddCategory').addEventListener('click', function () { openModal('add', null); });

    document.querySelectorAll('[data-edit-category]').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal('edit', JSON.parse(btn.dataset.editCategory)); });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (submitting) return;
        var name = nameEl.value;
        if (!name) {
            toast.warning('Tên danh mục là bắt buộc!');
            return;
        }

        var payload = {
            name: name,
            description: descEl.value,
            imageUrl: typeof imageEl.getValue === 'function' ? imageEl.getValue() : '',
            parentId: parentEl.value === '' ? null : parseInt(parentEl.value, 10),
            sortOrder: parseInt(sortEl.value, 10) || 0,
            isActive: activeEl.checked
        };

        submitting = true;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang lưu...';

        var req = modalType === 'add'
            ? api(BASE_URL, { method: 'POST', data: payload })
            : api(BASE_URL + '/' + current.id, { method: 'PUT', data: payload });

        req.then(function () {
            reloadWithToast(modalType === 'add' ? 'Thêm danh mục hải sản thành công!' : 'Cập nhật danh mục thành công!');
        }).catch(function (err) {
            toast.error(err.message || 'Lỗi khi lưu thông tin danh mục!');
            submitting = false;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Lưu danh mục';
        });
    });

    document.querySelectorAll('[data-delete-category]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.dataset.deleteCategory;
            confirmDialog(
                'Bạn có chắc chắn muốn ngưng hoạt động (xóa) danh mục này không? Các sản phẩm thuộc danh mục sẽ bị ảnh hưởng!',
                'Xóa danh mục hải sản',
                'danger'
            ).then(function (ok) {
                if (!ok) return;
                api(BASE_URL + '/' + id, { method: 'DELETE' })
                    .then(function () { reloadWithToast('Đã ngưng hoạt động danh mục thành công!'); })
                    .catch(function (err) {
                        toast.error(err.message || 'Không thể ngưng hoạt động danh mục này (có thể do có ràng buộc khóa ngoại)!');
                    });
            });
        });
    });
})();
</script>
@endpush
