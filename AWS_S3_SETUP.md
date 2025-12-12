# Hướng dẫn Setup AWS S3 cho Image Storage

## Tổng quan
Hệ thống đã được cấu hình để sử dụng AWS S3 để lưu trữ hình ảnh cho Tour Categories và Tours thay vì lưu trữ local. AWS S3 cung cấp khả năng lưu trữ đáng tin cậy, có thể mở rộng và hiệu suất cao.

## Các chức năng đã được tích hợp

1. **Upload images lên S3**: Khi tạo/cập nhật Tour Category hoặc Tour, hình ảnh sẽ được upload lên S3
2. **Delete images từ S3**: Khi xóa Tour Category hoặc Tour, hình ảnh cũng sẽ được xóa khỏi S3
3. **Tự động lấy URL**: Models tự động convert S3 path thành full URL khi truy cập `image_url` attribute

## Các bước setup

### 1. Tạo S3 Bucket

1. Đăng nhập vào **AWS Console**: https://console.aws.amazon.com
2. Vào **S3** (Simple Storage Service)
3. Click **Create bucket**
4. Điền thông tin:
   - **Bucket name**: Tên bucket (ví dụ: `tour-booking-images` hoặc `your-app-images`)
   - **AWS Region**: Chọn region phù hợp (ví dụ: `ap-southeast-1` cho Singapore)
   - **Object Ownership**: Chọn **ACLs enabled** (recommended) hoặc **Bucket owner preferred**
   - **Block Public Access settings**: **Bỏ chọn** "Block all public access" (vì cần public access cho images)
     - Hoặc giữ nguyên và sử dụng CloudFront/CDN sau
   - **Bucket Versioning**: Tùy chọn (khuyến nghị: Enable)
   - **Default encryption**: Khuyến nghị: Enable (SSE-S3)
5. Click **Create bucket**

### 2. Cấu hình Bucket Permissions

