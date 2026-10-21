<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Khi admin khóa tài khoản, phiên đăng nhập của người đó bị hủy ngay ở request kế tiếp
 * (tương đương việc thu hồi refresh token ở bản .NET).
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $msg = 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.';
            // Đang ở khu vực quản trị -> về trang đăng nhập quản trị (PrivateRoute → /admin/login)
            $loginRoute = $request->is('admin', 'admin/*') ? 'admin.login' : 'login';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg, 'redirect' => route($loginRoute)], 401);
            }
            return redirect()->route($loginRoute)->with('error', $msg);
        }
        return $next($request);
    }
}
