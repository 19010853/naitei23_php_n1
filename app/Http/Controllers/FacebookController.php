<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    /**
     * Điều hướng người dùng sang trang đăng nhập Facebook.
     *
     * - Nếu có tham số redirectTo, lưu vào session để sau khi login quay về đúng trang.
     */
    public function redirectToFacebook(Request $request): RedirectResponse
    {
        if ($request->filled('redirectTo')) {
            $request->session()->put('facebook_redirect_to', $request->input('redirectTo'));
        }

        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Xử lý callback từ Facebook sau khi người dùng đồng ý đăng nhập.
     *
     * - Tìm hoặc tạo user tương ứng trong hệ thống.
     * - Đăng nhập user và redirect về trang phù hợp.
     */
    public function handleFacebookCallback(Request $request): RedirectResponse
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            [$user, $isNewUser] = $this->findOrCreateFacebookUser($facebookUser);

            Auth::login($user, true);

            return $this->buildFacebookRedirectResponse($request, $isNewUser);
        } catch (Exception $exception) {
            Log::error('Facebook OAuth error', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'msg' => 'Facebook login failed. Please try again.',
            ]);
        }
    }

    /**
     * Tìm hoặc tạo User từ thông tin Facebook.
     *
     * - Ưu tiên map theo facebook_id.
     * - Nếu chưa có, cố gắng ghép theo email để gắn facebook_id cho tài khoản sẵn có.
     * - Nếu vẫn không tìm được, tạo user mới với mật khẩu ngẫu nhiên.
     *
     * @return array{0: User, 1: bool} Trả về [user, isNewUser]
     */
    protected function findOrCreateFacebookUser($facebookUser): array
    {
        $user = User::where('facebook_id', $facebookUser->getId())->first();

        if (! $user) {
            $existingUser = null;

            if ($facebookUser->getEmail()) {
                $existingUser = User::where('email', $facebookUser->getEmail())->first();
            }

            if ($existingUser) {
                $existingUser->update([
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                ]);

                $user = $existingUser;
            } else {
                $user = User::create([
                    'name' => $facebookUser->getName() ?: $facebookUser->getNickname(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                    'password' => bcrypt(str()->random(12)),
                ]);
            }
        }

        // Giữ nguyên cách tính isNewUser hiện có để không thay đổi business logic
        $isNewUser = ! $user->wasRecentlyCreated && ! $user->getOriginal('facebook_id');

        return [$user, $isNewUser];
    }

    /**
     * Xây dựng response redirect sau khi Facebook login thành công.
     *
     * - Nếu là user mới: flash message đăng ký thành công.
     * - Nếu là user cũ: flash message đăng nhập thành công.
     * - Ưu tiên redirect về URL đã lưu trong session, nếu không thì về /dashboard.
     */
    protected function buildFacebookRedirectResponse(Request $request, bool $isNewUser): RedirectResponse
    {
        if ($isNewUser) {
            session()->flash('success', __('common.facebook_register_success'));
        } else {
            session()->flash('success', __('common.facebook_login_success'));
        }

        $redirectTo = $request->session()->pull('facebook_redirect_to');

        if ($redirectTo) {
            return redirect($redirectTo);
        }

        return redirect('/dashboard');
    }
}


