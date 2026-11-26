<?php

namespace Webkul\Support\Services;

/**
 * Request samples for one endpoint of the in-app API reference.
 *
 * Built when the page renders rather than cached with the endpoint, because
 * they carry the viewer's own company id in X-Company-Id: copied as-is, a
 * sample then addresses the company the administrator is working in.
 *
 * The output is shown escaped. Values come from Scribe's examples and are
 * encoded for each language (JSON, a Python literal, a PHP array), never
 * pasted in raw, so a quote in an example cannot break out of a sample.
 */
class ApiCodeSamples
{
    public const TOKEN = 'YOUR_API_TOKEN';

    /**
     * @param  array<string, mixed>  $endpoint  one endpoint from ApiReference
     * @return array{curl: string, javascript: string, python: string, php: string}
     */
    public static function for(array $endpoint, string $baseUrl, ?int $companyId): array
    {
        $method = (string) $endpoint['method'];
        $url = rtrim($baseUrl, '/').$endpoint['example_path'];

        if (! empty($endpoint['query'])) {
            $url .= '?'.http_build_query($endpoint['query']);
        }

        $body = $endpoint['body'] ?? null;
        $hasBody = is_array($body) && $body !== [];

        $headers = [];

        if ($endpoint['authenticated'] ?? true) {
            $headers['Authorization'] = 'Bearer '.self::TOKEN;
        }

        $headers['Accept'] = 'application/json';

        if ($hasBody) {
            $headers['Content-Type'] = 'application/json';
        }

        if (($endpoint['authenticated'] ?? true) && $companyId) {
            $headers['X-Company-Id'] = (string) $companyId;
        }

        return [
            'curl'       => static::curl($method, $url, $headers, $hasBody ? $body : null),
            'javascript' => static::javascript($method, $url, $headers, $hasBody ? $body : null),
            'python'     => static::python($method, $url, $headers, $hasBody ? $body : null),
            'php'        => static::php($method, $url, $headers, $hasBody ? $body : null, $companyId, (bool) ($endpoint['authenticated'] ?? true)),
        ];
    }

    /**
     * @param  array<string, string>  $headers
     */
    protected static function curl(string $method, string $url, array $headers, ?array $body): string
    {
        $lines = ['curl --request '.$method, '  '.static::shellQuote($url)];

        foreach ($headers as $name => $value) {
            $lines[] = '  --header '.static::shellQuote($name.': '.$value);
        }

        if ($body !== null) {
            $lines[] = '  --data '.static::shellQuote(static::json($body, pretty: false));
        }

        return implode(" \\\n", $lines);
    }

    /**
     * @param  array<string, string>  $headers
     */
    protected static function javascript(string $method, string $url, array $headers, ?array $body): string
    {
        $headerLines = [];

        foreach ($headers as $name => $value) {
            $headerLines[] = '    '.static::json($name).': '.static::json($value).',';
        }

        $lines = [
            'const response = await fetch('.static::json($url).', {',
            '  method: '.static::json($method).',',
            '  headers: {',
            ...$headerLines,
            '  },',
        ];

        if ($body !== null) {
            $lines[] = '  body: JSON.stringify('.static::indent(static::json($body), '  ').'),';
        }

        $lines[] = '});';
        $lines[] = '';
        $lines[] = 'const data = await response.json();';

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, string>  $headers
     */
    protected static function python(string $method, string $url, array $headers, ?array $body): string
    {
        $lines = [
            'import requests',
            '',
            'response = requests.'.mb_strtolower($method).'(',
            '    '.static::json($url).',',
            '    headers='.static::indent(static::pythonLiteral($headers), '    ').',',
        ];

        if ($body !== null) {
            $lines[] = '    json='.static::indent(static::pythonLiteral($body), '    ').',';
        }

        $lines[] = ')';
        $lines[] = '';
        $lines[] = 'data = response.json()';

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, string>  $headers
     */
    protected static function php(string $method, string $url, array $headers, ?array $body, ?int $companyId, bool $authenticated): string
    {
        $lines = [
            'use Illuminate\\Support\\Facades\\Http;',
            '',
            '$response = Http::acceptJson()',
        ];

        if ($authenticated) {
            $lines[] = "    ->withToken('".self::TOKEN."')";
        }

        if ($authenticated && $companyId) {
            $lines[] = "    ->withHeaders(['X-Company-Id' => ".$companyId.'])';
        }

        $verb = mb_strtolower($method);
        $call = '    ->'.$verb.'('.static::phpLiteral($url);

        if ($body !== null) {
            $call .= ', '.static::indent(static::phpLiteral($body), '    ');
        }

        $lines[] = $call.');';
        $lines[] = '';
        $lines[] = '$data = $response->json();';

        return implode("\n", $lines);
    }

    protected static function json(mixed $value, bool $pretty = true): string
    {
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;

        return (string) json_encode($value, $pretty ? $flags | JSON_PRETTY_PRINT : $flags);
    }

    protected static function shellQuote(string $value): string
    {
        return "'".str_replace("'", "'\\''", $value)."'";
    }

    protected static function indent(string $text, string $prefix): string
    {
        return str_replace("\n", "\n".$prefix, $text);
    }

    /**
     * A JSON value as Python source: JSON strings and numbers are already
     * valid Python, only true/false/null and the containers differ.
     */
    protected static function pythonLiteral(mixed $value, int $depth = 0): string
    {
        if ($value === null) {
            return 'None';
        }

        if (is_bool($value)) {
            return $value ? 'True' : 'False';
        }

        if (! is_array($value)) {
            return static::json($value);
        }

        if ($value === []) {
            return '[]';
        }

        $pad = str_repeat('    ', $depth + 1);
        $close = str_repeat('    ', $depth);
        $items = [];

        foreach ($value as $key => $item) {
            $items[] = $pad.(array_is_list($value) ? '' : static::json((string) $key).': ').static::pythonLiteral($item, $depth + 1);
        }

        return (array_is_list($value) ? '[' : '{')."\n".implode(",\n", $items).",\n".$close.(array_is_list($value) ? ']' : '}');
    }

    protected static function phpLiteral(mixed $value, int $depth = 0): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (! is_array($value)) {
            return var_export((string) $value, true);
        }

        if ($value === []) {
            return '[]';
        }

        $pad = str_repeat('    ', $depth + 1);
        $close = str_repeat('    ', $depth);
        $items = [];

        foreach ($value as $key => $item) {
            $items[] = $pad.(array_is_list($value) ? '' : var_export((string) $key, true).' => ').static::phpLiteral($item, $depth + 1);
        }

        return "[\n".implode(",\n", $items).",\n".$close.']';
    }
}
