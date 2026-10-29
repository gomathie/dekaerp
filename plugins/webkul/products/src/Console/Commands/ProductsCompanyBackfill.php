<?php

namespace Webkul\Product\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Product\Models\Category;
use Webkul\Product\Services\CompanyCategoryProvisioner;
use Webkul\Support\Models\Company;

/**
 * Files this installation's existing categories and product 1 under their
 * company (docs/company-admin-role-plan.md §4k, "Revised backfill").
 *
 * A command rather than a migration on purpose: the rows are named by id, so it
 * is one installation's data, and a migration would also run on test and fresh
 * databases - where the expected rows do not exist, and where a command that
 * tried to be helpful would file somebody's test fixtures.
 *
 * Nothing is written until the database has been compared against the state the
 * plan was written from: the same ids, the same companies, the same parent
 * links. A wrong starting state would re-point a real document's category, and
 * the mistake is silent afterwards - a scoped relation read returns null rather
 * than raising.
 *
 *     php artisan products:company-backfill --dry-run
 *     php artisan products:company-backfill
 */
class ProductsCompanyBackfill extends Command
{
    protected $signature = 'products:company-backfill {--dry-run : Print every change without writing it}';

    protected $description = 'File the existing product categories and product 1 under their company';

    /** Company 1 created the ten categories and keeps the originals. */
    private const CATEGORY_COMPANY = 1;

    /** Product 1 belongs to company 2. */
    private const PRODUCT_ID = 1;

    private const PRODUCT_COMPANY = 2;

    private const PRODUCT_CATEGORY_ID = 2;

    /** Existing companies 3-5 have no catalogue rows in the production copy. */
    private const COMPANIES_WITHOUT_CATEGORIES = [3, 4, 5];

    /**
     * The "1 > 2" branch company 2 needs, parent first: a child cannot sit under
     * another company's parent.
     */
    private const COPIED_CATEGORY_IDS = [1, 2];

    /** The seeded tree, as production holds it before this command runs. */
    private const CATEGORY_PARENTS = [
        1  => null,
        2  => 1,
        3  => 1,
        4  => 1,
        5  => 1,
        6  => 1,
        7  => 6,
        8  => 6,
        9  => 6,
        10 => 9,
    ];

    public function handle(): int
    {
        foreach (['companies', 'products_products', 'products_categories', 'products_tags', 'partners_tags'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->components->error("The {$table} table does not exist, so there is nothing to file.");

                return self::FAILURE;
            }
        }

        if ($this->alreadyApplied()) {
            $this->components->info('Nothing to do: the categories and product 1 already match the target state.');

            return self::SUCCESS;
        }

        $differences = $this->differencesFromExpectedState();

        if ($differences !== []) {
            $this->components->error('The database does not match the expected starting state. Nothing was written.');

            foreach ($differences as $difference) {
                $this->line('  - '.$difference);
            }

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->printChanges(null);

            $this->newLine();
            $this->components->info('Dry run: nothing was written. Run again without --dry-run to apply.');

            return self::SUCCESS;
        }

        [$firstCopyId, $secondCopyId, $provisionedCategories] = DB::transaction(fn (): array => $this->apply());

        $this->printChanges([$firstCopyId, $secondCopyId, $provisionedCategories]);

        $this->newLine();
        $this->components->info('Applied.');

        return self::SUCCESS;
    }

