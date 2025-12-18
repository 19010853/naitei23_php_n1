<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Payment.
 *
 * Lưu thông tin thanh toán cho một booking (số tiền, trạng thái, thông tin Stripe, invoice...).
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'payment_date',
        'stripe_payment_intent_id',
        'stripe_session_id',
        'stripe_metadata',
        'invoice_id',
        'pdf_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Payment thuộc về một Booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Sinh mã invoice_id duy nhất cho mỗi Payment.
     *
     * Định dạng: INV-YYYYMMDD-XXXXXX (6 ký tự hex cuối của uniqid, viết hoa).
     */
    public static function generateInvoiceId(): string
    {
        do {
            $invoiceId = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('invoice_id', $invoiceId)->exists());

        return $invoiceId;
    }

    /**
     * Hook boot để tự động sinh invoice_id khi tạo Payment mới nếu chưa có.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->invoice_id)) {
                $payment->invoice_id = self::generateInvoiceId();
            }
        });
    }
}

