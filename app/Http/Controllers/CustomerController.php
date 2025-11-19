<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display the tours page for customers
     */
    public function categories()
    {
        $tours = Tour::withCount('schedules')
            ->orderBy('name')
            ->get();

        return view('customer.pages.categories', compact('tours'));
    }

    /**
     * Display tour schedules for a specific tour
     */
    public function tours($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        $schedules = TourSchedule::where('tour_id', $tourId)
            ->with('tour')
            ->withCount('bookings')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->paginate(12);

        return view('customer.pages.tours', compact('tour', 'schedules'));
    }
}

