@extends('layouts.admin')

@section('title', 'Người dùng - Seafood Admin')

@section('content')
@php
    $page = $users->currentPage();
    $totalPages = $users->lastPage();
    $totalCount = $users->total();
@endphp
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Quản lý khách hàng</h4>
    </div>

    {{-- Filter and Search Bar --}}
    <div class="custom-table-container py-3 mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-center">
            <div class="col-12 col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">{{ icon('search') }}</span>
                    <input type="text" name="search" class="form-control bg-light border-0"
                           placeholder="Tìm theo mã KH, họ tên, email, điện thoại..."
                           value="{{ $search }}" style="border-radius:0 8px 8px 0">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-secondary w-100 py-2" style="border-radius:8px">
                    Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Địa chỉ giao hàng</th>
                        <th>Ngày tham gia</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Khóa / Mở</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td class="fw-bold text-muted">#{{ $u->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $u->full_name }}</div>
                                <span class="badge bg-light text-secondary small">{{ $u->role }}</span>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->phone ?: '-' }}</td>
                            <td class="text-muted small" style="max-width:250px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $u->address ?: '-' }}
                            </td>
                            <td>{{ format_date($u->created_at) }}</td>
                            <td>
                                <span class="badge-status {{ $u->is_active ? 'badge-status-paid' : 'badge-status-cancelled' }}">
                                    {{ $u->is_active ? 'Hoạt động' : 'Đang khóa' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button"
                                        class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} border-0"
                                        style="border-radius:8px"
                                        title="{{ $u->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}"
                                        data-toggle-user="{{ $u->id }}" data-active="{{ $u->is_active ? '1' : '0' }}">
                                    {{ $u->is_active ? icon('lock', 16) : icon('unlock', 16) }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Không tìm thấy người dùng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($totalPages > 1)
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="text-muted small">
                    Hiển thị {{ ($page - 1) * $pageSize + 1 }} - {{ min($page * $pageSize, $totalCount) }} trên {{ $totalCount }} người dùng
                </span>
                <nav>
                    <ul class="pagination mb-0 gap-1">
                        <li class="page-item {{ $page === 1 ? 'disabled' : '' }}">
                            <a class="page-link border-0" href="{{ $users->url(max(1, $page - 1)) }}" style="border-radius:8px">Trước</a>
                        </li>
                        @for ($i = 1; $i <= $totalPages; $i++)
                            <li class="page-item {{ $page === $i ? 'active' : '' }}">
                                <a class="page-link border-0" href="{{ $users->url($i) }}" style="border-radius:8px">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $page === $totalPages ? 'disabled' : '' }}">
                            <a class="page-link border-0" href="{{ $users->url(min($totalPages, $page + 1)) }}" style="border-radius:8px">Sau</a>
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
    var USERS_URL = @json(url('/admin/users')) + '/';
    var updatingId = null;

    document.querySelectorAll('[data-toggle-user]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (updatingId !== null) return;
            var id = btn.dataset.toggleUser;
            var currentStatus = btn.dataset.active === '1';
            var actionText = currentStatus ? 'khóa' : 'mở khóa';

            confirmDialog(
                'Bạn có chắc chắn muốn ' + actionText + ' tài khoản người dùng này?',
                currentStatus ? 'Khóa tài khoản' : 'Mở khóa tài khoản',
                currentStatus ? 'danger' : 'info'
            ).then(function (ok) {
                if (!ok || updatingId !== null) return;
                updatingId = id;
                var original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

                api(USERS_URL + id + '/status', { method: 'PUT' })
                    .then(function () { reloadWithToast('Đã ' + actionText + ' tài khoản thành công!'); })
                    .catch(function (err) {
                        console.error(err);
                        toast.error('Không thể thực hiện hành động này!');
                        btn.disabled = false;
                        btn.innerHTML = original;
                        updatingId = null;
                    });
            });
        });
    });
})();
</script>
@endpush
