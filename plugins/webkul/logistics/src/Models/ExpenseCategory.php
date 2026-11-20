<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Database\Factories\ExpenseCategoryFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * Shared configuration (see ServiceType). is_subcontracting marks carrier and
 * other vendor costs (D2).
 */
class ExpenseCategory extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'logistics_expense_categories';

    protected $fillable = [
        'name',
        'code',
        'requires_receipt',
        'is_subcontracting',
        'is_active',
        'sort',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'requires_receipt'  => 'boolean',
        'is_subcontracting' => 'boolean',
        'is_active'         => 'boolean',
        'sort'              => 'integer',
    ];

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): ExpenseCategoryFactory
    {
        return ExpenseCategoryFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $record): void {
            $record->creator_id ??= Auth::id();
        });
    }
}
