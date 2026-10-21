<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Khu vực quản trị: yêu cầu đăng nhập với vai trò admin (tương đương PrivateRoute + policy AdminOnly).
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bạn không có quyền truy cập.', 'redirect' => route('admin.login')], 401);
            }
            return redirect()->route('admin.login');
        }
        return $next($request);
    }
}
