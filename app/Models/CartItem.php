<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['cart_id', 'product_id', 'quantity', 'price'];

    protected $casts = [
        'quantity' => 'integer',
        'price'    => 'float',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Thành tiền theo giá hiện tại của sản phẩm (giống CartItemDto.Subtotal) */
    public function getSubtotalAttribute()
    {
        return $this->product->effective_price * $this->quantity;
    }
}
