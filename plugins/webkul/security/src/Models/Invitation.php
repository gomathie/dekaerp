<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\Company;

class Invitation extends Model
{
    use HasFactory;

    protected $table = 'user_invitations';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'company_ids' => 'array',
            'role_ids'    => 'array',
        ];
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function defaultCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'default_company_id');
    }
}
