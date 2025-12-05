<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\StoresRedirectIntendedUrl;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    use StoresRedirectIntendedUrl;

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $redirectTo = $this->rememberRedirectTo($request) ?? $request->session()->get('url.intended');

        return view('auth.login', compact('redirectTo'));
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $this->rememberRedirectTo($request);

        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user && $user->hasRole('Admin')) {
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Đăng nhập thành công!');
        }

        return redirect()->intended(RouteServiceProvider::HOME)->with('success', 'Đăng nhập thành công!');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Đăng xuất thành công!');
    }
}
