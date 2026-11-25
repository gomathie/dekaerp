{{--
    The full API reference (Scalar), sign-in only. Served by ApiDocsController.

    Replaces resources/views/scribe/index.blade.php, which `scribe:generate`
    rewrites on every run and which loaded the unpinned latest Scalar build from
    the CDN: any new release, or a compromised one, ran on this page with the
    signed-in user's session. The version is pinned and checked by its
    Subresource Integrity hash; to upgrade, change both together (the hash of
    https://cdn.jsdelivr.net/npm/@scalar/api-reference@<version>/dist/browser/standalone.js).

    telemetry off and no proxyUrl: Scalar's "Test request" then calls the API
    directly from the browser, so a token typed into it never passes through a
    third party.
--}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex, nofollow" />
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
        }
    </style>
</head>
<body>
    <script
        id="api-reference"
        data-url="{{ $specUrl }}"
        data-configuration="{{ json_encode([
            'theme'              => 'kepler',
            'defaultHttpClient'  => ['targetKey' => 'js', 'clientKey' => 'fetch'],
            'hideModels'         => false,
            'hideDownloadButton' => false,
            'defaultOpenAllTags' => false,
            'telemetry'          => false,
            'withDefaultFonts'   => false,
        ], JSON_UNESCAPED_SLASHES) }}"
    ></script>

    <script
        src="https://cdn.jsdelivr.net/npm/@scalar/api-reference@1.76.4/dist/browser/standalone.js"
        integrity="sha384-hwB9WroDTpZaI38gr+u9CXA6ksz5gUYgD2GxXazKl/agT1L2GWUiRFWGJYNiFu4Q"
        crossorigin="anonymous"
    ></script>
</body>
</html>
