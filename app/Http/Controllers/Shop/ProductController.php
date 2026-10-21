<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    const PAGE_SIZE = 12;

    /** /san-pham, /danh-muc/{slug} */
    public function index(Request $request, $slug = null)
    {
        $str = function ($v, $default = '') {
            return is_string($v) ? $v : $default; // bỏ qua tham số dạng mảng (?search[]=...)
        };
        $search = trim($str($request->query('search')));
        $sort = $str($request->query('sort'), 'newest');
        $page = max(1, (int) $str($request->query('page'), '1'));

        $activeCategory = null;
        $query = Product::where('is_active', true);

        if ($slug) {
            $activeCategory = Category::where('slug', $slug)->where('is_active', true)->first();
            if ($activeCategory) {
                // Danh mục CHA: gồm cả sản phẩm của danh mục con
                $ids = Category::where('parent_id', $activeCategory->id)->pluck('id')->push($activeCategory->id);
                $query->whereIn('category_id', $ids);
            } else {
                // Danh mục không tồn tại / đã ẩn: không có sản phẩm (giống bản React)
                $query->whereRaw('1 = 0');
            }
        }

        if ($search !== '') {
            // % và _ được tìm đúng ký tự, không phải ký tự đại diện của LIKE
            $kw = addcslashes(mb_strtolower($search), '\\%_');
            $query->where(function ($q) use ($kw) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $kw . '%'])
                  ->orWhereRaw('LOWER(product_code) LIKE ?', ['%' . $kw . '%']);
            });
        }

        $total = (clone $query)->count();
        $items = $query->orderByDesc('created_at')
            ->skip(($page - 1) * self::PAGE_SIZE)->take(self::PAGE_SIZE)->get();

        // Sắp xếp giá trên trang hiện tại (giống bản React)
        if ($sort === 'priceAsc') {
            $items = $items->sortBy('effective_price')->values();
        } elseif ($sort === 'priceDesc') {
            $items = $items->sortByDesc('effective_price')->values();
        }

        $paginator = new LengthAwarePaginator($items, $total, self::PAGE_SIZE, $page, [
            'path'  => $request->url(),
            'query' => $request->except('page'),
        ]);

        $categories = Category::activeFlatWithCounts();

        $pageTitle = 'Tất cả sản phẩm';
        $crumbLabel = 'Cửa hàng';
        if ($activeCategory) {
            $pageTitle = $crumbLabel = $activeCategory->name;
        } elseif ($search !== '') {
            $pageTitle = 'Kết quả tìm kiếm: "' . $search . '"';
            $crumbLabel = 'Tìm kiếm: "' . $search . '"';
        }

        return view('shop.products', [
            'items'      => $paginator,
            'totalCount' => $total,
            'roots'      => $categories->whereNull('parent_id')->values(),
            'slug'       => $slug,
            'sort'       => $sort,
            'pageTitle'  => $pageTitle,
            'crumbLabel' => $crumbLabel,
        ]);
    }

    /** /san-pham/{slug} */
    public function show($slug)
    {
        $product = Product::with(['category', 'orderedImages'])
            ->where('slug', $slug)->where('is_active', true)->first();

        if (! $product) {
            return response()->view('shop.product-not-found', [], 404);
        }

        $reviews = $product->reviews()->where('is_approved', true)->orderByDesc('created_at')->get();
        $avgRating = $reviews->count() ? $reviews->avg('rating') : 0;

        $images = $product->orderedImages;
        $primary = $images->firstWhere('is_primary', true) ?: $images->first();
        $mainImage = $primary ? $primary->image_url : $product->image_url;

        // Sản phẩm tương tự = cùng danh mục CHA (bao gồm các danh mục con anh em)
        $groupId = $product->category && $product->category->parent_id ? $product->category->parent_id : $product->category_id;
        $ids = Category::where('parent_id', $groupId)->pluck('id')->push($groupId);
        $similar = Product::where('is_active', true)->whereIn('category_id', $ids)
            ->orderByDesc('created_at')->take(12)->get()
            ->where('id', '!=', $product->id)->take(5)->values();

        return view('shop.product-detail', compact('product', 'reviews', 'avgRating', 'images', 'mainImage', 'similar'));
    }
}
