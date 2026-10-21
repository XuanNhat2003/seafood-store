<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Hải Sản Tươi Ngon - Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/toastify/ReactToastify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
</head>
<body>
<div class="login-container">
    <div class="login-bg-circle" style="width:400px;height:400px;top:-10%;left:-10%"></div>
    <div class="login-bg-circle" style="width:500px;height:500px;bottom:-15%;right:-10%"></div>

    <div class="login-card">
        <div class="d-flex justify-content-center mb-3">
            <div class="brand-icon" style="width:70px;height:70px;border-radius:18px;padding:0;overflow:hidden;border:2px solid rgba(255,255,255,0.15)">
                <img src="{{ asset('images/logo.png') }}" alt="Seafood Store Logo" style="width:100%;height:100%;object-fit:cover">
            </div>
        </div>
        <h2 class="login-title">Seafood Store</h2>
        <p class="login-subtitle">Hệ thống quản trị cửa hàng hải sản</p>

        <form method="POST" action="{{ route('admin.login.submit') }}" id="adminLoginForm">
            @csrf
            <div class="mb-4">
                <label class="login-label">EMAIL ĐĂNG NHẬP</label>
                <div class="position-relative">
                    {{ icon('mail', null, 'position-absolute login-icon', 'none', 'left:15px;top:50%;transform:translateY(-50%);font-size:17px;z-index:10') }}
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-custom login-input-custom" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="login-label">MẬT KHẨU</label>
                <div class="position-relative">
                    {{ icon('lock', null, 'position-absolute login-icon', 'none', 'left:15px;top:50%;transform:translateY(-50%);font-size:17px;z-index:10') }}
                    <input type="password" name="password" class="form-control form-control-custom login-input-custom" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-custom w-100 mt-2 d-flex align-items-center justify-content-center gap-2"
                    data-loading-text='<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'>
                ĐĂNG NHẬP HỆ THỐNG
            </button>
        </form>
    </div>
</div>
<script>
    window.__FLASH = {!! json_encode([
        'success' => session('success'),
        'error' => session('error') ?: ($errors->any() ? $errors->first() : null),
        'warning' => session('warning'),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!};
</script>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/feather/feather.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
document.getElementById('adminLoginForm').addEventListener('submit', function (e) {
    if (!this.email.value || !this.password.value) { e.preventDefault(); toast.warning('Vui lòng điền đầy đủ thông tin!'); }
});
</script>
</body>
</html>
