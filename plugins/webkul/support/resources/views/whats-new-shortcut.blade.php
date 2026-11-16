{{-- What's New in the profile menu (user, 2026-11-12: moved out of the top
     bar to keep it short). While the newest release is unread (D-WN-2) a dot
     shows on the entry and, so it is noticed with the menu closed, on the
     avatar. --}}
@php
    $label = $unseen
        ? __('support::filament/pages/whats-new.shortcut.unseen')
        : __('support::filament/pages/whats-new.shortcut.label');
@endphp

<x-filament::dropdown.list>
    <x-filament::dropdown.list.item tag="a" :href="$url" icon="heroicon-o-megaphone" :title="$label">
        <span style="display: inline-flex; align-items: center; gap: .5rem;">
            {{ __('support::filament/pages/whats-new.shortcut.label') }}

            <span style="color: rgb(107 114 128); font-size: .75rem;">
                {{ __('support::support.version', ['version' => $version]) }}
            </span>

            @if ($unseen)
                <span
                    data-whats-new-dot
                    role="img"
                    aria-label="{{ $label }}"
                    style="width: .5rem; height: .5rem; border-radius: 9999px; background: rgb(var(--danger-500));"
                ></span>
            @endif
        </span>
    </x-filament::dropdown.list.item>
</x-filament::dropdown.list>

@if ($unseen)
    <style>
        .fi-user-menu:has([data-whats-new-dot]) .fi-user-menu-trigger { position: relative; }
        .fi-user-menu:has([data-whats-new-dot]) .fi-user-menu-trigger::after {
            content: ''; position: absolute; top: 0; inset-inline-end: 0;
            width: .625rem; height: .625rem; border-radius: 9999px;
            background: rgb(var(--danger-500)); box-shadow: 0 0 0 2px var(--color-white, #fff);
        }
    </style>
@endif
