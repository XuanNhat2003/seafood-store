<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** GET /admin/reviews?page=1&rating=&approved=true|false&search= (port of AdminReviewController.GetAll) */
    public function index(Request $request)
    {
        $pageSize = 10;
        $search = trim((string) $request->query('search', ''));
        $rating = (string) $request->query('rating', '');
        $approved = strtolower((string) $request->query('approved', ''));

        $query = Review::query()->with('product:id,name,product_code');

        if ($rating !== '' && ctype_digit($rating)) {
            $query->where('rating', (int) $rating);
        } else {
            $rating = '';
        }

        if ($approved === 'true' || $approved === 'false') {
            $query->where('is_approved', $approved === 'true');
        } else {
            $approved = '';
        }

        if ($search !== '') {
            // Escape ký tự đại diện của LIKE để "%"/"_" được tìm đúng nghĩa đen (như Contains của EF)
            $keyword = '%' . addcslashes(mb_strtolower($search, 'UTF-8'), '\\%_') . '%';
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(user_name) LIKE ?', [$keyword])
                  ->orWhereHas('product', function ($p) use ($keyword) {
                      $p->whereRaw('LOWER(name) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(product_code) LIKE ?', [$keyword]);
                  });
            });
        }

        $reviews = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate($pageSize)->withQueryString();

        // Trang vượt quá trang cuối (vd. vừa xóa đánh giá duy nhất ở trang cuối rồi tải lại) -> về trang cuối
        if ($reviews->total() > 0 && $reviews->currentPage() > $reviews->lastPage()) {
            return redirect()->to($reviews->url($reviews->lastPage()));
        }

        return view('admin.reviews', compact('reviews', 'search', 'rating', 'approved', 'pageSize'));
    }

    /** PUT /admin/reviews/{id}/approval  body: { isApproved: bool } */
    public function setApproval(Request $request, $id)
    {
        $review = Review::find($id);
        if (! $review) {
            return response()->json(['message' => 'Không tìm thấy đánh giá.'], 404);
        }

        $review->is_approved = $request->boolean('isApproved');
        $review->save();

        return response()->json([
            'message'    => $review->is_approved ? 'Đã duyệt đánh giá!' : 'Đã ẩn đánh giá!',
            'id'         => $review->id,
            'isApproved' => $review->is_approved,
        ]);
    }

    /** DELETE /admin/reviews/{id} */
    public function destroy($id)
    {
        $review = Review::find($id);
        if (! $review) {
            return response()->json(['message' => 'Không tìm thấy đánh giá.'], 404);
        }

        $review->delete();

        return response()->json(['message' => 'Đã xóa đánh giá thành công!']);
    }
}
