<?php

namespace Webkul\Attendance\Support;

use Illuminate\Support\Facades\Schema;
use Webkul\Attendance\Exceptions\AttendanceNotEnabledException;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Services\CompanyContext;

/**
 * The per-company attendance switch.
 *
 * Installing the plugin is global. This is what keeps a company that has not
 * turned attendance on from clocking in. Policies and the service both call it.
 * The service calls it again because a super-admin Gate::before skips policies.
 */
class AttendanceAccess
{
    /**
     * @var array<int, bool>|null
     */
    protected ?array $enabled = null;

    public static function enabledFor(?int $companyId): bool
    {
        if (! $companyId || ! PluginEntitlement::enabledForCompany('attendances', $companyId)) {
            return false;
        }

        return app(static::class)->flags()[$companyId] ?? false;
    }

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
            throw AttendanceNotEnabledException::forCompany($companyId);
        }
    }

    /**
     * A permission to correct attendance does not grant access outside the active tenant context.
     */
    public static function inActiveCompanyContext(?int $companyId): bool
    {
        if (! $companyId) {
            return false;
        }

        return in_array(
            $companyId,
            array_map('intval', app(CompanyContext::class)->activeIds()),
            true,
        );
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

        if (! Schema::hasTable('attendances_company_settings')) {
            return $this->enabled = [];
        }

        return $this->enabled = CompanySetting::withoutGlobalScope(CompanyScope::class)
            ->pluck('is_enabled', 'company_id')
            ->map(fn ($value): bool => (bool) $value)
            ->all();
    }
}
