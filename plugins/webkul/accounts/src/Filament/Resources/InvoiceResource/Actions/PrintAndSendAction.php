<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Support\Traits\PDFHandler;

class PrintAndSendAction extends Action
{
    use PDFHandler;

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.print-and-send';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/print-and-send.title'))
            ->color('gray')
            ->visible(function (Move $record) {
                return
                    $record->state == MoveState::CANCEL
                    || $record->state == MoveState::POSTED;
            });

        $this->beforeFormFilled(function (Move $record, Action $action) {
            $action->fillForm([
                'files'       => $this->prepareInvoice($record),
                'partners'    => [$record->partner_id],
                'subject'     => __('accounts::filament/resources/invoice/actions/print-and-send.message.subject', [
                    'partner' => $record->partner?->name,
                    'number'  => $record->name,
                ]),
                'description' => static::defaultMessage($record),
            ]);
        });

        $this->schema(
            function (Schema $schema) {
                return $schema->components([
                    Select::make('partners')
                        ->options(Partner::all()->pluck('name', 'id'))
                        ->multiple()
                        ->label(__('accounts::filament/resources/invoice/actions/print-and-send.modal.form.partners'))
                        ->searchable()
                        ->preload(),
                    TextInput::make('subject')
                        ->label(__('accounts::filament/resources/invoice/actions/print-and-send.modal.form.subject'))
                        ->hiddenLabel(),
                    RichEditor::make('description')
                        ->label(__('accounts::filament/resources/invoice/actions/print-and-send.modal.form.description'))
                        ->hiddenLabel(),
                    FileUpload::make('files')
                        ->label(__('accounts::filament/resources/invoice/actions/print-and-send.modal.form.files'))
                        ->acceptedFileTypes([
                            'image/*',
                            'application/pdf',
                        ])
                        ->downloadable()
                        ->openable()
                        ->multiple()
                        ->disk('public')
                        ->hiddenLabel(),
                ]);
            }
        );

        $this->modalSubmitActionLabel(__('accounts::filament/resources/invoice/actions/print-and-send.modal.action.submit.title'));
        $this->modalIcon('heroicon-m-paper-airplane');
        $this->icon('heroicon-o-envelope');
        $this->action(fn (Move $record, array $data) => AccountFacade::printAndSendMove($record, $data));
        $this->modalSubmitAction(function ($action) {
            $action->label(__('accounts::filament/resources/invoice/actions/print-and-send.modal.action.submit.title'));
            $action->icon('heroicon-m-paper-airplane');
        });
    }

    /**
     * The message the form opens with (plan 10, item 4, 2026-11-25). It was
     * fixed English, signed "Administrator" whoever sent it, and said "for
     * account N/A" when the invoice had no bank account. Now: the sender's
     * language (the partner has no language of its own), signed with the
     * sending user's name and the company, and the bank sentence only when
     * an account is set. Every value is escaped: the text is HTML and a
     * contact or company name is user input.
     */
    public static function defaultMessage(Move $record): string
    {
        $lang = 'accounts::filament/resources/invoice/actions/print-and-send.message';
        $strong = fn (?string $value): string => '<strong>'.e((string) $value).'</strong>';

        $paragraphs = [
            __($lang.'.greeting', ['name' => e((string) $record->partner?->name)]),
            __($lang.'.body', [
                'number'  => $strong($record->name),
                'company' => $strong($record->company?->name),
                'amount'  => $strong(money($record->amount_total, $record->currency?->name)),
            ]),
        ];

        $account = collect([$record->partnerBank?->account_number, $record->partnerBank?->bank?->name])->filter()->join(', ');

        if ($account !== '') {
            $paragraphs[] = __($lang.'.bank', ['number' => $strong($record->name), 'account' => $strong($account)]);
        }

        $paragraphs[] = __($lang.'.questions');

        $signature = collect([auth()->user()?->name, $record->company?->name])->filter()->map(fn (string $line): string => e($line))->join('<br>');

        $paragraphs[] = '<strong>'.__($lang.'.regards').'</strong>'.($signature !== '' ? '<br>'.$signature : '');

        return collect($paragraphs)->map(fn (string $paragraph): string => '<p>'.$paragraph.'</p>')->join('');
    }

    private function prepareInvoice(Move $record): ?string
    {
        // Named after the invoice plus a random part (TRACE365, 2026-11-23). It was
        // invoice-<day>.pdf, so two invoices e-mailed the same day shared one
        // file and the second customer could receive the first one's invoice.
        return $this->savePDF(
            view('accounts::invoice/actions/preview.index', compact('record'))->render(),
            PreviewAction::fileName($record).'-'.Str::lower(Str::random(8))
        );
    }
}
