@extends('layouts.admin')

@section('title', 'Sản phẩm - Seafood Admin')

@php
    $fallbackImg = 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=500';
    $items = $products->getCollection();
    $totalCount = $products->total();
    $page = $products->currentPage();
    $totalPages = $products->lastPage();
    $activeCount = $items->where('is_active', true)->count();
    $lowStockCount = $items->filter(function ($p) { return $p->stock_quantity <= 5; })->count();
    $featuredCount = $items->where('is_featured', true)->count();
    $categoryOptions = $categories->map(function ($c) {
        return ['id' => $c->id, 'label' => $c->parent_id !== null ? '— ' . $c->name : $c->name];
    });
@endphp

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Quản lý sản phẩm hải sản</h4>
        <button type="button" id="btnAddProduct" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2"
                style="border-radius:12px;background:var(--accent-gradient);border:none">
            {{ icon('plus') }}
            <span>Thêm sản phẩm</span>
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="admin-mini-stat">
                <span class="mini-stat-icon text-primary">{{ icon('package') }}</span>
                <div>
                    <p>Tổng sản phẩm</p>
                    <strong>{{ $totalCount }}</strong>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="admin-mini-stat">
                <span class="mini-stat-icon text-success">{{ icon('check') }}</span>
                <div>
                    <p>Đang bán</p>
                    <strong>{{ $activeCount }}</strong>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="admin-mini-stat">
                <span class="mini-stat-icon text-warning">{{ icon('tag') }}</span>
                <div>
                    <p>Sản phẩm nổi bật</p>
                    <strong>{{ $featuredCount }}</strong>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="admin-mini-stat">
                <span class="mini-stat-icon text-danger">{{ icon('package') }}</span>
                <div>
                    <p>Sắp hết hàng</p>
                    <strong>{{ $lowStockCount }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter and Search Bar --}}
    <div class="custom-table-container py-3 mb-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">{{ icon('search') }}</span>
                    <input type="text" name="search" class="form-control bg-light border-0"
                           placeholder="Tìm theo mã SP hoặc tên sản phẩm..." value="{{ $search }}"
                           style="border-radius:0 8px 8px 0">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">{{ icon('filter') }}</span>
                    <select name="categoryId" class="form-select bg-light border-0" style="border-radius:0 8px 8px 0"
                            onchange="this.form.submit()">
                        <option value="">Tất cả danh mục</option>
                        @foreach ($categoryOptions as $c)
                            <option value="{{ $c['id'] }}" {{ $categoryId !== '' && (int) $categoryId === $c['id'] ? 'selected' : '' }}>{{ $c['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-secondary w-100 py-2" style="border-radius:8px">Tìm kiếm</button>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px">STT</th>
                        <th class="text-center" style="width:100px">Mã SP</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá bán</th>
                        <th>Giá KM</th>
                        <th class="text-center">Tồn kho</th>
                        <th class="text-center">Đơn vị</th>
                        <th class="text-center">Nổi bật</th>
                        <th class="text-center">Kinh doanh</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $index => $p)
                        @php
                            $json = [
                                'id' => $p->id, 'categoryId' => $p->category_id, 'categoryName' => $p->category ? $p->category->name : '',
                                'productCode' => $p->product_code, 'name' => $p->name, 'slug' => $p->slug, 'description' => $p->description,
                                'price' => $p->price, 'salePrice' => $p->sale_price, 'stockQuantity' => $p->stock_quantity, 'unit' => $p->unit,
                                'imageUrl' => $p->image_url, 'displayImageUrl' => img_url($p->image_url),
                                'isActive' => (bool) $p->is_active, 'isFeatured' => (bool) $p->is_featured,
                                'createdAt' => format_date($p->created_at),
                            ];
                        @endphp
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ ($page - 1) * $pageSize + $index + 1 }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $p->product_code }}</span>
                            </td>
                            <td>
                                <img src="{{ img_url($p->image_url, $fallbackImg) }}" alt="{{ $p->name }}" class="product-img-thumb">
                            </td>
                            <td>
                                <div class="fw-bold">{{ $p->name }}</div>
                                <span class="text-muted small">{{ $p->slug }}</span>
                            </td>
                            <td>{{ $p->category ? $p->category->name : '' }}</td>
                            <td class="fw-bold">{{ format_price($p->price) }}</td>
                            <td class="text-danger fw-bold">{{ $p->sale_price ? format_price($p->sale_price) : '-' }}</td>
                            <td class="text-center">
                                <span class="fw-bold {{ $p->stock_quantity <= 5 ? 'text-danger' : '' }}">{{ $p->stock_quantity }}</span>
                            </td>
                            <td class="text-center">{{ $p->unit }}</td>
                            <td class="text-center">
                                @if ($p->is_featured)
                                    <span class="text-warning">{{ icon('check', 18) }}</span>
                                @else
                                    <span class="text-muted">{{ icon('x', 18) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge-status {{ $p->is_active ? 'badge-status-paid' : 'badge-status-cancelled' }}">
                                    {{ $p->is_active ? 'Đang bán' : 'Ngưng bán' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary border-0" style="border-radius:8px"
                                            title="Xem chi tiết sản phẩm" data-detail-product='@json($json)'>
                                        {{ icon('eye', 16) }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary border-0" style="border-radius:8px"
                                            title="Chỉnh sửa" data-edit-product='@json($json)'>
                                        {{ icon('edit-2', 16) }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" style="border-radius:8px"
                                            {{ $p->is_active ? '' : 'disabled' }}
                                            title="{{ $p->is_active ? 'Ngừng kinh doanh sản phẩm' : 'Sản phẩm đã ngừng kinh doanh' }}"
                                            data-deactivate-product="{{ $p->id }}">
                                        {{ icon('power', 16) }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" style="border-radius:8px"
                                            title="Xóa vĩnh viễn sản phẩm" data-delete-product="{{ $p->id }}" data-name="{{ $p->name }}">
                                        {{ icon('trash-2', 16) }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="text-center text-muted py-4">Không tìm thấy sản phẩm nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($totalPages > 1)
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="text-muted small">
                    Hiển thị {{ ($page - 1) * $pageSize + 1 }} - {{ min($page * $pageSize, $totalCount) }} trên {{ $totalCount }} sản phẩm
                </span>
                <nav>
                    <ul class="pagination mb-0 gap-1">
                        <li class="page-item {{ $page === 1 ? 'disabled' : '' }}">
                            <a class="page-link border-0" href="{{ $page > 1 ? $products->url($page - 1) : '#' }}" style="border-radius:8px">Trước</a>
                        </li>
                        @for ($i = 1; $i <= $totalPages; $i++)
                            <li class="page-item {{ $page === $i ? 'active' : '' }}">
                                <a class="page-link border-0" href="{{ $products->url($i) }}" style="border-radius:8px">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $page === $totalPages ? 'disabled' : '' }}">
                            <a class="page-link border-0" href="{{ $page < $totalPages ? $products->url($page + 1) : '#' }}" style="border-radius:8px">Sau</a>
                        </li>
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold" id="productModalTitle">Thêm sản phẩm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="pfName">Tên sản phẩm *</label>
                                <input type="text" id="pfName" class="form-control" maxlength="180" placeholder="Ví dụ: Tôm Hùm Alaska sống" required style="border-radius:10px">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="pfCategory">Danh mục *</label>
                                <select id="pfCategory" class="form-select" required style="border-radius:10px">
                                    @foreach ($categoryOptions as $c)
                                        <option value="{{ $c['id'] }}">{{ $c['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="pfPrice">Giá bán (VNĐ) *</label>
                                <input type="number" id="pfPrice" class="form-control" placeholder="Ví dụ: 1200000" required min="0" style="border-radius:10px">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="pfSalePrice">Giá khuyến mãi (VNĐ)</label>
                                <input type="number" id="pfSalePrice" class="form-control" placeholder="Bỏ trống nếu không giảm giá" min="0" style="border-radius:10px">
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="pfStock">Tồn kho *</label>
                                <input type="number" id="pfStock" class="form-control" required min="0" style="border-radius:10px">
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="pfUnit">Đơn vị *</label>
                                <input type="text" id="pfUnit" class="form-control" maxlength="50" required placeholder="kg, con, thùng" style="border-radius:10px">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted">Ảnh đại diện *</label>
                                <div id="pfImage" data-image-upload data-name="image_url" data-value="" data-label="Ảnh đại diện"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted" for="pfDescription">Mô tả sản phẩm</label>
                                <textarea id="pfDescription" class="form-control" rows="4" placeholder="Nhập thông tin mô tả chi tiết sản phẩm hải sản..." style="border-radius:10px"></textarea>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 d-flex align-items-center gap-3">
                            <div class="form-check form-switch fw-semibold text-muted small">
                                <input type="checkbox" class="form-check-input" id="featured-switch">
                                <label class="form-check-label" for="featured-switch">Sản phẩm Nổi bật (Featured)</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 d-flex align-items-center gap-3">
                            <div class="form-check form-switch fw-semibold text-muted small">
                                <input type="checkbox" class="form-check-input" id="active-switch">
                                <label class="form-check-label" for="active-switch">Đang Kinh doanh (Active)</label>
                            </div>
                        </div>

                        {{-- Multi-image gallery --}}
                        <div class="col-12">
                            <hr class="my-2">
                            <label class="form-label fw-semibold small text-muted">Thư viện ảnh sản phẩm</label>
                            <div id="galleryEditSection" class="d-none">
                                <div id="galleryGridWrap"></div>
                                <div class="mt-3">
                                    <div id="pfGalleryNew" data-image-upload data-name="gallery_image_url" data-value="" data-label="Thêm ảnh vào thư viện"></div>
                                    <button type="button" id="btnAddGallery" class="btn btn-primary mt-2" disabled
                                            style="border-radius:10px;background:var(--accent-gradient);border:none">Thêm vào thư viện</button>
                                </div>
                            </div>
                            <p id="galleryAddHint" class="text-muted small mb-0">Thư viện ảnh sẽ khả dụng sau khi bạn lưu sản phẩm.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px">Hủy</button>
                    <button type="submit" id="btnSaveProduct" class="btn btn-primary"
                            style="border-radius:10px;background:var(--accent-gradient);border:none">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="productDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-custom product-detail-modal">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold">Chi tiết sản phẩm hải sản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="productDetailBody"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var BASE_URL = @json(url('/admin/products'));
    var STORE_URL = @json(route('admin.products.store'));
    var ASSET_BASE = @json(url('/'));
    var FALLBACK_IMG = 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=500';
    var DETAIL_FALLBACK_IMG = 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=900';

    function displayUrl(v) {
        if (!v) return '';
        if (/^(https?:)?\/\//i.test(v) || v.indexOf('data:') === 0) return v;
        return ASSET_BASE + '/' + String(v).replace(/^\/+/, '');
    }
    function svg(name, size) {
        return (window.feather && feather.icons[name]) ? feather.icons[name].toSvg({ width: size || 16, height: size || 16 }) : '';
    }

    /* ---------------- Add / Edit modal ---------------- */
    var modalEl = document.getElementById('productModal');
    var modal = new bootstrap.Modal(modalEl);
    var form = document.getElementById('productForm');
    var f = {
        title: document.getElementById('productModalTitle'),
        name: document.getElementById('pfName'),
        category: document.getElementById('pfCategory'),
        price: document.getElementById('pfPrice'),
        salePrice: document.getElementById('pfSalePrice'),
        stock: document.getElementById('pfStock'),
        unit: document.getElementById('pfUnit'),
        image: document.getElementById('pfImage'),
        description: document.getElementById('pfDescription'),
        featured: document.getElementById('featured-switch'),
        active: document.getElementById('active-switch'),
        save: document.getElementById('btnSaveProduct')
    };
    var galleryNew = document.getElementById('pfGalleryNew');
    var btnAddGallery = document.getElementById('btnAddGallery');
    var gridWrap = document.getElementById('galleryGridWrap');
    var modalType = 'add';
    var editingId = null;
    var galleryImages = [];
    var submitting = false;
    var addingGallery = false;

    function setImageField(el, v) {
        if (el.setValue) el.setValue(v || ''); else el.dataset.value = v || '';
    }
    function getImageField(el) {
        return el.getValue ? el.getValue() : (el.dataset.value || '');
    }

    function renderGallery() {
        if (!galleryImages.length) {
            gridWrap.innerHTML = '<p class="text-muted small mb-3">Chưa có ảnh nào trong thư viện.</p>';
            return;
        }
        gridWrap.innerHTML = '<div class="gallery-grid">' + galleryImages.map(function (img) {
            return '<div class="gallery-item ' + (img.isPrimary ? 'is-primary' : '') + '">' +
                '<img src="' + esc(img.displayUrl || displayUrl(img.imageUrl)) + '" alt="Ảnh sản phẩm">' +
                (img.isPrimary ? '<span class="gallery-primary-badge">Ảnh chính</span>' : '') +
                '<div class="gallery-item-actions">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger border-0" style="border-radius:8px" title="Xóa ảnh" data-del-image="' + img.id + '">' + svg('trash-2', 16) + '</button>' +
                '</div></div>';
        }).join('') + '</div>';
    }

    function resetGalleryState() {
        galleryImages = [];
        addingGallery = false;
        gridWrap.innerHTML = '';
        setImageField(galleryNew, '');
        updateAddGalleryBtn();
    }

    function updateAddGalleryBtn() {
        btnAddGallery.disabled = addingGallery || !getImageField(galleryNew);
        btnAddGallery.textContent = addingGallery ? 'Đang thêm...' : 'Thêm vào thư viện';
    }

    function setGalleryMode(isEdit) {
        document.getElementById('galleryEditSection').classList.toggle('d-none', !isEdit);
        document.getElementById('galleryAddHint').classList.toggle('d-none', isEdit);
    }

    function fetchGallery(productId) {
        return api(BASE_URL + '/' + productId)
            .then(function (data) { galleryImages = data.images || []; renderGallery(); })
            .catch(function () { toast.error('Không thể tải thư viện ảnh!'); });
    }

    function openAddModal() {
        modalType = 'add';
        editingId = null;
        f.title.textContent = 'Thêm sản phẩm mới';
        f.name.value = '';
        f.category.selectedIndex = f.category.options.length ? 0 : -1;
        f.description.value = '';
        f.price.value = '';
        f.salePrice.value = '';
        f.stock.value = '';
        f.unit.value = 'kg';
        setImageField(f.image, '');
        f.active.checked = true;
        f.featured.checked = false;
        resetGalleryState();
        setGalleryMode(false);
        modal.show();
    }

    function openEditModal(product) {
        modalType = 'edit';
        editingId = product.id;
        f.title.textContent = 'Chỉnh sửa sản phẩm';
        f.name.value = product.name || '';
        f.category.value = product.categoryId;
        f.description.value = product.description || '';
        f.price.value = product.price;
        f.salePrice.value = product.salePrice || '';
        f.stock.value = product.stockQuantity;
        f.unit.value = product.unit || '';
        setImageField(f.image, product.imageUrl || '');
        f.active.checked = !!product.isActive;
        f.featured.checked = !!product.isFeatured;
        resetGalleryState();
        setGalleryMode(true);
        modal.show();
        fetchGallery(product.id);
    }

    document.getElementById('btnAddProduct').addEventListener('click', openAddModal);
    document.querySelectorAll('[data-edit-product]').forEach(function (btn) {
        btn.addEventListener('click', function () { openEditModal(JSON.parse(btn.dataset.editProduct)); });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (submitting) return;
        if (!f.name.value || !f.category.value || !f.price.value || f.stock.value === '') {
            toast.warning('Vui lòng nhập đầy đủ các trường bắt buộc!');
            return;
        }
        var payload = {
            categoryId: parseInt(f.category.value, 10),
            name: f.name.value,
            description: f.description.value,
            price: parseFloat(f.price.value),
            salePrice: f.salePrice.value ? parseFloat(f.salePrice.value) : null,
            stockQuantity: parseInt(f.stock.value, 10),
            unit: f.unit.value,
            imageUrl: getImageField(f.image) || FALLBACK_IMG,
            isActive: f.active.checked,
            isFeatured: f.featured.checked
        };
        submitting = true;
        f.save.disabled = true;
        f.save.textContent = 'Đang lưu...';
        var req = modalType === 'add'
            ? api(STORE_URL, { method: 'POST', data: payload })
            : api(BASE_URL + '/' + editingId, { method: 'PUT', data: payload });
        req.then(function () {
            reloadWithToast(modalType === 'add' ? 'Thêm sản phẩm thành công!' : 'Cập nhật sản phẩm thành công!');
        }).catch(function (err) {
            toast.error(err.message || 'Lỗi khi lưu thông tin sản phẩm!');
            submitting = false;
            f.save.disabled = false;
            f.save.textContent = 'Lưu sản phẩm';
        });
    });

    // Gallery: add / delete (stay in the modal, refresh gallery only)
    galleryNew.addEventListener('image-change', updateAddGalleryBtn);
    galleryNew.addEventListener('input', updateAddGalleryBtn);

    btnAddGallery.addEventListener('click', function () {
        var url = getImageField(galleryNew);
        if (!url) { toast.warning('Vui lòng chọn hoặc nhập đường dẫn ảnh!'); return; }
        if (!editingId) return;
        addingGallery = true;
        updateAddGalleryBtn();
        api(BASE_URL + '/' + editingId + '/images', {
            method: 'POST',
            data: { imageUrl: url, isPrimary: false, sortOrder: galleryImages.length + 1 }
        }).then(function () {
            return fetchGallery(editingId).then(function () {
                setImageField(galleryNew, '');
                toast.success('Đã thêm ảnh vào thư viện!');
            });
        }).catch(function (err) {
            toast.error(err.message || 'Không thể thêm ảnh vào thư viện!');
        }).then(function () {
            addingGallery = false;
            updateAddGalleryBtn();
        });
    });

    gridWrap.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-del-image]');
        if (!btn || !editingId) return;
        var imageId = btn.dataset.delImage;
        var productId = editingId;
        confirmDialog('Bạn có chắc chắn muốn xóa ảnh này khỏi thư viện không?', 'Xóa ảnh', 'danger').then(function (ok) {
            if (!ok) return;
            api(BASE_URL + '/' + productId + '/images/' + imageId, { method: 'DELETE' })
                .then(function () {
                    return fetchGallery(productId).then(function () { toast.success('Đã xóa ảnh khỏi thư viện!'); });
                })
                .catch(function (err) { toast.error(err.message || 'Không thể xóa ảnh!'); });
        });
    });

    /* ---------------- Deactivate / permanent delete ---------------- */
    document.querySelectorAll('[data-deactivate-product]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            confirmDialog(
                'Bạn có chắc chắn muốn ngừng kinh doanh sản phẩm này không? Sản phẩm sẽ không còn hiển thị để khách mua, nhưng dữ liệu vẫn được giữ lại.',
                'Ngừng kinh doanh sản phẩm',
                'deactivate'
            ).then(function (ok) {
                if (!ok) return;
                api(BASE_URL + '/' + btn.dataset.deactivateProduct, { method: 'DELETE' })
                    .then(function () { reloadWithToast('Đã chuyển sản phẩm sang trạng thái ngừng kinh doanh!'); })
                    .catch(function (err) { toast.error(err.message || 'Không thể ngừng kinh doanh sản phẩm!'); });
            });
        });
    });

    document.querySelectorAll('[data-delete-product]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            confirmDialog(
                'Bạn có chắc chắn muốn xóa vĩnh viễn sản phẩm "' + btn.dataset.name + '" không? Hành động này chỉ thực hiện được nếu sản phẩm chưa từng phát sinh đơn hàng.',
                'Xóa vĩnh viễn sản phẩm',
                'danger'
            ).then(function (ok) {
                if (!ok) return;
                api(BASE_URL + '/' + btn.dataset.deleteProduct + '/permanent', { method: 'DELETE' })
                    .then(function () { reloadWithToast('Đã xóa vĩnh viễn sản phẩm!'); })
                    .catch(function (err) { toast.error(err.message || 'Không thể xóa vĩnh viễn sản phẩm!'); });
            });
        });
    });

    /* ---------------- Detail modal ---------------- */
    var detailEl = document.getElementById('productDetailModal');
    var detailModal = new bootstrap.Modal(detailEl);
    var detailBody = document.getElementById('productDetailBody');
    var detailProduct = null;
    var detailLoading = false;
    var selectedDetailImage = '';

    function renderDetail() {
        var d = detailProduct;
        if (!d) { detailBody.innerHTML = ''; return; }
        var images = (d.images && d.images.length) ? d.images : [];
        var mainImage = selectedDetailImage || d.imageUrl || DETAIL_FALLBACK_IMG;
        var items = [];
        if (d.imageUrl) items.push({ id: 'main', imageUrl: d.imageUrl });
        images.forEach(function (img) { items.push(img); });
        items = items.filter(function (img, i) {
            return items.findIndex(function (x) { return x.imageUrl === img.imageUrl; }) === i;
        });
        var discount = (d.salePrice && d.price) ? Math.round(((d.price - d.salePrice) / d.price) * 100) : 0;

        var thumbs = items.length
            ? items.map(function (img, i) {
                return '<button type="button" class="detail-gallery-thumb ' + (mainImage === img.imageUrl ? 'is-active' : '') + '" data-thumb="' + esc(img.imageUrl) + '" title="Xem ảnh này">' +
                    '<img src="' + esc(displayUrl(img.imageUrl)) + '" alt="' + esc(d.name + ' ' + (i + 1)) + '"></button>';
            }).join('')
            : '<button type="button" class="detail-gallery-thumb is-active" data-thumb="' + esc(DETAIL_FALLBACK_IMG) + '" title="Xem ảnh này">' +
                '<img src="' + esc(DETAIL_FALLBACK_IMG) + '" alt="' + esc(d.name) + '"></button>';

        detailBody.innerHTML =
            '<div class="product-detail-shell">' +
                '<div class="product-detail-visual">' +
                    '<img src="' + esc(displayUrl(mainImage)) + '" alt="' + esc(d.name) + '">' +
                    '<div class="product-detail-badges">' +
                        '<span class="badge-status ' + (d.isActive ? 'badge-status-paid' : 'badge-status-cancelled') + '">' + (d.isActive ? 'Đang bán' : 'Ngưng bán') + '</span>' +
                        (d.isFeatured ? '<span class="detail-featured-badge">Nổi bật</span>' : '') +
                    '</div>' +
                '</div>' +
                '<div class="product-detail-content">' +
                    '<div class="product-detail-heading">' +
                        '<span class="detail-code">' + esc(d.productCode || ('#' + d.id)) + '</span>' +
                        '<h3>' + esc(d.name) + '</h3>' +
                        '<p>' + esc(d.description || 'Chưa có mô tả chi tiết cho sản phẩm này.') + '</p>' +
                    '</div>' +
                    '<div class="detail-metric-grid">' +
                        '<div class="detail-metric">' + svg('dollar-sign', '1em') + '<span>Giá bán</span><strong>' + esc(formatCurrency(d.price)) + '</strong></div>' +
                        '<div class="detail-metric">' + svg('tag', '1em') + '<span>Khuyến mãi</span><strong>' + esc(d.salePrice ? formatCurrency(d.salePrice) : 'Không áp dụng') + '</strong>' +
                            (discount > 0 ? '<small>Giảm ' + discount + '%</small>' : '') + '</div>' +
                        '<div class="detail-metric">' + svg('package', '1em') + '<span>Tồn kho</span><strong>' + esc(d.stockQuantity + ' ' + (d.unit || '')) + '</strong></div>' +
                        '<div class="detail-metric">' + svg('image', '1em') + '<span>Thư viện ảnh</span><strong>' + images.length + ' ảnh</strong></div>' +
                    '</div>' +
                    '<div class="detail-info-grid">' +
                        '<div><span>Danh mục</span><strong>' + esc(d.categoryName || '-') + '</strong></div>' +
                        '<div><span>Slug</span><strong>' + esc(d.slug || '-') + '</strong></div>' +
                        '<div><span>Ngày tạo</span><strong>' + esc(d.createdAt || '-') + '</strong></div>' +
                        '<div><span>Cập nhật</span><strong>' + esc(d.updatedAt || '-') + '</strong></div>' +
                    '</div>' +
                    '<div class="detail-gallery-section">' +
                        '<div class="d-flex align-items-center justify-content-between mb-2">' +
                            '<h6 class="mb-0">Ảnh sản phẩm</h6>' +
                            (detailLoading ? '<span class="text-muted small">Đang tải thêm...</span>' : '') +
                        '</div>' +
                        '<div class="detail-gallery-strip">' + thumbs + '</div>' +
                    '</div>' +
                    '<div class="d-flex justify-content-end gap-2 pt-3">' +
                        '<button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>' +
                        '<button type="button" class="btn btn-primary" data-detail-edit>' + svg('edit-2', '1em') + ' Chỉnh sửa sản phẩm</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    detailBody.addEventListener('click', function (e) {
        var thumb = e.target.closest('[data-thumb]');
        if (thumb) {
            selectedDetailImage = thumb.dataset.thumb;
            renderDetail();
            return;
        }
        if (e.target.closest('[data-detail-edit]')) {
            var product = detailProduct;
            detailEl.addEventListener('hidden.bs.modal', function onHidden() {
                detailEl.removeEventListener('hidden.bs.modal', onHidden);
                openEditModal(product);
            });
            detailModal.hide();
        }
    });

    document.querySelectorAll('[data-detail-product]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var product = JSON.parse(btn.dataset.detailProduct);
            detailProduct = product;
            selectedDetailImage = product.imageUrl || '';
            detailLoading = true;
            renderDetail();
            detailModal.show();
            api(BASE_URL + '/' + product.id)
                .then(function (data) {
                    if (!detailProduct || detailProduct.id !== product.id) return;
                    detailProduct = Object.assign({}, product, data);
                    selectedDetailImage = data.imageUrl || product.imageUrl || '';
                })
                .catch(function () { toast.error('Không thể tải chi tiết sản phẩm!'); })
                .then(function () {
                    if (!detailProduct || detailProduct.id !== product.id) return;
                    detailLoading = false;
                    renderDetail();
                });
        });
    });
})();
</script>
@endpush
