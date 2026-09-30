<?php

use Illuminate\Auth\Access\AuthorizationException;
use Webkul\Logistics\Support\CompanyStorage;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('runs storage work as the owning company and restores the prior context', function () {
    $current = LogisticsHelper::enable(LogisticsHelper::company());
    $owner = LogisticsHelper::enable(LogisticsHelper::company());

    FilamentHelper::actingAsCompanyUser([$current, $owner]);

    $during = CompanyStorage::run($owner->id, fn (): ?int => current_company_id());

    expect($during)->toBe($owner->id)
        ->and(current_company_id())->toBe($current->id);
});

it('refuses to switch storage into a company the user cannot access', function () {
    $current = LogisticsHelper::enable(LogisticsHelper::company());
    $other = LogisticsHelper::enable(LogisticsHelper::company());

    FilamentHelper::actingAsCompanyUser($current);

    expect(fn () => CompanyStorage::run($other->id, fn (): null => null))
        ->toThrow(AuthorizationException::class);
});

it('builds authenticated object URLs under the owning company prefix', function () {
    config()->set('filesystems.disks.public.driver', 'tenant-s3');
    config()->set('filesystems.disks.public.root', 'tenant-root');

    $key = CompanyStorage::objectKey(42, 'logistics/expenses/receipt.pdf');

    expect($key)->toBe('tenant-root/companies/42/logistics/expenses/receipt.pdf')
        ->and(CompanyStorage::url(42, 'logistics/expenses/receipt.pdf'))
        ->toBe(route('secure-storage', ['path' => $key]));
});
