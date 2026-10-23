<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Partner\Filament\Resources\PartnerResource\Exports\ContactTemplateExport;
use Webkul\Partner\Services\ContactImporter;
use Webkul\PluginManager\Package;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPartners extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('partners::filament/resources/partner/pages/list-partners.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->importContactsAction(),
            CreateAction::make()
                ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    /**
     * Upload a filled-in spreadsheet of contacts.
     *
     * The example file is offered inside the modal, so the download and the
     * upload sit in one place: open the action, take the template, fill it in,
     * come back and upload it.
     */
    protected function importContactsAction(): Action
    {
        return Action::make('importContacts')
            ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.label'))
            ->icon('heroicon-o-arrow-up-tray')
            ->color('gray')
            // Importing creates contacts, so it is gated by the same check
            // that decides whether the Create button is shown.
            ->visible(fn (): bool => static::getResource()::canCreate())
            ->modalHeading(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal.heading'))
            ->modalDescription(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal.description', [
                'max' => number_format(ContactImporter::MAX_ROWS),
            ]))
            ->modalSubmitActionLabel(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal.submit'))
            ->schema([
                FileUpload::make('file')
                    ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal.file.label'))
                    ->helperText(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal.file.helper', [
                        'columns' => implode(', ', ContactImporter::requiredColumns()),
                    ]))
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv',
                        'text/plain',
                        'application/csv',
                    ])
                    // acceptedFileTypes() above is only a browser hint and is
                    // trivially bypassed, so the type and size are enforced
                    // again server-side. Laravel's `mimes` rule sniffs the
                    // file's actual contents rather than trusting its
                    // extension, which is what catches a renamed file; `txt`
                    // is listed because a CSV written by Excel frequently
                    // sniffs as text/plain.
                    ->rules(['file', 'mimes:xlsx,xls,csv,txt', 'max:10240'])
                    ->maxSize(10240)
                    ->required(),
            ])
            // Two templates from one export class, because the staff filling
            // these in work in Excel. CSV is the default; Excel is offered
            // because Excel mangles CSV in ways that matter to these columns -
            // it strips the leading zero from a zip code and can reinterpret a
            // phone number written as +233... - and .xlsx keeps every cell as
            // typed.
            ->registerModalActions([
                Action::make('downloadTemplateCsv')
                    ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal.download-template-csv'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->link()
                    ->action(fn () => Excel::download(new ContactTemplateExport, 'deka-contacts-template.csv', ExcelFormat::CSV)),
                Action::make('downloadTemplateExcel')
                    ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal.download-template-excel'))
                    ->icon('heroicon-o-table-cells')
                    ->link()
                    ->color('gray')
                    ->action(fn () => Excel::download(new ContactTemplateExport, 'deka-contacts-template.xlsx', ExcelFormat::XLSX)),
            ])
            ->extraModalFooterActions(fn (Action $action): array => [
                $action->getModalAction('downloadTemplateCsv'),
                $action->getModalAction('downloadTemplateExcel'),
            ])
            ->action(function (array $data): void {
                $file = $data['file'] ?? null;

                if (is_array($file)) {
                    $file = reset($file);
                }

                $resolved = $this->resolveUploadedImportFile($file);

                if ($resolved === null) {
                    Notification::make()
                        ->title(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.notifications.unreadable'))
                        ->danger()
                        ->send();

                    return;
                }

                [$path, $extension, $isTemporaryCopy] = $resolved;

                try {
                    $result = app(ContactImporter::class)->import(
                        $path,
                        ContactImporter::readerTypeFor($extension),
                    );
                } finally {
                    if ($isTemporaryCopy) {
                        @unlink($path);
                    }
                }

                $this->sendImportResultNotification($result);
            });
    }

    /**
     * Resolve the uploaded file to a readable local path.
     *
     * In an action modal Filament leaves the state as a TemporaryUploadedFile,
     * which is the normal case and needs no copy. The string branch covers a
     * file that has already been stored on a disk: that disk may be S3, where
     * there is no local path at all, so the contents are streamed to a
     * temporary file and the caller deletes it afterwards.
     *
     * @return array{0: string, 1: string, 2: bool}|null
     */
    protected function resolveUploadedImportFile(mixed $file): ?array
    {
        if ($file instanceof TemporaryUploadedFile) {
            $path = $file->getRealPath();

            if (! is_string($path) || ! is_file($path)) {
                return null;
            }

            return [$path, $file->getClientOriginalExtension(), false];
        }

        if (! is_string($file) || $file === '') {
            return null;
        }

        $disk = Storage::disk(config('filament.default_filesystem_disk'));

        if (! $disk->exists($file)) {
            return null;
        }

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $temporaryPath = tempnam(sys_get_temp_dir(), 'contact-import-');

        if ($temporaryPath === false) {
            return null;
        }

        file_put_contents($temporaryPath, $disk->get($file));

        return [$temporaryPath, $extension, true];
    }

    /**
     * @param  array{created: int, skipped: int, errors: array<int, string>}  $result
     */
    protected function sendImportResultNotification(array $result): void
    {
        $prefix = 'partners::filament/resources/partner/pages/list-partners.header-actions.import.notifications.';

        // Rejected rows are listed by their spreadsheet row number so the file
        // can be corrected and uploaded again. Only the first few are shown -
        // a long list would be unreadable in a toast.
        $shownErrors = array_slice($result['errors'], 0, 5);
        $remaining = count($result['errors']) - count($shownErrors);

        if ($remaining > 0) {
            $shownErrors[] = __($prefix.'more-errors', ['count' => $remaining]);
        }

        $body = implode("\n", $shownErrors);

        if ($result['created'] === 0) {
            Notification::make()
                ->title(__($prefix.'nothing-imported'))
                ->body($body)
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $notification = Notification::make()
            ->title(__($prefix.'imported', ['count' => $result['created']]));

        if ($result['skipped'] > 0) {
            $notification
                ->body(__($prefix.'skipped', ['count' => $result['skipped']])."\n".$body)
                ->warning()
                ->persistent();
        } else {
            $notification->success();
        }

        $notification->send();
    }

    public function getPresetTableViews(): array
    {
        $views = [
            'individuals' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.individuals'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::INDIVIDUAL)),

            'companies' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.companies'))
                ->icon('heroicon-s-building-office')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::COMPANY)),

            'employees' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.employees'))
                ->icon('heroicon-s-identification')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('sub_type', 'employee')),

            'customers' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.customers'))
                ->icon('heroicon-s-shopping-bag')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_rank', '>', 0)),

            'vendors' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.vendors'))
                ->icon('heroicon-s-truck')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('supplier_rank', '>', 0)),

            'archived' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];

        // Customer and vendor ranks are columns the accounts plugin adds.
        if (! Package::isPluginInstalled('accounts')) {
            unset($views['customers'], $views['vendors']);
        }

        return $views;
    }
}
