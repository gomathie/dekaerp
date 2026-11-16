<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

/**
 * One line of an imported bank statement (accounting plan A3).
 *
 * The line takes its statement's company (InheritsParentCompany), never the
 * session's: an import run by a Multi-Company Admin must not file one
 * tenant's bank lines under another. `move_id` is the bank entry posted when
 * the line was reconciled, `payment_id` the payment it settled, if any.
 */
class BankStatementLine extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use InheritsParentCompany;

    protected static string $parentCompanyModel = BankStatement::class;

    protected static string $parentCompanyKey = 'statement_id';

    protected $table = 'accounts_bank_statement_lines';

    protected $fillable = [
        'sort',
        'statement_id',
        'journal_id',
        'company_id',
        'date',
        'partner_id',
        'currency_id',
        'partner_name',
        'payment_reference',
        'transaction_type',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'date'          => 'date',
            'amount'        => 'decimal:4',
            'is_reconciled' => 'boolean',
        ];
    }

    public function statement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class, 'statement_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'move_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $line): void {
            $line->creator_id ??= Auth::id();
        });
    }
}
