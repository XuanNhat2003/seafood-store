<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    const ALLOWED = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const ALLOWED_MIMES = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp'];
    const MAX_SIZE = 5 * 1024 * 1024; // 5 MB

    /** POST /admin/upload (multipart/form-data, field "file") => {url, fileName} */
    public function store(Request $request)
    {
        $file = $request->file('file');
        // Tệp vượt upload_max_filesize của PHP: báo đúng lỗi dung lượng thay vì "Chưa chọn tệp ảnh."
        if ($file && in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return response()->json(['message' => 'Ảnh vượt quá dung lượng cho phép (5MB).'], 400);
        }
        if (! $file || ! $file->isValid() || $file->getSize() === 0) {
            return response()->json(['message' => 'Chưa chọn tệp ảnh.'], 400);
        }
        if ($file->getSize() > self::MAX_SIZE) {
            return response()->json(['message' => 'Ảnh vượt quá dung lượng cho phép (5MB).'], 400);
        }
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, self::ALLOWED, true)) {
            return response()->json(['message' => 'Định dạng ảnh không hợp lệ (chỉ chấp nhận jpg, png, gif, webp).'], 400);
        }
        // Kiểm tra nội dung thật của tệp (finfo), không chỉ tin phần mở rộng:
        // tệp văn bản/HTML đổi đuôi thành .png sẽ bị từ chối.
        $mime = (string) $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            return response()->json(['message' => 'Định dạng ảnh không hợp lệ (chỉ chấp nhận jpg, png, gif, webp).'], 400);
        }

        $dir = public_path('uploads');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $fileName = Str::random(32) . '.' . $ext;
        $file->move($dir, $fileName);

        // Lưu đường dẫn tương đối để chạy được ở mọi thư mục cài đặt (vd. /seafood-store/public)
        return response()->json(['url' => 'uploads/' . $fileName, 'fileName' => $fileName]);
    }
}
