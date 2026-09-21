<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Logistics\Enums\TripState;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Services\DispatchService;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewTrip extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = TripResource::class;

    protected static string $lang = 'logistics::filament/clusters/operations/resources/trip';

    protected function getHeaderActions(): array
    {
        return [
            static::stateAction('dispatchTrip', TripState::ASSIGNED, 'dispatch', 'heroicon-o-paper-airplane'),
            static::stateAction('startTrip', TripState::DISPATCHED, 'dispatch', 'heroicon-o-play'),
            static::stateAction('completeTrip', TripState::IN_PROGRESS, 'complete', 'heroicon-o-check-circle'),
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Filament actions carry no policy check of their own, and authorize()
     * takes ability names rather than closures, so the ability is checked here
     * in visible() while DispatchService authorises again server-side.
     *
     * Action names hold no dots: Filament reads a dot as a nested action path.
     */
    protected static function stateAction(string $name, TripState $from, string $ability, string $icon): Action
    {
        return Action::make($name)
            ->label(__(static::$lang.'.actions.'.$name.'.label'))
            ->icon($icon)
            ->color($name === 'completeTrip' ? 'success' : 'primary')
            ->requiresConfirmation()
            ->modalHeading(__(static::$lang.'.actions.'.$name.'.heading'))
            ->visible(fn (Trip $record): bool => $record->state === $from
                && (Auth::user()?->can($ability, $record) ?? false))
            ->action(function (Trip $record) use ($name): void {
                $service = app(DispatchService::class);

                match ($name) {
                    'dispatchTrip' => $service->dispatch($record),
                    'startTrip'    => $service->start($record),
                    'completeTrip' => $service->complete($record),
                };

                // Capacity is advisory (D5): the trip has already moved, and
                // the warning is shown afterwards rather than blocking it.
                $warnings = $service->capacityWarnings($record->refresh());

                Notification::make()
                    ->success()
                    ->title(__(static::$lang.'.actions.'.$name.'.notification'))
                    ->body($warnings === [] ? null : implode(' ', $warnings))
                    ->send();
            });
    }
}
