<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display the home page with tour schedules and search/filter functionality
     */
    public function home(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'tour_id' => 'nullable|integer|exists:tours,id',
            'departure_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:departure_date',
            'min_price' => 'nullable|integer|min:0',
            'max_price' => 'nullable|integer|min:0|gte:min_price',
            'min_participants' => 'nullable|integer|min:1',
            'max_participants' => 'nullable|integer|min:1|gte:min_participants',
        ]);

        $query = TourSchedule::with('tour')
            ->withCount('bookings')
            ->withSum(['bookings' => function ($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            }], 'num_participants')
            ->where('start_date', '>=', now());

        // Filter by tour (tour type/category)
        if ($request->filled('tour_id')) {
            $query->where('tour_id', $validated['tour_id']);
        }

        // Filter by date range: show tours that overlap with the selected range
        if ($request->filled('departure_date') && $request->filled('end_date')) {
            // Overlapping logic: tour starts on/before end_date AND ends on/after departure_date
            $query->whereDate('start_date', '<=', $validated['end_date'])
                  ->whereDate('end_date', '>=', $validated['departure_date']);
        } elseif ($request->filled('departure_date')) {
            $query->whereDate('end_date', '>=', $validated['departure_date']);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $validated['end_date']);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $validated['min_price']);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $validated['max_price']);
        }

        // Filter by participants
        if ($request->filled('min_participants')) {
            $query->where('max_participants', '>=', $validated['min_participants']);
        }
        if ($request->filled('max_participants')) {
            $query->where('max_participants', '<=', $validated['max_participants']);
        }

        $schedules = $query->orderBy('start_date')->paginate(12)->withQueryString();
        $tours = Tour::orderBy('name')->get();

        // Get min and max price for slider range
        $priceRange = TourSchedule::where('start_date', '>=', now())
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();
        
        $minPrice = $priceRange->min_price ?? 0;
        $maxPrice = $priceRange->max_price ?? max($minPrice, 20000000); // Default max if no data, or at least minPrice

        // Get min and max participants for slider range
        $participantsRange = TourSchedule::where('start_date', '>=', now())
            ->selectRaw('MIN(max_participants) as min_participants, MAX(max_participants) as max_participants')
            ->first();
        
        $minParticipants = $participantsRange->min_participants ?? 1;
        $maxParticipants = $participantsRange->max_participants ?? 50; // Default max if no data

        return view('welcome', compact('schedules', 'tours', 'minPrice', 'maxPrice', 'minParticipants', 'maxParticipants'));
    }

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
    public function tours(Tour $tour)
    {
        $schedules = TourSchedule::where('tour_id', $tour->id)
            ->with('tour')
            ->withCount('bookings')
            ->withSum(['bookings' => function ($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            }], 'num_participants')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->paginate(12);

        return view('customer.pages.tours', compact('tour', 'schedules'));
    }

    /**
     * Display tour details (description, reviews, comments) - accessible by guests
     */
    public function tourDetails(Tour $tour)
    {
        $tour->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        $reviews = $tour->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'reviews_page');

        $comments = $tour->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'comments_page');

        return view('customer.pages.tour-details', compact('tour', 'reviews', 'comments'));
    }
}

