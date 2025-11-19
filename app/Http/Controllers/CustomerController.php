<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tour;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display the categories page for customers
     */
    public function categories()
    {
        $categories = Category::withCount('tours')
            ->orderBy('name')
            ->get();

        return view('customer.pages.categories', compact('categories'));
    }

    /**
     * Display tours in a specific category
     */
    public function tours($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $tours = Tour::where('category_id', $categoryId)
            ->with('category')
            ->withCount(['reviews', 'likes'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(12);

        return view('customer.pages.tours', compact('category', 'tours'));
    }
}

