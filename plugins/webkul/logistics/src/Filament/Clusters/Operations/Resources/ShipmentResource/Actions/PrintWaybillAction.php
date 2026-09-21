<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Support\Traits\PDFHandler;

class PrintWaybillAction extends Action
{
    use PDFHandler;

    protected string $lang = 'logistics::documents/waybill';

    public static function getDefaultName(): ?string
    {
        return 'printWaybill';
    }

    public function download(Shipment $record): Response
    {
        Gate::authorize('view', $record);

        $shipment = DB::transaction(function () use ($record): Shipment {
            $shipment = Shipment::query()
                ->whereKey($record->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (blank($shipment->waybill_no)) {
                LogisticsSequences::ensure(LogisticsSequences::WAYBILL, (int) $shipment->company_id);

                $shipment->forceFill([
                    'waybill_no' => LogisticsSequences::next(
                        LogisticsSequences::WAYBILL,
                        (int) $shipment->company_id,
                    ),
                ])->save();
            }

            return $shipment;
        });

        $shipment->load([
            'company.partner.state',
            'company.partner.country',
            'customer.state',
            'customer.country',
            'carrier',
            'pickupAddress.state',
            'pickupAddress.country',
            'deliveryAddress.state',
            'deliveryAddress.country',
            'lines.packageType',
            'trips' => fn ($query) => $query->with(['driver', 'vehicle'])->orderByDesc('logistics_trips.id'),
        ]);

        return $this->downloadPDF(
            view('logistics::pdf.waybill', ['record' => $shipment])->render(),
            'waybill-'.$shipment->waybill_no,
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__($this->lang.'.action.label'))
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->visible(fn (Shipment $record): bool => Auth::user()?->can('view', $record) ?? false)
            ->action(fn (Shipment $record): Response => $this->download($record));
    }
}
