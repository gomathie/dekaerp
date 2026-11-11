@php
    $user = filament()->auth()->user();
    $context = app(\Webkul\Support\Services\CompanyContext::class);
    $company = $context->currentCompany();

    $hour = (int) now()->format('G');
    $greeting = match (true) {
        $hour < 12 => __('support::dashboard-banner.greetings.morning'),
        $hour < 17 => __('support::dashboard-banner.greetings.afternoon'),
        default => __('support::dashboard-banner.greetings.evening'),
    };

    $name = trim((string) $user?->name);
    $firstName = $name === '' ? null : explode(' ', $name)[0];

    $roles = $user ? $user->effectiveRoleNames() : [];
    $role = $roles === [] ? null : $roles[0];

    $location = \Webkul\Support\Filament\Contributions\DashboardBannerRegistry::resolvedLocation()
        ?? $company?->name;
@endphp

@if ($user)
    <style>
        .deka-welcome {
            --deka-brand: #f59e0b;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 1rem 1.25rem;
            border: 1px solid rgb(0 0 0 / 0.06);
            border-radius: 0.9rem;
            background: linear-gradient(135deg, rgb(245 158 11 / 0.10), rgb(255 255 255 / 0.9));
            color: #111827;
        }
        .dark .deka-welcome {
            border-color: rgb(255 255 255 / 0.10);
            background: linear-gradient(135deg, rgb(245 158 11 / 0.16), rgb(24 24 27 / 0.9));
            color: #f4f4f5;
        }
        .deka-welcome__title {
            font-size: 1.05rem;
            font-weight: 600;
            line-height: 1.35;
        }
        .deka-welcome__subtle {
            font-size: 0.8125rem;
            color: #52525b;
        }
        .dark .deka-welcome__subtle { color: #a1a1aa; }
        .deka-welcome__chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.375rem;
            margin-top: 0.375rem;
        }
        .deka-welcome__chip {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            background: rgb(245 158 11 / 0.16);
            color: #92400e;
        }
        .dark .deka-welcome__chip { color: #fcd34d; }
        .deka-welcome__aside {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }
        @media (max-width: 480px) {
            .deka-welcome { padding: 0.875rem; }
            .deka-welcome__aside { width: 100%; }
        }
    </style>

    <section class="deka-welcome" role="region" aria-label="{{ __('support::dashboard-banner.label') }}">
        <div>
            <p class="deka-welcome__title">
                {{ $greeting }}{{ $firstName ? ', '.$firstName : '' }}
            </p>

            <p class="deka-welcome__subtle">
                {{ $location
                    ? __('support::dashboard-banner.working-at', ['location' => $location])
                    : __('support::dashboard-banner.working-here') }}
            </p>

            <div class="deka-welcome__chips">
                @if ($role)
                    <span class="deka-welcome__chip">{{ $role }}</span>
                @endif

                @if ($company)
                    <span class="deka-welcome__chip">{{ $company->name }}</span>
                @endif

                <span class="deka-welcome__chip">{{ now()->translatedFormat('D, j M Y') }}</span>
            </div>
        </div>

        <div class="deka-welcome__aside">
            {!! \Webkul\Support\Filament\Contributions\DashboardBannerRegistry::renderedSections() !!}
        </div>
    </section>
@endif
