<?php

namespace Webkul\Field\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Field extends Model implements Sortable
{
    use SoftDeletes, SortableTrait;

    /**
     * Resolved custom fields, keyed by customizable model class.
     *
     * @var array<class-string, Collection>
     */
    protected static array $customizableCache = [];

    /** @var list<string>|null */
    protected static ?array $storedTypesCache = null;

    /** @var array<class-string, string> */
    protected static array $tableCache = [];

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'custom_fields';

    /**
     * Table name.
     *
     * @var string
     */
    protected $casts = [
        'is_multiselect'    => 'boolean',
        'options'           => 'array',
        'form_settings'     => 'array',
        'table_settings'    => 'array',
        'infolist_settings' => 'array',
    ];

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'input_type',
        'is_multiselect',
        'datalist',
        'options',
        'form_settings',
        'use_in_table',
        'table_settings',
        'infolist_settings',
        'sort',
        'customizable_type',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        $flush = fn () => static::flushCustomizableCache();

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);
    }

    /**
     * Get the custom fields applying to the given model.
     */
    public static function forCustomizable(string $model): Collection
    {
        return static::$customizableCache[$model] ??= static::query()
            ->whereIn('customizable_type', static::customizableTypes($model))
            ->get(['code', 'type', 'is_multiselect']);
    }

    /**
     * Forget the resolved custom fields.
     */
    public static function flushCustomizableCache(?string $model = null): void
    {
        // Any field change can add a new customizable type, which changes
        // which models share fields, so the type list is always reset.
        static::$storedTypesCache = null;

        if ($model === null) {
            static::$customizableCache = [];

            return;
        }

        unset(static::$customizableCache[$model]);
    }

    /**
     * Resolve the customizable types whose fields apply to a model: the model,
     * its parent classes, and every other model on the same table.
     *
     * A custom field is a column of a table. Each module has its own product
     * (and contact) model class, so a field created on, say, the Inventory
     * product showed only on Inventory screens and was not saved from the Sales
     * product screen, although every one of them writes products_products
     * (unified product resource, user 2026-11-11). Matching on the table makes
     * one field appear, and save, wherever that table is edited.
     */
    public static function customizableTypes(string $model): array
    {
        $types = [$model];

        foreach (class_parents($model) as $parent) {
            if ($parent === Model::class) {
                break;
            }

            $types[] = $parent;
        }

        $table = static::tableOf($model);

        if ($table !== null) {
            foreach (static::storedCustomizableTypes() as $type) {
                if (! in_array($type, $types, true) && static::tableOf($type) === $table) {
                    $types[] = $type;
                }
            }
        }

        return $types;
    }

    /**
     * Distinct customizable types that have fields, cached until a field changes.
     *
     * @return list<string>
     */
    protected static function storedCustomizableTypes(): array
    {
        return static::$storedTypesCache ??= static::query()
            ->withTrashed()
            ->distinct()
            ->pluck('customizable_type')
            ->filter()
            ->values()
            ->all();
    }

    /** The table of a model class, or null when the class is not loaded (its plugin is not installed). */
    protected static function tableOf(string $model): ?string
    {
        if (! class_exists($model) || ! is_subclass_of($model, Model::class)) {
            return null;
        }

        // Without the constructor: building a customizable model loads its
        // custom fields, which calls back into customizableTypes() and loops
        // until PHP crashes (caught by SharedProductCustomFieldsTest).
        return static::$tableCache[$model] ??= (new \ReflectionClass($model))
            ->newInstanceWithoutConstructor()
            ->getTable();
    }
}
