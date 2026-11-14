<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Security\Traits\HasOwnershipScope;
use Webkul\Support\Database\Factories\CompanyFactory;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Traits\RestrictToAllowedCompanies;

class Company extends Model implements Sortable
{
    use HasChatter, HasCustomFields, HasFactory, HasOwnershipScope, RestrictToAllowedCompanies, SoftDeletes, SortableTrait;

    protected static function ownershipScopeIsGlobal(): bool
    {
        return false;
    }

    protected $fillable = [
        'sort',
        'name',
        'company_id',
        'parent_id',
        'tax_id',
        'registration_number',
        'email',
        'phone',
        'mobile',
        'street1',
        'street2',
        'city',
        'zip',
        'state_id',
        'country_id',
        'logo',
        'color',
        'is_active',
        'founded_date',
        'creator_id',
        'currency_id',
        'partner_id',
        'website',
        'account_type',
        'test_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'test_ends_at' => 'immutable_datetime',
        ];
    }

    /**
     * A created observer can refuse a creator membership after INSERT. Wrap
     * every direct create so that refusal also removes its company and partner;
     * the security onboarding coordinator adds administrator work in its own
     * outer transaction (D-SAAS-03).
     */
    public function save(array $options = []): bool
    {
        if ($this->exists) {
            return parent::save($options);
        }

        return DB::transaction(fn (): bool => parent::save($options));
    }

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isBranch(): bool
    {
        return ! is_null($this->parent_id);
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Extra currencies this company may transact in (CR-6). Empty means every
     * active currency; see Currency::scopePermittedFor().
     */
    public function permittedCurrencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class, 'company_currencies');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id')
            ->withoutGlobalScope(CompanyScope::class);
    }

    public function parents()
    {
        $parents = collect();

        $current = $this->parent;

        while ($current) {
            $parents->push($current);

            $current = $current->parent;
        }

        return $parents;
    }

    public function getParentsAttribute()
    {
        return $this->parents();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function newFactory(): CompanyFactory
    {
        return CompanyFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::forceDeleting(function ($company) {
            if (! Schema::hasTable('sequences')) {
                return;
            }

            Sequence::withoutGlobalScope(CompanyScope::class)->where('company_id', $company->id)->get()->each(function (Sequence $sequence) {
                try {
                    $sequence->update(['company_id' => null]);
                } catch (UniqueConstraintViolationException) {
                    $sequence->delete();
                }
            });
        });

        static::creating(function ($company) {
            $company->creator_id ??= Auth::id();

            if (! $company->partner_id) {
                $partner = Partner::create([
                    'creator_id'       => $company->creator_id ?? Auth::id(),
                    'sub_type'         => 'company',
                    'company_registry' => $company->registration_number,
                    'name'             => $company->name,
                    'email'            => $company->email,
                    'website'          => $company->website,
                    'tax_id'           => $company->tax_id,
                    'phone'            => $company->phone,
                    'mobile'           => $company->mobile,
                    'color'            => $company->color,
                    'street1'          => $company->street1,
                    'street2'          => $company->street2,
                    'city'             => $company->city,
                    'zip'              => $company->zip,
                    'state_id'         => $company->state_id,
                    'country_id'       => $company->country_id,
                    'parent_id'        => $company->parent_id,
                    'company_id'       => $company->id,
                ]);

                $company->partner_id = $partner->id;
            }
        });

        static::saving(function ($company) {
            $company->currency?->update([
                'active' => true,
            ]);
        });

        static::created(function ($company) {
            if (! $company->creator_id) {
                return;
            }

            $creator = User::find($company->creator_id);

            if (! $creator instanceof User) {
                return;
            }

            // During the installer, the seat and invitation migrations may not
            // exist yet. Once they do, every programmatic company create uses
            // the same lock and reservation rule as the Users page.
            if (! Schema::hasColumn('companies', 'seat_limit') || ! Schema::hasTable('user_invitations')) {
                $creator->allowedCompanies()->syncWithoutDetaching([$company->id]);

                return;
            }

            $capacity = app(SeatCapacityService::class);

            $capacity->withLockedCompany($company, function () use ($creator, $company, $capacity): void {
                // Installation staff and inactive users do not consume seats
                // (D-SAAS-03). A tenant creator does, including while an invite
                // is pending, so check before attaching their membership.
                if ($creator->is_active && ! $creator->isSuperAdmin() && ! $creator->isMultiCompanyAdmin()) {
                    $capacity->assertCanAttachUser($creator, (int) $company->id);
                }

                $creator->allowedCompanies()->syncWithoutDetaching([$company->id]);
            });
        });

        static::saved(function ($company) {
            Partner::withoutGlobalScopes()->updateOrCreate(
                [
                    'id' => $company->partner_id,
                ],
                [
                    'sub_type'         => 'company',
                    'company_registry' => $company->registration_number,
                    'name'             => $company->name,
                    'email'            => $company->email,
                    'website'          => $company->website,
                    'tax_id'           => $company->tax_id,
                    'phone'            => $company->phone,
                    'mobile'           => $company->mobile,
                    'color'            => $company->color,
                    'street1'          => $company->street1,
                    'street2'          => $company->street2,
                    'city'             => $company->city,
                    'zip'              => $company->zip,
                    'state_id'         => $company->state_id,
                    'country_id'       => $company->country_id,
                    'parent_id'        => $company->parent_id,
                    'company_id'       => $company->id,
                ]
            );
        });
    }
}
