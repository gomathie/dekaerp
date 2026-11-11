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
  