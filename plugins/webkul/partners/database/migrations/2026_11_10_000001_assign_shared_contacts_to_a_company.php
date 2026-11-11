<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Contacts with no company were shared, so they appeared in every
     * company and isolation looked broken (user, 2026-11-10). This
     * supersedes "existing contacts stay shared" (2026-10-29, §4k).
     *
     * Each shared contact goes to the one company whose documents use it,
     * because moving it anywhere else would silently drop it from that
     * company's own orders and invoices (a scoped relation reads null).
     * A contact no company's documents use goes to the default company.
     * A contact two companies use stays shared and is logged: no choice is
     * safe for it.
     */
    public function up(): void
    {
        if (! Schema::hasTable('partners_partners')) {
            return;
        }

        $defaultCompanyId = $this->defaultCompanyId();

        if ($defaultCompanyId === null) {
            return;
        }

        $sharedIds = DB::table('partners_partners')->whereNull('company_id')->pluck('id');

        foreach ($sharedIds as $partnerId) {
            $companies = $this->companiesUsing((int) $partnerId);

            if (count($companies) > 1) {
                Log::warning('Shared contact left shared: used by several companies.', [
                    'partner_id' => $partnerId,
                    'companies'  => $companies,
                ]);

                continue;
            }

            DB::table('partners_partners')
                ->where('id', $partnerId)
                ->whereNull('company_id')
                ->update(['company_id' => $companies[0] ?? $defaultCompanyId]);
        }
    }

    /**
     * Not reversible: which contacts were shared is not recorded, and making
     * every contact shared again would be wrong.
     */
    public function down(): void
    {
        //
    }

    /**
     * @return list<int>
     */
    protected function companiesUsing(int $partnerId): array
    {
        $companies = collect();

        foreach (['sales_orders', 'purchases_orders', 'accounts_account_moves'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $companies = $companies->merge(
                DB::table($table)->where('partner_id', $partnerId)->whereNotNull('company_id')->distinct()->pluck('company_id')
            );
        }

        return $companies->map(fn ($id): int => (int) $id)->unique()->values()->all();
    }

    protected function defaultCompanyId(): ?int
    {
        if (Schema::hasTable('settings')) {
            $payload = DB::table('settings')
                ->where('group', 'general')
                ->where('name', 'default_company_id')
                ->value('payload');

            $decoded = $payload !== null ? json_decode((string) $payload, true) : null;

            if (is_int($decoded) && $decoded > 0 && DB::table('companies')->where('id', $decoded)->exists()) {
                return $decoded;
            }
        }

        $id = DB::table('companies')->whereNull('deleted_at')->min('id');

        return $id === null ? null : (int) $id;
    }
};
