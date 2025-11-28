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
                        @php
                            $reviewsCount = $tour->reviews_count ?? $tour->reviews()->count() ?? 0;
                            $hasReviews = $reviewsCount > 0;
                        @endphp
                        @if($hasReviews)
                        <div class="mb-4 flex items-center gap-2">
                            <div class="flex items-center">
                                @php
                                    $starsDisplay = $tour->stars_display ?? 0;
                                    $avgRating = $tour->average_rating ?? $tour->reviews_avg_rating ?? 0;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $starsDisplay)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @else
                                        <i class="far fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-lg font-semibold text-slate-900">
                                {{ number_format($avgRating, 1) }}/5
                            </span>
                            <span class="text-slate-500">
                                ({{ $reviewsCount }} {{ __('common.reviews') }})
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

            <!-- Write Review Section (for authenticated users) -->
            @auth
                @if(!$userReview)
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8" id="writeReviewSection">
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">
                            <i class="fas fa-edit mr-2 text-sky-600"></i>
                            {{ __('common.write_review') }}
                        </h2>
                        <form id="reviewForm" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ __('common.rating') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-2" id="ratingStars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" 
                                                class="rating-star-btn flex items-center justify-center text-2xl transition-colors"
                                                data-rating="{{ $i }}"
                                                title="{{ $i }}">
                                            <i class="far fa-star text-gray-300 hover:text-yellow-400"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingInput" required>
                                <p class="text-sm text-slate-500 mt-1" id="ratingText">{{ __('common.select_rating') }}</p>
                            </div>
                            <div>
                                <label for="reviewContent" class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ __('common.review_content') }}
                                </label>
                                <textarea name="content" 
                                          id="reviewContent" 
                                          rows="4" 
                                          class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                          placeholder="{{ __('common.write_your_review_here') }}"></textarea>
                            </div>
                            <button type="submit" 
                                    class="px-6 py-2 bg-sky-600 text-white rounded-lg font-semibold hover:bg-sky-700 transition-colors">
                                {{ __('common.submit_review') }}
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Edit Review Section -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8" id="editReviewSection">
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">
                            <i class="fas fa-star mr-2 text-yellow-400"></i>
                            {{ __('common.your_review') }}
                        </h2>
                        <div class="border border-slate-200 rounded-lg p-4 mb-4" id="userReviewDisplay">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $userReview->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-0.5" id="userReviewRating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $userReview->rating)
                                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                                            @else
                                                <i class="far fa-star text-gray-300 text-sm"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm font-semibold">{{ number_format($userReview->rating, 0) }}/5</span>
                                </div>
                            </div>
                            <p class="text-slate-700 mb-4" id="userReviewContent">{{ $userReview->content ?? __('common.no_content') }}</p>
                            <div class="flex gap-2">
                                <button type="button" 
                                        onclick="editReview()" 
                                        class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700 transition-colors">
                                    <i class="fas fa-edit mr-1"></i> {{ __('common.edit') }}
                                </button>
                                <button type="button" 
                                        onclick="deleteReview({{ $userReview->id }})" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash mr-1"></i> {{ __('common.delete') }}
                                </button>
                            </div>
                        </div>
                        <form id="editReviewForm" class="space-y-4 hidden">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ __('common.rating') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-2" id="editRatingStars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" 
                                                class="rating-star-btn flex items-center justify-center text-2xl transition-colors"
                                                data-rating="{{ $i }}"
                                                title="{{ $i }}">
                                            <i class="far fa-star text-gray-300 hover:text-yellow-400"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="editRatingInput" value="{{ $userReview->rating }}" required>
                                <p class="text-sm text-slate-500 mt-1" id="editRatingText">{{ number_format($userReview->rating, 0) }}/5</p>
                            </div>
                            <div>
                                <label for="editReviewContent" class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ __('common.review_content') }}
                                </label>
                                <textarea name="content" 
                                          id="editReviewContent" 
                                          rows="4" 
                                          class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ $userReview->content }}</textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" 
                                        class="px-6 py-2 bg-sky-600 text-white rounded-lg font-semibold hover:bg-sky-700 transition-colors">
                                    {{ __('common.update_review') }}
                                </button>
                                <button type="button" 
                                        onclick="cancelEditReview()" 
                                        class="px-6 py-2 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors">
                                    {{ __('common.cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @endauth

            <!-- Reviews Section -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">
                    <i class="fas fa-star mr-2 text-yellow-400"></i>
                    {{ __('common.all_reviews') }} ({{ $tour->reviews_count }})
                </h2>

                <div id="reviewsContainer" class="space-y-6">
                    @if($reviews->count() > 0)
                        @foreach($reviews as $review)
                            @include('customer.components.review-item', ['review' => $review, 'currentUser' => $user ?? null])
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-comment-alt text-4xl text-slate-300 mb-4"></i>
                            <p class="text-slate-500">{{ __('common.no_reviews_yet') }}</p>
                        </div>
                    @endif
                </div>

                @if($reviews->hasPages())
                    <div class="mt-6">
                        {{ $reviews->links() }}
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

            <!-- Back Button -->
            <div class="mt-8">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-6 py-3 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('common.back_to_home') }}
                </a>
            </div>
        </main>

        @auth
        <script>
            const tourId = {{ $tour->id }};
            const csrfToken = '{{ csrf_token() }}';

            // Rating Stars Handler - Only integer ratings (1-5)
            function initRatingStars(containerId, inputId, textId, initialRating = 0) {
                const container = document.getElementById(containerId);
                const input = document.getElementById(inputId);
                const text = document.getElementById(textId);
                
                // Check if required elements exist
                if (!container || !input || !text) {
                    console.error('Rating stars elements not found:', { containerId, inputId, textId });
                    return;
                }
                
                const starButtons = container.querySelectorAll('.rating-star-btn');
                
                if (starButtons.length === 0) {
                    console.error('No star buttons found');
                    return;
                }

                starButtons.forEach((starButton, index) => {
                    const starNumber = index + 1;
                    const starIcon = starButton.querySelector('i');
                    
                    if (!starIcon) {
                        return;
                    }

                    starButton.addEventListener('click', () => {
                        input.value = starNumber;
                        updateRatingStars(container, starNumber);
                        text.textContent = starNumber + '/5';
                    });

                    starButton.addEventListener('mouseenter', () => {
                        updateRatingStars(container, starNumber, true);
                    });
                });

                container.addEventListener('mouseleave', () => {
                    updateRatingStars(container, parseInt(input.value) || initialRating);
                });

                // Initialize with current rating
                if (initialRating > 0) {
                    updateRatingStars(container, Math.round(initialRating));
                }
            }

            function updateRatingStars(container, rating, isHover = false) {
                if (!container) {
                    console.error('Container not found for updateRatingStars');
                    return;
                }
                
                const starButtons = container.querySelectorAll('.rating-star-btn');
                
                if (starButtons.length === 0) {
                    console.error('No star buttons found for update');
                    return;
                }
                
                starButtons.forEach((starButton, index) => {
                    const starNumber = index + 1;
                    const starIcon = starButton.querySelector('i');
                    
                    if (!starIcon) {
                        return;
                    }

                    if (starNumber <= rating) {
                        // Filled star
                        starIcon.className = isHover ? 'fas fa-star text-yellow-300' : 'fas fa-star text-yellow-400';
                    } else {
                        // Empty star
                        starIcon.className = isHover ? 'far fa-star text-yellow-300' : 'far fa-star text-gray-300';
                    }
                });
            }

            // Initialize rating stars
            @if(!$userReview)
                document.addEventListener('DOMContentLoaded', () => {
                    initRatingStars('ratingStars', 'ratingInput', 'ratingText');
                });
            @else
                document.addEventListener('DOMContentLoaded', () => {
                    // Initialize edit rating stars even when form is hidden
                    const editRatingInput = document.getElementById('editRatingInput');
                    const editRatingStarsContainer = document.getElementById('editRatingStars');
                    
                    if (!editRatingInput || !editRatingStarsContainer) {
                        console.error('Edit rating elements not found on page load');
                        return;
                    }
                    
                    const initialRating = parseInt(editRatingInput.value) || 0;
                    
                    // Initialize the rating stars
                    initRatingStars('editRatingStars', 'editRatingInput', 'editRatingText', initialRating);
                    
                    // Ensure stars are displayed correctly on initial load
                    // Use setTimeout to ensure DOM is ready
                    setTimeout(() => {
                        if (initialRating > 0) {
                            updateRatingStars(editRatingStarsContainer, initialRating);
                        }
                    }, 50);
                });
            @endif

            // Submit Review
            document.getElementById('reviewForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const rating = formData.get('rating');

                if (!rating || rating === '0') {
                    alert('{{ __('common.please_select_rating') }}');
                    return;
                }

                try {
                    const response = await fetch(`/tours/${tourId}/reviews`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || '{{ __('common.error_creating_review') }}');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('{{ __('common.error_creating_review') }}');
                }
            });

            // Edit Review
            function editReview() {
                const userReviewDisplay = document.getElementById('userReviewDisplay');
                const editReviewForm = document.getElementById('editReviewForm');
                
                if (!userReviewDisplay || !editReviewForm) {
                    console.error('Edit review elements not found');
                    return;
                }
                
                userReviewDisplay.classList.add('hidden');
                editReviewForm.classList.remove('hidden');
                
                // Refresh rating stars display when showing edit form
                const editRatingInput = document.getElementById('editRatingInput');
                const editRatingStarsContainer = document.getElementById('editRatingStars');
                
                if (editRatingInput && editRatingStarsContainer) {
                    const currentRating = parseInt(editRatingInput.value) || 0;
                    // Use setTimeout to ensure DOM is updated
                    setTimeout(() => {
                        updateRatingStars(editRatingStarsContainer, currentRating);
                    }, 10);
                }
            }

            function cancelEditReview() {
                document.getElementById('userReviewDisplay').classList.remove('hidden');
                document.getElementById('editReviewForm').classList.add('hidden');
            }

            document.getElementById('editReviewForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const reviewId = {{ $userReview->id ?? 'null' }};

                try {
                    const response = await fetch(`/reviews/${reviewId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            rating: formData.get('rating'),
                            content: formData.get('content')
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || '{{ __('common.error_updating_review') }}');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('{{ __('common.error_updating_review') }}');
                }
            });

            // Delete Review
            function deleteReview(reviewId) {
                if (!confirm('{{ __('common.confirm_delete_review') }}')) {
                    return;
                }

                fetch(`/reviews/${reviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || '{{ __('common.error_deleting_review') }}');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __('common.error_deleting_review') }}');
                });
            }

            // Toggle Like
            function toggleLike(reviewId) {
                fetch(`/reviews/${reviewId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const btn = document.querySelector(`.like-btn[data-review-id="${reviewId}"]`);
                        if (!btn) return;
                        const icon = btn.querySelector('i');
                        const countSpan = btn.querySelector(`.likes-count-${reviewId}`);
                        
                        if (data.is_liked) {
                            icon.className = 'fas fa-heart';
                            btn.classList.add('text-red-600');
                        } else {
                            icon.className = 'far fa-heart';
                            btn.classList.remove('text-red-600');
                        }
                        countSpan.textContent = data.likes_count;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __('common.error_liking_review') }}');
                });
            }

            // Toggle Comments
            function toggleComments(reviewId) {
                const section = document.querySelector(`.comments-section-${reviewId}`);
                section.classList.toggle('hidden');
            }

            // Submit Comment
            function submitComment(event, reviewId) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                fetch(`/reviews/${reviewId}/comments`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        form.reset();
                        location.reload(); // Reload to show new comment
                    } else {
                        alert(data.message || '{{ __('common.error_creating_comment') }}');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __('common.error_creating_comment') }}');
                });
            }

            // Edit Comment
            function editComment(commentId, currentBody) {
                const bodyElement = document.querySelector(`.comment-body-${commentId}`);
                const form = document.querySelector(`.edit-comment-form-${commentId}`);
                
                if (!bodyElement || !form) return;
                
                bodyElement.parentElement.classList.add('hidden');
                form.classList.remove('hidden');
                form.querySelector('textarea').value = currentBody;

                // Remove existing listeners to prevent duplicates
                const newForm = form.cloneNode(true);
                form.parentNode.replaceChild(newForm, form);
                const freshForm = document.querySelector(`.edit-comment-form-${commentId}`);

                freshForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(freshForm);

                    try {
                        const response = await fetch(`/comments/${commentId}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                body: formData.get('body')
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || '{{ __('common.error_updating_comment') }}');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('{{ __('common.error_updating_comment') }}');
                    }
                });
            }

            function cancelEditComment(commentId) {
                document.querySelector(`.comment-body-${commentId}`).parentElement.classList.remove('hidden');
                document.querySelector(`.edit-comment-form-${commentId}`).classList.add('hidden');
            }

            // Delete Comment
            function deleteComment(commentId) {
                if (!confirm('{{ __('common.confirm_delete_comment') }}')) {
                    return;
                }

                fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || '{{ __('common.error_deleting_comment') }}');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __('common.error_deleting_comment') }}');
                });
            }
        </script>
        @endauth
    </body>
</html>





