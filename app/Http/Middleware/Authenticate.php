<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends Middleware
{
    /**
     * Khu vực khách hàng (CustomerRoute): chưa đăng nhập => về trang /dang-nhap,
     * ghi nhớ trang đang mở để quay lại sau khi đăng nhập.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            abort(response()->json([
                'message'  => 'Vui lòng đăng nhập để tiếp tục.',
                'redirect' => route('login'),
            ], 401));
        }
        throw new AuthenticationException('Unauthenticated.', $guards, $this->redirectTo($request));
    }
}
