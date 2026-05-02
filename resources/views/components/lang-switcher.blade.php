{{--
    Antigravity FR/EN Language Switcher Component
    Usage: <x-lang-switcher />
    Place inside your navbar.
--}}

@php
    $currentLocale = app()->getLocale();
@endphp

<div class="lang-switcher d-flex align-items-center gap-1" style="position:relative;">

    {{-- FR Button --}}
    <form method="POST" action="{{ route('lang.switch', 'fr') }}" class="m-0">
        @csrf
        <button type="submit"
            class="lang-btn {{ $currentLocale === 'fr' ? 'lang-btn--active' : '' }}"
            aria-label="Passer en français"
            title="Français">
            <span class="lang-btn__flag">🇫🇷</span>
            <span class="lang-btn__label">FR</span>
        </button>
    </form>

    {{-- Divider --}}
    <span class="lang-divider">|</span>

    {{-- EN Button --}}
    <form method="POST" action="{{ route('lang.switch', 'en') }}" class="m-0">
        @csrf
        <button type="submit"
            class="lang-btn {{ $currentLocale === 'en' ? 'lang-btn--active' : '' }}"
            aria-label="Switch to English"
            title="English">
            <span class="lang-btn__flag">🇬🇧</span>
            <span class="lang-btn__label">EN</span>
        </button>
    </form>
</div>

{{-- Inline styles scoped to this component --}}
<style>
.lang-switcher { user-select: none; }

.lang-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1px solid var(--tg-card-border);
    border-radius: 12px;
    background: var(--tg-bg-light);
    color: var(--tg-text-muted);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
}

.lang-btn:hover {
    background: var(--tg-primary-light);
    color: var(--tg-primary);
    border-color: var(--tg-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26, 111, 191, 0.1);
}

.lang-btn--active {
    background: var(--tg-primary);
    color: #fff !important;
    border-color: var(--tg-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26, 111, 191, 0.2);
}

.lang-btn__flag { font-size: 14px; line-height: 1; }

.lang-divider {
    color: var(--tg-card-border);
    font-size: 14px;
    font-weight: 300;
    opacity: 0.5;
}

/* Dark mode specific adjustments if needed */
[data-bs-theme="dark"] .lang-btn {
    background: #16222E;
}
[data-bs-theme="dark"] .lang-btn--active {
    background: var(--tg-primary);
}
</style>
