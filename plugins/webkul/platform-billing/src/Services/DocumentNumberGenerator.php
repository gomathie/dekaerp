<?php

namespace Webkul\PlatformBilling\Services;

use Illuminate\Support\Facades\DB;
use Webkul\PlatformBilling\Enums\PlatformDocumentType;

/**
 * Issues platform document numbers (D-SAAS-05).
 *
 * Format: `{prefix}-{compact year}-{six digits}`, e.g. `DEKA-INV-226-000001`.
 * Each document type has its own sequence per year, numbers are never reused,
 * and the row is locked while a number is handed out - two invoices raised in the
 * same second must not receive the same number.
 *
 * The compact year drops the third digit of a four-digit year (2026 -> 226). It
 * is stable across every year in this century and never collides, which a
 * `%02d`-style two-digit year eventually would.
 */
class DocumentNumberGenerator
{
    public function next(PlatformDocumentType $type, ?int $year = null): string
    {
        $year ??= (int) now()->year;

        $number = DB::transaction(function () use ($type, $year): int {
            $existing = DB::table('platform_billing_document_numbers')
                ->where('document_type', $type->value)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($existing === null) {
                // The unique key is the backstop: if two requests race here, one
                // insert fails and its transaction retries rather than both
                // issuing number 1.
                DB::table('platform_billing_document_numbers')->insert([
                    'document_type' => $type->value,
                    'year'          => $year,
                    'last_number'   => 1,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                return 1;
            }

            $next = (int) $existing->last_number + 1;

            DB::table('platform_billing_document_numbers')
                ->where('id', $existing->id)
                ->update([
                    'last_number' => $next,
                    'updated_at'  => now(),
                ]);

            return $next;
        });

        return $this->format($type, $year, $number);
    }

    public function format(PlatformDocumentType $type, int $year, int $number): string
    {
        return sprintf('%s-%s-%06d', $type->prefix(), $this->compactYear($year), $number);
    }

    /**
     * 2026 -> 226, 2027 -> 227: the third digit (always 0 this century) is
     * dropped. 2030 -> 230 and 2003 -> 203, so no two years share a compact form.
     */
    public function compactYear(int $year): string
    {
        $digits = (string) $year;

        if (strlen($digits) !== 4) {
            return $digits;
        }

        return $digits[0].substr($digits, 2);
    }
}
