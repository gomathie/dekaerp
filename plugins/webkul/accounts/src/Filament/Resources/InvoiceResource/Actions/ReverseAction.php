<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveReversal;
use Webkul\Account\Services\CreditNoteFromInvoice;
use Webkul\Support\Traits\PDFHandler;

class ReverseAction extends Action
{
    use PDFHandler;

    protected bool|Closure $hasDatabaseTransactions = true;

    protected static string $resource = CreditNoteResource::class;

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.credit-note';
    }

    public function setResource(string $resource): self
    {
        static::$resource = $resource;

        return $this;
    }

    /**
     * @return class-string
     */
    public static function getResource(): string
    {
        return static::$resource;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/reverse.title'))
            ->color('gray')
            ->visible(fn (Move $record) => $record->state == MoveState::POSTED)
            ->icon('heroicon-o-receipt-refund')
            ->modalHeading(__('accounts::filament/resources/invoice/actions/reverse.modal.heading'));

        $this->schema(function (Schema $schema) {
            $record = $this->getRecord();

            // Bills keep the draft reverse. A customer invoice is posted here.
            if ($record instanceof Move && $record->move_type === MoveType::OUT_INVOICE) {
                return $schema->components($this->customerCreditNoteFields());
            }

            return $schema->components([
                Textarea::make('reason')
                    ->label(__('accounts::filament/resources/invoice/actions/reverse.modal.form.reason'))
                    ->maxLength(245)
                    ->required(),
                Select::make('journal_id')
                    ->label(__('accounts::filament/resources/invoice/actions/reverse.modal.form.journal'))
                    ->relationship(
                        'journal',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->whereIn('id', [$this->getRecord()->journal_id])
                    )
                    ->required()
                    ->default(fn () => $this->getRecord()->journal_id),
                DatePicker::make('date')
                    ->label(__('accounts::filament/resources/invoice/actions/reverse.modal.form.date'))
                    ->default(now())
                    ->native(false)
                    ->required(),
            ]);
        });

        $this->action(function (Move $record, array $data, $livewire) {
            if ($record->move_type === MoveType::OUT_INVOICE) {
                $this->issueCustomerCreditNote($record, $data);

                return;
            }

            try {
                $moveReversal = MoveReversal::create([
                    'journal_id' => $data['journal_id'],
                    'date'       => $data['date'],
                    'reason'     => $data['reason'],
                ]);

                $moveReversal->moves()->attach($record);

                $defaultValues = $this->prepareMoveValues($record, $moveReversal);

                $isCancelNeeded = ! $defaultValues['auto_post'] && $record->move_type == MoveType::ENTRY;

                $reversedMoves = AccountFacade::reverseMoves(
                    collect([$record]),
                    $defaultValues,
                    $isCancelNeeded
                )->each(function (Move $move) use ($moveReversal) {
                    $moveReversal->newMoves()->attach($move->id);
                });

                AccountFacade::computeAccountMove($record);

                $redirectUrl = static::getResource()::getUrl('edit', ['record' => $reversedMoves->first()->id]);

                $livewire->redirect($redirectUrl, navigate: FilamentView::hasSpaMode());
            } catch (Throwable $e) {
                Notification::make()
                    ->warning()
                    ->body($e->getMessage())
                    ->send();

                $this->halt(shouldRollBackDatabaseTransaction: true);
            }
        });
    }

    /**
     * @return array<int, CheckboxList|DatePicker|Textarea|ToggleButtons>
     */
    private function customerCreditNoteFields(): array
    {
        return [
            ToggleButtons::make('scope')
                ->label(__('accounts::filament/resources/invoice/actions/reverse.one-step.scope'))
                ->options([
                    'whole' => __('accounts::filament/resources/invoice/actions/reverse.one-step.whole'),
                    'lines' => __('accounts::filament/resources/invoice/actions/reverse.one-step.lines'),
                ])
                ->default('whole')
                ->inline()
                ->required(),
            CheckboxList::make('line_ids')
                ->label(__('accounts::filament/resources/invoice/actions/reverse.one-step.line-list'))
                ->options(function (): array {
                    $record = $this->getRecord();

                    if (! $record instanceof Move) {
                        return [];
                    }

                    return $record->invoiceLines()
                        ->orderBy('id')
                        ->get()
                        ->mapWithKeys(fn ($line): array => [
                            $line->id => trim((string) $line->name.' '.$line->price_subtotal),
                        ])
                        ->all();
                })
                ->visible(fn (Get $get): bool => $get('scope') === 'lines')
                ->required(fn (Get $get): bool => $get('scope') === 'lines'),
            Textarea::make('reason')
                ->label(__('accounts::filament/resources/invoice/actions/reverse.modal.form.reason'))
                ->maxLength(245)
                ->required(),
            ToggleButtons::make('disposition')
                ->label(__('accounts::filament/resources/invoice/actions/reverse.one-step.disposition'))
                ->options([
                    CreditNoteFromInvoice::APPLY  => __('accounts::filament/resources/invoice/actions/reverse.one-step.apply'),
                    CreditNoteFromInvoice::REFUND => __('accounts::filament/resources/invoice/actions/reverse.one-step.refund'),
                    CreditNoteFromInvoice::CREDIT => __('accounts::filament/resources/invoice/actions/reverse.one-step.credit'),
                ])
                ->default(CreditNoteFromInvoice::APPLY)
                ->inline()
                ->required(),
            DatePicker::make('date')
                ->label(__('accounts::filament/resources/invoice/actions/reverse.modal.form.date'))
                ->default(now())
                ->native(false)
                ->required(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function issueCustomerCreditNote(Move $record, array $data): void
    {
        try {
            app(CreditNoteFromInvoice::class)->issue(
                $record,
                (string) ($data['reason'] ?? ''),
                (string) ($data['disposition'] ?? ''),
                ($data['scope'] ?? 'whole') === 'lines'
                    ? array_map('intval', (array) ($data['line_ids'] ?? []))
                    : [],
                isset($data['date']) ? (string) $data['date'] : null,
            );
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Notification::make()
                ->warning()
                ->body($exception->getMessage())
                ->send();

            $this->halt(shouldRollBackDatabaseTransaction: true);

            return;
        }

        Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/actions/reverse.one-step.saved'))
            ->send();
    }

    private function prepareMoveValues(Move $move, MoveReversal $moveReversal): array
    {
        return [
            'reference'         => Str::limit(
                "Reversal of: {$move->name}, {$moveReversal->reason}",
                250
            ),
            'date'              => $moveReversal->date,
            'invoice_date_due'  => $moveReversal->date,
            'invoice_date'      => $move->isInvoice(true) ? $moveReversal->date : null,
            'journal_id'        => $moveReversal->journal_id,
            'invoice_user_id'   => $move->invoice_user_id,
            'auto_post'         => false,
        ];
    }
}
