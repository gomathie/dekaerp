<?php

namespace Webkul\Security\Events;

use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * A company and its first administrator were just created, and the onboarding
 * transaction is still open.
 *
 * Security is the core plugin and must not reference optional ones, so other
 * plugins finish their part of onboarding here (CR-4: platform-billing starts
 * the plan chosen on the form). A listener that throws rolls the whole company
 * back with it: no company is left created without what the form promised.
 *
 * `$options` is the create form's state; each listener reads only its own keys.
 */
class CompanyOnboarded
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        public Company $company,
        public User $actor,
        public array $options = [],
    ) {}
}
