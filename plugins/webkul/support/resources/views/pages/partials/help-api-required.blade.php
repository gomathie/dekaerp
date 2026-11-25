@if ($required)
    <span class="font-semibold text-danger-600 dark:text-danger-400">{{ __('support::filament/pages/help-developers.endpoint.required') }}</span>
@else
    <span class="text-gray-400">{{ __('support::filament/pages/help-developers.endpoint.optional') }}</span>
@endif
