<?php

namespace Webkul\Support\Filament\Resources\SequenceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Gate;
use Webkul\Support\Filament\Resources\SequenceResource;
use Webkul\Support\Models\Sequence;

class ManageSequences extends ManageRecords
{
    protected static string $resource = SequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('support::filament/resources/sequence/pages/manage-sequences.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    // UF-029: only installation staff may create a shared
                    // sequence (no company), which numbers every company
                    // without its own. For anyone else the company field is
                    // locked to their current company; the payload is
                    // re-checked here, since a locked field can be tampered.
                    if (! Gate::allows('createIn', [Sequence::class, null])) {
                        $data['company_id'] = current_company_id();
                    }

                    $companyId = filled($data['company_id'] ?? null) ? (int) $data['company_id'] : null;

                    abort_unless(Gate::allows('createIn', [Sequence::class, $companyId]), 403);

                    $data['company_id'] = $companyId;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/sequence/pages/manage-sequences.header-actions.create.notification.title'))
                        ->body(__('support::filament/resources/sequence/pages/manage-sequences.header-actions.create.notification.body')),
                ),
        ];
    }
}
