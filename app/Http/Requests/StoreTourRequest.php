<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreTourRequest extends FormRequest
{
    /**
     * Xác định user có được phép tạo tour mới hay không.
     *
     * Mặc định cho phép (thường route đã gắn middleware admin).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate cho việc tạo mới tour.
     *
     * - Bắt buộc thuộc về 1 category tồn tại.
     * - Tên và slug tour phải là duy nhất.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255|unique:tours,name',
            'slug' => 'nullable|string|max:255|unique:tours,slug',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Chuẩn bị dữ liệu trước khi validate.
     *
     * - Nếu không truyền slug, tự sinh slug từ name + timestamp.
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
