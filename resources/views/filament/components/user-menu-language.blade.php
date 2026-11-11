{{--
    Language choice inside the profile pop-up (user, 2026-11-11: the admin
    panel had no switcher).

    Design (user, 2026-11-11, after complaints that five rows of languages
    cluttered the menu): one row, "Language" with the current code in a pill.
    Clicking it reveals a strip of short codes (EN AR ES FR PT-BR) in place,
    not a second dropdown, which is awkward on phones. The current code is
    filled in the accent colour; each chip carries the full native name as
    its tooltip and accessible name, and its own lang attribute.

    Each choice sends ?lang=, which SetLocale keeps in the session (UF fix
    2026-10-22). The link is built in the browser from the current address so
    filters survive: this menu is re-rendered by Livewire, where the
    server-side URL is the Livewire endpoint. Codes come from
    config('app.supported_locales') and are letters and underscores only.
--}}
@php
    $locales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();
    $shortCode = fn (string $code): string => strtoupper(str_replace('_', '-', $code));
@endphp

@if (count($locales) > 1)
    <x-filament::dropdown.list>
        <div x-data="{ languagesOpen: false }" class="uml">
            <button
                type="button"
                class="fi-dropdown-list-item uml-toggle"
                x-on:click.stop="languagesOpen = ! languagesOpen"
                x-bind:aria-expanded="languagesOpen.toString()"
                aria-controls="user-menu-languages"
            >
                <x-filament::icon icon="heroicon-o-language" class="fi-dropdown-list-item-icon" />

                <span class="fi-dropdown-list-item-label">{{ __('support::filament/pages/profile.fields.language') }}</span>

                <span class="uml-current" lang="{{ $currentLocale }}">{{ $shortCode($currentLocale) }}</span>

                <x-filament::icon
                    icon="heroicon-m-chevron-down"
                    class="uml-chevron"
                    x-bind:class="{ 'uml-chevron--open': languagesOpen }"
                />
            </button>

            <div
                id="user-menu-languages"
                x-cloak
                x-show="languagesOpen"
                x-transition:enter="uml-enter"
                x-transition:enter-start="uml-enter-start"
                x-transition:enter-end="uml-enter-end"
                class="uml-chips"
                role="group"
                aria-label="{{ __('support::filament/pages/profile.fields.language') }}"
            >
                @foreach ($locales as $code => $locale)
                    @php($isCurrent = $code === $currentLocale)

                    <a
                        href="?lang={{ $code }}"
                        lang="{{ $code }}"
                        title="{{ $locale['native'] ?? $code }}"
                        aria-label="{{ $locale['native'] ?? $code }}"
                        @if ($isCurrent) aria-current="true" @endif
                        @class(['uml-chip', 'uml-chip--current' => $isCurrent])
                        x-on:click.prevent="const url = new URL(window.location.href); url.searchParams.set('lang', '{{ $code }}'); window.location.assign(url.toString())"
                    >{{ $shortCode($code) }}</a>
                @endforeach
            </div>
        </div>
    </x-filament::dropdown.list>

    <style>
        .uml-toggle {
            width: 100%;
        }

        .uml-current {
            margin-inline-start: auto;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            font-variant-numeric: tabular-nums;
            color: #B45309;
            background: rgb(245 158 11 / 0.14);
        }

        .dark .uml-current {
            color: #FCD34D;
            background: rgb(251 191 36 / 0.16);
        }

        .uml-chevron {
            width: 1rem;
            height: 1rem;
            color: var(--gray-400);
            transition: transform 160ms ease;
        }

        .uml-chevron--open {
            transform: rotate(180deg);
        }

        .uml-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
            padding: 0.25rem 0.5rem 0.5rem 2.5rem;
        }

        .uml-chip {
            min-width: 2.5rem;
            padding: 0.3125rem 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid var(--gray-200);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-align: center;
            color: var(--gray-700);
            transition: background-color 120ms ease, border-color 120ms ease, color 120ms ease;
        }

        .uml-chip:hover,
        .uml-chip:focus-visible {
            border-color: #F59E0B;
            color: #B45309;
            background: rgb(245 158 11 / 0.08);
            outline: none;
        }

        .uml-chip:focus-visible {
            box-shadow: 0 0 0 2px rgb(245 158 11 / 0.45);
        }

        .uml-chip--current,
        .uml-chip--current:hover {
            border-color: #F59E0B;
            color: #fff;
            background: #F59E0B;
        }

        .dark .uml-chip {
            border-color: rgb(255 255 255 / 0.1);
            color: var(--gray-200);
        }

        .dark .uml-chip:hover,
        .dark .uml-chip:focus-visible {
            border-color: #FBBF24;
            color: #FCD34D;
            background: rgb(251 191 36 / 0.1);
        }

        .dark .uml-chip--current,
        .dark .uml-chip--current:hover {
            border-color: #FBBF24;
            color: #1F2937;
            background: #FBBF24;
        }

        .uml-enter {
            transition: opacity 150ms ease, transform 150ms ease;
        }

        .uml-enter-start {
            opacity: 0;
            transform: translateY(-4px);
        }

        .uml-enter-end {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .uml-chevron,
            .uml-chip,
            .uml-enter {
                transition: none;
            }
        }
    </style>
@endif
