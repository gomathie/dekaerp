<?php

use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/SecurityHelper.php';

class CompanyHelper
{
    public static function company(array $overrides = []): Company
    {
        return Company::factory()->create($overrides);
    }

    /**
     * Authenticate a user restricted to the given companies, with the company
     * scope ACTIVE (no bypass). $companies may be a Company, an id, or an array.
     */
    public static function actingAsCompanyUser($companies, array $permissions = [], ?array $activeIds = null): User
    {
        $ids = collect(is_array($companies) ? $companies : [$companies])
            ->map(fn ($company) => $company instanceof Company ? $company->id : $company)
            ->all();

        $user = SecurityHelper::authenticateWithPermissions($permissions, bypassCompanyScope: false);

        $user->forceFill(['default_company_id' => $ids[0] ?? null])->saveQuietly();

        $user->allowedCompanies()->sync($ids);

        session([CompanyContext::SESSION_KEY => $activeIds ?? $ids]);

        app()->forgetInstance(CompanyContext::class);

        return $user;
    }

    /**
     * Run the callback as a console/seeder would: nobody authenticated and no
     * active company, so current_company_id() resolves to null.
     */
    public static function withoutCompanyContext(callable $callback): mixed
    {
        $active = session(CompanyContext::SESSION_KEY);

        session()->forget(CompanyContext::SESSION_KEY);

        app()->instance(CompanyContext::class, new class extends CompanyContext
        {
            public function activeIds(): array
            {
                return [];
            }

            public function currentId(): ?int
            {
                return null;
            }
        });

        try {
            return $callback();
        } finally {
            if ($active !== null) {
                session([CompanyContext::SESSION_KEY => $active]);
            }

            app()->forgetInstance(CompanyContext::class);
        }
    }

    /**
     * Write fixture rows the way a seeder or job does: with nobody signed in.
     *
     * Since UF-025 (2026-11-16) a signed-in user may not write into a company
     * they are not a member of, and a job or seeder with no acting user is
     * exempt. A fixture that seeds another company's rows - or a project owned
     * by a user from outside the company - belongs on that exempt path, not
     * under whoever the test signed in last. The previous user is restored.
     */
    public static function asNobody(callable $callback): mixed
    {
        // Both guards: SecurityHelper makes Sanctum the default, and Sanctum's
        // guard resolves its user through the web session guard, which in turn
        // re-reads the login id from the session. Clearing only the default
        // guard therefore signed the user straight back in.
        $web = auth()->guard('web');
        $sanctum = auth()->guard('sanctum');
        $previousWeb = $web->user();
        $previousSanctum = $sanctum->user();

        $sessionKey = $web->getName();
        $loginId = session()->pull($sessionKey);

        $web->forgetUser();
        $sanctum->forgetUser();

        try {
            return $callback();
        } finally {
            if ($loginId !== null) {
                session([$sessionKey => $loginId]);
            }

            if ($previousWeb !== null) {
                $web->setUser($previousWeb);
            }

            if ($previousSanctum !== null) {
                $sanctum->setUser($previousSanctum);
            }
        }
    }

    public static function setActive(array $ids): void
    {
        session([CompanyContext::SESSION_KEY => $ids]);

        app()->forgetInstance(CompanyContext::class);
    }
}
