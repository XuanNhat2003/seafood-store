<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title', 'Hải Sản Tươi Ngon - Admin')</title>
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
    $admin = auth()->user();
    $ordersActive = request()->routeIs('admin.orders.*');
    $statusQ = request('status');
    $menuItems = [
        ['route' => 'admin.dashboard', 'name' => 'Dashboard', 'icon' => 'grid', 'match' => 'admin.dashboard'],
        ['route' => 'admin.products.index', 'name' => 'Sản phẩm', 'icon' => 'shopping-bag', 'match' => 'admin.products.*'],
        ['route' => 'admin.categories.index', 'name' => 'Danh mục', 'icon' => 'layers', 'match' => 'admin.categories.*'],
    ];
    $restMenuItems = [
        ['route' => 'admin.inventory.index', 'name' => 'Kho hàng', 'icon' => 'archive', 'match' => 'admin.inventory.*'],
        ['route' => 'admin.vouchers.index', 'name' => 'Khuyến mãi', 'icon' => 'percent', 'match' => 'admin.vouchers.*'],
        ['route' => 'admin.reviews.index', 'name' => 'Đánh giá', 'icon' => 'star', 'match' => 'admin.reviews.*'],
        ['route' => 'admin.reports.index', 'name' => 'Báo cáo thống kê', 'icon' => 'bar-chart-2', 'match' => 'admin.reports.*'],
        ['route' => 'admin.users.index', 'name' => 'Người dùng', 'icon' => 'users', 'match' => 'admin.users.*'],
        ['route' => 'admin.banners.index', 'name' => 'Banners', 'icon' => 'image', 'match' => 'admin.banners.*'],
        ['route' => 'admin.contacts.index', 'name' => 'Liên hệ', 'icon' => 'mail', 'match' => 'admin.contacts.*'],
    ];
    $orderSub = [
        ['status' => null, 'name' => 'Tất cả'],
        ['status' => 'pending', 'name' => 'Chờ xác nhận'],
        ['status' => 'confirmed', 'name' => 'Đã xác nhận'],
        ['status' => 'shipping', 'name' => 'Đang giao'],
        ['status' => 'delivered', 'name' => 'Đã giao'],
        ['status' => 'cancelled', 'name' => 'Đã hủy'],
    ];
@endphp
<div class="admin-wrapper">
    {{-- Sidebar --}}
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon" style="padding:0;overflow:hidden;border:1px solid rgba(255,255,255,0.1)">
                <img src="{{ asset('images/logo.png') }}" alt="Seafood Admin" style="width:100%;height:100%;object-fit:cover">
            </div>
            <span class="brand-text">Seafood Admin</span>
        </div>
        <ul class="sidebar-menu">
            @foreach ($menuItems as $item)
                <li class="menu-item">
                    <a href="{{ route($item['route']) }}" class="menu-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                        {{ icon($item['icon']) }}
                        <span>{{ $item['name'] }}</span>
                    </a>
                </li>
            @endforeach

            {{-- Orders collapsible item --}}
            <li class="menu-item">
                <div class="menu-link d-flex align-items-center justify-content-between {{ $ordersActive ? 'active' : '' }}" style="cursor:pointer" id="ordersToggle">
                    <div class="d-flex align-items-center gap-3">
                        {{ icon('trending-up') }}
                        <span>Đơn hàng</span>
                    </div>
                    <span id="ordersArrow">{!! $ordersActive ? icon('chevron-down', null, 'collapse-arrow') : icon('chevron-right', null, 'collapse-arrow') !!}</span>
                </div>
                <ul class="submenu-list {{ $ordersActive ? '' : 'collapsed' }}" id="ordersSubmenu">
                    @foreach ($orderSub as $s)
                        @php
                            $active = $ordersActive && (($s['status'] === null && !$statusQ) || ($s['status'] !== null && $statusQ === $s['status']));
                        @endphp
                        <li class="submenu-item">
                            <a href="{{ route('admin.orders.index', $s['status'] ? ['status' => $s['status']] : []) }}" class="submenu-link {{ $active ? 'active' : '' }}">{{ $s['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>

            @foreach ($restMenuItems as $item)
                <li class="menu-item">
                    <a href="{{ route($item['route']) }}" class="menu-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                        {{ icon($item['icon']) }}
                        <span>{{ $item['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 border-0 d-flex align-items-center justify-content-center gap-2 py-2" style="border-radius:12px">
                    {{ icon('log-out') }}
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content Area --}}
    <main class="admin-main">
        <header class="admin-header">
            <div class="d-flex align-items-center gap-4">
                <div class="header-title">
                    <h4 class="m-0">Seafood Store Admin</h4>
                </div>
                <div class="header-signal d-none d-xl-flex">
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="header-signal-item text-decoration-none">
                        {{ icon('clock') }}
                        <span><span data-qs="pendingOrders">0</span> đơn chờ</span>
                    </a>
                    <a href="{{ route('admin.contacts.index') }}" class="header-signal-item text-decoration-none">
                        {{ icon('inbox') }}
                        <span><span data-qs="unreadContacts">0</span> liên hệ mới</span>
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.contacts.index') }}"
                   class="position-relative d-flex align-items-center justify-content-center bg-light rounded-circle border"
                   style="width:40px;height:40px;cursor:pointer;transition:all 0.2s" title="Xem thông báo liên hệ">
                    {{ icon('bell', null, 'text-muted', 'none', 'font-size:18px') }}
                    <span id="notiCount" class="position-absolute translate-middle badge rounded-pill bg-danger border border-light" style="top:6px;right:-4px;padding:4px 6px;font-size:9px;display:none">0</span>
                </a>

                <div class="user-profile ms-2">
                    <div class="user-info">
                        <p class="user-name">{{ $admin->full_name ?: 'Quản trị viên' }}</p>
                        <p class="user-role">{{ $admin->role ?: 'admin' }}</p>
                    </div>
                    <div class="avatar">{{ mb_strtoupper(mb_substr($admin->full_name ?: 'A', 0, 1)) }}</div>
                </div>
            </div>
        </header>

        <div class="admin-content-body">
            @yield('content')
        </div>
    </main>
</div>

<script>
    window.__FLASH = {!! json_encode([
        'success' => session('success'),
        'error' => session('error') ?: ($errors->any() ? $errors->first() : null),
        'info' => session('info'),
        'warning' => session('warning'),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!};
    window.ADMIN = {!! json_encode([
        'quickStatsUrl' => route('admin.quick-stats'),
        'uploadUrl' => route('admin.upload'),
    ], JSON_UNESCAPED_SLASHES) !!};
</script>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/feather/feather.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
