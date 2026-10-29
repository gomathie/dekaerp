<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Product\Database\Factories\CategoryFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Concerns\HasContributedAttributes;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

class Category extends Model
{
    use BelongsToCompany;
    use HasChatter, HasContributedAttributes, HasCustomFields, HasFactory, HasLogActivity;
    use InheritsParentCompany;

    public const ACTIVITY_PLAN_PLUGIN = 'products';

    /*
     * Each client has its own catalogue, so a category belongs to the company
     * that creates it and a sub-category takes its parent's company, never the
     * session's (docs/company-admin-role-plan.md §4k). InheritsParentCompany
     * refuses a child filed under a different company from its parent.
     */
    protected static string $parentCompanyModel = self::class;

    protected static string $parentCompanyKey = 'parent_id';

    protected $table = 'products_categories';

    public function getModelTitle(): string
    {
        return __('products::models/category.title');
    }

    protected $fillable = [
        'name',
        'full_name',
        'parent_path',
        'parent_id',
        'company_id',
        'creator_id',
    ];

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                 => __('products::models/category.log-attributes.name'),
            'full_name'            => __('products::models/category.log-attributes.full_name'),
            'parent_path'          => __('products::models/category.log-attributes.parent_path'),
            'parent.name'          => __('products::models/category.log-attributes.parent'),
            'creator.name'         => __('products::models/category.log-attributes.creator'),
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function priceRuleItems(): HasMany
    {
        return $this->hasMany(PriceRuleItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (! static::validateNoRecursion($category)) {
                throw new InvalidArgumentException('Circular reference detected in product category hierarchy');
            }

            // Auth::id() rather than Auth::user()->id: provisioning runs from
            // Company::created, where nobody may be acting yet, and reading a
            // property off null is a warning Laravel turns into an exception.
            $category->creator_id ??= Auth::id();

            static::handleProductCategoryData($category);
        });

        static::updating(function ($category) {
            if (! static::validateNoRecursion($category)) {
                throw new InvalidArgumentException('Circular reference detected in product category hierarchy');
            }

            static::handleProductCategoryData($category);
        });
    }

    protected static function validateNoRecursion($category)
    {
        if (! $category->parent_id) {
            return true;
        }

        if (
            $category->exists
            && $category->id == $category->parent_id
        ) {
            return false;
        }

        $visitedIds = [$category->exists ? $category->id : -1];
        $currentParentId = $category->parent_id;

        while ($currentParentId) {
            if (in_array($currentParentId, $visitedIds)) {
                return false;
            }

            $visitedIds[] = $currentParentId;
            $parent = static::withoutGlobalScopes()->find($currentParentId);

            if (! $parent) {
                break;
            }

            $currentParentId = $parent->parent_id;
        }

        return true;
    }

    protected static function handleProductCategoryData($category)
    {
        if ($category->parent_id) {
            // Scope-free, like the other parent reads in this class: the parent
            // may belong to a company that is not active in this session (a
            // company provisioner, a queue worker, a user acting across
            // companies). CompanyScope would return null and the branch below
            // then silently clears parent_id and resets parent_path - the
            // category moves to the root instead of failing loudly.
            $parent = static::withoutGlobalScopes()->find($category->parent_id);

            if ($parent) {
                $category->parent_path = $parent->parent_path.$parent->id.'/';
            } else {
                $category->parent_path = '/';
                $category->parent_id = null;
            }
        } else {
            $category->parent_path = '/';
        }

        $category->full_name = static::getCompleteName($category);
    }

    protected static function getCompleteName($category)
    {
        $names = [];
        $names[] = $category->name;

        $currentCategory = $category;

        while ($currentCategory->parent_id) {
            $currentCategory = static::withoutGlobalScopes()->find($currentCategory->parent_id);

            if ($currentCategory) {
                array_unshift($names, $currentCategory->name);
            } else {
                break;
            }
        }

        return implode(' / ', $names);
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
