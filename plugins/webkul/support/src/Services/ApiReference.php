<?php

namespace Webkul\Support\Services;

use Illuminate\Support\Str;
use Knuckles\Camel\Camel;
use Knuckles\Scribe\Tools\DocumentationConfig;
use Knuckles\Scribe\Tools\PathConfig;
use Knuckles\Scribe\Writing\OpenAPISpecWriter;
use Knuckles\Scribe\Writing\Writer;
use Symfony\Component\Yaml\Yaml;

/**
 * The API reference, read from the committed Scribe data in .scribe/endpoints.
 *
 * Before this, /api/docs served storage/app/private/scribe/openapi.yaml, a file
 * `scribe:generate` writes on the developer's machine and that is gitignored,
 * so production had no spec and every visit answered 500 (Sentry DEKAERP-26).
 * Building the OpenAPI document and the Postman collection here from the
 * committed endpoint files means a deploy needs no generation step, no
 * database and no network.
 *
 * The endpoint files are about 1.9 MB of YAML. They are parsed only when their
 * content changes: each generated document is written once under
 * storage/framework/cache, named by the fingerprint of the files it was built
 * from.
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

    protected ?string $fingerprint = null;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    protected ?array $rawGroups = null;

    public function __construct(protected ?string $directory = null) {}

    public function directory(): string
    {
        return $this->directory ?? base_path('.scribe/endpoints');
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
