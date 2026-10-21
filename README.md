# Hải Sản Tươi Ngon — Laravel 8 (XAMPP PHP 7.4)

Bản chuyển đổi của website bán hải sản (trước đây: ASP.NET Core API + PostgreSQL + React) sang
**Laravel 8 + Blade + MySQL**, chạy được trên **XAMPP 7.4.16**. Giao diện, chức năng và nghiệp vụ được giữ như bản cũ,
thêm **gửi email đơn hàng qua SMTP** và **thanh toán online VNPay (sandbox)**.

## 1. Cài đặt

1. Copy thư mục `seafood-store` vào `C:\xampp\htdocs\` (đã có sẵn `vendor/`, không cần `composer install`).
2. Mở XAMPP Control Panel, bật **Apache** và **MySQL**.
3. Tạo database: vào http://localhost/phpmyadmin, tạo database `seafood_store` (utf8mb4_unicode_ci), rồi **một trong hai cách**:
   - Import file `database/seafood_store_mysql.sql`, **hoặc**
   - Chạy lệnh: `C:\xampp\php\php.exe artisan migrate:fresh --seed` (dữ liệu mẫu có ngày được dời theo ngày hiện tại).
4. Kiểm tra file `.env` (DB_*, MAIL_*, VNPAY_*).
5. Mở trình duyệt: **http://localhost/seafood-store/public** (hoặc http://localhost/seafood-store/ sẽ tự chuyển hướng).

## 2. Tài khoản mẫu

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Quản trị | admin@seafood.com | admin123 |
| Khách hàng | namlh@gmail.com, chipq@gmail.com, ... | Customer@123 |

Trang quản trị: http://localhost/seafood-store/public/admin/login

## 3. Chức năng

**Khách hàng**: trang chủ (banner, bán chạy, nổi bật, theo danh mục), cửa hàng + lọc danh mục + tìm kiếm + sắp xếp + phân trang,
chi tiết sản phẩm (gallery, đánh giá, sản phẩm tương tự), giỏ hàng, mã giảm giá, đặt hàng (COD / chuyển khoản / **VNPay**),
tài khoản (hồ sơ, đổi mật khẩu, đơn hàng, theo dõi trạng thái, hủy đơn, **thanh toán lại VNPay**), liên hệ, giới thiệu, tin tức.

**Quản trị**: dashboard + biểu đồ, sản phẩm (ảnh, gallery, ngừng bán/xóa vĩnh viễn), danh mục cha/con, đơn hàng (chuyển trạng thái,
hoàn kho khi hủy, in hóa đơn), kho hàng (nhập kho, cảnh báo tồn thấp), khuyến mãi, đánh giá, báo cáo doanh thu (xuất Excel),
người dùng (khóa/mở khóa), banner, liên hệ.

## 4. Email (Gmail SMTP)

Cấu hình trong `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email-cua-ban@gmail.com
MAIL_PASSWORD=mat-khau-ung-dung-16-ky-tu   # App Password của Google, không phải mật khẩu đăng nhập
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email-cua-ban@gmail.com
```
Email được gửi khi: đặt hàng thành công, admin đổi trạng thái đơn, thanh toán VNPay thành công.
Lỗi gửi mail không làm hỏng đơn hàng (được ghi vào `storage/logs/laravel.log`).
Đặt `MAIL_MAILER=log` để chỉ ghi email ra log khi test.

## 5. VNPay (sandbox)

```
VNPAY_TMN_CODE=...        # mã website do VNPay cấp
VNPAY_HASH_SECRET=...     # chuỗi bí mật
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL="${APP_URL}/thanh-toan/vnpay-return"
VNPAY_IPN_URL="${APP_URL}/api/vnpay-ipn"
```
- Đăng ký tài khoản test: https://sandbox.vnpayment.vn/devreg/
- Thẻ test NCB: `9704198526191432198` — NGUYEN VAN A — `07/15` — OTP `123456`
- Luồng: chọn "Thanh toán online qua VNPay" ở trang thanh toán → đơn được tạo (chưa thanh toán) → chuyển sang VNPay →
  VNPay trả về `/thanh-toan/vnpay-return` → kiểm tra chữ ký, cập nhật "Đã thanh toán". Nếu hủy/thất bại có thể
  "Thanh toán ngay qua VNPay" lại trong chi tiết đơn hàng.
- IPN (`/api/vnpay-ipn`) chỉ hoạt động khi website có URL public (vd. dùng ngrok); ở localhost, kết quả được cập nhật qua return URL.

## 6. Lưu ý vận hành (quan trọng)

- **Cấu hình được cache** (`bootstrap/cache/config.php`) để tránh lỗi ngẫu nhiên "No application encryption key"
  của PHP thread-safe trên XAMPP Windows khi có nhiều request cùng lúc.
  **Mỗi lần sửa `.env` phải chạy lại:** `C:\xampp\php\php.exe artisan config:cache`
- `APP_DEBUG=false` khi bàn giao (trang lỗi không lộ mật khẩu email/VNPay). Khi cần sửa lỗi, đặt `APP_DEBUG=true`
  rồi chạy lại `config:cache`; chi tiết lỗi luôn được ghi ở `storage/logs/laravel.log`.
- Đăng nhập (khách & admin) giới hạn 10 lần/phút mỗi IP để chống dò mật khẩu.
- Không xóa thư mục `public/uploads` (ảnh tải lên từ trang quản trị).

## 7. Cấu trúc chính

```
app/Http/Controllers/Shop/*     # trang khách hàng, giỏ hàng, thanh toán, VNPay
app/Http/Controllers/Admin/*    # trang quản trị
app/Models/*                    # Eloquent models
app/Services/VnpayService.php   # tạo URL / xác thực chữ ký VNPay
app/Services/OrderMailer.php    # gửi email đơn hàng
app/helpers.php                 # format_price, format_date, img_url, icon (Feather) ...
resources/views/layouts         # layout shop & admin
resources/views/shop, admin, emails
public/css, public/js, public/vendor (Bootstrap, Chart.js, Feather — chạy offline)
database/migrations, database/seeders (dữ liệu chuyển từ PostgreSQL)
```
