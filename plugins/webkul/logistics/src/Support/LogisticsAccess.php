<?php

namespace Webkul\Logistics\Support;

use Illuminate\Support\Facades\Schema;
use Webkul\Logistics\Exceptions\LogisticsNotEnabledException;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Services\CompanyContext;

/**
 * The per-company Logistics switch.
 *
 * Installing a plugin is global, so this is what keeps companies that don't do
 * logistics from seeing or creating logistics records. It is checked by the
 * policies and by every service that creates records or changes their state.
 *
 * Bound as a scoped singleton (LogisticsServiceProvider), so the loaded flags
 * live for one request or queued job only.
 */
class LogisticsAccess
{
    /**
     * @var array<int, bool>|null
     */
    protected ?array $enabled = null;

    public static function enabledFor(?int $companyId): bool
    {
        if (! $companyId) {
            return false;
        }

        return app(static::class)->flags()[$companyId] ?? false;
    }

    /**
     * True when at least one of the user's active companies has Logistics enabled.
     */
    public static function enabledForCurrent(): bool
    {
        $context = app(CompanyContext::class);

        $ids = $context->activeIds();

        if ($ids === [] && $context->currentId()) {
            $ids = [$context->currentId()];
        }

        foreach ($ids as $id) {
            if (static::enabledFor((int) $id)) {
                return true;
            }
        }

        return false;
    }

    public static function ensureEnabled(?int $companyId): void
    {
        if (! static::enabledFor($companyId)) {
            throw LogisticsNotEnabledException::forCompany($companyId);
        }
    }

    /**
     * @return array<int, int>
     */
    public static function enabledCompanyIds(): array
    {
        return array_keys(array_filter(app(static::class)->flags()));
    }

    public static function flush(): void
    {
        app(static::class)->enabled = null;
    }

    /**
     * @return array<int, bool>
     */
    protected function flags(): array
    {
        if ($this->enabled !== null) {
            return $this->enabled;
        }

        if (! Schema::hasTable('logistics_company_settings')) {
            return $this->enabled = [];
        }

        // The switch must be readable for any company a record belongs to, not only
        // the companies active in the current session, so the company scope is
        // removed here. Only the boolean flag leaves this method.
        return $this->enabled = CompanySetting::withoutGlobalScope(CompanyScope::class)
            ->pluck('is_enabled', 'company_id')
            ->map(fn ($value): bool => (bool) $value)
            ->all();
    }
}
