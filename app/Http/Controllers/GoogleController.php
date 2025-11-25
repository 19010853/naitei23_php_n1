<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesSocialAuthentication;
use App\Http\Controllers\Concerns\StoresRedirectIntendedUrl;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    use HandlesSocialAuthentication;
    use StoresRedirectIntendedUrl;

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $this->rememberRedirectTo($request);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            [$user, $isNewUser] = $this->resolveSocialUser($googleUser, 'google');

            if ($isNewUser) {
                return redirect()->route('profile.edit')->with(
                    'welcome_message',
                    __('common.social_welcome', ['name' => $user->name])
                );
            }

            return redirect()->intended(route('customer.categories'));
        } catch (Exception $exception) {
            Log::error('Google OAuth error', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'msg' => __('common.social_login_failed', ['provider' => 'Google']),
            ]);
        }
    }
}



