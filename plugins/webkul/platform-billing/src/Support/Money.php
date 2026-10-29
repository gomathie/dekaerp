<?php

namespace Webkul\PlatformBilling\Support;

use InvalidArgumentException;
use OverflowException;

/**
 * An amount of money as an integer number of minor units, with its currency.
 *
 * Integer minor units, not floats. `accounts_account_moves.amount_total` is a
 * `decimal(15,4)` and the tenant ledger already drifts where PHP does the
 * arithmetic in floats; platform billing is where a customer is invoiced, so it
 * does not inherit that. 0.1 + 0.2 is 30, not 0.30000000000000004.
 *
 * The currency travels *with* the amount. Adding USD to ZMW is a programming
 * error, never an implicit conversion: D-SAAS-01 defers multi-currency to later
 * and forbids implicit FX.
 *
 * Immutable: every operation returns a new instance, so a value cannot be
 * changed after it has been written to a document.
 */
final class Money
{
    private function __construct(
        public readonly int $minor,
        public readonly string $currency,
    ) {}

    public static function of(int $minor, string $currency): self
    {
        return new self($minor, self::normalizeCurrency($currency));
    }

    public static function zero(string $currency): self
    {
        return self::of(0, $currency);
    }

    /**
     * Build from the decimal string a human or a CSV provides.
     *
     * Deliberately a `string`: a float parameter would accept `0.1 + 0.2` and
     * quietly store the drift, which is the exact failure this class exists to
     * prevent. Scale defaults to 2 (USD cents per D-SAAS-01).
     */
    public static function fromDecimal(string|int $amount, string $currency, int $scale = 2): self
    {
        if ($scale < 0 || $scale > 4) {
            throw new InvalidArgumentException("Unsupported money scale [{$scale}].");
        }

        $amount = trim((string) $amount);

        if (preg_match('/^(-)?(\d+)(?:\.(\d+))?$/', $amount, $matches) !== 1) {
            throw new InvalidArgumentException("Not a decimal amount: [{$amount}].");
        }

        $negative = ($matches[1] ?? '') === '-';
        $whole = $matches[2];
        $fraction = $matches[3] ?? '';

        if (strlen($fraction) > $scale) {
            throw new InvalidArgumentException(
                "[{$amount}] has more than {$scale} decimal places; round it deliberately before it becomes money."
            );
        }

        $fraction = str_pad($fraction, $scale, '0');

        $minor = $whole.($scale > 0 ? $fraction : '');

        self::assertFitsInteger($minor, $amount);

        $value = (int) $minor;

        return new self($negative ? -$value : $value, self::normalizeCurrency($currency));
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self(self::guardOverflow($this->minor + $other->minor), $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self(self::guardOverflow($this->minor - $other->minor), $this->currency);
    }

    public function multiply(int $factor): self
    {
        if ($factor !== 0 && abs($this->minor) > intdiv(PHP_INT_MAX, abs($factor))) {
            throw new OverflowException("Multiplying {$this->minor} by {$factor} would overflow.");
        }

        return new self($this->minor * $factor, $this->currency);
    }

    public function negate(): self
    {
        return new self(self::guardOverflow(-$this->minor), $this->currency);
    }

    public function isZero(): bool
    {
        return $this->minor === 0;
    }

    public function isNegative(): bool
    {
        return $this->minor < 0;
    }

    public function equals(self $other): bool
    {
        return $this->minor === $other->minor && $this->currency === $other->currency;
    }

    /**
     * The decimal string to store or print. Never used for arithmetic.
     */
    public function toDecimal(int $scale = 2): string
    {
        if ($scale === 0) {
            return (string) $this->minor;
        }

        $absolute = abs($this->minor);
        $divisor = 10 ** $scale;

        $whole = intdiv($absolute, $divisor);
        $fraction = str_pad((string) ($absolute % $divisor), $scale, '0', STR_PAD_LEFT);

        return ($this->minor < 0 ? '-' : '').$whole.'.'.$fraction;
    }

    /**
     * Tax applied to an amount, at a rate expressed as a decimal string so the
     * rate itself cannot drift. Rounded half-up to the minor unit, once.
     */
    public function percentage(string $rate): self
    {
        if (preg_match('/^-?\d+(?:\.\d{1,4})?$/', trim($rate)) !== 1) {
            throw new InvalidArgumentException("Not a percentage rate: [{$rate}].");
        }

        // The rate is parsed from its digits, never through a float, so "15.0001"
        // is exactly 150001 ten-thousandths of a percent.
        $rate = trim($rate);
        [$whole, $fraction] = array_pad(explode('.', ltrim($rate, '-'), 2), 2, '');
        $scaledRate = ((int) $whole * 10000) + (int) str_pad($fraction, 4, '0');

        if (str_starts_with($rate, '-')) {
            $scaledRate = -$scaledRate;
        }

        if ($scaledRate !== 0 && abs($this->minor) > intdiv(PHP_INT_MAX, abs($scaledRate))) {
            throw new OverflowException("Applying {$rate}% to {$this->minor} would overflow.");
        }

        $product = $this->minor * $scaledRate;

        // One divisor for both scales: 10,000 undoes the four rate decimals and
        // 100 turns a percent into a fraction. Dividing by 10,000 alone returned
        // the amount times the rate - 15% of 100.00 came out as 1,500.00.
        $divisor = 100 * 10000;

        $rounded = intdiv($product + ($product >= 0 ? intdiv($divisor, 2) : -intdiv($divisor, 2)), $divisor);

        return new self($rounded, $this->currency);
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Cannot combine {$this->currency} with {$other->currency}: platform billing has no implicit FX."
            );
        }
    }

    private static function guardOverflow(int|float $value): int
    {
        if (! is_int($value)) {
            throw new OverflowException('The amount overflows an integer minor-unit value.');
        }

        return $value;
    }

    /**
     * A PHP int cast saturates rather than failing, so the digits are compared
     * as strings first - otherwise "9223372036854775808" would quietly become
     * PHP_INT_MAX and be invoiced.
     */
    private static function assertFitsInteger(string $digits, string $original): void
    {
        $digits = ltrim($digits, '0');
        $max = (string) PHP_INT_MAX;

        if (strlen($digits) > strlen($max) || (strlen($digits) === strlen($max) && strcmp($digits, $max) > 0)) {
            throw new OverflowException("[{$original}] does not fit in an integer minor-unit amount.");
        }
    }

    private static function normalizeCurrency(string $currency): string
    {
        $currency = mb_strtoupper(trim($currency));

        if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
            throw new InvalidArgumentException("Not a currency code: [{$currency}].");
        }

        return $currency;
    }
}
