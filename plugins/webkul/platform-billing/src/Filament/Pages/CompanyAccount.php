<?php

namespace Webkul\PlatformBilling\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use DateTimeImmutable;
use DateTimeZone;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\OverdueExtensions;
use Webkul\PlatformBilling\Services\PlatformPayments;
use Webkul\PlatformBilling\Support\Money;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Security\Services\UserInvitationService;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;
use Webkul\Support\Models\Company;

/**
 * What a Company Admin can see of their own company's platform bill.
 * The company id is resolved from the signed-in user on every call. It is
 * not a Livewire property, so a forged payload cannot point the page at
 * another tenant.
 */
class CompanyAccount extends Page implements HasTable
{
    use ChecksPagePermission;
    use HasPageShield;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Setting;

    protected static ?int $navigationSort = 91;

    protected string $view = 'platform-billing::filament.pages.table';

    public static function getNavigationLabel(): string
    {
        return __('platform-billing::filament/billing.account.navigation');
    }

    public function getTitle(): string
    {
        return __('platform-billing::filament/billing.account.title');
    }

    /**
     * HasPageShield's canAccess() is replaced by this method. The permission
     * check has to stay, or every Company Admin of an entitled company could
     * open the page without the role box being ticked.
     */
    public static function canAccess(): bool
    {
        return static::administeredCompanyId() !== null && static::hasPagePermission();
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_platform_billing_company_account';
    }

    /**
     * Platform Company Admin is true for every company id. Membership of the
     * session company is what keeps this page on one tenant.
     */
    public static function administeredCompanyId(): ?int
    {
        $user = auth()->user();
        $companyId = current_company_id();

        if (! $user instanceof User || ! $user->is_active || $companyId === null) {
            return null;
        }

        if (! $user->isCompanyAdmin($companyId)) {
            return null;
        }

        $member = $user->allowedCompanies()->whereKey($companyId)->exists();

        return $member ? $companyId : null;
    }

    /**
     * @return array<int, string>
     */
    public function summaryLines(): array
    {
        $companyId = static::administeredCompanyId();

        if ($companyId === null) {
            return [];
        }

        $seats = app(SeatCapacityService::class);
        $company = Company::withoutGlobalScopes()->find($companyId);
        $limit = $company ? $seats->limitFor($company) : null;
        $used = $company ? $seats->usedSeats($company) : 0;
        $credit = app(PlatformPayments::class)->reconcile($companyId);
        $subscription = Subscription::withoutGlobalScopes()->where('company_id', $companyId)->latest('id')->first();
        $lang = 'platform-billing::filament/billing.account';

        $lines = [
            __($lang.'.seats', [
                'used'  => $used,
                'limit' => $limit === null ? __($lang.'.unlimited') : (string) $limit,
            ]),
            __($lang.'.credit', [
                'amount'   => Money::of($credit['unapplied_minor'], 'USD')->toDecimal(),
                'currency' => 'USD',
            ]),
            $subscription instanceof Subscription
                ? __($lang.'.subscription', ['status' => $subscription->status, 'plan' => (string) $subscription->plan_name])
                : __($lang.'.no-subscription'),
        ];

        $pending = Invitation::query()->pending()->where('default_company_id', $companyId)->orderBy('id')->get();

        if ($pending->isEmpty()) {
            $lines[] = __($lang.'.no-invitations');
        }

        foreach ($pending as $invitation) {
            $lines[] = __($lang.'.invitation', ['email' => $invitation->email]);
        }

        return $lines;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Invoice::withoutGlobalScopes()
                ->where('company_id', static::administeredCompanyId() ?? 0)
                ->orderByDesc('issued_at'))
            ->columns([
                TextColumn::make('number')
                    ->label(__('platform-billing::filament/billing.invoices.columns.number'))
                    ->searchable(),
                TextColumn::make('total_minor')
                    ->label(__('platform-billing::filament/billing.invoices.columns.total'))
                    ->state(fn (Invoice $record): string => Money::of((int) $record->total_minor, (string) $record->currency)->toDecimal().' '.$record->currency),
                TextColumn::make('issued_at')
                    ->label(__('platform-billing::filament/billing.invoices.columns.issued'))
                    ->dateTime(),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading(__('platform-billing::filament/billing.account.no-invoices'));
    }

    protected function getHeaderActions(): array
    {
        $lang = 'platform-billing::filament/billing.account.actions';

        return [
            Action::make('extend')
                ->label(__($lang.'.extend'))
                ->requiresConfirmation()
                ->schema([
                    Select::make('invoice_id')
                        ->label(__($lang.'.invoice'))
                        ->options(fn (): array => $this->ownInvoices()->pluck('number', 'id')->all())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data, Action $action): void {
                    $invoice = $this->ownInvoices()->whereKey($data['invoice_id'] ?? 0)->first();

                    if (! $invoice instanceof Invoice) {
                        throw new NotFoundHttpException;
                    }

                    try {
                        app(OverdueExtensions::class)->extendAsCompanyAdmin(
                            ReportsServiceFailure::actor(),
                            $invoice,
                            new DateTimeImmutable('now', new DateTimeZone('UTC')),
                        );
                        Notification::make()->success()->title(__('platform-billing::filament/billing.account.saved'))->send();
                    } catch (Throwable $exception) {
                        ReportsServiceFailure::onAction($action, $exception);
                    }
                }),
            Action::make('resendInvitation')
                ->label(__($lang.'.resend'))
                ->schema([
                    Select::make('invitation_id')
                        ->label(__($lang.'.invitation'))
                        ->options(fn (): array => $this->ownInvitations()->pluck('email', 'id')->all())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data, Action $action): void {
                    $invitation = $this->ownInvitations()->whereKey($data['invitation_id'] ?? 0)->first();

                    if (! $invitation instanceof Invitation) {
                        throw new NotFoundHttpException;
                    }

                    try {
                        app(UserInvitationService::class)->renew($invitation, ReportsServiceFailure::actor());
                        Notification::make()->success()->title(__('platform-billing::filament/billing.account.saved'))->send();
                    } catch (Throwable $exception) {
                        ReportsServiceFailure::onAction($action, $exception);
                    }
                }),
            Action::make('revokeInvitation')
                ->label(__($lang.'.revoke'))
                ->color('danger')
                ->requiresConfirmation()
                ->schema([
                    Select::make('invitation_id')
                        ->label(__($lang.'.invitation'))
                        ->options(fn (): array => $this->ownInvitations()->pluck('email', 'id')->all())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data, Action $action): void {
                    $invitation = $this->ownInvitations()->whereKey($data['invitation_id'] ?? 0)->first();

                    if (! $invitation instanceof Invitation) {
                        throw new NotFoundHttpException;
                    }

                    try {
                        app(UserInvitationService::class)->revoke($invitation, ReportsServiceFailure::actor());
                        Notification::make()->success()->title(__('platform-billing::filament/billing.account.saved'))->send();
                    } catch (Throwable $exception) {
                        ReportsServiceFailure::onAction($action, $exception);
                    }
                }),
        ];
    }

    protected function ownInvoices(): Builder
    {
        return Invoice::withoutGlobalScopes()->where('company_id', static::administeredCompanyId() ?? 0);
    }

    protected function ownInvitations(): Builder
    {
        return Invitation::query()->pending()->where('default_company_id', static::administeredCompanyId() ?? 0);
    }
}
