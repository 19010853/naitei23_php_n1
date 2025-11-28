@php
    use Illuminate\Support\Str;
@endphp

@extends('admin.layouts.app')

@section('page-title', __('common.reviews'))

@section('content')
    <div class="table-wrapper">
        <div class="table-head">
            <div class="table-title">{{ __('common.all_reviews') }}</div>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('common.customer') }}</th>
                    <th>{{ __('common.tour') }}</th>
                    <th>{{ __('common.rating') }}</th>
                    <th>{{ __('common.review_content') }}</th>
                    <th>{{ __('common.admin_reply') }}</th>
                    <th>{{ __('common.review_date') }}</th>
                    <th style="width: 160px; text-align: center;">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>{{ $review->user->name ?? __('common.anonymous') }}</td>
                        <td>{{ $review->tour->name ?? '-' }}</td>
                        <td>
                            <span class="chip">{{ number_format($review->rating, 1) }}/5</span>
                        </td>
                        <td data-full-content="{{ $review->content }}">{{ Str::limit($review->content, 90) }}</td>
                        <td>
                            @if($review->admin_reply)
                                <span class="status-badge status-success">{{ __('common.has_reply') }}</span>
                            @else
                                <span class="status-badge status-pending">{{ __('common.no_reply') }}</span>
                            @endif
                        </td>
                        <td>{{ optional($review->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                <button class="btn btn-primary btn-sm btn-action reply-review-btn" 
                                        data-review-id="{{ $review->id }}"
                                        data-review-content="{{ $review->content }}"
                                        data-admin-reply="{{ $review->admin_reply ?? '' }}"
                                        title="{{ $review->admin_reply ? __('common.edit_reply') : __('common.reply') }}">
                                    <i class="fas fa-reply"></i>
                                    <span>{{ $review->admin_reply ? __('common.edit_reply') : __('common.reply') }}</span>
                                </button>
                                @if($review->admin_reply)
                                    <button class="btn btn-danger btn-sm btn-action delete-reply-btn" 
                                            data-review-id="{{ $review->id }}"
                                            title="{{ __('common.delete_reply') }}">
                                        <i class="fas fa-trash"></i>
                                        <span>{{ __('common.delete') }}</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">{{ __('common.no_reviews') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $reviews->links() }}
        </div>
    </div>

    <!-- Admin Reply Modal -->
    <div id="adminReplyModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header modal-header-edit">
                <h3 id="adminReplyModalTitle">{{ __('common.reply_to_review') }}</h3>
                <span class="close" onclick="document.getElementById('adminReplyModal').style.display='none'">&times;</span>
            </div>
            <form id="adminReplyForm" method="POST">
                @csrf
                <input type="hidden" id="admin_reply_review_id" name="review_id">
                <div class="form-group">
                    <label>{{ __('common.review_content') }}</label>
                    <div id="admin_reply_review_content" class="p-3 bg-gray-50 rounded border text-sm" style="min-height: 60px;"></div>
                </div>
                <div class="form-group">
                    <label>{{ __('common.admin_reply') }} <span class="text-red-500">*</span></label>
                    <textarea name="admin_reply" 
                              id="admin_reply_text" 
                              class="form-control" 
                              rows="4" 
                              required
                              placeholder="{{ __('common.write_admin_reply_here') }}"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" 
                    class="btn btn-secondary" 
                    onclick="document.getElementById('adminReplyModal').style.display='none'">
                {{ __('common.cancel') }}
            </button>
            <button type="submit" form="adminReplyForm" class="btn btn-primary">{{ __('common.save') }}</button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle reply/edit reply buttons
        document.querySelectorAll('.reply-review-btn').forEach(button => {
            button.addEventListener('click', function() {
                const reviewId = this.getAttribute('data-review-id');
                const reviewContent = this.getAttribute('data-review-content');
                const adminReply = this.getAttribute('data-admin-reply');
                const hasReply = adminReply && adminReply.trim() !== '';
                
                openAdminReplyModal(reviewId, reviewContent, adminReply, hasReply);
            });
        });

        // Handle delete reply buttons
        document.querySelectorAll('.delete-reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                const reviewId = this.getAttribute('data-review-id');
                deleteAdminReply(reviewId);
            });
        });
    });

    function openAdminReplyModal(reviewId, reviewContent, adminReply, hasReply) {
        const modal = document.getElementById('adminReplyModal');
        const form = document.getElementById('adminReplyForm');
        const title = document.getElementById('adminReplyModalTitle');
        const contentDiv = document.getElementById('admin_reply_review_content');
        const textarea = document.getElementById('admin_reply_text');
        const reviewIdInput = document.getElementById('admin_reply_review_id');

        title.textContent = hasReply ? @json(__('common.edit_reply')) : @json(__('common.reply_to_review'));
        contentDiv.textContent = reviewContent || @json(__('common.no_content'));
        textarea.value = adminReply || '';
        reviewIdInput.value = reviewId;

        if (hasReply) {
            form.action = `/admin/reviews/${reviewId}/reply`;
            form.method = 'PUT';
        } else {
            form.action = `/admin/reviews/${reviewId}/reply`;
            form.method = 'POST';
        }

        modal.style.display = 'block';
    }

    // Handle form submission
    document.getElementById('adminReplyForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const reviewId = formData.get('review_id');
        const method = form.method === 'PUT' ? 'PUT' : 'POST';
        const url = `/admin/reviews/${reviewId}/reply`;

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    admin_reply: formData.get('admin_reply')
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || @json(__('common.error_processing_request')));
            }
        } catch (error) {
            console.error('Error:', error);
            alert(@json(__('common.error_processing_request')));
        }
    });

    function deleteAdminReply(reviewId) {
        if (!confirm(@json(__('common.confirm_delete_reply')))) {
            return;
        }

        fetch(`/admin/reviews/${reviewId}/reply`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || @json(__('common.error_deleting_reply')));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(@json(__('common.error_deleting_reply')));
        });
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('adminReplyModal');
        if (modal && event.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>
@endpush

