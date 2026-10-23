<?php

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Partner\Filament\Resources\PartnerResource\Exports\ContactTemplateExport;
use Webkul\Partner\Models\Partner;
use Webkul\Partner\Services\ContactImporter;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

/**
 * Write a spreadsheet to a temporary file and return its path.
 *
 * @param  array<int, array<int, string>>  $rows
 */
function contactImportFile(array $rows, ?array $headings = null): string
{
    $headings ??= array_keys(ContactImporter::columns());

    // No extension needed: every caller passes the reader type explicitly,
    // and appending one would leave the file tempnam() created behind.
    $path = tempnam(sys_get_temp_dir(), 'contact-import-test-');

    $handle = fopen($path, 'w');

    foreach (array_merge([$headings], $rows) as $row) {
        fputcsv($handle, $row);
    }

    fclose($handle);

    return $path;
}

/**
 * A row keyed by column name, padded out to the full column list.
 *
 * @param  array<string, string>  $values
 * @return array<int, string>
 */
function contactImportRow(array $values): array
{
    return array_map(
        fn (string $column): string => $values[$column] ?? '',
        array_keys(ContactImporter::columns()),
    );
}

/**
 * The country the example template refers to.
 *
 * firstOrCreate, not create: this suite's database is seeded with the world's
 * countries, so Ghana already exists. Creating a second one made the importer
 * resolve the country to the seeded row while the state hung off the duplicate,
 * and the template then failed to import for a reason that had nothing to do
 * with the importer.
 */
function contactImportGhana(): Country
{
    $country = Country::query()->firstOrCreate(
        ['code' => 'GH'],
        ['name' => 'Ghana'],
    );

    State::query()->firstOrCreate(
        ['country_id' => $country->id, 'name' => 'Greater Accra'],
        ['code' => 'GA'],
    );

    return $country;
}

/**
 * The template is the instructions for this feature, so it is tested as the
 * real input rather than a hand-written file: download it, upload it back
 * unchanged, and both example rows must import. This is what catches a column
 * added to the importer but not the template, a heading the parser cannot
 * match, and example data that does not satisfy the importer's own rules.
 */
it('imports the downloadable example file unchanged', function () {
    contactImportGhana();

    Excel::store(new ContactTemplateExport, 'contact-template-test.xlsx', 'local', ExcelFormat::XLSX);

    $path = Storage::disk('local')->path('contact-template-test.xlsx');

    $result = app(ContactImporter::class)->import($path, ExcelFormat::XLSX);

    expect($result['errors'])->toBe([])
        ->and($result['created'])->toBe(2)
        ->and($result['skipped'])->toBe(0);

    $company = Partner::query()->where('name', 'Acme Industries Ltd')->first();
    $person = Partner::query()->where('name', 'Ama Mensah')->first();

    expect($company)->not->toBeNull()
        ->and($company->account_type->value)->toBe('company')
        ->and($person)->not->toBeNull()
        ->and($person->job_title)->toBe('Procurement Officer');

    // The second example row names the first as its company, so a parent is
    // resolved against a contact created earlier in the same file.
    expect($person->parent_id)->toBe($company->id);

    Storage::disk('local')->delete('contact-template-test.xlsx');
});

it('keeps the template headings and the importer columns in step', function () {
    expect((new ContactTemplateExport)->headings())
        ->toBe(array_keys(ContactImporter::columns()));
});

it('imports a csv as well as an excel file', function () {
    $path = contactImportFile([
        contactImportRow(['name' => 'Kofi Boateng', 'email' => 'kofi@example.test']),
    ]);

    $result = app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect($result['created'])->toBe(1)
        ->and($result['errors'])->toBe([]);

    unlink($path);
});

it('reports a bad row by its spreadsheet row number and imports the rest', function () {
    $path = contactImportFile([
        contactImportRow(['name' => 'Good One', 'email' => 'good@example.test']),
        contactImportRow(['name' => 'Bad Email', 'email' => 'not-an-email']),
        contactImportRow(['name' => 'Another Good']),
    ]);

    $result = app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect($result['created'])->toBe(2)
        ->and($result['skipped'])->toBe(1)
        ->and($result['errors'])->toHaveCount(1)
        // Row 3, not row 2: the heading row counts, which is what makes the
        // number usable in the spreadsheet the person is looking at.
        ->and($result['errors'][0])->toContain('3');

    expect(Partner::query()->where('name', 'Bad Email')->exists())->toBeFalse();

    unlink($path);
});

