@extends('layouts.shop')

@section('title', 'Không tìm thấy sản phẩm - Hải Sản Tươi Ngon')

@section('content')
<div class="shop-container">
    <div class="shop-empty">
        <p>Không tìm thấy sản phẩm</p>
        <a href="{{ url('/san-pham') }}" class="btn-shop-orange">Quay lại danh sách sản phẩm</a>
    </div>
</div>
@endsection