    /**
     * One transaction: a product pointing at a half-copied branch is worse than
     * no backfill at all.
     *
     * @return array{0: int, 1: int, 2: array<int, array{company_id: int, category_id: int}>} the two copy ids and newly provisioned roots
     */
    protected function apply(): array
    {
        DB::table('products_categories')
            ->whereIn('id', array_keys(self::CATEGORY_PARENTS))
            ->update(['company_id' => self::CATEGORY_COMPANY]);

        $copiedIds = [];

        foreach (self::COPIED_CATEGORY_IDS as $originalId) {
            $original = Category::withoutGlobalScopes()->findOrFail($originalId);

            // Through the model, so parent_path and full_name are derived by the
            // same code the form uses rather than restated here.
            $copy = Category::withoutGlobalScopes()->create([
                'name'       => $original->name,
                'parent_id'  => $original->parent_id === null ? null : $copiedIds[$original->parent_id],
                'company_id' => self::PRODUCT_COMPANY,
                'creator_id' => $original->creator_id,
            ]);

            $copiedIds[$originalId] = $copy->id;
        }

        DB::table('products_products')
            ->where('id', self::PRODUCT_ID)
            ->update([
                'category_id' => $copiedIds[self::PRODUCT_CATEGORY_ID],
                'company_id'  => self::PRODUCT_COMPANY,
            ]);

        $provisionedCategories = [];
        $categoryProvisioner = app(CompanyCategoryProvisioner::class);

        foreach (Company::withoutGlobalScopes()->whereNull('deleted_at')->orderBy('id')->get() as $company) {
            if (Category::withoutGlobalScopes()->where('company_id', $company->id)->exists()) {
                continue;
            }

            $category = $categoryProvisioner->provision($company);

            if ($category) {
                $provisionedCategories[] = [
                    'company_id'  => (int) $company->id,
                    'category_id' => (int) $category->id,
                ];
            }
        }

        return [$copiedIds[self::COPIED_CATEGORY_IDS[0]], $copiedIds[self::PRODUCT_CATEGORY_ID], $provisionedCategories];
    }

    /**
     * True once this command has run: the originals belong to the category
     * company, product 1 belongs to its own company, and it points at a
     * category of that company.
     */
    protected function alreadyApplied(): bool
    {
        $filed = DB::table('products_categories')
            ->whereIn('id', array_keys(self::CATEGORY_PARENTS))
            ->where('company_id', self::CATEGORY_COMPANY)
            ->count();

        if ($filed !== count(self::CATEGORY_PARENTS)) {
            return false;
        }

        $product = DB::table('products_products')
            ->where('id', self::PRODUCT_ID)
            ->first(['company_id', 'category_id']);

        if (! $product || (int) $product->company_id !== self::PRODUCT_COMPANY) {
            return false;
        }

        $categoryCompany = DB::table('products_categories')
            ->where('id', $product->category_id)
            ->value('company_id');

        if ((int) $categoryCompany !== self::PRODUCT_COMPANY) {
            return false;
        }

        return $this->companiesMissingCategories() === [];
    }

    /**
     * Every way the database differs from the expected starting state, phrased
     * so the operator can see what to do about it.
     *
     * @return array<int, string>
     */
    protected function differencesFromExpectedState(): array
    {
        $differences = [];

        foreach ([self::CATEGORY_COMPANY => 'created the categories', self::PRODUCT_COMPANY => 'owns product 1'] as $companyId => $role) {
            if (! DB::table('companies')->where('id', $companyId)->exists()) {
                $differences[] = "Company #{$companyId}, which {$role}, does not exist.";
            }
        }

        foreach (self::COMPANIES_WITHOUT_CATEGORIES as $companyId) {
            if (! DB::table('companies')->where('id', $companyId)->exists()) {
                $differences[] = "Company #{$companyId}, which needs a default category, does not exist.";

                continue;
            }

            if (DB::table('products_categories')->where('company_id', $companyId)->exists()) {
                $differences[] = "Company #{$companyId} already has a category; it was expected to have none before this backfill.";
            }
        }

        $categories = DB::table('products_categories')
            ->whereIn('id', array_keys(self::CATEGORY_PARENTS))
            ->get(['id', 'company_id', 'parent_id'])
            ->keyBy('id');

        foreach (self::CATEGORY_PARENTS as $id => $expectedParentId) {
            $category = $categories->get($id);

            if (! $category) {
                $differences[] = "Category #{$id} does not exist.";

                continue;
            }

            if ($category->company_id !== null) {
                $differences[] = "Category #{$id} already belongs to company #{$category->company_id}; it was expected to be shared.";
            }

            $actualParentId = $category->parent_id === null ? null : (int) $category->parent_id;

            if ($actualParentId !== $expectedParentId) {
                $differences[] = "Category #{$id} has parent ".($actualParentId ?? 'none').', expected '.($expectedParentId ?? 'none').'.';
            }
        }

        $product = DB::table('products_products')
            ->where('id', self::PRODUCT_ID)
            ->first(['id', 'company_id', 'category_id']);

        if (! $product) {
            $differences[] = 'Product #'.self::PRODUCT_ID.' does not exist.';
        } else {
            if ($product->company_id !== null) {
                $differences[] = 'Product #'.self::PRODUCT_ID." already belongs to company #{$product->company_id}; it was expected to be shared.";
            }

            if ((int) $product->category_id !== self::PRODUCT_CATEGORY_ID) {
                $differences[] = 'Product #'.self::PRODUCT_ID.' points at category #'.($product->category_id ?? 'none').', expected #'.self::PRODUCT_CATEGORY_ID.'.';
            }
        }

        foreach (['products_tags', 'partners_tags'] as $table) {
            $count = DB::table($table)->count();

            if ($count > 0) {
                $differences[] = "{$table} holds {$count} tag(s); the plan expects none, so they need their own decision rather than this command.";
            }
        }

        return $differences;
    }

