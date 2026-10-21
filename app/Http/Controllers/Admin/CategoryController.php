<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** GET /admin/categories — danh sách phẳng sắp theo cây (cha rồi tới các con) */
    public function index()
    {
        $all = Category::with('parent')->withCount('products')->get();

        $sorter = function ($a, $b) {
            if ($a->sort_order !== $b->sort_order) {
                return $a->sort_order <=> $b->sort_order;
            }
            return strcmp($a->name, $b->name);
        };

        $roots = $all->filter(function ($c) { return $c->parent_id === null; })->sort($sorter)->values();

        $ordered = collect();
        $usedIds = [];
        foreach ($roots as $root) {
            $ordered->push($root);
            $usedIds[$root->id] = true;
            $children = $all->filter(function ($c) use ($root) { return $c->parent_id === $root->id; })->sort($sorter);
            foreach ($children as $ch) {
                $ordered->push($ch);
                $usedIds[$ch->id] = true;
            }
        }
        // Thêm các danh mục con "mồ côi" (cha không còn) để không bị mất
        foreach ($all as $c) {
            if ($c->parent_id !== null && ! isset($usedIds[$c->id])) {
                $ordered->push($c);
            }
        }

        $categories = $ordered;
        $parentOptions = $all->filter(function ($c) { return $c->parent_id === null; })->sort($sorter)->values();

        return view('admin.categories', compact('categories', 'parentOptions'));
    }

    /** Kiểm tra dữ liệu (kể cả độ dài theo cột DB để tránh lỗi SQL 500). Trả về thông báo lỗi hoặc null. */
    private function validateData(array $data)
    {
        if ($data['name'] === '') {
            return 'Tên danh mục là bắt buộc!';
        }
        if (mb_strlen($data['name']) > 180) {
            return 'Tên danh mục không được vượt quá 180 ký tự.';
        }
        if ($data['image_url'] !== null && mb_strlen($data['image_url']) > 1000) {
            return 'Đường dẫn ảnh quá dài (tối đa 1000 ký tự).';
        }
        if (abs($data['sort_order']) > 2147483647) {
            return 'Thứ tự hiển thị không hợp lệ.';
        }
        return null;
    }

    /** POST /admin/categories */
    public function store(Request $request)
    {
        $data = $this->payload($request);
        if ($error = $this->validateData($data)) {
            return response()->json(['message' => $error], 400);
        }

        if ($data['parent_id'] !== null && ! Category::where('id', $data['parent_id'])->exists()) {
            return response()->json(['message' => 'Danh mục cha không tồn tại.'], 400);
        }

        $category = new Category($data);
        $category->slug = Category::uniqueSlug($data['name']);
        $category->save();

        return response()->json(['message' => 'Thêm danh mục hải sản thành công!', 'id' => $category->id], 201);
    }

    /** PUT /admin/categories/{id} */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (! $category) {
            return response()->json(['message' => 'Không tìm thấy danh mục.'], 404);
        }

        $data = $this->payload($request);
        if ($error = $this->validateData($data)) {
            return response()->json(['message' => $error], 400);
        }

        // Không thể là chính nó hoặc một danh mục con cháu của nó
        if ($data['parent_id'] !== null) {
            if ($data['parent_id'] === (int) $category->id) {
                return response()->json(['message' => 'Danh mục không thể là cha của chính nó.'], 400);
            }
            if (! Category::where('id', $data['parent_id'])->exists()) {
                return response()->json(['message' => 'Danh mục cha không tồn tại.'], 400);
            }
            if ($this->isDescendant($data['parent_id'], (int) $category->id)) {
                return response()->json(['message' => 'Không thể chọn danh mục con làm danh mục cha.'], 400);
            }
        }

        // Chỉ sinh lại slug khi tên thay đổi (không phân biệt hoa thường)
        if (mb_strtolower($category->name, 'UTF-8') !== mb_strtolower($data['name'], 'UTF-8')) {
            $category->slug = Category::uniqueSlug($data['name'], $category->id);
        }

        $category->fill($data);
        $category->save();

        return response()->json(['message' => 'Cập nhật danh mục thành công!', 'id' => $category->id]);
    }

    /** DELETE /admin/categories/{id} */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (! $category) {
            return response()->json(['message' => 'Không tìm thấy danh mục.'], 404);
        }

        if ($category->children()->exists()) {
            return response()->json(['message' => 'Không thể xóa: danh mục này còn danh mục con.'], 400);
        }
        if ($category->products()->exists()) {
            return response()->json(['message' => 'Không thể xóa: danh mục này còn sản phẩm.'], 400);
        }

        $category->delete();

        return response()->json(['message' => 'Đã ngưng hoạt động danh mục thành công!']);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    /** Đọc payload camelCase giống React (name, description, imageUrl, parentId, sortOrder, isActive) */
    private function payload(Request $request)
    {
        $parentId = $request->input('parentId');
        $isActive = $request->input('isActive', true);
        // Mảng/đối tượng (payload sai) → '' / null thay vì lỗi 500 "Array to string conversion"
        $str = function ($v) { return is_scalar($v) ? (string) $v : null; };
        $description = $str($request->input('description'));
        $imageUrl = $str($request->input('imageUrl'));

        return [
            'name'        => trim((string) $str($request->input('name', ''))),
            'description' => $description,
            'image_url'   => $imageUrl,
            // parentId không phải số (vd. "abc", [1]) → 0 → bị chặn bởi kiểm tra "Danh mục cha không tồn tại."
            'parent_id'   => ($parentId === null || $parentId === '') ? null : (is_numeric($parentId) ? (int) $parentId : 0),
            'sort_order'  => (int) $str($request->input('sortOrder', 0)),
            'is_active'   => filter_var($isActive, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    /** true nếu $candidateParentId là con cháu của $categoryId */
    private function isDescendant($candidateParentId, $categoryId)
    {
        $current = Category::find($candidateParentId);
        $guard = 0;
        while ($current && $current->parent_id !== null && $guard++ < 50) {
            if ((int) $current->parent_id === $categoryId) {
                return true;
            }
            $current = Category::find($current->parent_id);
        }
        return false;
    }
}
