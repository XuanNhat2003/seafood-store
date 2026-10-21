@extends('shop.account.layout')

@section('title', 'Đổi mật khẩu - Hải Sản Tươi Ngon')

@section('account')
<div class="shop-panel">
    <h1 class="shop-panel-title h5 mb-4">Đổi mật khẩu</h1>

    <form method="POST" action="{{ route('account.password.update') }}" id="pwForm">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="shop-form-label" for="currentPassword">Mật khẩu hiện tại</label>
            <input id="currentPassword" name="current_password" type="password" class="shop-input" placeholder="Nhập mật khẩu hiện tại" autocomplete="current-password">
        </div>
        <div class="mb-3">
            <label class="shop-form-label" for="newPassword">Mật khẩu mới</label>
            <input id="newPassword" name="new_password" type="password" class="shop-input" placeholder="Tối thiểu 6 ký tự" autocomplete="new-password">
        </div>
        <div class="mb-4">
            <label class="shop-form-label" for="confirmPassword">Xác nhận mật khẩu mới</label>
            <input id="confirmPassword" name="confirm_password" type="password" class="shop-input" placeholder="Nhập lại mật khẩu mới" autocomplete="new-password">
        </div>
        <button type="submit" class="btn-shop-orange" data-loading-text="Đang xử lý...">{{ icon('lock') }} Đổi mật khẩu</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('pwForm').addEventListener('submit', function (e) {
    var np = document.getElementById('newPassword').value;
    var cp = document.getElementById('confirmPassword').value;
    if (np.length < 6) { e.preventDefault(); toast.warning('Mật khẩu mới phải có ít nhất 6 ký tự'); return; }
    if (np !== cp) { e.preventDefault(); toast.warning('Xác nhận mật khẩu không khớp'); }
});
</script>
@endpush
