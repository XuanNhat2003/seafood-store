<?php

/*
|--------------------------------------------------------------------------
| Global view helpers (tương đương utils/format.js của frontend React)
|--------------------------------------------------------------------------
*/

if (! function_exists('format_price')) {
    /** 1377000 => "1.377.000 ₫" (giống Intl.NumberFormat vi-VN currency VND) */
    function format_price($price)
    {
        if ($price === null || $price === '') {
            return '0 ₫';
        }
        return number_format((float) $price, 0, ',', '.') . ' ₫';
    }
}

if (! function_exists('format_date')) {
    /** "21:54 02/04/2026" (giống Intl.DateTimeFormat vi-VN) */
    function format_date($date)
    {
        if (! $date) {
            return '';
        }
        $d = $date instanceof \DateTimeInterface ? \Carbon\Carbon::instance($date) : \Carbon\Carbon::parse($date);
        return $d->format('H:i d/m/Y');
    }
}

if (! function_exists('status_label')) {
    function status_label($status)
    {
        $map = [
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
        ];
        return $map[$status] ?? $status;
    }
}

if (! function_exists('status_color')) {
    function status_color($status)
    {
        $map = [
            'pending'   => 'warning',
            'confirmed' => 'info',
            'shipping'  => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ];
        return $map[$status] ?? 'secondary';
    }
}

if (! function_exists('payment_method_label')) {
    function payment_method_label($method)
    {
        $map = [
            'cod'   => 'Thanh toán khi nhận hàng',
            'bank'  => 'Chuyển khoản ngân hàng',
            'vnpay' => 'Thanh toán online qua VNPay',
        ];
        return $map[$method] ?? ($method ?: '-');
    }
}

if (! function_exists('payment_status_label')) {
    function payment_status_label($status)
    {
        if ($status === 'paid') return 'Đã thanh toán';
        if ($status === 'unpaid') return 'Chưa thanh toán';
        return $status ?: '-';
    }
}

if (! function_exists('img_url')) {
    /**
     * Ảnh lưu trong DB dạng đường dẫn tương đối (images/local/x.jpg, uploads/x.png)
     * hoặc URL tuyệt đối (https://...). Trả về URL hiển thị được.
     */
    function img_url($path, $fallback = null)
    {
        if (! $path) {
            return $fallback;
        }
        if (preg_match('#^(https?:)?//#i', $path) || strpos($path, 'data:') === 0) {
            return $path;
        }
        return asset(ltrim($path, '/'));
    }
}

if (! function_exists('link_url')) {
    /** Link banner: "/danh-muc/abc" => url('/danh-muc/abc'); URL tuyệt đối giữ nguyên. */
    function link_url($link)
    {
        if (! $link) {
            return '#';
        }
        if (preg_match('#^(https?:)?//#i', $link) || $link[0] === '#') {
            return $link;
        }
        return url($link);
    }
}

if (! function_exists('icon')) {
    /**
     * Render Feather icon (bộ icon "Fi" của react-icons) dưới dạng SVG inline.
     * icon('shopping-cart'), icon('star', 16, 'empty', 'currentColor')
     */
    function icon($name, $size = null, $class = '', $fill = 'none', $style = '')
    {
        static $icons = null;
        if ($icons === null) {
            $file = public_path('vendor/feather/icons.json');
            $icons = is_file($file) ? json_decode(file_get_contents($file), true) : [];
        }
        $inner = $icons[$name] ?? '';
        $dim = $size ? (is_numeric($size) ? $size . 'px' : $size) : '1em';
        $cls = trim('feather feather-' . $name . ' ' . $class);
        $styleAttr = $style ? ' style="' . e($style) . '"' : '';
        return new \Illuminate\Support\HtmlString(
            '<svg xmlns="http://www.w3.org/2000/svg" width="' . $dim . '" height="' . $dim . '" viewBox="0 0 24 24" fill="' . e($fill) . '" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . e($cls) . '"' . $styleAttr . '>' . $inner . '</svg>'
        );
    }
}

if (! function_exists('make_slug')) {
    /** Port của SlugHelper.GenerateSlug (.NET) */
    function make_slug($text)
    {
        if (trim((string) $text) === '') {
            return '';
        }
        $text = str_replace(['đ', 'Đ'], 'd', $text);
        $text = \Illuminate\Support\Str::ascii($text, 'vi');
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '-', $text);
        $text = preg_replace('/\s/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}
