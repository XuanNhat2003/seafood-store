@extends('layouts.admin')

@section('title', 'Liên hệ - Seafood Admin')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Hộp thư liên hệ khách hàng</h4>
    </div>

    {{-- Filter bar --}}
    <div class="custom-table-container py-3 mb-4">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-12 col-md-4">
                <select name="status" class="form-select bg-light border-0" style="border-radius:8px" onchange="this.form.submit()">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>Tất cả liên hệ</option>
                    <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                    <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Đã đọc</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Contacts Table --}}
    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Tin nhắn ngắn</th>
                        <th>Thời gian gửi</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contacts as $c)
                        @php
                            $json = ['id' => $c->id, 'name' => $c->name, 'email' => $c->email, 'phone' => $c->phone,
                                     'message' => $c->message, 'isRead' => $c->is_read, 'createdAt' => format_date($c->created_at)];
                        @endphp
                        <tr class="{{ !$c->is_read ? 'fw-bold' : '' }}">
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->phone ?: '-' }}</td>
                            <td class="text-muted small" style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $c->message }}</td>
                            <td>{{ format_date($c->created_at) }}</td>
                            <td>
                                <span class="badge-status {{ $c->is_read ? 'badge-status-paid' : 'badge-status-unpaid' }}">
                                    {{ $c->is_read ? 'Đã xử lý (đọc)' : 'Chưa đọc' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary border-0" style="border-radius:8px"
                                            title="Xem nội dung liên hệ" data-show-contact='@json($json)'>
                                        {{ icon('eye', 18) }}
                                    </button>
                                    @if (!$c->is_read)
                                        <button type="button" class="btn btn-sm btn-outline-success border-0" style="border-radius:8px"
                                                title="Đánh dấu đã đọc" data-mark-read="{{ $c->id }}">
                                            {{ icon('check', 18) }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Hộp thư trống</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Details Modal --}}
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold">Chi tiết tin nhắn liên hệ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="color:var(--text-dark)">
                <div class="mb-3"><strong>Khách hàng gửi:</strong> <span data-f="name"></span></div>
                <div class="row mb-3">
                    <div class="col-6"><strong>Email:</strong> <span data-f="email"></span></div>
                    <div class="col-6"><strong>Điện thoại:</strong> <span data-f="phone"></span></div>
                </div>
                <div class="mb-3"><strong>Thời gian gửi:</strong> <span data-f="createdAt"></span></div>
                {{-- white-space:pre-wrap => không để khoảng trắng/xuống dòng của mã nguồn bên trong khối này --}}
                <div class="mb-4 p-3 bg-light rounded-3" style="white-space:pre-wrap"><strong>Nội dung tin nhắn:</strong><p class="mt-2 mb-0 text-secondary" data-f="message"></p></div>
                {{-- Bọc ngoài để ẩn/hiện bằng style.display (class d-flex có !important nên không ẩn được trực tiếp) --}}
                <div id="modalMarkReadWrap">
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <button type="button" class="btn btn-success d-flex align-items-center gap-1" style="border-radius:10px" id="modalMarkRead">
                            {{ icon('check') }} <span>Đánh dấu đã đọc</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-footer-custom">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var READ_URL = @json(url('/admin/contacts')) + '/';
    var modalEl = document.getElementById('contactModal');
    var modal = new bootstrap.Modal(modalEl);
    var current = null;
    var updating = false;

    function markRead(id) {
        if (updating) return;
        updating = true;
        api(READ_URL + id + '/read', { method: 'PUT' })
            .then(function () { reloadWithToast('Đã đánh dấu liên hệ đã đọc!'); })
            .catch(function () { toast.error('Không thể cập nhật trạng thái liên hệ!'); updating = false; });
    }

    document.querySelectorAll('[data-show-contact]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            current = JSON.parse(btn.dataset.showContact);
            modalEl.querySelectorAll('[data-f]').forEach(function (el) {
                var v = current[el.dataset.f];
                el.textContent = (v === null || v === '') ? '-' : v;
            });
            document.getElementById('modalMarkReadWrap').style.display = current.isRead ? 'none' : '';
            modal.show();
        });
    });

    document.querySelectorAll('[data-mark-read]').forEach(function (btn) {
        btn.addEventListener('click', function () { markRead(btn.dataset.markRead); });
    });
    document.getElementById('modalMarkRead').addEventListener('click', function () { if (current) markRead(current.id); });
})();
</script>
@endpush
