<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    const PAGE_SIZE = 10;

    /** GET /admin/orders?page=1&status=...&search=... (port AdminOrderController.GetAll) */
    public function index(Request $request)
    {
        // Query string có thể là mảng (?status[]=x) => bỏ qua thay vì lỗi 500 "Array to string conversion"
        $status = is_string($request->query('status')) ? trim($request->query('status')) : '';
        $search = is_string($request->query('search')) ? $request->query('search') : '';

        $query = Order::query()->withCount('items');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $keyword = mb_strtolower(trim($search));
        if ($keyword !== '') {
            // Tìm chuỗi con theo nghĩa đen (như .NET Contains): escape ký tự đại diện % _ của LIKE
            $like = '%' . addcslashes($keyword, '\\%_') . '%';
            $query->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(order_code) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(full_name) LIKE ?', [$like])
                    ->orWhere('phone', 'like', $like);
            });
        }

        $orders = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        // ?page vượt quá số trang (vd. sau khi lọc) => về trang cuối thay vì "Hiển thị 981 - 21 trên 21"
        if ($orders->total() > 0 && $orders->currentPage() > $orders->lastPage()) {
            return redirect($orders->url($orders->lastPage()));
        }

        $pageSize = self::PAGE_SIZE;

        return view('admin.orders', compact('orders', 'status', 'search', 'pageSize'));
    }

    /** GET /admin/orders/{id} → JSON chi tiết (OrderDto) */
    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])->find($id);
        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json($order->toDetailArray());
    }

    /** PUT /admin/orders/{id}/status  body {status} (port AdminOrderController.UpdateStatus) */
    public function updateStatus(Request $request, $id)
    {
        if (! Order::whereKey($id)->exists()) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $rawStatus = $request->input('status', '');
        $newStatus = is_string($rawStatus) ? mb_strtolower(trim($rawStatus)) : '';

        // Khóa dòng đơn hàng trong transaction: kiểm tra chuyển trạng thái + hoàn kho dựa trên
        // trạng thái mới nhất, nên 2 request hủy đồng thời không thể hoàn kho 2 lần.
        $error = null;
        $order = DB::transaction(function () use ($id, $newStatus, &$error) {
            $order = Order::whereKey($id)->lockForUpdate()->first();
            if (! $order) {
                $error = 'Order not found.';
                return null;
            }

            if (! self::isValidTransition($order->status, $newStatus)) {
                $error = "Invalid status transition from '{$order->status}' to '{$newStatus}'.";
                return $order;
            }

            // Hủy đơn chưa bị hủy => hoàn lại tồn kho cho từng sản phẩm
            if ($newStatus === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items()->get() as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock_quantity', (int) $item->quantity);
                    }
                }
            }

            $order->status = $newStatus;

            if ($newStatus === 'delivered') {
                $order->payment_status = 'paid';
                if (! $order->paid_at) {
                    $order->paid_at = now();
                }
            }

            $order->save();

            return $order;
        });

        if ($error !== null) {
            return response()->json(['message' => $error], $order ? 400 : 404);
        }

        $order->load(['items.product', 'user']);

        // Thông báo khách hàng (best-effort, OrderMailer đã try/catch bên trong)
        if ($order->user && ! empty($order->user->email)) {
            OrderMailer::sendStatusUpdate($order, $order->user->email);
        }

        return response()->json(array_merge(
            ['message' => 'Cập nhật trạng thái đơn hàng thành công!'],
            $order->toDetailArray()
        ));
    }

    private static function isValidTransition($current, $next)
    {
        if ($next === null || trim($next) === '') {
            return false;
        }

        // Mọi trạng thái đều có thể chuyển sang "cancelled"
        if ($next === 'cancelled') {
            return true;
        }

        $allowed = [
            'pending'   => 'confirmed',
            'confirmed' => 'shipping',
            'shipping'  => 'delivered',
        ];

        return isset($allowed[$current]) && $allowed[$current] === $next;
    }
}
