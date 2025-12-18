<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Xác định user có được phép cập nhật category hay không.
     *
     * Mặc định cho phép (đã được middleware kiểm soát).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate cho việc cập nhật category.
     *
     * - Tên và slug vẫn phải duy nhất, nhưng bỏ qua category hiện tại.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category');
        if ($categoryId instanceof \App\Models\Category) {
            $categoryId = $categoryId->getKey();
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Chuẩn bị dữ liệu trước khi validate.
     *
     * - Nếu chưa có slug thì tự sinh từ name + timestamp (tương tự create).
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


