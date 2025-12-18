<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateTourRequest extends FormRequest
{
    /**
     * Xác định user có được phép cập nhật tour hay không.
     *
     * Ở đây luôn cho phép, logic phân quyền nằm ở layer khác (middleware/policy).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate cho việc cập nhật tour.
     *
     * - Tên và slug vẫn phải duy nhất, nhưng ignore tour hiện tại.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tourId = $this->route('tour');
        if ($tourId instanceof \App\Models\Tour) {
            $tourId = $tourId->getKey();
        }

        return [
            'category_id' => 'required|integer|exists:categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tours', 'name')->ignore($tourId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tours', 'slug')->ignore($tourId),
            ],
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Chuẩn bị dữ liệu trước khi validate.
     *
     * - Nếu không có slug sẽ tự tạo slug từ name + timestamp.
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
