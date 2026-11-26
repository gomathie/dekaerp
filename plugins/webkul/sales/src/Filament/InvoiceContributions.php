<?php

namespace Webkul\Sale\Filament;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Support\InvoiceSchemaRegistry;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\DeletedInvoiceOrders;
use Webkul\Sale\Services\OrderFromInvoice;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Sale\Support\SalesAccess;

/**
 * "Create sales order" on the customer invoice form, and the linked order and
 * its delivery on the invoice view. Contributed through InvoiceSchemaRegistry
 * because Accounts must not reference Sales.
 *
 * Delivery (user, 2026-11-22, options A + D + E): the form asks whether the
 * customer has taken the goods, defaulting to the company's Sales setting;
 * the invoice page shows the delivery and can mark it delivered later.
 *
 * Registered from SaleServiceProvider.
 */
class InvoiceContributions
{
    /**
     * packageBooted() can run more than once and the registry is static, so
     * this must land only once or the fields are rendered twice.
     */
    protected static bool $registered = false;

    public static function register(): void
    {
        if (static::$registered) {
            return;
        }

        static::$registered = true;

        // Checked when the form is built, not here: registration runs at
        // boot for every installation, and Sales' tables exist only once
        // Sales is installed. Without this the invoice form queried a missing
        // table on any installation without Sales.
        InvoiceSchemaRegistry::form('general.after', fn (): array => Package::isPluginInstalled('sales') ? [
            static::createOrderCheckbox(),
            static::deliveredNowCheckbox(),
        ] : []);

        InvoiceSchemaRegistry::infolist('general.after', fn (): array => Package::isPluginInstalled('sales') ? [
            static::linkedOrderEntry(),
            static::deliveryEntry(),
            static::markDeliveredActions(),
        ] : []);

        // The delete confirmation of a customer invoice (production,
        // 2026-11-25): deleting an order's draft invoice left the order
        // "Invoiced" with its delivery still reserving stock. The answer is
        // acted on by RecomputeOrdersOfDeletedInvoiceListener.
        InvoiceSchemaRegistry::form('delete', fn (iterable $records = []): array => Package::isPluginInstalled('sales')
            ? static::deleteQuestion($records)
            : []);
    }

    /**
     * Yes/No to cancel the orders the invoices came from, asked only of
     * someone who may change them. Anyone else is told the order stays; it
     * goes back to "To invoice" either way.
     *
     * @param  iterable<Move>  $records
     * @return list<Radio|Text>
     */
    protected static function deleteQuestion(iterable $records): array
    {
        $records = collect($records)->filter(fn ($record): bool => $record instanceof Move);

        if ($records->isEmpty()) {
            return [];
        }

        $service = app(DeletedInvoiceOrders::class);

        $cancellable = $service->cancellableOrders($records);

        $staying = $records
            ->flatMap(fn (Move $record) => $service->ordersOf($record))
            ->filter(fn (Order $order): bool => $order->state === OrderState::SALE)
            ->reject(fn (Order $order): bool => $cancellable->contains('id', $order->getKey()))
            ->unique('id');

        $lang = 'sales::filament/contributions/invoice.delete';
        $fields = [];

        if ($cancellable->isNotEmpty()) {
            $fields[] = Radio::make('cancel_sale_orders')
                ->label(trans_choice($lang.'.question', $cancellable->count(), ['orders' => $cancellable->pluck('name')->join(', ')]))
                ->boolean(__($lang.'.yes'), __($lang.'.no'))
                ->default(false)
                ->inline();
        }

        if ($staying->isNotEmpty()) {
            $fields[] = Text::make(trans_choice($lang.'.stays', $staying->count(), ['orders' => $staying->pluck('name')->join(', ')]));
        }

        return $fields;
    }

    protected static function createOrderCheckbox(): Checkbox
    {
        return Checkbox::make('create_sale_order')
            ->label(__('sales::filament/contributions/invoice.create-sale-order.label'))
            ->helperText(__('sales::filament/contributions/invoice.create-sale-order.helper'))
            ->live()
            // Not a column on the invoice; saved below into Sales' own table.
            ->dehydrated(false)
            ->visible(fn (?Move $record, $livewire): bool => static::mayRequest($record, $livewire))
            // "New direct sale" opens the form with this ticked (user,
            // 2026-11-22); CreateInvoice presets the customer and "Paid now".
            ->formatStateUsing(fn (?Move $record): bool => $record === null
                ? static::isDirectSale()
                : app(OrderFromInvoice::class)->link($record) !== null)
            // Once the invoice is confirmed the choice is made; undoing it is
            // done by cancelling the invoice, which cancels the order.
            ->disabled(fn (?Move $record): bool => $record !== null && $record->state !== MoveState::DRAFT)
            ->saveRelationshipsUsing(function (?Move $record, $state, Get $get): void {
                if (! $record || ! $state || $record->state === MoveState::CANCEL) {
                    return;
                }

                // Re-checked at save: a hidden field is not saved, but the
                // super-admin Gate::before skips policies, so the module switch
                // is asserted in request() too.
                if (! static::mayRequest($record, null)) {
                    return;
                }

                app(OrderFromInvoice::class)->request($record, auth()->id(), (bool) $get('goods_delivered_now'));
            });
    }

