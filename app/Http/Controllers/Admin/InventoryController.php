<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    const LOW_STOCK_THRESHOLD = 5;

    /** GET /admin/inventory (port AdminInventoryController.GetInventoryList; lọc/tìm kiếm làm ở client như React) */
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->orderBy('stock_quantity')
            ->orderBy('id')
            ->get();

        $items = $products->map(function ($p) {
            return $this->toItem($p);
        });

        $totalCount = $items->count();
        $outOfStockCount = $items->filter(function ($i) { return $i['stockQuantity'] === 0; })->count();
        $lowStockCount = $items->filter(function ($i) { return $i['stockQuantity'] > 0 && $i['isLowStock']; })->count();
        $normalStockCount = $items->filter(function ($i) { return $i['stockQuantity'] > self::LOW_STOCK_THRESHOLD; })->count();

        $categories = $this->orderedCategories();

        return view('admin.inventory', compact(
            'items', 'categories', 'totalCount', 'outOfStockCount', 'lowStockCount', 'normalStockCount'
        ));
    }

    /** POST /admin/inventory/import  body {productId, quantity} */
    public function import(Request $request)
    {
        $rawQty = $request->input('quantity', 0);
        $rawId = $request->input('productId', 0);
        // Chỉ nhận giá trị số (mảng/chuỗi lạ → 0) để tránh (int)[5] === 1 hoặc lỗi 500
        $quantity = is_numeric($rawQty) ? (int) $rawQty : 0;
        if ($quantity <= 0) {
            return response()->json(['message' => 'Số lượng nhập hàng phải lớn hơn 0.'], 400);
        }

        $product = Product::with('category')->find(is_numeric($rawId) ? (int) $rawId : 0);
        if (! $product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        // Cột stock_quantity là INT: chặn tràn số (tránh lỗi SQL 500)
        if ((int) $product->stock_quantity + $quantity > 2147483647) {
            return response()->json(['message' => 'Số lượng tồn kho sau khi nhập vượt quá giới hạn cho phép.'], 400);
        }

        $product->stock_quantity = (int) $product->stock_quantity + $quantity;
        $product->updated_at = now();
        $product->save();

        $item = $this->toItem($product);
        $item['message'] = 'Đã nhập thêm ' . $quantity . ' ' . $product->unit . ' cho sản phẩm "' . $product->name . '" thành công!';

        return response()->json($item);
    }

    /** InventoryItemDto */
    private function toItem(Product $p)
    {
        $stock = (int) $p->stock_quantity;

        return [
            'id'            => $p->id,
            'productCode'   => $p->product_code,
            'productName'   => $p->name,
            'categoryName'  => $p->category ? $p->category->name : '',
            'imageUrl'      => $p->image_url,
            'price'         => (float) $p->price,
            'stockQuantity' => $stock,
            'unit'          => $p->unit,
            'isActive'      => (bool) $p->is_active,
            'isLowStock'    => $stock <= self::LOW_STOCK_THRESHOLD,
            'updatedAt'     => $p->updated_at,
        ];
    }

    /** Thứ tự như AdminCategoryController.GetAll: gốc theo sort_order/name, mỗi gốc theo sau là các con; cuối cùng là con mồ côi. */
    private function orderedCategories()
    {
        $all = Category::all();
        $roots = $all->whereNull('parent_id')->sortBy(function ($c) {
            return [$c->sort_order, $c->name];
        });

        $ordered = collect();
        foreach ($roots as $root) {
            $ordered->push($root);
            $children = $all->where('parent_id', $root->id)->sortBy(function ($c) {
                return [$c->sort_order, $c->name];
            });
            foreach ($children as $child) {
                $ordered->push($child);
            }
        }
        $ids = $ordered->pluck('id')->all();
        foreach ($all as $c) {
            if ($c->parent_id !== null && ! in_array($c->id, $ids, true)) {
                $ordered->push($c);
            }
        }

        return $ordered;
    }
}
