@php
    use Illuminate\Support\Str;

    $authUser = auth()->user();
    $initials = $authUser ? Str::of($authUser->name)->trim()->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') : 'AD';
    $currentLocale = app()->getLocale();
@endphp

<header class="admin-header">
    <div class="header-left">
        <button type="button" class="admin-sidebar-toggle" data-sidebar-toggle aria-label="Toggle sidebar">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div>
            <div class="header-title">{{ $pageTitle ?? __('common.admin_dashboard') }}</div>
            <div class="header-breadcrumb">
                {{ __('common.traveloka_admin') }} • {{ $pageTitle ?? __('common.admin_dashboard') }}
            </div>
        </div>
    </div>

    <div class="header-actions">
        <div class="language-switcher">
            <button type="button"
                    id="adminLanguageToggle"
                    class="language-toggle"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-label="{{ __('common.change_language') }}">
                @php
                    $flagUrls = config('app.locale_flags', []);
                    $fallbackFlag = 'https://flagcdn.com/w20/us.png';
                    $currentFlag = $flagUrls[$currentLocale] ?? $fallbackFlag;
                @endphp
                <img src="{{ $currentFlag }}" alt="{{ strtoupper($currentLocale) }}" style="width: 20px; height: 15px; object-fit: cover; border-radius: 2px; margin-right: 6px;">
                <span>{{ strtoupper($currentLocale) }}</span>
                <i class="fas fa-chevron-down" style="margin-left: 6px; font-size: 10px;"></i>
            </button>
            <div class="language-menu" id="adminLanguageMenu">
                <a href="{{ route('locale.switch', 'en') }}" 
                   role="menuitem"
                   class="language-option {{ $currentLocale === 'en' ? 'active' : '' }}">
                    <img src="{{ $flagUrls['en'] ?? $fallbackFlag }}" alt="EN" style="width: 20px; height: 15px; object-fit: cover; border-radius: 2px; margin-right: 8px;">
                    <span>{{ __('common.english') }}</span>
                </a>
                <a href="{{ route('locale.switch', 'vi') }}" 
                   role="menuitem"
                   class="language-option {{ $currentLocale === 'vi' ? 'active' : '' }}">
                    <img src="{{ $flagUrls['vi'] ?? 'https://flagcdn.com/w20/vn.png' }}" alt="VI" style="width: 20px; height: 15px; object-fit: cover; border-radius: 2px; margin-right: 8px;">
                    <span>{{ __('common.vietnamese') }}</span>
                </a>
                <a href="{{ route('locale.switch', 'ja') }}" 
                   role="menuitem"
                   class="language-option {{ $currentLocale === 'ja' ? 'active' : '' }}">
                    <img src="{{ $flagUrls['ja'] ?? 'https://flagcdn.com/w20/jp.png' }}" alt="JA" style="width: 20px; height: 15px; object-fit: cover; border-radius: 2px; margin-right: 8px;">
                    <span>{{ __('common.japanese') }}</span>
                </a>
            </div>
        </div>

        <div class="user-pill">
            <div class="user-avatar">{{ Str::upper($initials) }}</div>
            <div>
                <div style="font-weight: 600; color: var(--traveloka-slate);">
                    {{ $authUser?->name ?? 'Admin' }}
                </div>
                <div class="chip">{{ __('common.super_admin') }}</div>
            </div>
            <div style="position: relative;">
                <svg width="16" height="16" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
                    <path d="m6 9 6 6 6-6" />
                </svg>
                <div class="user-menu">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const languageToggle = document.getElementById('adminLanguageToggle');
    const languageMenu = document.getElementById('adminLanguageMenu');
    
    if (languageToggle && languageMenu) {
        languageToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = languageMenu.classList.contains('show');
            if (isOpen) {
                languageMenu.classList.remove('show');
                languageToggle.setAttribute('aria-expanded', 'false');
            } else {
                languageMenu.classList.add('show');
                languageToggle.setAttribute('aria-expanded', 'true');
            }
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!languageToggle.contains(e.target) && !languageMenu.contains(e.target)) {
                languageMenu.classList.remove('show');
                languageToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Keyboard navigation
        languageToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                languageToggle.click();
            } else if (e.key === 'Escape') {
                languageMenu.classList.remove('show');
                languageToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>

