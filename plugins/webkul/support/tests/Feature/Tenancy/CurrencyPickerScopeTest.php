<?php

use Symfony\Component\Finder\Finder;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * Plan #11 MC-1 (2026-11-24): a company sees only its own currencies (CR-6).
 * TRACE365 saw every active currency; the purchase agreement picker still used
 * `active()` alone. Every currency picker on a transaction form applies
 * `permittedFor()`; this scan fails when a new one does not.
 *
 * The allow-list is configuration, not transactions, decided in CR-6: the
 * company's own base currency, the permitted-list picker itself, and account
 * and vendor-price currencies.
 */
it('limits every transaction currency picker to the company list', function () {
    $allowed = [
        'accounts/src/CompanyCurrencyContributions.php',
        'accounts/src/Filament/Resources/AccountResource/Schemas/AccountForm.php',
        'purchases/src/Filament/Admin/Clusters/Configurations/Resources/VendorPriceResource/Schemas/VendorPriceForm.php',
        // The installation's default currency (Settings › Currency), a platform setting.
        'security/src/Filament/Clusters/Settings/Pages/ManageCurrency.php',
        'support/src/Filament/Resources/CompanyResource/RelationManagers/BranchesRelationManager.php',
        'support/src/Filament/Resources/CompanyResource/Schemas/CompanyForm.php',
    ];

    $root = base_path('plugins/webkul');
    $pickers = [];
    $unscoped = [];

    foreach (Finder::create()->files()->name('*.php')->in($root.'/*/src')->contains("/Select::make\('\w*currency_id'\)/") as $file) {
        $relative = str_replace('\\', '/', substr($file->getRealPath(), strlen(realpath($root)) + 1));
        $lines = explode("\n", $file->getContents());

        foreach ($lines as $number => $line) {
            if (! preg_match("/Select::make\('\w*currency_id'\)/", $line)) {
                continue;
            }

            $pickers[] = $relative;
            $window = implode("\n", array_slice($lines, $number, 16));

            if (! str_contains($window, 'permittedFor') && ! in_array($relative, $allowed, true)) {
                $unscoped[] = $relative.':'.($number + 1);
            }
        }
    }

    // A scan that found nothing would pass vacuously.
    expect(count($pickers))->toBeGreaterThan(10)
        ->and($unscoped)->toBe([]);
});
