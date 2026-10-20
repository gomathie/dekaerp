<?php

namespace Webkul\Logistics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ViewErrorBag;
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

    /**
     * The fragment-based landing page (WP-5b).
     *
     * This is the page whose URL is shared with the driver. The token lives in
     * the URL fragment and is read by JavaScript, so it is never transmitted in
     * an HTTP request line and never appears in access logs.
     */
    public function landing(): Response
    {
        return $this->secureView('logistics::stop-link.landing');
    }

    /**
     * Exchange a fragment token (from the POST body) for the capture form.
     *
     * The token arrives as a POST field, not a route parameter, so it is not
     * in the request line and not in any log. The response is the full capture
     * form, with the token embedded in a hidden field so the subsequent submit
     * can pass it back through the same safe channel.
     */
    public function exchange(Request $request): Response
    {
        $token = (string) $request->input('token', '');
        $link = $this->links->resolve($token);
        $settings = CompanySetting::forCompany((int) $link->company_id);

        return $this->secureView('logistics::stop-link.form', [
            'token'             => $token,
            'stop'              => $this->links->stopFor($link),
            'askForRecipientId' => (bool) $settings->capture_recipient_id,
            'photoRequired'     => (bool) $settings->require_pod_photo,
        ]);
    }

    /**
     * Capture proof of delivery from the fragment-based form.
     *
     * The token arrives in the POST body alongside the form fields. On
     * validation failure the form is re-rendered directly (no redirect)
     * so the driver is not sent somewhere without their token.
     */
    public function submit(Request $request): Response
    {
        $token = (string) $request->input('token', '');

        // Verify the token before touching the form data: an invalid token gets
        // the same 404 as an expired link, not a validation response.
        $link = $this->links->resolve($token);
        $settings = CompanySetting::forCompany((int) $link->company_id);

        $validator = Validator::make($request->all(), [
            'recipient_name'         => ['required', 'string', 'max:255'],
            'recipient_id_reference' => ['nullable', 'string', 'max:100'],
            'notes'                  => ['nullable', 'string', 'max:2000'],
            'photo'          => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
            'signature'      => ['nullable', 'string', 'max:2000000'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy_m'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        if ($validator->fails()) {
            $errorBag = (new ViewErrorBag)->put('default', $validator->errors());

            // Flash old input so old() works in the form, and put errors in
            // both the session (for assertSessionHasErrors in tests) and the
            // view factory (so $errors is populated without a redirect).
            $request->flash();
            session()->flash('errors', $errorBag);
            view()->share('errors', $errorBag);

            return $this->secureView('logistics::stop-link.form', [
                'token'             => $token,
                'stop'              => $this->links->stopFor($link),
                'askForRecipientId' => (bool) $settings->capture_recipient_id,
                'photoRequired'     => (bool) $settings->require_pod_photo,
            ])->setStatusCode(422);
        }

        $validated = $validator->validated();
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
            if ($signature && is_file($signature->getPathname())) {
                @unlink($signature->getPathname());
            }
        }

        return $this->secureView('logistics::stop-link.done');
    }

    public function show(string $token, StopLinkService $links): Response
    {
        $link = $links->resolve($token);
        $settings = CompanySetting::forCompany((int) $link->company_id);

        return $this->secureView('logistics::stop-link.show', [
            'token'              => $token,
            'stop'               => $links->stopFor($link),
            'askForRecipientId'  => (bool) $settings->capture_recipient_id,
            'photoRequired'      => (bool) $settings->require_pod_photo,
        ]);
    }

    public function store(Request $request, string $token): Response
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
        return $this->secureView('logistics::stop-link.done');
    }

    /**
     * The URL is a credential and the page contains shipment details. Keep it
     * out of browser/proxy caches and prevent UI-redress capture in a frame.
     *
     * @param  array<string, mixed>  $data
     */
    protected function secureView(string $view, array $data = []): Response
    {
        return response()->view($view, $data)->withHeaders([
            'Cache-Control'           => 'no-store, private',
            'Pragma'                  => 'no-cache',
            'Referrer-Policy'         => 'no-referrer',
            'X-Content-Type-Options'  => 'nosniff',
            'X-Frame-Options'         => 'DENY',
        ]);
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
