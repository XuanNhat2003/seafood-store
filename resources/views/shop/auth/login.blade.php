@extends('layouts.shop')

@section('title', 'Đăng nhập - Hải Sản Tươi Ngon')

@section('content')
<div class="shop-auth-wrap">
    <div class="shop-auth-card">
        <h2 class="text-center mb-1">Đăng nhập</h2>
        <p class="text-center text-muted mb-4">Chào mừng bạn quay lại!</p>

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="shop-form-label" for="login-email">{{ icon('mail', null, 'me-1') }} Email</label>
                <input id="login-email" name="email" type="email" class="shop-input" placeholder="Nhập email của bạn" value="{{ old('email') }}" required>
            </div>

            <div class="mb-4">
                <label class="shop-form-label" for="login-password">{{ icon('lock', null, 'me-1') }} Mật khẩu</label>
                <input id="login-password" name="password" type="password" class="shop-input" placeholder="Nhập mật khẩu" required>
            </div>

            <button type="submit" class="btn-shop-orange w-100" data-loading-text="Đang đăng nhập...">Đăng nhập</button>
        </form>

        <p class="text-center mt-4 mb-0">
            Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
        </p>
    </div>
</div>
@endsection
