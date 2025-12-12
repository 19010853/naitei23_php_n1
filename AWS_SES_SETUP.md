# Hướng dẫn Setup AWS SES (Simple Email Service)

## Tổng quan
Hệ thống đã được cấu hình để sử dụng AWS SES thay vì SMTP để gửi email. AWS SES là dịch vụ email của Amazon Web Services, cung cấp khả năng gửi email đáng tin cậy và có thể mở rộng.

## Các chức năng email trong hệ thống

1. **Email Verification**: Xác minh email khi user đăng ký
2. **Password Reset**: Gửi link reset mật khẩu
3. **Temporary Password**: Gửi mật khẩu tạm thời cho user đăng ký qua social login (Google/Facebook)

## Các bước setup

### 1. Tạo tài khoản AWS

1. Truy cập https://aws.amazon.com và đăng ký tài khoản (nếu chưa có)
2. Đăng nhập vào AWS Console: https://console.aws.amazon.com
3. Chọn region phù hợp (ví dụ: `us-east-1`, `ap-southeast-1`)

### 2. Verify Email Domain hoặc Email Address

#### Option A: Verify Email Address (Nhanh, cho development)

1. Vào **AWS Console** > **Simple Email Service (SES)**
2. Chọn region (ví dụ: `us-east-1`)
3. Vào **Verified identities** > **Create identity**
4. Chọn **Email address**
5. Nhập email address bạn muốn verify (ví dụ: `noreply@yourdomain.com`)
6. Click **Create identity**
7. Kiểm tra email và click link verification trong email từ AWS
8. Sau khi verify, email address sẽ có status **Verified**

**Lưu ý:** 
- Với **Sandbox mode** (mặc định), bạn chỉ có thể gửi email đến các email đã được verify
- Để gửi email đến bất kỳ địa chỉ nào, cần request **Production access** (xem bước 5)

#### Option B: Verify Domain (Khuyến nghị cho production)

1. Vào **AWS Console** > **Simple Email Service (SES)**
2. Chọn region
3. Vào **Verified identities** > **Create identity**
4. Chọn **Domain**
5. Nhập domain của bạn (ví dụ: `yourdomain.com`)
6. Chọn **Use a TXT record** để verify domain
7. Copy **TXT record** được cung cấp
8. Thêm TXT record vào DNS của domain:
   - Vào DNS management của domain provider
   - Thêm TXT record với giá trị từ AWS
   - Đợi DNS propagate (có thể mất vài phút đến vài giờ)
9. Click **Verify** trong AWS Console
10. Sau khi verify, bạn có thể gửi email từ bất kỳ địa chỉ nào trong domain (ví dụ: `noreply@yourdomain.com`, `support@yourdomain.com`)

### 3. Tạo IAM User và Access Keys

1. Vào **AWS Console** > **IAM** > **Users**
2. Click **Create user**
3. Nhập tên user (ví dụ: `ses-email-sender`)
4. Chọn **Provide user access to the AWS Management Console** (tùy chọn) hoặc chỉ cần **Programmatic access**
5. Click **Next**
6. Chọn **Attach policies directly**
7. Tìm và chọn policy: **AmazonSESFullAccess** (hoặc tạo custom policy với quyền hạn chế hơn)
8. Click **Next** > **Create user**
9. **Quan trọng**: Copy **Access Key ID** và **Secret Access Key** ngay lập tức (bạn sẽ không thể xem lại Secret Access Key sau này)
10. Lưu các keys này an toàn

### 4. Request Production Access (Nếu cần)

Nếu bạn muốn gửi email đến bất kỳ địa chỉ nào (không chỉ các email đã verify):

1. Vào **AWS Console** > **SES** > **Account dashboard**
2. Click **Request production access**
3. Điền form:
   - **Mail Type**: Transactional (cho ứng dụng này)
   - **Website URL**: URL của website
   - **Use case description**: Mô tả cách bạn sử dụng email (ví dụ: "Sending booking confirmations, password resets, and email verifications for tour booking system")
   - **Compliance**: Đảm bảo tuân thủ các quy định về email
4. Submit request
5. AWS sẽ review và approve (thường mất 24-48 giờ)

**Lưu ý:** 
- Trong Sandbox mode, bạn chỉ có thể gửi đến email đã verify
- Production access cho phép gửi đến bất kỳ email nào
- AWS có thể yêu cầu thêm thông tin nếu cần

### 5. Cập nhật file .env

Thêm các dòng sau vào file `.env`:

```env
# Mail Configuration
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# AWS SES Configuration
AWS_ACCESS_KEY_ID=your_access_key_id_here
AWS_SECRET_ACCESS_KEY=your_secret_access_key_here
AWS_DEFAULT_REGION=us-east-1
AWS_SES_CONFIGURATION_SET=optional_configuration_set_name
```

**Lưu ý:**
- Thay `your_access_key_id_here` và `your_secret_access_key_here` bằng keys từ bước 3
- Thay `us-east-1` bằng region bạn đã chọn trong AWS Console
- `MAIL_FROM_ADDRESS` phải là email đã được verify trong SES
- `AWS_SES_CONFIGURATION_SET` là tùy chọn (dùng để tracking và analytics)

