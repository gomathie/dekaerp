<?php

namespace Webkul\Logistics\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Support\Models\Company;

/**
 * PILOT Telematics API client (User API v2).
 *
 * Authentication is credentials-per-request (no session or token): every call
 * sends username + password as multipart form fields alongside the command.
 *
 * Endpoint: POST https://{server}/api/CRIT/api.php?node={node}
 *
 * Credentials are stored encrypted in logistics_company_settings; they are
 * decrypted by Eloquent before reaching this class and must never be logged.
 */
class PilotClient
{
    /**
     * Test whether the stored credentials are accepted by the PILOT server.
     *
     * Returns true when the server responds with a non-error HTTP status and
     * a parseable JSON body. Throws on network failure so the caller can
     * surface a user-friendly message.
     *
     * @throws ConnectionException
     */
    public function testConnection(CompanySetting $settings): bool
    {
        $response = $this->post($settings, 'api_objectlist');

        return $response->successful() && is_array($response->json());
    }

    /**
     * Pull vehicles from PILOT and upsert them into logistics_vehicles.
     *
     * Matches by registration number (PLATE) within the company. On a match,
     * updates the name, telematics_device_ref (IMEI), and last known location.
     * On no match, creates a new active vehicle. Soft-deleted vehicles are
     * skipped to avoid accidentally restoring archived records.
     *
     * Returns an array with keys `created` and `updated` (counts).
     *
     * @return array{created: int, updated: int, skipped: int}
     *
     * @throws ConnectionException
     * @throws \RuntimeException
     */
    public function syncFleet(Company $company, CompanySetting $settings): array
    {
        $response = $this->post($settings, 'api_objectlist');

        if (! $response->successful()) {
            throw new \RuntimeException(__('logistics::services/pilot-client.http-error', ['status' => $response->status()]));
        }

        $vehicles = $response->json();

        if (! is_array($vehicles)) {
            throw new \RuntimeException(__('logistics::services/pilot-client.invalid-response'));
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($vehicles as $row) {
            $plate = trim((string) ($row['PLATE'] ?? ''));

            if ($plate === '') {
                $skipped++;

                continue;
            }

            $existing = Vehicle::query()
                ->where('company_id', $company->id)
                ->where('registration_no', $plate)
                ->first();

            $locationFields = $this->extractLocation($row);

            if ($existing) {
                $existing->update(array_merge([
                    'name'                  => $row['NAME'] ?? $existing->name,
                    'telematics_device_ref' => $row['IMEI'] ?? $existing->telematics_device_ref,
                ], $locationFields));

                $updated++;
            } else {
                Vehicle::create([
                    'registration_no'       => $plate,
                    'name'                  => $row['NAME'] ?? $plate,
                    'telematics_device_ref' => $row['IMEI'] ?? null,
                    'is_active'             => true,
                    'company_id'            => $company->id,
                    'creator_id'            => Auth::id(),
                ] + $locationFields);

                $created++;
            }
        }

        return compact('created', 'updated', 'skipped');
    }

    /**
     * Extract GPS coordinates from a PILOT vehicle row.
     *
     * Returns only the fields where a non-zero value is present. A (0, 0)
     * coordinate is treated as absent — PILOT returns zeros for vehicles that
     * have not yet reported a position.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function extractLocation(array $row): array
    {
        $lat = isset($row['LATITUDE']) ? (float) $row['LATITUDE'] : null;
        $lng = isset($row['LONGITUDE']) ? (float) $row['LONGITUDE'] : null;

        if ($lat === null || $lng === null || ($lat === 0.0 && $lng === 0.0)) {
            return [];
        }

        return [
            'last_latitude'   => $lat,
            'last_longitude'  => $lng,
            'last_located_at' => Carbon::now(),
        ];
    }

    /**
     * POST a command to the PILOT CRIT API.
     *
     * Credentials are sent as multipart form fields; they must not appear in
     * any log (HTTP client logging must be disabled or redacted at the app
     * level for production).
     */
    private function post(CompanySetting $settings, string $functionName): \Illuminate\Http\Client\Response
    {
        $server = rtrim((string) $settings->pilot_server, '/');
        $node   = (int) ($settings->pilot_node ?: 1);

        return Http::timeout(15)
            ->asMultipart()
            ->post("{$server}/api/CRIT/api.php?node={$node}", [
                ['name' => 'username',     'contents' => (string) $settings->pilot_username],
                ['name' => 'password',     'contents' => (string) $settings->pilot_password],
                ['name' => 'functionName', 'contents' => $functionName],
            ]);
    }
}
