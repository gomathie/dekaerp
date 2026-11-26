<?php

namespace Webkul\Support\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Knuckles\Camel\Camel;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Knuckles\Scribe\Tools\PathConfig;
use Knuckles\Scribe\Writing\OpenAPISpecWriter;
use Knuckles\Scribe\Writing\Writer;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\GuideAudience;

/**
 * The API reference, read from the committed Scribe data in .scribe/endpoints.
 *
 * One source for both readers of it: the Developers section of the Help page
 * and the full reference at /api/docs. Before this, /api/docs served
 * storage/app/private/scribe/openapi.yaml, a file `scribe:generate` writes on
 * the developer's machine and that is gitignored, so production had no spec
 * and every visit answered 500 (Sentry DEKAERP-26). Building the spec here from
 * the committed endpoint files means a deploy needs no generation step, no
 * database and no network, and the spec can never lag behind the data the
 * Developers section shows.
 *
 * The endpoint files are about 1.9 MB of YAML. They are parsed only when their
 * content changes: the Developers data goes to the cache, the OpenAPI and
 * Postman documents to files under storage/framework/cache, and every cache
 * entry carries the fingerprint of the files it was built from.
 *
 * Bound as a scoped singleton in SupportServiceProvider.
 */
class ApiReference
{
    /**
     * Raised whenever the shape of the cached data changes, so an entry written
     * by an older build is rebuilt rather than read with missing keys.
     */
    protected const VERSION = 1;

    protected const CACHE_PREFIX = 'support.api-reference.';

    /**
     * Scribe group => the plugin whose entitlement guards its routes, and the
     * key used in the page's URL and translation files.
     *
     * The plugin names match `plugin.entitlement:<plugin>` on each plugin's
     * routes/api.php, so a company is shown exactly the endpoints its tokens
     * may call. A group not listed here is never shown: without a plugin to
     * check, there is no way to tell whether the company has it.
     */
    public const MODULES = [
        'Sales API Management'     => 'sales',
        'Purchase API Management'  => 'purchases',
        'Account API Management'   => 'accounts',
        'Inventory API Management' => 'inventories',
        'Product API Management'   => 'products',
        'Partner API Management'   => 'partners',
        'Project API Management'   => 'projects',
        'Support API Management'   => 'support',
    ];

    /**
     * Sign-in and sign-out. Every integration needs them whatever modules the
     * company has, so they belong to Getting started rather than to a module.
     */
    public const AUTH_GROUP = 'Security API Management';

    protected ?string $fingerprint = null;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    protected ?array $rawGroups = null;

    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $modules = [];

    public function __construct(protected ?string $directory = null) {}

    /**
     * The Filament colour of a method badge: GET blue, POST green, PUT and
     * PATCH amber, DELETE red.
     */
    public static function methodColor(string $method): string
    {
        return match (strtoupper($method)) {
            'GET'          => 'info',
            'POST'         => 'success',
            'PUT', 'PATCH' => 'warning',
            'DELETE'       => 'danger',
            default        => 'gray',
        };
    }

    public function directory(): string
    {
        return $this->directory ?? base_path('.scribe/endpoints');
    }

    /**
     * Who may read the in-app reference: Company Admin and up.
     *
     * The same rule as the administrator guide, on purpose - an API token is
     * an administrative decision, and two rules for "is this an administrator"
     * would drift apart.
     */
    public function allows(?object $user): bool
    {
        return GuideAudience::allowsUser($user, GuideAudience::Admin);
    }

    /**
     * The modules the signed-in user may read, for the company they are in.
     *
     * Everything the Developers section shows goes through this or module(),
     * so the administrator check and the company's grants hold by default
     * rather than wherever a caller remembered them. No super-admin bypass:
     * the page answers "what can this company's tokens call", and a module the
     * company lacks answers 403 to its tokens.
     *
     * @return array<string, array{key: string, group: string, count: int}>
     */
    public function availableModules(): array
    {
        if (! $this->allows(auth()->user())) {
            return [];
        }

        $companyId = PluginEntitlement::currentCompanyId();

        return array_filter(
            $this->summary()['modules'],
            fn (array $module): bool => PluginEntitlement::enabledForCompany($module['key'], $companyId),
        );
    }

    /**
     * One module's resources and endpoints, or null when it is not available.
     *
     * @return array{key: string, group: string, count: int, resources: array<int, array<string, mixed>>}|null
     */
    public function module(?string $key): ?array
    {
        if (blank($key) || ! array_key_exists($key, $this->availableModules())) {
            return null;
        }

        return $this->moduleData($key);
    }

