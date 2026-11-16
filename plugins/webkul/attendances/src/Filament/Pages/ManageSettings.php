<?php

namespace Webkul\Attendance\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;
use Webkul\Attendance\Enums\CaptureLocation;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Services\CompanyProvisioner;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

/**
 * Attendance settings of the current company, including the per-company switch.
 *
 * Added 2026-11-12 (user: Attendance installed but nowhere to be seen). The
 * switch existed only in CompanyProvisioner; no screen called it, so every
 * company stayed off and the module never appeared. This page is reachable
 * while Attendance is off - that is where it is switched on - and is guarded
 * by its own Shield page permission, page_attendance_manage_settings.
 */
class ManageSettings extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'icon-attendance';

    protected static ?int $navigationSort = 7;

    protected static ?string $slug = 'attendances/settings';

    protected static string $lang = 'attendances::filament/pages/manage-settings';

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_attendance_manage_settings';
    }

    public static function getNavigationGroup(): string|UnitEnum
    {
        return NavigationGroup::Employee;
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
        return $schema
            ->statePath('data')
            ->disabled($this->company() === null)
            ->components([
                Section::make(__(static::$lang.'.sections.status'))
                    ->compact()
                    ->schema([
                        TextEntry::make('status')
                            ->hiddenLabel()
                            ->badge()
                            ->state(fn (): string => $this->setting()?->is_enabled
                                ? __(static::$lang.'.status.enabled')
                                : __(static::$lang.'.status.disabled'))
                            ->color(fn (): string => $this->setting()?->is_enabled ? 'success' : 'gray'),
                    ]),

                Section::make(__(static::$lang.'.sections.rules'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('auto_checkout_after_hours')
                            ->label(__(static::$lang.'.fields.auto-checkout'))
                            ->helperText(__(static::$lang.'.fields.auto-checkout-help'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(24)
                            ->required()
                            ->suffix(__(static::$lang.'.units.hours')),

                        Select::make('capture_location')
                            ->label(__(static::$lang.'.fields.capture-location'))
                            ->helperText(__(static::$lang.'.fields.capture-location-help'))
                            ->options(CaptureLocation::class)
                            ->required(),

                        TextInput::make('late_tolerance_minutes')
                            ->label(__(static::$lang.'.fields.late-tolerance'))
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(240)
                            ->required()
                            ->suffix(__(static::$lang.'.units.minutes')),

                        TextInput::make('overtime_tolerance_minutes')
                            ->label(__(static::$lang.'.fields.overtime-tolerance'))
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(240)
                            ->required()
                            ->suffix(__(static::$lang.'.units.minutes')),
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

        $values = $this->form->getState();

        $setting = app(CompanyProvisioner::class)->provision($company);
        $setting->forceFill(collect($values)->only(static::editableFields())->all())->save();

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
                ->modalDescription(__(static::$lang.'.actions.enable.description'))
                ->action(function (): void {
                    app(CompanyProvisioner::class)->enable($this->company());

                    Notification::make()
                        ->success()
                        ->title(__(static::$lang.'.actions.enable.notification'))
                        ->send();

                    // Reload so the navigation shows the attendance list at once.
                    $this->redirect(static::getUrl());
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

                    $this->redirect(static::getUrl());
                }),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function editableFields(): array
    {
        return [
            'auto_checkout_after_hours',
            'capture_location',
            'late_tolerance_minutes',
            'overtime_tolerance_minutes',
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

    /**
     * @return array<string, mixed>
     */
    protected function currentValues(): array
    {
        $setting = $this->setting();

        return collect(static::editableFields())
            ->mapWithKeys(function (string $field) use ($setting): array {
                $value = $setting?->{$field};

                return [$field => $value instanceof BackedEnum ? $value->value : $value];
            })
            ->all();
    }
}
