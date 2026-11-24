<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * Per-company accounting switches, one row per company (plan #10 MC-2,
 * 2026-11-24). Read through forCompany(), never through the scoped model: a
 * company's switch must be found whoever is acting.
 */
class CompanySetting extends Model
{
    use BelongsToCompany;

    protected $table = 'accounts_company_settings';

    /**
     * Mirrors the column defaults, so an unsaved row from forCompany() reads
     * the same as a saved one (the users.is_active trap).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'ask_sale_currency' => false,
    ];

    protected $fillable = [
        'company_id',
        'ask_sale_currency',
    ];

    protected function casts(): array
    {
        return [
            'ask_sale_currency' => 'boolean',
        ];
    }

    /**
     * The company's row, or an unsaved one with the defaults.
     */
    public static function forCompany(int $companyId): self
    {
        return static::withoutGlobalScopes()->where('company_id', $companyId)->first()
            ?? new static(['company_id' => $companyId]);
    }
}
