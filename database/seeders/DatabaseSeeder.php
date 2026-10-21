<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Nạp dữ liệu mẫu (chuyển từ database PostgreSQL của bản .NET).
     *  - Admin:      admin@seafood.com / admin123
     *  - Khách hàng: namlh@gmail.com, chipq@gmail.com, ... / Customer@123
     */
    public function run()
    {
        DB::unprepared(file_get_contents(database_path('seeders/sql/seafood_data.sql')));

        // Dữ liệu gốc được xuất ngày 2026-06-21 (bản .NET sinh ngày tương đối theo lúc chạy seeder).
        // Dời toàn bộ mốc thời gian theo ngày hiện tại để voucher còn hạn, dashboard/báo cáo có số liệu.
        $offset = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse('2026-06-21'), false) * -1;
        if ($offset > 0) {
            $shift = function ($table, array $cols) use ($offset) {
                $sets = implode(', ', array_map(function ($c) use ($offset) {
                    return "`$c` = DATE_ADD(`$c`, INTERVAL $offset DAY)";
                }, $cols));
                DB::statement("UPDATE `$table` SET $sets");
            };
            $shift('users', ['created_at', 'updated_at']);
            $shift('products', ['created_at', 'updated_at']);
            $shift('carts', ['created_at', 'updated_at']);
            $shift('orders', ['created_at', 'updated_at']);
            $shift('banners', ['created_at', 'updated_at']);
            $shift('contacts', ['created_at', 'updated_at']);
            $shift('vouchers', ['expiry_date', 'created_at', 'updated_at']);
            $shift('reviews', ['created_at', 'updated_at']);
        }

        // Đảm bảo tài khoản admin luôn đăng nhập được bằng admin123 (giống DbSeeder.SeedAdminAsync)
        $admin = User::firstOrNew(['email' => 'admin@seafood.com']);
        $admin->full_name = $admin->full_name ?: 'Quản Trị Viên';
        $admin->role = 'admin';
        $admin->is_active = true;
        $admin->password = Hash::make('admin123');
        $admin->save();
        $admin->getOrCreateCart();
    }
}
