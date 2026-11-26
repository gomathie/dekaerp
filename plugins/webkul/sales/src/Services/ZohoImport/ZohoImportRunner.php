<?php

namespace Webkul\Sale\Services\ZohoImport;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;
use Webkul\Sale\Jobs\RunZohoImport;
use Webkul\Sale\Models\ZohoImportRun;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

/**
 * The Super Admin import page's three steps - propose, start, run - in one
 * place, so the page, the queued job and the tests go through the same
 * checks. The work itself is ZohoImporter and ZohoComparison, exactly as the
 * zoho:import and zoho:compare commands use them.
 *
 * Every step re-checks the actor: a Livewire call can be replayed by anyone
 * signed in, so hiding the page is not the boundary.
 */
class ZohoImportRunner
{
    /** Largest export accepted, in bytes (HiTrace's full history is ~3 MB). */
    public const MAX_BYTES = 10 * 1024 * 1024;

    public function __construct(
        protected MappingProposer $proposer,
        protected ZohoImporter $importer,
        protected ZohoComparison $comparison,
        protected CompanyContext $context,
    ) {}

    /**
     * Only an active Super Admin may import into a company. Not a
     * Multi-Company Admin, not a Company Admin: the import writes a whole
     * history into the books, for any client (user, 2026-11-25).
     */
    public static function mayImport(?object $user): bool
    {
        return $user instanceof User && $user->is_active && $user->isSuperAdmin();
    }

    public function assertMayImport(?object $user): void
    {
        if (! static::mayImport($user)) {
            throw new AuthorizationException('Only an active Super Admin may import from Zoho Books.');
        }
    }

    /**
     * Read the uploaded export, check it, and propose its mapping.
     *
     * @param  array{numbering?: string, skip_unused_samples?: bool}  $options
     */
    public function propose(?object $user, int $companyId, string $json, array $options = [], string $mode = ZohoImportRun::MODE_IMPORT): ZohoImportRun
    {
        $this->assertMayImport($user);

        if (strlen($json) > self::MAX_BYTES) {
            throw new ZohoImportException('The export is larger than 10 MB.');
        }

        $company = Company::withoutGlobalScopes()->find($companyId);

        if (! $company) {
            throw new ZohoImportException('No company with that id.');
        }

        $export = ZohoExport::fromJson($json);

        if ($export->currency() !== (string) $company->currency?->name) {
            throw new ZohoImportException("Zoho's currency ({$export->currency()}) is not the company's ({$company->currency?->name}).");
        }

        $mode = $mode === ZohoImportRun::MODE_COMPARE ? ZohoImportRun::MODE_COMPARE : ZohoImportRun::MODE_IMPORT;
        $load = ZohoImporter::initialLoad((int) $company->id);

        // Refused here already, so nobody reviews a mapping for an import
        // that cannot run (the importer refuses it again).
        if ($mode === ZohoImportRun::MODE_IMPORT && $load) {
            throw new ZohoImportException(ZohoImporter::initialLoadDoneMessage($load->cutoff_at));
        }

        // A parallel-run check reads Zoho's accounts through the mapping the
        // initial load followed, so both sides are grouped the same way.
        $mapping = $mode === ZohoImportRun::MODE_COMPARE && filled($load?->mapping)
            ? $load->mapping
            : $this->context->runFor($company->id, fn (): array => $this->proposer->propose($company, $export, $options));

        // An upload nobody started is not kept: its export is client data,
        // and only the newest proposal can be started anyway.
        ZohoImportRun::query()
            ->where('status', ZohoImportRun::PROPOSED)
            ->update(['status' => ZohoImportRun::FAILED, 'export' => null, 'message' => 'Replaced by a newer upload.', 'finished_at' => now()]);

        return ZohoImportRun::query()->create([
            'company_id' => $company->id,
            'user_id'    => $user->getKey(),
            'mode'       => $mode,
            'status'     => ZohoImportRun::PROPOSED,
            'export'     => $json,
            'mapping'    => $mapping,
        ]);
    }

