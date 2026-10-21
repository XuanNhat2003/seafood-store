@extends('shop.account.layout')

@section('title', 'Thông tin cá nhân - Hải Sản Tươi Ngon')

@section('account')
<div class="shop-panel">
    <h1 class="shop-panel-title h5 mb-4">Thông tin cá nhân</h1>

    <form method="POST" action="{{ route('account.profile.update') }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="shop-form-label" for="fullName">Họ tên</label>
            <input id="fullName" name="full_name" type="text" class="shop-input" value="{{ old('full_name', $user->full_name) }}" placeholder="Nhập họ tên">
        </div>
        <div class="mb-3">
            <label class="shop-form-label" for="email">Email</label>
            <input id="email" type="email" class="shop-input" value="{{ $user->email }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label class="shop-form-label" for="phone">Số điện thoại</label>
            <input id="phone" name="phone" type="tel" class="shop-input" value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại">
        </div>
        <div class="mb-4">
            <label class="shop-form-label" for="address">Địa chỉ</label>
            <textarea id="address" name="address" class="shop-input" rows="3" placeholder="Nhập địa chỉ">{{ old('address', $user->address) }}</textarea>
        </div>
        <button type="submit" class="btn-shop-orange" data-loading-text="Đang lưu...">{{ icon('save') }} Lưu thay đổi</button>
    </form>
</div>
@endsection
