<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourScheduleRequest extends FormRequest
{
    /**
     * Xác định user có được phép tạo lịch tour mới hay không.
     *
     * Thường chỉ admin mới gọi tới request này (proxy qua middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Các rules validate cho việc tạo mới lịch tour.
     *
     * - Ngày bắt đầu >= hôm nay, ngày kết thúc > ngày bắt đầu.
     * - Giá và số lượng người tham gia phải hợp lệ (>= 0 hoặc >= 1).
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
