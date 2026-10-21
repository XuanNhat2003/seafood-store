@extends('layouts.admin')

@section('title', 'Banner - Seafood Admin')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Quản lý banner quảng cáo</h4>
        <button type="button" id="btnAddBanner"
                class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2"
                style="border-radius:12px;background:var(--accent-gradient);border:none">
            {{ icon('plus') }}
            <span>Thêm banner</span>
        </button>
    </div>

    {{-- Banners Grid --}}
    <div class="row g-4">
        @forelse ($banners as $b)
            @php
                $json = ['id' => $b->id, 'title' => $b->title, 'imageUrl' => $b->image_url, 'link' => $b->link,
                         'description' => $b->description, 'sortOrder' => $b->sort_order, 'isActive' => $b->is_active];
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0" style="border-radius:16px;overflow:hidden">
                    <img src="{{ img_url($b->image_url) }}" class="card-img-top" alt="{{ $b->title }}" style="height:180px;object-fit:cover">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-secondary">Thứ tự: {{ $b->sort_order }}</span>
                                <span class="badge-status {{ $b->is_active ? 'badge-status-paid' : 'badge-status-cancelled' }}">
                                    {{ $b->is_active ? 'Hoạt động' : 'Ẩn' }}
                                </span>
                            </div>
                            <h5 class="card-title fw-bold">{{ $b->title }}</h5>
                            <p class="card-text text-muted small">{{ $b->description ?: 'Không có mô tả' }}</p>
                            @if ($b->link)
                                <p class="small mb-3">
                                    <strong>Liên kết:</strong> <span class="text-primary">{{ $b->link }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="d-flex gap-2 pt-3 border-top mt-3">
                            <button type="button" class="btn btn-outline-primary w-50" style="border-radius:10px" data-edit-banner='@json($json)'>
                                {{ icon('edit-2', null, 'me-1') }} Sửa
                            </button>
                            <button type="button" class="btn btn-outline-danger w-50" style="border-radius:10px" data-delete-banner="{{ $b->id }}">
                                {{ icon('trash-2', null, 'me-1') }} Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                Chưa có banner quảng cáo nào được cấu hình
            </div>
        @endforelse
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold" id="bannerModalTitle">Thêm banner mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bannerForm" novalidate>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="banner-title">Tiêu đề banner *</label>
                        <input type="text" id="banner-title" maxlength="191" class="form-control" placeholder="Ví dụ: Đại Tiệc Cua Hoàng Đế" required style="border-radius:10px">
                    </div>

                    <div class="mb-3">
                        <div id="banner-image" data-image-upload data-name="image_url" data-value="" data-label="Ảnh banner" data-height="180"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="banner-link">Đường dẫn liên kết (Link)</label>
                        <input type="text" id="banner-link" maxlength="1000" class="form-control" placeholder="Ví dụ: /products/cua-hoang-de-alaska" style="border-radius:10px">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="banner-desc">Mô tả chi tiết</label>
                        <textarea id="banner-desc" class="form-control" rows="3" placeholder="Mô tả chương trình khuyến mãi hoặc banner..." style="border-radius:10px"></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted" for="banner-sort">Thứ tự hiển thị</label>
                            <input type="number" id="banner-sort" class="form-control" min="0" value="0" style="border-radius:10px">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="mb-2">
                                <div class="form-check form-switch fw-semibold text-muted small">
                                    <input class="form-check-input" type="checkbox" id="banner-active-switch" checked>
                                    <label class="form-check-label" for="banner-active-switch">Kích hoạt hiển thị</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="bannerSubmit"
                            style="border-radius:10px;background:var(--accent-gradient);border:none">Lưu banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var BASE_URL = @json(url('/admin/banners'));
    var modalEl = document.getElementById('bannerModal');
    var modal = new bootstrap.Modal(modalEl);
    var form = document.getElementById('bannerForm');
    var titleHeading = document.getElementById('bannerModalTitle');
    var titleEl = document.getElementById('banner-title');
    var imageEl = document.getElementById('banner-image');
    var linkEl = document.getElementById('banner-link');
    var descEl = document.getElementById('banner-desc');
    var sortEl = document.getElementById('banner-sort');
    var activeEl = document.getElementById('banner-active-switch');
    var submitBtn = document.getElementById('bannerSubmit');

    var modalType = 'add';
    var current = null;
    var submitting = false;

    function setImage(v) {
        if (typeof imageEl.setValue === 'function') imageEl.setValue(v || '');
        else imageEl.dataset.value = v || '';
    }
    function getImage() {
        return typeof imageEl.getValue === 'function' ? imageEl.getValue() : '';
    }

    function openModal(type, banner) {
        modalType = type;
        current = banner;
        titleHeading.textContent = type === 'add' ? 'Thêm banner mới' : 'Chỉnh sửa banner';
        titleEl.value = banner ? banner.title : '';
        setImage(banner ? banner.imageUrl : '');
        linkEl.value = banner ? (banner.link || '') : '';
        descEl.value = banner ? (banner.description || '') : '';
        sortEl.value = banner ? String(banner.sortOrder) : '0';
        activeEl.checked = banner ? !!banner.isActive : true;
        modal.show();
    }

    document.getElementById('btnAddBanner').addEventListener('click', function () { openModal('add', null); });

    document.querySelectorAll('[data-edit-banner]').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal('edit', JSON.parse(btn.dataset.editBanner)); });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (submitting) return;
        var title = titleEl.value;
        var imageUrl = getImage();
        if (!title || !imageUrl) {
            toast.warning('Tiêu đề và đường dẫn ảnh là bắt buộc!');
            return;
        }

        var payload = {
            title: title,
            imageUrl: imageUrl,
            link: linkEl.value,
            description: descEl.value,
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
            reloadWithToast(modalType === 'add' ? 'Thêm banner quảng cáo thành công!' : 'Cập nhật thông tin banner thành công!');
        }).catch(function (err) {
            toast.error(err.message || 'Lỗi khi lưu thông tin banner!');
            submitting = false;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Lưu banner';
        });
    });

    document.querySelectorAll('[data-delete-banner]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.dataset.deleteBanner;
            confirmDialog('Bạn có chắc chắn muốn xóa banner này không?', 'Xóa banner quảng cáo', 'danger').then(function (ok) {
                if (!ok) return;
                api(BASE_URL + '/' + id, { method: 'DELETE' })
                    .then(function () { reloadWithToast('Đã xóa banner thành công!'); })
                    .catch(function (err) { toast.error(err.message || 'Không thể xóa banner!'); });
            });
        });
    });
})();
</script>
@endpush
