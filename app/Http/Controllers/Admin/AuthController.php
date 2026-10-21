<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        if (! $request->email || ! $request->password) {
            return back()->withInput($request->only('email'))->with('warning', 'Vui lòng điền đầy đủ thông tin!');
        }

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))->with('error', 'Email hoặc mật khẩu không đúng');
        }
        if (! $user->is_active) {
            return back()->withInput($request->only('email'))->with('error', 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.');
        }
        if ($user->role !== 'admin') {
            return back()->withInput($request->only('email'))->with('error', 'Tài khoản của bạn không có quyền truy cập trang quản trị!');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập hệ thống quản trị thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
