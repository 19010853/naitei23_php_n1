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
                    <th>{{ __('common.review_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>{{ $review->user->name ?? __('common.anonymous') }}</td>
                        <td>{{ $review->tour->name ?? '-' }}</td>
                        <td>
                            <span class="chip">{{ $review->rating }}/5</span>
                        </td>
                        <td data-full-content="{{ $review->content }}">{{ Str::limit($review->content, 90) }}</td>
                        <td>{{ optional($review->created_at)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">{{ __('common.no_reviews') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $reviews->links() }}
        </div>
    </div>
@endsection

