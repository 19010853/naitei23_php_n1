<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesSocialAuthentication;
use App\Http\Controllers\Concerns\StoresRedirectIntendedUrl;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Trait xử lý đăng nhập mạng xã hội (tạo user, gán role, gửi mail mật khẩu tạm).
     * Giúp controller gọn hơn, tái sử dụng được ở nhiều provider nếu cần.
     */
    use HandlesSocialAuthentication;

    /**
     * Trait lưu lại URL dự định redirect sau khi đăng nhập.
     * Đảm bảo không bị open redirect ra ngoài domain hệ thống.
     */
    use StoresRedirectIntendedUrl;

    /**
     * Điều hướng người dùng sang trang đăng nhập Google.
     *
     * - Nếu có tham số redirectTo, trait StoresRedirectIntendedUrl sẽ lưu lại vào session.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $this->rememberRedirectTo($request);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Xử lý callback từ Google sau khi user đã đồng ý đăng nhập.
     *
     * - Lấy thông tin user từ Google.
     * - Gọi trait HandlesSocialAuthentication để tạo/cập nhật user + gán role Customer.
     * - Nếu là user mới: chuyển sang profile để user hoàn thiện thông tin.
     * - Nếu là user cũ: redirect về URL dự định hoặc trang categories cho khách.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            [$user, $isNewUser] = $this->resolveSocialUser($googleUser, 'google');

            return $this->buildGoogleRedirectResponse($user, $isNewUser);
        } catch (Exception $exception) {
            Log::error('Google OAuth error', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'msg' => __('common.social_login_failed', ['provider' => 'Google']),
            ]);
        }
    }

    /**
     * Tính toán response redirect sau khi Google login thành công.
     *
     * - User mới: hiển thị màn profile + thông báo chào mừng.
     * - User cũ: quay về URL đã intended hoặc trang categories.
     */
    protected function buildGoogleRedirectResponse(User $user, bool $isNewUser): RedirectResponse
    {
        if ($isNewUser) {
            return redirect()->route('profile.edit')->with(
                'welcome_message',
                __('common.social_welcome', ['name' => $user->name])
            );
        }

        return redirect()->intended(route('customer.categories'));
    }
}



