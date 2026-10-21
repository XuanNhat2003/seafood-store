@extends('layouts.shop')

@section('title', 'Hải Sản Tươi Ngon - Hải sản tươi sống giao thần tốc')

@php
    $FEATURES = [
        ['icon' => 'shield', 'title' => 'SẢN PHẨM AN TOÀN', 'sub' => 'Cam kết chất lượng'],
        ['icon' => 'refresh-cw', 'title' => 'ĐỔI TRẢ HÀNG', 'sub' => 'Nhanh chóng, rõ ràng'],
        ['icon' => 'shopping-cart', 'title' => 'QUY CÁCH RÕ RÀNG', 'sub' => 'Sạch và tiện lợi'],
        ['icon' => 'truck', 'title' => 'MIỄN PHÍ GIAO HÀNG', 'sub' => 'Cho hóa đơn từ 500.000đ'],
    ];
    $NEWS_FALLBACK = 'https://placehold.co/500x375/eaf4fc/1577c4?text=Hải+Sản';
    $NEWS = [
        ['title' => 'Các món ăn chế biến từ mực lá', 'img' => 'https://images.unsplash.com/photo-1535399831218-d5bd36d1a6b3?w=500&q=70', 'excerpt' => 'Mực lá xào cà ri, mực nướng sa tế... những món ngon khó cưỡng từ mực lá tươi.'],
        ['title' => 'Ốc hương và những món ngon dễ làm', 'img' => 'https://images.unsplash.com/photo-1559737558-2f5a35f4523b?w=500&q=70', 'excerpt' => 'Ốc hương rang muối, ốc hương hấp sả... đơn giản mà đậm đà hương vị biển.'],
        ['title' => '15 món hải sản ngon nổi tiếng', 'img' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=500&q=70', 'excerpt' => 'Tổng hợp các món hải sản trứ danh không nên bỏ lỡ khi thưởng thức.'],
        ['title' => 'Mang không khí biển về nhà', 'img' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=500&q=70', 'excerpt' => 'Cách chế biến hải sản tươi sống ngay tại nhà chuẩn vị nhà hàng.'],
    ];
@endphp

@section('content')
<div class="shop-home" style="padding-bottom:20px">
    {{-- HERO --}}
    <div class="row g-3 mt-3">
        <div class="col-lg-8">
            @if ($mainBanner)
                <a href="{{ link_url($mainBanner->link) }}">
                    <img src="{{ img_url($mainBanner->image_url) }}" alt="{{ $mainBanner->title }}"
                         style="width:100%;height:380px;object-fit:cover;border-radius:10px">
                </a>
            @else
                <div style="height:380px;border-radius:10px;background:linear-gradient(135deg,#2aa3f0,#0e63b0)"></div>
            @endif
        </div>
        <div class="col-lg-4 d-flex flex-column gap-3">
            @for ($i = 0; $i < 2; $i++)
                @php $b = $sideBanners[$i] ?? null; @endphp
                @if ($b)
                    <a href="{{ link_url($b->link) }}" style="flex:1">
                        <img src="{{ img_url($b->image_url) }}" alt="{{ $b->title }}"
                             style="width:100%;height:182px;object-fit:cover;border-radius:10px">
                    </a>
                @else
                    <div style="flex:1;min-height:182px;border-radius:10px;background:#eaf4fc"></div>
                @endif
            @endfor
        </div>
    </div>

    {{-- FEATURE STRIP --}}
    <div class="shop-features">
        @foreach ($FEATURES as $f)
            <div class="feature-item">
                <span class="fi-ico">{{ icon($f['icon']) }}</span>
                <div>
                    <div class="fi-title">{{ $f['title'] }}</div>
                    <div class="fi-sub">{{ $f['sub'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- BEST SELLERS --}}
    @if ($bestSellers->isNotEmpty())
        @component('shop.partials.section', ['title' => 'Hải sản bán chạy', 'moreTo' => url('/san-pham')])
            <div class="shop-grid shop-grid-5">
                @foreach ($bestSellers as $product)
                    @include('shop.partials.product-card')
                @endforeach
            </div>
        @endcomponent
    @endif

    {{-- FEATURED --}}
    @if ($featured->isNotEmpty())
        @component('shop.partials.section', ['title' => 'Sản phẩm nổi bật', 'moreTo' => url('/san-pham')])
            <div class="shop-grid shop-grid-5">
                @foreach ($featured as $product)
                    @include('shop.partials.product-card')
                @endforeach
            </div>
        @endcomponent
    @endif

    {{-- BY CATEGORY --}}
    @foreach ($catSections as $sec)
        @component('shop.partials.section', ['title' => $sec['root']->name, 'moreTo' => url('/danh-muc/' . $sec['root']->slug)])
            <div class="shop-grid shop-grid-5">
                @foreach ($sec['items'] as $product)
                    @include('shop.partials.product-card')
                @endforeach
            </div>
        @endcomponent
    @endforeach

    {{-- NEWS --}}
    @component('shop.partials.section', ['title' => 'Món ngon mỗi ngày'])
        <div class="shop-grid">
            @foreach ($NEWS as $n)
                <div class="product-card">
                    <div class="pc-imgwrap">
                        <img src="{{ $n['img'] }}" alt="{{ $n['title'] }}" loading="lazy"
                             onerror="this.onerror=null;this.src='{{ $NEWS_FALLBACK }}'">
                    </div>
                    <div class="pc-body" style="text-align:left">
                        <a href="#" class="pc-name" style="min-height:auto">{{ $n['title'] }}</a>
                        <p style="font-size:15px;color:var(--shop-muted);margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                            {{ $n['excerpt'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endcomponent
</div>
@endsection
