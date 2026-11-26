<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Account\Database\Factories\ExpenseFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * A cost paid straight from a bank or cash journal (plan #10 Z1).
 *
 * Written only through Account\Services\ExpenseRecorder, which also posts,
 * rewrites and cancels the entry in move_id. None of company_id, number,
 * move_id, the computed amounts or the rate is fillable: a form or a
 * tampered Livewire payload must not choose which company an expense is
 * filed under, which entry it owns, or what the books say it cost.
 */
class Expense extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'accounts_expenses';

    protected $fillable = [
        'date',
        'currency_id',
        'amount',
        'expense_account_id',
        'journal_id',
        'partner_id',
        'tax_id',
        'reference',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'date'                 => 'date',
            'amount'               => 'decimal:4',
            'currency_rate'        => 'decimal:10',
            'amount_total'         => 'decimal:4',
            'amount_tax'           => 'decimal:4',
            'amount_total_company' => 'decimal:4',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Whether the entry is an expense's own, posted in the journal the
     * expense was paid from (Z2). Read past the global scopes: Move's
     * saving hook asks this for whoever is acting, and a scoped miss would
     * silently move the entry out of its bank journal. Deleted expenses
     * count too: their cancelled entry stays where it was posted.
     */
    public static function ownsMove(Move $move): bool
    {
        if (! $move->exists) {
            return false;
        }

        return static::withoutGlobalScopes()
            ->where('move_id', $move->getKey())
            ->where('journal_id', $move->journal_id)
            ->exists();
    }

    protected static function newFactory(): ExpenseFactory
    {
        return ExpenseFactory::new();
    }
}
