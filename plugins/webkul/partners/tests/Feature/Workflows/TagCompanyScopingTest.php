<?php

use Webkul\Partner\Models\Tag;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('stamps a new contact tag with the active company and hides it from another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $tag = Tag::factory()->create();

    expect($tag->company_id)->toBe($companyA->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Tag::query()->pluck('id'))->not->toContain($tag->id);
});

it('lets two companies use the same contact tag name', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $tagOfA = Tag::factory()->create(['name' => 'Key account']);

    CompanyHelper::actingAsCompanyUser($companyB);

    $tagOfB = Tag::factory()->create(['name' => 'Key account']);

    expect($tagOfB->id)->not->toBe($tagOfA->id)
        ->and($tagOfB->company_id)->toBe($companyB->id);
});
