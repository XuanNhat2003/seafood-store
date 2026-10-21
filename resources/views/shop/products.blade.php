@extends('layouts.shop')

@section('title', $pageTitle . ' - Hải Sản Tươi Ngon')

@section('content')
<div class="shop-container">
    <div class="row">
        {{-- SIDEBAR --}}
        <aside class="col-lg-3 mb-4">
            <div class="shop-sidebar">
                <div class="shop-sidebar-head">Danh mục sản phẩm</div>
                <ul class="shop-cat-list">
                    @foreach ($roots as $root)
                        <li class="shop-cat-item">
                            <a href="{{ url('/danh-muc/' . $root->slug) }}" class="{{ $slug === $root->slug ? 'active' : '' }}">
                                <span>{{ $root->name }}</span>
                                <span class="count">{{ $root->product_count ?? 0 }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        {{-- CONTENT --}}
        <div class="col-lg-9">
            <nav class="shop-breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="sep"> / </span>
                <span class="current">{{ $crumbLabel }}</span>
            </nav>

            <div class="shop-head d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="shop-title h4 mb-1">{{ $pageTitle }}</h1>
                    <div class="shop-result-count text-muted small">Hiển thị {{ $totalCount }} kết quả</div>
                </div>
                <div class="shop-sort">
                    <form method="GET" id="sortForm">
                        @foreach (request()->except(['sort']) as $k => $v)
                            @if (is_string($v))
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <select class="form-select" name="sort" aria-label="Sắp xếp sản phẩm" onchange="this.form.submit()">
                            <option value="newest" {{ !in_array($sort, ['priceAsc', 'priceDesc']) ? 'selected' : '' }}>Mới nhất</option>
                            <option value="priceAsc" {{ $sort === 'priceAsc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="priceDesc" {{ $sort === 'priceDesc' ? 'selected' : '' }}>Giá giảm dần</option>
                        </select>
                    </form>
                </div>
            </div>

            @if ($items->isEmpty())
                <div class="shop-empty text-center py-5">
                    {{ icon('inbox', 56, 'mb-3 text-muted') }}
                    <p class="mb-0">Không tìm thấy sản phẩm nào</p>
                </div>
            @else
                <div class="shop-grid">
                    @foreach ($items as $product)
                        @include('shop.partials.product-card')
                    @endforeach
                </div>

                @if ($items->lastPage() > 1)
                    <nav class="mt-4" aria-label="Phân trang">
                        <ul class="pagination justify-content-center">
                            <li class="page-item{{ $items->onFirstPage() ? ' disabled' : '' }}">
                                <a class="page-link" href="{{ $items->previousPageUrl() ?: '#' }}">Trước</a>
                            </li>
                            @for ($n = 1; $n <= $items->lastPage(); $n++)
                                <li class="page-item{{ $n === $items->currentPage() ? ' active' : '' }}">
                                    <a class="page-link" href="{{ $items->url($n) }}">{{ $n }}</a>
                                </li>
                            @endfor
                            <li class="page-item{{ $items->hasMorePages() ? '' : ' disabled' }}">
                                <a class="page-link" href="{{ $items->nextPageUrl() ?: '#' }}">Sau</a>
                            </li>
                        </ul>
                    </nav>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
