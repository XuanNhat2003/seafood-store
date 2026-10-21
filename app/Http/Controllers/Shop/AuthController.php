<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Đã đăng nhập bằng tài khoản khách hàng => về trang chủ
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return redirect('/');
        }
        return view('shop.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [], ['email' => 'Email', 'password' => 'Mật khẩu']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withInput($request->only('email'))->with('error', 'Email hoặc mật khẩu không đúng');
        }
        if (! $user->is_active) {
            return back()->withInput($request->only('email'))->with('error', 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.');
        }
        if (! Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))->with('error', 'Email hoặc mật khẩu không đúng');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
        }
        return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
    }

    public function showRegister()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return redirect('/');
        }
        return view('shop.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name'             => 'required|string|max:255',
            'email'                 => 'required|email|max:255',
            'phone'                 => 'nullable|string|max:30',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.min'               => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password_confirmation.same' => 'Mật khẩu xác nhận không khớp',
            'email.email'                => 'Dữ liệu không hợp lệ',
            'required'                   => 'Dữ liệu không hợp lệ',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Email đã tồn tại');
        }

        $user = User::create([
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'full_name' => $request->full_name,
            'phone'     => $request->phone,
            'role'      => 'customer',
            'is_active' => true,
        ]);
        $user->cart()->create();

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Đăng ký thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
