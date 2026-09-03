<?php

namespace Webkul\Security\Filament\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Clusters\Settings;

/**
 * Issue and revoke the Sanctum tokens client integrations authenticate with.
 *
 * Without this, revoking a token meant the client calling /logout, deleting a
 * personal_access_tokens row by hand, or disabling the whole user - none of
 * which is what you want when a customer reports a leaked key.
 *
 * A token inherits the roles and allowed companies of the user it belongs to,
 * so the table shows which user each token speaks for. See docs/api-access.md.
 */
class ManageApiTokens extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected string $view = 'security::filament.pages.manage-api-tokens';

    /**
     * The plaintext token, held only for the request that created it. Sanctum
     * stores a hash, so this is the one moment it can be shown.
     */
    public ?string $plainTextToken = null;

    public static function canAccess(): bool
    {
        return Auth::user()?->can('view_any_security_user') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/pages/manage-api-tokens.navigation.label');
    }

    public function getTitle(): string
    {
        return __('security::filament/pages/manage-api-tokens.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('security::filament/pages/manage-api-tokens.group');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('security::filament/pages/manage-api-tokens.breadcrumb'),
        ];
    }

    public function getSubheading(): ?string
    {
        return __('security::filament/pages/manage-api-tokens.subheading');
    }

    public function table(Table $table): Table
    {
        $key = 'security::filament/pages/manage-api-tokens';

        return $table
            ->query(PersonalAccessToken::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->label(__($key.'.table.columns.name'))
                    ->searchable(),
                TextColumn::make('tokenable_id')
                    ->label(__($key.'.table.columns.user'))
                    ->getStateUsing(fn (PersonalAccessToken $record): string => User::withoutGlobalScopes()
                        ->find($record->tokenable_id)?->email ?? (string) $record->tokenable_id),
                TextColumn::make('abilities')
                    ->label(__($key.'.table.columns.abilities'))
                    ->getStateUsing(fn (PersonalAccessToken $record): string => implode(', ', $record->abilities ?? [])),
                TextColumn::make('last_used_at')
                    ->label(__($key.'.table.columns.last-used'))
                    ->dateTime()
                    ->placeholder(__($key.'.table.never-used'))
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(__($key.'.table.columns.expires'))
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__($key.'.table.columns.created'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('issue')
                    ->label(__($key.'.actions.issue.label'))
                    ->icon('heroicon-o-plus')
                    ->modalSubmitActionLabel(__($key.'.actions.issue.submit'))
                    ->schema([
                        Select::make('user_id')
                            ->label(__($key.'.actions.issue.fields.user'))
                            ->options(fn (): array => User::withoutGlobalScopes()
                                ->orderBy('email')
                                ->pluck('email', 'id')
                                ->all())
                            ->searchable()
                            ->required()
                            ->helperText(__($key.'.actions.issue.fields.user-helper')),
                        TextInput::make('name')
                            ->label(__($key.'.actions.issue.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->helperText(__($key.'.actions.issue.fields.name-helper')),
                        CheckboxList::make('abilities')
                            ->label(__($key.'.actions.issue.fields.abilities'))
                            ->options([
                                'read'  => __($key.'.abilities.read'),
                                'write' => __($key.'.abilities.write'),
                                '*'     => __($key.'.abilities.all'),
                            ])
                            ->default(['read'])
                            ->required()
                            ->helperText(__($key.'.actions.issue.fields.abilities-helper')),
                    ])
                    ->action(function (array $data) use ($key): void {
                        $user = User::withoutGlobalScopes()->find($data['user_id']);

                        if (! $user) {
                            Notification::make()
                                ->danger()
                                ->title(__($key.'.notifications.missing-user'))
                                ->send();

                            return;
                        }

                        $this->plainTextToken = $user
                            ->createToken($data['name'], $data['abilities'])
                            ->plainTextToken;

                        Notification::make()
                            ->success()
                            ->title(__($key.'.notifications.issued.title'))
                            ->body(__($key.'.notifications.issued.body'))
                            ->persistent()
                            ->send();
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label(__($key.'.actions.revoke.label'))
                    ->modalHeading(__($key.'.actions.revoke.heading'))
                    ->modalDescription(__($key.'.actions.revoke.description'))
                    ->successNotificationTitle(__($key.'.notifications.revoked')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__($key.'.actions.revoke.bulk')),
                ]),
            ])
            ->emptyStateHeading(__($key.'.empty.heading'))
            ->emptyStateDescription(__($key.'.empty.description'));
    }
}
