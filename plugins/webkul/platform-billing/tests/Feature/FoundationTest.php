<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Webkul\PlatformBilling\Models\DocumentNumber;
use Webkul\PlatformBilling\PlatformBillingServiceProvider;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';

/**
 * SAAS-08: the foundation is registered, and it is platform data.
 *
 * Two invariants only a test can hold: every migration file in this plugin is
 * listed in the provider (the project's own rule), and no tenant actor can reach
 * the table through Gate.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

it('registers every migration file it ships', function () {
    $files = collect(glob(base_path('plugins/webkul/platform-billing/database/migrations/*.php')))
        ->map(fn (string $path): string => basename($path, '.php'))
        ->sort()
        ->values()
        ->all();

    $registered = (new ReflectionClass(PlatformBillingServiceProvider::class))
        ->getStaticPropertyValue('name');

    expect($files)->not->toBeEmpty()
        ->and($registered)->toBe('platform-billing');

    // Read the provider source the same way ResourceStructureSnapshotTest-style
    // guards do: the list is fluent, not a static property, so the file is the
    // source of truth.
    $source = file_get_contents((new ReflectionClass(PlatformBillingServiceProvider::class))->getFileName());

    foreach ($files as $file) {
        expect($source)->toContain($file);
    }
});

it('creates the numbering table', function () {
    expect(Schema::hasTable('platform_billing_document_numbers'))->toBeTrue()
        ->and(Schema::hasColumn('platform_billing_document_numbers', 'document_type'))->toBeTrue()
        ->and(Schema::hasColumn('platform_billing_document_numbers', 'last_number'))->toBeTrue();

    // Platform data carries no tenant column: it belongs to the installation.
    expect(Schema::hasColumn('platform_billing_document_numbers', 'company_id'))->toBeFalse();
});

it('denies every tenant ability on platform documents', function () {
    $user = SecurityHelper::authenticateWithPermissions([
        'view_any_platform_billing_document_number',
        'update_platform_billing_document_number',
    ]);

    $record = DocumentNumber::query()->create([
        'document_type' => 'invoice',
        'year'          => 2026,
        'last_number'   => 1,
    ]);

    // The permissions exist so the refusal is attributable to the policy rather
    // than to a missing permission.
    expect($user->can('view_any_platform_billing_document_number'))->toBeTrue();

    expect(Gate::forUser($user)->allows('viewAny', DocumentNumber::class))->toBeFalse()
        ->and(Gate::forUser($user)->allows('update', $record))->toBeFalse();
});
