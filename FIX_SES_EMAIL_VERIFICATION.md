# Khắc phục lỗi: Email address is not verified

## Lỗi hiện tại

```
Request to AWS SES API failed. Reason: Email address is not verified. 
The following identities failed the check in region AP-SOUTHEAST-1: 
"${Traveloka}" <noreply@traveloka.com>
```

## Nguyên nhân

1. **Email `noreply@traveloka.com` chưa được verify trong AWS SES**
2. **`MAIL_FROM_NAME` đang gửi literal string `"${Traveloka}"` thay vì giá trị thực tế**

## Cách khắc phục

### Bước 1: Verify email trong AWS SES

1. Đăng nhập vào **AWS Console**: https://console.aws.amazon.com
2. Chọn region **AP-SOUTHEAST-1** (Singapore) - region bạn đang dùng
3. Vào **Simple Email Service (SES)**
4. Click **Verified identities** > **Create identity**
5. Chọn **Email address**
6. Nhập email: `noreply@traveloka.com`
7. Click **Create identity**
8. Kiểm tra email inbox của `noreply@traveloka.com`
9. Click link verification trong email từ AWS
10. Sau khi verify, email sẽ có status **Verified**

**Lưu ý:** Nếu bạn không có quyền truy cập email `noreply@traveloka.com`, bạn cần:
- Verify domain `traveloka.com` thay vì email address (xem hướng dẫn trong `AWS_SES_SETUP.md`)
- Hoặc dùng email khác mà bạn có quyền truy cập

### Bước 2: Sửa cấu hình MAIL_FROM_NAME

**Vấn đề:** File `.env` có thể có `MAIL_FROM_NAME="${APP_NAME}"` hoặc `MAIL_FROM_NAME="${Traveloka}"`, nhưng Laravel không tự động resolve biến trong `.env`.

**Giải pháp:** Có 2 cách:

#### Cách 1: Để trống MAIL_FROM_NAME (Khuyến nghị)

Trong file `.env`, xóa hoặc comment dòng `MAIL_FROM_NAME`:

```env
# Mail Configuration
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@traveloka.com
# MAIL_FROM_NAME="${APP_NAME}"  # Comment hoặc xóa dòng này
```

Hệ thống sẽ tự động dùng `APP_NAME` từ config.

#### Cách 2: Set giá trị trực tiếp

Trong file `.env`, set giá trị cụ thể:

```env
# Mail Configuration
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@traveloka.com
MAIL_FROM_NAME="Traveloka"  # Hoặc tên bạn muốn, không dùng ${APP_NAME}
```

### Bước 3: Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Bước 4: Kiểm tra lại

1. Kiểm tra config:
   ```bash
   php artisan config:show mail.from.name
   php artisan config:show mail.from.address
   ```

2. Test gửi email lại (đăng ký user mới hoặc reset password)

## Kiểm tra trong AWS SES Console

Sau khi verify email, kiểm tra:

1. Vào **AWS Console** > **SES** > **Verified identities**
2. Tìm email `noreply@traveloka.com`
3. Đảm bảo status là **Verified** (màu xanh)
4. Nếu vẫn là **Pending**, kiểm tra email inbox và click link verification

## Nếu vẫn gặp lỗi

1. **Kiểm tra region:**
   - Đảm bảo `AWS_DEFAULT_REGION=ap-southeast-1` trong `.env`
   - Đảm bảo region trong AWS SES Console khớp với region trong `.env`

2. **Kiểm tra email đã verify:**
   - Vào **SES Console** > **Verified identities**
   - Tìm email `noreply@traveloka.com`
   - Đảm bảo status là **Verified**

3. **Kiểm tra logs:**
   - Xem `storage/logs/laravel.log` để tìm lỗi chi tiết

4. **Test với email khác:**
   - Nếu có email khác đã verify, thử dùng email đó:
   ```env
   MAIL_FROM_ADDRESS=your-verified-email@example.com
   ```

## Lưu ý về Sandbox Mode

Nếu AWS SES đang ở **Sandbox mode**:
- Bạn chỉ có thể gửi email đến các email đã được verify
- Email người nhận (`20251.web@gmail.com` trong trường hợp này) cũng cần được verify
- Hoặc request **Production Access** để gửi đến bất kỳ email nào

**Cách kiểm tra Sandbox mode:**
1. Vào **AWS Console** > **SES** > **Account dashboard**
2. Xem "Sending limits" - nếu thấy "Sandbox" nghĩa là đang ở Sandbox mode

