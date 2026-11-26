<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * Every Livewire upload passes the global temporary-upload rules
 * (config/livewire.php) before a field's own rules run. The Zoho Books import
 * page takes a JSON export; with JSON missing from those rules, every upload
 * there failed with a 422 in production (2026-11-26) while the page's own tests,
 * which skip the temporary-upload endpoint, passed.
 */
function passesLivewireUploadRules(UploadedFile $file): bool
{
    return Validator::make(['files' => [$file]], ['files.*' => FileUploadConfiguration::rules()])->passes();
}

it('accepts a json export at the temporary upload step', function () {
    $file = UploadedFile::fake()->createWithContent('zoho-export.json', json_encode(['invoices' => [['invoice_number' => 'INV-1', 'total' => 100]]], JSON_PRETTY_PRINT));

    expect(passesLivewireUploadRules($file))->toBeTrue();
});

it('still refuses a php file at the temporary upload step', function () {
    $file = UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;');

    expect(passesLivewireUploadRules($file))->toBeFalse();
});
