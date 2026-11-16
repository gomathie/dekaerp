<?php

namespace Webkul\Website;

use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;
use Webkul\Website\Filament\Customer\Auth\Login;
use Webkul\Website\Filament\Customer\Auth\PasswordReset\RequestPasswordReset;
use Webkul\Website\Filament\Customer\Auth\PasswordReset\ResetPassword;
use Webkul\Website\Filament\Customer\Clusters\Account;

/**
 * The customer/vendor portal: a partner signs in and sees their own documents.
 *
 * This plugin used to carry a small CMS as well - editable pages with SEO
 * fields, a public homepage, a contact-settings screen and blog widgets. All of
 * that was removed on 2026-10-22 (user decision: an external site at
 * dekaerp.com serves the marketing content, so a second place to author pages
 * earned nothing). What is left is the portal, which is the part only the ERP
 * can provide.
 *
 * Two deliberate consequences of that removal:
 *
 * - **No registration.** Self-registration created a Partner in the production
 *   database for anyone who found the URL. Access is now granted by staff from
 *   the admin Partner page - see the actions in Filament/Admin/Actions/Portal,
 *   wired in by PortalContributions.
 * - **No homepage.** The old one rendered a CMS page, so with the CMS gone it
 *   would have served a blank root URL. Filament now sends an unauthenticated
 *   visitor at `/` to the login screen instead.
 *
 * The header and footer render hooks went with the CMS too: both existed to
 * show page links, contact details and social icons that no longer exist. The
 * panel uses Filament's own chrome.
 *
 * The plugin and its namespace are still called "website" purely to avoid a
 * rename landing in the same change as the removal. Renaming it to "portal" is
 * mechanical and belongs on its own.
 */
class WebsitePlugin implements Plugin
{
    public function getId(): string
    {
        return 'website';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel->when($panel->getId() === 'customer', function (Panel $panel): void {
            $panel
                ->login(Login::class)
                ->passwordReset(RequestPasswordReset::class, ResetPassword::class)
                ->discoverClusters(
                    in: __DIR__.'/Filament/Customer/Clusters',
                    for: 'Webkul\\Website\\Filament\\Customer\\Clusters',
                )
                ->userMenuItems([
                    'my_account' => Action::make('my_account')
                        ->label(fn (): string => __('website::filament/app.navigation.account.label'))
                        ->url(fn (): string => Account::getUrl())
                        ->sort(2)
                        ->visible(fn (): bool => Account::getClusteredComponents() !== []),
                ]);
        });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
