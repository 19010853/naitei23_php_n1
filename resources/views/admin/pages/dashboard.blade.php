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

    <div class="dashboard-panels">
        <div class="panel-card">
            <div class="panel-head">
                <div>
                    <p class="panel-title">{{ __('common.recent_bookings') }}</p>
                    <p class="panel-subtitle">{{ __('common.realtime_update') }}</p>
                </div>
                <span class="chip chip-soft">5 {{ __('common.bookings') }}</span>
            </div>

            <div class="panel-list">
                @forelse ($recentBookings as $booking)
                    <div class="panel-row">
                        <div class="panel-col panel-col--user">
                            <div class="avatar-circle">{{ Str::upper(Str::substr($booking->user->name ?? 'C', 0, 1)) }}</div>
                            <div>
                                <div class="text-strong">{{ $booking->user->name ?? __('common.anonymous') }}</div>
                                <div class="text-dim">{{ $booking->user->email ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="panel-col panel-col--tour">
                            <div class="text-strong">{{ $booking->tourSchedule->tour->name ?? __('common.tour') }}</div>
                            <div class="text-dim">{{ $booking->tourSchedule->tour->location ?? '-' }}</div>
                        </div>
                        <div class="panel-col">
                            <div class="pill-muted">
                                <i class="far fa-calendar-alt"></i>
                                {{ optional($booking->tourSchedule->start_date)->format('d/m/Y') ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="panel-col panel-col--status">
                            <span class="status-badge status-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'cancelled' : 'pending') }}">
                                {{ __("common.{$booking->status}") }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">{{ __('common.no_bookings') }}</div>
                @endforelse
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-head">
                <div>
                    <p class="panel-title">{{ __('common.top_tours_by_rating') }}</p>
                    <p class="panel-subtitle">{{ __('common.realtime_update') }}</p>
                </div>
            </div>

            <div class="panel-list">
                @forelse ($topTours as $tour)
                    <div class="panel-row">
                        <div class="panel-col panel-col--tour">
                            <div class="text-strong">{{ $tour->name }}</div>
                            <div class="text-dim" data-full-content="{{ $tour->description ?? '' }}">{{ Str::limit($tour->description ?? '', 60) }}</div>
                        </div>
                        <div class="panel-col">
                            <div class="pill-muted">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $tour->location }}
                            </div>
                        </div>
                        <div class="panel-col">
                            <div class="pill-soft">
                                {{ $tour->schedules_count }} {{ __('common.schedules') }}
                            </div>
                        </div>
                        <div class="panel-col panel-col--rating">
                            <div class="rating-chip">
                                <i class="fas fa-star"></i>
                                {{ number_format((float) ($tour->reviews_avg_rating ?? 0), 1) }}
                                <span class="text-dim">({{ number_format($tour->reviews_count) }})</span>
                            </div>
                        </div>
                        <div class="panel-col panel-col--likes">
                            <div class="pill-muted">
                                <i class="far fa-heart"></i>
                                {{ number_format($tour->likes_count) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">{{ __('common.no_data') }}</div>
                @endforelse
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

