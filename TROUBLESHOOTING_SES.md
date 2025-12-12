# Troubleshooting AWS SES - Email không được gửi

## Vấn đề phổ biến và cách khắc phục

### 1. Kiểm tra cấu hình trong .env

Đảm bảo file `.env` có các dòng sau:

```env
# Mail Configuration
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Giải thích:**
- `MAIL_FROM_NAME` có thể để trống, hệ thống sẽ tự động dùng `APP_NAME` từ config
- Hoặc set trực tiếp: `MAIL_FROM_NAME="Tour Booking System"` (tên cố định)
- **Lưu ý:** Không dùng `MAIL_FROM_NAME="${APP_NAME}"` vì Laravel không tự động resolve biến trong `.env`

# AWS SES Configuration
AWS_ACCESS_KEY_ID=your_access_key_id_here
AWS_SECRET_ACCESS_KEY=your_secret_access_key_here
AWS_DEFAULT_REGION=us-east-1
```

**Lưu ý quan trọng:**
- `MAIL_MAILER=ses` phải được set, không phải `smtp`
- `MAIL_FROM_ADDRESS` phải là email đã được verify trong AWS SES
- `AWS_ACCESS_KEY_ID` và `AWS_SECRET_ACCESS_KEY` phải là keys hợp lệ từ AWS IAM

### 2. Clear cache sau khi thay đổi .env

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 3. Kiểm tra config đã được load đúng chưa

Chạy lệnh sau để kiểm tra:

```bash
php artisan config:show mail.default
```

Kết quả phải là `ses`, không phải `smtp`.

Nếu vẫn là `smtp`, kiểm tra:
- File `.env` có `MAIL_MAILER=ses` không
- Đã chạy `php artisan config:clear` chưa
- Không có config cache: `php artisan config:cache` (nếu có, xóa bằng `php artisan config:clear`)

### 4. Kiểm tra AWS Credentials

Chạy lệnh sau để kiểm tra AWS credentials:

```bash
php artisan tinker
```

Sau đó chạy:

```php
echo config('mail.mailers.ses.key') . PHP_EOL;
echo config('mail.mailers.ses.secret') . PHP_EOL;
echo config('mail.mailers.ses.region') . PHP_EOL;
```

Nếu trả về `null` hoặc rỗng, nghĩa là AWS credentials chưa được set trong `.env`.

### 5. Kiểm tra Sandbox Mode

AWS SES mặc định ở **Sandbox mode**, chỉ cho phép:
- Gửi email đến các email đã được verify
- Giới hạn số lượng email (thường 1 email/giây, 200 emails/ngày)

**Cách kiểm tra:**
1. Vào AWS Console > SES > Account dashboard
2. Xem "Sending limits" - nếu thấy "Sandbox" nghĩa là đang ở Sandbox mode

**Cách khắc phục:**
- Verify email của người nhận trong SES Console
- Hoặc request Production Access (xem hướng dẫn trong `AWS_SES_SETUP.md`)

### 6. Kiểm tra Email đã được verify chưa

`MAIL_FROM_ADDRESS` phải là email đã được verify trong AWS SES:

1. Vào AWS Console > SES > Verified identities
2. Kiểm tra xem email trong `MAIL_FROM_ADDRESS` có trong danh sách "Verified" không
3. Nếu chưa, verify email đó

### 7. Kiểm tra IAM Permissions

IAM user phải có quyền gửi email qua SES:

1. Vào AWS Console > IAM > Users
2. Chọn user có Access Key đang dùng
3. Kiểm tra Policies - phải có `AmazonSESFullAccess` hoặc custom policy với quyền:
   - `ses:SendEmail`
   - `ses:SendRawEmail`

### 8. Kiểm tra Region

Region trong `.env` phải khớp với region trong AWS SES Console:

1. Kiểm tra region trong AWS SES Console (góc trên bên phải)
2. Đảm bảo `AWS_DEFAULT_REGION` trong `.env` khớp với region đó

### 9. Kiểm tra Logs

Xem logs để tìm lỗi chi tiết:

```bash
tail -f storage/logs/laravel.log
```

Hoặc xem file `storage/logs/laravel.log` để tìm các lỗi liên quan đến email.

### 10. Test gửi email thủ công

Sử dụng script test trong file `test-email.php` hoặc chạy trong tinker:

```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test email từ AWS SES', function ($message) {
        $message->to('your-verified-email@example.com') // Thay bằng email đã verify
                ->subject('Test AWS SES Email');
    });
    echo "Email đã được gửi thành công!\n";
} catch (\Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
```

### 11. Các lỗi thường gặp

#### Lỗi: "Email address not verified"
- **Nguyên nhân:** Email trong `MAIL_FROM_ADDRESS` chưa được verify
- **Giải pháp:** Verify email trong AWS SES Console

#### Lỗi: "Access Denied"
- **Nguyên nhân:** IAM user không có quyền gửi email
- **Giải pháp:** Thêm policy `AmazonSESFullAccess` cho IAM user

#### Lỗi: "MessageRejected: Email address is not verified"
- **Nguyên nhân:** Đang ở Sandbox mode và email người nhận chưa được verify
- **Giải pháp:** Verify email người nhận hoặc request Production Access

#### Lỗi: "The security token included in the request is invalid"
- **Nguyên nhân:** AWS Access Key hoặc Secret Key sai
- **Giải pháp:** Kiểm tra lại credentials trong `.env`

#### Email không đến nhưng không có lỗi
- **Nguyên nhân:** Email có thể vào spam, hoặc bị bounce
- **Giải pháp:** 
  - Kiểm tra spam folder
  - Kiểm tra Suppression list trong SES Console
  - Kiểm tra Sending statistics trong SES Console

### 12. Checklist cuối cùng

Trước khi báo lỗi, đảm bảo đã kiểm tra:

- [ ] File `.env` có `MAIL_MAILER=ses`
- [ ] File `.env` có `AWS_ACCESS_KEY_ID` và `AWS_SECRET_ACCESS_KEY`
- [ ] File `.env` có `MAIL_FROM_ADDRESS` là email đã verify
- [ ] Đã chạy `php artisan config:clear`
- [ ] `php artisan config:show mail.default` trả về `ses`
- [ ] Email người nhận đã được verify (nếu ở Sandbox mode)
- [ ] IAM user có quyền gửi email
- [ ] Region trong `.env` khớp với region trong SES Console
- [ ] Đã kiểm tra logs để xem lỗi chi tiết

### 13. Liên hệ hỗ trợ

Nếu vẫn gặp vấn đề sau khi đã thử tất cả các bước trên:

1. Xem chi tiết lỗi trong `storage/logs/laravel.log`
2. Kiểm tra AWS CloudWatch Logs (nếu có)
3. Kiểm tra Sending statistics trong SES Console
4. Xem Suppression list trong SES Console

