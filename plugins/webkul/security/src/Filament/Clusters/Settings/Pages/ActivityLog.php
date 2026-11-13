<?php

namespace Webkul\Security\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Webkul\Chatter\Models\Message;
use Webkul\Security\Filament\Exports\ActivityLogExporter;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Services\CompanyContext;

/**
 * Company activity log (plan #1, CR-11; user, 2026-11-13: "find out what
 * changes are going on in the company, who changed what, who deleted what").
 *
 * One read-only table over what is already recorded: model activity
 * (HasLogActivity: created / updated / deleted with old and new values),
 * security events (SecurityAuditLogger) and reasons given when a confirmed
 * document is reopened. No second log is written.
 *
 * Isolation: read past the global scopes and filtered explicitly to the
 * companies the viewer is working in. CompanyScope would also show rows with
 * no company (shared), which here would leak one tenant's history to every
 * other, so `whereIn(company_id)` excludes them on purpose. In "All
 * companies" mode installation staff see every company, as elsewhere.
 */
class ActivityLog extends Page implements HasTable
{
    use HasPageShield, InteractsWithTable;

    protected string $view = 'security::filament.clusters.settings.pages.activity-log';

    protected static ?string $cluster = Settings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 90;

    protected static function getPagePermission(): ?string
    {
        return 'page_security_activity_log';
    }

    /**
     * The page permission, or the Company Admin of the company being viewed
     * (a global Company Admin role is true for any id, so membership of the
     * current company is checked too). The permission check is written out,
     * not delegated: a canAccess() here replaces HasPageShield's, and
     * parent::canAccess() would reach Filament's, which allows everyone.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($user->can(static::getPagePermission())) {
            return true;
        }

        $companyId = current_company_id();

        return $companyId !== null
            && $user->isCompanyAdmin($companyId)
            && $user->allowedCompanies()->withoutGlobalScopes()->where('companies.id', $companyId)->exists();
    }

    public static function getNavigationGroup(): string
    {
        return __('security::filament/clusters/activity-log.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/clusters/activity-log.navigation');
    }

    public function getTitle(): string
    {
        return __('security::filament/clusters/activity-log.title');
    }

    /**
     * The one query every column, filter and the export read.
     */
    public static function activityQuery(): Builder
    {
        return Message::query()
            ->withoutGlobalScopes()
            ->whereIn('company_id', app(CompanyContext::class)->activeIds())
            ->whereNotNull('event')
            ->with('causer');
    }

    public function table(Table $table): Table
    {
        $lang = 'security::filament/clusters/activity-log';

        return $table
            ->query(static::activityQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label(__($lang.'.columns.when'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label(__($lang.'.columns.who'))
                    ->placeholder(__($lang.'.system')),
                TextColumn::make('messageable_type')
                    ->label(__($lang.'.columns.record'))
                    ->formatStateUsing(fn (Message $record): string => static::recordLabel($record))
                    ->wrap(),
                TextColumn::make('event')
                    ->label(__($lang.'.columns.action'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => static::eventLabel($state))
                    ->color(fn (?string $state): string => match (true) {
                        str_contains((string) $state, 'deleted')                               => 'danger',
                        $state === 'created'                                                   => 'success',
                        str_starts_with((string) $state, 'security.')                          => 'warning',
                        default                                                                => 'gray',
                    }),
                TextColumn::make('properties')
                    ->label(__($lang.'.columns.changes'))
                    ->state(fn (Message $record): string => static::changesSummary($record))
                    ->wrap()
                    ->limit(160),
            ])
            ->filters([
                SelectFilter::make('causer_id')
                    ->label(__($lang.'.columns.who'))
                    ->options(fn (): array => CompanyMembers::options())
                    ->searchable(),
                SelectFilter::make('messageable_type')
                    ->label(__($lang.'.filters.record-type'))
                    ->options(fn (): array => static::activityQuery()
                        ->reorder()
                        ->distinct()
                        ->pluck('messageable_type')
                        ->mapWithKeys(fn (string $type): array => [$type => Str::headline(class_basename($type))])
                        ->sort()
                        ->all()),
                SelectFilter::make('event')
                    ->label(__($lang.'.columns.action'))
                    ->options([
                        'created' => static::eventLabel('created'),
                        'updated' => static::eventLabel('updated'),
                        'deleted' => static::eventLabel('deleted'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->where('event', 'like', '%'.$data['value'].'%')
                        : $query),
                Filter::make('period')
                    ->schema([
                        DatePicker::make('from')->label(__($lang.'.filters.from')),
                        DatePicker::make('until')->label(__($lang.'.filters.until')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date))),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__($lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(ActivityLogExporter::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function recordLabel(Message $message): string
    {
        $type = Str::headline(class_basename((string) $message->messageable_type));

        return $type.' #'.$message->messageable_id;
    }

    public static function eventLabel(?string $event): string
    {
        $key = 'security::filament/clusters/activity-log.events.'.str_replace('.', '-', (string) $event);

        return __($key) !== $key ? __($key) : Str::headline(str_replace(['.', '_'], ' ', (string) $event));
    }

    /**
     * "field: old → new" for each logged attribute; the reason for a reopened
     * document; nothing for events that carry no values. Plain text only: the
     * values are user data and the column does not render HTML.
     */
    public static function changesSummary(Message $message): string
    {
        $properties = (array) ($message->properties ?? []);

        if (isset($properties['reason'])) {
            return (string) $properties['reason'];
        }

        $parts = [];

        foreach ($properties as $field => $change) {
            if (is_array($change) && (array_key_exists('old_value', $change) || array_key_exists('new_value', $change))) {
                // An update: what it was and what it became.
                $parts[] = Str::headline((string) $field).': '.static::plain($change['old_value'] ?? null).' → '.static::plain($change['new_value'] ?? null);
            } elseif (! is_array($change) && $change !== null && $change !== '') {
                // A create or delete snapshot: the value at that moment.
                $parts[] = Str::headline((string) $field).': '.static::plain($change);
            }
        }

        return implode('; ', $parts);
    }

    protected static function plain(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return is_scalar($value) ? Str::limit(strip_tags((string) $value), 60) : Str::limit((string) json_encode($value), 60);
    }
}
