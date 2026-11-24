{{--
    The period bar (DB-4 mockup, approved 2026-11-23): the period is always
    visible and says what it is compared with. It used to sit inside the
    collapsed "Filters" panel, so the dashboard never said which dates its
    figures covered. Other dates stay in Filters.
--}}
<style>
    .overview-period { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem 1rem; }
    .overview-period__presets { display: flex; flex-wrap: wrap; gap: 0.25rem; padding: 0.1875rem; border-radius: 0.625rem; background: var(--gray-100); }
    .dark .overview-period__presets { background: rgb(255 255 255 / 0.06); }
    .overview-period__preset {
        min-height: 2.25rem; padding: 0 0.875rem; border: 0; border-radius: 0.5rem;
        background: transparent; color: var(--gray-600); font-size: 0.8125rem; font-weight: 500; cursor: pointer;
    }
    .dark .overview-period__preset { color: var(--gray-300); }
    .overview-period__preset[aria-pressed="true"] { background: #fff; color: var(--gray-950); font-weight: 600; box-shadow: 0 1px 2px rgb(24 24 27 / 0.12); }
    .dark .overview-period__preset[aria-pressed="true"] { background: var(--gray-800); color: #fff; }
    .overview-period__preset:focus-visible { outline: 2px solid var(--primary-500); outline-offset: 1px; }
    .overview-period__text { margin: 0; font-size: 0.8125rem; color: var(--gray-600); font-variant-numeric: tabular-nums; }
    .dark .overview-period__text { color: var(--gray-400); }
</style>

<div class="overview-period">
    <div class="overview-period__presets" role="group" aria-label="{{ __('invoices::filament/pages/overview.period.label') }}">
        @foreach ($presets as $key => $label)
            <button
                type="button"
                class="overview-period__preset"
                aria-pressed="{{ $active === $key ? 'true' : 'false' }}"
                wire:click="selectPeriod('{{ $key }}')"
                wire:loading.attr="disabled"
            >{{ $label }}</button>
        @endforeach
        @if ($active === null)
            <span class="overview-period__preset" aria-pressed="true" style="display: inline-flex; align-items: center;">{{ __('invoices::filament/pages/overview.period.custom') }}</span>
        @endif
    </div>

    <p class="overview-period__text">
        {{ __('invoices::filament/pages/overview.period.compared', ['period' => $period, 'previous' => $previous]) }}
        @if ($active === null)
            · {{ __('invoices::filament/pages/overview.period.custom-hint') }}
        @endif
    </p>
</div>
