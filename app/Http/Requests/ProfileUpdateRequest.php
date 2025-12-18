<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Xác định user có quyền gửi request cập nhật profile hay không.
     *
     * Ở đây cho phép mọi user đã đăng nhập sử dụng form này (check auth ở middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate áp dụng cho form cập nhật profile.
     *
     * - Bắt buộc name.
     * - Email phải là duy nhất trừ chính user hiện tại.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
        ];
    }
}