    /**
     * @return array<int, int>
     */
    protected function companiesMissingCategories(): array
    {
        return Company::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('products_categories')
                    ->whereColumn('products_categories.company_id', 'companies.id');
            })
            ->orderBy('id')
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    /**
     * @param  array{0: int, 1: int, 2: array<int, array{company_id: int, category_id: int}>}|null  $copyIds  null in a dry run, where the ids are not assigned yet
     */
    protected function printChanges(?array $copyIds): void
    {
        $this->components->info($copyIds === null ? 'Planned changes' : 'Changes applied');

        $this->table(['Record', 'Change'], [
            [
                'products_categories #'.implode(', #', array_keys(self::CATEGORY_PARENTS)),
                'company_id: none -> '.self::CATEGORY_COMPANY,
            ],
            [
                'products_categories (new)',
                $copyIds === null
                    ? 'a copy of #1 for company '.self::PRODUCT_COMPANY.', then a copy of #2 under it'
                    : 'copy of #1 is #'.$copyIds[0].', copy of #2 is #'.$copyIds[1].', both for company '.self::PRODUCT_COMPANY,
            ],
            [
                'products_products #'.self::PRODUCT_ID,
                'category_id: '.self::PRODUCT_CATEGORY_ID.' -> '.($copyIds === null ? 'the copy of #2' : '#'.$copyIds[1])
                    .', company_id: none -> '.self::PRODUCT_COMPANY,
            ],
            [
                'products_categories (missing company roots)',
                $copyIds === null
                    ? 'companies #'.implode(', #', self::COMPANIES_WITHOUT_CATEGORIES).': create one default root category each'
                    : collect($copyIds[2])->map(fn (array $row): string => 'company #'.$row['company_id'].' -> category #'.$row['category_id'])->implode('; '),
            ],
            [
                'products_tags, partners_tags',
                'nothing to do (the plan expects no tags)',
            ],
        ]);

        if ($copyIds === null) {
            return;
        }

        $this->newLine();
        $this->components->info('Rollback');

        $allCreatedCategoryIds = array_merge([$copyIds[0], $copyIds[1]], array_column($copyIds[2], 'category_id'));

        $this->line('  delete from products_categories where id in ('.implode(', ', $allCreatedCategoryIds).');');
        $this->line('  update products_products set category_id = '.self::PRODUCT_CATEGORY_ID.', company_id = null where id = '.self::PRODUCT_ID.';');
        $this->line('  update products_categories set company_id = null where id in ('.implode(', ', array_keys(self::CATEGORY_PARENTS)).');');
    }
}