    /**
     * The Super Admin approved the table on the page: queue the import.
     */
    public function start(?object $user, ZohoImportRun $run): ZohoImportRun
    {
        $this->assertMayImport($user);

        if ($run->status !== ZohoImportRun::PROPOSED || blank($run->export)) {
            throw new ZohoImportException('This import was already started; propose it again to run it again.');
        }

        // One run at a time per company: two would race for the same
        // documents, and the second could not tell the first's from the
        // company's own.
        $busy = ZohoImportRun::query()
            ->where('company_id', $run->company_id)
            ->whereIn('status', [ZohoImportRun::QUEUED, ZohoImportRun::RUNNING])
            ->exists();

        if ($busy) {
            throw new ZohoImportException('An import into this company is already running.');
        }

        if ($run->mode === ZohoImportRun::MODE_IMPORT && ($load = ZohoImporter::initialLoad((int) $run->company_id))) {
            throw new ZohoImportException(ZohoImporter::initialLoadDoneMessage($load->cutoff_at));
        }

        $mapping = $run->mapping;
        $mapping['approved'] = true;
        $mapping['approved_by'] = $user->getKey();
        $mapping['approved_at'] = now()->toIso8601String();

        $run->update(['status' => ZohoImportRun::QUEUED, 'mapping' => $mapping, 'message' => null]);

        app(SecurityAuditLogger::class)->record('sales.zoho_import.started', $run, $user, (int) $run->company_id);

        RunZohoImport::dispatch($run->id);

        return $run->refresh();
    }

    /**
     * The queued job: import, compare, keep the result, forget the export.
     */
    public function execute(ZohoImportRun $run): ZohoImportRun
    {
        if ($run->status !== ZohoImportRun::QUEUED || blank($run->export)) {
            return $run;
        }

        $run->update(['status' => ZohoImportRun::RUNNING, 'started_at' => now()]);

        try {
            $company = Company::withoutGlobalScopes()->findOrFail($run->company_id);
            $export = ZohoExport::fromJson((string) $run->export);

            $compareOnly = $run->mode === ZohoImportRun::MODE_COMPARE;

            // A compare writes nothing: it is the parallel-run check.
            $counts = $compareOnly ? [] : $this->importer->import($company, $export, $run->mapping);

            // An import keeps customers numbered; a parallel-run check names
            // them, because the person reading it has to find the document.
            $comparison = $this->context->runFor($company->id, fn (): array => [
                'rows'      => $this->comparison->compare($company, $export, $run->mapping, anonymous: ! $compareOnly),
                'unmatched' => $this->comparison->unmatched($company, $export, $run->mapping, anonymous: ! $compareOnly),
            ]);

            $run->update([
                'status'      => ZohoImportRun::FINISHED,
                // The cut-off a later import is held to (user, 2026-11-26).
                'cutoff_at'   => $compareOnly ? null : (ZohoImporter::initialLoad((int) $company->id)?->cutoff_at
                    ?? ($export->exportedAt() ? Carbon::parse($export->exportedAt())->utc() : now()->utc())),
                'counts'      => $counts,
                'comparison'  => $comparison,
                'finished_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $this->fail($run, $exception);
        } finally {
            $run->forceFill(['export' => null])->save();
        }

        app(SecurityAuditLogger::class)->record(
            'sales.zoho_import.'.$run->status,
            $run,
            User::query()->find($run->user_id),
            (int) $run->company_id,
            after: [
                'counts'      => $run->counts,
                'mode'        => $run->mode,
                'differences' => collect($run->comparison['rows'] ?? [])->where('status', 'DIFF')->count(),
                'unmatched'   => count($run->comparison['unmatched'] ?? []),
            ],
        );

        return $run->refresh();
    }

    public function fail(ZohoImportRun $run, Throwable $exception): void
    {
        // A ZohoImportException names the document and why; anything else
        // may quote a whole SQL statement with client data, so only its
        // first part is kept, and nothing goes to the logs or Sentry.
        $message = $exception instanceof ZohoImportException
            ? $exception->getMessage()
            : Str::limit(Str::before($exception->getMessage(), ' (Connection:'), 300);

        $run->forceFill([
            'status'      => ZohoImportRun::FAILED,
            'message'     => $message,
            'finished_at' => now(),
            'export'      => null,
        ])->save();
    }
}
