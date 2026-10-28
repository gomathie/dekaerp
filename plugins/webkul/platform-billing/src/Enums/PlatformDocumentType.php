<?php

namespace Webkul\PlatformBilling\Enums;

/**
 * The platform documents DEKA issues (D-SAAS-05).
 *
 * TitleCase keys per project convention. The value is the key used in config and
 * in stored rows, so it must never be renamed without a migration.
 */
enum PlatformDocumentType: string
{
    case Invoice = 'invoice';

    case CreditNote = 'credit_note';

    /**
     * The prefix comes from configuration so an installation can re-brand
     * without a code change; the fallback keeps numbering working if the config
     * file is missing.
     */
    public function prefix(): string
    {
        return (string) config(
            'platform-billing.document_prefixes.'.$this->value,
            $this->defaultPrefix(),
        );
    }

    public function defaultPrefix(): string
    {
        return match ($this) {
            self::Invoice    => 'DEKA-INV',
            self::CreditNote => 'DEKA-CN',
        };
    }
}
