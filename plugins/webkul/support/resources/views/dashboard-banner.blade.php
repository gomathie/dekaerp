{{--
    Dashboard welcome banner (Support; not tied to any plugin).

    Refined 2026-11-12 (user: "according to modern standards"):
    - The greeting and date come from the viewer's own clock and language in
      the browser. The server's clock said "Good morning" in the evening for
      anyone in another timezone. The server-rendered text stays as the
      no-JavaScript fallback.
    - The company appeared twice (sentence and chip); it is now one detail,
      and the work location shows only when it differs from the company.
    - Quiet detail line with icons instead of uppercase filled chips, and the
      person's avatar as the visual anchor.
    Other plugins add controls on the right through DashboardBannerRegistry
    (Attendance adds the clock button).
--}}
@php
    $user = filament()->auth()->user();
    $company = app(\Webkul\Support\Services\CompanyContext::class)->currentCompany();

    $hour = (int) now()->format('G');
    $greetings = [
        'morning'   => __('support::dashboard-banner.greetings.morning'),
        'afternoon' => __('support::dashboard-banner.greetings.afternoon'),
        'evening'   => __('support::dashboard-banner.greetings.evening'),
    ];
    $fallbackGreeting = $hour < 12 ? $greetings['morning'] : ($hour < 17 ? $greetings['afternoon'] : $greetings['evening']);

    $name = trim((string) $user?->name);
    $firstName = $name === '' ? null : explode(' ', $name)[0];

    $roles = $user ? $user->effectiveRoleNames() : [];
    $role = $roles === [] ? null : $roles[0];

    $location = \Webkul\Support\Filament\Contributions\DashboardBannerRegistry::resolvedLocation();
    $location = ($location !== null && $location !== $company?->name) ? $location : null;

    $avatarUrl = $user ? filament()->getUserAvatarUrl($user) : null;
    $locale = str_replace('_', '-', app()->getLocale());
    $aside = \Webkul\Support\Filament\Contributions\DashboardBannerRegistry::renderedSections();
@endphp

@if ($user)
    <style>
        .deka-welcome {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            padding: 0.875rem 1.125rem;
            border: 1px solid rgb(0 0 0 / 0.06);
            border-radius: 0.875rem;
            background:
                radial-gradient(120% 140% at 0% 0%, rgb(245 158 11 / 0.10), transparent 55%),
                #ffffff;
            color: #111827;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.04);
        }
        .dark .deka-welcome {
            border-color: rgb(255 255 255 / 0.08);
            background:
                radial-gradient(120% 140% at 0% 0%, rgb(245 158 11 / 0.14), transparent 55%),
                rgb(24 24 27);
            color: #f4f4f5;
        }
        .deka-welcome__who {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            min-width: 0;
        }
        .deka-welcome__avatar {
            flex-shrink: 0;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 9999px;
            object-fit: cover;
            box-shadow: 0 0 0 2px #ffffff, 0 0 0 3.5px rgb(245 158 11 / 0.55);
        }
        .dark .deka-welcome__avatar {
            box-shadow: 0 0 0 2px rgb(24 24 27), 0 0 0 3.5px rgb(251 191 36 / 0.6);
        }
        .deka-welcome__title {
            margin: 0;
            font-size: 1.0625rem;
            font-weight: 600;
            line-height: 1.3;
            letter-spacing: -0.01em;
            text-wrap: balance;
        }
        .deka-welcome__meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem 0.875rem;
            margin: 0.25rem 0 0;
            padding: 0;
            list-style: none;
            font-size: 0.8125rem;
            color: #6b7280;
        }
        .dark .deka-welcome__meta { color: #a1a1aa; }
        .deka-welcome__meta li {
            display: inline-flex;
            align-items: center;
            gap: 0.3125rem;
            white-space: nowrap;
        }
        .deka-welcome__meta svg {
            width: 0.9375rem;
            height: 0.9375rem;
            flex-shrink: 0;
            color: #d97706;
        }
        .dark .deka-welcome__meta svg { color: #fbbf24; }
        .deka-welcome__aside {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }
        .deka-welcome__aside:empty { display: none; }
        @media (max-width: 480px) {
            .deka-welcome { padding: 0.75rem 0.875rem; }
            .deka-welcome__avatar { width: 2.25rem; height: 2.25rem; }
            .deka-welcome__aside { width: 100%; }
        }
    </style>

    <section
        class="deka-welcome"
        aria-label="{{ __('support::dashboard-banner.label') }}"
        x-data="{
            greetings: @js($greetings),
            locale: @js($locale),
            get greeting() {
                const hour = new Date().getHours();
                return hour < 12 ? this.greetings.morning : (hour < 17 ? this.greetings.afternoon : this.greetings.evening);
            },
            get today() {
                try {
                    return new Intl.DateTimeFormat(this.locale, { weekday: 'long', day: 'numeric', month: 'long' }).format(new Date());
                } catch (e) {
                    return new Date().toDateString();
                }
            },
        }"
    >
        <div class="deka-welcome__who">
            @if ($avatarUrl)
                <img class="deka-welcome__avatar" src="{{ $avatarUrl }}" alt="" loading="lazy" />
            @endif

            <div style="min-width: 0">
                <p class="deka-welcome__title">
                    <span x-text="greeting">{{ $fallbackGreeting }}</span>{{ $firstName ? ', '.$firstName : '' }}
                </p>

                <ul class="deka-welcome__meta">
                    @if ($role)
                        <li>
                            <x-filament::icon icon="heroicon-m-identification" />
                            <span>{{ $role }}</span>
                        </li>
                    @endif

                    @if ($company)
                        <li>
                            <x-filament::icon icon="heroicon-m-building-office-2" />
                            <span>{{ $company->name }}</span>
                        </li>
                    @endif

                    @if ($location)
                        <li>
                            <x-filament::icon icon="heroicon-m-map-pin" />
                            <span>{{ $location }}</span>
                        </li>
                    @endif

                    <li>
                        <x-filament::icon icon="heroicon-m-calendar-days" />
                        <span x-text="today">{{ now()->translatedFormat('l, j F') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        @if (trim((string) $aside) !== '')
            <div class="deka-welcome__aside">{!! $aside !!}</div>
        @endif
    </section>
@endif
