<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /** Dữ liệu giỏ hàng dạng mảng (tương đương CartDto) để trả JSON */
    public function toCartArray()
    {
        $items = $this->items()->with('product')->orderBy('id')->get()->map(function (CartItem $ci) {
            return [
                'id'           => $ci->id,
                'productId'    => $ci->product_id,
                'productName'  => $ci->product->name,
                'productImage' => img_url($ci->product->image_url),
                'price'        => $ci->product->price,
                'salePrice'    => $ci->product->sale_price,
                'effectivePrice' => $ci->product->effective_price,
                'quantity'     => $ci->quantity,
                'unit'         => $ci->product->unit,
                'subtotal'     => $ci->subtotal,
            ];
        });

        return [
            'id'          => $this->id,
            'items'       => $items->values()->all(),
            'totalAmount' => $items->sum('subtotal'),
            'totalItems'  => $items->sum('quantity'),
        ];
    }
}
