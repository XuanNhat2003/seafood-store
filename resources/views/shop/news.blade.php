@extends('layouts.shop')

@section('title', 'Tin tức & Cẩm nang - Hải Sản Tươi Ngon')

@php
    $POSTS = [
        ['title' => 'Bí quyết chọn tôm sú tươi ngon chuẩn nhà hàng', 'excerpt' => 'Làm sao để phân biệt tôm tươi và tôm ươn? Cùng khám phá những mẹo nhỏ giúp bạn chọn được tôm sú chắc thịt, ngọt nước cho bữa ăn gia đình.', 'img' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&fit=crop&w=600&q=80'],
        ['title' => 'Cách hấp cua biển giữ trọn vị ngọt tự nhiên', 'excerpt' => 'Hấp cua tưởng đơn giản nhưng không phải ai cũng làm đúng. Bài viết chia sẻ thời gian và cách hấp để cua chắc thịt, không bị tanh.', 'img' => 'https://images.unsplash.com/photo-1559737558-2f5a35f4523b?auto=format&fit=crop&w=600&q=80'],
        ['title' => 'Top 5 món ngon từ mực bạn nên thử cuối tuần', 'excerpt' => 'Từ mực hấp gừng đến mực nướng sa tế, đây là những món ăn dễ làm mà cả nhà đều mê. Cùng vào bếp với hải sản tươi sống nhé!', 'img' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=600&q=80'],
        ['title' => 'Giá trị dinh dưỡng tuyệt vời từ cá hồi', 'excerpt' => 'Cá hồi giàu omega-3 tốt cho tim mạch và trí não. Tìm hiểu cách bảo quản và chế biến cá hồi để giữ trọn dưỡng chất.', 'img' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=600&q=80'],
        ['title' => 'Mẹo bảo quản hải sản tươi trong tủ lạnh', 'excerpt' => 'Hải sản mua về chưa dùng ngay nên bảo quản thế nào? Những lưu ý quan trọng giúp giữ độ tươi và an toàn thực phẩm cho gia đình.', 'img' => 'https://images.unsplash.com/photo-1498654200943-1088dd4438ae?auto=format&fit=crop&w=600&q=80'],
        ['title' => 'Hải sản và sức khỏe: ăn bao nhiêu là đủ?', 'excerpt' => 'Hải sản tốt cho sức khỏe nhưng cần ăn đúng cách và đúng lượng. Chuyên gia dinh dưỡng chia sẻ khẩu phần hợp lý cho mỗi tuần.', 'img' => 'https://images.unsplash.com/photo-1535140728325-a4d3707eee61?auto=format&fit=crop&w=600&q=80'],
    ];
@endphp

@section('content')
<div class="shop-section">
    <nav class="shop-breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a> / <span>Tin tức</span>
    </nav>

    <h2 class="mb-4">Tin tức &amp; Cẩm nang</h2>

    <div class="shop-grid">
        @foreach ($POSTS as $post)
            <div class="card h-100 border">
                <img src="{{ $post['img'] }}" alt="{{ $post['title'] }}" class="card-img-top" style="height:180px;object-fit:cover">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold">{{ $post['title'] }}</h5>
                    <p class="text-muted flex-grow-1" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                        {{ $post['excerpt'] }}
                    </p>
                    <a href="#" class="btn-shop-blue align-self-start">Xem thêm</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
