<?php

namespace Webkul\Support\Filament\Resources\CompanyResource\Support;

use Webkul\Support\Filament\Contributions\AbstractSchemaRegistry;

/**
 * Slots other plugins can contribute into the company form.
 *
 * Exists because the company form is in `support`, the base plugin, while the
 * things a company needs configured belong to the plugins above it. Plugin
 * Manager contributes the per-company module grants this way, so `support`
 * never has to reference a plugin that depends on it.
 *
 * Mirrors PartnerSchemaRegistry and ProductSchemaRegistry.
 */
class CompanySchemaRegistry extends AbstractSchemaRegistry
{
    protected static function scope(): string
    {
        return 'company';
    }
}
