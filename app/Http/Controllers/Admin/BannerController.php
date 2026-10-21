<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /** GET /admin/banners */
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();

        return view('admin.banners', compact('banners'));
    }

    /** POST /admin/banners */
    public function store(Request $request)
    {
        $data = $this->payload($request);
        if ($error = $this->validatePayload($data)) {
            return response()->json(['message' => $error], 400);
        }

        $banner = Banner::create($data);

        return response()->json(['message' => 'Thêm banner quảng cáo thành công!', 'id' => $banner->id], 201);
    }

    /** PUT /admin/banners/{id} */
    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);
        if (! $banner) {
            return response()->json(['message' => 'Không tìm thấy banner.'], 404);
        }

        $data = $this->payload($request);
        if ($error = $this->validatePayload($data)) {
            return response()->json(['message' => $error], 400);
        }

        $banner->fill($data);
        $banner->save();

        return response()->json(['message' => 'Cập nhật thông tin banner thành công!', 'id' => $banner->id]);
    }

    /** DELETE /admin/banners/{id} */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        if (! $banner) {
            return response()->json(['message' => 'Không tìm thấy banner.'], 404);
        }

        $banner->delete();

        return response()->json(['message' => 'Đã xóa banner thành công!']);
    }

    /**
     * Trả về thông báo lỗi (string) hoặc null. Giới hạn độ dài theo cột DB
     * (title varchar(191), image_url/link varchar(1000)) để không ném lỗi SQL 500.
     */
    private function validatePayload(array $data)
    {
        if ($data['title'] === '' || $data['image_url'] === '') {
            return 'Tiêu đề và đường dẫn ảnh là bắt buộc!';
        }
        if (mb_strlen($data['title'], 'UTF-8') > 191) {
            return 'Tiêu đề banner tối đa 191 ký tự.';
        }
        if (mb_strlen($data['image_url'], 'UTF-8') > 1000) {
            return 'Đường dẫn ảnh quá dài (tối đa 1000 ký tự).';
        }
        if ($data['link'] !== null && mb_strlen($data['link'], 'UTF-8') > 1000) {
            return 'Đường dẫn liên kết quá dài (tối đa 1000 ký tự).';
        }
        if ($data['description'] !== null && strlen($data['description']) > 65535) { // cột TEXT
            return 'Mô tả quá dài.';
        }
        return null;
    }

    /** Payload camelCase giống React (title, imageUrl, link, description, sortOrder, isActive) */
    private function payload(Request $request)
    {
        return [
            'title'       => trim((string) $request->input('title', '')),
            'image_url'   => trim((string) $request->input('imageUrl', '')),
            'link'        => $request->input('link') !== null ? (string) $request->input('link') : null,
            'description' => $request->input('description') !== null ? (string) $request->input('description') : null,
            'sort_order'  => (int) $request->input('sortOrder', 0),
            'is_active'   => filter_var($request->input('isActive', true), FILTER_VALIDATE_BOOLEAN),
        ];
    }
}
