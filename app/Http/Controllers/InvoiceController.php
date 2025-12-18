<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Download invoice PDF (English only, as before).
     *
     * - Chỉ chủ sở hữu payment mới được tải (authorize qua policy).
     * - Luôn dùng locale en để bố cục/format invoice ổn định.
     */
    public function download(Request $request, Payment $payment)
    {
        // Authorize: only the payment owner can download
        $this->authorize('downloadInvoice', $payment);

        // Force English for invoice
        app()->setLocale('en');

        try {
            $pdf = $this->generatePdf($payment);

            return $pdf->download($this->getInvoiceFileName($payment));
        } catch (\Exception $e) {
            Log::error('Invoice generation error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', __('common.invoice_generation_error'));
        }
    }

    /**
     * Generate PDF for payment (English only).
     *
     * - Eager load đầy đủ booking, tour, customer để tránh N+1.
     * - Dùng view 'customer.pdf.invoice' làm template.
     */
    protected function generatePdf(Payment $payment): \Barryvdh\DomPDF\PDF
    {
        // Ensure English locale
        app()->setLocale('en');

        $payment->load(['booking.tourSchedule.tour', 'booking.user']);

        $data = [
            'payment' => $payment,
            'booking' => $payment->booking,
            'tour' => $payment->booking->tourSchedule->tour,
            'tourSchedule' => $payment->booking->tourSchedule,
            'customer' => $payment->booking->user,
            'company' => [
                'name' => config('app.name', 'Tour Booking System'),
                'address' => config('app.company_address', '123 Main Street, City, Country'),
                'phone' => config('app.company_phone', '+84 123 456 789'),
                'email' => config('app.company_email', 'info@example.com'),
                'tax_id' => config('app.company_tax_id', 'TAX-123456'),
            ],
        ];

        $pdf = Pdf::loadView('customer.pdf.invoice', $data);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setOption('enable-font-subsetting', true);
        $pdf->setOption('isUnicode', true);
        $pdf->setOption('dpi', 96);

        return $pdf;
    }

    /**
     * Get invoice file name (no language suffix).
     *
     * Đặt tên file theo invoice_id cho dễ tìm kiếm / tra cứu.
     */
    protected function getInvoiceFileName(Payment $payment): string
    {
        return 'Invoice-' . $payment->invoice_id . '.pdf';
    }
}
