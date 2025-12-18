<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTourScheduleRequest extends FormRequest
{
    /**
     * Xác định user có được phép cập nhật lịch tour hay không.
     *
     * Thường chỉ admin mới truy cập (đã check qua middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate cho việc cập nhật lịch tour.
     *
     * - Quy tắc ngày và giá, số lượng tương tự khi tạo mới.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tour_id' => 'required|exists:tours,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'price' => 'required|numeric|min:0',
            'max_participants' => 'required|integer|min:1',
        ];
    }
}
