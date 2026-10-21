@extends('layouts.shop')

@section('title', 'Liên hệ - Hải Sản Tươi Ngon')

@section('content')
<div class="shop-section">
    <nav class="shop-breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a> / <span>Liên hệ</span>
    </nav>

    <h2 class="mb-4">Liên hệ với chúng tôi</h2>

    <div class="row g-4">
        {{-- LEFT: shop info --}}
        <div class="col-12 col-md-5">
            <h5 class="fw-bold mb-3">Thông tin cửa hàng</h5>

            <div class="d-flex align-items-start mb-3">
                {{ icon('map-pin', 20, 'text-shop-orange me-2 mt-1') }}
                <div>
                    <strong>Địa chỉ</strong>
                    <div class="text-muted">123 Đường Hải Sản, Q.1, TP.HCM</div>
                </div>
            </div>
            <div class="d-flex align-items-start mb-3">
                {{ icon('phone', 20, 'text-shop-orange me-2 mt-1') }}
                <div>
                    <strong>Hotline</strong>
                    <div class="text-muted">0987832744</div>
                </div>
            </div>
            <div class="d-flex align-items-start mb-3">
                {{ icon('mail', 20, 'text-shop-orange me-2 mt-1') }}
                <div>
                    <strong>Email</strong>
                    <div class="text-muted">phamxuannhat2706@gmail.com</div>
                </div>
            </div>
            <div class="d-flex align-items-start mb-3">
                {{ icon('clock', 20, 'text-shop-orange me-2 mt-1') }}
                <div>
                    <strong>Giờ mở cửa</strong>
                    <div class="text-muted">07:00 - 21:00 (Thứ 2 - Chủ nhật)</div>
                </div>
            </div>
        </div>

        {{-- RIGHT: contact form --}}
        <div class="col-12 col-md-7">
            <div class="shop-panel">
                <h5 class="fw-bold mb-3">Gửi liên hệ</h5>
                <form method="POST" action="{{ route('contact.submit') }}" id="contactForm">
                    @csrf
                    <div class="mb-3">
                        <label class="shop-form-label" for="contact-name">Họ tên *</label>
                        <input id="contact-name" name="name" type="text" class="shop-input" placeholder="Nhập họ và tên" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="shop-form-label" for="contact-email">Email *</label>
                        <input id="contact-email" name="email" type="email" class="shop-input" placeholder="Nhập email" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="shop-form-label" for="contact-phone">Số điện thoại</label>
                        <input id="contact-phone" name="phone" type="tel" class="shop-input" placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                    </div>
                    <div class="mb-4">
                        <label class="shop-form-label" for="contact-message">Nội dung *</label>
                        <textarea id="contact-message" name="message" class="shop-input" rows="4" placeholder="Nhập nội dung cần liên hệ" required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn-shop-orange" data-loading-text="Đang gửi...">Gửi liên hệ</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('contactForm').addEventListener('submit', function (e) {
    var f = e.target.elements;
    if (!f['name'].value.trim() || !f['email'].value.trim() || !f['message'].value.trim()) {
        e.preventDefault();
        toast.warning('Vui lòng điền đầy đủ họ tên, email và nội dung');
    }
});
</script>
@endpush
