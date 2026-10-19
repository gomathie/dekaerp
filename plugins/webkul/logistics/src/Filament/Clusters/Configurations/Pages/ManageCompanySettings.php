<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Logistics\Enums\CapacityCheck;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\ServiceType;
use Webkul\Logistics\Services\CompanyProvisioner;
use Webkul\Logistics\Services\StopLinkService;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

/**
 * Logistics settings of the current company, including the per-company switch.
 *
 * Reachable while Logistics is off (that's where it is switched on); guarded by
 * the page permission page_logistics_manage_company_settings.
 */
class ManageCompanySettings extends Page
{
    use HasPageShield;

    protected static ?string $cluster = Configurations::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'settings';

    protected static string $lang = 'logistics::filament/clusters/configurations/pages/manage-company-settings';

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_logistics_manage_company_settings';
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.navigation.title');
    }

    public function getTitle(): string
    {
        return __(static::$lang.'.title');
    }

    public function getSubheading(): ?string
    {
        $company = $this->company();

        return $company
            ? __(static::$lang.'.subheading', ['company' => $company->name])
            : __(static::$lang.'.no-company');
    }

    public function mount(): void
    {
        $this->form->fill($this->currentValues());
    }

    public function form(Schema $schema): Schema
    {
        $companyId = $this->company()?->id;

        return $schema
            ->statePath('data')
            ->disabled(! $companyId)
            ->components([
                Section::make(__(static::$lang.'.sections.status'))
                    ->schema([
                        TextEntry::make('status')
                            ->hiddenLabel()
                            ->state(fn (): string => $this->setting()?->is_enabled
                                ? __(static::$lang.'.status.enabled')
                                : __(static::$lang.'.status.disabled'))
                            ->badge()
                            ->color(fn (): string => $this->setting()?->is_enabled ? 'success' : 'gray'),
                        TextEntry::make('readiness')
                            ->label(__(static::$lang.'.readiness.label'))
                            ->state(fn (): array => $this->readinessMessages() ?: [__(static::$lang.'.readiness.ok')])
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->color(fn (): string => $this->readinessMessages() ? 'warning' : 'success'),
                    ]),

                Section::make(__(static::$lang.'.sections.delivery'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('require_pod_for_delivery')
                            ->label(__(static::$lang.'.fields.require-pod-for-delivery')),
                        Toggle::make('require_pod_photo')
                            ->label(__(static::$lang.'.fields.require-pod-photo')),
                        Toggle::make('capture_driver_on_pod')
                            ->label(__(static::$lang.'.fields.capture-driver-on-pod'))
                            ->helperText(__(static::$lang.'.fields.capture-driver-on-pod-help')),
                        Toggle::make('capture_recipient_id')
                            ->label(__(static::$lang.'.fields.capture-recipient-id'))
                            ->helperText(__(static::$lang.'.fields.capture-recipient-id-help')),
                        // A fixed set rather than free text: the value is how
                        // long a credential that travels in a URL stays usable,
                        // so it is a deliberate choice from sensible options,
                        // not a number to mistype.
                        Select::make('stop_link_ttl_hours')
                            ->label(__(static::$lang.'.fields.stop-link-ttl-hours'))
                            ->helperText(__(static::$lang.'.fields.stop-link-ttl-hours-help'))
                            ->options(StopLinkService::ttlOptions())
                            ->default(24)
                            ->selectablePlaceholder(false)
                            ->required(),
                        TextInput::make('stop_link_rate_limit')
                            ->label(__(static::$lang.'.fields.stop-link-rate-limit'))
                            ->helperText(__(static::$lang.'.fields.stop-link-rate-limit-help'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(600)
                            ->placeholder((string) config('logistics.stop_link.per_token_per_minute')),
                    ]),

                Section::make(__(static::$lang.'.sections.operations'))
                    ->columns(2)
                    ->schema([
                        Select::make('default_service_type_id')
                            ->label(__(static::$lang.'.fields.default-service-type'))
                            ->options(fn (): array => ServiceType::query()->active()->orderBy('sort')->pluck('name', 'id')->all()),
                        Select::make('capacity_check')
                            ->label(__(static::$lang.'.fields.capacity-check'))
                            ->options(CapacityCheck::class)
                            ->required()
                            ->native(false),
                        TextInput::make('overdue_grace_minutes')
                            ->label(__(static::$lang.'.fields.overdue-grace-minutes'))
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required(),
                        TextInput::make('free_waiting_minutes')
                            ->label(__(static::$lang.'.fields.free-waiting-minutes'))
                            ->helperText(__(static::$lang.'.fields.free-waiting-minutes-helper'))
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required(),
                    ]),

                Section::make(__(static::$lang.'.sections.accounting'))
                    ->columns(2)
                    ->schema([
                        Select::make('invoice_journal_id')
                            ->label(__(static::$lang.'.fields.invoice-journal'))
                            ->options(fn (): array => $this->journalOptions($companyId, JournalType::SALE)),
                        Select::make('bill_journal_id')
                            ->label(__(static::$lang.'.fields.bill-journal'))
                            ->options(fn (): array => $this->journalOptions($companyId, JournalType::PURCHASE)),
                        Select::make('default_expense_account_id')
                            ->label(__(static::$lang.'.fields.default-expense-account'))
                            ->options(fn (): array => Account::query()->orderBy('code')->get()
                                ->mapWithKeys(fn (Account $account): array => [$account->id => trim($account->code.' '.$account->name)])
                                ->all())
                            ->searchable(),
                        Toggle::make('expense_approval_required')
                            ->label(__(static::$lang.'.fields.expense-approval-required')),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label(__(static::$lang.'.actions.save'))
                            ->submit('save')
                            ->visible(fn (): bool => $this->company() !== null),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $company = $this->company();

        if (! $company) {
            return;
        }

        $data = $this->form->getState();

        $setting = CompanySetting::forCompany($company->id);
        $setting->fill(collect($data)->only(static::editableFields())->all());
        $setting->save();

        Notification::make()
            ->success()
            ->title(__(static::$lang.'.notifications.saved'))
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enable')
                ->label(__(static::$lang.'.actions.enable.label'))
                ->icon('heroicon-o-power')
                ->color('success')
                ->visible(fn (): bool => $this->company() !== null && ! $this->setting()?->is_enabled)
                ->requiresConfirmation()
                ->modalHeading(__(static::$lang.'.actions.enable.heading'))
                ->modalDescription(fn (): string => $this->readinessMessages()
                    ? __(static::$lang.'.actions.enable.description-with-problems', ['problems' => implode(' ', $this->readinessMessages())])
                    : __(static::$lang.'.actions.enable.description'))
                ->action(function (): void {
                    app(CompanyProvisioner::class)->enable($this->company());

                    $this->form->fill($this->currentValues());

                    Notification::make()
                        ->success()
                        ->title(__(static::$lang.'.actions.enable.notification'))
                        ->send();
                }),

            Action::make('disable')
                ->label(__(static::$lang.'.actions.disable.label'))
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn (): bool => (bool) $this->setting()?->is_enabled)
                ->requiresConfirmation()
                ->modalHeading(__(static::$lang.'.actions.disable.heading'))
                ->modalDescription(__(static::$lang.'.actions.disable.description'))
                ->action(function (): void {
                    app(CompanyProvisioner::class)->disable($this->company());

                    Notification::make()
                        ->success()
                        ->title(__(static::$lang.'.actions.disable.notification'))
                        ->send();
                }),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function editableFields(): array
    {
        return [
            'require_pod_for_delivery',
            'require_pod_photo',
            'stop_link_ttl_hours',
            'stop_link_rate_limit',
            'capture_driver_on_pod',
            'capture_recipient_id',
            'default_service_type_id',
            'capacity_check',
            'overdue_grace_minutes',
            'free_waiting_minutes',
            'invoice_journal_id',
            'bill_journal_id',
            'default_expense_account_id',
            'expense_approval_required',
        ];
    }

    protected function company(): ?Company
    {
        return app(CompanyContext::class)->currentCompany();
    }

    protected function setting(): ?CompanySetting
    {
        $company = $this->company();

        return $company ? CompanySetting::forCompany($company->id) : null;
    }

    protected function currentValues(): array
    {
        $setting = $this->setting();

        $defaults = [
            'require_pod_for_delivery'  => true,
            'require_pod_photo'         => false,
            'stop_link_ttl_hours'       => 24,
            'capacity_check'            => CapacityCheck::WARN->value,
            'overdue_grace_minutes'     => 30,
            'free_waiting_minutes'      => 60,
            'expense_approval_required' => true,
        ];

        if (! $setting?->exists) {
            return $defaults;
        }

        return array_merge($defaults, collect($setting->only(static::editableFields()))
            ->map(fn ($value) => $value instanceof BackedEnum ? $value->value : $value)
            ->all());
    }

    /**
     * @return array<int, string>
     */
    protected function readinessMessages(): array
    {
        $company = $this->company();

        if (! $company) {
            return [];
        }

        return collect(app(CompanyProvisioner::class)->readiness($company))
            ->map(fn (string $key): string => __('logistics::services/company-provisioner.readiness.'.$key))
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function journalOptions(?int $companyId, JournalType $type): array
    {
        if (! $companyId) {
            return [];
        }

        return Journal::query()
            ->where('company_id', $companyId)
            ->where('type', $type)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
