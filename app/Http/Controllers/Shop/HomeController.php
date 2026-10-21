<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();

        // Hải sản bán chạy: theo tổng số lượng đã bán (fallback: mới nhất) — GET /api/products/best-sellers
        $sold = OrderItem::selectRaw('product_id, SUM(quantity) as qty')->groupBy('product_id')->pluck('qty', 'product_id');
        $bestSellers = Product::where('is_active', true)->orderByDesc('created_at')->get()
            ->sort(function ($a, $b) use ($sold) {
                $qa = (int) ($sold[$a->id] ?? 0);
                $qb = (int) ($sold[$b->id] ?? 0);
                if ($qa !== $qb) return $qb <=> $qa;
                return $b->created_at <=> $a->created_at;
            })->take(5)->values();

        // Sản phẩm nổi bật — GET /api/products/featured
        $featured = Product::where('is_active', true)->where('is_featured', true)
            ->orderByDesc('created_at')->take(5)->get();

        // Mỗi danh mục gốc: 5 sản phẩm (gồm sản phẩm của danh mục con)
        $roots = Category::where('is_active', true)->whereNull('parent_id')
            ->orderBy('sort_order')->orderBy('name')->get();
        $catSections = [];
        foreach ($roots as $root) {
            $ids = Category::where('parent_id', $root->id)->pluck('id')->push($root->id);
            $items = Product::where('is_active', true)->whereIn('category_id', $ids)
                ->orderByDesc('created_at')->take(5)->get();
            if ($items->isNotEmpty()) {
                $catSections[] = ['root' => $root, 'items' => $items];
            }
        }

        return view('shop.home', [
            'mainBanner'  => $banners->first(),
            'sideBanners' => $banners->slice(1, 2)->values(),
            'bestSellers' => $bestSellers,
            'featured'    => $featured,
            'catSections' => $catSections,
        ]);
    }
}
