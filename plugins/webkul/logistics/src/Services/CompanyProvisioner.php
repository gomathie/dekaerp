<?php

namespace Webkul\Logistics\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\UOM;

/**
 * Switches Logistics on or off for one company and prepares what it needs.
 * Every step is safe to repeat.
 *
 * Accounting records (journals, chart of accounts) belong to Accounting: they
 * are reported by readiness() when missing, never created here.
 */
class CompanyProvisioner
{
    /**
     * Default service products per company (D14), keyed by product reference.
     */
    public const SERVICE_PRODUCTS = [
        'LOG-FREIGHT'  => 'Freight',
        'LOG-PICKUP'   => 'Pickup',
        'LOG-DELIVERY' => 'Delivery',
        'LOG-HANDLING' => 'Handling',
        'LOG-WAITING'  => 'Waiting time',
        'LOG-STORAGE'  => 'Storage',
    ];

    /**
     * What the company is missing for Logistics to work end to end.
     *
     * @return array<int, string> keys under logistics::services/company-provisioner.readiness
     */
    public function readiness(Company $company): array
    {
        $problems = [];

        if (! $company->currency_id) {
            $problems[] = 'missing-currency';
        }

        if (! $this->journalId($company, JournalType::SALE)) {
            $problems[] = 'missing-sale-journal';
        }

        if (! $this->journalId($company, JournalType::PURCHASE)) {
            $problems[] = 'missing-purchase-journal';
        }

        if (! $this->unitsUomId() || ! $this->rootCategoryId()) {
            $problems[] = 'missing-product-defaults';
        }

        return $problems;
    }

    public function provision(Company $company): CompanySetting
    {
        return DB::transaction(function () use ($company): CompanySetting {
            foreach (LogisticsSequences::CODES as $code) {
                LogisticsSequences::ensure($code, $company->id);
            }

            $this->ensureServiceProducts($company);

            $setting = CompanySetting::forCompany($company->id);

            $setting->invoice_journal_id ??= $this->journalId($company, JournalType::SALE);
            $setting->bill_journal_id ??= $this->journalId($company, JournalType::PURCHASE);

            $setting->save();

            return $setting;
        });
    }

    public function enable(Company $company): CompanySetting
    {
        $setting = DB::transaction(function () use ($company): CompanySetting {
            $setting = $this->provision($company);

            if (! $setting->is_enabled) {
                $setting->forceFill([
                    'is_enabled'    => true,
                    'enabled_at'    => now(),
                    'enabled_by_id' => Auth::id(),
                    'disabled_at'   => null,
                ])->save();
            }

            return $setting;
        });

        LogisticsAccess::flush();

        return $setting;
    }

    /**
     * Hides Logistics for the company and blocks new records. Data is kept.
     */
    public function disable(Company $company): CompanySetting
    {
        $setting = CompanySetting::forCompany($company->id);

        if ($setting->exists && $setting->is_enabled) {
            $setting->forceFill([
                'is_enabled'  => false,
                'disabled_at' => now(),
            ])->save();
        }

        LogisticsAccess::flush();

        return $setting;
    }

    protected function ensureServiceProducts(Company $company): void
    {
        $uomId = $this->unitsUomId();
        $categoryId = $this->rootCategoryId();

        if (! $uomId || ! $categoryId) {
            return;
        }

        foreach (self::SERVICE_PRODUCTS as $reference => $name) {
            // All scopes removed on purpose: an existing (even archived) product with
            // this reference in this company must be found so it isn't duplicated.
            $exists = Product::withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('reference', $reference)
                ->exists();

            if ($exists) {
                continue;
            }

            Product::create([
                'type'            => ProductType::SERVICE,
                'name'            => $name,
                'reference'       => $reference,
                'price'           => 0,
                'enable_sales'    => true,
                'enable_purchase' => false,
                'uom_id'          => $uomId,
                'uom_po_id'       => $uomId,
                'category_id'     => $categoryId,
                'company_id'      => $company->id,
                'creator_id'      => Auth::id(),
            ]);
        }
    }

    protected function journalId(Company $company, JournalType $type): ?int
    {
        return Journal::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id)
            ->where('type', $type)
            ->orderBy('id')
            ->value('id');
    }

    protected function unitsUomId(): ?int
    {
        return UOM::query()->where('name', 'Units')->orderBy('id')->value('id');
    }

    protected function rootCategoryId(): ?int
    {
        return Category::query()->whereNull('parent_id')->orderBy('id')->value('id');
    }
}
