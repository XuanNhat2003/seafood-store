<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    const PAGE_SIZE = 10;

    /** GET /admin/users?page=1&search=... (port AdminUserController.GetAll — chỉ khách hàng) */
    public function index(Request $request)
    {
        // ?search[]=x => bỏ qua thay vì lỗi 500 "Array to string conversion"
        $search = is_string($request->query('search')) ? $request->query('search') : '';

        $query = User::query()->where('role', 'customer');

        $keyword = mb_strtolower(trim($search));
        if ($keyword !== '') {
            // "KH001" / "kh1" / "1" => tìm theo id
            $numericKeyword = Str::startsWith($keyword, 'kh') ? substr($keyword, 2) : $keyword;
            $hasUserId = preg_match('/^\s*[+-]?\d{1,9}\s*$/', $numericKeyword) === 1;
            $userId = $hasUserId ? (int) trim($numericKeyword) : null;
            // Tìm chuỗi con theo nghĩa đen (như .NET Contains): escape ký tự đại diện % _ của LIKE
            $like = '%' . addcslashes($keyword, '\\%_') . '%';

            $query->where(function ($q) use ($like, $hasUserId, $userId) {
                $q->whereRaw('LOWER(full_name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$like])
                    ->orWhere(function ($q2) use ($like) {
                        $q2->whereNotNull('phone')->where('phone', 'like', $like);
                    });
                if ($hasUserId) {
                    $q->orWhere('id', $userId);
                }
            });
        }

        // id phụ để phân trang ổn định khi nhiều tài khoản trùng created_at
        $users = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        if ($users->total() > 0 && $users->currentPage() > $users->lastPage()) {
            return redirect($users->url($users->lastPage()));
        }

        $pageSize = self::PAGE_SIZE;

        return view('admin.users', compact('users', 'search', 'pageSize'));
    }

    /**
     * PUT /admin/users/{id}/status — đảo is_active.
     * (Bản .NET thu hồi refresh token khi khóa; ở Laravel middleware tự đăng xuất tài khoản bị khóa.)
     */
    public function toggleStatus($id)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Trang chỉ quản lý khách hàng: không cho khóa tài khoản quản trị (tránh admin tự khóa mình qua API)
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Không thể khóa tài khoản quản trị viên.'], 400);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return response()->json([
            'message'   => $user->is_active ? 'Đã mở khóa tài khoản thành công!' : 'Đã khóa tài khoản thành công!',
            'id'        => $user->id,
            'email'     => $user->email,
            'fullName'  => $user->full_name,
            'phone'     => $user->phone,
            'address'   => $user->address,
            'role'      => $user->role,
            'isActive'  => (bool) $user->is_active,
            'createdAt' => format_date($user->created_at),
        ]);
    }
}
