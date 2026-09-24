<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;

/**
 * Why a set of approved expenses cannot become vendor bills yet.
 *
 * Each reason names the thing to go and fix, because every one of them is a
 * setup problem someone can resolve in a minute - an employee with no contact,
 * a company with no purchase journal - and a bare "posting failed" would send
 * them looking through accounting instead.
 */
class CannotPostBill extends RuntimeException
{
    public static function noExpenses(string $shipment): self
    {
        return new self(__('logistics::exceptions.nothing-to-bill', [
            'shipment' => $shipment,
        ]));
    }

    public static function missingPartner(string $expense): self
    {
        return new self(__('logistics::exceptions.missing-partner', [
            'expense' => $expense,
        ]));
    }

    public static function missingEmployeeContact(string $employee): self
    {
        return new self(__('logistics::exceptions.missing-employee-contact', [
            'employee' => $employee,
        ]));
    }

    public static function missingJournal(): self
    {
        return new self(__('logistics::exceptions.missing-bill-journal'));
    }

    public static function missingExpenseAccount(): self
    {
        return new self(__('logistics::exceptions.missing-expense-account'));
    }
}
