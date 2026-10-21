@extends('layouts.shop')

@section('title', 'Đăng ký tài khoản - Hải Sản Tươi Ngon')

@section('content')
<div class="shop-auth-wrap">
    <div class="shop-auth-card">
        <h2 class="text-center mb-4">Đăng ký tài khoản</h2>

        <form method="POST" action="{{ route('register.submit') }}" id="registerForm">
            @csrf
            <div class="mb-3">
                <label class="shop-form-label" for="reg-fullname">{{ icon('user', null, 'me-1') }} Họ tên *</label>
                <input id="reg-fullname" name="full_name" type="text" class="shop-input" placeholder="Nhập họ và tên" value="{{ old('full_name') }}" required>
            </div>

            <div class="mb-3">
                <label class="shop-form-label" for="reg-email">{{ icon('mail', null, 'me-1') }} Email *</label>
                <input id="reg-email" name="email" type="email" class="shop-input" placeholder="Nhập email" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="shop-form-label" for="reg-phone">{{ icon('phone', null, 'me-1') }} Số điện thoại</label>
                <input id="reg-phone" name="phone" type="tel" class="shop-input" placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
            </div>

            <div class="mb-3">
                <label class="shop-form-label" for="reg-password">{{ icon('lock', null, 'me-1') }} Mật khẩu *</label>
                <input id="reg-password" name="password" type="password" class="shop-input" placeholder="Tối thiểu 6 ký tự" required>
            </div>

            <div class="mb-4">
                <label class="shop-form-label" for="reg-confirm">{{ icon('lock', null, 'me-1') }} Xác nhận mật khẩu *</label>
                <input id="reg-confirm" name="password_confirmation" type="password" class="shop-input" placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit" class="btn-shop-orange w-100" data-loading-text="Đang xử lý...">Đăng ký</button>
        </form>

        <p class="text-center mt-4 mb-0">
            Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('registerForm').addEventListener('submit', function (e) {
    var pw = document.getElementById('reg-password').value;
    var cf = document.getElementById('reg-confirm').value;
    if (pw.length < 6) { e.preventDefault(); toast.warning('Mật khẩu phải có ít nhất 6 ký tự'); return; }
    if (pw !== cf) { e.preventDefault(); toast.warning('Mật khẩu xác nhận không khớp'); }
});
</script>
@endpush
