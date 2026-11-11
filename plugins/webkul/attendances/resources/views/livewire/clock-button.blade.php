<div>
    @if ($state['visible'])
        <style>
            .deka-attendance {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
            }
            .deka-attendance__pill {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                padding: 0.25rem 0.625rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
                background: rgb(0 0 0 / 0.06);
                color: #3f3f46;
                white-space: nowrap;
            }
            .dark .deka-attendance__pill {
                background: rgb(255 255 255 / 0.10);
                color: #e4e4e7;
            }
            .deka-attendance__pill--on {
                background: rgb(16 185 129 / 0.16);
                color: #047857;
            }
            .dark .deka-attendance__pill--on { color: #6ee7b7; }
            .deka-attendance__pill--leave {
                background: rgb(59 130 246 / 0.16);
                color: #1d4ed8;
            }
            .dark .deka-attendance__pill--leave { color: #93c5fd; }
            .deka-attendance__dot {
                width: 0.5rem;
                height: 0.5rem;
                border-radius: 9999px;
                background: currentColor;
            }
            .deka-attendance__button {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                min-height: 2.5rem;
                padding: 0.5rem 1rem;
                border: 0;
                border-radius: 0.6rem;
                font-size: 0.8125rem;
                font-weight: 600;
                color: #ffffff;
                background: #f59e0b;
                cursor: pointer;
            }
            .deka-attendance__button:hover { background: #d97706; }
            .deka-attendance__button:disabled { opacity: 0.6; cursor: progress; }
            .deka-attendance__button--out { background: #3f3f46; }
            .deka-attendance__button--out:hover { background: #27272a; }
            @media (max-width: 480px) {
                .deka-attendance { width: 100%; }
                .deka-attendance__button { flex: 1 1 auto; justify-content: center; }
            }
        </style>

        <div class="deka-attendance">
            <span class="deka-attendance__pill {{ $state['open'] ? 'deka-attendance__pill--on' : ($state['onLeave'] ? 'deka-attendance__pill--leave' : '') }}">
                <span class="deka-attendance__dot" aria-hidden="true"></span>

                @if ($state['open'])
                    {{ __('attendances::attendance.statuses.clocked-in', ['time' => $state['since']]) }}
                @elseif ($state['onLeave'])
                    {{ __('attendances::attendance.statuses.on-leave') }}
                @else
                    {{ __('attendances::attendance.statuses.not-clocked-in') }}
                @endif
            </span>

            @if ($state['open'])
                <button
                    type="button"
                    class="deka-attendance__button deka-attendance__button--out"
                    wire:click="clockOut"
                    wire:loading.attr="disabled"
                    wire:target="clockOut"
                >
                    {{ __('attendances::attendance.actions.clock-out') }}
                </button>
            @else
                <button
                    type="button"
                    class="deka-attendance__button"
                    wire:click="clockIn"
                    wire:loading.attr="disabled"
                    wire:target="clockIn"
                >
                    {{ __('attendances::attendance.actions.clock-in') }}
                </button>
            @endif
        </div>
    @endif
</div>
