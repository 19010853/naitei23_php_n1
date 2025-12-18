<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Xác định user có được phép cập nhật user khác hay không.
     *
     * Ở đây luôn cho phép, thực tế route thường đã được bảo vệ bởi middleware admin.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate cho việc cập nhật user bên admin.
     *
     * - Email vẫn phải duy nhất, nhưng bỏ qua user hiện tại.
     * - Mật khẩu có thể bỏ trống (nullable).
     * - role_ids là mảng id role hợp lệ.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}
