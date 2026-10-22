<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

it('zz probe: dumps translation reference output', function () {
    $pluginPath = base_path('plugins/webkul/zz-uf008-probe2');

    File::ensureDirectoryExists($pluginPath.'/src');
    File::ensureDirectoryExists($pluginPath.'/resources/lang/en');

    File::put($pluginPath.'/src/Reference.php', <<<'PHP'
<?php

__('zz-uf008-probe2::messages.missing');
__('zz-uf008-probe2::absent.value');
PHP);

    File::put($pluginPath.'/resources/lang/en/messages.php', <<<'PHP'
<?php

return [
    'present' => 'Present',
];
PHP);

    $exit = Artisan::call('translations:check', [
        '--plugin'     => 'zz-uf008-probe2',
        '--references' => true,
        '--details'    => true,
    ]);

    fwrite(STDERR, "\n===EXIT:{$exit}===\n".Artisan::output()."\n===END===\n");

    File::deleteDirectory($pluginPath);

    expect(true)->toBeTrue();
});
