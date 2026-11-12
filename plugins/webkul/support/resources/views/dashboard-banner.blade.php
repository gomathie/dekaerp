{{--
    Dashboard welcome banner (Support; not tied to any plugin).

    2026-11-12 (user: "modern"): headline-first greeting with one friendly
    line, quiet outline pills for role / company / location, and the viewer's
    live local time as the visual focus on the right. No avatar (user,
    2026-11-12): the profile picture is already in the top bar.
    - Greeting, time and date come from the viewer's own clock and language in
      the browser. The server's clock said "Good morning" in the evening for
      anyone in another timezone. The server-rendered text is the
      no-JavaScript fallback.
    - The company shows once; the work location only when it differs.
    - Top margin: the page-start slot has no padding, so the banner touched
      the top bar.
    Other plugins add controls on the right through DashboardBannerRegistry
    (Attendance adds the clock in/out button).
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

    // The company is already a pill; naming it in the sentence too repeated it.
    $tagline = __('support::dashboard-banner.tagline-solo');

    $locale = str_replace('_', '-', app()->getLocale());
    $aside = \Webkul\Support\Filament\Contributions\DashboardBannerRegistry::renderedSections();

    // 2026-11-13 (user, after Dribbble finance dashboards): the line under the
    // greeting says what needs you today instead of a generic welcome, and
    // up to three "new" shortcuts sit beside it. Nothing to do keeps the
    // friendly tagline, so the banner never looks like an alarm.
    $summary = \Webkul\Support\Filament\Contributions\DashboardBannerRegistry::summaryPhrases();
    $quickActions = \Webkul\Support\Filament\Contributions\DashboardBannerRegistry::quickActions();
@endphp

@if ($user)
    <style>
        .deka-welcome {
            --dw-ink: #0f172a;
            --dw-muted: #64748b;
            --dw-line: rgb(15 23 42 / 0.08);
            --dw-pill: rgb(255 255 255 / 0.7);
            --dw-accent: #d97706;
            --dw-surface: #ffffff;
            /* One diagonal wash in the panel's primary (branding) colour,
               strongest behind the greeting and gone before the clock, so
               the right side stays calm (user, 2026-11-12: "gradient behind
               the greeting, clean design"). color-mix keeps it a tint of the
               surface rather than a saturated band. */
            --dw-tint-strong: color-mix(in oklab, var(--primary-500, #f59e0b) 16%, var(--dw-surface));
            --dw-tint-soft: color-mix(in oklab, var(--primary-400, #fbbf24) 7%, var(--dw-surface));

            position: relative;
            overflow: hidden;
            isolation: isolate;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem 2rem;
            margin-top: 1.5rem;
            margin-bottom: 1.25rem;
            padding: 1.25rem 1.5rem;
            border: 1px solid var(--dw-line);
            border-radius: 1rem;
            background: linear-gradient(115deg, var(--dw-tint-strong) 0%, var(--dw-tint-soft) 38%, var(--dw-surface) 72%);
            color: var(--dw-ink);
            box-shadow: 0 1px 2px rgb(15 23 42 / 0.04), 0 8px 24px -12px rgb(15 23 42 / 0.10);
        }
        .dark .deka-welcome {
            --dw-ink: #f8fafc;
            --dw-muted: #a1a1aa;
            --dw-line: rgb(255 255 255 / 0.08);
            --dw-pill: rgb(255 255 255 / 0.04);
            --dw-accent: #fbbf24;
            --dw-surface: rgb(24 24 27);
            --dw-tint-strong: color-mix(in oklab, var(--primary-500, #f59e0b) 20%, var(--dw-surface));
            --dw-tint-soft: color-mix(in oklab, var(--primary-600, #d97706) 8%, var(--dw-surface));
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.3), 0 8px 24px -12px rgb(0 0 0 / 0.5);
        }
        .deka-welcome__who {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
            flex: 1 1 22rem;
        }
        .deka-welcome__title {
            margin: 0;
            font-size: 1.375rem;
            font-weight: 650;
            line-height: 1.2;
            letter-spacing: -0.02em;
            text-wrap: balance;
        }
        .deka-welcome__tagline {
            margin: 0.25rem 0 0;
            font-size: 0.875rem;
            color: var(--dw-muted);
        }
        .deka-welcome__pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
            margin: 0.625rem 0 0;
            padding: 0;
            list-style: none;
        }
        .deka-welcome__pills li {
            display: inline-flex;
            align-items: center;
            gap: 0.3125rem;
            padding: 0.1875rem 0.625rem;
            border: 1px solid var(--dw-line);
            border-radius: 9999px;
            background: var(--dw-pill);
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--dw-muted);
            white-space: nowrap;
            backdrop-filter: blur(6px);
        }
        .deka-welcome__pills svg {
            width: 0.875rem;
            height: 0.875rem;
            color: var(--dw-accent);
        }
        .deka-welcome__today {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dw-ink);
            font-weight: 500;
        }
        .deka-welcome__dot {
            flex: none;
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background: var(--dw-accent);
            box-shadow: 0 0 0 3px color-mix(in oklab, var(--dw-accent) 22%, transparent);
        }
        .deka-welcome__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.875rem;
        }
        .deka-welcome__actions a {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            min-height: 2.25rem;
            padding: 0.375rem 0.875rem;
            border: 1px solid var(--dw-line);
            border-radius: 0.625rem;
            background: var(--dw-surface);
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--dw-ink);
            text-decoration: none;
            transition: border-color 120ms ease, box-shadow 120ms ease;
        }
        .deka-welcome__actions a svg {
            width: 1rem;
            height: 1rem;
            color: var(--primary-600, #d97706);
        }
        @media (hover: hover) {
            .deka-welcome__actions a:hover {
                border-color: color-mix(in oklab, var(--primary-500, #f59e0b) 45%, var(--dw-line));
                box-shadow: 0 2px 8px -4px rgb(15 23 42 / 0.18);
            }
        }
        .deka-welcome__actions a:focus-visible {
            outline: 2px solid var(--primary-500, #f59e0b);
            outline-offset: 2px;
        }
        .deka-welcome__side {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-left: auto;
        }
        .deka-welcome__clock {
            text-align: right;
            line-height: 1.1;
        }
        .deka-welcome__time {
            font-size: 1.875rem;
            font-weight: 650;
            letter-spacing: -0.03em;
            font-variant-numeric: tabular-nums;
        }
        .deka-welcome__date {
            margin-top: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
            color: var(--dw-muted);
        }
        .deka-welcome__aside {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-left: 1.25rem;
            border-left: 1px solid var(--dw-line);
        }
        /* Under the greeting the page title ("Overview") competed with it at
           Filament's 30px (user, 2026-11-12). This style tag exists only on
           pages that show the banner, so other pages keep their headings. */
        .fi-page:has(.deka-welcome) .fi-header-heading {
            font-size: 1.25rem;
            line-height: 1.75rem;
            font-weight: 600;
            letter-spacing: -0.01em;
        }
        @media (max-width: 640px) {
            .deka-welcome { margin-top: 1rem; padding: 1rem; gap: 0.875rem; }
            .deka-welcome__title { font-size: 1.1875rem; }
            .deka-welcome__side { width: 100%; justify-content: space-between; margin-left: 0; }
            .deka-welcome__clock { text-align: left; }
            .deka-welcome__time { font-size: 1.5rem; }
            .deka-welcome__aside { padding-left: 0; border-left: 0; }
        }
    </style>

    <section
        class="deka-welcome"
        aria-label="{{ __('support::dashboard-banner.label') }}"
        x-data="{
            greetings: @js($greetings),
            locale: @js($locale),
            now: new Date(),
            init() {
                this.timer = setInterval(() => { this.now = new Date() }, 30000);
            },
            destroy() {
                clearInterval(this.timer);
            },
            get greeting() {
                const hour = this.now.getHours();
                return hour < 12 ? this.greetings.morning : (hour < 17 ? this.greetings.afternoon : this.greetings.evening);
            },
            format(options) {
                try {
                    return new Intl.DateTimeFormat(this.locale, options).format(this.now);
                } catch (e) {
                    return this.now.toLocaleString();
                }
            },
        }"
    >
        <div class="deka-welcome__who">
            <div style="min-width: 0">
                <p class="deka-welcome__title">
                    <span x-text="greeting">{{ $fallbackGreeting }}</span>{{ $firstName ? ', '.$firstName : '' }}
                </p>

                @if ($summary !== [])
                    <p class="deka-welcome__tagline deka-welcome__today">
                        <span class="deka-welcome__dot" aria-hidden="true"></span>
                        {{ implode(' · ', $summary) }}
                    </p>
                @else
                    <p class="deka-welcome__tagline">{{ $tagline }}</p>
                @endif

                <ul class="deka-welcome__pills">
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
                </ul>

                @if ($quickActions !== [])
                    <nav class="deka-welcome__actions" aria-label="{{ __('support::dashboard-banner.quick-actions') }}">
                        @foreach ($quickActions as $quickAction)
                            <a href="{{ $quickAction['url'] }}" wire:navigate>
                                <x-filament::icon :icon="$quickAction['icon']" />
                                <span>{{ $quickAction['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                @endif
            </div>
        </div>

        <div class="deka-welcome__side">
            <div class="deka-welcome__clock" aria-label="{{ __('support::dashboard-banner.local-time') }}">
                <div class="deka-welcome__time" x-text="format({ hour: '2-digit', minute: '2-digit' })">{{ now()->format('H:i') }}</div>
                <div class="deka-welcome__date" x-text="format({ weekday: 'long', day: 'numeric', month: 'long' })">{{ now()->translatedFormat('l, j F') }}</div>
            </div>

            @if (trim((string) $aside) !== '')
                <div class="deka-welcome__aside">{!! $aside !!}</div>
            @endif
        </div>
    </section>
@endif
