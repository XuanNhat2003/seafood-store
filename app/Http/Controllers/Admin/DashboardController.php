<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->stats();

        $recentOrders = Order::with('user')->withCount('items')
            ->orderByDesc('created_at')->take(10)->get();

        // Doanh thu 6 tháng gần nhất (tất cả đơn, giống bản .NET)
        $now = now();
        $from = $now->copy()->startOfMonth()->subMonths(5);
        $orders = Order::where('created_at', '>=', $from)->get(['created_at', 'total_amount']);
        $monthly = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = $now->copy()->startOfMonth()->subMonths($i);
            $inMonth = $orders->filter(function ($o) use ($d) {
                return $o->created_at->year === $d->year && $o->created_at->month === $d->month;
            });
            $monthly[] = [
                'month'      => 'T' . $d->month . '/' . $d->year,
                'revenue'    => (float) $inMonth->sum('total_amount'),
                'orderCount' => $inMonth->count(),
            ];
        }

        return view('admin.dashboard', $stats + [
            'recentOrders'   => $recentOrders,
            'monthlyRevenue' => $monthly,
        ]);
    }

    /** GET /admin/quick-stats — số đơn chờ & liên hệ chưa đọc cho header */
    public function quickStats()
    {
        return response()->json([
            'pendingOrders'  => Order::where('status', 'pending')->count(),
            'unreadContacts' => Contact::where('is_read', false)->count(),
        ]);
    }

    private function stats()
    {
        return [
            'totalOrders'    => Order::count(),
            'totalProducts'  => Product::where('is_active', true)->count(),
            'totalUsers'     => User::where('role', 'customer')->count(),
            'totalRevenue'   => (float) Order::where('status', 'delivered')->sum('total_amount'),
            'pendingOrders'  => Order::where('status', 'pending')->count(),
            'unreadContacts' => Contact::where('is_read', false)->count(),
        ];
    }
}
