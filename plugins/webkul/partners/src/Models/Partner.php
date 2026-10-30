<?php

namespace Webkul\Partner\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Database\Factories\PartnerFactory;
use Webkul\Partner\Enums\AccountType;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasOwnershipScope;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Concerns\HasContributedAttributes;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;
use Webkul\Support\Traits\BelongsToCompany;

class Partner extends Authenticatable implements FilamentUser
{
    use BelongsToCompany;
    use HasChatter,HasContributedAttributes, HasCustomFields, HasFactory, HasLogActivity, HasOwnershipScope, Notifiable, SoftDeletes;

    public const ACTIVITY_PLAN_PLUGIN = 'partners';

    protected $table = 'partners_partners';

    protected static function ownershipScopeIsGlobal(): bool
    {
        return false;
    }

    protected $fillable = [
        'account_type',
        'sub_type',
        'name',
        'avatar',
        'email',
        'job_title',
        'website',
        'tax_id',
        'phone',
        'mobile',
        'color',
        'company_registry',
        'reference',
        'street1',
        'street2',
        'city',
        'zip',
        'state_id',
        'country_id',
        'parent_id',
        'creator_id',
        'user_id',
        'title_id',
        'company_id',
        'industry_id',
    ];

    protected $casts = [
        'account_type' => AccountType::class,
        'is_active'    => 'boolean',
    ];

    public function getModelTitle(): string
    {
        return 'Partner';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getAvatarUrlAttribute()
    {
        if (! $this->avatar) {
            return;
        }

        return Storage::disk('public')->url($this->avatar);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('account_type', AccountType::ADDRESS);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('account_type', '!=', AccountType::ADDRESS);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'partner_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'partners_partner_tag', 'partner_id', 'tag_id');
    }

    protected static function newFactory(): PartnerFactory
    {
        return PartnerFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($partner) {
            $partner->creator_id ??= Auth::id();
        });
    }

    /*
     * Deliberately no autoAssignsCompany() override, a fork from upstream
     * (57e234223 returned false, which made every new contact shared across
     * companies). Clients are unrelated businesses (user decision,
     * 2026-10-29), so BelongsToCompany stamps the creating company. See
     * docs/company-admin-role-plan.md §4k.
     *
     * Not InheritsParentCompany either. A branch company's own contact
     * legitimately belongs to the branch while its parent contact belongs to
     * the parent company, which that trait refuses. Child contacts get their
     * owner's company where they are created instead: AddressResource,
     * ManageContacts and the address API.
     */

    /**
     * The contacts a document of `$companyId` may be emailed to: that company's
     * own contacts and shared ones (no company). Any other id is refused, not
     * dropped.
     *
     * Why (user decision, 2026-10-30, §4k read audit): clients are unrelated
     * businesses. The email actions used to read recipients through the scoped
     * query. That silently dropped an ordinary user's out-of-company id, and
     * widened for a super admin (who bypasses CompanyScope) or a Multi-Company
     * Admin with several companies active, letting one client's invoice or
     * order reach another client's contact. The record's company decides,
     * whoever is acting.
     *
     * Read with withoutGlobalScopes(): Partner also carries the ownership scope,
     * so dropping only CompanyScope could still hide a valid recipient.
     *
     * @param  array<int, mixed>  $ids
     * @return EloquentCollection<int, static>
     *
     * @throws ValidationException naming the refused ids
     */
    public static function recipientsForCompany(array $ids, ?int $companyId): EloquentCollection
    {
        $ids = array_values(array_unique(array_map('intval', array_filter($ids, 'is_numeric'))));

        $recipients = static::withoutGlobalScopes()
            ->whereKey($ids)
            ->whereNull((new static)->getTable().'.deleted_at')
            ->where(owned_by_company($companyId))
            ->get();

        $refused = array_values(array_diff($ids, array_map('intval', $recipients->modelKeys())));

        if ($refused !== []) {
            throw ValidationException::withMessages([
                'partners' => __('These recipients do not belong to this company and cannot be emailed: :ids.', [
                    'ids' => implode(', ', $refused),
                ]),
            ]);
        }

        return $recipients;
    }
}
