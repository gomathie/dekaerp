<?php

namespace Webkul\Sale\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Webkul\Sale\Models\ZohoImportRun;
use Webkul\Sale\Services\ZohoImport\MappingProposer;
use Webkul\Sale\Services\ZohoImport\ZohoImportException;
use Webkul\Sale\Services\ZohoImport\ZohoImportRunner;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\ImportUpload;
use Webkul\Support\Models\Company;

/**
 * Import a company's history from Zoho Books (user, 2026-11-25): pick the
 * company, upload the export, review the proposed matching, import, then
 * read the Zoho against DEKA comparison. The lead runs production imports
 * from here because Laravel Cloud's console is not open to them.
 *
 * Active Super Admins only. canAccess() hides the page; every action asks
 * ZohoImportRunner again, because a Livewire call can be replayed by anyone
 * signed in. No Shield page permission: like the platform billing cluster,
 * this is installation staff's tool, not a role a company may hand out.
 */
class ZohoImport extends Page
{
    protected const LANG = 'sales::filament/pages/zoho-import';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

    protected static ?int $navigationSort = 95;

    protected static ?string $slug = 'zoho-import';

    protected string $view = 'sales::filament.pages.zoho-import';

    public ?int $runId = null;

    public static function canAccess(): bool
    {
        return ZohoImportRunner::mayImport(auth()->user());
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function getNavigationLabel(): string
    {
        return __(self::LANG.'.navigation');
    }

    public function getTitle(): string
    {
        return __(self::LANG.'.title');
    }

    public function mount(): void
    {
        $this->runId = ZohoImportRun::query()->latest('id')->value('id');
    }

    public function currentRun(): ?ZohoImportRun
    {
        return $this->runId ? ZohoImportRun::query()->with('company')->find($this->runId) : null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('propose')
                ->label(__(self::LANG.'.propose.label'))
                ->icon('heroicon-o-arrow-up-tray')
                ->modalDescription(__(self::LANG.'.propose.description'))
                ->schema([
                    Select::make('company_id')
                        ->label(__(self::LANG.'.propose.company'))
                        ->options(fn (): array => Company::withoutGlobalScopes()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(),
                    FileUpload::make('file')
                        ->label(__(self::LANG.'.propose.file'))
                        ->acceptedFileTypes(['application/json', 'text/plain'])
                        // The browser hint is trivially bypassed; the rules
                        // check the content server-side, and the export's
                        // structure is checked again when it is read.
                        ->rules(['file', 'mimes:json,txt', 'max:10240'])
                        ->maxSize(10240)
                        ->storeFiles(false)
                        ->required(),
                    Radio::make('numbering')
                        ->label(__(self::LANG.'.propose-options.numbering'))
                        ->options([
                            MappingProposer::NUMBERING_DEKA => __(self::LANG.'.propose-options.numbering-deka'),
                            MappingProposer::NUMBERING_ZOHO => __(self::LANG.'.propose-options.numbering-zoho'),
                        ])
                        ->default(MappingProposer::NUMBERING_DEKA)
                        ->required(),
                    Toggle::make('skip_unused_samples')
                        ->label(__(self::LANG.'.propose-options.skip-samples'))
                        ->default(true),
                ])
                ->action(function (array $data, ZohoImportRunner $runner): void {
                    $file = is_array($data['file'] ?? null) ? reset($data['file']) : ($data['file'] ?? null);
                    $resolved = ImportUpload::resolve($file, 'zoho-');

                    try {
                        if ($resolved === null) {
                            throw new ZohoImportException(__(self::LANG.'.errors.empty-file'));
                        }

                        $this->runId = $runner->propose(auth()->user(), (int) $data['company_id'], (string) file_get_contents($resolved[0]), [
                            'numbering'           => (string) ($data['numbering'] ?? MappingProposer::NUMBERING_DEKA),
                            'skip_unused_samples' => (bool) ($data['skip_unused_samples'] ?? true),
                        ])->id;

                        Notification::make()->success()->title(__(self::LANG.'.propose.done'))->send();
                    } catch (ZohoImportException|AuthorizationException $exception) {
                        Notification::make()->danger()->title($exception->getMessage())->send();
                    } finally {
                        // The export is client data: the run keeps it until
                        // the import ends; the upload itself is not kept.
                        if ($file instanceof TemporaryUploadedFile) {
                            $file->delete();
                        }

                        if ($resolved !== null && $resolved[2]) {
                            @unlink($resolved[0]);
                        }
                    }
                }),

            // The parallel run (user, 2026-11-26): a fresh export compared
            // with DEKA's live books. Writes nothing, so it needs no approval.
            Action::make('compare')
                ->label(__(self::LANG.'.compare.label'))
                ->icon('heroicon-o-scale')
                ->color('gray')
                ->modalDescription(__(self::LANG.'.compare.description'))
                ->schema([
                    Select::make('company_id')
                        ->label(__(self::LANG.'.propose.company'))
                        ->options(fn (): array => Company::withoutGlobalScopes()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(),
                    FileUpload::make('file')
                        ->label(__(self::LANG.'.propose.file'))
                        ->acceptedFileTypes(['application/json', 'text/plain'])
                        ->rules(['file', 'mimes:json,txt', 'max:10240'])
                        ->maxSize(10240)
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data, ZohoImportRunner $runner): void {
                    $file = is_array($data['file'] ?? null) ? reset($data['file']) : ($data['file'] ?? null);
                    $resolved = ImportUpload::resolve($file, 'zoho-');

                    try {
                        if ($resolved === null) {
                            throw new ZohoImportException(__(self::LANG.'.errors.empty-file'));
                        }

                        $run = $runner->propose(auth()->user(), (int) $data['company_id'], (string) file_get_contents($resolved[0]), mode: ZohoImportRun::MODE_COMPARE);
                        $this->runId = $runner->start(auth()->user(), $run)->id;

                        Notification::make()->success()->title(__(self::LANG.'.compare.queued'))->send();
                    } catch (ZohoImportException|AuthorizationException $exception) {
                        Notification::make()->danger()->title($exception->getMessage())->send();
                    } finally {
                        if ($file instanceof TemporaryUploadedFile) {
                            $file->delete();
                        }

                        if ($resolved !== null && $resolved[2]) {
                            @unlink($resolved[0]);
                        }
                    }
                }),

            Action::make('download')
                ->label(__(self::LANG.'.download.label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => filled($this->currentRun()?->comparison))
                ->action(function (ZohoImportRunner $runner) {
                    $runner->assertMayImport(auth()->user());

                    $run = $this->currentRun();

                    return response()->streamDownload(function () use ($run): void {
                        $out = fopen('php://output', 'w');
                        // Names come from the client's books: a cell that
                        // starts like a formula is neutralised before a
                        // spreadsheet can run it.
                        $safe = fn ($value) => is_string($value) && preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;

                        fputcsv($out, ['Section', 'Row', 'Zoho', 'DEKA', 'Difference', 'Status']);

                        foreach ($run->comparison['rows'] ?? [] as $row) {
                            fputcsv($out, [$row['section'], $safe($row['label']), $row['zoho'], $row['deka'], $row['difference'], $row['status']]);
                        }

                        fputcsv($out, []);
                        fputcsv($out, ['Documents in one system only']);

                        foreach ($run->comparison['unmatched'] ?? [] as $document) {
                            fputcsv($out, [$safe($document['text'])]);
                        }

                        fclose($out);
                    }, 'zoho-comparison-'.$run->id.'.csv', ['Content-Type' => 'text/csv']);
                }),

            Action::make('import')
                ->label(__(self::LANG.'.import.label'))
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn (): bool => $this->currentRun()?->status === ZohoImportRun::PROPOSED && $this->currentRun()?->mode === ZohoImportRun::MODE_IMPORT)
                ->requiresConfirmation()
                ->modalHeading(fn (): string => __(self::LANG.'.import.confirm', ['company' => $this->currentRun()?->company?->name]))
                ->modalDescription(__(self::LANG.'.import.description'))
                ->action(function (ZohoImportRunner $runner): void {
                    $run = $this->currentRun();

                    try {
                        if (! $run) {
                            throw new ZohoImportException(__(self::LANG.'.errors.no-run'));
                        }

                        $runner->start(auth()->user(), $run);

                        Notification::make()->success()->title(__(self::LANG.'.import.queued'))->send();
                    } catch (ZohoImportException|AuthorizationException $exception) {
                        Notification::make()->danger()->title($exception->getMessage())->send();
                    }
                }),
        ];
    }

    /**
     * @return array<string, int>
     */
    public function actionCounts(string $section): array
    {
        return collect($this->currentRun()?->mapping[$section] ?? [])->countBy('action')->all();
    }
}
