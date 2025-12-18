<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    /**
     * Policy cho model Payment.
     *
     * Chỉ cho phép chủ sở hữu Payment (thông qua Booking) xem và tải invoice của mình.
     */
    /**
     * Xác định user có thể xem danh sách payments hay không.
     *
     * Hiện tại không sử dụng (trả về void), việc filter được xử lý ở query/controller.
     */
    public function viewAny(User $user): bool
    {
        // Có thể implement thêm nếu cần phân quyền view danh sách payments trong tương lai.
        return false;
    }

    /**
     * Xác định user có thể xem chi tiết một payment cụ thể hay không.
     *
     * - Chỉ cho phép nếu payment thuộc về booking của user hiện tại.
     */
    public function view(User $user, Payment $payment): bool
    {
        return $payment->booking->user_id === $user->id;
    }

    /**
     * Xác định user có thể tải invoice PDF cho payment hay không.
     *
     * - Chỉ cho phép nếu payment thuộc về booking của user và trạng thái payment là 'success'.
     */
    public function downloadInvoice(User $user, Payment $payment): bool
    {
        return $payment->booking->user_id === $user->id && $payment->status === 'success';
    }

    /**
     * Các method còn lại (create/update/delete/restore/forceDelete) hiện không dùng.
     *
     * Để nguyên stub theo Laravel, có thể triển khai sau nếu muốn quản trị Payment từ admin.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payment $payment): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payment $payment): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payment $payment): bool
    {
        //
    }
}
