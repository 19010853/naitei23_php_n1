@extends('admin.layouts.app')

@section('page-title', __('common.bookings'))

@section('content')
    <div class="table-wrapper">
        <div class="table-head">
            <div class="table-title">{{ __('common.booking_list') }}</div>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('common.booking_code') }}</th>
                    <th>{{ __('common.customer') }}</th>
                    <th>{{ __('common.tour') }}</th>
                    <th>{{ __('common.num_participants') }}</th>
                    <th>{{ __('common.total_price') }}</th>
                    <th>{{ __('common.status') }}</th>
                    <th>{{ __('common.created_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td>#{{ $booking->id }}</td>
                        <td>{{ $booking->user->name ?? __('common.anonymous') }}</td>
                        <td>{{ $booking->tourSchedule->tour->name ?? '-' }}</td>
                        <td>{{ $booking->num_participants }}</td>
                        <td>{{ number_format($booking->total_price, 0, ',', '.') }} {{ __('common.vnd') }}</td>
                        <td>
                            <span class="status-badge status-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'cancelled' : 'pending') }}">
                                {{ __("common.{$booking->status}") }}
                            </span>
                        </td>
                        <td>{{ optional($booking->created_at)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">{{ __('common.no_bookings') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

