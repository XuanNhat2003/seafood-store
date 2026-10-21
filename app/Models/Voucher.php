<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'discount_amount', 'discount_type', 'min_order_amount', 'expiry_date', 'is_active',
    ];

    protected $casts = [
        'discount_amount'  => 'float',
        'min_order_amount' => 'float',
        'expiry_date'      => 'datetime',
        'is_active'        => 'boolean',
    ];

    public static function findByCode($code)
    {
        $code = strtoupper(trim((string) $code));
        return static::whereRaw('UPPER(code) = ?', [$code])->first();
    }

    /**
     * Kiểm tra voucher với số tiền đơn hàng (port VoucherHelper.Evaluate).
     * Trả về ['valid' => bool, 'message' => string, 'discount' => float]
     */
    public static function evaluate($voucher, $orderAmount)
    {
        if (! $voucher) {
            return ['valid' => false, 'message' => 'Mã giảm giá không tồn tại.', 'discount' => 0];
        }
        if (! $voucher->is_active) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã ngừng hoạt động.', 'discount' => 0];
        }
        if ($voucher->expiry_date->lt(now())) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết hạn.', 'discount' => 0];
        }
        if ($orderAmount < $voucher->min_order_amount) {
            return [
                'valid'    => false,
                'message'  => 'Đơn hàng tối thiểu ' . number_format($voucher->min_order_amount, 0, ',', '.') . 'đ để dùng mã này.',
                'discount' => 0,
            ];
        }

        $discount = $voucher->discount_type === 'percentage'
            ? round($orderAmount * $voucher->discount_amount / 100, 0, PHP_ROUND_HALF_EVEN) // giống Math.Round của .NET
            : $voucher->discount_amount;

        if ($discount > $orderAmount) {
            $discount = $orderAmount;
        }

        return ['valid' => true, 'message' => 'Áp dụng mã giảm giá thành công.', 'discount' => (float) $discount];
    }
}
