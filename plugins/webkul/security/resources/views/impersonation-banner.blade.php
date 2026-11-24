{{-- Shown on every admin page while an impersonation is active (TenantImpersonation). Inline styles, like the company switcher, so it needs no CSS rebuild. --}}
<div
    role="status"
    id="impersonation-banner"
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

{{--
    The top bar and the module sidebar stick below the banner (TRACE365,
    2026-11-23). Both were sticky or fixed at the very top too, so on a phone
    the banner, a higher layer, covered the Sales tabs once the page scrolled.
    The banner's real height is measured, because its text wraps to two or
    three lines on a phone and with long names. Rendered only with the banner,
    so ending the impersonation also removes the reserved space.
--}}
<style>
    .fi-topbar-ctn {
        top: calc(env(safe-area-inset-top, 0px) + var(--impersonation-banner-height, 0px));
    }

    .module-sidebar {
        top: calc(var(--module-topbar-height) + var(--impersonation-banner-height, 0px));
        height: calc(100vh - var(--module-topbar-height) - var(--impersonation-banner-height, 0px));
    }
</style>

<script>
    (function () {
        // By id: TenantImpersonationTest counts the banner's data attribute
        // to prove it renders once, so this script must not repeat it.
        var banner = document.getElementById('impersonation-banner');

        if (! banner) {
            return;
        }

        var measure = function () {
            document.documentElement.style.setProperty('--impersonation-banner-height', banner.offsetHeight + 'px');
        };

        measure();

        if (window.ResizeObserver) {
            new ResizeObserver(measure).observe(banner);
        }
    })();
</script>
