<?php

namespace Webkul\Support\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;

/**
 * The parts every printed document shares (TRACE365, 2026-11-23, plan #10
 * R-8): the issuing company's header with its logo, and the customer or
 * supplier block. Each document (invoice, bill, quotation...) builds its own
 * lines and totals and renders them through the `support::pdfs.document`
 * layout, so the layout exists once instead of five near-identical copies.
 */
class PrintedDocument
{
    /** A larger logo is left out rather than bloating every PDF. */
    protected const MAX_LOGO_BYTES = 1_500_000;

    /**
     * @return array{name: string, lines: list<string>, tax_id: ?string, phone: ?string, email: ?string, website: ?string, logo: ?string}
     */
    public static function company(?Company $company): array
    {
        $partner = $company?->partner;

        return [
            'name'    => (string) ($company?->name ?? ''),
            'lines'   => static::addressLines($partner),
            'tax_id'  => filled($company?->tax_id) ? (string) $company->tax_id : null,
            'phone'   => filled($company?->phone) ? (string) $company->phone : null,
            'email'   => filled($company?->email) ? (string) $company->email : null,
            'website' => filled($company?->website) ? (string) $company->website : null,
            'logo'    => static::logo($company),
        ];
    }

    /**
     * The company's branding colour for the table header, when it is a plain
     * hex colour: it is printed into a style rule, so anything else is
     * refused rather than escaped. A light colour falls back to the default
     * too: the header text is white, and a pale grey branding colour left it
     * unreadable (production, 2026-11-23).
     */
    public static function accent(?Company $company): string
    {
        $default = '#1f2937';
        $color = trim((string) ($company?->color ?? ''));

        if (preg_match('/^#[0-9a-fA-F]{6}$/', $color) !== 1) {
            return $default;
        }

        [$red, $green, $blue] = array_map(fn (string $hex): float => hexdec($hex) / 255, str_split(substr($color, 1), 2));

        // Perceived lightness; above this, white text no longer reads.
        $lightness = 0.299 * $red + 0.587 * $green + 0.114 * $blue;

        return $lightness > 0.6 ? $default : $color;
    }

    /**
     * A quantity without trailing zeros: 1, 2.5, not 1.0000.
     */
    public static function quantity(float|int|string|null $quantity): string
    {
        $formatted = number_format((float) $quantity, 4, '.', ',');

        return rtrim(rtrim($formatted, '0'), '.');
    }

    /**
     * @return array{name: string, lines: list<string>, tax_id: ?string, email: ?string, phone: ?string}
     */
    public static function party(?Partner $partner): array
    {
        return [
            'name'   => (string) ($partner?->name ?? ''),
            'lines'  => static::addressLines($partner),
            'tax_id' => filled($partner?->tax_id) ? (string) $partner->tax_id : null,
            'email'  => filled($partner?->email) ? (string) $partner->email : null,
            'phone'  => filled($partner?->phone) ? (string) $partner->phone : null,
        ];
    }

    /**
     * Street, "city, state zip" and country, skipping what is empty. The old
     * templates printed ", ," for a contact with no address.
     *
     * @return list<string>
     */
    public static function addressLines(?Partner $partner): array
    {
        if (! $partner) {
            return [];
        }

        $street = collect([$partner->street1, $partner->street2])->filter(fn ($part): bool => filled($part))->implode(', ');
        $place = collect([$partner->city, $partner->state?->name, $partner->zip])->filter(fn ($part): bool => filled($part))->implode(', ');

        return collect([$street, $place, $partner->country?->name])
            ->filter(fn ($line): bool => filled($line))
            ->map(fn ($line): string => (string) $line)
            ->values()
            ->all();
    }

    /**
     * The company logo as a data URI, so the PDF needs no remote fetch.
     *
     * Read from the document company's own folder: on tenant-s3 the public
     * disk prefixes keys with the *session's* company, so a Super Admin
     * printing another company's invoice would read the wrong folder (the
     * same reason as Partner::getAvatarUrlAttribute()). The plain s3 disk has
     * no prefix, as SecureStorageController uses it. Any failure prints the
     * document without a logo rather than failing it.
     */
    public static function logo(?Company $company): ?string
    {
        $path = $company?->partner?->avatar;

        if (blank($path) || str_contains((string) $path, '..')) {
            return null;
        }

        try {
            if (config('filesystems.disks.public.driver') === 'tenant-s3') {
                $root = trim((string) config('filesystems.disks.public.root'), '/');
                $disk = Storage::disk('s3');
                $path = ($root !== '' ? $root.'/' : '')."companies/{$company->getKey()}/".ltrim((string) $path, '/');
            } else {
                $disk = Storage::disk('public');
            }

            if (! $disk->exists($path) || $disk->size($path) > self::MAX_LOGO_BYTES) {
                return null;
            }

            $bytes = $disk->get($path);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer((string) $bytes);

            if (! in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/webp', 'image/svg+xml'], true)) {
                return null;
            }

            return 'data:'.$mime.';base64,'.base64_encode((string) $bytes);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }
}
