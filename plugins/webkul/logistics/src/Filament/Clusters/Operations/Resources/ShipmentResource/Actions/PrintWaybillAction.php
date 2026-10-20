<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Support\SupportServiceProvider;
use Webkul\Support\Traits\PDFHandler;

class PrintWaybillAction extends Action
{
    use PDFHandler;

    protected string $lang = 'logistics::documents/waybill';

    public static function getDefaultName(): ?string
    {
        return 'printWaybill';
    }

    public function download(Shipment $record): StreamedResponse
    {
        Gate::authorize('view', $record);

        $shipment = DB::transaction(function () use ($record): Shipment {
            $shipment = Shipment::query()
                ->whereKey($record->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            LogisticsAccess::ensureEnabled((int) $shipment->company_id);

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

        $pdf = $this->generatePDF(view('logistics::pdf.waybill', [
            'record' => $shipment,
            'isRtl'  => SupportServiceProvider::isRtl(),
        ])->render());

        return response()->streamDownload(
            function () use ($pdf): void {
                echo $pdf->output();
            },
            static::fileName($shipment->waybill_no),
            ['Content-Type' => 'application/pdf'],
        );
    }

    /**
     * Waybill numbers carry their sequence prefix, which contains slashes
     * (WB/2026/00001). Symfony refuses a Content-Disposition filename holding
     * a path separator, so those become hyphens; without this every print
     * threw InvalidArgumentException after the PDF had already been rendered.
     */
    public static function fileName(?string $waybillNumber): string
    {
        return 'waybill-'.str_replace(['/', '\\'], '-', (string) $waybillNumber).'.pdf';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__($this->lang.'.action.label'))
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->visible(fn (Shipment $record): bool => Auth::user()?->can('view', $record) ?? false)
            ->action(fn (Shipment $record): StreamedResponse => $this->download($record));
    }
}
