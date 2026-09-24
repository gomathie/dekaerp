<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Logistics\Enums\CapacityCheck;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * Per-company Logistics switch and settings (one row per company).
 */
class CompanySetting extends Model
{
    use BelongsToCompany;

    protected $table = 'logistics_company_settings';

    /**
     * Mirrors the column defaults in the company settings migrations.
     *
     * forCompany() hands back an unsaved instance for a company that has no row
     * yet, and without these every default-bearing column on it reads as null:
     * stop_link_ttl_hours would be 0 rather than 24, and require_pod_for_delivery
     * would be falsy although the schema defaults it to true - quietly dropping
     * a delivery control for any company whose settings were never saved. Same
     * trap as `users.is_active` and `logistics_shipments.state`. Keep this list
     * in step with the migrations.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_enabled'                => false,
        'require_pod_for_delivery'  => true,
        'require_pod_photo'         => false,
        'expense_approval_required' => true,
        'overdue_grace_minutes'     => 30,
        'free_waiting_minutes'      => 60,
        'stop_link_ttl_hours'       => 24,
        'capacity_check'            => 'warn',
        'capture_driver_on_pod'     => false,
        'capture_recipient_id'      => false,
    ];

    protected $fillable = [
        'company_id',
        'is_enabled',
        'enabled_at',
        'disabled_at',
        'enabled_by_id',
        'require_pod_for_delivery',
        'require_pod_photo',
        'expense_approval_required',
        'overdue_grace_minutes',
        'free_waiting_minutes',
        'stop_link_ttl_hours',
        'stop_link_rate_limit',
        'capture_driver_on_pod',
        'capture_recipient_id',
        'capacity_check',
        'default_service_type_id',
        'invoice_journal_id',
        'bill_journal_id',
        'default_expense_account_id',
    ];

    protected $casts = [
        'is_enabled'                => 'boolean',
        'enabled_at'                => 'datetime',
        'disabled_at'               => 'datetime',
        'require_pod_for_delivery'  => 'boolean',
        'require_pod_photo'         => 'boolean',
        'expense_approval_required' => 'boolean',
        'overdue_grace_minutes'     => 'integer',
        'free_waiting_minutes'      => 'integer',
        'stop_link_ttl_hours'       => 'integer',
        'stop_link_rate_limit'      => 'integer',
        'capture_driver_on_pod'     => 'boolean',
        'capture_recipient_id'      => 'boolean',
        'capacity_check'            => CapacityCheck::class,
    ];

    /**
     * The settings row of a given company, whether or not that company is active in
     * the current session. Returns an unsaved default instance when there is none.
     */
    public static function forCompany(int $companyId): self
    {
        return static::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)
            ->first()
            ?? new static(['company_id' => $companyId]);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function enabledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enabled_by_id');
    }

    public function defaultServiceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'default_service_type_id');
    }

    public function invoiceJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'invoice_journal_id');
    }

    public function billJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'bill_journal_id');
    }

    public function defaultExpenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_expense_account_id');
    }
}
