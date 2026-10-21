<?php

use App\Http\Controllers\Shop\VnpayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| VNPay gọi IPN (server-to-server) để báo kết quả thanh toán.
| URL: {APP_URL}/api/vnpay-ipn  (cấu hình VNPAY_IPN_URL trong .env)
*/

Route::get('/vnpay-ipn', [VnpayController::class, 'ipn'])->name('vnpay.ipn');
