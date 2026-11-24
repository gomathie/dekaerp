<?php

namespace Webkul\Sale\Support;

use Webkul\Account\Facades\Tax;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Support\Support\PrintedDocument;

/**
 * What a printed quotation or sales order shows, for the shared
 * `support::pdfs.document` layout (TRACE365, 2026-11-23, plan #9 R-8).
 *
 * Taxes are listed one per tax (NHIL/GETFund, VAT) by running each line
 * through the same Tax::computeAll() call as OrderCalculator, so the rows add
 * up to the order's stored tax amount.
 */
class OrderDocument
{
    /**
     * @return array<string, mixed>
     */
    public static function data(Order $order): array
    {
        $order->loadMissing(['company.partner.country', 'company.partner.state', 'partner.country', 'partner.state', 'currency', 'paymentTerm', 'user', 'lines.product', 'lines.uom', 'lines.taxes', 'optionalLines.product']);

        $currency = $order->currency?->name;
        $money = fn (float|int|string|null $amount): string => money((float) $amount, $currency);
        $isOrder = in_array($order->state, [OrderState::SALE], true);
        $productLines = $order->lines->filter(fn ($line): bool => blank($line->display_type));

        $lines = $productLines->map(function ($line) use ($money): array {
            $product = $line->product?->name;
            $label = trim((string) $line->name);

            return [
                'name'        => $product ?: ($label !== '' ? $label : '—'),
                'description' => $product && $label !== '' && $label !== $product ? $label : null,
                'quantity'    => PrintedDocument::quantity($line->product_uom_qty),
                'unit'        => static::showsUnits() ? $line->uom?->name : null,
                'rate'        => $money($line->price_unit),
                'discount'    => (float) $line->discount > 0 ? PrintedDocument::quantity($line->discount).'%' : '',
                'amount'      => $money($line->price_subtotal),
            ];
        })->values()->all();

        $totals = [['label' => __('support::documents.subtotal'), 'value' => $money($order->amount_untaxed)]];

        foreach (static::taxRows($order, $productLines) as $name => $amount) {
            $totals[] = ['label' => $name, 'value' => $money($amount)];
        }

        $totals[] = ['label' => __('support::documents.total'), 'value' => $money($order->amount_total), 'class' => 'total'];

        $optional = in_array($order->state, [OrderState::DRAFT, OrderState::SENT], true)
            ? $order->optionalLines->map(fn ($option): string => trim(($option->product?->name ?? $option->name).' · '.PrintedDocument::quantity($option->quantity).' × '.$money($option->price_unit)))->values()->all()
            : [];

        return [
            'title'         => __('support::documents.titles.'.($isOrder ? 'sales-order' : 'quotation')),
            'number'        => $order->name,
            'status'        => $order->state === OrderState::CANCEL ? __('support::documents.statuses.cancelled') : null,
            'accent'        => PrintedDocument::accent($order->company),
            'company'       => PrintedDocument::company($order->company),
            'party_label'   => __('support::documents.bill-to'),
            'party'         => PrintedDocument::party($order->partner),
            'headline'      => ['label' => __('support::documents.total'), 'value' => $money($order->amount_total)],
            'meta'          => array_values(array_filter([
                [__('support::documents.date'), $order->date_order?->translatedFormat('d M Y')],
                [__('support::documents.valid-until'), $isOrder ? null : $order->validity_date?->translatedFormat('d M Y')],
                [__('support::documents.payment-terms'), $order->paymentTerm?->name],
                [__('support::documents.reference'), $order->client_order_ref],
                [__('support::documents.salesperson'), $order->user?->name],
            ], fn (array $row): bool => filled($row[1]))),
            'show_discount' => collect($lines)->contains(fn (array $line): bool => $line['discount'] !== ''),
            'lines'         => $lines,
            'totals'        => $totals,
            'sections'      => $optional === [] ? [] : [['label' => __('sales::app.documents.options'), 'lines' => $optional]],
            'payment'       => [__('support::documents.payment-reference', ['reference' => $order->name])],
            'notes'         => $order->note,
            'terms'         => null,
        ];
    }

    /**
     * @return array<string, float>
     */
    protected static function taxRows(Order $order, $lines): array
    {
        $rows = [];

        foreach ($lines as $line) {
            if ($line->taxes->isEmpty()) {
                continue;
            }

            $result = Tax::computeAll(
                $line->taxes,
                (float) $line->price_unit * (1 - ((float) $line->discount / 100)),
                $order->currency,
                (float) $line->product_qty,
                $line->product,
                $order->partner,
            );

            foreach ($result['taxes'] as $tax) {
                $rows[$tax['name']] = ($rows[$tax['name']] ?? 0) + (float) $tax['amount'];
            }
        }

        return array_map(fn (float $amount): float => round($amount, 2), $rows);
    }

    protected static function showsUnits(): bool
    {
        return (bool) settings(ProductSettings::class)->enable_uom;
    }
}
