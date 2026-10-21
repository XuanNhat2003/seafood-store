<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'product_code', 'name', 'slug', 'description', 'price', 'sale_price',
        'stock_quantity', 'unit', 'image_url', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'price'          => 'float',
        'sale_price'     => 'float',
        'stock_quantity' => 'integer',
        'category_id'    => 'integer',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /** Ảnh: chính trước, rồi theo sort_order, id */
    public function orderedImages()
    {
        return $this->hasMany(ProductImage::class)
            ->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getHasSaleAttribute()
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /** Giá thực tế: giá khuyến mãi nếu có và nhỏ hơn giá gốc */
    public function getEffectivePriceAttribute()
    {
        return $this->has_sale ? $this->sale_price : $this->price;
    }

    public function getDiscountPercentAttribute()
    {
        return $this->has_sale && $this->price > 0 ? (int) round((1 - $this->sale_price / $this->price) * 100) : 0;
    }

    public static function uniqueSlug($name, $excludeId = null)
    {
        $base = mb_substr(make_slug($name), 0, 180) ?: 'san-pham';
        $slug = $base;
        $suffix = 2;
        while (static::where('slug', $slug)->when($excludeId, function ($q) use ($excludeId) {
            $q->where('id', '!=', $excludeId);
        })->exists()) {
            $slug = $base . '-' . $suffix++;
        }
        return $slug;
    }

    /**
     * Đồng bộ ảnh chính vào bảng product_images (port SyncPrimaryImage).
     */
    public function syncPrimaryImage($imageUrl)
    {
        if (trim((string) $imageUrl) === '') {
            return;
        }
        $url = trim($imageUrl);
        $primary = $this->orderedImages()->first();

        if (! $primary) {
            $this->images()->create(['image_url' => $url, 'is_primary' => true, 'sort_order' => 1]);
            return;
        }

        $primary->image_url = $url;
        $primary->is_primary = true;
        if ($primary->sort_order <= 0) {
            $primary->sort_order = 1;
        }
        $primary->save();

        $this->images()->where('id', '!=', $primary->id)->update(['is_primary' => false]);
    }
}
