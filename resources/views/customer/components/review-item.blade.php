@php
    $isLiked = $currentUser ? $review->isLikedBy($currentUser) : false;
    $likesCount = $review->likes_count ?? $review->likes()->count();
    $comments = $review->comments()->with('user')->orderBy('created_at', 'asc')->get();
@endphp

<div class="review-item border-b border-slate-200 pb-6 last:border-0 last:pb-0" data-review-id="{{ $review->id }}">
    <!-- Review Header -->
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-semibold flex-shrink-0">
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
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-0.5" id="review-rating-{{ $review->id }}">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                    @else
                        <i class="far fa-star text-gray-300 text-sm"></i>
                    @endif
                @endfor
            </div>
            <span class="text-sm font-semibold">{{ number_format($review->rating, 0) }}/5</span>
        </div>
    </div>

    <!-- Review Content -->
    @if($review->content)
        <p class="text-slate-700 leading-relaxed mb-3">{{ $review->content }}</p>
    @endif

    <!-- Admin Reply (if exists) -->
    @if($review->admin_reply)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-3">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-shield-alt text-blue-600"></i>
                <span class="font-semibold text-blue-900">{{ __('common.admin_reply') }}</span>
                <span class="text-xs text-blue-600">
                    {{ $review->admin_replied_at ? $review->admin_replied_at->format('d/m/Y H:i') : '' }}
                </span>
            </div>
            <p class="text-blue-800 leading-relaxed">{{ $review->admin_reply }}</p>
        </div>
    @endif

    <!-- Review Actions (Like & Comment) -->
    <div class="flex items-center gap-4 mb-4">
        <button type="button" 
                class="like-btn flex items-center gap-2 text-slate-600 hover:text-red-600 transition-colors {{ $isLiked ? 'text-red-600' : '' }}"
                data-review-id="{{ $review->id }}"
                onclick="toggleLike({{ $review->id }})">
            <i class="{{ $isLiked ? 'fas' : 'far' }} fa-heart"></i>
            <span class="likes-count-{{ $review->id }}">{{ $likesCount }}</span>
            <span class="ml-1">{{ __('common.helpful') }}</span>
        </button>
        <button type="button" 
                class="comment-toggle-btn flex items-center gap-2 text-slate-600 hover:text-sky-600 transition-colors"
                data-review-id="{{ $review->id }}"
                onclick="toggleComments({{ $review->id }})">
            <i class="fas fa-comment"></i>
            <span class="comments-count-{{ $review->id }}">{{ $comments->count() }}</span>
            <span class="ml-1">{{ __('common.comments') }}</span>
        </button>
    </div>

    <!-- Comments Section (Collapsible) -->
    <div class="comments-section-{{ $review->id }} hidden mt-4 pl-4 border-l-2 border-slate-200">
        <!-- Existing Comments -->
        <div class="existing-comments-{{ $review->id }} space-y-4 mb-4">
            @foreach($comments as $comment)
                <div class="comment-item flex items-start gap-3" data-comment-id="{{ $comment->id }}">
                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-semibold text-sm flex-shrink-0">
                        {{ strtoupper(substr($comment->user->name ?? __('common.anonymous'), 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-semibold text-sm text-slate-900">
                                {{ $comment->user->name ?? __('common.anonymous') }}
                            </p>
                            <span class="text-xs text-slate-500">
                                {{ $comment->created_at->format('d/m/Y H:i') }}
                            </span>
                            @auth
                                @if($comment->user_id === auth()->id())
                                    <button type="button" 
                                            onclick="editComment({{ $comment->id }}, @json($comment->body))"
                                            class="text-xs text-sky-600 hover:text-sky-700 ml-2">
                                        <i class="fas fa-edit"></i> {{ __('common.edit') }}
                                    </button>
                                    <button type="button" 
                                            onclick="deleteComment({{ $comment->id }})"
                                            class="text-xs text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash"></i> {{ __('common.delete') }}
                                    </button>
                                @endif
                            @endauth
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed comment-body-{{ $comment->id }}">{{ $comment->body }}</p>
                        <!-- Edit Comment Form (hidden by default) -->
                        <form class="edit-comment-form-{{ $comment->id }} hidden mt-2">
                            @csrf
                            @method('PUT')
                            <textarea name="body" rows="2" 
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"></textarea>
                            <div class="flex gap-2 mt-2">
                                <button type="submit" 
                                        class="px-3 py-1 bg-sky-600 text-white rounded text-sm font-semibold hover:bg-sky-700">
                                    {{ __('common.save') }}
                                </button>
                                <button type="button" 
                                        onclick="cancelEditComment({{ $comment->id }})"
                                        class="px-3 py-1 bg-slate-200 text-slate-700 rounded text-sm font-semibold hover:bg-slate-300">
                                    {{ __('common.cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Add Comment Form (for authenticated users) -->
        @auth
            <form class="add-comment-form-{{ $review->id }}" onsubmit="submitComment(event, {{ $review->id }})">
                @csrf
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-semibold text-sm flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <textarea name="body" 
                                  rows="2" 
                                  class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                  placeholder="{{ __('common.write_a_comment') }}"
                                  required></textarea>
                        <button type="submit" 
                                class="mt-2 px-4 py-1 bg-sky-600 text-white rounded text-sm font-semibold hover:bg-sky-700">
                            {{ __('common.post_comment') }}
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="p-3 bg-sky-50 border border-sky-200 rounded-lg">
                <p class="text-xs text-sky-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ __('common.login_to_comment') }}
                </p>
            </div>
        @endauth
    </div>
</div>

