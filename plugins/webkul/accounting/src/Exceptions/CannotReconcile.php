<?php

namespace Webkul\Accounting\Exceptions;

use RuntimeException;

/**
 * Why a bank statement line could not be imported, matched or booked
 * (accounting plan A3). One constructor per reason so tests pin the message.
 */
class CannotReconcile extends RuntimeException
{
    public static function notAllowed(): self
    {
        return new self(__('accounting::bank-reconciliation.errors.not-allowed'));
    }

    public static function notABankJournal(): self
    {
        return new self(__('accounting::bank-reconciliation.errors.not-a-bank-journal'));
    }

    public static function foreignCurrency(): self
    {
        return new self(__('accounting::bank-reconciliation.errors.foreign-currency'));
    }

    public static function missingBankAccount(): self
    {
        return new self(__('accounting::bank-reconciliation.errors.missing-bank-account'));
    }

    public static function emptyFile(): self
    {
        return new self(__('accounting::bank-reconciliation.errors.empty-file'));
    }

    public static function tooManyRows(int $max): self
    {
        return new self(__('accounting::bank-reconciliation.errors.too-many-rows', ['max' => $max]));
    }

    public static function badRow(int $row, string $field): self
    {
        return new self(__('accounting::bank-reconciliation.errors.bad-row', ['row' => $row, 'field' => $field]));
    }

    public static function alreadyReconciled(): self
    {
        return new self(__('accounting::bank-reconciliation.errors.already-reconciled'));
    }

    public static function paymentDoesNotMatch(): self
    {
        return new self(__('accounting::bank-reconciliation.errors.payment-does-not-match'));
    }

    public static function accountNotAllowed(): self
    {
        return new self(__('accounting::bank-reconciliation.errors.account-not-allowed'));
    }
}
