<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TourSchedule;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class BookingController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show booking form
     *
     * Hiển thị form đặt tour cho lịch cụ thể, đồng thời chặn trường hợp đã full chỗ.
     */
    public function show(Request $request, TourSchedule $schedule)
    {
        $schedule->load('tour');
        
        // Check if schedule is fully booked
        if ($schedule->isFullyBooked()) {
            return redirect()->back()->with('error', __('common.fully_booked'));
        }

        return view('customer.pages.booking', compact('schedule'));
    }

    /**
     * Process booking and create Stripe checkout session
     *
     * Xử lý tạo Booking + Payment và khởi tạo phiên thanh toán Stripe.
     */
    public function store(Request $request, TourSchedule $schedule)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'num_participants' => [
                'required',
                'integer',
                'min:1',
                'max:' . $schedule->available_slots,
            ],
        ], [
            'num_participants.max' => __('common.exceeds_available_slots', ['slots' => $schedule->available_slots]),
        ]);

        // Calculate total price in VND
        $totalPriceVND = $schedule->price * $validated['num_participants'];

        // Get exchange rate from API or fallback to config
        $exchangeRate = config('services.exchange_rate.enabled', true)
            ? ExchangeRateService::getRate()
            : config('services.stripe.vnd_to_usd_rate', 25000);
        
        // Convert VND to USD for Stripe
        $totalPriceUSD = $totalPriceVND / $exchangeRate;

        // Create booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'tour_schedule_id' => $schedule->id,
            'num_participants' => $validated['num_participants'],
            'total_price' => $totalPriceVND, // Store VND price in database
            'status' => 'pending',
            'booking_date' => now(),
        ]);

        // Create payment record (store VND amount)
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $totalPriceVND, // Store VND amount
            'payment_method' => 'stripe',
            'status' => 'pending',
        ]);

        try {
            // Create Stripe Checkout Session with USD
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $schedule->tour->name,
                            'description' => __('common.booking_for') . ' ' . $schedule->tour->name . ' - ' . 
                                            $schedule->start_date->format('d/m/Y') . ' - ' . 
                                            $schedule->end_date->format('d/m/Y') . 
                                            ' (' . number_format($totalPriceVND, 0, ',', '.') . ' VND)',
                        ],
                        'unit_amount' => (int)round($totalPriceUSD * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('booking.success', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('booking.cancel', ['booking' => $booking->id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                ],
                'customer_email' => $user->email,
            ]);

            // Update payment with Stripe session ID
            $payment->update([
                'stripe_session_id' => $session->id,
            ]);

            // Redirect to Stripe Checkout
            return redirect($session->url);

        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout error', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
            ]);

            // Update booking and payment status to failed
            $booking->update(['status' => 'cancelled']);
            $payment->update(['status' => 'failed']);

            return redirect()->back()
                ->with('error', __('common.payment_processing_error'))
                ->withInput();
        }
    }

    /**
     * Handle successful payment
     *
     * Xác nhận trạng thái thanh toán từ Stripe và cập nhật Booking/Payment tương ứng.
     */
    public function success(Request $request, Booking $booking)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('dashboard')
                ->with('error', __('common.invalid_payment_session'));
        }

        try {
            $session = Session::retrieve($sessionId);

            // Verify the session belongs to this booking
            if ($session->metadata->booking_id != $booking->id) {
                return redirect()->route('dashboard')
                    ->with('error', __('common.invalid_payment_session'));
            }

            // Get payment record
            $payment = Payment::where('stripe_session_id', $sessionId)->first();

            if (!$payment) {
                return redirect()->route('dashboard')
                    ->with('error', __('common.payment_not_found'));
            }

            // Process payment based on session status
            if ($session->payment_status === 'paid') {
                // Payment successful - update records
                if ($payment->status !== 'success') {
                    $payment->update([
                        'status' => 'success',
                        'transaction_id' => $session->payment_intent ?? $session->id,
                        'stripe_payment_intent_id' => $session->payment_intent ?? null,
                        'payment_date' => now(),
                        'stripe_metadata' => json_encode($session),
                    ]);

                    // Update booking status
                    $booking->update(['status' => 'confirmed']);

                    // Thông báo cho admin về thanh toán thành công (hiển thị khi vào màn payments)
                    $this->notifyAdmin($booking, $payment, 'success');
                }

                return view('customer.pages.booking-success', compact('booking', 'payment'));
            } elseif ($session->payment_status === 'unpaid') {
                // Payment not completed yet
                return view('customer.pages.booking-success', compact('booking'))
                    ->with('message', __('common.payment_processing'));
            } else {
                // Payment failed or cancelled
                $payment->update([
                    'status' => 'failed',
                    'stripe_metadata' => json_encode($session),
                ]);
                $booking->update(['status' => 'cancelled']);

                return redirect()->route('booking.cancel', $booking);
            }

        } catch (ApiErrorException $e) {
            Log::error('Stripe session retrieval error', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);

            return redirect()->route('dashboard')
                ->with('error', __('common.payment_verification_error'));
        }
    }

    /**
     * Thông báo cho admin về trạng thái thanh toán.
     * Dùng session flash để hiển thị thông báo khi admin mở trang payments.
     */
    protected function notifyAdmin(Booking $booking, Payment $payment, string $status): void
    {
        // Đảm bảo đã load user để lấy tên khách
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

    /**
     * Handle cancelled payment
     */
    public function cancel(Booking $booking)
    {
        // Update booking status
        $booking->update(['status' => 'cancelled']);

        // Update payment status
        $payment = $booking->payments()->latest()->first();
        if ($payment && $payment->status === 'pending') {
            $payment->update(['status' => 'failed']);
        }

        return view('customer.pages.booking-cancel', compact('booking'));
    }
}
