<?php

namespace App\Providers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        Carbon::setLocale('vi');

        // Dữ liệu dùng chung cho layout cửa hàng: danh mục (mega menu, footer) + số lượng giỏ hàng
        View::composer('layouts.shop', function ($view) {
            $cats = Category::activeFlatWithCounts();
            $cartCount = 0;
            if (Auth::check() && Auth::user()->cart) {
                $cartCount = (int) Auth::user()->cart->items()->sum('quantity');
            }
            $view->with('layoutCats', $cats)->with('cartCount', $cartCount);
        });
    }
}
