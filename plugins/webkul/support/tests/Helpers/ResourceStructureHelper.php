<?php

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

require_once __DIR__.'/FilamentHelper.php';

/**
 * Declared-shape snapshots of Filament resources, for the resource structure
 * alignment work (docs/upstream-structure-alignment-plan.md, Phase 0).
 *
 * Forms and infolists are built standalone: Schema::make() accepts a null
 * livewire, so the declared tree is reachable without mounting a page.
 *
 * Tables are built on an unmounted index page (describeTable()), so columns,
 * filters, groups and actions are captured too - a dropped column or a lost
 * visibleFrom() is the failure that matters most for this work.
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
            'table'    => self::guard(fn (): ?array => self::describeTable($resource)),
        ];
    }

    /**
     * The declared table, built on an unmounted instance of the resource's
     * index page: Table::make() only needs a HasTable to hang off, and
     * mounting would tie the snapshot to records and to the acting user.
     *
     * Columns keep declaration order, and record visibleFrom() and toggle
     * state, because those are what the mobile work added by hand (14
     * resources) and what a careless move drops without failing a test.
     *
     * @return array<string, mixed>|null
     */
    public static function describeTable(string $resource): ?array
    {
        $page = $resource::getPages()['index'] ?? null;

        if ($page === null) {
            return null;
        }

        $pageClass = $page->getPage();
        $livewire = new $pageClass;

        if (! $livewire instanceof HasTable) {
            return null;
        }

        $table = $resource::table(Table::make($livewire));

        return [
            'columns' => array_values(array_map(fn (Column $column): array => array_filter([
                'type'        => $column::class,
                'name'        => $column->getName(),
                'visibleFrom' => self::guard(fn (): ?string => $column->getVisibleFrom()),
                'sortable'    => self::guard(fn (): bool => $column->isSortable()) ?: null,
                'searchable'  => self::guard(fn (): bool => $column->isSearchable()) ?: null,
                'toggleable'  => self::guard(fn (): bool => $column->isToggleable()) ?: null,
                'hiddenByDefault' => self::guard(fn (): bool => $column->isToggledHiddenByDefault()) ?: null,
            ], fn (mixed $value): bool => $value !== null), $table->getColumns())),
            'filters'        => array_values(array_map(fn (object $filter): array => ['type' => $filter::class, 'name' => $filter->getName()], $table->getFilters(withHidden: true))),
            'groups'         => array_keys($table->getGroups()),
            'defaultSort'    => self::guard(fn (): ?string => $table->getDefaultSortColumn()),
            'recordActions'  => self::describeActions($table->getRecordActions()),
            'toolbarActions' => self::describeActions($table->getToolbarActions()),
            'headerActions'  => self::describeActions($table->getHeaderActions()),
        ];
    }

    /**
     * Actions and action groups, in order, by class and name.
     *
     * @param  array<int|string, object>  $actions
     * @return array<int, array<string, mixed>>
     */
    private static function describeActions(array $actions): array
    {
        $described = [];

        foreach ($actions as $action) {
            $entry = ['type' => $action::class];

            if (method_exists($action, 'getName')) {
                $entry['name'] = self::guard(fn (): ?string => $action->getName());
            }

            if (method_exists($action, 'getActions')) {
                $entry['children'] = self::describeActions(self::guard(fn (): array => $action->getActions()) ?: []);
            }

            $described[] = $entry;
        }

        return $described;
    }

    /**
     * The declared component tree, in declaration order.
     *
     * Order is preserved and never keyed by name, because in Filament field
     * order is display order, and docs/completed/restructure-backlog.md compares code as
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

        // getDefaultChildComponents() returns the declared children directly.
        // getChildSchemas() would build Schema containers instead, which needs
        // a livewire context Schema::make() cannot supply - that threw, the
        // error was swallowed, and the snapshot came out flat.
        if (method_exists($component, 'getDefaultChildComponents')) {
            $children = self::guard(function () use ($component): array {
                $declared = $component->getDefaultChildComponents();

                if ($declared instanceof Schema) {
                    return self::describeSchema($declared);
                }

                $nested = [];

                foreach ($declared as $child) {
                    if (is_object($child)) {
                        $nested[] = self::describeComponent($child);
                    }
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
            // The message is recorded, not just the class: a swallowed message
            // once hid the reason the whole snapshot came out flat.
            return ['__error' => $exception::class, '__message' => $exception->getMessage()];
        }
    }
}
