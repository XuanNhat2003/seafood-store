<?php

/*
|--------------------------------------------------------------------------
| VNPay (sandbox) — demo thanh toán online
|--------------------------------------------------------------------------
| Đăng ký tài khoản test: https://sandbox.vnpayment.vn/devreg/
| Thẻ test (NCB): 9704198526191432198 - NGUYEN VAN A - 07/15 - OTP 123456
*/

return [
    'tmn_code'    => env('VNPAY_TMN_CODE', ''),
    'hash_secret' => env('VNPAY_HASH_SECRET', ''),
    'url'         => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url'  => env('VNPAY_RETURN_URL'),
    'ipn_url'     => env('VNPAY_IPN_URL'),
    'version'     => '2.1.0',
    'locale'      => 'vn',
    'expire_minutes' => 15,
];
