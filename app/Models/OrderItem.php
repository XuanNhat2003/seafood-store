<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'product_image', 'quantity', 'price', 'unit',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'price'      => 'float',
        'product_id' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    /** Ưu tiên ảnh hiện tại của sản phẩm, fallback ảnh snapshot lúc đặt */
    public function getDisplayImageAttribute()
    {
        if ($this->product && trim((string) $this->product->image_url) !== '') {
            return $this->product->image_url;
        }
        return $this->product_image;
    }
}
