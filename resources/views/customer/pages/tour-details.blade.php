@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $tour->name }} - {{ __('common.brand') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 antialiased">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-sky-600">
                        {{ __('common.brand') }}
                    </a>
                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-slate-600 hover:text-sky-600">{{ __('common.dashboard') }}</a>
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
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-6 py-12">
            <!-- Breadcrumb -->
            <nav aria-label="Breadcrumb" class="mb-6 text-sm text-slate-600">
                <ol class="flex items-center">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-sky-600">{{ __('common.home') }}</a>
                    </li>
                    <li>
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                    </li>
                    <li aria-current="page">
                        {{ $tour->name }}
                    </li>
                </ol>
            </nav>

            <!-- Tour Info Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    <div>
                        <img src="{{ Str::startsWith($tour->image_url, ['http://', 'https://']) ? $tour->image_url : asset($tour->image_url) }}" 
                             alt="{{ $tour->name }}" 
                             width="800" 
                             height="320"
                             class="w-full h-80 object-cover rounded-lg">
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 mb-4">{{ $tour->name }}</h1>
                        <p class="text-slate-600 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-sky-600"></i>
                            {{ $tour->location }}
                        </p>
                        
                        <!-- Average Rating -->
                        @if($tour->reviews_count > 0)
                        <div class="mb-4 flex items-center gap-2">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($tour->reviews_avg_rating))
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @else
                                        <i class="far fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-lg font-semibold text-slate-900">
                                {{ number_format($tour->reviews_avg_rating, 1) }}/5
                            </span>
                            <span class="text-slate-500">
                                ({{ $tour->reviews_count }} {{ __('common.reviews') }})
                            </span>
                        </div>
                        @else
                        <div class="mb-4 text-slate-500">
                            <i class="far fa-star mr-1"></i>
                            {{ __('common.no_reviews_yet') }}
                        </div>
                        @endif

                        @if($tour->description)
                        <div class="prose max-w-none">
                            <p class="text-slate-700 leading-relaxed">{{ $tour->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">
                    <i class="fas fa-star mr-2 text-yellow-400"></i>
                    {{ __('common.reviews') }} ({{ $tour->reviews_count }})
                </h2>

                @if($reviews->count() > 0)
                    <div class="space-y-6">
                        @foreach($reviews as $review)
                            <div class="border-b border-slate-200 pb-6 last:border-0 last:pb-0">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-semibold">
                                            {{ strtoupper(substr($review->user->name ?? __('common.anonymous'), 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                {{ $review->user->name ?? __('common.anonymous') }}
                                            </p>
                                            <p class="text-sm text-slate-500">
                                                {{ $review->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                                            @else
                                                <i class="far fa-star text-gray-300 text-sm"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($review->content)
                                    <p class="text-slate-700 leading-relaxed">{{ $review->content }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($reviews->hasPages())
                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-comment-alt text-4xl text-slate-300 mb-4"></i>
                        <p class="text-slate-500">{{ __('common.no_reviews_yet') }}</p>
                    </div>
                @endif

                @guest
                    <div class="mt-6 p-4 bg-sky-50 border border-sky-200 rounded-lg">
                        <p class="text-sm text-sky-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ __('common.login_to_review') }}
                        </p>
                    </div>
                @endguest
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">
                    <i class="fas fa-comments mr-2 text-sky-600"></i>
                    {{ __('common.comments') }} ({{ $tour->comments()->count() }})
                </h2>

                @if($comments->count() > 0)
                    <div class="space-y-6 mb-6">
                        @foreach($comments as $comment)
                            <div class="border-b border-slate-200 pb-6 last:border-0 last:pb-0">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($comment->user->name ?? __('common.anonymous'), 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <p class="font-semibold text-slate-900">
                                                {{ $comment->user->name ?? __('common.anonymous') }}
                                            </p>
                                            <span class="text-sm text-slate-500">
                                                {{ $comment->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        <p class="text-slate-700 leading-relaxed">{{ $comment->body }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($comments->hasPages())
                        <div class="mb-6">
                            {{ $comments->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-comment text-4xl text-slate-300 mb-4"></i>
                        <p class="text-slate-500">{{ __('common.no_comments_yet') }}</p>
                    </div>
                @endif

                @guest
                    <div class="p-4 bg-sky-50 border border-sky-200 rounded-lg">
                        <p class="text-sm text-sky-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ __('common.login_to_comment') }}
                        </p>
                    </div>
                @endguest
            </div>

            <!-- Back Button -->
            <div class="mt-8">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-6 py-3 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('common.back_to_home') }}
                </a>
            </div>
        </main>
    </body>
</html>




