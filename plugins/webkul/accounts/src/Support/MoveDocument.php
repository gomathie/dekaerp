<?php

namespace Webkul\Account\Support;

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Tax;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Support\Support\PrintedDocument;

/**
 * What a printed invoice, bill, credit note or refund shows, for the shared
 * `support::pdfs.document` layout (TRACE365, 2026-11-23, plan #10 R-8).
 *
 * Fixes the old templates, which printed only the product name (no line
 * amount), one "Tax" line for NHIL, GETFund and VAT together, a "Discount"
 * that summed the lines' discount *percentages* as money, raw timestamps,
 * and no balance due.
 */
class MoveDocument
{
    /**
     * @return array<string, mixed>
     */
    public static function data(Move $move): array
    {
        $move->loadMissing(['company.partner.country', 'company.partner.state', 'partner.country', 'partner.state', 'currency', 'invoicePaymentTerm', 'partnerBank.bank', 'invoiceLines.product', 'invoiceLines.uom', 'taxLines']);

        $currency = $move->currency?->name;
        $money = fn (float|int|string|null $amount): string => money((float) $amount, $currency);
        $isSale = in_array($move->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_REFUND, MoveType::OUT_RECEIPT], true);
        $isPosted = $move->state === MoveState::POSTED;

        $lines = $move->invoiceLines->map(function ($line) use ($money): array {
            $product = $line->product?->name;
            $label = trim((string) $line->name);

            return [
                'name'        => $product ?: ($label !== '' ? $label : '—'),
                // The line's own text when it says more than the product name.
                'description' => $product && $label !== '' && $label !== $product ? $label : null,
                'quantity'    => PrintedDocument::quantity($line->quantity),
                'unit'        => static::showsUnits() ? $line->uom?->name : null,
                'rate'        => $money($line->price_unit),
                'discount'    => (float) $line->discount > 0 ? PrintedDocument::quantity($line->discount).'%' : '',
                'amount'      => $money($line->price_subtotal),
            ];
        })->values()->all();

        $totals = [['label' => __('support::documents.subtotal'), 'value' => $money($move->amount_untaxed)]];

        foreach (static::taxRows($move) as $name => $amount) {
            $totals[] = ['label' => $name, 'value' => $money($amount)];
        }

        $totals[] = ['label' => __('support::documents.total'), 'value' => $money($move->amount_total), 'class' => 'total'];

        $residual = (float) $move->amount_residual;
        $paid = max(0, (float) $move->amount_total - $residual);

        if ($isPosted && $paid > 0.004) {
            $totals[] = ['label' => __('support::documents.amount-paid'), 'value' => $money(-$paid)];
        }

        if ($isPosted) {
            $totals[] = ['label' => __('support::documents.balance-due'), 'value' => $money($residual), 'class' => 'balance'];
        }

        return [
            'title'         => __('support::documents.titles.'.static::kind($move)),
            'number'        => $move->name ?: __('support::documents.draft'),
            'status'        => static::status($move),
            'accent'        => PrintedDocument::accent($move->company),
            'company'       => PrintedDocument::company($move->company),
            'party_label'   => __($isSale ? 'support::documents.bill-to' : 'support::documents.vendor'),
            'party'         => PrintedDocument::party($move->partner),
            'headline'      => $isPosted
                ? ['label' => __('support::documents.balance-due'), 'value' => $money($residual)]
                : ['label' => __('support::documents.total'), 'value' => $money($move->amount_total)],
            'meta'          => array_values(array_filter([
                [__('support::documents.date'), $move->invoice_date?->translatedFormat('d M Y')],
                [__('support::documents.payment-terms'), $move->invoicePaymentTerm?->name],
                [__('support::documents.due-date'), $move->invoice_date_due?->translatedFormat('d M Y')],
                [__('support::documents.reference'), $move->ref],
            ], fn (array $row): bool => filled($row[1]))),
            'show_discount' => collect($lines)->contains(fn (array $line): bool => $line['discount'] !== ''),
            'lines'         => $lines,
            'totals'        => $totals,
            'payment'       => static::paymentLines($move),
            'notes'         => $move->narration,
            'terms'         => null,
        ];
    }

    /** invoice, bill, credit-note or refund. */
    public static function kind(Move $move): string
    {
        return match ($move->move_type) {
            MoveType::IN_INVOICE, MoveType::IN_RECEIPT => 'bill',
            MoveType::OUT_REFUND => 'credit-note',
            MoveType::IN_REFUND  => 'refund',
            default              => 'invoice',
        };
    }

    /**
     * One row per tax (NHIL/GETFund, VAT...), from the posted tax lines in the
     * document's currency, signed so the document shows them as positive.
     *
     * @return array<string, float>
     */
    protected static function taxRows(Move $move): array
    {
        $byTax = $move->taxLines->groupBy(fn ($line) => $line->tax_line_id ?: 'line-'.$line->getKey());

        $names = Tax::withoutGlobalScopes()
            ->whereIn('id', $move->taxLines->pluck('tax_line_id')->filter()->unique()->all())
            ->pluck('name', 'id');

        $rows = [];

        foreach ($byTax as $taxId => $lines) {
            $name = (string) ($names[$taxId] ?? $lines->first()->name ?? __('support::documents.tax'));
            $rows[$name] = ($rows[$name] ?? 0) + abs((float) $lines->sum('amount_currency'));
        }

        return $rows;
    }

    /** @return list<string> */
    protected static function paymentLines(Move $move): array
    {
        if ($move->state !== MoveState::POSTED) {
            return [];
        }

        $lines = [__('support::documents.payment-reference', ['reference' => $move->payment_reference ?: $move->name])];

        $bank = $move->partnerBank;

        if ($bank) {
            $lines[] = collect([
                $bank->bank?->name,
                $bank->account_holder_name,
                $bank->account_number,
            ])->filter(fn ($part): bool => filled($part))->implode(' · ');
        }

        return array_values(array_filter($lines, fn ($line): bool => filled($line)));
    }

    protected static function status(Move $move): ?string
    {
        if ($move->state === MoveState::DRAFT) {
            return __('support::documents.statuses.draft');
        }

        if ($move->state === MoveState::CANCEL) {
            return __('support::documents.statuses.cancelled');
        }

        return match ($move->payment_state) {
            PaymentState::PAID, PaymentState::IN_PAYMENT => __('support::documents.statuses.paid'),
            PaymentState::PARTIAL                        => __('support::documents.statuses.partly-paid'),
            default                                      => null,
        };
    }

    protected static function showsUnits(): bool
    {
        return (bool) settings(ProductSettings::class)->enable_uom;
    }
}
