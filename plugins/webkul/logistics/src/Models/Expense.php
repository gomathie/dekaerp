<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\Move;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Logistics\Database\Factories\ExpenseFactory;
use Webkul\Logistics\Enums\ExpensePaidBy;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Exceptions\CompanyMismatchException;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * An operational or vendor cost of a shipment, trip or vehicle. Subcontractor
 * and carrier costs are expenses too (D2). Approved expenses become draft vendor
 * bills (WP-8b); this is not a ledger.
 */
class Expense extends Model
{
    use BelongsToCompany;
    use HasChatter, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'logistics_expenses';

    /**
     * Mirrors the column defaults in the expenses migration. See Trip for why.
     *
     * `paid_by` is also set by the creating hook below; declared here as well so
     * the value is right on the instance before it is saved, not only after.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'paid_by' => 'company',
        'state'   => 'draft',
    ];

    protected $fillable = [
        'date',
        'amount',
        'paid_by',
        'vendor_reference',
        'description',
        'receipt_path',
        'state',
        'approved_at',
        'category_id',
        'shipment_id',
        'trip_id',
        'vehicle_id',
        'employee_id',
        'payee_id',
        'currency_id',
        'approved_by_id',
        'bill_move_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'date'        => 'date',
        'amount'      => 'decimal:4',
        'paid_by'     => ExpensePaidBy::class,
        'state'       => ExpenseState::class,
        'approved_at' => 'datetime',
    ];

    /**
     * Links whose company must match the expense's company.
     *
     * @var array<string, class-string<Model>>
     */
    protected const COMPANY_LINKS = [
        'shipment_id' => Shipment::class,
        'trip_id'     => Trip::class,
        'vehicle_id'  => Vehicle::class,
    ];

    public function getModelTitle(): string
    {
        return __('logistics::models/expense.title');
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'state'         => __('logistics::models/expense.log-attributes.state'),
            'amount'        => __('logistics::models/expense.log-attributes.amount'),
            'category.name' => __('logistics::models/expense.log-attributes.category'),
            'payee.name'    => __('logistics::models/expense.log-attributes.payee'),
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'payee_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function billMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'bill_move_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): ExpenseFactory
    {
        return ExpenseFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $expense): void {
            $expense->creator_id ??= Auth::id();
            $expense->state ??= ExpenseState::DRAFT;
            $expense->paid_by ??= ExpensePaidBy::COMPANY;
        });

        // Registered after BelongsToCompany's "creating" listener, so company_id is
        // already set; checked before the row is written.
        static::creating(fn (self $expense) => $expense->ensureLinksShareCompany());

        static::updating(fn (self $expense) => $expense->ensureLinksShareCompany());
    }

    protected function ensureLinksShareCompany(): void
    {
        foreach (self::COMPANY_LINKS as $key => $model) {
            if (! $this->{$key}) {
                continue;
            }

            if ($this->exists && ! $this->isDirty([$key, 'company_id'])) {
                continue;
            }

            $linkedCompanyId = $model::withoutGlobalScope(CompanyScope::class)
                ->withTrashed()
                ->whereKey($this->{$key})
                ->value('company_id');

            if ($linkedCompanyId !== null && (int) $linkedCompanyId !== (int) $this->company_id) {
                throw CompanyMismatchException::between('Expense', class_basename($model));
            }
        }
    }
}