    /**
     * The sign-in endpoints shown in Getting started.
     *
     * @return array<int, array<string, mixed>>
     */
    public function authEndpoints(): array
    {
        if (! $this->allows(auth()->user())) {
            return [];
        }

        return $this->moduleData('security')['resources'][0]['endpoints'] ?? [];
    }

    /**
     * Every endpoint in the reference, across all modules.
     */
    public function totalCount(): int
    {
        return (int) $this->summary()['total'];
    }

    /**
     * The OpenAPI document for /api/docs, as a path to a JSON file.
     */
    public function openApiPath(): string
    {
        return $this->document('openapi', function (array $groups, DocumentationConfig $config): string {
            $spec = app(OpenAPISpecWriter::class, ['config' => $config])->generateSpecContent($groups);

            return json_encode($spec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
        });
    }

    /**
     * The Postman collection for /api/docs, as a path to a JSON file.
     */
    public function postmanPath(): string
    {
        return $this->document('postman', function (array $groups, DocumentationConfig $config): string {
            return (new Writer($config, $this->pathConfig()))->generatePostmanCollection($groups);
        });
    }

    /**
     * A hash of everything the cached data is built from.
     *
     * Content hashes rather than modification times: a deploy rewrites every
     * file's mtime, and keying on it would rebuild the reference on each
     * deploy for no change. The base URL is included because the generated
     * documents carry it.
     */
    public function fingerprint(): string
    {
        if ($this->fingerprint !== null) {
            return $this->fingerprint;
        }

        $parts = [static::VERSION, rtrim((string) config('app.url'), '/'), (string) config('scribe.title')];

        foreach ($this->files() as $file) {
            $parts[] = basename($file).':'.md5_file($file);
        }

        return $this->fingerprint = md5(implode('|', $parts));
    }

    public function cacheKey(string $name): string
    {
        return static::CACHE_PREFIX.$name;
    }

    /**
     * @return array{modules: array<string, array{key: string, group: string, count: int}>, total: int}
     */
    protected function summary(): array
    {
        $cached = $this->cached('summary');

        if ($cached !== null) {
            return $cached;
        }

        return $this->build()['summary'];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function moduleData(string $key): ?array
    {
        if (array_key_exists($key, $this->modules)) {
            return $this->modules[$key];
        }

        $cached = $this->cached('module.'.$key);

        if ($cached === null) {
            $cached = $this->build()['modules'][$key] ?? null;
        }

        return $this->modules[$key] = $cached;
    }

    /**
     * Read a cache entry, ignoring one built from different files.
     *
     * Entries live under fixed keys and are overwritten on rebuild, so a
     * change to the endpoint files replaces them instead of leaving the old
     * reference behind in the cache store forever.
     *
     * @return array<string, mixed>|null
     */
    protected function cached(string $name): ?array
    {
        $entry = Cache::get($this->cacheKey($name));

        if (! is_array($entry) || ($entry['fingerprint'] ?? null) !== $this->fingerprint()) {
            return null;
        }

        return $entry['data'] ?? null;
    }

    /**
     * Parse the endpoint files once and cache every module separately.
     *
     * Separate entries so a page asking for one module reads that module, not
     * the whole reference.
     *
     * @return array{summary: array<string, mixed>, modules: array<string, array<string, mixed>>}
     */
    protected function build(): array
    {
        $modules = [];
        $summary = ['modules' => [], 'total' => 0];

        foreach ($this->rawGroups() as $name => $group) {
            $key = $name === static::AUTH_GROUP ? 'security' : (static::MODULES[$name] ?? null);

            if ($key === null) {
                continue;
            }

            $module = [
                'key'       => $key,
                'group'     => $name,
                'count'     => count($group['endpoints'] ?? []),
                'resources' => $this->resources($group['endpoints'] ?? []),
            ];

            $modules[$key] = $module;
            $summary['total'] += $module['count'];

            if ($key !== 'security') {
                $summary['modules'][$key] = ['key' => $key, 'group' => $name, 'count' => $module['count']];
            }
        }

        // Shown in the page's order, which is the order a business meets them,
        // not the alphabetical order of the YAML files.
        $order = array_values(static::MODULES);

        uksort($summary['modules'], fn (string $a, string $b): int => array_search($a, $order, true) <=> array_search($b, $order, true));

        $fingerprint = $this->fingerprint();

        foreach ($modules as $key => $module) {
            Cache::forever($this->cacheKey('module.'.$key), ['fingerprint' => $fingerprint, 'data' => $module]);
        }

        Cache::forever($this->cacheKey('summary'), ['fingerprint' => $fingerprint, 'data' => $summary]);

        return ['summary' => $summary, 'modules' => $modules];
    }

    /**
     * @param  array<int, array<string, mixed>>  $endpoints
     * @return array<int, array{key: string, name: string, description: string, endpoints: array<int, array<string, mixed>>}>
     */
    protected function resources(array $endpoints): array
    {
        $resources = [];
        $ids = [];

        foreach ($endpoints as $endpoint) {
            $compact = $this->endpoint($endpoint);

            // Ids anchor the page and the sidebar links, so two endpoints must
            // never share one.
            $id = $compact['id'];
            $suffix = 2;

            while (isset($ids[$compact['id']])) {
                $compact['id'] = $id.'-'.$suffix++;
            }

            $ids[$compact['id']] = true;

            // An endpoint with no subgroup keeps an empty name, translated when
            // shown: this array is cached, and a label in the builder's language
            // would be served to everyone.
            $name = trim((string) ($endpoint['metadata']['subgroup'] ?? ''));

            $resources[$name] ??= [
                'key'         => Str::slug($name) ?: 'general',
                'name'        => $name,
                'description' => $this->plainText($endpoint['metadata']['subgroupDescription'] ?? ''),
                'endpoints'   => [],
            ];

            $resources[$name]['endpoints'][] = $compact;
        }

        return array_values($resources);
    }

    /**
     * The part of a Scribe endpoint the page shows.
     *
     * Descriptions are reduced to plain text here and printed escaped. Scribe
     * fills them from docblocks and enum lists and they carry HTML fragments
     * such as `</br><b>Available options:</b>`; printing those raw would make
     * the endpoint files a way to put markup into an administrator's page.
     *
     * @param  array<string, mixed>  $endpoint
     * @return array<string, mixed>
     */
    protected function endpoint(array $endpoint): array
    {
        $methods = array_values(array_diff(array_map('strtoupper', (array) ($endpoint['httpMethods'] ?? ['GET'])), ['HEAD']));
        $method = $methods[0] ?? 'GET';
        $path = '/'.ltrim((string) ($endpoint['uri'] ?? ''), '/');
        $title = trim((string) ($endpoint['metadata']['title'] ?? '')) ?: $method.' '.$path;

        $parameters = [];

        foreach (['urlParameters' => 'path', 'queryParameters' => 'query', 'bodyParameters' => 'body'] as $source => $in) {
            foreach ((array) ($endpoint[$source] ?? []) as $name => $parameter) {
                $parameters[] = [
                    'name'        => (string) ($parameter['name'] ?? $name),
                    'type'        => (string) ($parameter['type'] ?? 'string'),
                    'in'          => $in,
                    'description' => $this->plainText($parameter['description'] ?? ''),
                    'required'    => (bool) ($parameter['required'] ?? false),
                ];
            }
        }

        $examplePath = $path;

        foreach ((array) ($endpoint['cleanUrlParameters'] ?? []) as $name => $value) {
            if (is_scalar($value)) {
                $examplePath = str_replace(['{'.$name.'}', '{'.$name.'?}'], rawurlencode((string) $value), $examplePath);
            }
        }

        $query = [];

        foreach ((array) ($endpoint['queryParameters'] ?? []) as $name => $parameter) {
            // Only required filters go into the samples: every optional filter
            // and include at once makes a sample nobody would send.
            if (($parameter['required'] ?? false) && is_scalar($endpoint['cleanQueryParameters'][$name] ?? null)) {
                $query[$name] = $endpoint['cleanQueryParameters'][$name];
            }
        }

        return [
            'id'            => trim((string) preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($method.' '.$path)), '-'),
            'method'        => $method,
            'path'          => $path,
            'example_path'  => $examplePath,
            'query'         => $query,
            'body'          => in_array($method, ['GET', 'DELETE'], true) ? null : $this->trimExample($endpoint['cleanBodyParameters'] ?? [], forRequest: true),
            'title'         => $this->plainText($title),
            'description'   => $this->plainText($endpoint['metadata']['description'] ?? ''),
            'authenticated' => (bool) ($endpoint['metadata']['authenticated'] ?? true),
            'parameters'    => $parameters,
            'response'      => $this->exampleResponse((array) ($endpoint['responses'] ?? [])),
        ];
    }

    /**
     * The first successful example answer, pretty-printed and shortened.
     *
     * @param  array<int, array<string, mixed>>  $responses
     * @return array{status: int, content: string}|null
     */
    protected function exampleResponse(array $responses): ?array
    {
        foreach ($responses as $response) {
            $status = (int) ($response['status'] ?? 0);

            if ($status < 200 || $status >= 300) {
                continue;
            }

            $content = (string) ($response['content'] ?? '');

            try {
                $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                $content = json_encode($this->trimExample($decoded), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } catch (Throwable) {
                // Not JSON: shown as text, shortened below.
            }

            return ['status' => $status, 'content' => Str::limit((string) $content, 6000, "\n…")];
        }

        return null;
    }

    /**
     * Shorten an example so a page of endpoints stays light.
     *
     * Lists keep two items (one in a request body, which must stay a valid
     * payload) and long strings are cut. A listing endpoint's example can
     * otherwise run to hundreds of records.
     */
    protected function trimExample(mixed $value, int $depth = 0, bool $forRequest = false): mixed
    {
        if (is_string($value)) {
            return Str::limit($value, 120);
        }

        if (! is_array($value)) {
            return $value;
        }

        if ($depth >= 6) {
            return $forRequest ? [] : '…';
        }

        if (array_is_list($value)) {
            $keep = $forRequest ? 1 : 2;
            $trimmed = array_map(fn (mixed $item): mixed => $this->trimExample($item, $depth + 1, $forRequest), array_slice($value, 0, $keep));

            if (! $forRequest && count($value) > $keep) {
                $trimmed[] = '… '.(count($value) - $keep).' more';
            }

            return $trimmed;
        }

        $trimmed = [];

        foreach ($value as $key => $item) {
            $trimmed[$key] = $this->trimExample($item, $depth + 1, $forRequest);
        }

        return $trimmed;
    }

    protected function plainText(mixed $html): string
    {
        $text = preg_replace('#<\s*/?\s*br\s*/?\s*>#i', "\n", (string) $html);
        $text = preg_replace('#</\s*(p|li|div|ul|ol)\s*>#i', "\n", (string) $text);
        $text = html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace("/\n{3,}/", "\n\n", $text));
    }

    /**
     * The endpoint files, as Scribe's `scribe:generate` writes them.
     *
     * @return array<int, string>
     */
    protected function files(): array
    {
        $files = glob($this->directory().'/*.yaml') ?: [];

        sort($files);

        return $files;
    }

    /**
     * Every group, as Scribe itself loads them for `--no-extraction`.
     *
     * custom.*.yaml holds hand-written endpoints; Scribe merges them into their
     * group by name, and so does this.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function rawGroups(): array
    {
        if ($this->rawGroups !== null) {
            return $this->rawGroups;
        }

        $groups = [];

        foreach ($this->files() as $file) {
            $parsed = Yaml::parseFile($file);

            if (str_starts_with(basename($file), 'custom.')) {
                foreach ((array) $parsed as $endpoint) {
                    $name = $endpoint['metadata']['groupName'] ?? (string) config('scribe.groups.default', 'Endpoints');

                    $groups[$name] ??= ['name' => $name, 'description' => '', 'endpoints' => []];
                    $groups[$name]['endpoints'][] = $endpoint;
                }

                continue;
            }

            if (is_array($parsed) && isset($parsed['name'])) {
                $groups[$parsed['name']] = $parsed;
            }
        }

        return $this->rawGroups = $groups;
    }

    /**
     * Build a generated document once per set of endpoint files.
     *
     * Written to local disk rather than the cache store: the OpenAPI document
     * is several megabytes, and the cache store is shared (database or Redis),
     * so every visit to /api/docs would have pulled it across the network. A
     * new container builds its own copy on first use.
     *
     * @param  callable(array, DocumentationConfig): string  $generate
     */
    protected function document(string $name, callable $generate): string
    {
        $directory = storage_path('framework/cache/api-docs');
        $path = $directory.'/'.$name.'-'.$this->fingerprint().'.json';

        if (is_file($path)) {
            return $path;
        }

        // The server address is the running application's, read now: config
        // files are cached with base_url resolved, and the fingerprint above
        // keys the document on this same value.
        $config = new DocumentationConfig(array_replace((array) config('scribe'), ['base_url' => rtrim((string) config('app.url'), '/')]));
        $groups = Camel::prepareGroupedEndpointsForOutput($this->rawGroups(), (array) config('scribe.groups.order', []));

        $contents = $generate($groups, $config);

        if (! is_dir($directory)) {
            mkdir($directory, 0o775, true);
        }

        // Written under a temporary name and renamed, so a request arriving
        // mid-write never serves half a document.
        $temporary = $path.'.'.Str::random(8).'.tmp';

        file_put_contents($temporary, $contents);
        rename($temporary, $path);

        foreach (glob($directory.'/'.$name.'-*.json') ?: [] as $stale) {
            if ($stale !== $path) {
                @unlink($stale);
            }
        }

        return $path;
    }

    protected function pathConfig(): PathConfig
    {
        return new PathConfig('scribe', dirname($this->directory()));
    }
}
