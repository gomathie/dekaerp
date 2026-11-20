<x-filament-panels::page>
    {{-- Sanctum stores only a hash, so this is the one and only render in
         which the plaintext token exists. It is intentionally noisy. --}}
    @if ($plainTextToken)
        <div class="rounded-xl border border-warning-300 bg-warning-50 p-4 dark:border-warning-700 dark:bg-warning-500/10">
            <div class="flex items-start gap-3">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="size-5 shrink-0 text-warning-600 dark:text-warning-400" />

                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold text-warning-800 dark:text-warning-200">
                        {{ __('security::filament/pages/manage-api-tokens.plaintext.title') }}
                    </div>

                    <div class="mt-1 text-sm text-warning-700 dark:text-warning-300">
                        {{ __('security::filament/pages/manage-api-tokens.plaintext.description') }}
                    </div>

                    <code class="mt-3 block overflow-x-auto rounded-lg bg-white px-3 py-2 font-mono text-xs text-gray-900 dark:bg-gray-900 dark:text-gray-100">{{ $plainTextToken }}</code>
                </div>
            </div>
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
