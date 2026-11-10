<?php

namespace Webkul\PlatformBilling\Services;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\OverdueExtension;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;

/**
 * Moves the read-only instant by recording days on an overdue invoice.
 * isReadOnly() is the only clock. There is no screen in this package.
 */
class OverdueExtensions
{
    public const COMPANY_ADMIN_DAYS = 10;

    public const ONLY_COMPANY_ADMIN = 'Only a Company Admin of this company may extend the overdue invoice.';

    public const ONLY_SUPER_ADMIN = 'Only a Super Admin may grant a manual overdue extension.';

    public const ALREADY_COMPANY_ADMIN = 'This invoice already has its company-admin extension.';

    public const ALREADY_SUPER_ADMIN = 'This invoice already has its super-admin extension.';

    public const NOT_OVERDUE = 'This invoice is not overdue.';

    public const LENGTH_REQUIRED = 'A manual extension needs a number of days, weeks, or months.';

    public const UNIT_REQUIRED = 'A manual extension needs a length in days, weeks, or months.';

    public const REASON_REQUIRED = 'A manual extension needs a reason.';

    public function extendAsCompanyAdmin(User $actor, Invoice $invoice, DateTimeImmutable $at): OverdueExtension
    {
        $invoice = $this->overdueInvoice($invoice, $at);

        if (! $actor->is_active || ! $actor->isCompanyAdmin((int) $invoice->company_id)) {
            throw new AuthorizationException(self::ONLY_COMPANY_ADMIN);
        }

        return $this->grant(
            $actor,
            $invoice,
            OverdueExtension::KIND_COMPANY_ADMIN,
            self::COMPANY_ADMIN_DAYS,
            OverdueExtension::UNIT_DAY,
            null,
            $at,
            self::ALREADY_COMPANY_ADMIN,
        );
    }

    /**
     * Separate from the company-admin slot. The Super Admin enters the
     * length and chooses days, weeks, or months (user, 2026-11-10). One
     * grant per invoice.
     */
    public function extendAsSuperAdmin(User $actor, Invoice $invoice, int $length, string $unit, string $reason, DateTimeImmutable $at): OverdueExtension
    {
        $invoice = $this->overdueInvoice($invoice, $at);

        if (! $actor->is_active || ! $actor->isSuperAdmin()) {
            throw new AuthorizationException(self::ONLY_SUPER_ADMIN);
        }

        if ($length < 1) {
            throw ValidationException::withMessages(['length' => self::LENGTH_REQUIRED]);
        }

        $unit = $this->normalizeUnit($unit);
        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => self::REASON_REQUIRED]);
        }

        return $this->grant(
            $actor,
            $invoice,
            OverdueExtension::KIND_SUPER_ADMIN,
            $length,
            $unit,
            $reason,
            $at,
            self::ALREADY_SUPER_ADMIN,
        );
    }

    protected function overdueInvoice(Invoice $invoice, DateTimeImmutable $at): Invoice
    {
        $locked = Invoice::withoutGlobalScopes()->findOrFail($invoice->getKey());
        $grace = $locked->grace_starts_at;

        if (! $grace instanceof DateTimeInterface || $grace > $at
            || app(PlatformPayments::class)->invoiceBalanceMinor($locked) <= 0) {
            throw ValidationException::withMessages(['invoice' => self::NOT_OVERDUE]);
        }

        return $locked;
    }

    protected function grant(
        User $actor,
        Invoice $invoice,
        string $kind,
        int $length,
        string $unit,
        ?string $reason,
        DateTimeImmutable $at,
        string $alreadyGranted,
    ): OverdueExtension {
        // The unique key is the backstop. This check is what the caller sees,
        // so a second grant is the pinned message rather than a database error.
        if (OverdueExtension::withoutGlobalScopes()->where('invoice_id', $invoice->getKey())->where('kind', $kind)->exists()) {
            throw ValidationException::withMessages(['invoice' => $alreadyGranted]);
        }

        try {
            $extension = OverdueExtension::query()->create([
                'company_id' => $invoice->company_id,
                'invoice_id' => $invoice->getKey(),
                'kind'       => $kind,
                'length'     => $length,
                'unit'       => $unit,
                'reason'     => $reason,
                'granted_by' => $actor->getKey(),
                'granted_at' => $at->setTimezone(new DateTimeZone('UTC')),
            ]);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['invoice' => $alreadyGranted]);
        }

        app(SecurityAuditLogger::class)->record(
            'platform_billing.overdue_extended',
            $extension,
            $actor,
            (int) $invoice->company_id,
            after: ['invoice_id' => $invoice->getKey(), 'kind' => $kind, 'length' => $length, 'unit' => $unit],
        );

        return $extension;
    }

    protected function normalizeUnit(string $unit): string
    {
        $unit = match (strtolower(trim($unit))) {
            'day', 'days'     => OverdueExtension::UNIT_DAY,
            'week', 'weeks'   => OverdueExtension::UNIT_WEEK,
            'month', 'months' => OverdueExtension::UNIT_MONTH,
            default           => '',
        };

        if ($unit === '') {
            throw ValidationException::withMessages(['unit' => self::UNIT_REQUIRED]);
        }

        return $unit;
    }
}
