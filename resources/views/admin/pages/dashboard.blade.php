@php
    use Illuminate\Support\Str;
@endphp

@extends('admin.layouts.app')

@section('page-title', 'Bảng điều khiển')

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

    <div class="grid" style="margin-top: 30px; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 22px;">
        <div class="table-wrapper">
            <div class="table-head">
                <div class="table-title">Booking gần đây</div>
                <span class="chip">5 bản ghi</span>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Tour</th>
                        <th>Ngày khởi hành</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentBookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->user->name ?? 'Ẩn danh' }}</strong><br>
                                <small style="color: var(--traveloka-muted);">{{ $booking->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                {{ $booking->tourSchedule->tour->name ?? 'Tour không xác định' }}<br>
                                <small style="color: var(--traveloka-muted);">{{ $booking->tourSchedule->tour->location ?? '-' }}</small>
                            </td>
                            <td>
                                {{ optional($booking->tourSchedule->start_date)->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="status-badge status-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'cancelled' : 'pending') }}">
                                    {{ Str::title($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">Chưa có booking nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-wrapper">
            <div class="table-head">
                <div class="table-title">Top tour theo đánh giá</div>
                <span class="chip">Cập nhật realtime</span>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tour</th>
                        <th>Địa điểm</th>
                        <th>Lịch trình</th>
                        <th>Đánh giá</th>
                        <th>Lượt thích</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topTours as $tour)
                        <tr>
                            <td>
                                <strong>{{ $tour->name }}</strong>
                                <br>
                                <small style="color: var(--traveloka-muted);">{{ Str::limit($tour->description ?? '', 50) }}</small>
                            </td>
                            <td>{{ $tour->location }}</td>
                            <td>{{ $tour->schedules_count }} lịch</td>
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
                            <td colspan="5" class="empty-state">Chưa có dữ liệu tour</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-wrapper" style="margin-top: 30px;">
        <div class="table-head">
            <div class="table-title">Đánh giá mới nhất</div>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Tour</th>
                    <th>Rating</th>
                    <th>Nội dung</th>
                    <th>Ngày</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentReviews as $review)
                    <tr>
                        <td>{{ $review->user->name ?? 'Ẩn danh' }}</td>
                        <td>{{ $review->tour->name ?? 'Tour' }}</td>
                        <td>
                            <span class="chip">{{ $review->rating }}/5</span>
                        </td>
                        <td>{{ Str::limit($review->content, 70) }}</td>
                        <td>{{ optional($review->created_at)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">Chưa có đánh giá</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

