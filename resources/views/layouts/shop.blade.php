<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title', 'Hải Sản Tươi Ngon')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/toastify/ReactToastify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @stack('styles')
</head>
<body>
@php
    $authUser = auth()->user();
    $isCustomer = $authUser && $authUser->role !== 'admin';
    $roots = $layoutCats->whereNull('parent_id')->values();
    $menu = [
        ['name' => 'Trang chủ', 'url' => url('/'), 'active' => request()->is('/')],
        ['name' => 'Giới thiệu', 'url' => url('/gioi-thieu'), 'active' => request()->is('gioi-thieu')],
        ['name' => 'Cửa hàng', 'url' => url('/san-pham'), 'active' => request()->is('san-pham') && !request('sort')],
        ['name' => 'Hàng mới về', 'url' => url('/san-pham?sort=new'), 'active' => request()->is('san-pham') && request('sort') === 'new'],
        ['name' => 'Tin tức', 'url' => url('/tin-tuc'), 'active' => request()->is('tin-tuc')],
        ['name' => 'Liên hệ', 'url' => url('/lien-he'), 'active' => request()->is('lien-he')],
    ];
@endphp
<div class="shop-body">
    {{-- Top promo bar --}}
    <div class="shop-topbar">
        <div class="shop-topbar-fallback">
            <span>HẢI SẢN <span class="hl">TƯƠI SỐNG</span></span>
            <span>| GIAO "THẦN TỐC" TRONG 2H |</span>
            <a href="{{ url('/san-pham') }}" class="pill">MUA SẮM NGAY ➜</a>
        </div>
    </div>

    {{-- Header --}}
    <header class="shop-header">
        <div class="shop-container shop-header-inner">
            <a href="{{ url('/') }}" class="shop-logo">
                <span class="shop-logo-badge">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M45 20c-13-4-27 0-35 12 8 12 22 16 35 12 7-2 12-7 14-12-2-5-7-10-14-12z" fill="#fff"/>
                        <path d="M45 20l11-7-2.5 13L56 39l-11-7z" fill="#fff" opacity="0.9"/>
                        <circle cx="22" cy="29" r="3.2" fill="#1577c4"/>
                        <path d="M30 41c4.5 3.2 10 3.2 14.5 0" stroke="#1577c4" stroke-width="2.4" stroke-linecap="round" fill="none"/>
                        <path d="M6 50c5-4 9-4 14 0s9 4 14 0 9-4 14 0 9 4 14 0" stroke="#bfe0fb" stroke-width="2.4" stroke-linecap="round" fill="none"/>
                    </svg>
                </span>
                <span class="logo-text">Hải Sản Tươi Ngon<small>TƯƠI NGON MỖI NGÀY</small></span>
            </a>

            <form class="shop-search" action="{{ url('/san-pham') }}" method="GET">
                <input name="search" value="{{ request()->is('san-pham') && is_string(request('search')) ? request('search') : '' }}" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit">{{ icon('search') }}</button>
            </form>

            <div class="shop-hotline d-none d-lg-flex">
                <div class="ico">{{ icon('phone') }}</div>
                <div>
                    <div class="label">Hotline</div>
                    <div class="num">0987832744</div>
                </div>
            </div>

            {{-- Account --}}
            <div class="position-relative" id="userMenuWrap">
                @if ($isCustomer)
                    @php $nameParts = preg_split('/\s+/', trim($authUser->full_name)); @endphp
                    <button type="button" class="btn-shop-outline d-flex align-items-center gap-2" id="userMenuBtn">
                        {{ icon('user') }} {{ end($nameParts) ?: 'Tài khoản' }} {{ icon('chevron-down') }}
                    </button>
                    <div class="shop-user-menu" id="userMenu">
                        <div class="um-head">
                            <div class="um-avatar">{{ mb_substr($authUser->full_name ?: 'K', 0, 1) }}</div>
                            <div style="min-width:0">
                                <div class="um-name">{{ $authUser->full_name ?: 'Khách hàng' }}</div>
                                <div class="um-email">{{ $authUser->email }}</div>
                            </div>
                        </div>
                        <a href="{{ url('/tai-khoan') }}">{{ icon('user') }} Thông tin cá nhân</a>
                        <a href="{{ url('/tai-khoan/don-hang') }}">{{ icon('package') }} Đơn hàng của tôi</a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="um-logout">{{ icon('log-out') }} Đăng xuất</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-shop-outline d-flex align-items-center gap-2">
                        {{ icon('user') }} Đăng nhập
                    </a>
                @endif
            </div>

            {{-- Cart --}}
            <a href="{{ url('/gio-hang') }}" class="shop-cart-btn">
                {{ icon('shopping-cart', 18) }} GIỎ HÀNG
                <span class="badge-count" id="cartBadge" @if($cartCount <= 0) style="display:none" @endif>{{ $cartCount }}</span>
            </a>
        </div>
    </header>

    {{-- Nav bar --}}
    <nav class="shop-nav">
        <div class="shop-container shop-nav-inner">
            <div class="shop-nav-cat-wrap">
                <div class="shop-nav-cat">
                    {{ icon('menu') }} <span>Danh mục sản phẩm</span> {{ icon('chevron-down', null, '', 'none', 'margin-left:auto') }}
                </div>
                <div class="shop-mega">
                    @if ($roots->isEmpty())
                        <div class="text-muted p-2">Đang tải danh mục...</div>
                    @endif
                    @foreach ($roots as $root)
                        @php $children = $layoutCats->where('parent_id', $root->id); @endphp
                        <div class="shop-mega-col">
                            <a href="{{ url('/danh-muc/' . $root->slug) }}" class="shop-mega-title">
                                {{ icon('grid') }} {{ $root->name }}
                            </a>
                            <div class="shop-mega-children">
                                @foreach ($children as $ch)
                                    <a href="{{ url('/danh-muc/' . $ch->slug) }}">{{ $ch->name }}</a>
                                @endforeach
                                @if ($children->isEmpty())
                                    <a href="{{ url('/danh-muc/' . $root->slug) }}" class="text-muted">Xem tất cả</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="shop-nav-menu">
                @foreach ($menu as $m)
                    <a href="{{ $m['url'] }}" class="{{ $m['active'] ? 'active' : '' }}">{{ $m['name'] }}</a>
                @endforeach
            </div>

            <a href="{{ url('/san-pham') }}" class="shop-nav-promo">🔥 Khuyến mãi giờ vàng</a>
        </div>
    </nav>

    {{-- Page content --}}
    <main class="shop-container" style="min-height:50vh">
        @yield('content')
    </main>

    {{-- Floating contact widgets --}}
    <div class="shop-floats">
        <a href="#" class="shop-float zalo">Chat Zalo</a>
        <a href="#" class="shop-float fb">Chat Facebook</a>
        <a href="tel:0999999999" class="shop-float phone">Hotline: 0987832744</a>
    </div>

    {{-- Footer --}}
    <footer class="shop-footer">
        <div class="shop-footer-inner">
            <div>
                <h5>HẢI SẢN SEAFOOD</h5>
                <p style="font-size:14px;line-height:1.7">
                    Chúng tôi chuyên cung cấp hải sản tươi sống với giá cả cạnh tranh,
                    đáp ứng nhu cầu của khách hàng trên toàn quốc.
                </p>
            </div>
            <div>
                <h5>THÔNG TIN LIÊN HỆ</h5>
                <a href="#">📍 123 Đường Hải Sản, Q.1, TP.HCM</a>
                <a href="tel:0999999999">📞 0987832744</a>
                <a href="mailto:phamxuannhat2706@gmail.com">✉️ phamxuannhat2706@gmail.com</a>
            </div>
            <div>
                <h5>CHÍNH SÁCH CHUNG</h5>
                <a href="{{ url('/gioi-thieu') }}">Giới thiệu</a>
                <a href="#">Chính sách vận chuyển</a>
                <a href="#">Chính sách bảo hành</a>
                <a href="#">Chính sách đổi trả</a>
            </div>
            <div>
                <h5>DANH MỤC NỔI BẬT</h5>
                @foreach ($roots->take(5) as $r)
                    <a href="{{ url('/danh-muc/' . $r->slug) }}">{{ $r->name }}</a>
                @endforeach
            </div>
        </div>
        <div class="shop-footer-bottom">
            © 2025 Hải Sản Tươi Ngon. Đồ án thực tập — Website thương mại điện tử hải sản.
        </div>
    </footer>
</div>

<script>
    window.__FLASH = {!! json_encode([
        'success' => session('success'),
        'error' => session('error') ?: ($errors->any() ? $errors->first() : null),
        'info' => session('info'),
        'warning' => session('warning'),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!};
    window.SHOP = {!! json_encode([
        'isAuthenticated' => (bool) $authUser,
        'loginUrl' => route('login'),
        'cartAddUrl' => route('cart.add'),
    ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};
</script>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/feather/feather.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/shop.js') }}"></script>
@stack('scripts')
</body>
</html>
