<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    /**
     * Seed demo bookings & payments to preview admin screens.
     *
     * @return void
     */
    public function run()
    {
        $customer = User::where('email', 'customer@example.com')->first() ?? User::first();

        if (!$customer) {
            $this->command->warn('No users found. Run UserSeeder before BookingSeeder.');
            return;
        }

        $schedules = TourSchedule::with('tour')->orderBy('start_date')->get();

        if ($schedules->isEmpty()) {
            $this->command->warn('No tour schedules found. Run TourSeeder before BookingSeeder.');
            return;
        }

        $bookingTemplates = [
            [
                'status' => 'confirmed',
                'num_participants' => 2,
            ],
            [
                'status' => 'pending',
                'num_participants' => 4,
            ],
            [
                'status' => 'completed',
                'num_participants' => 3,
            ],
        ];

        foreach ($bookingTemplates as $index => $template) {
            $schedule = $schedules[$index % $schedules->count()];
            $bookingDate = $schedule->start_date->copy()->subDays(3)->setTime(10, 0, 0);

            $booking = Booking::updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'tour_schedule_id' => $schedule->id,
                    'booking_date' => $bookingDate,
                ],
                [
                    'num_participants' => $template['num_participants'],
                    'total_price' => $template['num_participants'] * $schedule->price,
                    'status' => $template['status'],
                ]
            );

            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'amount' => $booking->total_price,
                    'payment_method' => Arr::random(['credit_card', 'internet_banking', 'wallet']),
                    'status' => $template['status'] === 'pending' ? 'pending' : 'success',
                    'transaction_id' => Str::upper(Str::random(10)),
                    'payment_date' => $template['status'] === 'pending' ? null : now()->subDays($index),
                ]
            );
        }
    }
}


