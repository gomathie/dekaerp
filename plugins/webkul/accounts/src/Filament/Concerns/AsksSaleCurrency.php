<?php

namespace Webkul\Account\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\SaleCurrencyQuestion;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * "Ask which currency each sale is in" (plan #10 MC-2, TRACE365, user
 * 2026-11-24): the one dialog for every page that starts a sale, a new
 * customer invoice (MC-2a) and a new quotation or sales order (MC-2b). Its
 * rules are in SaleCurrencyQuestion; this asks and applies the answer. Two
 * copies of the dialog would be two sets of rules.
 */
trait AsksSaleCurrency
{
    /**
     * The dialog's answer. Locked: only the dialog's own action sets them,
     * never the browser.
     */
    #[Locked]
    public ?int $saleCurrencyId = null;

    /** A corrected rate to keep (stored convention), or null for the feed's. */
    #[Locked]
    public ?float $saleCurrencyRate = null;

    /** The record's rate column: invoice_currency_rate on an invoice, currency_rate on an order. */
    abstract protected function saleCurrencyRateColumn(): string;

    /** Whether this page asks. A page narrows it (an invoice asks only for a customer invoice). */
    protected function asksSaleCurrency(): bool
    {
        return app(SaleCurrencyQuestion::class)->isAsked(current_company_id());
    }

    /** The question opens with the page, before anything is typed. */
    protected function openSaleCurrencyQuestion(): void
    {
        if ($this->asksSaleCurrency()) {
            $this->defaultAction = 'askSaleCurrency';
        }
    }

