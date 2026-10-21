<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Voucher;
use App\Services\OrderMailer;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->user()->getOrCreateCart();
        return view('shop.checkout', [
            'cart'           => $cart->toCartArray(),
            'user'           => $request->user(),
            'vnpayAvailable' => app(VnpayService::class)->isConfigured(),
        ]);
    }

    /** POST /thanh-toan/ma-giam-gia {code, orderAmount} — kiểm tra mã giảm giá */
    public function validateVoucher(Request $request)
    {
        $code = is_string($request->input('code')) ? strtoupper(trim($request->input('code'))) : '';
        $orderAmount = (float) $request->input('orderAmount', 0);
        $voucher = Voucher::findByCode($code);
        $result = Voucher::evaluate($voucher, $orderAmount);

        if (! $result['valid']) {
            return response()->json(['success' => false, 'message' => $result['message']]);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => [
                'valid'          => true,
                'code'           => $voucher->code,
                'discountType'   => $voucher->discount_type,
                'discountAmount' => $voucher->discount_amount,
                'discountValue'  => $result['discount'],
                'finalAmount'    => $orderAmount - $result['discount'],
            ],
        ]);
    }

    /** POST /thanh-toan — tạo đơn hàng từ giỏ (port OrderController.CreateOrder) */
    public function store(Request $request, VnpayService $vnpay)
    {
        $request->validate([
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:30',
            'shipping_address' => 'required|string',
            'note'             => 'nullable|string',
            'payment_method'   => 'required|in:cod,bank,vnpay',
            'voucher_code'     => 'nullable|string|max:50',
        ], [
            'full_name.required'        => 'Vui lòng điền đầy đủ thông tin giao hàng.',
            'phone.required'            => 'Vui lòng điền đầy đủ thông tin giao hàng.',
            'shipping_address.required' => 'Vui lòng điền đầy đủ thông tin giao hàng.',
        ]);

        $user = $request->user();

        if ($request->payment_method === 'vnpay' && ! $vnpay->isConfigured()) {
            return back()->withInput()->with('error', 'Cổng thanh toán VNPay chưa được cấu hình.');
        }

        try {
            $order = DB::transaction(function () use ($request, $user) {
                $cart = $user->getOrCreateCart();
                $items = $cart->items()->with(['product' => function ($q) {
                    $q->lockForUpdate();
                }])->get();

                if ($items->isEmpty()) {
                    throw new CheckoutException('Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi đặt hàng.', 'empty');
                }

                foreach ($items as $item) {
                    if ($item->product->stock_quantity < $item->quantity) {
                        throw new CheckoutException("Sản phẩm '{$item->product->name}' không đủ hàng. Còn lại: {$item->product->stock_quantity}.");
                    }
                }

                $subTotal = $items->sum(function ($ci) {
                    return $ci->product->effective_price * $ci->quantity;
                });

                // Áp dụng voucher (tùy chọn)
                $discount = 0;
                $appliedCode = null;
                if (trim((string) $request->voucher_code) !== '') {
                    $voucher = Voucher::findByCode($request->voucher_code);
                    $eval = Voucher::evaluate($voucher, $subTotal);
                    if (! $eval['valid']) {
                        throw new CheckoutException($eval['message']);
                    }
                    $discount = $eval['discount'];
                    $appliedCode = $voucher->code;
                }

                $order = Order::create([
                    'user_id'          => $user->id,
                    'order_code'       => 'TMP' . uniqid('', true), // mã tạm, đổi thành DHyyMMddNNN theo id bên dưới
                    'full_name'        => trim($request->full_name),
                    'phone'            => trim($request->phone),
                    'shipping_address' => trim($request->shipping_address),
                    'note'             => $request->note ? trim($request->note) : null,
                    'payment_method'   => $request->payment_method,
                    'payment_status'   => 'unpaid',
                    'status'           => 'pending',
                    'sub_total'        => $subTotal,
                    'discount_amount'  => $discount,
                    'voucher_code'     => $appliedCode,
                    'total_amount'     => $subTotal - $discount,
                ]);
                // Sinh mã đơn từ id thật => không trùng khi nhiều khách đặt cùng lúc
                $order->order_code = Order::codeForId($order->id);
                $order->save();

                foreach ($items as $item) {
                    $order->items()->create([
                        'product_id'    => $item->product_id,
                        'product_name'  => $item->product->name,
                        'product_image' => $item->product->image_url,
                        'quantity'      => $item->quantity,
                        'price'         => $item->product->effective_price,
                        'unit'          => $item->product->unit,
                    ]);
                    $item->product->decrement('stock_quantity', $item->quantity);
                }

                $cart->items()->delete();
                $cart->touch();

                return $order;
            });
        } catch (CheckoutException $e) {
            if ($e->type === 'empty') {
                return redirect()->route('cart')->with('error', $e->getMessage());
            }
            return back()->withInput()->with('error', $e->getMessage());
        }

        if ($order->payment_method === 'vnpay') {
            // Đơn VNPay: chỉ gửi email khi thanh toán thành công (xem VnpayController)
            return redirect()->away($vnpay->createPaymentUrl($order, $request->ip()));
        }

        // Email xác nhận đơn hàng COD / chuyển khoản (lỗi mail không ảnh hưởng đơn hàng)
        OrderMailer::sendConfirmation($order, $user->email);

        return redirect()->route('account.orders.show', $order->id)->with('success', 'Đặt hàng thành công!');
    }
}

class CheckoutException extends \RuntimeException
{
    public $type;

    public function __construct($message, $type = 'error')
    {
        parent::__construct($message);
        $this->type = $type;
    }
}
