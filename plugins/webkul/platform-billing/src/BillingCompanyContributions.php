<?php

namespace Webkul\PlatformBilling;

use DateTimeImmutable;
use DateTimeZone;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Schemas\SubscriptionForm;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Services\CompanyAccountType;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Support\Filament\Resources\CompanyResource\Support\CompanySchemaRegistry;
use Webkul\Support\Models\Company;

/**
 * Trial length, the commercial switch, and the seat limit, on the company
 * that is being edited. Module grants stay on Plugin Manager's section.
 * This does not write a company column: every change goes through a service.
 */
class BillingCompanyContributions
{
    /**
     * SeatCapacityService::setLimit does not check the actor. The screen
     * refuses everyone but a Super Admin so a forged Livewire call cannot
     * set another tenant's limit. That is this screen's access check, not a
     * second capacity rule.
     */
    public const ONLY_SUPER_ADMIN_SEATS = 'Only a Super Admin may set a seat limit.';

    protected static bool $registered = false;

    protected static string $lang = 'platform-billing::filament/billing.company';

    public static function register(): void
    {
        if (static::$registered) {
            return;
        }

        static::$registered = true;

        CompanySchemaRegistry::form('main.after', fn (): array => [static::section()]);
    }

    protected static function section(): Section
    {
        return Section::make(__(static::$lang.'.title'))
            ->description(__(static::$lang.'.description'))
            ->icon('heroicon-o-banknotes')
            ->visible(fn (?Company $record): bool => $record instanceof Company && $record->exists && static::maySee())
            ->schema([
                Text::make(fn (?Company $record): string => static::summary($record)),
                Actions::make([
                    Action::make('addTrialDays')
                        ->label(__(static::$lang.'.add-days'))
                        // Visible on a commercial company too. Hiding it would
                        // make CompanyAccountType's "only a test company"
                        // refusal unreachable from a forged action.
                        ->schema([
                            TextInput::make('test_days')
                                ->label(__(static::$lang.'.days'))
                                ->integer()
                                ->minValue(1)
                                ->required(),
                        ])
                        ->action(function (array $data, Company $record): void {
                            static::run(function (User $actor) use ($data, $record): void {
                                app(CompanyAccountType::class)->addDays(
                                    $actor,
                                    $record,
                                    (int) ($data['test_days'] ?? 0),
                                    new DateTimeImmutable('now', new DateTimeZone('UTC')),
                                );
                            });
                        }),
                    Action::make('switchToCommercial')
                        ->label(__(static::$lang.'.switch'))
                        ->requiresConfirmation()
                        // Same reason as add trial days: the service refuses a
                        // company that is already commercial.
                        ->schema([
                            Select::make('price_id')
                                ->label(__(static::$lang.'.price'))
                                ->options(fn (): array => SubscriptionForm::priceOptions())
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (array $data, Company $record): void {
                            $price = Price::query()->find($data['price_id'] ?? 0);

                            if (! $price instanceof Price) {
                                return;
                            }

                            static::run(function (User $actor) use ($price, $record): void {
                                app(CompanyAccountType::class)->switchToCommercial(
                                    $actor,
                                    $record,
                                    $price,
                                    new DateTimeImmutable('now', new DateTimeZone('UTC')),
                                );
                            });
                        }),
                    Action::make('setSeatLimit')
                        ->label(__(static::$lang.'.set-seats'))
                        ->requiresConfirmation()
                        ->schema([
                            TextInput::make('seat_limit')
                                ->label(__(static::$lang.'.seat-limit'))
                                ->helperText(__(static::$lang.'.seat-help'))
                                ->integer()
                                ->minValue(0),
                        ])
                        ->action(function (array $data, Company $record): void {
                            static::run(function (User $actor) use ($data, $record): void {
                                // setLimit() does not check who is calling.
                                // Hiding the button is not the check: a forged
                                // action still reaches this line.
                                if (! $actor->isSuperAdmin()) {
                                    throw new AuthorizationException(self::ONLY_SUPER_ADMIN_SEATS);
                                }

                                $raw = $data['seat_limit'] ?? null;
                                $limit = ($raw === null || $raw === '') ? null : (int) $raw;
                                app(SeatCapacityService::class)->setLimit($record, $limit, $actor);
                            });
                        }),
                ])->key('platformBilling'),
            ]);
    }

    protected static function summary(?Company $record): string
    {
        if (! $record instanceof Company || ! $record->exists) {
            return '';
        }

        $fresh = $record->fresh() ?? $record;
        $seats = app(SeatCapacityService::class);
        $limit = $seats->limitFor($fresh);
        $used = $seats->usedSeats($fresh);
        $type = $fresh->account_type === 'test'
            ? __(static::$lang.'.test')
            : __(static::$lang.'.commercial');
        $ends = $fresh->test_ends_at?->timezone('UTC')->format('Y-m-d H:i') ?? '—';
        $limitText = $limit === null ? __(static::$lang.'.unlimited') : (string) $limit;

        return __(static::$lang.'.summary', [
            'type'  => $type,
            'ends'  => $ends,
            'used'  => $used,
            'limit' => $limitText,
        ]);
    }

    protected static function maySee(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->is_active && ($user->isSuperAdmin() || $user->isMultiCompanyAdmin());
    }

    protected static function run(callable $callback): void
    {
        $actor = auth()->user();

        if (! $actor instanceof User) {
            abort(403);
        }

        try {
            $callback($actor);
            Notification::make()->success()->title(__(static::$lang.'.saved'))->send();
        } catch (Throwable $exception) {
            ReportsServiceFailure::notify($exception);
        }
    }
}
