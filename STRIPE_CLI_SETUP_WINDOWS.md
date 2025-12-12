# Hướng dẫn cài đặt Stripe CLI trên Windows

## Cách 1: Download trực tiếp (Khuyến nghị)

1. Truy cập: https://github.com/stripe/stripe-cli/releases/latest
2. Tìm file `stripe_X.X.X_windows_x86_64.zip` (hoặc `stripe_X.X.X_windows_i386.zip` cho 32-bit)
3. Download và giải nén
4. Copy file `stripe.exe` vào một thư mục (ví dụ: `C:\stripe-cli\`)
5. Thêm thư mục vào PATH:
   - Mở **System Properties** > **Environment Variables**
   - Trong **System variables**, tìm `Path` và click **Edit**
   - Click **New** và thêm đường dẫn đến thư mục chứa `stripe.exe` (ví dụ: `C:\stripe-cli\`)
   - Click **OK** để lưu
   - Mở lại PowerShell/Terminal mới

6. Kiểm tra cài đặt:
   ```powershell
   stripe --version
   ```

## Cách 2: Sử dụng Scoop (Package Manager)

Nếu bạn đã cài đặt Scoop:

```powershell
scoop install stripe
```

## Cách 3: Sử dụng Chocolatey

Nếu bạn đã cài đặt Chocolatey:

```powershell
choco install stripe-cli
```

## Sau khi cài đặt

1. Login vào Stripe:
   ```powershell
   stripe login
   ```

2. Forward webhook events:
   ```powershell
   stripe listen --forward-to localhost:8000/webhook/stripe
   ```

3. Copy webhook signing secret từ output (bắt đầu với `whsec_`)

## Giải pháp thay thế: Sử dụng ngrok (Nhanh hơn)

Nếu không muốn cài Stripe CLI, bạn có thể dùng ngrok:

1. Download ngrok: https://ngrok.com/download
2. Giải nén và chạy:
   ```powershell
   ngrok http 8000
   ```
3. Copy URL HTTPS từ ngrok (ví dụ: `https://abc123.ngrok.io`)
4. Trong Stripe Dashboard:
   - Vào **Developers** > **Webhooks**
   - Click **Add endpoint**
   - URL: `https://abc123.ngrok.io/webhook/stripe`
   - Events: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`
   - Copy **Signing secret**

## Lưu ý

- Với ngrok, mỗi lần restart sẽ có URL mới, cần cập nhật lại trong Stripe Dashboard
- Stripe CLI ổn định hơn cho development vì có thể forward events tự động

