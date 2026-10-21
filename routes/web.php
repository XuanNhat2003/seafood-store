<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Shop;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| STOREFRONT (khách ghé thăm)
|--------------------------------------------------------------------------
*/
Route::get('/', [Shop\HomeController::class, 'index'])->name('home');
Route::get('/san-pham', [Shop\ProductController::class, 'index'])->name('shop');
Route::get('/danh-muc/{slug}', [Shop\ProductController::class, 'index'])->name('shop.category');
Route::get('/san-pham/{slug}', [Shop\ProductController::class, 'show'])->name('product.show');
Route::get('/gioi-thieu', [Shop\PageController::class, 'about'])->name('about');
Route::get('/tin-tuc', [Shop\PageController::class, 'news'])->name('news');
Route::get('/lien-he', [Shop\PageController::class, 'contact'])->name('contact');
Route::post('/lien-he', [Shop\PageController::class, 'submitContact'])->name('contact.submit');

Route::get('/dang-nhap', [Shop\AuthController::class, 'showLogin'])->name('login');
Route::post('/dang-nhap', [Shop\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
Route::get('/dang-ky', [Shop\AuthController::class, 'showRegister'])->name('register');
Route::post('/dang-ky', [Shop\AuthController::class, 'register'])->name('register.submit');
Route::post('/dang-xuat', [Shop\AuthController::class, 'logout'])->name('logout');

// VNPay redirect người dùng về sau khi thanh toán
Route::get('/thanh-toan/vnpay-return', [Shop\VnpayController::class, 'return'])->name('vnpay.return');

/*
|--------------------------------------------------------------------------
| CUSTOMER (cần đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/gio-hang', [Shop\CartController::class, 'index'])->name('cart');
    Route::post('/gio-hang/items', [Shop\CartController::class, 'add'])->name('cart.add');
    Route::put('/gio-hang/items/{item}', [Shop\CartController::class, 'update'])->name('cart.update');
    Route::delete('/gio-hang/items/{item}', [Shop\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/gio-hang', [Shop\CartController::class, 'clear'])->name('cart.clear');

    Route::get('/thanh-toan', [Shop\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/thanh-toan', [Shop\CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/thanh-toan/ma-giam-gia', [Shop\CheckoutController::class, 'validateVoucher'])->name('checkout.voucher');

    Route::post('/danh-gia', [Shop\ReviewController::class, 'store'])->name('review.store');

    Route::get('/tai-khoan', [Shop\AccountController::class, 'profile'])->name('account.profile');
    Route::put('/tai-khoan', [Shop\AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::get('/tai-khoan/doi-mat-khau', [Shop\AccountController::class, 'password'])->name('account.password');
    Route::put('/tai-khoan/doi-mat-khau', [Shop\AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/tai-khoan/don-hang', [Shop\AccountController::class, 'orders'])->name('account.orders');
    Route::get('/tai-khoan/don-hang/{id}', [Shop\AccountController::class, 'orderDetail'])->name('account.orders.show');
    Route::post('/tai-khoan/don-hang/{id}/huy', [Shop\AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    Route::post('/tai-khoan/don-hang/{id}/thanh-toan-vnpay', [Shop\VnpayController::class, 'pay'])->name('account.orders.vnpay');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [Admin\AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1')->name('admin.login.submit');
Route::post('/admin/logout', [Admin\AuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/quick-stats', [Admin\DashboardController::class, 'quickStats'])->name('quick-stats');

    Route::post('/upload', [Admin\UploadController::class, 'store'])->name('upload');

    // Sản phẩm
    Route::get('/products', [Admin\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [Admin\ProductController::class, 'show'])->name('products.show');
    Route::post('/products', [Admin\ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [Admin\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [Admin\ProductController::class, 'deactivate'])->name('products.deactivate');
    Route::delete('/products/{id}/permanent', [Admin\ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/images', [Admin\ProductController::class, 'addImage'])->name('products.images.store');
    Route::delete('/products/{id}/images/{imageId}', [Admin\ProductController::class, 'deleteImage'])->name('products.images.destroy');

    // Danh mục
    Route::get('/categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Đơn hàng
    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');

    // Người dùng
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::put('/users/{id}/status', [Admin\UserController::class, 'toggleStatus'])->name('users.status');

    // Banner
    Route::get('/banners', [Admin\BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [Admin\BannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{id}', [Admin\BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{id}', [Admin\BannerController::class, 'destroy'])->name('banners.destroy');

    // Liên hệ
    Route::get('/contacts', [Admin\ContactController::class, 'index'])->name('contacts.index');
    Route::put('/contacts/{id}/read', [Admin\ContactController::class, 'markRead'])->name('contacts.read');

    // Kho hàng
    Route::get('/inventory', [Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/import', [Admin\InventoryController::class, 'import'])->name('inventory.import');

    // Khuyến mãi
    Route::get('/vouchers', [Admin\VoucherController::class, 'index'])->name('vouchers.index');
    Route::post('/vouchers', [Admin\VoucherController::class, 'store'])->name('vouchers.store');
    Route::put('/vouchers/{id}', [Admin\VoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('/vouchers/{id}', [Admin\VoucherController::class, 'destroy'])->name('vouchers.destroy');

    // Báo cáo
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');

    // Đánh giá
    Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{id}/approval', [Admin\ReviewController::class, 'setApproval'])->name('reviews.approval');
    Route::delete('/reviews/{id}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
});
