<section class="shop-section">
    <div class="shop-section-head">
        <span class="shop-section-title">{{ $title }}</span>
        @if (!empty($moreTo))
            <a href="{{ $moreTo }}" class="shop-section-more">
                Xem tất cả {{ icon('arrow-right', null, '', 'none', 'vertical-align:-2px') }}
            </a>
        @endif
    </div>
    {{ $slot }}
</section>
