<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use InvalidArgumentException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Move;
use Webkul\Account\Support\MoveDocument;
use Webkul\Support\Traits\PDFHandler;

class PreviewAction extends Action
{
    use PDFHandler;

    protected string $template = '';

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.preview';
    }

    public function getTemplate(): string
    {
        return (string) $this->template;
    }

    public function setTemplate(string $template): static
    {
        if (! view()->exists($template)) {
            throw new InvalidArgumentException("The view [{$template}] does not exist.");
        }

        $this->template = $template;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/preview.title'))
            ->color('gray')
            ->visible(fn (Move $record) => $record->state == MoveState::POSTED)
            ->icon('heroicon-o-viewfinder-circle')
            ->modalHeading(__('accounts::filament/resources/invoice/actions/preview.modal.title'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('accounts::filament/resources/invoice/actions/preview.modal.action.close.title'))
            ->modalContent(fn (Move $record) => view($this->getTemplate(), ['record' => $record]))
            ->modalFooterActions(function (Action $action): array {
                return [
                    $action->getModalCancelAction(),
                    Action::make('print')
                        ->label(__('accounts::filament/resources/invoice/actions/preview.modal.action.print.title'))
                        ->icon('heroicon-o-printer')
                        ->color('primary')
                        // Streamed straight to the browser, named after the document
                        // (TRACE365, 2026-11-23). It used to be saved to the public disk
                        // as invoice-<day>.pdf, so two documents printed the same day
                        // overwrote each other and the file stayed there afterwards.
                        ->action(fn (Move $record) => $this->downloadPDF(
                            view($this->getTemplate(), ['record' => $record])->render(),
                            static::fileName($record),
                        )),
                ];
            });
    }

    /**
     * "invoice-INV-2026-00012": the kind and number, slashes made dashes so
     * the slug keeps them apart.
     */
    public static function fileName(Move $record): string
    {
        return MoveDocument::kind($record).'-'.str_replace(['/', '\\'], '-', (string) ($record->name ?: $record->getKey()));
    }
}
