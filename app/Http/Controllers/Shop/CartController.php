<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->user()->getOrCreateCart();
        return view('shop.cart', ['cart' => $cart->toCartArray()]);
    }

    /** POST /gio-hang/items  {productId, quantity} */
    public function add(Request $request)
    {
        $productId = (int) $request->input('productId');
        $quantity = max(1, (int) $request->input('quantity', 1));

        $product = Product::find($productId);
        if (! $product || ! $product->is_active) {
            return response()->json(['message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.'], 400);
        }

        $cart = $request->user()->getOrCreateCart();
        $existing = $cart->items()->where('product_id', $productId)->first();

        // Kiểm tra tồn kho theo TỔNG số lượng mong muốn (đã có trong giỏ + thêm mới)
        $desired = ($existing ? $existing->quantity : 0) + $quantity;
        if ($product->stock_quantity < $desired) {
            return response()->json(['message' => 'Không đủ hàng trong kho. Còn lại: ' . $product->stock_quantity . '.'], 400);
        }

        if ($existing) {
            $existing->quantity += $quantity;
            $existing->save();
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $product->effective_price,
            ]);
        }
        $cart->touch();

        return response()->json($cart->toCartArray());
    }

    /** PUT /gio-hang/items/{item}  {quantity} */
    public function update(Request $request, $itemId)
    {
        $cart = $request->user()->getOrCreateCart();
        $item = $cart->items()->where('id', $itemId)->first();
        if (! $item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm trong giỏ hàng.'], 404);
        }

        $qty = (int) $request->input('quantity');
        if ($qty <= 0) {
            $item->delete();
        } else {
            $item->quantity = $qty;
            $item->save();
        }
        $cart->touch();

        return response()->json($cart->toCartArray());
    }

    public function remove(Request $request, $itemId)
    {
        $cart = $request->user()->getOrCreateCart();
        $item = $cart->items()->where('id', $itemId)->first();
        if (! $item) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm trong giỏ hàng.'], 404);
        }
        $item->delete();
        $cart->touch();

        return response()->json($cart->toCartArray());
    }

    public function clear(Request $request)
    {
        $cart = $request->user()->getOrCreateCart();
        $cart->items()->delete();
        $cart->touch();

        return response()->json($cart->toCartArray());
    }
}