    /**
     * The base currency opens the form as before. Another currency shows the
     * day's rate, which the person accepts or corrects; there is no way on
     * without a rate, because a missing rate is never 1.
     */
    public function askSaleCurrencyAction(): Action
    {
        $question = app(SaleCurrencyQuestion::class);
        $company = Company::withoutGlobalScopes()->with('currency')->find(current_company_id());
        $baseId = (int) $company?->currency_id;
        $lang = 'accounts::filament/resources/invoice/pages/create-invoice.sale-currency';

        $feed = fn (?int $currencyId): ?array => $company && $currencyId && $currencyId !== $baseId && ($currency = Currency::find($currencyId))
            ? $question->feedRate($currency, $company)
            : null;

        $display = function (?int $currencyId) use ($question, $company, $feed): ?array {
            $currency = $currencyId ? Currency::find($currencyId) : null;

            if (! $company?->currency || ! $currency) {
                return null;
            }

            return $question->display($feed($currencyId)['rate'] ?? 1.0, $company->currency, $currency);
        };

        $fillRate = function (Set $set, ?int $currencyId) use ($feed, $display): void {
            $set('rate', $feed($currencyId) ? ($display($currencyId)['amount'] ?? null) : null);
        };

        return Action::make('askSaleCurrency')
            ->modalHeading(__($lang.'.heading'))
            ->modalDescription(__($lang.'.description'))
            ->modalIcon('heroicon-o-banknotes')
            ->modalWidth(Width::Large)
            // On a phone the question is a bottom sheet with full-width
            // 48 px buttons and the rate in 28 px type (MC-2 step 1); the CSS
            // is with the other phone rules in the panel topbar view.
            ->extraModalWindowAttributes(['class' => 'deka-sale-currency-sheet'])
            ->modalSubmitActionLabel(__($lang.'.continue'))
            ->fillForm(function () use ($baseId, $feed, $display): array {
                $currencyId = (int) ($this->data['currency_id'] ?? $baseId);
                $shown = $display($currencyId);

                return [
                    'currency_id' => $currencyId,
                    'rate'        => $feed($currencyId) ? ($shown['amount'] ?? null) : null,
                ];
            })
            ->schema([
                ToggleButtons::make('currency_id')
                    ->hiddenLabel()
                    ->options(fn (): array => collect($question->options((int) current_company_id()))
                        ->mapWithKeys(fn (string $code, int $id): array => [$id => $id === $baseId ? __($lang.'.base', ['code' => $code]) : $code])
                        ->all())
                    ->inline()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, $state) => $fillRate($set, (int) $state)),
                TextInput::make('rate')
                    ->label(__($lang.'.rate'))
                    ->numeric()
                    ->extraInputAttributes(['class' => 'deka-sale-currency-rate'])
                    ->minValue(0.000001)
                    ->required(fn (Get $get): bool => (int) $get('currency_id') !== $baseId)
                    ->visible(fn (Get $get): bool => (int) $get('currency_id') !== $baseId)
                    ->prefix(fn (Get $get): string => '1 '.($display((int) $get('currency_id'))['from'] ?? '').' =')
                    ->suffix(fn (Get $get): string => $display((int) $get('currency_id'))['to'] ?? '')
                    ->helperText(function (Get $get) use ($feed, $lang): string {
                        $rate = $feed((int) $get('currency_id'));

                        if ($rate === null) {
                            return __($lang.'.no-rate');
                        }

                        $date = $rate['date']?->translatedFormat('j M Y') ?? '—';

                        return $rate['stale'] ? __($lang.'.stale', ['date' => $date]) : __($lang.'.feed', ['date' => $date]);
                    }),
            ])
            ->action(function (array $data) use ($question, $baseId, $feed, $display): void {
                $currencyId = (int) $data['currency_id'];

                // Only a currency the company may use (CR-6); anything else
                // keeps the form as it was.
                if (! array_key_exists($currencyId, $question->options((int) current_company_id()))) {
                    return;
                }

                $this->data['currency_id'] = $currencyId;
                $this->saleCurrencyId = $currencyId;
                $this->saleCurrencyRate = null;

                if ($currencyId === $baseId) {
                    return;
                }

                // The direction the dialog showed, worked out again here
                // rather than taken back from the browser.
                $stored = $question->fromDisplay((float) $data['rate'], $display($currencyId)['baseFirst'] ?? true);
                $feedRate = $feed($currencyId)['rate'] ?? null;

                // The feed's own rate is not pinned: it keeps following the
                // document date, as every document does. Only a corrected
                // rate (or one typed because there was none) is kept as manual.
                if ($feedRate === null || abs($stored - $feedRate) > 1e-9 * max(1.0, $feedRate)) {
                    $this->saleCurrencyRate = $stored;
                }
            });
    }

    /**
     * "Currency and rate" reopens the same question, so a rate can be
     * corrected after the dialog is closed (MC-2 rate bar).
     */
    protected function saleCurrencyHeaderAction(): Action
    {
        return $this->askSaleCurrencyAction()
            ->label(__('accounts::filament/resources/invoice/pages/create-invoice.sale-currency.change'))
            ->icon('heroicon-o-banknotes')
            ->color('gray')
            ->visible(fn (): bool => $this->asksSaleCurrency());
    }

    /**
     * A confirmed rate goes on the record before its first save, so every
     * line is costed at it from the start. Applied only while the record is
     * still in the currency it was confirmed for: choosing a customer with
     * another default currency afterwards leaves it on the feed.
     */
    protected function applyConfirmedSaleRate(Model $record): void
    {
        if ($this->saleCurrencyRate !== null && (int) $record->currency_id === (int) $this->saleCurrencyId) {
            $record->forceFill([
                $this->saleCurrencyRateColumn() => $this->saleCurrencyRate,
                'currency_rate_source'          => Move::RATE_MANUAL,
                'currency_rate_confirmed_by'    => Auth::id(),
            ]);
        }
    }

    /** Who kept which rate, on the record's own history. */
    protected function noteConfirmedSaleRate(Model $record, string $langKey): void
    {
        if ($record->currency_rate_source !== Move::RATE_MANUAL || ! $record->currency || ! $record->company?->currency) {
            return;
        }

        $record->addMessage([
            'type' => 'note',
            'body' => e(__($langKey, [
                'rate' => app(SaleCurrencyQuestion::class)->label((float) $record->{$this->saleCurrencyRateColumn()}, $record->company->currency, $record->currency),
            ])),
        ]);
    }
}
