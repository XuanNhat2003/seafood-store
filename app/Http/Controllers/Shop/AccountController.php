<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function profile(Request $request)
    {
        return view('shop.account.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'nullable|string|max:30',
            'address'   => 'nullable|string',
        ], ['full_name.required' => 'Dữ liệu không hợp lệ']);

        $request->user()->update($data);

        return back()->with('success', 'Cập nhật hồ sơ thành công');
    }

    public function password()
    {
        return view('shop.account.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6',
            'confirm_password' => 'required|string',
        ], [
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'required'         => 'Dữ liệu không hợp lệ',
        ]);

        $user = $request->user();
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng');
        }
        if ($request->new_password !== $request->confirm_password) {
            return back()->with('error', 'Mật khẩu xác nhận không khớp');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công');
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->withCount('items')->orderByDesc('created_at')->get();
        return view('shop.account.orders', compact('orders'));
    }

    public function orderDetail(Request $request, $id)
    {
        $order = Order::with('items.product')->where('id', $id)->where('user_id', $request->user()->id)->first();
        return view('shop.account.order-detail', compact('order'));
    }

    /** Khách chỉ được hủy đơn đang chờ xác nhận; hoàn lại tồn kho */
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::with('items.product')->where('id', $id)->where('user_id', $request->user()->id)->first();
        if (! $order) {
            return back()->with('error', 'Không tìm thấy đơn hàng');
        }
        if ($order->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xác nhận.');
        }

        $ok = DB::transaction(function () use ($order) {
            // Khóa dòng đơn hàng và kiểm tra lại trạng thái => không hoàn kho 2 lần khi hủy đồng thời
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();
            if (! $locked || $locked->status !== 'pending') {
                return false;
            }
            $locked->status = 'cancelled';
            $locked->save();
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }
            return true;
        });

        if (! $ok) {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xác nhận.');
        }
        return back()->with('success', 'Đã hủy đơn hàng');
    }
}
