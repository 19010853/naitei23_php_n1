@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Traveloka</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen traveloka-gradient antialiased">
        <div class="max-w-7xl mx-auto px-6 py-10 lg:py-16">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <x-application-logo class="text-2xl text-sky-600" />
                <div class="flex items-center gap-4">
                    @php
                        $currentLocale = app()->getLocale();
                        $flagUrls = config('app.locale_flags', []);
                        $currentFlag = $flagUrls[$currentLocale] ?? $flagUrls['en'] ?? 'https://flagcdn.com/w20/us.png';
                        $fallbackFlag = 'https://flagcdn.com/w20/us.png';
                    @endphp
                    <div class="relative" x-data="{ open: false }">
                        <button type="button"
                                @click="open = !open"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 hover:border-sky-300 bg-white text-slate-700 hover:text-sky-600 transition-colors"
                                aria-haspopup="true"
                                :aria-expanded="open ? 'true' : 'false'"
                                aria-label="{{ __('common.change_language') }}">
                            <img src="{{ $currentFlag }}" alt="{{ strtoupper($currentLocale) }}" width="20" height="15" class="object-cover rounded" style="border: 1px solid #e5e7eb;">
                            <span class="text-sm font-medium">{{ strtoupper($currentLocale) }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open"
                             @click.away="open = false"
                             x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50">
                            <a href="{{ route('locale.switch', 'en') }}" 
                               role="menuitem"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentLocale === 'en' ? 'bg-sky-50 text-sky-600' : '' }}">
                                <img src="{{ $flagUrls['en'] ?? $fallbackFlag }}" alt="EN" width="20" height="15" class="object-cover rounded mr-3" style="border: 1px solid #e5e7eb;">
                                <span>{{ __('common.english') }}</span>
                            </a>
                            <a href="{{ route('locale.switch', 'vi') }}" 
                               role="menuitem"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentLocale === 'vi' ? 'bg-sky-50 text-sky-600' : '' }}">
                                <img src="{{ $flagUrls['vi'] ?? 'https://flagcdn.com/w20/vn.png' }}" alt="VI" width="20" height="15" class="object-cover rounded mr-3" style="border: 1px solid #e5e7eb;">
                                <span>{{ __('common.vietnamese') }}</span>
                            </a>
                            <a href="{{ route('locale.switch', 'ja') }}" 
                               role="menuitem"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentLocale === 'ja' ? 'bg-sky-50 text-sky-600' : '' }}">
                                <img src="{{ $flagUrls['ja'] ?? 'https://flagcdn.com/w20/jp.png' }}" alt="JA" width="20" height="15" class="object-cover rounded mr-3" style="border: 1px solid #e5e7eb;">
                                <span>{{ __('common.japanese') }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="space-x-4 text-sm font-semibold text-slate-600">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sky-600">{{ __('common.dashboard') }}</a>
                            @else
                                <a href="{{ route('login') }}" class="text-slate-600 hover:text-sky-600">{{ __('common.login') }}</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-sky-600 text-white hover:bg-sky-500">
                                        {{ __('common.register') }}
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="mb-16 grid gap-12 lg:grid-cols-2 items-center">
                <div>
                    <p class="text-sm font-semibold text-sky-600 uppercase">{{ __('common.travel_easy') }}</p>
                    <h1 class="mt-4 text-4xl lg:text-5xl font-bold text-slate-900 leading-snug">
                        {{ __('common.discover_vietnam') }} <span class="text-sky-600">Traveloka</span>
                    </h1>
                    <p class="mt-6 text-lg text-slate-600">
                        {{ __('common.welcome_description') }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" class="traveloka-button bg-gradient-to-r from-sky-600 to-blue-600 text-white shadow-xl">
                            {{ __('common.get_started') }}
                        </a>
                        <a href="{{ route('login') }}" class="traveloka-button bg-white text-sky-600 border border-sky-100">
                            {{ __('common.i_have_account') }}
                        </a>
                    </div>
                    <div class="mt-8 flex items-center gap-6 text-sm text-slate-500">
                        <div>
                            <p class="text-2xl font-semibold text-slate-900">4.9/5</p>
                            <p>{{ __('common.user_rating') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-semibold text-slate-900">+1.2k</p>
                            <p>{{ __('common.new_tours_monthly') }}</p>
                        </div>
                    </div>
                </div>
                <div class="glass-card rounded-[32px] p-10 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-100 via-transparent to-white opacity-80"></div>
                    <div class="relative space-y-6 text-slate-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500">{{ __('common.departure') }}</p>
                                <p class="text-xl font-semibold text-slate-900">Hà Giang</p>
                                <p class="text-sm text-slate-500">24 - 26 Nov</p>
                            </div>
                            <span class="px-4 py-2 rounded-full bg-sky-50 text-sky-600 text-sm font-semibold">
                                {{ __('common.confirmed') }}
                            </span>
                        </div>
                        <hr class="border-slate-100">
                        <div>
                            <p class="text-sm text-slate-500 mb-2">{{ __('common.highlights') }}</p>
                            <ul class="space-y-2 text-sm">
                                <li>• Cột mốc Lũng Cú</li>
                                <li>• Đèo Mã Pí Lèng</li>
                                <li>• Phiên chợ Đồng Văn</li>
                            </ul>
                        </div>
                        <div class="mt-6 p-5 rounded-2xl bg-white shadow-inner text-sm">
                            "{{ __('common.testimonial') }}"
                            <p class="mt-3 font-semibold text-slate-800">— Gia Hưng, HCM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Section -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-slate-900 mb-6">{{ __('common.search_tours') }}</h2>
                <form method="GET" action="{{ route('home') }}" id="tour_filter_form" class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <!-- Tour Type Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                {{ __('common.tour_type') }}
                            </label>
                            <select name="tour_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="">{{ __('common.all_tours') }}</option>
                                @foreach($tours as $tour)
                                    <option value="{{ $tour->id }}" {{ request('tour_id') == $tour->id ? 'selected' : '' }}>
                                        {{ $tour->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departure Date -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                {{ __('common.departure_date') }}
                            </label>
                            <input type="date" 
                                   name="departure_date" 
                                   value="{{ request('departure_date') }}"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                {{ __('common.end_date') }}
                            </label>
                            <input type="date" 
                                   name="end_date" 
                                   value="{{ request('end_date') }}"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        </div>

                        <!-- Filter by Price Button -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                {{ __('common.filter_by_price') }}
                            </label>
                            <button type="button" 
                                    id="toggle_price_filter"
                                    aria-expanded="false"
                                    aria-label="{{ __('common.filter_by_price') }}"
                                    class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-white text-slate-700 hover:bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors text-left flex items-center justify-between">
                                <span id="price_filter_display">
                                    @if(request('min_price') || request('max_price'))
                                        {{ number_format((int)(request('min_price') ?: $minPrice), 0, ',', '.') }}₫ - {{ number_format((int)(request('max_price') ?: $maxPrice), 0, ',', '.') }}₫
                                    @else
                                        {{ __('common.select_price_range') }}
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Hidden inputs for form submission -->
                            <input type="hidden" 
                                   id="min_price" 
                                   name="min_price" 
                                   value="{{ request('min_price') ? (int)request('min_price') : '' }}">
                            <input type="hidden" 
                                   id="max_price" 
                                   name="max_price" 
                                   value="{{ request('max_price') ? (int)request('max_price') : '' }}">
                        </div>

                        <!-- Filter by Participant Button -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                {{ __('common.filter_by_participant') }}
                            </label>
                            <button type="button" 
                                    id="toggle_participant_filter"
                                    aria-expanded="false"
                                    aria-label="{{ __('common.filter_by_participant') }}"
                                    class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-white text-slate-700 hover:bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors text-left flex items-center justify-between">
                                <span id="participant_filter_display">
                                    @if(request('min_participants') || request('max_participants'))
                                        {{ request('min_participants') ?: $minParticipants }} - {{ request('max_participants') ?: $maxParticipants }} {{ __('common.people') }}
                                    @else
                                        {{ __('common.select_participant_range') }}
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Hidden inputs for form submission -->
                            <input type="hidden" 
                                   id="min_participants" 
                                   name="min_participants" 
                                   value="{{ request('min_participants') ? (int)request('min_participants') : '' }}">
                            <input type="hidden" 
                                   id="max_participants" 
                                   name="max_participants" 
                                   value="{{ request('max_participants') ? (int)request('max_participants') : '' }}">
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="px-6 py-2 bg-sky-600 text-white rounded-lg font-semibold hover:bg-sky-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>{{ __('common.search') }}
                        </button>
                        <a href="{{ route('home') }}" class="px-6 py-2 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors">
                            <i class="fas fa-times mr-2"></i>{{ __('common.clear_filters') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Price Filter Pop-up -->
            <div id="price_filter_popup" 
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="price_filter_title"
                 class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center"
                 style="display: none;">
                <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-2xl w-full mx-4 relative" @click.stop>
                    <button type="button" 
                            id="close_price_filter"
                            aria-label="{{ __('common.close') }}"
                            class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    
                    <h3 id="price_filter_title" class="text-xl font-bold text-slate-900 mb-6">{{ __('common.filter_by_price') }}</h3>
                    
                    <!-- Price Display Inputs -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex-1">
                            <label class="block text-xs text-slate-500 mb-1">{{ __('common.min_price') }}</label>
                            <input type="text" 
                                   id="min_price_display" 
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-white text-slate-700 font-medium text-center focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                   placeholder="{{ number_format((int)$minPrice, 0, ',', '.') }}₫"
                                   value="{{ request('min_price') ? number_format((int)request('min_price'), 0, ',', '.') . '₫' : number_format((int)$minPrice, 0, ',', '.') . '₫' }}">
                            <p id="min_price_error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                        <span class="text-slate-500 font-semibold mt-6">-</span>
                        <div class="flex-1">
                            <label class="block text-xs text-slate-500 mb-1">{{ __('common.max_price') }}</label>
                            <input type="text" 
                                   id="max_price_display" 
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-white text-slate-700 font-medium text-center focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                   placeholder="{{ number_format((int)$maxPrice, 0, ',', '.') }}₫"
                                   value="{{ request('max_price') ? number_format((int)request('max_price'), 0, ',', '.') . '₫' : number_format((int)$maxPrice, 0, ',', '.') . '₫' }}">
                            <p id="max_price_error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                    </div>

                    <!-- Dual Range Slider -->
                    <div class="relative mb-6">
                        <input type="range" 
                               id="min_price_slider" 
                               name="min_price_slider"
                               min="{{ (int)$minPrice }}" 
                               max="{{ (int)$maxPrice }}" 
                               value="{{ request('min_price') ? (int)request('min_price') : (int)$minPrice }}"
                               step="1000"
                               class="absolute w-full h-2 bg-transparent appearance-none pointer-events-none z-10">
                        <input type="range" 
                               id="max_price_slider" 
                               name="max_price_slider"
                               min="{{ (int)$minPrice }}" 
                               max="{{ (int)$maxPrice }}" 
                               value="{{ request('max_price') ? (int)request('max_price') : (int)$maxPrice }}"
                               step="1000"
                               class="absolute w-full h-2 bg-transparent appearance-none pointer-events-none z-10">
                        
                        <!-- Slider Track -->
                        <div class="relative h-2 bg-slate-200 rounded-full">
                            <div id="price_range_fill" class="absolute h-2 bg-sky-500 rounded-full"></div>
                        </div>
                        
                        <!-- Slider Thumbs -->
                        <div class="absolute top-0 left-0 w-full h-2 pointer-events-none">
                            <div id="min_price_thumb" class="absolute w-5 h-5 bg-white border-2 border-sky-500 rounded-full shadow-lg transform -translate-x-1/2 -translate-y-1.5 cursor-pointer pointer-events-auto"></div>
                            <div id="max_price_thumb" class="absolute w-5 h-5 bg-white border-2 border-sky-500 rounded-full shadow-lg transform -translate-x-1/2 -translate-y-1.5 cursor-pointer pointer-events-auto"></div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" 
                                id="apply_price_filter"
                                class="flex-1 px-6 py-2 bg-sky-600 text-white rounded-lg font-semibold hover:bg-sky-700 transition-colors">
                            {{ __('common.apply') }}
                        </button>
                        <button type="button" 
                                id="clear_price_filter"
                                class="px-6 py-2 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors">
                            {{ __('common.clear') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Participant Filter Pop-up -->
            <div id="participant_filter_popup" 
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="participant_filter_title"
                 class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center"
                 style="display: none;">
                <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-2xl w-full mx-4 relative" @click.stop>
                    <button type="button" 
                            id="close_participant_filter"
                            aria-label="{{ __('common.close') }}"
                            class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    
                    <h3 id="participant_filter_title" class="text-xl font-bold text-slate-900 mb-6">{{ __('common.filter_by_participant') }}</h3>
                    
                    <!-- Participant Display Inputs -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex-1">
                            <label class="block text-xs text-slate-500 mb-1">{{ __('common.min_participants') }}</label>
                            <input type="number" 
                                   id="min_participant_display" 
                                   min="{{ (int)$minParticipants }}"
                                   max="{{ (int)$maxParticipants }}"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-white text-slate-700 font-medium text-center focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                   placeholder="{{ (int)$minParticipants }}"
                                   value="{{ request('min_participants') ? (int)request('min_participants') : (int)$minParticipants }}">
                            <p id="min_participant_error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                        <span class="text-slate-500 font-semibold mt-6">-</span>
                        <div class="flex-1">
                            <label class="block text-xs text-slate-500 mb-1">{{ __('common.max_participants') }}</label>
                            <input type="number" 
                                   id="max_participant_display" 
                                   min="{{ (int)$minParticipants }}"
                                   max="{{ (int)$maxParticipants }}"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-white text-slate-700 font-medium text-center focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                   placeholder="{{ (int)$maxParticipants }}"
                                   value="{{ request('max_participants') ? (int)request('max_participants') : (int)$maxParticipants }}">
                            <p id="max_participant_error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                        <span class="text-slate-500 font-semibold mt-6">{{ __('common.people') }}</span>
                    </div>

                    <!-- Dual Range Slider -->
                    <div class="relative mb-6">
                        <input type="range" 
                               id="min_participant_slider" 
                               name="min_participant_slider"
                               min="{{ (int)$minParticipants }}" 
                               max="{{ (int)$maxParticipants }}" 
                               value="{{ request('min_participants') ? (int)request('min_participants') : (int)$minParticipants }}"
                               step="1"
                               class="absolute w-full h-2 bg-transparent appearance-none pointer-events-none z-10">
                        <input type="range" 
                               id="max_participant_slider" 
                               name="max_participant_slider"
                               min="{{ (int)$minParticipants }}" 
                               max="{{ (int)$maxParticipants }}" 
                               value="{{ request('max_participants') ? (int)request('max_participants') : (int)$maxParticipants }}"
                               step="1"
                               class="absolute w-full h-2 bg-transparent appearance-none pointer-events-none z-10">
                        
                        <!-- Slider Track -->
                        <div class="relative h-2 bg-slate-200 rounded-full">
                            <div id="participant_range_fill" class="absolute h-2 bg-sky-500 rounded-full"></div>
                        </div>
                        
                        <!-- Slider Thumbs -->
                        <div class="absolute top-0 left-0 w-full h-2 pointer-events-none">
                            <div id="min_participant_thumb" class="absolute w-5 h-5 bg-white border-2 border-sky-500 rounded-full shadow-lg transform -translate-x-1/2 -translate-y-1.5 cursor-pointer pointer-events-auto"></div>
                            <div id="max_participant_thumb" class="absolute w-5 h-5 bg-white border-2 border-sky-500 rounded-full shadow-lg transform -translate-x-1/2 -translate-y-1.5 cursor-pointer pointer-events-auto"></div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" 
                                id="apply_participant_filter"
                                class="flex-1 px-6 py-2 bg-sky-600 text-white rounded-lg font-semibold hover:bg-sky-700 transition-colors">
                            {{ __('common.apply') }}
                        </button>
                        <button type="button" 
                                id="clear_participant_filter"
                                class="px-6 py-2 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors">
                            {{ __('common.clear') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tour Schedules Section -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-slate-900 mb-6">{{ __('common.available_schedules') }}</h2>
                
                @if($schedules->total() > 0)
                    <p class="text-slate-600 mb-6">
                        {{ __('common.showing_results', [
                            'from' => $schedules->firstItem(),
                            'to' => $schedules->lastItem(),
                            'total' => $schedules->total()
                        ]) }}
                    </p>
                @endif

                <!-- Tour Schedules Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @forelse($schedules as $schedule)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            @if($schedule->tour->image_url)
                                <div class="h-48 overflow-hidden">
                                    <img src="{{ Str::startsWith($schedule->tour->image_url, ['http://', 'https://']) ? $schedule->tour->image_url : asset($schedule->tour->image_url) }}" 
                                         alt="{{ $schedule->tour->name }}"
                                         width="400" 
                                         height="192"
                                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                </div>
                            @endif
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $schedule->tour->name }}</h3>
                                @if($schedule->tour->location)
                                    <p class="text-sm text-slate-600 mb-4">
                                        <i class="fas fa-map-marker-alt mr-1 text-sky-600"></i>
                                        {{ $schedule->tour->location }}
                                    </p>
                                @endif
                                
                                <div class="space-y-3 mb-4">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">{{ __('common.departure_date') }}:</span>
                                        <span class="font-semibold text-slate-900">{{ $schedule->start_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">{{ __('common.end_date') }}:</span>
                                        <span class="font-semibold text-slate-900">{{ $schedule->end_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">{{ __('common.price') }}:</span>
                                        <span class="text-xl font-bold text-orange-500">
                                            {{ number_format($schedule->price, 0, ',', '.') }} {{ __('common.vnd') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">{{ __('common.max_participants') }}:</span>
                                        <span class="font-semibold text-slate-900">
                                            <i class="fas fa-users mr-1"></i>{{ $schedule->max_participants }} {{ __('common.people') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">{{ __('common.booked_participants') }}:</span>
                                        <span class="font-semibold {{ $schedule->booked_participants >= $schedule->max_participants ? 'text-red-600' : 'text-green-600' }}">
                                            <i class="fas fa-user-check mr-1"></i>{{ $schedule->booked_participants }} / {{ $schedule->max_participants }} {{ __('common.people') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">{{ __('common.available_slots') }}:</span>
                                        <span class="font-semibold {{ $schedule->available_slots > 0 ? 'text-sky-600' : 'text-red-600' }}">
                                            <i class="fas fa-ticket-alt mr-1"></i>{{ $schedule->available_slots }} {{ __('common.slots') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">{{ __('common.days') }}:</span>
                                        <span class="font-semibold text-slate-900">
                                            {{ $schedule->start_date->diffInDays($schedule->end_date) + 1 }} {{ __('common.days') }}
                                        </span>
                                    </div>
                                </div>

                                @php
                                    $bookingRoute = route('customer.tours', ['tour' => $schedule->tour->id, 'schedule' => $schedule->id]);
                                @endphp
                                <div class="flex gap-2">
                                    <a href="{{ route('customer.tour.details', $schedule->tour->id) }}" 
                                       class="flex-1 text-center px-4 py-3 bg-white border-2 border-sky-600 text-sky-600 rounded-lg font-semibold hover:bg-sky-50 transition-colors">
                                        {{ __('common.details') }}
                                    </a>
                                    @if($schedule->isFullyBooked())
                                        <button disabled class="flex-1 px-4 py-3 bg-slate-400 text-white rounded-lg font-semibold cursor-not-allowed">
                                            {{ __('common.fully_booked') }}
                                        </button>
                                    @else
                                        @auth
                                            <a href="{{ $bookingRoute }}" 
                                               class="flex-1 text-center px-4 py-3 bg-sky-600 text-white rounded-lg font-semibold hover:bg-sky-700 transition-colors">
                                                {{ __('common.book_now') }}
                                            </a>
                                        @else
                                            <a href="{{ route('login', ['redirectTo' => $bookingRoute]) }}" 
                                               class="flex-1 text-center px-4 py-3 bg-sky-600 text-white rounded-lg font-semibold hover:bg-sky-700 transition-colors">
                                                {{ __('common.login_to_book') ?: __('common.book_now') }}
                                            </a>
                                        @endauth
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <i class="fas fa-inbox text-6xl text-slate-300 mb-4"></i>
                            <p class="text-slate-600 text-lg mb-4">{{ __('common.no_schedules_found') }}</p>
                            <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700">
                                {{ __('common.clear_filters') }}
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
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const minSlider = document.getElementById('min_price_slider');
                const maxSlider = document.getElementById('max_price_slider');
                const minPriceDisplay = document.getElementById('min_price_display');
                const maxPriceDisplay = document.getElementById('max_price_display');
                const minPriceHidden = document.getElementById('min_price');
                const maxPriceHidden = document.getElementById('max_price');
                const priceRangeFill = document.getElementById('price_range_fill');
                const minThumb = document.getElementById('min_price_thumb');
                const maxThumb = document.getElementById('max_price_thumb');

                const minValue = parseInt(minSlider.min);
                const maxValue = parseInt(minSlider.max);

                // Parse price from formatted string (3.490.000₫ -> 3490000)
                function parsePrice(value) {
                    if (!value) return 0;
                    // Remove all non-digit characters
                    return parseInt(value.toString().replace(/[^\d]/g, '')) || 0;
                }

                // Format number with Vietnamese locale and append currency symbol (e.g., 3490000 -> "3.490.000₫", 500000 -> "500.000₫")
                function formatPrice(value) {
                    // Remove any existing formatting
                    const numValue = parseInt(value) || 0;
                    
                    // Format with Vietnamese locale (e.g., "3.490.000") and append currency symbol
                    const formatted = numValue.toLocaleString('vi-VN', { 
                        minimumFractionDigits: 0, 
                        maximumFractionDigits: 0 
                    });
                    
                    return formatted + '₫';
                }

                // Validate price range
                function validatePriceRange() {
                    const minPriceError = document.getElementById('min_price_error');
                    const maxPriceError = document.getElementById('max_price_error');
                    const minParsed = parsePrice(minPriceDisplay.value);
                    const maxParsed = parsePrice(maxPriceDisplay.value);
                    
                    minPriceError.classList.add('hidden');
                    maxPriceError.classList.add('hidden');
                    
                    let isValid = true;
                    const minDiff = 100000; // Minimum 100,000 VND difference
                    
                    if (minParsed < minValue) {
                        minPriceError.textContent = '{{ __('common.min_price_too_low') ?? "Minimum price is too low" }}';
                        minPriceError.classList.remove('hidden');
                        isValid = false;
                    }
                    
                    if (maxParsed > maxValue) {
                        maxPriceError.textContent = '{{ __('common.max_price_too_high') ?? "Maximum price is too high" }}';
                        maxPriceError.classList.remove('hidden');
                        isValid = false;
                    }
                    
                    if (minParsed >= maxParsed) {
                        minPriceError.textContent = '{{ __('common.min_must_be_less_than_max') ?? "Minimum must be less than maximum" }}';
                        minPriceError.classList.remove('hidden');
                        isValid = false;
                    } else if (maxParsed - minParsed < minDiff) {
                        minPriceError.textContent = '{{ __('common.price_diff_too_small') ?? "Difference must be at least 100,000 VND" }}';
                        minPriceError.classList.remove('hidden');
                        isValid = false;
                    }
                    
                    return isValid;
                }

                // Update slider position and fill
                function updateSlider() {
                    const min = parseInt(minSlider.value);
                    const max = parseInt(maxSlider.value);

                    // Ensure min doesn't exceed max
                    if (min > max) {
                        minSlider.value = max;
                    }

                    // Update display values
                    minPriceDisplay.value = formatPrice(minSlider.value);
                    maxPriceDisplay.value = formatPrice(maxSlider.value);

                    // Update hidden inputs
                    minPriceHidden.value = minSlider.value;
                    maxPriceHidden.value = maxSlider.value;

                    // Calculate percentages for positioning
                    const minPercent = ((min - minValue) / (maxValue - minValue)) * 100;
                    const maxPercent = ((max - minValue) / (maxValue - minValue)) * 100;

                    // Update fill
                    priceRangeFill.style.left = minPercent + '%';
                    priceRangeFill.style.width = (maxPercent - minPercent) + '%';

                    // Update thumb positions
                    minThumb.style.left = minPercent + '%';
                    maxThumb.style.left = maxPercent + '%';
                }

                // Initialize slider
                updateSlider();

                // Update slider from input fields
                function updateSliderFromInput() {
                    const minParsed = parsePrice(minPriceDisplay.value);
                    const maxParsed = parsePrice(maxPriceDisplay.value);
                    
                    // Clamp values to valid range
                    const minClamped = Math.max(minValue, Math.min(maxValue, minParsed));
                    const maxClamped = Math.max(minValue, Math.min(maxValue, maxParsed));
                    
                    // Round to nearest 1000
                    const minRounded = Math.round(minClamped / 1000) * 1000;
                    const maxRounded = Math.round(maxClamped / 1000) * 1000;
                    
                    // Update sliders
                    minSlider.value = minRounded;
                    maxSlider.value = maxRounded;
                    
                    // Update display with formatted values
                    minPriceDisplay.value = formatPrice(minRounded);
                    maxPriceDisplay.value = formatPrice(maxRounded);
                    
                    // Update slider visual
                    updateSlider();
                }

                // Event listeners for input fields
                minPriceDisplay.addEventListener('blur', function() {
                    const minParsed = parsePrice(minPriceDisplay.value);
                    const maxParsed = parsePrice(maxPriceDisplay.value);
                    const minDiff = 100000;
                    
                    if (validatePriceRange()) {
                        updateSliderFromInput();
                    } else {
                        // Auto-correct if possible
                        if (minParsed >= maxParsed) {
                            minPriceDisplay.value = formatPrice(Math.max(minValue, maxParsed - minDiff));
                        } else if (maxParsed - minParsed < minDiff) {
                            minPriceDisplay.value = formatPrice(Math.max(minValue, maxParsed - minDiff));
                        }
                        updateSliderFromInput();
                        validatePriceRange();
                    }
                });

                maxPriceDisplay.addEventListener('blur', function() {
                    const minParsed = parsePrice(minPriceDisplay.value);
                    const maxParsed = parsePrice(maxPriceDisplay.value);
                    const minDiff = 100000;
                    
                    if (validatePriceRange()) {
                        updateSliderFromInput();
                    } else {
                        // Auto-correct if possible
                        if (minParsed >= maxParsed) {
                            maxPriceDisplay.value = formatPrice(Math.min(maxValue, minParsed + minDiff));
                        } else if (maxParsed - minParsed < minDiff) {
                            maxPriceDisplay.value = formatPrice(Math.min(maxValue, minParsed + minDiff));
                        }
                        updateSliderFromInput();
                        validatePriceRange();
                    }
                });

                // Format on input (real-time formatting)
                minPriceDisplay.addEventListener('input', function() {
                    const input = this;
                    const rawValue = input.value;
                    const selectionStart = input.selectionStart;
                    
                    // Count digits before the cursor
                    let digitsBeforeCursor = 0;
                    for (let i = 0; i < selectionStart; i++) {
                        if (/\d/.test(rawValue[i])) digitsBeforeCursor++;
                    }
                    
                    const parsed = parsePrice(rawValue);
                    if (parsed > 0) {
                        const formatted = formatPrice(parsed);
                        input.value = formatted;
                        
                        // Find the new cursor position that matches the same number of digits
                        let newCursor = 0, digitCount = 0;
                        while (newCursor < formatted.length && digitCount < digitsBeforeCursor) {
                            if (/\d/.test(formatted[newCursor])) digitCount++;
                            newCursor++;
                        }
                        input.setSelectionRange(newCursor, newCursor);
                    }
                });

                maxPriceDisplay.addEventListener('input', function() {
                    const input = this;
                    const rawValue = input.value;
                    const selectionStart = input.selectionStart;
                    
                    // Count digits before the cursor
                    let digitsBeforeCursor = 0;
                    for (let i = 0; i < selectionStart; i++) {
                        if (/\d/.test(rawValue[i])) digitsBeforeCursor++;
                    }
                    
                    const parsed = parsePrice(rawValue);
                    if (parsed > 0) {
                        const formatted = formatPrice(parsed);
                        input.value = formatted;
                        
                        // Find the new cursor position that matches the same number of digits
                        let newCursor = 0, digitCount = 0;
                        while (newCursor < formatted.length && digitCount < digitsBeforeCursor) {
                            if (/\d/.test(formatted[newCursor])) digitCount++;
                            newCursor++;
                        }
                        input.setSelectionRange(newCursor, newCursor);
                    }
                });

                // Event listeners for sliders
                minSlider.addEventListener('input', function() {
                    if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
                        minSlider.value = maxSlider.value;
                    }
                    updateSlider();
                });

                maxSlider.addEventListener('input', function() {
                    if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
                        maxSlider.value = minSlider.value;
                    }
                    updateSlider();
                });

                // Make thumbs draggable
                let isDraggingMin = false;
                let isDraggingMax = false;

                minThumb.addEventListener('mousedown', function(e) {
                    isDraggingMin = true;
                    e.preventDefault();
                });

                maxThumb.addEventListener('mousedown', function(e) {
                    isDraggingMax = true;
                    e.preventDefault();
                });

                document.addEventListener('mousemove', function(e) {
                    if (!isDraggingMin && !isDraggingMax) return;

                    const sliderRect = minSlider.getBoundingClientRect();
                    const percent = Math.max(0, Math.min(100, ((e.clientX - sliderRect.left) / sliderRect.width) * 100));
                    const value = Math.round(minValue + (percent / 100) * (maxValue - minValue));
                    const steppedValue = Math.round(value / 1000) * 1000; // Round to nearest 1000

                    if (isDraggingMin) {
                        if (steppedValue <= parseInt(maxSlider.value)) {
                            minSlider.value = steppedValue;
                            updateSlider();
                        }
                    } else if (isDraggingMax) {
                        if (steppedValue >= parseInt(minSlider.value)) {
                            maxSlider.value = steppedValue;
                            updateSlider();
                        }
                    }
                });

                document.addEventListener('mouseup', function() {
                    isDraggingMin = false;
                    isDraggingMax = false;
                });

                // Touch events for mobile
                minThumb.addEventListener('touchstart', function(e) {
                    isDraggingMin = true;
                    e.preventDefault();
                });

                maxThumb.addEventListener('touchstart', function(e) {
                    isDraggingMax = true;
                    e.preventDefault();
                });

                document.addEventListener('touchmove', function(e) {
                    if (!isDraggingMin && !isDraggingMax) return;

                    const sliderRect = minSlider.getBoundingClientRect();
                    const touch = e.touches[0];
                    const percent = Math.max(0, Math.min(100, ((touch.clientX - sliderRect.left) / sliderRect.width) * 100));
                    const value = Math.round(minValue + (percent / 100) * (maxValue - minValue));
                    const steppedValue = Math.round(value / 1000) * 1000;

                    if (isDraggingMin) {
                        if (steppedValue <= parseInt(maxSlider.value)) {
                            minSlider.value = steppedValue;
                            updateSlider();
                        }
                    } else if (isDraggingMax) {
                        if (steppedValue >= parseInt(minSlider.value)) {
                            maxSlider.value = steppedValue;
                            updateSlider();
                        }
                    }
                });

                document.addEventListener('touchend', function() {
                    isDraggingMin = false;
                    isDraggingMax = false;
                });

                // Price Filter Pop-up Toggle
                const togglePriceFilter = document.getElementById('toggle_price_filter');
                const priceFilterPopup = document.getElementById('price_filter_popup');
                const closePriceFilter = document.getElementById('close_price_filter');
                const applyPriceFilter = document.getElementById('apply_price_filter');
                const clearPriceFilter = document.getElementById('clear_price_filter');
                const priceFilterDisplay = document.getElementById('price_filter_display');

                // Toggle pop-up
                function togglePricePopup() {
                    const isOpen = !priceFilterPopup.classList.contains('hidden');
                    if (isOpen) {
                        priceFilterPopup.classList.add('hidden');
                        priceFilterPopup.style.display = 'none';
                        togglePriceFilter.setAttribute('aria-expanded', 'false');
                    } else {
                        priceFilterPopup.classList.remove('hidden');
                        priceFilterPopup.style.display = 'flex';
                        togglePriceFilter.setAttribute('aria-expanded', 'true');
                    }
                }

                if (togglePriceFilter) {
                    togglePriceFilter.addEventListener('click', function(e) {
                        e.preventDefault();
                        togglePricePopup();
                    });
                }

                if (closePriceFilter) {
                    closePriceFilter.addEventListener('click', function() {
                        togglePricePopup();
                    });
                }

                // Close when clicking outside
                if (priceFilterPopup) {
                    priceFilterPopup.addEventListener('click', function(e) {
                        if (e.target === priceFilterPopup) {
                            togglePricePopup();
                        }
                    });
                }

                // Update price filter display
                function updatePriceFilterDisplay() {
                    if (!priceFilterDisplay || !minPriceHidden || !maxPriceHidden) return;
                    
                    const min = parseInt(minPriceHidden.value) || parseInt(minSlider.min);
                    const max = parseInt(maxPriceHidden.value) || parseInt(maxSlider.max);
                    
                    if (minPriceHidden.value && maxPriceHidden.value && 
                        (parseInt(minPriceHidden.value) !== parseInt(minSlider.min) || parseInt(maxPriceHidden.value) !== parseInt(maxSlider.max))) {
                        priceFilterDisplay.textContent = formatPrice(min) + ' - ' + formatPrice(max);
                    } else {
                        priceFilterDisplay.textContent = '{{ __('common.select_price_range') }}';
                    }
                }

                // Apply price filter
                if (applyPriceFilter) {
                    applyPriceFilter.addEventListener('click', function() {
                        // Update slider from input if needed
                        updateSliderFromInput();
                        
                        // Validate before applying
                        if (!validatePriceRange()) {
                            return; // Don't apply if validation fails
                        }
                        
                        minPriceHidden.value = minSlider.value;
                        maxPriceHidden.value = maxSlider.value;
                        updatePriceFilterDisplay();
                        togglePricePopup();
                        // Submit form
                        document.getElementById('tour_filter_form').submit();
                    });
                }

                // Clear price filter
                if (clearPriceFilter) {
                    clearPriceFilter.addEventListener('click', function() {
                        minSlider.value = minSlider.min;
                        maxSlider.value = maxSlider.max;
                        minPriceHidden.value = '';
                        maxPriceHidden.value = '';
                        updateSlider();
                        updatePriceFilterDisplay();
                    });
                }

                // Initialize display
                updatePriceFilterDisplay();

                // Participant Filter Slider
                const minParticipantSlider = document.getElementById('min_participant_slider');
                const maxParticipantSlider = document.getElementById('max_participant_slider');
                const minParticipantDisplay = document.getElementById('min_participant_display');
                const maxParticipantDisplay = document.getElementById('max_participant_display');
                const minParticipantHidden = document.getElementById('min_participants');
                const maxParticipantHidden = document.getElementById('max_participants');
                const participantRangeFill = document.getElementById('participant_range_fill');
                const minParticipantThumb = document.getElementById('min_participant_thumb');
                const maxParticipantThumb = document.getElementById('max_participant_thumb');

                if (minParticipantSlider && maxParticipantSlider) {
                    const minParticipantValue = parseInt(minParticipantSlider.min);
                    const maxParticipantValue = parseInt(minParticipantSlider.max);

                    // Validate participant range
                    function validateParticipantRange() {
                        const minParticipantError = document.getElementById('min_participant_error');
                        const maxParticipantError = document.getElementById('max_participant_error');
                        const minParsed = parseInt(minParticipantDisplay.value) || 0;
                        const maxParsed = parseInt(maxParticipantDisplay.value) || 0;
                        
                        minParticipantError.classList.add('hidden');
                        maxParticipantError.classList.add('hidden');
                        
                        let isValid = true;
                        const minDiff = 1; // Minimum 1 person difference
                        
                        if (minParsed < minParticipantValue) {
                            minParticipantError.textContent = '{{ __('common.min_participant_too_low') ?? "Minimum participants is too low" }}';
                            minParticipantError.classList.remove('hidden');
                            isValid = false;
                        }
                        
                        if (maxParsed > maxParticipantValue) {
                            maxParticipantError.textContent = '{{ __('common.max_participant_too_high') ?? "Maximum participants is too high" }}';
                            maxParticipantError.classList.remove('hidden');
                            isValid = false;
                        }
                        
                        if (minParsed >= maxParsed) {
                            minParticipantError.textContent = '{{ __('common.min_must_be_less_than_max') ?? "Minimum must be less than maximum" }}';
                            minParticipantError.classList.remove('hidden');
                            isValid = false;
                        } else if (maxParsed - minParsed < minDiff) {
                            minParticipantError.textContent = '{{ __('common.participant_diff_too_small') ?? "Difference must be at least 1 person" }}';
                            minParticipantError.classList.remove('hidden');
                            isValid = false;
                        }
                        
                        return isValid;
                    }

                    // Update participant slider from input fields
                    function updateParticipantSliderFromInput() {
                        const minParsed = parseInt(minParticipantDisplay.value) || minParticipantValue;
                        const maxParsed = parseInt(maxParticipantDisplay.value) || maxParticipantValue;
                        
                        // Clamp values to valid range
                        const minClamped = Math.max(minParticipantValue, Math.min(maxParticipantValue, minParsed));
                        const maxClamped = Math.max(minParticipantValue, Math.min(maxParticipantValue, maxParsed));
                        
                        // Update sliders
                        minParticipantSlider.value = minClamped;
                        maxParticipantSlider.value = maxClamped;
                        
                        // Update display values
                        minParticipantDisplay.value = minClamped;
                        maxParticipantDisplay.value = maxClamped;
                        
                        // Update slider visual
                        updateParticipantSlider();
                    }

                    // Update participant slider position and fill
                    function updateParticipantSlider() {
                        const min = parseInt(minParticipantSlider.value);
                        const max = parseInt(maxParticipantSlider.value);

                        // Ensure min doesn't exceed max
                        if (min > max) {
                            minParticipantSlider.value = max;
                        }

                        // Update display values
                        minParticipantDisplay.value = minParticipantSlider.value;
                        maxParticipantDisplay.value = maxParticipantSlider.value;

                        // Update hidden inputs
                        minParticipantHidden.value = minParticipantSlider.value;
                        maxParticipantHidden.value = maxParticipantSlider.value;

                        // Calculate percentages for positioning
                        const minPercent = ((min - minParticipantValue) / (maxParticipantValue - minParticipantValue)) * 100;
                        const maxPercent = ((max - minParticipantValue) / (maxParticipantValue - minParticipantValue)) * 100;

                        // Update fill
                        participantRangeFill.style.left = minPercent + '%';
                        participantRangeFill.style.width = (maxPercent - minPercent) + '%';

                        // Update thumb positions
                        minParticipantThumb.style.left = minPercent + '%';
                        maxParticipantThumb.style.left = maxPercent + '%';
                    }

                    // Initialize participant slider
                    updateParticipantSlider();

                    // Event listeners for input fields
                    minParticipantDisplay.addEventListener('blur', function() {
                        const minParsed = parseInt(minParticipantDisplay.value) || minParticipantValue;
                        const maxParsed = parseInt(maxParticipantDisplay.value) || maxParticipantValue;
                        const minDiff = 1;
                        
                        if (validateParticipantRange()) {
                            updateParticipantSliderFromInput();
                        } else {
                            // Auto-correct if possible
                            if (minParsed >= maxParsed) {
                                minParticipantDisplay.value = Math.max(minParticipantValue, maxParsed - minDiff);
                            } else if (maxParsed - minParsed < minDiff) {
                                minParticipantDisplay.value = Math.max(minParticipantValue, maxParsed - minDiff);
                            }
                            updateParticipantSliderFromInput();
                            validateParticipantRange();
                        }
                    });

                    maxParticipantDisplay.addEventListener('blur', function() {
                        const minParsed = parseInt(minParticipantDisplay.value) || minParticipantValue;
                        const maxParsed = parseInt(maxParticipantDisplay.value) || maxParticipantValue;
                        const minDiff = 1;
                        
                        if (validateParticipantRange()) {
                            updateParticipantSliderFromInput();
                        } else {
                            // Auto-correct if possible
                            if (minParsed >= maxParsed) {
                                maxParticipantDisplay.value = Math.min(maxParticipantValue, minParsed + minDiff);
                            } else if (maxParsed - minParsed < minDiff) {
                                maxParticipantDisplay.value = Math.min(maxParticipantValue, minParsed + minDiff);
                            }
                            updateParticipantSliderFromInput();
                            validateParticipantRange();
                        }
                    });

                    // Event listeners for sliders
                    minParticipantSlider.addEventListener('input', function() {
                        if (parseInt(minParticipantSlider.value) > parseInt(maxParticipantSlider.value)) {
                            minParticipantSlider.value = maxParticipantSlider.value;
                        }
                        updateParticipantSlider();
                    });

                    maxParticipantSlider.addEventListener('input', function() {
                        if (parseInt(maxParticipantSlider.value) < parseInt(minParticipantSlider.value)) {
                            maxParticipantSlider.value = minParticipantSlider.value;
                        }
                        updateParticipantSlider();
                    });

                    // Make thumbs draggable
                    let isDraggingMinParticipant = false;
                    let isDraggingMaxParticipant = false;

                    minParticipantThumb.addEventListener('mousedown', function(e) {
                        isDraggingMinParticipant = true;
                        e.preventDefault();
                    });

                    maxParticipantThumb.addEventListener('mousedown', function(e) {
                        isDraggingMaxParticipant = true;
                        e.preventDefault();
                    });

                    document.addEventListener('mousemove', function(e) {
                        if (!isDraggingMinParticipant && !isDraggingMaxParticipant) return;

                        const sliderRect = minParticipantSlider.getBoundingClientRect();
                        const percent = Math.max(0, Math.min(100, ((e.clientX - sliderRect.left) / sliderRect.width) * 100));
                        const value = Math.round(minParticipantValue + (percent / 100) * (maxParticipantValue - minParticipantValue));

                        if (isDraggingMinParticipant) {
                            if (value <= parseInt(maxParticipantSlider.value)) {
                                minParticipantSlider.value = value;
                                updateParticipantSlider();
                            }
                        } else if (isDraggingMaxParticipant) {
                            if (value >= parseInt(minParticipantSlider.value)) {
                                maxParticipantSlider.value = value;
                                updateParticipantSlider();
                            }
                        }
                    });

                    document.addEventListener('mouseup', function() {
                        isDraggingMinParticipant = false;
                        isDraggingMaxParticipant = false;
                    });
                }

                // Participant Filter Pop-up Toggle
                const toggleParticipantFilter = document.getElementById('toggle_participant_filter');
                const participantFilterPopup = document.getElementById('participant_filter_popup');
                const closeParticipantFilter = document.getElementById('close_participant_filter');
                const applyParticipantFilter = document.getElementById('apply_participant_filter');
                const clearParticipantFilter = document.getElementById('clear_participant_filter');
                const participantFilterDisplay = document.getElementById('participant_filter_display');

                // Toggle pop-up
                function toggleParticipantPopup() {
                    const isOpen = !participantFilterPopup.classList.contains('hidden');
                    if (isOpen) {
                        participantFilterPopup.classList.add('hidden');
                        participantFilterPopup.style.display = 'none';
                        toggleParticipantFilter.setAttribute('aria-expanded', 'false');
                    } else {
                        participantFilterPopup.classList.remove('hidden');
                        participantFilterPopup.style.display = 'flex';
                        toggleParticipantFilter.setAttribute('aria-expanded', 'true');
                    }
                }

                if (toggleParticipantFilter) {
                    toggleParticipantFilter.addEventListener('click', function(e) {
                        e.preventDefault();
                        toggleParticipantPopup();
                    });
                }

                if (closeParticipantFilter) {
                    closeParticipantFilter.addEventListener('click', function() {
                        toggleParticipantPopup();
                    });
                }

                // Close when clicking outside
                if (participantFilterPopup) {
                    participantFilterPopup.addEventListener('click', function(e) {
                        if (e.target === participantFilterPopup) {
                            toggleParticipantPopup();
                        }
                    });
                }

                // Update participant filter display
                function updateParticipantFilterDisplay() {
                    if (!participantFilterDisplay || !minParticipantHidden || !maxParticipantHidden) return;
                    
                    const min = parseInt(minParticipantHidden.value) || parseInt(minParticipantSlider.min);
                    const max = parseInt(maxParticipantHidden.value) || parseInt(maxParticipantSlider.max);
                    
                    if (minParticipantHidden.value && maxParticipantHidden.value && 
                        (parseInt(minParticipantHidden.value) !== parseInt(minParticipantSlider.min) || parseInt(maxParticipantHidden.value) !== parseInt(maxParticipantSlider.max))) {
                        participantFilterDisplay.textContent = min + ' - ' + max + ' {{ __('common.people') }}';
                    } else {
                        participantFilterDisplay.textContent = '{{ __('common.select_participant_range') }}';
                    }
                }

                // Apply participant filter
                if (applyParticipantFilter) {
                    applyParticipantFilter.addEventListener('click', function() {
                        // Update slider from input if needed
                        updateParticipantSliderFromInput();
                        
                        // Validate before applying
                        if (!validateParticipantRange()) {
                            return; // Don't apply if validation fails
                        }
                        
                        minParticipantHidden.value = minParticipantSlider.value;
                        maxParticipantHidden.value = maxParticipantSlider.value;
                        updateParticipantFilterDisplay();
                        toggleParticipantPopup();
                        // Submit form
                        document.getElementById('tour_filter_form').submit();
                    });
                }

                // Clear participant filter
                if (clearParticipantFilter) {
                    clearParticipantFilter.addEventListener('click', function() {
                        minParticipantSlider.value = minParticipantSlider.min;
                        maxParticipantSlider.value = maxParticipantSlider.max;
                        minParticipantHidden.value = '';
                        maxParticipantHidden.value = '';
                        updateParticipantSlider();
                        updateParticipantFilterDisplay();
                    });
                }

                // Initialize participant display
                if (minParticipantSlider && maxParticipantSlider) {
                    updateParticipantFilterDisplay();
                }
            });
        </script>

        <style>
            /* Hide default range input appearance */
            input[type="range"] {
                -webkit-appearance: none;
                appearance: none;
                background: transparent;
                cursor: pointer;
            }

            input[type="range"]::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 20px;
                height: 20px;
                background: white;
                border: 2px solid #0ea5e9;
                border-radius: 50%;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            input[type="range"]::-moz-range-thumb {
                width: 20px;
                height: 20px;
                background: white;
                border: 2px solid #0ea5e9;
                border-radius: 50%;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            input[type="range"]::-webkit-slider-track {
                background: transparent;
            }

            input[type="range"]::-moz-range-track {
                background: transparent;
            }
        </style>
    </body>
</html>
