<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** POST /danh-gia — khách hàng đã đăng nhập viết đánh giá */
    public function store(Request $request)
    {
        $rating = (int) $request->input('rating');
        $comment = is_string($request->input('comment')) ? trim($request->input('comment')) : '';

        $product = Product::where('id', $request->input('product_id'))->where('is_active', true)->first();
        if (! $product) {
            return back()->with('error', 'Sản phẩm không tồn tại.');
        }
        $back = redirect()->to(route('product.show', $product->slug) . '#reviews');

        if ($rating < 1 || $rating > 5) {
            return $back->with('error', 'Đánh giá phải từ 1 đến 5 sao.');
        }
        if ($comment === '') {
            return $back->with('info', 'Vui lòng nhập nội dung đánh giá.');
        }

        Review::create([
            'product_id'  => $product->id,
            'user_id'     => $request->user()->id,
            'user_name'   => $request->user()->full_name ?: 'Khách hàng',
            'rating'      => $rating,
            'comment'     => $comment,
            'is_approved' => true,
        ]);

        return $back->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
