@extends('layouts.shop')

@section('title', $product->name . ' - Hải Sản Tươi Ngon')

@php
    $PLACEHOLDER = 'https://placehold.co/600x600?text=Hải+Sản';
    $inStock = $product->stock_quantity > 0;
    $descParagraphs = array_values(array_filter(array_map('trim', preg_split('/\n+/', (string) $product->description))));
    $stars = function ($value, $size = 16) {
        $rounded = (int) round($value);
        $html = '<span class="shop-stars">';
        for ($n = 1; $n <= 5; $n++) {
            $html .= icon('star', $size, $n <= $rounded ? '' : 'empty', $n <= $rounded ? 'currentColor' : 'none');
        }
        return $html . '</span>';
    };
@endphp

@section('content')
<div class="shop-container">
    <div class="shop-breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span> / </span>
        <a href="{{ url('/san-pham') }}">{{ optional($product->category)->name }}</a>
        <span> / </span>
        <span>{{ $product->name }}</span>
    </div>

    <div class="row">
        {{-- Gallery --}}
        <div class="col-lg-5">
            <div class="pd-gallery-main">
                <img id="pdMainImage" src="{{ img_url($mainImage, $PLACEHOLDER) }}" alt="{{ $product->name }}">
            </div>
            @if ($images->isNotEmpty())
                <div class="pd-thumbs">
                    @foreach ($images as $img)
                        <div class="pd-thumb {{ $mainImage === $img->image_url ? 'active' : '' }}" data-src="{{ img_url($img->image_url) }}">
                            <img src="{{ img_url($img->image_url) }}" alt="{{ $product->name }}">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            <h1 class="pd-title">{{ $product->name }}</h1>

            <div style="display:flex;align-items:center;gap:8px;margin:8px 0">
                {!! $stars($avgRating) !!}
                <span style="color:#64748b;font-size:14px">({{ $reviews->count() }} đánh giá)</span>
            </div>

            <div class="pd-price">
                {{ format_price($product->effective_price) }}
                @if ($product->has_sale)
                    <span class="old">{{ format_price($product->price) }}</span>
                @endif
            </div>

            <div class="pd-meta-row"><strong>Mã SP:</strong> <span>{{ $product->product_code }}</span></div>
            <div class="pd-meta-row">
                <strong>Tình trạng:</strong>
                <span style="color:{{ $inStock ? '#16a34a' : '#dc2626' }};font-weight:600">{{ $inStock ? 'Còn hàng' : 'Hết hàng' }}</span>
            </div>
            <div class="pd-meta-row"><strong>Đơn vị:</strong> <span>{{ $product->unit }}</span></div>
            <div class="pd-meta-row"><strong>Danh mục:</strong> <span>{{ optional($product->category)->name }}</span></div>

            {{-- Quantity --}}
            <div style="display:flex;align-items:center;gap:12px;margin:16px 0">
                <span style="font-weight:600">Số lượng:</span>
                <div class="qty-stepper">
                    <button type="button" id="qtyDec" aria-label="Giảm">{{ icon('minus') }}</button>
                    <input type="number" id="qtyInput" min="1" max="{{ max(1, $product->stock_quantity) }}" value="1">
                    <button type="button" id="qtyInc" aria-label="Tăng">{{ icon('plus') }}</button>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:12px;flex-wrap:wrap">
                <button type="button" class="btn-shop-orange" id="btnAddToCart" {{ $inStock ? '' : 'disabled' }}>
                    {{ icon('shopping-cart') }} Thêm vào giỏ hàng
                </button>
                <button type="button" class="btn-shop-blue" id="btnBuyNow" {{ $inStock ? '' : 'disabled' }}>
                    Mua ngay
                </button>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="shop-panel" style="margin-top:28px">
        <div class="shop-panel-head">Mô tả sản phẩm</div>
        <div class="shop-panel-body" style="padding:16px 20px">
            @forelse ($descParagraphs as $p)
                <p>{{ $p }}</p>
            @empty
                <p>Sản phẩm hải sản tươi ngon, được tuyển chọn kỹ lưỡng. Liên hệ với chúng tôi để biết thêm chi tiết.</p>
            @endforelse
        </div>
    </div>

    {{-- Reviews --}}
    <div class="shop-panel" style="margin-top:28px" id="reviews">
        <div class="shop-panel-head">Đánh giá sản phẩm ({{ $reviews->count() }})</div>
        <div class="shop-panel-body" style="padding:16px 20px">
            @if ($reviews->isEmpty())
                <p style="color:#64748b">Chưa có đánh giá nào cho sản phẩm này.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:18px">
                    @foreach ($reviews as $rv)
                        <div style="display:flex;gap:12px">
                            <div style="width:42px;height:42px;border-radius:50%;flex-shrink:0;background:#0e63b0;color:#fff;font-weight:700;display:flex;align-items:center;justify-content:center;text-transform:uppercase">
                                {{ mb_substr($rv->user_name ?: '?', 0, 1) }}
                            </div>
                            <div style="flex:1">
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                                    <strong>{{ $rv->user_name }}</strong>
                                    {!! $stars($rv->rating, 14) !!}
                                    <span style="color:#94a3b8;font-size:13px">{{ format_date($rv->created_at) }}</span>
                                </div>
                                <p style="margin:6px 0 0">{{ $rv->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Review form --}}
            <div style="margin-top:24px;border-top:1px solid #e5edf5;padding-top:20px">
                @auth
                    <form method="POST" action="{{ route('review.store') }}" id="reviewForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="rating" id="ratingInput" value="5">
                        <div style="font-weight:700;margin-bottom:10px">Viết đánh giá của bạn</div>
                        <div class="rating-picker" style="display:flex;align-items:center;gap:6px;margin-bottom:12px">
                            <span style="margin-right:4px">Đánh giá:</span>
                            @for ($n = 1; $n <= 5; $n++)
                                <span data-star="{{ $n }}" style="color:#f5a623;display:inline-flex">{{ icon('star', 24, '', '#f5a623') }}</span>
                            @endfor
                        </div>
                        <textarea class="form-control" name="comment" id="reviewComment" rows="4"
                                  placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." style="margin-bottom:12px"></textarea>
                        <button type="submit" class="btn-shop-orange" data-loading-text="Đang gửi...">
                            {{ icon('check') }} Gửi đánh giá
                        </button>
                    </form>
                @else
                    <p style="color:#64748b">
                        Vui lòng <a href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá.
                    </p>
                @endauth
            </div>
        </div>
    </div>

    {{-- Similar products --}}
    @if ($similar->isNotEmpty())
        <div class="shop-section" style="margin-top:28px">
            <div class="shop-section-head">
                <span class="shop-section-title">Sản phẩm tương tự</span>
            </div>
            <div class="shop-grid shop-grid-5">
                @foreach ($similar as $product_)
                    @include('shop.partials.product-card', ['product' => $product_])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    var productId = {{ $product->id }};
    var maxQty = {{ max(1, $product->stock_quantity) }};
    var qty = document.getElementById('qtyInput');

    // Gallery thumbnails
    document.querySelectorAll('.pd-thumb').forEach(function (t) {
        t.addEventListener('click', function () {
            document.getElementById('pdMainImage').src = t.dataset.src;
            document.querySelectorAll('.pd-thumb').forEach(function (x) { x.classList.remove('active'); });
            t.classList.add('active');
        });
    });

    // Quantity stepper
    function clamp(v) { v = parseInt(v, 10); if (isNaN(v)) return 1; return Math.min(Math.max(1, v), maxQty); }
    document.getElementById('qtyDec').addEventListener('click', function () { qty.value = clamp(qty.value - 1); });
    document.getElementById('qtyInc').addEventListener('click', function () { qty.value = clamp(+qty.value + 1); });
    qty.addEventListener('change', function () { qty.value = clamp(qty.value); });

    function doAdd() { return addToCart(productId, clamp(qty.value)).then(handleAddResult); }
    document.getElementById('btnAddToCart').addEventListener('click', function () { doAdd(); });
    document.getElementById('btnBuyNow').addEventListener('click', function () {
        doAdd().then(function (ok) { if (ok) window.location.href = @json(url('/gio-hang')); });
    });

    // Rating picker
    var ratingInput = document.getElementById('ratingInput');
    if (ratingInput) {
        var stars = document.querySelectorAll('.rating-picker [data-star]');
        function paint(r) {
            stars.forEach(function (s) {
                var on = +s.dataset.star <= r;
                var svg = s.querySelector('svg');
                svg.setAttribute('fill', on ? '#f5a623' : 'none');
                s.style.color = on ? '#f5a623' : '#d6dde4';
            });
        }
        stars.forEach(function (s) {
            s.addEventListener('click', function () { ratingInput.value = s.dataset.star; paint(+s.dataset.star); });
        });
        paint(5);
        document.getElementById('reviewForm').addEventListener('submit', function (e) {
            if (!document.getElementById('reviewComment').value.trim()) {
                e.preventDefault();
                toast.info('Vui lòng nhập nội dung đánh giá.');
            }
        });
    }
})();
</script>
@endpush
