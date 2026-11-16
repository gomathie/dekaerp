<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Support\Models\Company as BaseCompany;

class Company extends BaseCompany
{
    /**
     * The company's members: everyone whose allowed companies include it.
     *
     * Lives on security's subclass, not on support's Company, because `support`
     * is the base plugin and must not reference security's User. Used by the
     * company page's Users list (UsersRelationManager).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_allowed_companies', 'company_id', 'user_id');
    }
}
