<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Handle Stripe webhook events
     *
     * Tiếp nhận sự kiện từ Stripe và ủy quyền xử lý cho từng handler riêng.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            default:
                Log::info('Stripe webhook: Unhandled event type', ['type' => $event->type]);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle successful checkout session
     *
     * Khi checkout.session.completed, xác nhận và cập nhật Booking/Payment.
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $bookingId = $session->metadata->booking_id ?? null;
        $paymentId = $session->metadata->payment_id ?? null;

        if (!$bookingId || !$paymentId) {
            Log::error('Stripe webhook: Missing metadata', ['session_id' => $session->id]);
            return;
        }

        $booking = Booking::find($bookingId);
        $payment = Payment::find($paymentId);

        if (!$booking || !$payment) {
            Log::error('Stripe webhook: Booking or Payment not found', [
                'booking_id' => $bookingId,
                'payment_id' => $paymentId,
            ]);
            return;
        }

        // Update payment
        $payment->update([
            'status' => 'success',
            'transaction_id' => $session->payment_intent ?? $session->id,
            'stripe_payment_intent_id' => $session->payment_intent ?? null,
            'payment_date' => now(),
            'stripe_metadata' => json_encode($session),
        ]);

        // Update booking
        $booking->update([
            'status' => 'confirmed',
        ]);

        // Thông báo cho admin về thanh toán thành công
        $this->notifyAdmin($booking, $payment, 'success');

        Log::info('Stripe webhook: Payment successful', [
            'booking_id' => $bookingId,
            'payment_id' => $paymentId,
        ]);
    }

    /**
     * Handle successful payment intent
     *
     * Dự phòng trường hợp Stripe gửi sự kiện payment_intent.succeeded riêng.
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Find payment by payment intent ID
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (!$payment) {
            Log::error('Stripe webhook: Payment not found', ['payment_intent_id' => $paymentIntent->id]);
            return;
        }

        // Update payment if not already updated
        if ($payment->status !== 'success') {
            $payment->update([
                'status' => 'success',
                'transaction_id' => $paymentIntent->id,
                'payment_date' => now(),
                'stripe_metadata' => json_encode($paymentIntent),
            ]);

            // Update booking
            $booking = $payment->booking;
            $booking->update(['status' => 'confirmed']);

            // Thông báo cho admin về thanh toán thành công
            $this->notifyAdmin($booking, $payment, 'success');
        }
    }

    /**
     * Handle failed payment intent
     *
     * Xử lý khi thanh toán thất bại, cập nhật trạng thái và thông báo admin.
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        // Find payment by payment intent ID
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (!$payment) {
            Log::error('Stripe webhook: Payment not found', ['payment_intent_id' => $paymentIntent->id]);
            return;
        }

        // Update payment
        $payment->update([
            'status' => 'failed',
            'transaction_id' => $paymentIntent->id,
            'stripe_metadata' => json_encode($paymentIntent),
        ]);

        // Update booking
        $booking = $payment->booking;
        $booking->update(['status' => 'cancelled']);

        // Thông báo cho admin về thanh toán thất bại
        $this->notifyAdmin($booking, $payment, 'failed');

        Log::info('Stripe webhook: Payment failed', [
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
        ]);
    }

    /**
     * Thông báo cho admin về trạng thái thanh toán (webhook side).
     * Sử dụng session flash tương tự phía BookingController để đồng nhất trải nghiệm.
     */
    protected function notifyAdmin(Booking $booking, Payment $payment, string $status): void
    {
        $booking->loadMissing('user');

        session()->flash('payment_notification', true);
        session()->flash('payment_notification_message', 
            $status === 'success' 
                ? __('common.payment_success_notification', [
                    'customer' => $booking->user->name,
                    'amount' => number_format($payment->amount, 0, ',', '.'),
                ])
                : __('common.payment_failed_notification', [
                    'customer' => $booking->user->name,
                    'amount' => number_format($payment->amount, 0, ',', '.'),
                ])
        );
    }

}
