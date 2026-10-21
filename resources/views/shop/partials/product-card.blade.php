@php
    $FALLBACK = 'https://placehold.co/400x400/eaf4fc/1577c4?text=Hải+Sản';
    $img = img_url($product->image_url, $FALLBACK);
    $detailUrl = url('/san-pham/' . $product->slug);
@endphp
<div class="product-card">
    @if ($product->has_sale)
        <span class="pc-sale">-{{ $product->discount_percent }}%</span>
    @endif
    <a href="{{ $detailUrl }}" class="pc-imgwrap">
        <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy"
             onerror="this.onerror=null;this.src='{{ $FALLBACK }}'">
    </a>
    <div class="pc-body">
        <a href="{{ $detailUrl }}" class="pc-name">{{ $product->name }}</a>
        <div class="pc-price">
            {{ format_price($product->effective_price) }}
            @if ($product->has_sale)
                <span class="old">{{ format_price($product->price) }}</span>
            @endif
        </div>
        <button class="pc-cta" type="button" data-add-to-cart="{{ $product->id }}">
            {{ icon('shopping-cart', null, '', 'none', 'margin-right:6px') }}Thêm vào giỏ hàng
        </button>
    </div>
</div>
