@php
    $navItems = [
        ['label' => __('common.dashboard'), 'route' => 'admin.dashboard', 'icon' => ['M3 12h18', 'M3 6h18', 'M3 18h18']],
        ['label' => __('common.users'), 'route' => 'admin.users', 'icon' => ['M5 20h14', 'M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8z']],
        ['label' => __('common.tours'), 'route' => 'admin.tours', 'icon' => ['M4 4h16v8H4z', 'M4 16h9v4H4z']],
        ['label' => __('common.tour_categories'), 'route' => 'admin.categories', 'icon' => ['M4 6h16v4H4z', 'M4 14h16v4H4z']],
        ['label' => __('common.tour_schedules'), 'route' => 'admin.tour-schedules', 'icon' => ['M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z']],
        ['label' => __('common.bookings'), 'route' => 'admin.bookings', 'icon' => ['M4 6h16v12H4z', 'M4 10h16']],
        ['label' => __('common.payments'), 'route' => 'admin.payments', 'icon' => ['M4 7h16', 'M4 17h16', 'M7 7v10', 'M17 7v10']],
        ['label' => __('common.reviews'), 'route' => 'admin.reviews', 'icon' => ['M12 17.27 18.18 21 16.54 13.97 22 9.24 14.81 8.63 12 2 9.19 8.63 2 9.24 7.46 13.97 5.82 21z']],
        ['label' => __('common.comments'), 'route' => 'admin.comments', 'icon' => ['M4 5h16v9H7l-3 3z']],
    ];
@endphp

<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="admin-logo">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
        <span>Traveloka Admin</span>
    </a>

    <nav class="sidebar-nav">
        @foreach ($navItems as $item)
            <a
                href="{{ route($item['route']) }}"
                class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    @foreach ($item['icon'] as $path)
                        <path d="{{ $path }}"></path>
                    @endforeach
                </svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="admin-sidebar-footer">
        {{ now()->format('d/m/Y') }}<br>
        <span>{{ __('common.stay_inspired') }} ✈️</span>
    </div>
</aside>

