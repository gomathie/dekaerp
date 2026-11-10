{{-- Shown on every admin page while an impersonation is active (TenantImpersonation). Inline styles, like the company switcher, so it needs no CSS rebuild. --}}
<div
    role="status"
    data-impersonation-banner
    style="position:sticky;top:env(safe-area-inset-top, 0px);z-index:40;display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:0.5rem 1rem;padding:0.5rem 1rem;background:rgb(180 83 9);color:#fff;font-size:0.875rem;line-height:1.25rem;"
>
    <span style="display:inline-flex;align-items:center;gap:0.375rem;">
        <x-filament::icon icon="heroicon-o-eye" style="width:1.25rem;height:1.25rem;flex-shrink:0;" />

        <span>
            {{ __('security::impersonation.banner.message', [
                'target' => $target?->name ?? '?',
                'company' => $company?->name ?? '?',
                'impersonator' => $impersonator?->name ?? '?',
            ]) }}
        </span>
    </span>

    <span>{{ __('security::impersonation.banner.ends-at', ['time' => $expiresAt->format('H:i').' UTC']) }}</span>

    <form method="POST" action="{{ route('security.impersonation.end') }}" style="margin:0;">
        @csrf

        <button
            type="submit"
            style="padding:0.25rem 0.75rem;border-radius:0.375rem;border:1px solid rgba(255,255,255,0.7);background:transparent;color:#fff;font-weight:600;cursor:pointer;"
        >
            {{ __('security::impersonation.banner.end') }}
        </button>
    </form>
</div>
