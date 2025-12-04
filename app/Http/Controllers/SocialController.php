<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirectToFacebook(Request $request): RedirectResponse
    {
        if ($request->filled('redirectTo')) {
            $request->session()->put('facebook_redirect_to', $request->input('redirectTo'));
        }

        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(Request $request): RedirectResponse
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

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

            $isNewUser = ! $user->wasRecentlyCreated && ! $user->getOriginal('facebook_id');

            Auth::login($user, true);

            if ($isNewUser) {
                session()->flash('success', 'Đăng ký bằng Facebook thành công!');
            } else {
                session()->flash('success', 'Đăng nhập bằng Facebook thành công!');
            }

            $redirectTo = $request->session()->pull('facebook_redirect_to');

            if ($redirectTo) {
                return redirect($redirectTo);
            }

            return redirect('/dashboard');
        } catch (Exception $exception) {
            Log::error('Facebook OAuth error', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'msg' => 'Facebook login failed. Please try again.',
            ]);
        }
    }
}