    protected static function deliveredNowCheckbox(): Checkbox
    {
        return Checkbox::make('goods_delivered_now')
            ->label(__('sales::filament/contributions/invoice.goods-delivered-now.label'))
            ->helperText(__('sales::filament/contributions/invoice.goods-delivered-now.helper'))
            ->dehydrated(false)
            ->default(fn (): bool => static::companyDefault())
            ->formatStateUsing(function (?Move $record): bool {
                $link = $record ? app(OrderFromInvoice::class)->link($record) : null;

                if ($link) {
                    return $link->deliver_now;
                }

                // A direct sale is goods handed over at the counter.
                return static::isDirectSale() || static::companyDefault();
            })
            ->visible(fn (Get $get, ?Move $record, $livewire): bool => (bool) $get('create_sale_order') && static::mayRequest($record, $livewire))
            ->disabled(fn (?Move $record): bool => $record !== null && $record->state !== MoveState::DRAFT);
    }

    protected static function linkedOrderEntry(): TextEntry
    {
        return TextEntry::make('linked_sale_orders')
            ->label(__('sales::filament/contributions/invoice.linked-order.label'))
            ->icon('heroicon-o-shopping-bag')
            ->state(fn (Move $record): array => static::linkedOrders($record)->pluck('name')->all())
            ->url(function (Move $record): ?string {
                $order = static::linkedOrders($record)->first();

                return $order && Gate::allows('view', $order)
                    ? OrderResource::getUrl('view', ['record' => $order])
                    : null;
            })
            ->badge()
            ->visible(fn (Move $record): bool => SalesAccess::enabledForCompany((int) $record->company_id) && static::linkedOrders($record)->isNotEmpty());
    }

    /**
     * Delivery of the order made from this invoice: done, or waiting for the
     * warehouse. Hidden for invoices without such an order and for services.
     */
    protected static function deliveryEntry(): TextEntry
    {
        return TextEntry::make('sale_order_delivery')
            ->label(__('sales::filament/contributions/invoice.delivery.label'))
            ->icon('heroicon-o-truck')
            ->badge()
            ->state(fn (Move $record): ?string => static::pendingCount($record) > 0
                ? __('sales::filament/contributions/invoice.delivery.waiting')
                : __('sales::filament/contributions/invoice.delivery.done'))
            ->color(fn (Move $record): string => static::pendingCount($record) > 0 ? 'warning' : 'success')
            ->visible(fn (Move $record): bool => static::hasDeliveries($record));
    }

    protected static function markDeliveredActions(): Actions
    {
        return Actions::make([
            Action::make('markSaleOrderDelivered')
                ->label(__('sales::filament/contributions/invoice.delivery.mark-delivered'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('sales::filament/contributions/invoice.delivery.confirm-heading'))
                ->modalDescription(__('sales::filament/contributions/invoice.delivery.confirm-description'))
                ->visible(fn (Move $record): bool => static::pendingCount($record) > 0 && static::mayDeliver($record))
                ->action(function (Move $record): void {
                    // Re-checked here: visibility is not authorization.
                    if (! static::mayDeliver($record)) {
                        return;
                    }

                    $done = app(OrderFromInvoice::class)->markDelivered($record);

                    $done
                        ? Notification::make()->success()->title(__('sales::filament/contributions/invoice.delivery.delivered'))->send()
                        : Notification::make()->warning()->title(__('sales::filament/contributions/invoice.delivery.not-enough-stock'))->send();
                }),
        ]);
    }

    protected static function companyDefault(): bool
    {
        return (bool) (app(QuotationAndOrderSettings::class)->goods_leave_on_invoice ?? true);
    }

    /** The invoice form was opened from "New direct sale" (only read when it is filled). */
    protected static function isDirectSale(): bool
    {
        return request()->boolean('direct_sale');
    }

    protected static function hasDeliveries(Move $record): bool
    {
        $order = app(OrderFromInvoice::class)->liveOrder($record);

        return $order !== null
            && SalesAccess::enabledForCompany((int) $order->company_id)
            && Package::isPluginInstalled('inventories')
            && $order->operations()->exists();
    }

    protected static function pendingCount(Move $record): int
    {
        $order = app(OrderFromInvoice::class)->liveOrder($record);

        return $order ? app(OrderFromInvoice::class)->pendingDeliveries($order) : 0;
    }

    /**
     * Validating a delivery moves stock: the user must be allowed to change
     * the order, in a company that has Sales.
     */
    protected static function mayDeliver(Move $record): bool
    {
        $order = app(OrderFromInvoice::class)->liveOrder($record);

        return $order !== null
            && SalesAccess::enabledForCompany((int) $order->company_id)
            && Gate::allows('update', $order);
    }

    /**
     * Orders linked to the invoice, whether it was made from them or they
     * were made from it; cancelled ones left out.
     *
     * @return Collection<int, Order>
     */
    protected static function linkedOrders(Move $record): Collection
    {
        return Order::query()
            ->whereHas('accountMoves', fn ($query) => $query->whereKey($record->getKey()))
            ->where('state', '!=', OrderState::CANCEL)
            ->orderBy('id')
            ->get(['id', 'name', 'company_id']);
    }

    /**
     * Customer invoices only (credit notes reuse the form), for users who may
     * create sales orders in a company that has Sales.
     */
    protected static function mayRequest(?Move $record, $livewire): bool
    {
        if ($livewire !== null && method_exists($livewire, 'getResource') && str_contains($livewire::getResource(), 'CreditNote')) {
            return false;
        }

        if ($record !== null && $record->move_type !== MoveType::OUT_INVOICE) {
            return false;
        }

        $companyId = $record?->company_id ?? current_company_id();

        return SalesAccess::enabledForCompany($companyId ? (int) $companyId : null)
            && Gate::allows('create', Order::class);
    }
}
