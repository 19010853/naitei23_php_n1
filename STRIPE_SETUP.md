# Hướng dẫn Setup Stripe Payment Integration

## Tổng quan
Hệ thống đã được tích hợp Stripe để xử lý thanh toán booking tour. Người dùng có thể thanh toán bằng thẻ VISA/credit card thông qua Stripe Checkout. Hệ thống sử dụng **xử lý đồng bộ** (synchronous) - không cần webhook.

## Ưu điểm
- ✅ Đơn giản, không cần setup webhook
- ✅ Không cần ngrok hoặc Stripe CLI
- ✅ Xử lý thanh toán ngay lập tức
- ✅ Phù hợp cho development và production

## Các bước cần thực hiện

### 1. Tạo tài khoản Stripe

1. Truy cập https://stripe.com và đăng ký tài khoản
2. Xác minh email và hoàn tất thông tin tài khoản
3. Chọn chế độ **Test Mode** để test (hoặc **Live Mode** cho production)

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
- Thay `pk_test_`, `sk_test_` bằng các keys thực tế của bạn
- Với production, sử dụng `pk_live_` và `sk_live_`
- Không commit file `.env` lên Git
- **Không cần** `STRIPE_WEBHOOK_SECRET` vì không sử dụng webhook

### 4. Clear cache và test

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
7. Cập nhật Payment và Booking status ngay lập tức
8. Hiển thị success page cho user

## Troubleshooting

### Payment không được cập nhật

1. **Kiểm tra API keys:**
   - Đảm bảo `STRIPE_KEY` và `STRIPE_SECRET` trong `.env` đúng
   - Chạy `php artisan config:clear`

2. **Kiểm tra logs:**
   - Xem `storage/logs/laravel.log` để tìm lỗi
   - Kiểm tra payment trong Stripe Dashboard

3. **Kiểm tra redirect:**
   - Đảm bảo user được redirect về success page sau khi thanh toán
   - Nếu user đóng tab trước khi redirect, payment vẫn được ghi nhận trong Stripe nhưng có thể chưa cập nhật trong database

### Lỗi "Invalid API Key"

1. Kiểm tra `STRIPE_KEY` và `STRIPE_SECRET` trong `.env`
2. Chạy `php artisan config:clear`
3. Đảm bảo không có khoảng trắng thừa trong `.env`
4. Đảm bảo đang sử dụng đúng Test Mode keys

### Payment thành công nhưng status vẫn là pending

1. Kiểm tra xem có lỗi trong `storage/logs/laravel.log`
2. Kiểm tra xem user có redirect về success page không
3. Nếu cần, có thể verify lại payment trong Stripe Dashboard và cập nhật thủ công

### Lỗi "Invalid payment session"

1. Đảm bảo `session_id` được truyền đúng trong URL
2. Kiểm tra booking ID trong metadata của Stripe session
3. Kiểm tra logs để xem chi tiết lỗi

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
   - Admin sẽ nhận thông báo về mọi payment (thành công hoặc thất bại)

4. **Edge Cases:**
   - Nếu user đóng tab trước khi redirect về success page, payment vẫn được ghi nhận trong Stripe
   - Có thể verify payment trong Stripe Dashboard và cập nhật thủ công nếu cần
   - Nếu muốn xử lý 100% tự động, có thể cân nhắc sử dụng webhook (xem phần dưới)

## Nếu muốn sử dụng Webhook (Tùy chọn)

Nếu muốn xử lý thanh toán bất đồng bộ và đảm bảo 100% payment được xử lý tự động:

1. **Uncomment webhook route** trong `routes/web.php`:
   ```php
   Route::post('/webhook/stripe', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])
       ->middleware('web')
       ->name('webhook.stripe');
   ```

2. **Uncomment webhook exception** trong `app/Http/Middleware/VerifyCsrfToken.php`:
   ```php
   protected $except = [
       'webhook/stripe',
   ];
   ```

3. **Setup webhook endpoint:**
   - Sử dụng ngrok: `ngrok http 8000` → Copy URL HTTPS
   - Hoặc sử dụng Stripe CLI: `stripe listen --forward-to localhost:8000/webhook/stripe`
   - Tạo webhook endpoint trong Stripe Dashboard với URL trên
   - Chọn events: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`

4. **Thêm webhook secret** vào `.env`:
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
   ```

5. Xem file `STRIPE_CLI_SETUP_WINDOWS.md` để biết cách setup Stripe CLI trên Windows

## Tài liệu tham khảo

- Stripe Documentation: https://stripe.com/docs
- Stripe PHP SDK: https://github.com/stripe/stripe-php
- Stripe Testing: https://stripe.com/docs/testing
- Stripe Checkout: https://stripe.com/docs/payments/checkout
- Stripe Webhooks: https://stripe.com/docs/webhooks (nếu muốn sử dụng)
