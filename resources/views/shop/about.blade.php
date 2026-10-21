@extends('layouts.shop')

@section('title', 'Giới thiệu - Hải Sản Tươi Ngon')

@php
    $HIGHLIGHTS = [
        ['icon' => 'droplet', 'title' => 'Tươi sống mỗi ngày', 'desc' => 'Hải sản được nhập trực tiếp từ tàu cá và nuôi giữ trong bể oxy, đảm bảo độ tươi sống đến tận tay bạn.'],
        ['icon' => 'truck', 'title' => 'Giao hàng thần tốc', 'desc' => 'Giao nhanh trong vòng 2 giờ tại nội thành TP.HCM, đóng gói giữ lạnh chuyên nghiệp.'],
        ['icon' => 'tag', 'title' => 'Giá tận gốc', 'desc' => 'Cam kết giá tốt nhờ liên kết trực tiếp với ngư dân và vùng nuôi, không qua trung gian.'],
    ];
@endphp

@section('content')
<div class="shop-section">
    <nav class="shop-breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a> / <span>Giới thiệu</span>
    </nav>

    <h2 class="mb-4">Giới thiệu về Hải Sản Tươi Ngon</h2>

    <img src="https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&fit=crop&w=1200&q=80"
         alt="Hải sản tươi sống" class="img-fluid rounded mb-4"
         style="width:100%;max-height:360px;object-fit:cover">

    <p>
        <strong>Hải Sản Tươi Ngon</strong> là địa chỉ tin cậy của hàng nghìn gia đình
        Việt trong việc lựa chọn nguồn hải sản tươi sống, an toàn và chất lượng. Ra đời
        với sứ mệnh mang hương vị biển cả nguyên bản đến từng bữa cơm, chúng tôi không
        ngừng nỗ lực để mỗi sản phẩm đến tay khách hàng đều đạt độ tươi ngon tuyệt đối.
    </p>

    <p>
        Chúng tôi <strong>cam kết tươi sống</strong> 100%: toàn bộ tôm, cua, cá, mực,
        ngao, sò... được tuyển chọn kỹ lưỡng và bảo quản trong hệ thống bể oxy hiện đại.
        Mỗi sản phẩm đều có <strong>nguồn gốc rõ ràng</strong>, được truy xuất minh bạch
        từ vùng nuôi đạt chuẩn và các ngư trường uy tín dọc bờ biển Việt Nam.
    </p>

    <p>
        Với hệ thống logistics chuyên nghiệp, chúng tôi tự hào <strong>giao hàng nhanh
        trong 2 giờ</strong> tại khu vực nội thành, đảm bảo hải sản vẫn giữ trọn độ tươi
        khi đến nhà bạn. Quy trình đóng gói giữ lạnh giúp sản phẩm an toàn trên mọi hành
        trình.
    </p>

    <p>
        Đứng sau mỗi đơn hàng là <strong>đội ngũ</strong> nhân viên tận tâm, am hiểu hải
        sản và luôn sẵn sàng tư vấn cách chọn, cách chế biến phù hợp với từng món ăn.
        Hải Sản Tươi Ngon trân trọng từng khách hàng và xem sự hài lòng của bạn là thước
        đo thành công của chúng tôi.
    </p>

    <div class="row g-4 mt-3">
        @foreach ($HIGHLIGHTS as $h)
            <div class="col-12 col-md-4">
                <div class="shop-panel h-100 text-center">
                    <div class="text-shop-orange mb-3">{{ icon($h['icon'], 28) }}</div>
                    <h5 class="fw-bold">{{ $h['title'] }}</h5>
                    <p class="text-muted mb-0">{{ $h['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
