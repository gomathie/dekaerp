<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Webkul\Security\Filament\Resources\CompanyResource;

require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

$namespace = 'Webkul\\Support\\';

beforeEach(function () {
    SecurityHelper::disableUserEvents();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

/**
 * Support's own resources with global search details, plus the company
 * resource the panel registers in Support's place. Since 2026-11-11 Support's
 * CompanyResource is not registered, and Security's subclass, which declares
 * its own details, serves the company pages. Without this, the list is empty
 * and the checks below would prove nothing.
 *
 * @return list<class-string>
 */
function supportGlobalSearchResources(string $namespace): array
{
    $resources = FilamentHelper::resourcesWithOwnGlobalSearchDetails($namespace);

    if (in_array(CompanyResource::class, Filament\Facades\Filament::getPanel('admin')->getResources(), true)) {
        $resources[] = CompanyResource::class;
    }

    return $resources;
}

it('registers resources that build their own global search details', function () use ($namespace) {
    expect(supportGlobalSearchResources($namespace))->not->toBeEmpty();
});

it('builds global search details for a record whose optional relations are empty', function () use ($namespace) {
    $failures = [];

    foreach (supportGlobalSearchResources($namespace) as $resource) {
        $model = $resource::getModel();

        if (! is_subclass_of($model, Model::class)) {
            continue;
        }

        try {
            $resource::getGlobalSearchResultDetails(new $model);
        } catch (Throwable $exception) {
            $failures[] = $resource.' => '.$exception->getMessage();
        }
    }

    expect($failures)->toBe([]);
});

it('builds a global search title for a record whose optional relations are empty', function () use ($namespace) {
    $failures = [];

    foreach (supportGlobalSearchResources($namespace) as $resource) {
        $model = $resource::getModel();

        if (! is_subclass_of($model, Model::class)) {
            continue;
        }

        try {
            $resource::getGlobalSearchResultTitle(new $model);
        } catch (Throwable $exception) {
            $failures[] = $resource.' => '.$exception->getMessage();
        }
    }

    expect($failures)->toBe([]);
});
