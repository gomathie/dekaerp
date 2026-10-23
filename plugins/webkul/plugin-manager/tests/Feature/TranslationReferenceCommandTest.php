<?php

use Illuminate\Support\Facades\File;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

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

    // One expectation per output line. A reference line reads
    // "- path:3 ns::messages.missing", and PendingCommand matches each
    // doWrite call against the first expectation whose substring fits, so two
    // expectations aimed at the same line can never both be satisfied.
    $this->artisan('translations:check', [
        '--plugin'     => TRANSLATION_REFERENCE_FIXTURE,
        '--references' => true,
        '--details'    => true,
    ])
        ->expectsOutputToContain('src/Reference.php:3')
        ->expectsOutputToContain('canonical key does not exist: missing')
        ->expectsOutputToContain('src/Reference.php:4')
        ->expectsOutputToContain('canonical file does not exist: absent.php')
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

/**
 * Root-scope references resolve against lang/en. They are checked through the
 * command itself rather than through `artisan translations:check --references`,
 * because that mode scans every plugin in the repository and takes minutes.
 */
function validateRootReference(string $reference): ?string
{
    $command = app(Webkul\PluginManager\Console\Commands\FindMissingTranslations::class);

    $catalogs = (new ReflectionMethod($command, 'getTranslationCatalogs'))->invoke($command);

    return (new ReflectionMethod($command, 'validateTranslationReference'))->invoke($command, $reference, $catalogs);
}

it('accepts a root reference whose key exists in lang/en', function () {
    expect(validateRootReference('welcome.title'))->toBeNull()
        ->and(validateRootReference('welcome.nav.dashboard'))->toBeNull();
});

it('reports a root reference whose key is missing from lang/en', function () {
    expect(validateRootReference('welcome.not_a_real_key'))
        ->toBe('canonical key does not exist: not_a_real_key');
});

it('does not report root references that Laravel itself translates', function () {
    // validation.php, auth.php and pagination.php live inside the framework, so
    // these resolve without a file of ours and must not be reported as missing.
    expect(validateRootReference('validation.required'))->toBeNull()
        ->and(validateRootReference('auth.failed'))->toBeNull();
});
