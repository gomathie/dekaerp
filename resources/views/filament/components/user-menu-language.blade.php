{{--
    Language choice inside the profile pop-up (user, 2026-11-11: the admin
    panel had no switcher).

    Shown once: one row with the current language; clicking it opens the
    others below it, inside the same menu (user, 2026-11-11, after complaints
    that five rows of languages cluttered the menu). It expands in place
    rather than opening a second dropdown, which would be awkward on phones.

    Each choice sends ?lang=, which SetLocale keeps in the session (UF fix
    2026-10-22). The link is built in the browser from the current address,
    so filters and other query values survive, because this menu is
    re-rendered by Livewire, where the server-side URL is the Livewire
    endpoint. Locale codes come from config('app.supported_locales') and are
    letters and underscores only.
--}}
@php
    $locales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();
    $currentLabel = $locales[$currentLocale]['native'] ?? strtoupper($currentLocale);
@endphp

@if (count($locales) > 1)
    <x-filament::dropdown.list>
        <div x-data="{ languagesOpen: false }" class="user-menu-language">
            <button
                type="button"
                class="fi-dropdown-list-item"
                x-on:click.stop="languagesOpen = ! languagesOpen"
                x-bind:aria-expanded="languagesOpen.toString()"
                aria-haspopup="true"
            >
                <x-filament::icon icon="heroicon-o-language" class="fi-dropdown-list-item-icon" />

                <span class="fi-dropdown-list-item-label">{{ $currentLabel }}</span>

                <x-filament::icon
                    icon="heroicon-m-chevron-down"
                    class="user-menu-language-chevron"
                    x-bind:class="{ 'user-menu-language-chevron--open': languagesOpen }"
                />
            </button>

            <div x-cloak x-show="languagesOpen" x-transition.opacity.duration.150ms class="user-menu-language-options" role="menu">
                @foreach ($locales as $code => $locale)
                    @continue($code === $currentLocale)

                    <a
                        href="?lang={{ $code }}"
                        lang="{{ $code }}"
                        role="menuitem"
                        class="fi-dropdown-list-item"
                        x-on:click.prevent="const url = new URL(window.location.href); url.searchParams.set('lang', '{{ $code }}'); window.location.assign(url.toString())"
                    >
                        <span class="fi-dropdown-list-item-label">{{ $locale['native'] ?? strtoupper($code) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </x-filament::dropdown.list>

    <style>
        .user-menu-language .fi-dropdown-list-item {
            width: 100%;
        }

        .user-menu-language-chevron {
            width: 1rem;
            height: 1rem;
            margin-inline-start: auto;
            color: var(--gray-400);
            transition: transform 150ms ease;
        }

        .user-menu-language-chevron--open {
            transform: rotate(180deg);
        }

        .user-menu-language-options .fi-dropdown-list-item {
            padding-inline-start: 2.5rem;
        }

        @media (prefers-reduced-motion: reduce) {
            .user-menu-language-chevron {
                transition: none;
            }
        }
    </style>
@endif
