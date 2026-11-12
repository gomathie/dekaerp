<?php

namespace Webkul\Support\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Support\Filament\Pages\WhatsNew;

/**
 * Release notes as a PDF, for everyone who can open the page (D-WN-4).
 * Reuses the page's parser: never a second read of CHANGELOG.md, which is
 * how the two copies would drift.
 */
class WhatsNewPdfController
{
    public function __invoke(?string $release = null): Response
    {
        abort_unless(WhatsNew::canAccess(), 403);

        $releases = WhatsNew::loadReleases();

        if ($release !== null) {
            $releases = array_values(array_filter($releases, fn (array $candidate): bool => $candidate['id'] === $release));

            abort_if($releases === [], 404);
        }

        $pdf = Pdf::loadView('support::pdfs.whats-new', [
            'releases' => $releases,
            'app'      => (string) config('app.name', 'ERP'),
        ])
            ->setPaper('a4')
            ->setOption('isFontSubsettingEnabled', true);

        $name = 'whats-new'.($release ? '-'.$release : '').'.pdf';

        // Inline, so it opens in a new tab instead of downloading blindly.
        return $pdf->stream($name);
    }
}
