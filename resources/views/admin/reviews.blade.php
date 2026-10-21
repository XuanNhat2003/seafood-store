@extends('layouts.admin')

@section('title', 'Đánh giá - Seafood Admin')

@section('content')
@php
    $page = $reviews->currentPage();
    $totalCount = $reviews->total();
    $totalPages = $reviews->lastPage();
@endphp
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Quản lý đánh giá</h4>
    </div>

    {{-- Filter and Search Bar --}}
    <div class="custom-table-container py-3 mb-4">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">{{ icon('search') }}</span>
                    <input type="text" name="search" class="form-control bg-light border-0"
                           placeholder="Tìm theo mã SP, tên sản phẩm, khách hàng..."
                           value="{{ $search }}" style="border-radius:0 8px 8px 0">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="rating" class="form-select bg-light border-0" style="border-radius:8px" title="Số sao" onchange="this.form.submit()">
                    <option value="" {{ $rating === '' ? 'selected' : '' }}>Tất cả số sao</option>
                    @foreach ([5, 4, 3, 2, 1] as $n)
                        <option value="{{ $n }}" {{ $rating === (string) $n ? 'selected' : '' }}>{{ $n }} sao</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="approved" class="form-select bg-light border-0" style="border-radius:8px" title="Trạng thái" onchange="this.form.submit()">
                    <option value="" {{ $approved === '' ? 'selected' : '' }}>Tất cả trạng thái</option>
                    <option value="true" {{ $approved === 'true' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="false" {{ $approved === 'false' ? 'selected' : '' }}>Chưa duyệt</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-secondary w-100 py-2" style="border-radius:8px">Lọc &amp; Tìm kiếm</button>
            </div>
        </form>
    </div>

    {{-- Reviews Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Đánh giá</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $index => $r)
                        <tr data-review-row="{{ $r->id }}">
                            <td class="fw-bold text-muted">{{ ($page - 1) * $pageSize + $index + 1 }}</td>
                            <td class="fw-semibold">{{ optional($r->product)->name }}</td>
                            <td>{{ $r->user_name }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $r->rating)
                                                {{ icon('star', 16, '', 'currentColor') }}
                                            @else
                                                {{ icon('star', 16, 'star-empty') }}
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="rating-pill">{{ $r->rating }}/5</span>
                                </div>
                            </td>
                            <td class="text-muted small" style="max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $r->comment }}">
                                {{ $r->comment ?: '-' }}
                            </td>
                            <td>
                                <span class="badge-status {{ $r->is_approved ? 'badge-status-paid' : 'badge-status-unpaid' }}">
                                    {{ $r->is_approved ? 'Đã duyệt' : 'Chưa duyệt' }}
                                </span>
                            </td>
                            <td>{{ format_date($r->created_at) }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if ($r->is_approved)
                                        <button type="button" class="btn btn-sm btn-outline-secondary border-0 d-flex align-items-center gap-1" style="border-radius:8px"
                                                title="Ẩn đánh giá" data-toggle-approval="{{ $r->id }}" data-next="false">
                                            {{ icon('eye-off', 16) }}
                                            <span>Ẩn</span>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-success border-0 d-flex align-items-center gap-1" style="border-radius:8px"
                                                title="Duyệt đánh giá" data-toggle-approval="{{ $r->id }}" data-next="true">
                                            {{ icon('check', 16) }}
                                            <span>Duyệt</span>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" style="border-radius:8px"
                                            title="Xóa đánh giá" data-delete-review="{{ $r->id }}">
                                        {{ icon('trash-2', 16) }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có đánh giá nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($totalPages > 1)
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="text-muted small">
                    Hiển thị {{ ($page - 1) * $pageSize + 1 }} - {{ min($page * $pageSize, $totalCount) }} trên {{ $totalCount }} đánh giá
                </span>
                <nav>
                    <ul class="pagination mb-0 gap-1">
                        <li class="page-item {{ $page === 1 ? 'disabled' : '' }}">
                            <a class="page-link border-0" href="{{ $reviews->url(max(1, $page - 1)) }}" style="border-radius:8px">Trước</a>
                        </li>
                        @for ($n = 1; $n <= $totalPages; $n++)
                            <li class="page-item {{ $page === $n ? 'active' : '' }}">
                                <a class="page-link border-0" href="{{ $reviews->url($n) }}" style="border-radius:8px">{{ $n }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $page === $totalPages ? 'disabled' : '' }}">
                            <a class="page-link border-0" href="{{ $reviews->url(min($totalPages, $page + 1)) }}" style="border-radius:8px">Sau</a>
                        </li>
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var BASE_URL = @json(url('/admin/reviews')) + '/';
    var updatingId = null;

    function setRowBusy(id, busy, spinnerBtn) {
        var row = document.querySelector('[data-review-row="' + id + '"]');
        if (!row) return;
        row.querySelectorAll('button').forEach(function (b) { b.disabled = busy; });
        if (busy && spinnerBtn) {
            spinnerBtn.dataset.html = spinnerBtn.innerHTML;
            spinnerBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
        } else if (!busy && spinnerBtn && spinnerBtn.dataset.html) {
            spinnerBtn.innerHTML = spinnerBtn.dataset.html;
        }
    }

    document.querySelectorAll('[data-toggle-approval]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (updatingId) return;
            var id = btn.dataset.toggleApproval;
            var next = btn.dataset.next === 'true';
            updatingId = id;
            setRowBusy(id, true, btn);
            api(BASE_URL + id + '/approval', { method: 'PUT', data: { isApproved: next } })
                .then(function () { reloadWithToast(next ? 'Đã duyệt đánh giá!' : 'Đã ẩn đánh giá!'); })
                .catch(function () {
                    toast.error('Không thể cập nhật trạng thái đánh giá!');
                    setRowBusy(id, false, btn);
                    updatingId = null;
                });
        });
    });

    document.querySelectorAll('[data-delete-review]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (updatingId) return;
            var id = btn.dataset.deleteReview;
            confirmDialog('Xóa đánh giá này?', 'Xóa đánh giá', 'danger').then(function (ok) {
                if (!ok) return;
                updatingId = id;
                setRowBusy(id, true, null);
                api(BASE_URL + id, { method: 'DELETE' })
                    .then(function () { reloadWithToast('Đã xóa đánh giá thành công!'); })
                    .catch(function () {
                        toast.error('Không thể xóa đánh giá này!');
                        setRowBusy(id, false, null);
                        updatingId = null;
                    });
            });
        });
    });
})();
</script>
@endpush
