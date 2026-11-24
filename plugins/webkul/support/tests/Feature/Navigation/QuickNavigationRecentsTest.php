<?php

use Webkul\Support\Services\QuickNavigationRecents;

require_once __DIR__.'/../../Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * U54, 2026-11-21: recent items were shared across companies, so after a
 * company switch they linked to records that answer 404. Each company keeps
 * its own recents.
 */
it('keeps recent items per company', function (): void {
    $first = CompanyHelper::company();
    $second = CompanyHelper::company();

    FilamentHelper::actingAsCompanyUser([$first, $second]);

    CompanyHelper::setActive([$first->id]);
    QuickNavigationRecents::push('/admin/sale/orders/quotations/1', 'Quotation 1');

    expect(array_column(QuickNavigationRecents::get(), 'url'))->toBe(['/admin/sale/orders/quotations/1']);

    CompanyHelper::setActive([$second->id]);

    expect(QuickNavigationRecents::get())->toBe([]);

    CompanyHelper::setActive([$first->id]);

    expect(array_column(QuickNavigationRecents::get(), 'url'))->toBe(['/admin/sale/orders/quotations/1']);
});
