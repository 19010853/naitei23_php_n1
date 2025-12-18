<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Xác định user có được phép gửi request login này hay không.
     *
     * Ở đây luôn trả về true vì check quyền đã được xử lý ở nơi khác (middleware, guard).
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Các rules validate áp dụng cho form đăng nhập.
     *
     * - Bắt buộc email/password, định dạng email hợp lệ.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Thực hiện đăng nhập với credentials từ request.
     *
     * - Có tích hợp rate limiter để hạn chế brute-force (tối đa 5 lần thử).
     * - Nếu login thất bại: tăng counter và ném ValidationException với message auth.failed.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Đảm bảo request đăng nhập chưa bị giới hạn tần suất.
     *
     * - Nếu quá số lần cho phép: phát sự kiện Lockout và ném lỗi throttle.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Tạo key cho rate limiter dựa trên email + IP.
     *
     * Ví dụ: user@example.com|127.0.0.1
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
