<?php

namespace Webkul\Partner\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class ContactImporter
{
    /**
     * Rows are imported inside the web request rather than on the queue, so
     * the upload is capped. Whether a queue worker runs is a deployment
     * setting, and an import that silently waits forever for a worker is worse
     * than one that refuses a file that is too large.
     */
    public const MAX_ROWS = 2000;

    /** @var array<string, int|null> */
    protected array $countryCache = [];

    /** @var array<string, int|null> */
    protected array $stateCache = [];

    /**
     * The spreadsheet columns, in the order the template writes them.
     *
     * This is the single source of truth for the feature: the template export
     * builds its heading row from it and the importer maps uploaded columns
     * with it, so a column cannot be added to one and forgotten in the other.
     *
     * `example` supplies the two sample rows in the downloadable template.
     *
     * @return array<string, array{required: bool, rules: string, example: array<int, string>}>
     */
    public static function columns(): array
    {
        return [
            'name'         => ['required' => true,  'rules' => 'required|string|max:255', 'example' => ['Acme Industries Ltd', 'Ama Mensah']],
            'account_type' => ['required' => false, 'rules' => 'nullable|string|in:individual,company', 'example' => ['company', 'individual']],
            'email'        => ['required' => false, 'rules' => 'nullable|email|max:255', 'example' => ['accounts@acme.test', 'ama.mensah@acme.test']],
            'phone'        => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['+233 30 123 4567', '']],
            'mobile'       => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['', '+233 24 765 4321']],
            'job_title'    => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['', 'Procurement Officer']],
            'website'      => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['https://acme.test', '']],
            'tax_id'       => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['C0001234567', '']],
            'reference'    => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['CUST-0001', 'CUST-0002']],
            'street1'      => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['12 Independence Avenue', '12 Independence Avenue']],
            'street2'      => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['', 'Suite 4']],
            'city'         => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['Accra', 'Accra']],
            'zip'          => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['GA-145-0001', 'GA-145-0001']],
            'country'      => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['Ghana', 'GH']],
            'state'        => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['Greater Accra', '']],
            'parent'       => ['required' => false, 'rules' => 'nullable|string|max:255', 'example' => ['', 'Acme Industries Ltd']],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function requiredColumns(): array
    {
        return array_keys(array_filter(
            static::columns(),
            fn (array $column): bool => $column['required'],
        ));
    }

    /**
     * Map an uploaded file's extension onto an explicit reader type.
     *
     * The uploaded file is read from Livewire's temporary directory, so the
     * path cannot be relied on to carry a meaningful extension. Naming the
     * reader explicitly avoids an inference that depends on it.
     */
    public static function readerTypeFor(?string $extension): string
    {
        return match (strtolower((string) $extension)) {
            'csv', 'txt' => ExcelFormat::CSV,
            'xls'        => ExcelFormat::XLS,
            default      => ExcelFormat::XLSX,
        };
    }

    /**
     * Import contacts from a spreadsheet.
     *
     * A bad row never costs the user the whole upload: each row is validated
     * and written on its own, and anything rejected is reported by its
     * spreadsheet row number so it can be corrected and re-uploaded.
     *
     * @return array{created: int, skipped: int, errors: array<int, string>}
     */
    public function import(string $path, ?string $readerType = null): array
    {
        $sheets = Excel::toArray(new class {}, $path, null, $readerType);

        $rows = $sheets[0] ?? [];

        if ($rows === []) {
            return ['created' => 0, 'skipped' => 0, 'errors' => [__('partners::services/contact-importer.errors.empty')]];
        }

        $map = $this->resolveHeadingMap(array_shift($rows));

        if ($map === []) {
            return ['created' => 0, 'skipped' => 0, 'errors' => [__('partners::services/contact-importer.errors.no-columns', [
                'columns' => implode(', ', array_keys(static::columns())),
            ])]];
        }

        if (! array_key_exists('name', $map)) {
            return ['created' => 0, 'skipped' => 0, 'errors' => [__('partners::services/contact-importer.errors.missing-name')]];
        }

        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            // +2: the heading row was shifted off, and spreadsheets are 1-based.
            $rowNumber = $index + 2;

            if ($this->rowIsBlank($row)) {
                continue;
            }

            if (($created + $skipped) >= static::MAX_ROWS) {
                $errors[] = __('partners::services/contact-importer.errors.max-rows', ['max' => static::MAX_ROWS]);

                break;
            }

            $attributes = $this->extract($row, $map);

            $validator = Validator::make(
                $attributes,
                array_map(fn (array $column): string => $column['rules'], static::columns()),
            );

            if ($validator->fails()) {
                $skipped++;
                $errors[] = __('partners::services/contact-importer.errors.row', [
                    'row'    => $rowNumber,
                    'reason' => implode(' ', $validator->errors()->all()),
                ]);

                continue;
            }

            $result = $this->importRow($attributes, $rowNumber);

            if ($result === null) {
                $created++;

                continue;
            }

            $skipped++;
            $errors[] = $result;
        }

        return compact('created', 'skipped', 'errors');
    }

    /**
     * Write one validated row, or return the reason it was not written.
     */
    protected function importRow(array $attributes, int $rowNumber): ?string
    {
        // Read duplicates past the global scopes on purpose. Partner carries
        // both CompanyScope and OwnershipScope, so a scoped lookup can report
        // "no match" for a contact that exists but is not visible to the
        // importing user - and then happily create a second one. Reporting a
        // skip the user cannot see is the lesser problem.
        if (filled($attributes['email'])) {
            $existing = Partner::query()
                ->withoutGlobalScopes()
                ->where('email', $attributes['email'])
                ->exists();

            if ($existing) {
                return __('partners::services/contact-importer.errors.duplicate-email', [
                    'row'   => $rowNumber,
                    'email' => $attributes['email'],
                ]);
            }
        }

        $countryId = $this->resolveCountryId($attributes['country']);

        if (filled($attributes['country']) && $countryId === null) {
            return __('partners::services/contact-importer.errors.unknown-country', [
                'row'     => $rowNumber,
                'country' => $attributes['country'],
            ]);
        }

        $stateId = $this->resolveStateId($attributes['state'], $countryId);

        if (filled($attributes['state']) && $stateId === null) {
            return __('partners::services/contact-importer.errors.unknown-state', [
                'row'   => $rowNumber,
                'state' => $attributes['state'],
            ]);
        }

        $parentId = $this->resolveParentId($attributes['parent']);

        if (filled($attributes['parent']) && $parentId === null) {
            return __('partners::services/contact-importer.errors.unknown-parent', [
                'row'    => $rowNumber,
                'parent' => $attributes['parent'],
            ]);
        }

        // company_id is deliberately left unset. Partner opts out of automatic
        // company assignment (autoAssignsCompany() returns false) and the
        // contact form has no owning-company field, so contacts are created
        // shared across companies. Stamping the active company here would make
        // imported contacts behave differently from typed ones.
        DB::transaction(function () use ($attributes, $countryId, $stateId, $parentId): void {
            Partner::query()->create([
                'name'         => $attributes['name'],
                'account_type' => $attributes['account_type'] ?: AccountType::INDIVIDUAL->value,
                'email'        => $attributes['email'] ?: null,
                'phone'        => $attributes['phone'] ?: null,
                'mobile'       => $attributes['mobile'] ?: null,
                'job_title'    => $attributes['job_title'] ?: null,
                'website'      => $attributes['website'] ?: null,
                'tax_id'       => $attributes['tax_id'] ?: null,
                'reference'    => $attributes['reference'] ?: null,
                'street1'      => $attributes['street1'] ?: null,
                'street2'      => $attributes['street2'] ?: null,
                'city'         => $attributes['city'] ?: null,
                'zip'          => $attributes['zip'] ?: null,
                'country_id'   => $countryId,
                'state_id'     => $stateId,
                'parent_id'    => $parentId,
            ]);
        });

        return null;
    }

    /**
     * Match the uploaded heading row against the known columns.
     *
     * Headings are matched case-insensitively and ignoring spaces, so both
     * "Job Title" and "job_title" land on the same column. Unknown columns are
     * ignored rather than refused, which keeps a file with an extra note
     * column usable.
     *
     * @return array<string, int>
     */
    protected function resolveHeadingMap(mixed $headingRow): array
    {
        if (! is_array($headingRow)) {
            return [];
        }

        $known = array_keys(static::columns());
        $map = [];

        foreach ($headingRow as $position => $heading) {
            $normalised = str_replace([' ', '-'], '_', strtolower(trim((string) $heading)));

            if (in_array($normalised, $known, true) && ! array_key_exists($normalised, $map)) {
                $map[$normalised] = $position;
            }
        }

        return $map;
    }

    /**
     * @param  array<string, int>  $map
     * @return array<string, string>
     */
    protected function extract(mixed $row, array $map): array
    {
        $row = is_array($row) ? $row : [];
        $attributes = [];

        foreach (array_keys(static::columns()) as $column) {
            $value = array_key_exists($column, $map) ? ($row[$map[$column]] ?? null) : null;

            $attributes[$column] = trim((string) $value);
        }

        $attributes['account_type'] = strtolower($attributes['account_type']);

        return $attributes;
    }

    protected function rowIsBlank(mixed $row): bool
    {
        if (! is_array($row)) {
            return true;
        }

        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function resolveCountryId(string $country): ?int
    {
        if ($country === '') {
            return null;
        }

        $key = strtolower($country);

        if (array_key_exists($key, $this->countryCache)) {
            return $this->countryCache[$key];
        }

        $id = Country::query()
            ->whereRaw('LOWER(name) = ?', [$key])
            ->orWhereRaw('LOWER(code) = ?', [$key])
            ->value('id');

        return $this->countryCache[$key] = $id === null ? null : (int) $id;
    }

    protected function resolveStateId(string $state, ?int $countryId): ?int
    {
        if ($state === '') {
            return null;
        }

        $key = strtolower($state).'|'.($countryId ?? '');

        if (array_key_exists($key, $this->stateCache)) {
            return $this->stateCache[$key];
        }

        $id = State::query()
            ->when($countryId !== null, fn ($query) => $query->where('country_id', $countryId))
            ->where(fn ($query) => $query
                ->whereRaw('LOWER(name) = ?', [strtolower($state)])
                ->orWhereRaw('LOWER(code) = ?', [strtolower($state)]))
            ->value('id');

        return $this->stateCache[$key] = $id === null ? null : (int) $id;
    }

    /**
     * Resolve the parent company contact by name.
     *
     * Read past the global scopes for the same reason as the duplicate check:
     * a scoped miss would silently file the contact with no parent.
     */
    protected function resolveParentId(string $parent): ?int
    {
        if ($parent === '') {
            return null;
        }

        $id = Partner::query()
            ->withoutGlobalScopes()
            ->whereRaw('LOWER(name) = ?', [strtolower($parent)])
            ->value('id');

        return $id === null ? null : (int) $id;
    }
}