**Giải thích `MAIL_FROM_NAME`:**
- Có thể để trống `MAIL_FROM_NAME` (hoặc không set), hệ thống sẽ tự động dùng `APP_NAME` từ config
- Hoặc set trực tiếp với tên cố định: `MAIL_FROM_NAME="Tour Booking System"`
- **Lưu ý quan trọng:** Không dùng `MAIL_FROM_NAME="${APP_NAME}"` vì Laravel không tự động resolve biến trong `.env` file. Nếu dùng cách này, nó sẽ gửi literal string `"${APP_NAME}"` thay vì giá trị thực tế.
- Ví dụ: Nếu `APP_NAME="Tour Booking System"` trong `.env` và bạn không set `MAIL_FROM_NAME`, thì email sẽ hiển thị từ: "Tour Booking System <noreply@yourdomain.com>"

### 6. Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 7. Test gửi email

#### Test Email Verification

1. Đăng ký tài khoản mới
2. Kiểm tra email inbox để nhận verification link
3. Click link để verify email

#### Test Password Reset

1. Vào trang login
2. Click "Forgot password"
3. Nhập email và submit
4. Kiểm tra email để nhận reset link

#### Test Social Login Email

1. Đăng nhập bằng Google/Facebook với email mới
2. Kiểm tra email để nhận temporary password

### 8. Kiểm tra trong AWS Console

1. Vào **AWS Console** > **SES** > **Sending statistics**
2. Xem số lượng email đã gửi, bounce rate, complaint rate
3. Vào **Suppression list** để xem các email bị bounce hoặc complaint

## Troubleshooting

### Email không được gửi

**QUAN TRỌNG:** Xem file `TROUBLESHOOTING_SES.md` để có hướng dẫn chi tiết về troubleshooting.

1. **Kiểm tra cấu hình trong .env:**
   - Đảm bảo `MAIL_MAILER=ses` (KHÔNG phải `smtp`)
   - Đảm bảo `AWS_ACCESS_KEY_ID` và `AWS_SECRET_ACCESS_KEY` trong `.env` đúng
   - Chạy `php artisan config:clear` sau khi thay đổi `.env`
   - Kiểm tra: `php artisan config:show mail.default` phải trả về `ses`

2. **Kiểm tra Sandbox mode:**
   - Nếu đang ở Sandbox mode, chỉ có thể gửi đến email đã verify
   - Verify email của người nhận hoặc request production access

3. **Kiểm tra logs:**
   - Xem `storage/logs/laravel.log` để tìm lỗi
   - Kiểm tra AWS CloudWatch Logs nếu có

4. **Kiểm tra email address:**
   - Đảm bảo `MAIL_FROM_ADDRESS` đã được verify trong SES
   - Đảm bảo region trong `.env` khớp với region trong AWS Console

### Lỗi "Email address not verified"

- Verify email address trong AWS SES Console
- Đảm bảo `MAIL_FROM_ADDRESS` trong `.env` là email đã verify

### Lỗi "Access Denied"

- Kiểm tra IAM user có quyền `ses:SendEmail` và `ses:SendRawEmail`
- Đảm bảo Access Key ID và Secret Access Key đúng

### Email bị bounce

1. Kiểm tra **Suppression list** trong SES Console
2. Xem lý do bounce (invalid email, mailbox full, etc.)
3. Remove email khỏi suppression list nếu cần

### Email vào spam

1. **Setup SPF record:**
   - Thêm SPF record vào DNS: `v=spf1 include:amazonses.com ~all`

2. **Setup DKIM:**
   - Trong SES Console, vào verified domain
   - Copy DKIM records (3 CNAME records)
   - Thêm vào DNS của domain

3. **Setup DMARC:**
   - Tạo DMARC record trong DNS: `v=DMARC1; p=none; rua=mailto:dmarc@yourdomain.com`

4. **Warm up domain:**
   - Bắt đầu với số lượng email nhỏ và tăng dần
   - Tránh gửi số lượng lớn ngay từ đầu

## Best Practices

1. **Sử dụng Configuration Sets:**
   - Tạo Configuration Set trong SES để tracking opens, clicks, bounces
   - Thêm `AWS_SES_CONFIGURATION_SET` vào `.env`

2. **Monitor sending statistics:**
   - Thường xuyên kiểm tra bounce rate và complaint rate
   - Giữ bounce rate < 5% và complaint rate < 0.1%

3. **Handle bounces và complaints:**
   - Implement logic để xử lý bounces và complaints
   - Remove email khỏi mailing list nếu bị complaint

4. **Use verified domain:**
   - Verify domain thay vì chỉ verify email address
   - Cho phép gửi từ nhiều email addresses trong domain

5. **Rate limiting:**
   - AWS SES có rate limits (ví dụ: 1 email/giây trong Sandbox)
   - Implement queue để xử lý email bất đồng bộ nếu cần

## Chi phí

- **Free tier**: 62,000 emails/tháng miễn phí (nếu gửi từ EC2 instance)
- **Pricing**: $0.10 per 1,000 emails sau free tier
- **Chi phí rất thấp** so với các dịch vụ email khác

## Tài liệu tham khảo

- AWS SES Documentation: https://docs.aws.amazon.com/ses/
- Laravel Mail Documentation: https://laravel.com/docs/mail
- AWS SES Pricing: https://aws.amazon.com/ses/pricing/
- AWS SES Best Practices: https://docs.aws.amazon.com/ses/latest/dg/best-practices.html

## Migration từ SMTP

Nếu bạn đang sử dụng SMTP và muốn chuyển sang SES:

1. Follow các bước setup ở trên
2. Cập nhật `.env`:
   - Đổi `MAIL_MAILER=smtp` thành `MAIL_MAILER=ses`
   - Thêm AWS credentials
   - Xóa các config SMTP cũ (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD)
3. Clear cache: `php artisan config:clear`
4. Test gửi email
5. Monitor trong AWS Console để đảm bảo mọi thứ hoạt động

