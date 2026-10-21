<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /** GET /admin/reports?startDate=yyyy-MM-dd&endDate=yyyy-MM-dd (port of AdminReportController.GetSalesReport) */
    public function index(Request $request)
    {
        $start = $this->parseDate($request->query('startDate'));
        $end = $this->parseDate($request->query('endDate'));

        // Mặc định 30 ngày gần nhất (tính cả hôm nay)
        $startDateTime = $start ? $start->copy()->startOfDay() : now()->startOfDay()->subDays(29);
        $endDateTime = $end ? $end->copy()->endOfDay() : now()->endOfDay();

        $inRange = function ($q) use ($startDateTime, $endDateTime) {
            return $q->where('created_at', '>=', $startDateTime)->where('created_at', '<=', $endDateTime);
        };

        // Tổng số đơn (tất cả trạng thái)
        $totalOrders = $inRange(Order::query())->count();
        $cancelledOrders = $inRange(Order::query())->where('status', 'cancelled')->count();

        // Chỉ tính doanh thu của các đơn đã giao thành công
        $delivered = $inRange(Order::query())->where('status', 'delivered');
        $totalRevenue = (float) (clone $delivered)->sum('total_amount');
        $deliveredOrders = (clone $delivered)->count();

        // Doanh thu theo ngày (dd/MM/yyyy, tăng dần)
        $dailyRevenue = (clone $delivered)->get(['created_at', 'total_amount'])
            ->groupBy(function ($o) { return $o->created_at->format('Y-m-d'); })
            ->sortKeys()
            ->map(function ($g, $key) {
                return [
                    'date'       => Carbon::createFromFormat('Y-m-d', $key)->format('d/m/Y'),
                    'revenue'    => (float) $g->sum('total_amount'),
                    'orderCount' => $g->count(),
                ];
            })
            ->values()
            ->all();

        // Top 5 sản phẩm bán chạy (theo số lượng) trong các đơn đã giao
        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', $startDateTime)
            ->where('orders.created_at', '<=', $endDateTime)
            ->where('orders.status', 'delivered')
            ->groupBy('order_items.product_id', 'order_items.product_name', 'order_items.unit')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.unit',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get()
            ->map(function ($r) {
                return [
                    'productId'     => (int) $r->product_id,
                    'productName'   => $r->product_name,
                    'unit'          => $r->unit,
                    'totalQuantity' => (int) $r->total_quantity,
                    'totalRevenue'  => (float) $r->total_revenue,
                ];
            })
            ->all();

        // Top 5 khách hàng chi tiêu nhiều nhất trong kỳ
        $topCustomers = (clone $delivered)
            ->groupBy('user_id', 'full_name', 'phone')
            ->select('user_id', 'full_name', 'phone', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->orderByDesc('total_spent')
            ->take(5)
            ->get()
            ->map(function ($r) {
                return [
                    'userId'     => (int) $r->user_id,
                    'fullName'   => $r->full_name,
                    'phone'      => $r->phone,
                    'orderCount' => (int) $r->order_count,
                    'totalSpent' => (float) $r->total_spent,
                ];
            })
            ->all();

        $report = [
            'startDate'       => $startDateTime->format('Y-m-d'),
            'endDate'         => $endDateTime->format('Y-m-d'),
            'totalRevenue'    => $totalRevenue,
            'totalOrders'     => $totalOrders,
            'deliveredOrders' => $deliveredOrders,
            'cancelledOrders' => $cancelledOrders,
            'dailyRevenue'    => $dailyRevenue,
            'topProducts'     => $topProducts,
            'topCustomers'    => $topCustomers,
        ];

        return view('admin.reports', compact('report'));
    }

    /** Parse exact "yyyy-MM-dd", null if missing/invalid (giống DateTime.TryParseExact). */
    private function parseDate($value)
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($value))) {
            return null;
        }
        $d = \DateTime::createFromFormat('!Y-m-d', trim($value));
        $errors = \DateTime::getLastErrors();
        if (! $d || ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return null;
        }
        return Carbon::instance($d);
    }
}
