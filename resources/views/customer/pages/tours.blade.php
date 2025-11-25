@extends('customer.layouts.app')

@section('title', $tour->name . ' - ' . __('common.brand'))

@section('hero')
<section class="bg-gradient-to-r from-orange-500 to-orange-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-4">{{ $tour->name }}</h1>
        <div class="flex items-center justify-center space-x-2 text-orange-100">
            <a href="{{ route('customer.categories') }}" class="hover:text-white">{{ __('common.home') }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('customer.categories') }}" class="hover:text-white">{{ __('common.tour_list') }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span>{{ $tour->name }}</span>
        </div>
    </div>
</section>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Tour Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($tour->image_url)
            <div>
                <img src="{{ asset($tour->image_url) }}" alt="{{ $tour->name }}" 
                     class="w-full h-64 object-cover rounded-lg">
            </div>
            @endif
            <div>
                <h2 class="text-2xl font-bold mb-4">{{ $tour->name }}</h2>
                <p class="text-gray-600 mb-4">
                    <i class="fas fa-map-marker-alt mr-2 text-orange-500"></i>
                    {{ $tour->location }}
                </p>
                @if($tour->description)
                <p class="text-gray-700 mb-4">{{ $tour->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-gray-50 rounded-lg p-4 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <span>{{ __('common.showing') }} {{ $schedules->firstItem() ?? 0 }}-{{ $schedules->lastItem() ?? 0 }} {{ __('common.of') }} {{ $schedules->total() }} {{ __('common.schedules') }}</span>
        </div>
    </div>

    <!-- Tour Schedules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        @forelse($schedules as $schedule)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ __('common.departure_date') }}</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $schedule->start_date->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-1">{{ __('common.end_date') }}</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $schedule->end_date->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600">{{ __('common.price') }}:</span>
                        <span class="text-2xl font-bold text-orange-500">
                            {{ number_format($schedule->price, 0, '.', ',') }} {{ __('common.vnd') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>
                            <i class="fas fa-users mr-1"></i>
                            {{ __('common.max') }} {{ $schedule->max_participants }} {{ __('common.people') }}
                        </span>
                        <span>
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $schedule->start_date->diffInDays($schedule->end_date) + 1 }} {{ __('common.days') }}
                        </span>
                    </div>
                </div>
                @php
                    $bookingRoute = route('customer.tours', ['tour' => $tour->id, 'schedule' => $schedule->id]);
                @endphp
                @auth
                    <a href="{{ $bookingRoute }}"
                       class="block text-center w-full mt-4 bg-orange-500 text-white px-6 py-3 rounded-md font-semibold hover:bg-orange-600 transition-colors">
                        {{ __('common.book_now') }}
                    </a>
                @else
                    <a href="{{ route('login', ['redirectTo' => $bookingRoute]) }}"
                       class="block text-center w-full mt-4 bg-orange-500 text-white px-6 py-3 rounded-md font-semibold hover:bg-orange-600 transition-colors">
                        {{ __('common.book_now') }}
                    </a>
                @endauth
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-600 text-lg mb-4">{{ __('common.no_schedules_for_tour') }}</p>
            <a href="{{ route('customer.categories') }}" class="inline-block bg-orange-500 text-white px-6 py-2 rounded-md hover:bg-orange-600">
                {{ __('common.back_to_tour_list') }}
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($schedules->hasPages())
    <div class="flex justify-center">
        {{ $schedules->links() }}
    </div>
    @endif
</div>
@endsection


