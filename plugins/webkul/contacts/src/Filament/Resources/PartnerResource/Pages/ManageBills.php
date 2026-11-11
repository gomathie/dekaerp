<?php

namespace Webkul\Contact\Filament\Resources\PartnerResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManageBills as BaseManageBills;

/** The vendor bills tab on the Contacts screen (unified contacts, 2026-11-11). */
class ManageBills extends BaseManageBills
{
    protected static string $resource = PartnerResource::class;
}
