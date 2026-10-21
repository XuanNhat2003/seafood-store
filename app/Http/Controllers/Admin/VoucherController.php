<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /** GET /admin/vouchers (port AdminVoucherController.GetAll + thống kê client-side của Vouchers.jsx) */
    public function index(Request $request)
    {
        $vouchers = Voucher::orderByDesc('created_at')->orderByDesc('id')->get();
        $now = now();

        $totalCount = $vouchers->count();
        $activeCount = $vouchers->filter(function ($v) use ($now) {
            return $v->is_active && $v->expiry_date->gt($now);
        })->count();
        $expiredCount = $vouchers->filter(function ($v) use ($now) {
            return $v->expiry_date->lt($now);
        })->count();

        return view('admin.vouchers', compact('vouchers', 'now', 'totalCount', 'activeCount', 'expiredCount'));
    }

    /** POST /admin/vouchers */
    public function store(Request $request)
    {
        $code = trim((string) $request->input('code', ''));
        if ($code === '') {
            return response()->json(['message' => 'Mã voucher không được để trống.'], 400);
        }
        $codeUpper = mb_strtoupper($code, 'UTF-8');
        if (mb_strlen($codeUpper, 'UTF-8') > 50) {
            return response()->json(['message' => 'Mã voucher tối đa 50 ký tự.'], 400);
        }
        if (Voucher::where('code', $codeUpper)->exists()) {
            return response()->json(['message' => 'Mã voucher này đã tồn tại.'], 400);
        }

        $data = $this->payload($request);
        if (is_string($data)) {
            return response()->json(['message' => $data], 400);
        }

        $voucher = new Voucher($data);
        $voucher->code = $codeUpper;
        $voucher->save();

        return response()->json(['message' => 'Thêm mã khuyến mãi thành công!', 'voucher' => $this->toDto($voucher)], 201);
    }

    /** PUT /admin/vouchers/{id} */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::find($id);
        if (! $voucher) {
            return response()->json(['message' => 'Không tìm thấy voucher.'], 404);
        }

        $code = trim((string) $request->input('code', ''));
        if ($code === '') {
            return response()->json(['message' => 'Mã voucher không được để trống.'], 400);
        }
        $codeUpper = mb_strtoupper($code, 'UTF-8');
        if (mb_strlen($codeUpper, 'UTF-8') > 50) {
            return response()->json(['message' => 'Mã voucher tối đa 50 ký tự.'], 400);
        }
        if (Voucher::where('code', $codeUpper)->where('id', '!=', $voucher->id)->exists()) {
            return response()->json(['message' => 'Mã voucher này đã tồn tại trên một voucher khác.'], 400);
        }

        $data = $this->payload($request);
        if (is_string($data)) {
            return response()->json(['message' => $data], 400);
        }

        $voucher->fill($data);
        $voucher->code = $codeUpper;
        $voucher->save();

        return response()->json(['message' => 'Cập nhật mã khuyến mãi thành công!', 'voucher' => $this->toDto($voucher)]);
    }

    /** DELETE /admin/vouchers/{id} */
    public function destroy($id)
    {
        $voucher = Voucher::find($id);
        if (! $voucher) {
            return response()->json(['message' => 'Không tìm thấy voucher.'], 404);
        }
        $code = $voucher->code;
        $voucher->delete();

        return response()->json(['message' => 'Đã xóa mã khuyến mãi "' . $code . '" thành công!']);
    }

    /**
     * Đọc các trường còn lại của CreateVoucherDto. Trả về mảng dữ liệu hoặc chuỗi lỗi.
     */
    private function payload(Request $request)
    {
        $type = $request->input('discountType', 'fixed');
        if ($type !== 'percentage') {
            $type = 'fixed';
        }

        // Ràng buộc giống validate phía client của Vouchers.jsx (chặn cả khi gọi API trực tiếp)
        $amount = (float) $request->input('discountAmount', 0);
        if (! is_numeric($request->input('discountAmount')) || $amount <= 0) {
            return $type === 'percentage' ? 'Phần trăm giảm phải lớn hơn 0!' : 'Số tiền giảm phải lớn hơn 0!';
        }
        if ($type === 'percentage' && $amount > 100) {
            return 'Phần trăm giảm tối đa là 100%!';
        }
        $minOrder = (float) ($request->input('minOrderAmount') ?: 0);
        if ($minOrder < 0) {
            $minOrder = 0;
        }
        // Cột decimal(18,2): tránh lỗi SQL "out of range"
        if ($amount >= 1e16 || $minOrder >= 1e16) {
            return 'Giá trị quá lớn.';
        }

        $expiry = $this->parseExpiry($request->input('expiryDate'));
        if (! $expiry) {
            return 'Vui lòng chọn ngày hết hạn!';
        }

        return [
            'discount_amount'  => $amount,
            'discount_type'    => $type,
            'min_order_amount' => $minOrder,
            'expiry_date'      => $expiry,
            'is_active'        => filter_var($request->input('isActive', true), FILTER_VALIDATE_BOOLEAN),
        ];
    }

    /**
     * React gửi `new Date('YYYY-MM-DD').toISOString()` (= 00:00 UTC của ngày đã chọn) và .NET lưu nguyên thời điểm UTC đó.
     * Ở đây: chuỗi 'YYYY-MM-DD' (input type=date) => 00:00 UTC ngày đó; chuỗi ISO đầy đủ => giữ nguyên thời điểm.
     * Sau đó quy đổi sang giờ địa phương (Asia/Ho_Chi_Minh) để lưu vào cột DATETIME.
     */
    private function parseExpiry($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $value . ' 00:00:00', 'UTC');
            } else {
                $dt = Carbon::parse($value, config('app.timezone'));
            }
        } catch (\Exception $e) {
            return null;
        }

        return $dt->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    private function toDto(Voucher $v)
    {
        return [
            'id'             => $v->id,
            'code'           => $v->code,
            'discountAmount' => (float) $v->discount_amount,
            'discountType'   => $v->discount_type,
            'minOrderAmount' => (float) $v->min_order_amount,
            'expiryDate'     => $v->expiry_date ? $v->expiry_date->toIso8601String() : null,
            'isActive'       => (bool) $v->is_active,
            'createdAt'      => $v->created_at ? $v->created_at->toIso8601String() : null,
        ];
    }
}
