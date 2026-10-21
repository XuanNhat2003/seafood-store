<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_code', 'full_name', 'phone', 'shipping_address', 'note',
        'payment_method', 'payment_status', 'status', 'sub_total', 'discount_amount',
        'voucher_code', 'total_amount', 'vnpay_txn_ref', 'vnpay_transaction_no',
        'vnpay_bank_code', 'paid_at',
    ];

    protected $casts = [
        'sub_total'       => 'float',
        'discount_amount' => 'float',
        'total_amount'    => 'float',
        'user_id'         => 'integer',
        'paid_at'         => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Có thể thanh toán (lại) qua VNPay: đơn VNPay chưa trả tiền và chưa bị hủy */
    public function getCanPayVnpayAttribute()
    {
        return $this->payment_method === 'vnpay'
            && $this->payment_status !== 'paid'
            && $this->status === 'pending';
    }

    /** Mã đơn DHyyMMddNNN (cùng định dạng OrderController.CreateOrder của bản .NET) */
    public static function codeForId($id)
    {
        return 'DH' . now()->format('ymd') . str_pad($id, 3, '0', STR_PAD_LEFT);
    }

    /** Dữ liệu chi tiết đơn (tương đương OrderDto) cho modal admin */
    public function toDetailArray()
    {
        $this->loadMissing(['items.product', 'user']);
        return [
            'id'              => $this->id,
            'orderCode'       => $this->order_code,
            'fullName'        => $this->full_name,
            'phone'           => $this->phone,
            'shippingAddress' => $this->shipping_address,
            'note'            => $this->note,
            'paymentMethod'   => $this->payment_method,
            'paymentMethodLabel' => payment_method_label($this->payment_method),
            'paymentStatus'   => $this->payment_status,
            'status'          => $this->status,
            'subTotal'        => $this->sub_total,
            'discountAmount'  => $this->discount_amount,
            'voucherCode'     => $this->voucher_code,
            'totalAmount'     => $this->total_amount,
            'vnpayTransactionNo' => $this->vnpay_transaction_no,
            'vnpayBankCode'   => $this->vnpay_bank_code,
            'paidAt'          => $this->paid_at ? format_date($this->paid_at) : null,
            'createdAt'       => format_date($this->created_at),
            'updatedAt'       => format_date($this->updated_at),
            'user'            => $this->user ? [
                'id' => $this->user->id, 'email' => $this->user->email, 'fullName' => $this->user->full_name,
            ] : null,
            'orderItems'      => $this->items->map(function (OrderItem $oi) {
                return [
                    'id'           => $oi->id,
                    'productId'    => $oi->product_id,
                    'productName'  => $oi->product_name,
                    'productImage' => img_url($oi->display_image),
                    'quantity'     => $oi->quantity,
                    'price'        => $oi->price,
                    'unit'         => $oi->unit,
                    'subtotal'     => $oi->subtotal,
                ];
            })->values()->all(),
        ];
    }
}
