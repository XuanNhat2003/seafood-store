<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'description', 'image_url', 'parent_id', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'parent_id'  => 'integer',
        'sort_order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /** Sinh slug duy nhất từ tên (port EnsureUniqueSlugAsync) */
    public static function uniqueSlug($name, $excludeId = null)
    {
        $base = mb_substr(make_slug($name), 0, 180) ?: 'danh-muc';
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
     * Danh sách phẳng các danh mục đang hoạt động (giống GET /api/categories):
     * product_count của danh mục CHA bao gồm sản phẩm của các danh mục con.
     */
    public static function activeFlatWithCounts()
    {
        $cats = static::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        $direct = Product::where('is_active', true)
            ->selectRaw('category_id, COUNT(*) as cnt')
            ->groupBy('category_id')
            ->pluck('cnt', 'category_id');

        foreach ($cats as $c) {
            $total = (int) ($direct[$c->id] ?? 0);
            if ($c->parent_id === null) {
                foreach ($cats as $ch) {
                    if ($ch->parent_id === $c->id) {
                        $total += (int) ($direct[$ch->id] ?? 0);
                    }
                }
            }
            $c->product_count = $total;
            $parent = $cats->firstWhere('id', $c->parent_id);
            $c->parent_name = $parent ? $parent->name : null;
        }

        return $cats;
    }
}
