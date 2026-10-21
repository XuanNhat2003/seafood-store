@extends('layouts.shop')

@section('content')
@php $u = auth()->user(); @endphp
<div class="shop-container">
    <nav class="shop-breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span class="sep"> / </span>
        <span class="current">Tài khoản</span>
    </nav>

    <div class="row">
        {{-- LEFT — menu --}}
        <aside class="col-lg-3 mb-4">
            <div class="account-menu">
                <div class="account-menu-head">
                    <div class="account-avatar">{{ mb_strtoupper(mb_substr($u->full_name ?: 'K', 0, 1)) }}</div>
                    <div class="account-meta">
                        <div class="account-name">{{ $u->full_name ?: 'Khách hàng' }}</div>
                        <div class="account-email">{{ $u->email }}</div>
                    </div>
                </div>

                <nav class="account-nav">
                    <a href="{{ route('account.profile') }}" class="account-nav-link {{ request()->routeIs('account.profile') ? 'active' : '' }}">
                        {{ icon('user') }} <span>Thông tin cá nhân</span>
                    </a>
                    <a href="{{ route('account.orders') }}" class="account-nav-link {{ request()->routeIs('account.orders*') ? 'active' : '' }}">
                        {{ icon('shopping-bag') }} <span>Đơn hàng của tôi</span>
                    </a>
                    <a href="{{ route('account.password') }}" class="account-nav-link {{ request()->routeIs('account.password') ? 'active' : '' }}">
                        {{ icon('lock') }} <span>Đổi mật khẩu</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="account-nav-link account-logout">{{ icon('log-out') }} <span>Đăng xuất</span></button>
                    </form>
                </nav>
            </div>
        </aside>

        {{-- RIGHT — content --}}
        <div class="col-lg-9">
            @yield('account')
        </div>
    </div>
</div>
@endsection
