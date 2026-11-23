<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Webkul\Sale\Models\Order;
use Webkul\Support\Traits\PDFHandler;

class PreviewAction extends Action
{
    use PDFHandler;

    public static function getDefaultName(): ?string
    {
        return 'orders.sales.preview-quotation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/preview.title'))
            ->modalContent(fn ($record) => view('sales::sales.quotation', ['record' => $record]))
            // A quotation could be previewed and e-mailed but not downloaded
            // (TRACE365, 2026-11-23): Print streams the same document as a PDF.
            ->modalFooterActions(fn (Action $action): array => [
                $action->getModalCancelAction(),
                Action::make('print')
                    ->label(__('accounts::filament/resources/invoice/actions/preview.modal.action.print.title'))
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->action(fn (Order $record) => $this->downloadPDF(
                        view('sales::sales.quotation', ['record' => $record])->render(),
                        str_replace(['/', '\\'], '-', (string) $record->name),
                    )),
            ])
            ->color('gray');
    }
}
