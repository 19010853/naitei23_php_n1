<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait StoresRedirectIntendedUrl
{
    /**
     * Capture and persist the redirectTo/query value as the intended URL.
     */
    protected function rememberRedirectTo(Request $request): ?string
    {
        $redirectTo = $request->query('redirectTo') ?? $request->input('redirect_to');
        $redirectTo = $this->sanitizeRedirectTo($redirectTo);

        if ($redirectTo) {
            $request->session()->put('url.intended', $redirectTo);
        }

        return $redirectTo;
    }

    /**
     * Make sure the redirect target stays within the application.
     */
    protected function sanitizeRedirectTo(?string $redirectTo): ?string
    {
        if (blank($redirectTo)) {
            return null;
        }

        $redirectTo = trim($redirectTo);
        $appUrl = rtrim(config('app.url'), '/');

        if (Str::startsWith($redirectTo, $appUrl)) {
            $redirectTo = substr($redirectTo, strlen($appUrl)) ?: '/';
        }

        // Block protocol-relative URLs and backslash variants
        if (preg_match('#^//|^\\\\|^/\\\\#', $redirectTo)) {
            return null;
        }

        if (!Str::startsWith($redirectTo, '/')) {
            return null;
        }

        return $redirectTo;
    }
}



