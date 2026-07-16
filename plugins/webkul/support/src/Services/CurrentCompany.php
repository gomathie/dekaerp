<?php

namespace Webkul\Support\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Models\Company;

class CurrentCompany
{
    public const SESSION_KEY = 'active_company_id';

    public const ALL = 'all';

    protected static ?Collection $allowedCompanies = null;

    /**
     * Companies the authenticated user may act in: the allowed-companies
     * pivot plus the default company, in case it is missing from the pivot.
     */
    public static function allowedCompanies(): Collection
    {
        if (static::$allowedCompanies !== null) {
            return static::$allowedCompanies;
        }

        $user = Auth::user();

        if (! $user || ! method_exists($user, 'allowedCompanies')) {
            return static::$allowedCompanies = collect();
        }

        $companies = $user->allowedCompanies()
            ->where('is_active', true)
            ->get();

        if (
            $user->default_company_id
            && ! $companies->contains('id', $user->default_company_id)
            && ($defaultCompany = Company::find($user->default_company_id))
        ) {
            $companies->push($defaultCompany);
        }

        return static::$allowedCompanies = $companies->sortBy('name')->values();
    }

    public static function allowedIds(): array
    {
        return static::allowedCompanies()->pluck('id')->all();
    }

    /**
     * The validated selection: a company id, the ALL sentinel, or null
     * when the user has no companies configured.
     */
    public static function selection(): int|string|null
    {
        $allowedIds = static::allowedIds();

        $selection = session(static::SESSION_KEY);

        if ($selection === static::ALL) {
            return count($allowedIds) > 1 ? static::ALL : ($allowedIds[0] ?? null);
        }

        if ($selection && in_array((int) $selection, $allowedIds)) {
            return (int) $selection;
        }

        $user = Auth::user();

        if (
            $user?->default_company_id
            && (empty($allowedIds) || in_array($user->default_company_id, $allowedIds))
        ) {
            return $user->default_company_id;
        }

        return $allowedIds[0] ?? null;
    }

    public static function isAll(): bool
    {
        return static::selection() === static::ALL;
    }

    /**
     * The single active company id. When "all companies" is selected this
     * falls back to the user's default company, so it is always safe to use
     * for assigning new records.
     */
    public static function id(): ?int
    {
        $selection = static::selection();

        if ($selection === static::ALL) {
            $user = Auth::user();

            return $user?->default_company_id ?? (static::allowedIds()[0] ?? null);
        }

        return $selection;
    }

    public static function company(): ?Company
    {
        return static::allowedCompanies()->firstWhere('id', static::id());
    }

    public static function set(int|string $selection): bool
    {
        if ($selection === static::ALL) {
            session()->put(static::SESSION_KEY, static::ALL);

            return true;
        }

        if (in_array((int) $selection, static::allowedIds())) {
            session()->put(static::SESSION_KEY, (int) $selection);

            return true;
        }

        return false;
    }

    public static function forget(): void
    {
        session()->forget(static::SESSION_KEY);

        static::$allowedCompanies = null;
    }
}