1. Vào bucket vừa tạo
2. Click tab **Permissions**
3. Scroll xuống **Bucket policy**
4. Click **Edit** và thêm policy sau (thay `your-bucket-name` bằng tên bucket của bạn):

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::your-bucket-name/*"
        }
    ]
}
```

**Lưu ý:** Policy này cho phép public read access. Nếu bạn muốn bảo mật hơn, có thể:
- Sử dụng CloudFront với signed URLs
- Hoặc chỉ cho phép access từ domain cụ thể

5. Click **Save changes**

### 3. Cấu hình CORS (Nếu cần)

Nếu bạn cần upload từ browser trực tiếp (client-side), cần cấu hình CORS:

1. Vào bucket > **Permissions** > **Cross-origin resource sharing (CORS)**
2. Click **Edit** và thêm:

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "PUT", "POST", "DELETE", "HEAD"],
        "AllowedOrigins": ["http://localhost:8000", "https://yourdomain.com"],
        "ExposeHeaders": ["ETag"]
    }
]
```

3. Click **Save changes**

### 4. Tạo IAM User và Access Keys (Nếu chưa có)

Nếu bạn đã có IAM user từ AWS SES setup, có thể dùng chung. Nếu chưa:

1. Vào **AWS Console** > **IAM** > **Users**
2. Click **Create user**
3. Nhập tên user (ví dụ: `s3-image-uploader`)
4. Chọn **Attach policies directly**
5. Tìm và chọn policy: **AmazonS3FullAccess** (hoặc tạo custom policy với quyền hạn chế hơn)
6. Click **Create user**
7. **Quan trọng**: Copy **Access Key ID** và **Secret Access Key** ngay lập tức

**Custom Policy (Khuyến nghị - chỉ cho phép access bucket cụ thể):**

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::your-bucket-name",
                "arn:aws:s3:::your-bucket-name/*"
            ]
        }
    ]
}
```

### 5. Cập nhật file .env

Thêm các dòng sau vào file `.env`:

```env
# Filesystem Configuration
FILESYSTEM_DRIVER=s3

# AWS S3 Configuration
AWS_ACCESS_KEY_ID=your_access_key_id_here
AWS_SECRET_ACCESS_KEY=your_secret_access_key_here
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket-name.s3.ap-southeast-1.amazonaws.com
```

**Lưu ý:**
- `AWS_ACCESS_KEY_ID` và `AWS_SECRET_ACCESS_KEY`: Keys từ IAM user (có thể dùng chung với SES)
- `AWS_DEFAULT_REGION`: Region của bucket (ví dụ: `ap-southeast-1`, `us-east-1`)
- `AWS_BUCKET`: Tên bucket bạn vừa tạo
- `AWS_URL`: URL của bucket (format: `https://bucket-name.s3.region.amazonaws.com`)

**Ví dụ:**
```env
FILESYSTEM_DRIVER=s3
AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE
AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=tour-booking-images
AWS_URL=https://tour-booking-images.s3.ap-southeast-1.amazonaws.com
```

### 6. Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 7. Test upload image

1. Đăng nhập vào admin panel
2. Tạo hoặc cập nhật một Tour Category hoặc Tour với hình ảnh
3. Kiểm tra trong AWS S3 Console xem file đã được upload chưa
4. Kiểm tra trên website xem hình ảnh hiển thị đúng không

## Cấu trúc thư mục trong S3

Hệ thống sẽ tự động tạo cấu trúc thư mục như sau:

```
your-bucket-name/
├── images/
│   ├── categories/
│   │   └── {uuid}.{extension}
│   └── tours/
│       └── {uuid}.{extension}
```

## Troubleshooting

### Lỗi "Access Denied" khi upload

1. **Kiểm tra IAM permissions:**
   - Đảm bảo IAM user có quyền `s3:PutObject`, `s3:GetObject`, `s3:DeleteObject`
   - Kiểm tra bucket policy cho phép access

2. **Kiểm tra bucket permissions:**
   - Đảm bảo bucket không block public access (nếu cần public read)
   - Hoặc cấu hình bucket policy đúng

3. **Kiểm tra credentials:**
   - Đảm bảo `AWS_ACCESS_KEY_ID` và `AWS_SECRET_ACCESS_KEY` trong `.env` đúng
   - Chạy `php artisan config:clear`

### Image không hiển thị

1. **Kiểm tra URL:**
   - Xem `storage/logs/laravel.log` để kiểm tra URL được generate
   - Đảm bảo `AWS_URL` trong `.env` đúng format

2. **Kiểm tra bucket policy:**
   - Đảm bảo bucket policy cho phép public read (nếu cần)
   - Hoặc sử dụng CloudFront/CDN

3. **Kiểm tra CORS:**
   - Nếu upload từ browser, đảm bảo CORS được cấu hình đúng

### Lỗi "Bucket does not exist"

1. **Kiểm tra bucket name:**
   - Đảm bảo `AWS_BUCKET` trong `.env` đúng tên bucket
   - Tên bucket phải unique globally

2. **Kiểm tra region:**
   - Đảm bảo `AWS_DEFAULT_REGION` khớp với region của bucket

### Image cũ vẫn hiển thị (cache)

1. **Clear browser cache**
2. **Clear Laravel cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Kiểm tra CDN cache** (nếu có)

## Best Practices

1. **Sử dụng CloudFront/CDN:**
   - Tạo CloudFront distribution cho S3 bucket
   - Cập nhật `AWS_URL` trong `.env` thành CloudFront URL
   - Cải thiện performance và giảm chi phí

2. **Image Optimization:**
   - Resize images trước khi upload
   - Sử dụng format WebP cho better compression
   - Implement image optimization trong upload process

3. **Backup:**
   - Enable bucket versioning để backup tự động
   - Setup lifecycle policies để quản lý storage costs

4. **Security:**
   - Sử dụng IAM policies với quyền hạn chế (chỉ access bucket cụ thể)
   - Sử dụng CloudFront với signed URLs nếu cần bảo mật
   - Enable bucket encryption

5. **Monitoring:**
   - Setup CloudWatch alarms cho bucket
   - Monitor storage costs
   - Track access patterns

## Migration từ Local Storage

Nếu bạn đã có images lưu local và muốn migrate lên S3:

1. **Upload existing images lên S3:**
   ```bash
   # Sử dụng AWS CLI hoặc script PHP
   aws s3 sync public/images s3://your-bucket-name/images
   ```

2. **Update database paths:**
   - Nếu paths trong database là relative (ví dụ: `images/tours/abc.jpg`), không cần thay đổi
   - Models sẽ tự động convert thành S3 URL

3. **Test:**
   - Kiểm tra images hiển thị đúng
   - Xóa images cũ từ local storage sau khi confirm

## Chi phí

- **Storage**: ~$0.023 per GB/tháng (Standard storage)
- **Requests**: 
  - PUT requests: $0.005 per 1,000 requests
  - GET requests: $0.0004 per 1,000 requests
- **Data Transfer Out**: 
  - First 10 TB: $0.09 per GB
  - Next 40 TB: $0.085 per GB

**Lưu ý:** Sử dụng CloudFront có thể giảm chi phí data transfer.

## Tài liệu tham khảo

- AWS S3 Documentation: https://docs.aws.amazon.com/s3/
- Laravel Filesystem Documentation: https://laravel.com/docs/filesystem
- AWS S3 Pricing: https://aws.amazon.com/s3/pricing/
- AWS S3 Best Practices: https://docs.aws.amazon.com/AmazonS3/latest/userguide/security-best-practices.html