it('skips a row whose email already belongs to a contact', function () {
    Partner::factory()->create(['email' => 'taken@example.test']);

    $path = contactImportFile([
        contactImportRow(['name' => 'Duplicate', 'email' => 'taken@example.test']),
    ]);

    $result = app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect($result['created'])->toBe(0)
        ->and($result['skipped'])->toBe(1)
        ->and($result['errors'][0])->toContain('taken@example.test');

    unlink($path);
});

/**
 * A value beginning with `=` is refused rather than stored. It is not a
 * server-side execution risk - the reader never calculates formulas - but a
 * stored `=...` becomes one for whoever later exports the contacts to CSV and
 * opens the file in Excel.
 */
it('refuses a value that a spreadsheet would read as a formula', function () {
    $path = contactImportFile([
        contactImportRow(['name' => '=cmd|\'/C calc\'!A0']),
        contactImportRow(['name' => 'Ordinary Contact']),
    ]);

    $result = app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect($result['created'])->toBe(1)
        ->and($result['skipped'])->toBe(1)
        ->and(Partner::query()->where('name', 'like', '=%')->exists())->toBeFalse();

    unlink($path);
});

/**
 * The formula guard must not fire on `+`, or it would reject every
 * international phone number - including the ones in this importer's own
 * example file.
 */
it('accepts a phone number that starts with a plus', function () {
    $path = contactImportFile([
        contactImportRow(['name' => 'Phone Person', 'phone' => '+233 30 123 4567', 'mobile' => '+233 24 765 4321']),
    ]);

    $result = app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect($result['errors'])->toBe([])
        ->and($result['created'])->toBe(1)
        ->and(Partner::query()->where('name', 'Phone Person')->value('phone'))->toBe('+233 30 123 4567');

    unlink($path);
});

it('resolves a country by name or by code, and reports one it cannot match', function () {
    $country = contactImportGhana();

    $path = contactImportFile([
        contactImportRow(['name' => 'By Name', 'country' => 'Ghana']),
        contactImportRow(['name' => 'By Code', 'country' => 'gh']),
        contactImportRow(['name' => 'By Nonsense', 'country' => 'Atlantis']),
    ]);

    $result = app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect($result['created'])->toBe(2)
        ->and($result['skipped'])->toBe(1)
        ->and($result['errors'][0])->toContain('Atlantis');

    expect(Partner::query()->where('name', 'By Name')->value('country_id'))->toBe($country->id)
        ->and(Partner::query()->where('name', 'By Code')->value('country_id'))->toBe($country->id);

    unlink($path);
});

it('matches headings whatever their case or spacing', function () {
    $path = contactImportFile(
        [['Spaced Heading Person', 'individual', 'spaced@example.test', 'Sales Lead']],
        ['Name', 'Account Type', 'EMAIL', 'Job Title'],
    );

    $result = app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect($result['created'])->toBe(1)
        ->and(Partner::query()->where('name', 'Spaced Heading Person')->value('job_title'))->toBe('Sales Lead');

    unlink($path);
});

it('refuses a file with no column it recognises', function () {
    $path = contactImportFile([['x', 'y']], ['nothing', 'useful']);

    $result = app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect($result['created'])->toBe(0)
        ->and($result['errors'])->toHaveCount(1);

    unlink($path);
});

/**
 * Imported contacts must be filed exactly as typed ones are. Partner opts out
 * of automatic company assignment, so a contact created through the form has a
 * null company_id; an import that stamped the active company instead would
 * make imported contacts invisible to the other companies that can see the
 * typed ones. CompanyIsolationTest pins the form side of this.
 */
it('leaves company_id null so imported contacts match typed ones', function () {
    $company = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($company);

    $path = contactImportFile([
        contactImportRow(['name' => 'Shared Contact']),
    ]);

    app(ContactImporter::class)->import($path, ContactImporter::readerTypeFor('csv'));

    expect(Partner::query()->where('name', 'Shared Contact')->value('company_id'))->toBeNull();

    unlink($path);
});
