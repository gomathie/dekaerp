<?php

namespace Webkul\Logistics\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Webkul\Logistics\Enums\ProofCaptureChannel;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Services\PodData;
use Webkul\Logistics\Services\StopLinkService;

/**
 * The only public entry point in this plugin (D15, WP-5b).
 *
 * No login, so the token is the credential: everything reachable from here is
 * the one stop that token was issued for. Nothing accepts an id from the
 * request - the stop and the shipment come from the link, never from input, so
 * there is no parameter to tamper with to reach another company's record.
 *
 * The signature arrives as a PNG data URL from a canvas and is converted to an
 * uploaded file here, because DeliveryService validates real files (mimetypes
 * and size) rather than trusting a string.
 */
class StopLinkController extends Controller
{
    public function __construct(protected StopLinkService $links) {}

    public function show(string $token, StopLinkService $links): View
    {
        $link = $links->resolve($token);
        $settings = CompanySetting::forCompany((int) $link->company_id);

        return view('logistics::stop-link.show', [
            'token'              => $token,
            'stop'               => $links->stopFor($link),
            'askForRecipientId'  => (bool) $settings->capture_recipient_id,
            'photoRequired'      => (bool) $settings->require_pod_photo,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse|View
    {
        $validated = $request->validate([
            'recipient_name'         => ['required', 'string', 'max:255'],
            'recipient_id_reference' => ['nullable', 'string', 'max:100'],
            'notes'                  => ['nullable', 'string', 'max:2000'],
            'photo'          => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
            'signature'      => ['nullable', 'string', 'max:2000000'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy_m'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        $signature = $this->signatureFile($validated['signature'] ?? null);

        try {
            $this->links->capture($token, new PodData(
                recipientName: $validated['recipient_name'],
                receivedAt: now(),
                notes: $validated['notes'] ?? null,
                photo: $request->file('photo'),
                signature: $signature,
                capturedVia: ProofCaptureChannel::STOP_LINK,
                recipientIdReference: $validated['recipient_id_reference'] ?? null,
                latitude: isset($validated['latitude']) ? (float) $validated['latitude'] : null,
                longitude: isset($validated['longitude']) ? (float) $validated['longitude'] : null,
                accuracyM: isset($validated['accuracy_m']) ? (float) $validated['accuracy_m'] : null,
            ));
        } finally {
            // The signature is decoded to a temp file, and storing it copies
            // rather than moves, so without this every capture leaves one
            // behind. On a public endpoint that is a slow way to fill a disk,
            // and in the finally so a rejected submission cleans up too.
            if ($signature && is_file($signature->getPathname())) {
                @unlink($signature->getPathname());
            }
        }

        // No redirect back to the link: it has just been spent, so following it
        // again would show the "no longer valid" page and read as a failure.
        return view('logistics::stop-link.done');
    }

    /**
     * Turn the canvas signature (a PNG data URL) into a real file.
     *
     * Kept strict rather than permissive: only a base64 PNG data URL is
     * accepted, decoding is strict, and the result is handed to
     * DeliveryService as a file so its mimetype and size rules apply to it
     * exactly as they do to the photo. Anything else is treated as "no
     * signature" rather than as an error - the signature is optional, and a
     * driver at the door should not be blocked by a canvas quirk.
     */
    protected function signatureFile(?string $dataUrl): ?UploadedFile
    {
        if (blank($dataUrl) || ! str_starts_with($dataUrl, 'data:image/png;base64,')) {
            return null;
        }

        $decoded = base64_decode(substr($dataUrl, strlen('data:image/png;base64,')), true);

        if ($decoded === false || $decoded === '') {
            return null;
        }

        $path = tempnam(sys_get_temp_dir(), 'pod-signature-');

        if ($path === false || file_put_contents($path, $decoded) === false) {
            return null;
        }

        return new UploadedFile($path, 'signature.png', 'image/png', null, true);
    }
}
