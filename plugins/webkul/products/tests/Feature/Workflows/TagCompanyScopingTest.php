<?php

use Illuminate\Support\Facades\Validator;
use Webkul\Product\Http\Requests\TagRequest;
use Webkul\Product\Models\Tag;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('stamps a new product tag with the active company and hides it from another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $tag = Tag::factory()->create();

    expect($tag->company_id)->toBe($companyA->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Tag::query()->pluck('id'))->not->toContain($tag->id);
});

it('lets two companies use the same tag name', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $tagOfA = Tag::factory()->create(['name' => 'VIP']);

    CompanyHelper::actingAsCompanyUser($companyB);

    $tagOfB = Tag::factory()->create(['name' => 'VIP']);

    expect($tagOfB->id)->not->toBe($tagOfA->id)
        ->and($tagOfB->company_id)->toBe($companyB->id);
});

it('rejects a duplicate product tag name only inside the active company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    Tag::factory()->create(['name' => 'VIP']);

    $sameCompany = Validator::make(
        ['name' => 'VIP'],
        (new TagRequest)->rules(),
    );

    CompanyHelper::actingAsCompanyUser($companyB);

    $otherCompany = Validator::make(
        ['name' => 'VIP'],
        (new TagRequest)->rules(),
    );

    expect($sameCompany->fails())->toBeTrue()
        ->and($otherCompany->fails())->toBeFalse();
});
