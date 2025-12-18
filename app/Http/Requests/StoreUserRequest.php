<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Xác định user có được phép tạo mới user khác hay không.
     *
     * Ở đây trả về true, thường đã được bảo vệ bởi middleware admin.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate cho việc tạo mới user bên admin.
     *
     * - Bắt buộc name/email/password.
     * - Email phải là duy nhất.
     * - role_ids là mảng id role hợp lệ.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}
