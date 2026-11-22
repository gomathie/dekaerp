<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\EditCompany as BaseEditCompany;

/**
 * An existing company gets an administrator, or a new password for one,
 * through the Administrator section's own actions (AdministratorPanel, user,
 * 2026-11-22), not on save. *Superseded:* from 2026-11-03 this page's
 * afterSave() read loose email and password fields and invited, created or
 * reset depending on which were filled.
 */
class EditCompany extends BaseEditCompany
{
    protected static string $resource = CompanyResource::class;
}
