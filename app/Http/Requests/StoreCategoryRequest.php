<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Xác định user có được phép tạo mới category hay không.
     *
     * Mặc định cho phép (đã được bảo vệ bởi route/middleware admin).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate cho việc tạo mới category.
     *
     * - Tên và slug phải là duy nhất.
     * - Ảnh nếu có phải đúng định dạng và kích thước cho phép.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Chuẩn bị dữ liệu trước khi validate.
     *
     * - Nếu chưa truyền slug thì tự sinh slug từ name + timestamp để đảm bảo duy nhất.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name') && !$this->filled('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name) . '-' . time(),
            ]);
        }
    }
}


