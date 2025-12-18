@if ($users->hasPages())
    @php
        $currentPage = $users->currentPage();
        $lastPage = $users->lastPage();
        $start = max(1, $currentPage - 2);
        $end = min($lastPage, $currentPage + 2);
        $from = ($users->currentPage() - 1) * $users->perPage() + 1;
        $to = min($users->currentPage() * $users->perPage(), $users->total());
    @endphp

    <div class="aws-pagination-wrapper">
        <div class="aws-pagination-summary">
            {{ __('Hiển thị :from–:to trên tổng số :total', ['from' => $from, 'to' => $to, 'total' => $users->total()]) }}
        </div>

        <nav class="aws-pagination-nav" role="navigation" aria-label="Pagination Navigation">
            <ul class="aws-pagination-list">
                {{-- Previous --}}
                @if ($users->onFirstPage())
                    <li class="aws-page-item disabled">
                        <span class="aws-page-link">&laquo; {{ __('Trước') }}</span>
                    </li>
                @else
                    <li class="aws-page-item">
                        <a href="{{ $users->previousPageUrl() }}" class="aws-page-link" rel="prev">
                            &laquo; {{ __('Trước') }}
                        </a>
                    </li>
                @endif

                {{-- First page + left dots --}}
                @if ($start > 1)
                    <li class="aws-page-item">
                        <a href="{{ $users->url(1) }}" class="aws-page-link">1</a>
                    </li>
                    @if ($start > 2)
                        <li class="aws-page-item aws-page-dots">
                            <span class="aws-page-link">…</span>
                        </li>
                    @endif
                @endif

                {{-- Middle window --}}
                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $currentPage)
                        <li class="aws-page-item active">
                            <span class="aws-page-link" aria-current="page">{{ $page }}</span>
                        </li>
                    @else
                        <li class="aws-page-item">
                            <a href="{{ $users->url($page) }}" class="aws-page-link">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Right dots + last page --}}
                @if ($end < $lastPage)
                    @if ($end < $lastPage - 1)
                        <li class="aws-page-item aws-page-dots">
                            <span class="aws-page-link">…</span>
                        </li>
                    @endif
                    <li class="aws-page-item">
                        <a href="{{ $users->url($lastPage) }}" class="aws-page-link">{{ $lastPage }}</a>
                    </li>
                @endif

                {{-- Next --}}
                @if ($users->hasMorePages())
                    <li class="aws-page-item">
                        <a href="{{ $users->nextPageUrl() }}" class="aws-page-link" rel="next">
                            {{ __('Sau') }} &raquo;
                        </a>
                    </li>
                @else
                    <li class="aws-page-item disabled">
                        <span class="aws-page-link">{{ __('Sau') }} &raquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif

