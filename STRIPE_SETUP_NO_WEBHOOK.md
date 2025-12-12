# Hướng dẫn Setup Stripe Payment (Không cần Webhook)

## Tổng quan
Hệ thống đã được cấu hình để xử lý thanh toán Stripe **đồng bộ** (synchronous) mà không cần webhook. Thanh toán được xử lý ngay sau khi user hoàn tất checkout.

## Ưu điểm của phương pháp này
- ✅ Đơn giản hơn, không cần setup webhook
- ✅ Không cần ngrok hoặc Stripe CLI
- ✅ Xử lý thanh toán ngay lập tức
- ✅ Phù hợp cho development và production nhỏ

## Nhược điểm
- ⚠️ Nếu user đóng tab trước khi redirect về success page, payment có thể không được cập nhật (nhưng Stripe vẫn đã nhận tiền)
- ⚠️ Không xử lý được các edge cases phức tạp như payment failed sau checkout

## Các bước setup

### 1. Tạo tài khoản Stripe

1. Truy cập https://stripe.com và đăng ký tài khoản
2. Xác minh email và hoàn tất thông tin tài khoản
3. Chọn chế độ **Test Mode** để test

### 2. Lấy API Keys từ Stripe Dashboard

1. Đăng nhập vào Stripe Dashboard: https://dashboard.stripe.com
2. Vào **Developers** > **API keys**
3. Copy các keys sau:
   - **Publishable key** (bắt đầu với `pk_test_` hoặc `pk_live_`)
   - **Secret key** (bắt đầu với `sk_test_` hoặc `sk_live_`)

### 3. Cập nhật file .env

Thêm các dòng sau vào file `.env`:

```env
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
```

**Lưu ý:** 
- Không cần `STRIPE_WEBHOOK_SECRET` vì không sử dụng webhook
- Thay `pk_test_`, `sk_test_` bằng các keys thực tế của bạn
- Với production, sử dụng `pk_live_` và `sk_live_`
- Không commit file `.env` lên Git

### 4. Clear cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 5. Test thanh toán

1. **Test với thẻ thành công:**
   - Số thẻ: `4242 4242 4242 4242`
   - Ngày hết hạn: Bất kỳ ngày trong tương lai (ví dụ: `12/25`)
   - CVC: Bất kỳ 3 số (ví dụ: `123`)
   - ZIP: Bất kỳ 5 số (ví dụ: `12345`)

2. **Test với thẻ thất bại:**
   - Số thẻ: `4000 0000 0000 0002`
   - Các thông tin khác: Tương tự như trên

3. **Test với thẻ yêu cầu 3D Secure:**
   - Số thẻ: `4000 0025 0000 3155`
   - Các thông tin khác: Tương tự như trên

### 6. Kiểm tra kết quả

1. **Phía Customer:**
   - Sau khi thanh toán thành công, sẽ redirect đến trang success
   - Thông báo hiển thị ở góc trên bên phải
   - Booking được cập nhật trong Dashboard

2. **Phía Admin:**
   - Vào `http://localhost:8000/admin/payments`
   - Kiểm tra payment đã được ghi nhận
   - Thông báo hiển thị khi có payment mới

3. **Kiểm tra trong Stripe Dashboard:**
   - Vào **Payments** để xem các giao dịch
   - Kiểm tra payment status và amount

## Cách hoạt động

1. User click "Book Now" → Điền số người tham gia
2. Submit form → Tạo Booking và Payment (status: pending)
3. Tạo Stripe Checkout Session → Redirect đến Stripe
4. User thanh toán trên Stripe
5. Stripe redirect về `/booking/{booking}/success?session_id=xxx`
6. System verify payment status từ Stripe API
7. Cập nhật Payment và Booking status
8. Hiển thị success page cho user

## Troubleshooting

### Payment không được cập nhật

1. Kiểm tra `STRIPE_KEY` và `STRIPE_SECRET` trong `.env`
2. Chạy `php artisan config:clear`
3. Kiểm tra logs trong `storage/logs/laravel.log`
4. Kiểm tra payment trong Stripe Dashboard

### Lỗi "Invalid API Key"

1. Kiểm tra `STRIPE_KEY` và `STRIPE_SECRET` trong `.env`
2. Chạy `php artisan config:clear`
3. Đảm bảo không có khoảng trắng thừa trong `.env`
4. Đảm bảo đang sử dụng đúng Test Mode keys

### Payment thành công nhưng status vẫn là pending

1. Kiểm tra xem có lỗi trong `storage/logs/laravel.log`
2. Kiểm tra xem user có redirect về success page không
3. Nếu cần, có thể verify lại payment trong Stripe Dashboard và cập nhật thủ công

## Lưu ý quan trọng

1. **Test Mode vs Live Mode:**
   - Luôn test kỹ với Test Mode trước khi chuyển sang Live Mode
   - Test Mode không tính phí thật
   - Live Mode sẽ tính phí thật và không thể hoàn tiền

2. **Currency:**
   - Hiện tại sử dụng VND (Vietnamese Dong)
   - Stripe không hỗ trợ VND trực tiếp, nhưng có thể sử dụng với số tiền tính bằng cents
   - Có thể cân nhắc chuyển sang USD nếu cần

3. **Error Handling:**
   - Hệ thống đã có error handling cơ bản
   - Nếu payment failed, booking sẽ được đánh dấu là cancelled

## Nếu muốn chuyển sang dùng Webhook sau

Nếu sau này muốn sử dụng webhook để xử lý bất đồng bộ và đảm bảo 100% payment được xử lý:

1. Uncomment webhook route trong `routes/web.php`
2. Uncomment webhook exception trong `app/Http/Middleware/VerifyCsrfToken.php`
3. Setup webhook endpoint trong Stripe Dashboard
4. Thêm `STRIPE_WEBHOOK_SECRET` vào `.env`
5. Xem file `STRIPE_SETUP.md` để biết cách setup webhook

## Tài liệu tham khảo

- Stripe Documentation: https://stripe.com/docs
- Stripe PHP SDK: https://github.com/stripe/stripe-php
- Stripe Testing: https://stripe.com/docs/testing
- Stripe Checkout: https://stripe.com/docs/payments/checkout

