<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    const PAGE_SIZE = 8;
    const FALLBACK_IMAGE = 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=500';
    const MAX_INT = 2147483647;
    const MAX_PRICE = 9999999999999999;

    /** GET /admin/products?page=1&search=...&categoryId=... */
    public function index(Request $request)
    {
        $search = trim($this->str($request->query('search', '')));
        $categoryId = $request->query('categoryId', '');
        $categoryId = is_numeric($categoryId) ? (int) $categoryId : '';

        $query = Product::with('category');

        if ($search !== '') {
            $keyword = '%' . addcslashes(mb_strtolower($search), '%_\\') . '%';
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(name) LIKE ?', [$keyword])
                  ->orWhereRaw('LOWER(product_code) LIKE ?', [$keyword]);
            });
        }

        if ($categoryId !== '') {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(self::PAGE_SIZE)->withQueryString();

        // Trang vượt quá trang cuối (vd. sau khi xóa/lọc) → về trang cuối thay vì hiển thị "Hiển thị 7985 - 41"
        if ($products->isEmpty() && $products->currentPage() > 1 && $products->total() > 0) {
            return redirect()->to($products->url($products->lastPage()));
        }

        $categories = $this->orderedCategories();
        $pageSize = self::PAGE_SIZE;

        return view('admin.products', compact('products', 'categories', 'search', 'categoryId', 'pageSize'));
    }

    /** GET /admin/products/{id} — chi tiết (JSON) cho modal xem/sửa */
    public function show($id)
    {
        $product = Product::with(['category', 'orderedImages'])->find($id);
        if (! $product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        return response()->json($this->toDetailArray($product));
    }

    /** POST /admin/products */
    public function store(Request $request)
    {
        $data = $this->validatedPayload($request);
        if (is_string($data)) {
            return response()->json(['message' => $data], 400);
        }

        $nextId = ((int) Product::max('id')) + 1;
        $productCode = sprintf('SP%03d', $nextId);
        while (Product::where('product_code', $productCode)->exists()) {
            $productCode = sprintf('SP%03d', ++$nextId);
        }

        $product = new Product($data);
        $product->product_code = $productCode;
        $product->slug = Product::uniqueSlug($data['name']);
        $product->save();

        $product->syncPrimaryImage($data['image_url']);
        $product->load(['category', 'orderedImages']);

        return response()->json(array_merge(
            ['message' => 'Thêm sản phẩm thành công!'],
            $this->toDetailArray($product)
        ), 201);
    }

    /** PUT /admin/products/{id} */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        $data = $this->validatedPayload($request);
        if (is_string($data)) {
            return response()->json(['message' => $data], 400);
        }

        // Sinh lại slug nếu tên thay đổi (không phân biệt hoa thường)
        if (mb_strtolower($product->name) !== mb_strtolower($data['name'])) {
            $product->slug = Product::uniqueSlug($data['name'], $product->id);
        }

        $product->fill($data);
        $product->save();

        $product->syncPrimaryImage($data['image_url']);
        $product->load(['category', 'orderedImages']);

        return response()->json(array_merge(
            ['message' => 'Cập nhật sản phẩm thành công!'],
            $this->toDetailArray($product)
        ));
    }

    /** DELETE /admin/products/{id} — ngừng kinh doanh (soft delete) */
    public function deactivate($id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        $product->is_active = false;
        $product->save();

        return response()->json(['message' => 'Đã chuyển sản phẩm sang trạng thái ngừng kinh doanh!']);
    }

    /** DELETE /admin/products/{id}/permanent — chỉ xóa khi chưa phát sinh đơn hàng */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        if (OrderItem::where('product_id', $product->id)->exists()) {
            return response()->json([
                'message' => 'Sản phẩm đã phát sinh đơn hàng nên không thể xóa vĩnh viễn. Hãy dùng chức năng ngừng kinh doanh để giữ lịch sử đơn hàng.',
            ], 400);
        }

        // product_images, reviews, cart_items bị xóa theo (FK cascadeOnDelete)
        $product->delete();

        return response()->json(['message' => 'Đã xóa vĩnh viễn sản phẩm!']);
    }

    /** POST /admin/products/{id}/images {imageUrl, isPrimary, sortOrder} */
    public function addImage(Request $request, $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        $imageUrl = trim($this->str($request->input('imageUrl', '')));
        if ($imageUrl === '') {
            return response()->json(['message' => 'Vui lòng chọn hoặc nhập đường dẫn ảnh!'], 400);
        }
        if (mb_strlen($imageUrl) > 1000) {
            return response()->json(['message' => 'Đường dẫn ảnh quá dài (tối đa 1000 ký tự).'], 400);
        }

        $isPrimary = filter_var($request->input('isPrimary', false), FILTER_VALIDATE_BOOLEAN);
        $sortOrder = max(-self::MAX_INT, min(self::MAX_INT, (int) $this->str($request->input('sortOrder', 0))));

        if ($isPrimary) {
            ProductImage::where('product_id', $product->id)->where('is_primary', true)
                ->update(['is_primary' => false]);
            $product->image_url = $imageUrl;
            $product->save();
        }

        $image = ProductImage::create([
            'product_id' => $product->id,
            'image_url'  => $imageUrl,
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder,
        ]);

        return response()->json(array_merge(
            ['message' => 'Đã thêm ảnh vào thư viện!', 'productId' => $product->id],
            $this->imageArray($image)
        ));
    }

    /** DELETE /admin/products/{id}/images/{imageId} */
    public function deleteImage($id, $imageId)
    {
        $image = ProductImage::where('id', $imageId)->where('product_id', $id)->first();
        if (! $image) {
            return response()->json(['message' => 'Không tìm thấy ảnh sản phẩm.'], 404);
        }

        $image->delete();

        return response()->json(['message' => 'Đã xóa ảnh khỏi thư viện!']);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    /** Ép giá trị đầu vào về chuỗi; mảng/đối tượng (vd. ?search[]=x) → '' thay vì lỗi 500 "Array to string conversion" */
    private function str($value)
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * Danh mục (kể cả không hoạt động) giống GET /api/admin/categories:
     * danh mục gốc theo sort_order/name, mỗi danh mục gốc theo sau là các con.
     */
    private function orderedCategories()
    {
        $all = Category::orderBy('sort_order')->orderBy('name')->get();
        $ordered = collect();
        foreach ($all->whereNull('parent_id') as $root) {
            $ordered->push($root);
            foreach ($all->where('parent_id', $root->id) as $child) {
                $ordered->push($child);
            }
        }
        // Danh mục con mồ côi (cha không tồn tại)
        foreach ($all as $c) {
            if ($c->parent_id !== null && ! $ordered->contains('id', $c->id)) {
                $ordered->push($c);
            }
        }
        return $ordered;
    }

    /**
     * Đọc + kiểm tra body JSON (camelCase như React). Trả về mảng cột snake_case
     * hoặc chuỗi thông báo lỗi.
     */
    private function validatedPayload(Request $request)
    {
        $name = trim($this->str($request->input('name', '')));
        $categoryId = $request->input('categoryId');
        $price = $request->input('price');
        $salePrice = $request->input('salePrice');
        $stock = $request->input('stockQuantity');

        if ($name === '' || $categoryId === null || $categoryId === '' || $price === null || $price === ''
            || $stock === null || $stock === '') {
            return 'Vui lòng nhập đầy đủ các trường bắt buộc!';
        }
        if (! is_numeric($categoryId) || ! Category::where('id', (int) $categoryId)->exists()) {
            return 'Danh mục không tồn tại.';
        }
        if (! is_numeric($price) || $price < 0) {
            return 'Giá bán không hợp lệ.';
        }
        if ($salePrice !== null && $salePrice !== '' && (! is_numeric($salePrice) || $salePrice < 0)) {
            return 'Giá khuyến mãi không hợp lệ.';
        }
        if (! is_numeric($stock) || (int) $stock < 0 || $stock > self::MAX_INT) {
            return 'Số lượng tồn kho không hợp lệ.';
        }
        // Giới hạn theo cột DB (tránh lỗi SQL 500): decimal(18,2), varchar(191/50/1000)
        if ($price > self::MAX_PRICE) {
            return 'Giá bán không hợp lệ.';
        }
        if ($salePrice !== null && $salePrice !== '' && $salePrice > self::MAX_PRICE) {
            return 'Giá khuyến mãi không hợp lệ.';
        }

        $unit = trim($this->str($request->input('unit', '')));
        $description = $request->input('description');
        $description = is_scalar($description) ? (string) $description : null;
        $imageUrl = trim($this->str($request->input('imageUrl', '')));

        if (mb_strlen($name) > 180) {
            return 'Tên sản phẩm không được vượt quá 180 ký tự.';
        }
        if (mb_strlen($unit) > 50) {
            return 'Đơn vị tính không được vượt quá 50 ký tự.';
        }
        if (mb_strlen($imageUrl) > 1000) {
            return 'Đường dẫn ảnh quá dài (tối đa 1000 ký tự).';
        }

        return [
            'category_id'    => (int) $categoryId,
            'name'           => $name,
            'description'    => ($description === null || $description === '') ? null : (string) $description,
            'price'          => (float) $price,
            'sale_price'     => ($salePrice === null || $salePrice === '') ? null : (float) $salePrice,
            'stock_quantity' => (int) $stock,
            'unit'           => $unit !== '' ? $unit : 'kg',
            'image_url'      => $imageUrl !== '' ? $imageUrl : null,
            'is_active'      => filter_var($request->input('isActive', true), FILTER_VALIDATE_BOOLEAN),
            'is_featured'    => filter_var($request->input('isFeatured', false), FILTER_VALIDATE_BOOLEAN),
        ];
    }

    private function imageArray(ProductImage $img)
    {
        return [
            'id'         => $img->id,
            'imageUrl'   => $img->image_url,
            'displayUrl' => img_url($img->image_url),
            'isPrimary'  => (bool) $img->is_primary,
            'sortOrder'  => (int) $img->sort_order,
        ];
    }

    private function toDetailArray(Product $p)
    {
        $images = $p->relationLoaded('orderedImages') ? $p->orderedImages : $p->orderedImages()->get();

        return [
            'id'              => $p->id,
            'categoryId'      => $p->category_id,
            'categoryName'    => $p->category ? $p->category->name : '',
            'productCode'     => $p->product_code,
            'name'            => $p->name,
            'slug'            => $p->slug,
            'description'     => $p->description,
            'price'           => $p->price,
            'salePrice'       => $p->sale_price,
            'stockQuantity'   => $p->stock_quantity,
            'unit'            => $p->unit,
            'imageUrl'        => $p->image_url,
            'displayImageUrl' => img_url($p->image_url),
            'isActive'        => (bool) $p->is_active,
            'isFeatured'      => (bool) $p->is_featured,
            'createdAt'       => format_date($p->created_at),
            'updatedAt'       => format_date($p->updated_at),
            'images'          => $images->map(function ($img) {
                return $this->imageArray($img);
            })->values()->all(),
        ];
    }
}
