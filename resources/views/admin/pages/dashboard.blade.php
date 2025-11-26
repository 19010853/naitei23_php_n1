@php
    use Illuminate\Support\Str;
@endphp

@extends('admin.layouts.app')

@section('page-title', __('common.admin_dashboard'))

@section('content')
    <div class="grid grid-3" data-dashboard-stats>
        @foreach ($statCards as $card)
            <div class="tile stat-card">
                <p class="stat-label">{{ $card['label'] }}</p>
                <p class="stat-value" data-stat-key="{{ $card['key'] }}">{{ number_format($card['value']) }}</p>

                @if (! empty($card['trend']))
                    <span class="stat-trend {{ $card['trend']['direction'] }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            @if ($card['trend']['direction'] === 'positive')
                                <path d="M5 12l5 5L20 7" />
                            @else
                                <path d="M19 12l-5-5L4 17" />
                            @endif
                        </svg>
                        {{ $card['trend']['text'] }}
                    </span>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid" style="margin-top: 30px; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 22px;">
        <div class="table-wrapper table-wrapper--scrollable">
            <div class="table-head">
                <div class="table-title">{{ __('common.recent_bookings') }}</div>
                <span class="chip">5 {{ __('common.bookings') }}</span>
            </div>
            <div class="table-scroll-container">
                <table class="admin-table" style="min-width: 600px;">
                    <colgroup>
                        <col style="width: 200px;">
                        <col style="width: 200px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>{{ __('common.customer') }}</th>
                            <th>{{ __('common.tour') }}</th>
                            <th>{{ __('common.departure_date') }}</th>
                            <th>{{ __('common.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentBookings as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->user->name ?? __('common.anonymous') }}</strong><br>
                                    <small style="color: var(--traveloka-muted);">{{ $booking->user->email ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ $booking->tourSchedule->tour->name ?? __('common.tour') }}<br>
                                    <small style="color: var(--traveloka-muted);">{{ $booking->tourSchedule->tour->location ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ optional($booking->tourSchedule->start_date)->format('d/m/Y') ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'cancelled' : 'pending') }}">
                                        {{ __("common.{$booking->status}") }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">{{ __('common.no_bookings') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-wrapper table-wrapper--scrollable">
            <div class="table-head">
                <div class="table-title">{{ __('common.top_tours_by_rating') }}</div>
                <span class="chip">{{ __('common.realtime_update') }}</span>
            </div>
        <div class="table-scroll-container">
            <table class="admin-table" style="min-width: 700px;">
                    <colgroup>
                        <col style="width: 220px;">
                        <col style="width: 150px;">
                        <col style="width: 100px;">
                        <col style="width: 130px;">
                        <col style="width: 100px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>{{ __('common.tour') }}</th>
                            <th>{{ __('common.location') }}</th>
                            <th>{{ __('common.schedules') }}</th>
                            <th>{{ __('common.rating') }}</th>
                            <th>{{ __('common.likes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topTours as $tour)
                            <tr>
                                <td>
                                    <strong>{{ $tour->name }}</strong>
                                    <br>
                                    <small style="color: var(--traveloka-muted);" data-full-content="{{ $tour->description ?? '' }}">{{ Str::limit($tour->description ?? '', 50) }}</small>
                                </td>
                                <td>{{ $tour->location }}</td>
                                <td>{{ $tour->schedules_count }} {{ __('common.schedules') }}</td>
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-star" style="color: var(--traveloka-orange);"></i>
                                        {{ number_format((float) ($tour->reviews_avg_rating ?? 0), 1) }}/5
                                        <small style="color: var(--traveloka-muted);">({{ number_format($tour->reviews_count) }})</small>
                                    </span>
                                </td>
                                <td>{{ number_format($tour->likes_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">{{ __('common.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="table-wrapper table-wrapper--scrollable" style="margin-top: 30px;">
        <div class="table-head">
            <div class="table-title">{{ __('common.latest_reviews') }}</div>
        </div>
        <div class="table-scroll-container">
            <table class="admin-table" style="min-width: 800px;">
                <colgroup>
                    <col style="width: 150px;">
                    <col style="width: 200px;">
                    <col style="width: 100px;">
                    <col style="min-width: 250px;">
                    <col style="width: 100px;">
                </colgroup>
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
                    @forelse ($recentReviews as $review)
                        <tr>
                            <td>{{ $review->user->name ?? __('common.anonymous') }}</td>
                            <td>{{ $review->tour->name ?? __('common.tour') }}</td>
                            <td>
                                <span class="chip">{{ $review->rating }}/5</span>
                            </td>
                            <td data-full-content="{{ $review->content }}">{{ Str::limit($review->content, 70) }}</td>
                            <td>{{ optional($review->created_at)->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">{{ __('common.no_reviews') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

