{{--
    Language choice inside the profile pop-up (user, 2026-11-11: the admin
    panel had no switcher). It sends ?lang=, which SetLocale keeps in the
    session (UF fix 2026-10-22). The link is built in the browser from the
    current address, because this menu is re-rendered by Livewire, where the
    server-side request URL is the Livewire endpoint, not the page.
--}}
@php
    $locales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();
@endphp

@if (count($locales) > 1)
    <x-filament::dropdown.list>
        @foreach ($locales as $code => $locale)
            <x-filament::dropdown.list.item
                tag="a"
                href="?lang={{ $code }}"
                :icon="$currentLocale === $code ? 'heroicon-m-check' : 'heroicon-o-language'"
                :color="$currentLocale === $code ? 'primary' : 'gray'"
                x-on:click.prevent="const url = new URL(window.location.href); url.searchParams.set('lang', @js($code)); window.location.assign(url.toString())"
                :lang="$code"
            >
                {{ $locale['native'] ?? strtoupper($code) }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
@endif
