<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Services\PlanCatalog;
use Webkul\Security\Models\User;

class CreatePlan extends CreateRecord
{
    protected static string $resource = PlanResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            abort(403);
        }

        try {
            return app(PlanCatalog::class)->createPlan($actor, $data);
        } catch (Throwable $exception) {
            ReportsServiceFailure::haltOnForm($this, $exception);
        }
    }
}
