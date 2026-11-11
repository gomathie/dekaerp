<?php

$roots = $argv[1] ?? 'plugins/webkul/attendances';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($roots));
$bad = 0;

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $output = [];
    $code = 0;
    exec('php -l '.escapeshellarg($file->getPathname()).' 2>&1', $output, $code);

    if ($code !== 0) {
        $bad++;
        echo implode(PHP_EOL, $output).PHP_EOL;
    }
}

echo 'BAD='.$bad.PHP_EOL;
