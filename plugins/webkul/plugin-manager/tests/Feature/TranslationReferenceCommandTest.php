<?php

use Illuminate\Support\Facades\File;

const TRANSLATION_REFERENCE_FIXTURE = 'translation-reference-fixture';

function translationReferenceFixture(string $source): void
{
    $pluginPath = base_path('plugins/webkul/'.TRANSLATION_REFERENCE_FIXTURE);

    File::ensureDirectoryExists($pluginPath.'/src');
    File::ensureDirectoryExists($pluginPath.'/resources/lang/en');

    File::put($pluginPath.'/src/Reference.php', $source);
    File::put($pluginPath.'/resources/lang/en/messages.php', <<<'PHP'
<?php

return [
    'present' => 'Present',
];
PHP);
}

afterEach(function (): void {
    File::deleteDirectory(base_path('plugins/webkul/'.TRANSLATION_REFERENCE_FIXTURE));
});

it('accepts literal references that exist in the canonical locale', function () {
    translationReferenceFixture(<<<'PHP'
<?php

__('translation-reference-fixture::messages.present');
PHP);

    $this->artisan('translations:check', [
        '--plugin'     => TRANSLATION_REFERENCE_FIXTURE,
        '--references' => true,
    ])->assertSuccessful();
});

it('reports literal references missing from the canonical locale with their source', function () {
    translationReferenceFixture(<<<'PHP'
<?php

__('translation-reference-fixture::messages.missing');
__('translation-reference-fixture::absent.value');
PHP);

    $this->artisan('translations:check', [
        '--plugin'     => TRANSLATION_REFERENCE_FIXTURE,
        '--references' => true,
        '--details'    => true,
    ])
        ->expectsOutputToContain('src/Reference.php:3')
        ->expectsOutputToContain('messages.missing')
        ->expectsOutputToContain('absent.value')
        ->assertFailed();
});

it('ignores dynamic references that cannot be verified statically', function () {
    translationReferenceFixture(<<<'PHP'
<?php

$key = 'present';
__('translation-reference-fixture::messages.'.$key);
PHP);

    $this->artisan('translations:check', [
        '--plugin'     => TRANSLATION_REFERENCE_FIXTURE,
        '--references' => true,
    ])->assertSuccessful();
});
