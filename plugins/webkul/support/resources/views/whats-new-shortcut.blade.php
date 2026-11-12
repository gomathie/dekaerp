@php
    $label = $unseen
        ? __('support::filament/pages/whats-new.shortcut.unseen')
        : __('support::filament/pages/whats-new.shortcut.label');
@endphp

<a
    href="{{ $url }}"
    title="{{ $label }}"
    aria-label="{{ $label }}"
    class="deka-whats-new-shortcut"
    style="position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; border-radius: .5rem; color: rgb(107 114 128);"
>
    <x-filament::icon icon="heroicon-o-megaphone" class="size-5" />

    @if ($unseen)
        <span
            aria-hidden="true"
            data-whats-new-dot
            style="position: absolute; top: .4rem; right: .4rem; width: .5rem; height: .5rem; border-radius: 9999px; background: rgb(var(--danger-500)); box-shadow: 0 0 0 2px var(--color-white, #fff);"
        ></span>
    @endif
</a>
