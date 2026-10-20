<?php

use Filament\Facades\Filament;
use Filament\Schemas\Schema;

require_once __DIR__.'/FilamentHelper.php';

/**
 * Declared-shape snapshots of Filament resources, for the resource structure
 * alignment work (docs/upstream-structure-alignment-plan.md, Phase 0).
 *
 * Forms and infolists are built standalone: Schema::make() accepts a null
 * livewire, so the declared tree is reachable without mounting a page. Tables
 * are not - Table::make() requires a HasTable component - so a table is
 * described only where the caller supplies a mounted List page.
 */
class ResourceStructureHelper
{
    /**
     * Registered admin panel resources, optionally limited to one namespace
     * prefix so a single plugin can be snapshotted on its own.
     *
     * @return array<int, class-string>
     */
    public static function resources(string $namespacePrefix = ''): array
    {
        FilamentHelper::bootAdminPanel();

        $resources = array_values(array_filter(
            Filament::getPanel('admin')->getResources(),
            fn (string $resource): bool => $namespacePrefix === ''
                || str_starts_with($resource, $namespacePrefix),
        ));

        sort($resources);

        return $resources;
    }

    /**
     * @return array<string, mixed>
     */
    public static function describeResource(string $resource): array
    {
        return [
            'model'    => self::guard(fn (): ?string => $resource::getModel()),
            'form'     => self::guard(fn (): array => self::describeSchema($resource::form(Schema::make()))),
            'infolist' => self::guard(fn (): array => self::describeSchema($resource::infolist(Schema::make()))),
        ];
    }

    /**
     * The declared component tree, in declaration order.
     *
     * Order is preserved and never keyed by name, because in Filament field
     * order is display order, and docs/restructure-backlog.md compares code as
     * sets and so cannot detect a reordering.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function describeSchema(Schema $schema): array
    {
        $described = [];

        // withHidden: true on purpose. Evaluating visibility would tie the
        // snapshot to the acting user and to form state, and would conceal a
        // field accidentally dropped behind a condition.
        foreach ($schema->getComponents(withActions: true, withHidden: true) as $component) {
            $described[] = self::describeComponent($component);
        }

        return $described;
    }

    /**
     * Columns of a table taken from a mounted List page, in order.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function describeTableColumns(Filament\Tables\Table $table): array
    {
        $described = [];

        foreach ($table->getColumns() as $column) {
            $described[] = [
                'name' => $column->getName(),
                'type' => $column::class,
            ];
        }

        return $described;
    }

    /**
     * @return array<string, mixed>
     */
    private static function describeComponent(object $component): array
    {
        $described = ['type' => $component::class];

        if (method_exists($component, 'getName')) {
            $name = self::guard(fn (): ?string => $component->getName());

            if ($name !== null) {
                $described['name'] = $name;
            }
        }

        if (method_exists($component, 'getChildSchemas')) {
            $children = self::guard(function () use ($component): array {
                $nested = [];

                foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                    $nested[] = self::describeSchema($childSchema);
                }

                return $nested;
            });

            if (is_array($children) && $children !== []) {
                $described['children'] = $children;
            }
        }

        return $described;
    }

    /**
     * A resource that cannot be described must not abort the whole snapshot:
     * the recorded reason is itself a fact worth diffing, and one unbuildable
     * resource would otherwise hide the other 226.
     */
    private static function guard(Closure $callback): mixed
    {
        try {
            return $callback();
        } catch (Throwable $exception) {
            return ['__error' => $exception::class];
        }
    }
}
