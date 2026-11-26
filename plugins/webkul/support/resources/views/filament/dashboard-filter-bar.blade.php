{{--
    The dashboard header row (user, 2026-11-25, approved mockup): period
    chips, "Custom dates", the dates shown with the period they are compared
    with, and a "Filters" button with the number of filters in use.

    It replaces two controls that set the same dates: the period bar (DB-4)
    and a date range inside a full-width "Filters" section. Dates are set here
    only. The filters form is this component's child schema and is rendered
    inside the Filters panel, so its fields, keys and live updates are the
    ones the page already had (HasDashboardFilterBar).

    On a computer everything sits on one row and the caption is cut with an
    ellipsis rather than wrapping. Below 1024px the caption and the Filters
    button move to a second line; on a phone the chips scroll in one line,
    "Custom dates" is an icon, and both panels open as bottom sheets (the
    house pattern of `deka-sale-currency-sheet`).

    Plain CSS on purpose: classes in a plugin view need a support.css rebuild,
    and this view needs none.
--}}
@php
    $caption = __('support::filament/dashboard.period.compared', ['period' => $period, 'previous' => $previous]);
    $inUseCount = count($inUse);
    $filtersTitle = $inUseCount > 0
        ? __('support::filament/dashboard.filters-in-use', ['filters' => implode(', ', $inUse)])
        : __('support::filament/dashboard.no-filters-in-use');
@endphp

