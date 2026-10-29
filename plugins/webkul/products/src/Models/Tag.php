<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Database\Factories\TagFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Tag extends Model
{
    use BelongsToCompany;
    use HasFactory, SoftDeletes;

    protected $table = 'products_tags';

    /*
     * Each client has its own catalogue, so a tag belongs to the company that
     * creates it; a null company would share it with every other client
     * (docs/company-admin-role-plan.md §4k).
     */

    protected $fillable = [
        'name',
        'color',
        'company_id',
        'creator_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            $tag->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
