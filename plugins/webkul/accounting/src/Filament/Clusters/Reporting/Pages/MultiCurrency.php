<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Reporting;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns\NormalizeDateFilter;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\MultiCurrencyExport;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\RefundResource;
use Webkul\Accounting\Support\MultiCurrencyReport;
use Webkul\Support\Models\Currency;

/**
 * Multi-currency report (plan #11 MC-3, TRACE365, user 2026-11-24: "double
 * or multi currency report", clean and easy to read on web and phone).
 *
 * Per currency: what was invoiced, received and is still open, in that
 * currency and in the company's at the rates booked, plus what the open
 * amount is worth at today's rate. Every figure comes from
 * MultiCurrencyReport, which the export uses too.
 */
class MultiCurrency extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms, NormalizeDateFilter;

    protected string $view = 'accounting::filament.clusters.reporting.pages.multi-currency';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?int $navigationSort = 9;

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_multi_currency';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/reporting.pages.multi-currency.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/reporting.pages.multi-currency.navigation.title');
    }

    public function getTitle(): string
    {
        return __('accounting::filament/clusters/reporting.pages.multi-currency.navigation.title');
    }

    public function mount(): void
    {
        $this->form->fill(['side' => MultiCurrencyReport::SALES]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label(__('accounting::filament/clusters/reporting.pages.multi-currency.actions.export-excel'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $data = $this->reportData;

                    return Excel::download(
                        new MultiCurrencyExport($data),
                        'multi-currency-'.$data['date_from']->format('Y-m-d').'-'.$data['date_to']->format('Y-m-d').'.xlsx'
                    );
                }),
        ];
    }

    protected function getFormSchema(): array
    {
        $lang = 'accounting::filament/clusters/reporting.pages.multi-currency.filters';

        return [
            Section::make()
                ->schema([
                    DateRangePicker::make('date_range')
                        ->label(__($lang.'.date-range'))
                        ->suffixIcon('heroicon-o-calendar')
                        ->defaultThisMonth()
                        ->ranges([
                            'This Month'   => [now()->startOfMonth(), now()->endOfMonth()],
                            'Last Month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
                            'This Quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
                            'This Year'    => [now()->startOfYear(), now()->endOfYear()],
                            'Last Year'    => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
                        ])
                        ->alwaysShowCalendar()
                        ->live(),
                    Select::make('side')
                        ->label(__($lang.'.side'))
                        ->options([
                            MultiCurrencyReport::SALES     => __($lang.'.sales'),
                            MultiCurrencyReport::PURCHASES => __($lang.'.purchases'),
                        ])
                        ->selectablePlaceholder(false)
                        ->live(),
                    // The company's own currencies only (CR-6).
                    Select::make('currency_id')
                        ->label(__($lang.'.currency'))
                        ->placeholder(__($lang.'.all-currencies'))
                        ->options(fn (): array => Currency::query()->active()->permittedFor(current_company_id())->orderBy('name')->pluck('name', 'id')->all())
                        ->live(),
                ])
                ->columns(['default' => 1, 'md' => 3])
                ->columnSpanFull(),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    /**
     * @return array<string, mixed>
     */
    #[Computed]
    public function reportData(): array
    {
        $dateRange = $this->parseDateRange();
        $dateFrom = $dateRange ? Carbon::parse($dateRange[0])->startOfDay() : now()->startOfMonth();
        $dateTo = $dateRange ? Carbon::parse($dateRange[1])->endOfDay() : now()->endOfMonth();

        $side = ($this->data['side'] ?? null) === MultiCurrencyReport::PURCHASES ? MultiCurrencyReport::PURCHASES : MultiCurrencyReport::SALES;

        // Only a currency the company may use; anything else is ignored.
        $currencyId = (int) ($this->data['currency_id'] ?? 0) ?: null;

        if ($currencyId && ! Currency::query()->active()->permittedFor(current_company_id())->whereKey($currencyId)->exists()) {
            $currencyId = null;
        }

        return app(MultiCurrencyReport::class)->build($dateFrom, $dateTo, $side, $currencyId);
    }

    /**
     * The document's own page, only for someone who may open that list.
     *
     * @param  array<string, mixed>  $document
     */
    public function documentUrl(array $document): ?string
    {
        $resource = match ($document['move_type']) {
            MoveType::OUT_INVOICE->value => InvoiceResource::class,
            MoveType::OUT_REFUND->value  => CreditNoteResource::class,
            MoveType::IN_INVOICE->value  => BillResource::class,
            MoveType::IN_REFUND->value   => RefundResource::class,
            default                      => null,
        };

        return $resource && $resource::canViewAny() ? $resource::getUrl('view', ['record' => $document['id']]) : null;
    }

    public function paymentStateLabel(?string $state): string
    {
        return $state ? (PaymentState::tryFrom($state)?->getLabel() ?? $state) : '—';
    }
}
