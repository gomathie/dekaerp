<?php

/*
 * Localhost-only gatekeeper for Adminer. The real Adminer source lives
 * outside public/ (storage/app/adminer/adminer-source.php) specifically so
 * requesting it directly can't bypass this check.
 *
 * Adminer authenticates against the database directly, not through the
 * app, so this file is never safe to expose beyond the machine running it.
 */

$clientIp = $_SERVER['REMOTE_ADDR'] ?? '';

$allowedIps = ['127.0.0.1', '::1'];

// Docker Desktop's gateway addresses for the machine running this container.
// Inbound host->container requests (via published ports) and outbound
// container->host DNS resolution (host.docker.internal) can report
// *different* addresses in the same internal range, so both are listed
// rather than resolved dynamically from just one of them.
$allowedIps[] = '192.168.65.1';
$allowedIps[] = '192.168.65.254';

// Escape hatch for setups where none of the above matches (e.g. a different
// Docker network layout): set ADMINER_ALLOWED_IPS to a comma-separated list.
if ($extra = getenv('ADMINER_ALLOWED_IPS')) {
    $allowedIps = [...$allowedIps, ...array_map('trim', explode(',', $extra))];
}

if (! in_array($clientIp, $allowedIps, true)) {
    http_response_code(404);
    exit;
}

require __DIR__.'/../storage/app/adminer/adminer-source.php';
