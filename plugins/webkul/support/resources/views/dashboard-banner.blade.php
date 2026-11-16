{{--
    Dashboard welcome banner (Support; not tied to any plugin).

    2026-11-13, rebuilt from the approved mockup (user, after the most-viewed
    ERP dashboards on Dribbble): a date and location line, the greeting, one
    plain sentence saying how many things need you, those things as linked
    chips coloured by urgency, and on the right the local clock with one
    primary quick action above quieter ones.
    Kept from the 2026-11-12 version, each for a reason:
    - Greeting, time and date come from the viewer's own clock and language in
      the browser; the server's clock said "Good morning" in the evening for
      anyone in another timezone. Server-rendered text is the no-JS fallback.
    - No avatar: the profile picture is already in the top bar.
    - Top margin: the page-start slot has no padding.
    - The page title under the banner is smaller (it competed at 30px).
    Other plugins add controls through DashboardBannerRegistry: the "today"
    items and quick actions (Invoices, Sales, Purchases) and side sections
    (Attendance's clock in/out). Nothing to do keeps the friendly sentence.
--}}
@php
    $registry = \Webkul\Support\Filament\Contributions\DashboardBannerRegistry::class;

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

    // Where the person works: their work location when a plugin knows it,
    // otherwise the company.
    $place = $registry::resolvedLocation() ?? $company?->name;

    $items = $registry::summaryItems();
    $quickActions = $registry::quickActions();
    $primaryAction = $quickActions[0] ?? null;
    $otherActions = array_slice($quickActions, 1);

    $sentence = $items === []
        ? __('support::dashboard-banner.tagline-solo')
        : trans_choice('support::dashboard-banner.needs-you', count($items), ['count' => count($items)]);

    $locale = str_replace('_', '-', app()->getLocale());
    $aside = $registry::renderedSections();
@endphp

@if ($user)
    <style>
        .deka-welcome {
            --dw-ink: #18181b;
            --dw-muted: #71717a;
            --dw-line: rgb(24 24 27 / 0.09);
            --dw-surface: #ffffff;
            --dw-accent: var(--primary-500, #f59e0b);
            --dw-wash: color-mix(in oklab, var(--primary-500, #f59e0b) 16%, transparent);

            position: relative;
            overflow: hidden;
            isolation: isolate;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            justify-content: space-between;
            gap: 1.25rem 2rem;
            margin-top: 1.5rem;
            margin-bottom: 1.25rem;
            padding: 1.375rem 1.5rem;
            border: 1px solid var(--dw-line);
            border-radius: 1.125rem;
            background: var(--dw-surface);
            color: var(--dw-ink);
            box-shadow: 0 1px 2px rgb(24 24 27 / 0.04);
        }
        .dark .deka-welcome {
            --dw-ink: #f4f4f5;
            --dw-muted: #a1a1aa;
            --dw-line: rgb(255 255 255 / 0.08);
            --dw-surface: rgb(24 24 27);
            --dw-wash: color-mix(in oklab, var(--primary-500, #f59e0b) 20%, transparent);
            box-shadow: none;
        }
        /* One soft wash in the company's brand colour, top-left only, so the
           clock and buttons on the right stay calm. */
        .deka-welcome::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background: radial-gradient(120% 140% at 0% 0%, var(--dw-wash) 0%, transparent 55%);
        }
        .deka-welcome__main { flex: 1 1 22rem; min-width: 0; }
        .deka-welcome__eyebrow { margin: 0 0 0.375rem; font-size: 0.75rem; font-weight: 550; color: var(--dw-muted); text-transform: capitalize; }
        .deka-welcome__title { margin: 0; font-size: 1.625rem; font-weight: 700; line-height: 1.15; letter-spacing: -0.025em; text-wrap: balance; }
        .deka-welcome__sentence { margin: 0.375rem 0 0; font-size: 0.875rem; color: var(--dw-muted); }
        .deka-welcome__chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin: 0.875rem 0 0; padding: 0; list-style: none; }
        .deka-welcome__chip {
            display: inline-flex; align-items: center; gap: 0.375rem;
            padding: 0.3125rem 0.75rem; border: 1px solid var(--dw-line); border-radius: 9999px;
            background: var(--dw-surface); color: var(--dw-ink);
            font-size: 0.8125rem; font-weight: 550; text-decoration: none;
        }
        a.deka-welcome__chip:focus-visible { outline: 2px solid var(--dw-accent); outline-offset: 2px; }
        @media (hover: hover) { a.deka-welcome__chip:hover { border-color: var(--dw-accent); } }
        .deka-welcome__dot { width: 0.4375rem; height: 0.4375rem; border-radius: 9999px; background: var(--dw-tone); flex: none; }
        .deka-welcome__chip b { font-weight: 700; font-variant-numeric: tabular-nums; }
        .deka-welcome__side { display: flex; flex-direction: column; justify-content: space-between; align-items: flex-end; gap: 0.875rem; }
        .deka-welcome__clock { text-align: right; line-height: 1.1; }
        .deka-welcome__time { display: block; font-size: 1.75rem; font-weight: 650; letter-spacing: -0.03em; font-variant-numeric: tabular-nums; }
        .deka-welcome__date { font-size: 0.75rem; color: var(--dw-muted); }
        .deka-welcome__actions { display: grid; gap: 0.375rem; justify-items: end; }
        .deka-welcome__row { display: flex; flex-wrap: wrap; gap: 0.375rem; justify-content: flex-end; }
        .deka-welcome__btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem;
            min-height: 2.25rem; padding: 0.375rem 0.875rem; border-radius: 0.625rem;
            border: 1px solid var(--dw-line); background: var(--dw-surface); color: var(--dw-ink);
            font-size: 0.8125rem; font-weight: 600; text-decoration: none; white-space: nowrap;
        }
        .deka-welcome__btn svg { width: 1rem; height: 1rem; }
        .deka-welcome__btn--primary { width: 100%; background: var(--primary-500, #f59e0b); border-color: var(--primary-500, #f59e0b); color: var(--primary-950, #1c1203); }
        @media (hover: hover) {
            .deka-welcome__btn:hover { border-color: var(--dw-accent); }
            .deka-welcome__btn--primary:hover { background: var(--primary-400, #fbbf24); }
        }
        .deka-welcome__btn:focus-visible { outline: 2px solid var(--dw-accent); outline-offset: 2px; }
        .deka-welcome__aside { display: flex; align-items: center; gap: 0.5rem; }
        .fi-page:has(.deka-welcome) .fi-header-heading { font-size: 1.25rem; line-height: 1.75rem; font-weight: 600; letter-spacing: -0.01em; }
        @media (max-width: 640px) {
            .deka-welcome { margin-top: 1rem; padding: 1.125rem; }
            .deka-welcome__title { font-size: 1.375rem; }
            .deka-welcome__side { flex-direction: row; flex-wrap: wrap; align-items: center; width: 100%; }
            .deka-welcome__clock { text-align: left; }
            .deka-welcome__actions { justify-items: stretch; width: 100%; }
            .deka-welcome__row { justify-content: flex-start; }
        }
    </style>

    <section
        class="deka-welcome"
        aria-label="{{ __('support::dashboard-banner.label') }}"
        x-data="{
            greetings: @js($greetings),
            locale: @js($locale),
            now: new Date(),
            init() { this.timer = setInterval(() => { this.now = new Date() }, 30000) },
            destroy() { clearInterval(this.timer) },
            get greeting() {
                const hour = this.now.getHours();
                return hour < 12 ? this.greetings.morning : (hour < 17 ? this.greetings.afternoon : this.greetings.evening);
            },
            format(options) {
                try { return new Intl.DateTimeFormat(this.locale, options).format(this.now) } catch (e) { return this.now.toLocaleString() }
            },
        }"
    >
        <div class="deka-welcome__main">
            <p class="deka-welcome__eyebrow">
                <span x-text="format({ weekday: 'long', day: 'numeric', month: 'long' })">{{ now()->translatedFormat('l, j F') }}</span>@if ($place) · {{ $place }}@endif
            </p>

            <p class="deka-welcome__title">
                <span x-text="greeting">{{ $fallbackGreeting }}</span>{{ $firstName ? ', '.$firstName : '' }}
            </p>

            <p class="deka-welcome__sentence">{{ $sentence }}</p>

            @if ($items !== [])
                <ul class="deka-welcome__chips">
                    @foreach ($items as $item)
                        @php($tone = "var(--{$item['tone']}-500)")
                        <li>
                            @if ($item['url'])
                                <a class="deka-welcome__chip" href="{{ $item['url'] }}" wire:navigate style="--dw-tone: {{ $tone }}">
                                    <span class="deka-welcome__dot" aria-hidden="true"></span>{{ $item['label'] }}
                                </a>
                            @else
                                <span class="deka-welcome__chip" style="--dw-tone: {{ $tone }}">
                                    <span class="deka-welcome__dot" aria-hidden="true"></span>{{ $item['label'] }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="deka-welcome__side">
            <div class="deka-welcome__clock" aria-label="{{ __('support::dashboard-banner.local-time') }}">
                <span class="deka-welcome__time" x-text="format({ hour: '2-digit', minute: '2-digit' })">{{ now()->format('H:i') }}</span>
                @if (trim((string) $aside) !== '')
                    <div class="deka-welcome__aside">{!! $aside !!}</div>
                @endif
            </div>

            @if ($primaryAction)
                <nav class="deka-welcome__actions" aria-label="{{ __('support::dashboard-banner.quick-actions') }}">
                    <a class="deka-welcome__btn deka-welcome__btn--primary" href="{{ $primaryAction['url'] }}" wire:navigate>
                        <x-filament::icon :icon="$primaryAction['icon']" />{{ $primaryAction['label'] }}
                    </a>
                    @if ($otherActions !== [])
                        <div class="deka-welcome__row">
                            @foreach ($otherActions as $quickAction)
                                <a class="deka-welcome__btn" href="{{ $quickAction['url'] }}" wire:navigate>{{ $quickAction['label'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </nav>
            @endif
        </div>
    </section>
@endif