<style>
    .deka-dash-bar {
        position: relative;
        z-index: 20;
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.625rem;
        min-width: 0;
    }
    .deka-dash-bar__presets {
        display: flex;
        flex: none;
        gap: 0.25rem;
        padding: 0.1875rem;
        border-radius: 0.625rem;
        background: var(--gray-100);
    }
    .dark .deka-dash-bar__presets { background: rgb(255 255 255 / 0.06); }
    .deka-dash-bar__preset {
        flex: none;
        min-height: 2.25rem;
        padding: 0 0.875rem;
        border: 0;
        border-radius: 0.5rem;
        background: transparent;
        color: var(--gray-600);
        font-size: 0.8125rem;
        font-weight: 500;
        white-space: nowrap;
        cursor: pointer;
    }
    .dark .deka-dash-bar__preset { color: var(--gray-300); }
    .deka-dash-bar__preset[aria-pressed="true"] { background: #fff; color: var(--gray-950); font-weight: 600; box-shadow: 0 1px 2px rgb(24 24 27 / 0.12); }
    .dark .deka-dash-bar__preset[aria-pressed="true"] { background: var(--gray-800); color: #fff; }

    .deka-dash-bar__anchor { position: relative; flex: none; }
    .deka-dash-bar__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        min-height: 2.5rem;
        padding: 0 0.875rem;
        border: 1px solid var(--gray-200);
        border-radius: 0.625rem;
        background: #fff;
        color: var(--gray-700);
        font-size: 0.8125rem;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
    }
    .dark .deka-dash-bar__btn { border-color: rgb(255 255 255 / 0.1); background: var(--gray-900); color: var(--gray-200); }
    .deka-dash-bar__btn svg { width: 1rem; height: 1rem; flex: none; }
    .deka-dash-bar__btn[data-active="true"] { border-color: var(--primary-500); color: var(--gray-950); box-shadow: inset 0 0 0 1px var(--primary-500); }
    .dark .deka-dash-bar__btn[data-active="true"] { color: #fff; }
    @media (hover: hover) {
        .deka-dash-bar__btn:hover { border-color: var(--gray-300); }
        .dark .deka-dash-bar__btn:hover { border-color: rgb(255 255 255 / 0.2); }
        .deka-dash-bar__preset:not([aria-pressed="true"]):hover { color: var(--gray-950); }
        .dark .deka-dash-bar__preset:not([aria-pressed="true"]):hover { color: #fff; }
    }
    .deka-dash-bar__preset:focus-visible,
    .deka-dash-bar__btn:focus-visible,
    .deka-dash-bar__action:focus-visible,
    .deka-dash-bar__input:focus-visible { outline: 2px solid var(--primary-500); outline-offset: 1px; }
    .deka-dash-bar__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.25rem;
        height: 1.25rem;
        padding: 0 0.375rem;
        border-radius: 9999px;
        background: var(--primary-500);
        color: var(--primary-950, #1c1203);
        font-size: 0.6875rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .deka-dash-bar__caption {
        flex: 1 1 auto;
        min-width: 0;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.8125rem;
        color: var(--gray-600);
        font-variant-numeric: tabular-nums;
    }
    .dark .deka-dash-bar__caption { color: var(--gray-400); }
    .deka-dash-bar__filters { margin-inline-start: auto; }

    .deka-dash-bar__popover {
        position: absolute;
        top: calc(100% + 0.5rem);
        z-index: 30;
        display: flex;
        flex-direction: column;
        border: 1px solid var(--gray-200);
        border-radius: 0.875rem;
        background: #fff;
        color: var(--gray-950);
        box-shadow: 0 12px 32px -8px rgb(24 24 27 / 0.25), 0 2px 6px rgb(24 24 27 / 0.08);
    }
    .dark .deka-dash-bar__popover { border-color: rgb(255 255 255 / 0.1); background: var(--gray-900); color: #fff; }
    .deka-dash-bar__popover:focus { outline: none; }
    .deka-dash-bar__popover--dates { inset-inline-start: 0; width: 20rem; }
    .deka-dash-bar__popover--filters { inset-inline-end: 0; width: min(40rem, calc(100vw - 2rem)); }
    .deka-dash-bar__head {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem 0;
        font-size: 0.9375rem;
        font-weight: 650;
    }
    .deka-dash-bar__body { padding: 0.875rem 1rem; }
    .deka-dash-bar__popover--filters .deka-dash-bar__body { max-height: min(60vh, 32rem); overflow-y: auto; }
    .deka-dash-bar__dates { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
    .deka-dash-bar__field { display: grid; gap: 0.25rem; font-size: 0.8125rem; font-weight: 500; color: var(--gray-700); }
    .dark .deka-dash-bar__field { color: var(--gray-300); }
    .deka-dash-bar__input {
        min-height: 2.5rem;
        padding: 0 0.625rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        background: #fff;
        color: var(--gray-950);
        font-size: 0.875rem;
        font-variant-numeric: tabular-nums;
    }
    .dark .deka-dash-bar__input { border-color: rgb(255 255 255 / 0.15); background: var(--gray-950); color: #fff; color-scheme: dark; }
    .deka-dash-bar__foot {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-top: 1px solid var(--gray-200);
    }
    .dark .deka-dash-bar__foot { border-top-color: rgb(255 255 255 / 0.08); }
    .deka-dash-bar__action {
        min-height: 2.25rem;
        padding: 0 0.875rem;
        border: 1px solid var(--gray-200);
        border-radius: 0.5rem;
        background: #fff;
        color: var(--gray-700);
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
    }
    .dark .deka-dash-bar__action { border-color: rgb(255 255 255 / 0.1); background: var(--gray-800); color: var(--gray-200); }
    .deka-dash-bar__action--primary { border-color: var(--primary-500); background: var(--primary-500); color: var(--primary-950, #1c1203); }
    .dark .deka-dash-bar__action--primary { border-color: var(--primary-500); background: var(--primary-500); color: var(--primary-950, #1c1203); }
    .deka-dash-bar__action:disabled { opacity: 0.5; cursor: not-allowed; }
    .deka-dash-bar__backdrop { display: none; }

    /* Tablet: the caption and Filters move to a second line rather than
       squeezing the caption to nothing. */
    @media (max-width: 1023px) {
        .deka-dash-bar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas: "presets custom" "caption filters";
            row-gap: 0.5rem;
        }
        .deka-dash-bar__presets { grid-area: presets; min-width: 0; justify-self: start; max-width: 100%; overflow-x: auto; scrollbar-width: none; }
        .deka-dash-bar__presets::-webkit-scrollbar { display: none; }
        .deka-dash-bar__anchor--dates { grid-area: custom; }
        .deka-dash-bar__caption { grid-area: caption; align-self: center; }
        .deka-dash-bar__filters { grid-area: filters; margin-inline-start: 0; }
        .deka-dash-bar__popover--dates { inset-inline-start: auto; inset-inline-end: 0; }
    }

    /* Phone: chips scroll in one line, Custom dates is an icon, and both
       panels are bottom sheets with thumb-sized buttons. */
    @media (max-width: 639px) {
        /* No stacking context of its own here, so the sheets and their
           backdrop can sit above the rest of the page. */
        .deka-dash-bar { z-index: auto; }
        .deka-dash-bar__presets { justify-self: stretch; }
        .deka-dash-bar__anchor--dates .deka-dash-bar__btn { width: 2.5rem; padding: 0; justify-content: center; }
        .deka-dash-bar__label {
            position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0;
            overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
        }
        .deka-dash-bar__backdrop { display: block; position: fixed; inset: 0; z-index: 40; background: rgb(9 9 11 / 0.5); }
        .deka-dash-bar__popover,
        .deka-dash-bar__popover--dates,
        .deka-dash-bar__popover--filters {
            position: fixed;
            inset-inline: 0;
            top: auto;
            bottom: 0;
            z-index: 41;
            width: auto;
            max-height: 85vh;
            border-end-start-radius: 0;
            border-end-end-radius: 0;
            border-start-start-radius: 1rem;
            border-start-end-radius: 1rem;
            padding-block-end: env(safe-area-inset-bottom, 0px);
            animation: deka-dash-sheet-in 180ms ease-out;
        }
        .deka-dash-bar__popover--filters .deka-dash-bar__body { max-height: none; flex: 1 1 auto; min-height: 0; }
        .deka-dash-bar__foot { flex-direction: column-reverse; }
        .deka-dash-bar__action { width: 100%; min-height: 3rem; }
        .deka-dash-bar__input { min-height: 3rem; font-size: 1rem; }
    }
    @keyframes deka-dash-sheet-in { from { transform: translateY(100%); } }
    @media (prefers-reduced-motion: reduce) {
        .deka-dash-bar__popover { animation: none !important; }
    }
</style>

<div
    class="deka-dash-bar"
    data-dashboard-filter-bar
    x-data="{
        open: null,
        from: '',
        to: '',
        busy: false,
        toggle(name) {
            if (this.open === name) {
                this.close();

                return;
            }

            if (name === 'dates') {
                this.from = this.$refs.datesTrigger.dataset.start;
                this.to = this.$refs.datesTrigger.dataset.end;
            }

            this.open = name;
            this.$nextTick(() => (name === 'dates' ? this.$refs.datesFrom : this.$refs.filtersPanel)?.focus());
        },
        close(returnFocus = false) {
            const was = this.open;
            this.open = null;

            if (returnFocus && was) {
                this.$refs[was + 'Trigger']?.focus();
            }
        },
        apply() {
            if (this.busy || ! this.from || ! this.to || this.from > this.to) {
                return;
            }

            this.busy = true;
            this.$wire.applyCustomPeriod(this.from, this.to).finally(() => {
                this.busy = false;
                this.close(true);
            });
        },
    }"
    x-on:keydown.escape.window="if (open) { close(true) }"
>
    <div class="deka-dash-bar__presets" role="group" aria-label="{{ __('support::filament/dashboard.period.label') }}">
        @foreach ($presets as $key => $label)
            <button
                type="button"
                class="deka-dash-bar__preset"
                aria-pressed="{{ $active === $key ? 'true' : 'false' }}"
                wire:click="selectPeriod('{{ $key }}')"
                wire:loading.attr="disabled"
            >{{ $label }}</button>
        @endforeach
    </div>

    <div class="deka-dash-bar__anchor deka-dash-bar__anchor--dates" x-on:click.outside="if (open === 'dates') close()">
        <button
            type="button"
            class="deka-dash-bar__btn"
            x-ref="datesTrigger"
            data-start="{{ $start }}"
            data-end="{{ $end }}"
            data-active="{{ $active === null ? 'true' : 'false' }}"
            aria-haspopup="dialog"
            aria-controls="deka-dash-bar-dates"
            x-bind:aria-expanded="open === 'dates' ? 'true' : 'false'"
            title="{{ __('support::filament/dashboard.custom-dates') }}"
            x-on:click="toggle('dates')"
        >
            <x-filament::icon icon="heroicon-m-calendar-days" />
            <span class="deka-dash-bar__label">{{ __('support::filament/dashboard.custom-dates') }}</span>
        </button>

        <div
            id="deka-dash-bar-dates"
            class="deka-dash-bar__popover deka-dash-bar__popover--dates"
            role="dialog"
            aria-labelledby="deka-dash-bar-dates-heading"
            x-cloak
            x-show="open === 'dates'"
            x-transition.opacity.duration.120ms
        >
            <form x-on:submit.prevent="apply()">
                <p class="deka-dash-bar__head" id="deka-dash-bar-dates-heading">{{ __('support::filament/dashboard.custom-dates') }}</p>

                <div class="deka-dash-bar__body deka-dash-bar__dates">
                    <label class="deka-dash-bar__field">
                        {{ __('support::filament/dashboard.from') }}
                        <input type="date" class="deka-dash-bar__input" x-ref="datesFrom" x-model="from" x-bind:max="to" required>
                    </label>
                    <label class="deka-dash-bar__field">
                        {{ __('support::filament/dashboard.to') }}
                        <input type="date" class="deka-dash-bar__input" x-model="to" x-bind:min="from" required>
                    </label>
                </div>

                <div class="deka-dash-bar__foot">
                    <button type="button" class="deka-dash-bar__action" x-on:click="close(true)">{{ __('support::filament/dashboard.cancel') }}</button>
                    <button
                        type="submit"
                        class="deka-dash-bar__action deka-dash-bar__action--primary"
                        x-bind:disabled="busy || ! from || ! to || from > to"
                    >{{ __('support::filament/dashboard.apply') }}</button>
                </div>
            </form>
        </div>
    </div>

    <p class="deka-dash-bar__caption" title="{{ $caption }}">{{ $caption }}</p>

    <div class="deka-dash-bar__anchor deka-dash-bar__filters" x-on:click.outside="if (open === 'filters') close()">
        <button
            type="button"
            class="deka-dash-bar__btn"
            x-ref="filtersTrigger"
            data-active="{{ $inUseCount > 0 ? 'true' : 'false' }}"
            aria-haspopup="dialog"
            aria-controls="deka-dash-bar-filters"
            aria-label="{{ trans_choice('support::filament/dashboard.filters-count', $inUseCount, ['count' => $inUseCount]) }}"
            x-bind:aria-expanded="open === 'filters' ? 'true' : 'false'"
            title="{{ $filtersTitle }}"
            x-on:click="toggle('filters')"
        >
            <x-filament::icon icon="heroicon-m-funnel" />
            <span>{{ __('support::filament/dashboard.filters') }}</span>
            @if ($inUseCount > 0)
                <span class="deka-dash-bar__badge" aria-hidden="true">{{ $inUseCount }}</span>
            @endif
        </button>

        <div
            id="deka-dash-bar-filters"
            class="deka-dash-bar__popover deka-dash-bar__popover--filters"
            role="dialog"
            aria-labelledby="deka-dash-bar-filters-heading"
            tabindex="-1"
            x-ref="filtersPanel"
            x-cloak
            x-show="open === 'filters'"
            x-transition.opacity.duration.120ms
        >
            <p class="deka-dash-bar__head" id="deka-dash-bar-filters-heading">
                {{ __('support::filament/dashboard.filters') }}
                @if ($inUseCount > 0)
                    <span class="deka-dash-bar__badge">{{ $inUseCount }}</span>
                @endif
            </p>

            {{-- Fixed positioning lets a select's options leave the scrolling panel. --}}
            <div class="deka-dash-bar__body fi-fixed-positioning-context">
                {{ $getChildSchema() }}
            </div>

            <div class="deka-dash-bar__foot">
                <button
                    type="button"
                    class="deka-dash-bar__action"
                    wire:click="clearDashboardFilters"
                    wire:loading.attr="disabled"
                    @disabled($inUseCount === 0)
                >{{ __('support::filament/dashboard.clear-all') }}</button>
                <button type="button" class="deka-dash-bar__action deka-dash-bar__action--primary" x-on:click="close(true)">{{ __('support::filament/dashboard.show-results') }}</button>
            </div>
        </div>
    </div>

    <div class="deka-dash-bar__backdrop" x-cloak x-show="open !== null" x-on:click="close(true)" aria-hidden="true"></div>
</div>
