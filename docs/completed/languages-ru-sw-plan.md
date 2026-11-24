# Russian and Swahili

Status: built 2026-11-23 on `feature/languages-ru-sw`, for the next monthly
release. Not a hotfix, and not in production until that release.

## Goal

Add Russian (`ru`) and Swahili (`sw`) to the languages a user can choose,
alongside English, Arabic, Spanish, French and Portuguese (Brazil).

## What it takes

French was added the same way on 2026-09-14 (27 language folders); follow
that change.

1. Add both to `config/app.php` `supported_locales` (label, native name,
   flag, `rtl => false`). Every language switcher reads that list.
2. Create a `ru` and an `sw` folder beside each plugin's `resources/lang/en`
   and translate every file, keeping the English structure line for line.
   The translation structure check compares them.
3. Quoted numeric keys (`'4' =>`) and escaped apostrophes (`\'`), as in
   project memory. Run `php -l` on every file in the Sail image.
4. Translate the in-app guides (`plugins/webkul/support/resources/data/guide*.json`)
   once the guide rewrite is finished, not before.
5. Check money, date and number formats for both locales on the dashboards
   and on printed documents.

## Decisions (user, 2026-11-22)

- **D-LANG-1:** one Swahili, `sw` (standard Swahili for Kenya and Tanzania),
  not separate `sw_KE` / `sw_TZ`.
- **D-LANG-2:** release, review later. Ship machine-assisted translations,
  say so in the CHANGELOG entry, and let a native speaker correct them when
  one is available. English stays one click away.

## Verification

Translation structure test, the full suite of each plugin touched, and a
browser check in each language on login, Overview, an invoice and its PDF.

## Handoff (2026-11-23)

**Flags.** The switcher does not render the `flag` code by itself. It asks
for `public/flags/{code}.svg`, and the files that already existed are `en`,
`ar`, `es` and `pt_BR` (French has `flag => fr` and no svg, so it shows no
flag). Russian uses `ru`, and that file was added. Swahili stays the locale
`sw` (D-LANG-1). Its flag is `tz`, Tanzania, because Swahili is that
country's national language. `locale_flag_code()` uses the configured flag
when that svg exists, and otherwise the locale code, so the existing four
flags keep working.

**Vendor.** Filament already ships `ru` and `sw` for actions, forms, tables,
notifications, support, schemas and the panel. Infolists and widgets ship
`ru` and not `sw`; those Swahili files are overrides in `lang/vendor`.
Filament Shield already ships `ru` (and the project already had
`lang/vendor/filament-shield/ru`). Swahili for Shield is a new override
beside the French one. Carbon already has `ru.php` and `sw.php`.

**Guides.** Not translated. The rewrite is not finished. Plan #1 does not
mark an in-app guide rewrite done (the operator guide in
`docs/saas-operations.md` is a different document). The dashboard plan still
says the guide wording is open. The 2026-10-23 change left the three guide
files in English until a translation was approved, and this plan says not
before the rewrite.

**Formats.** `DashboardPeriod::browserLocale()` maps only `en` to `en-GB`.
`ru` and `sw` pass through unchanged, which is what the browser and Carbon
expect. `money()` and `compact_money()` were reading `config('app.locale')`,
which stays the default after `SetLocale` calls `App::setLocale()`. They now
use the active locale, with the same Latin-digit rule as before (Arabic still
formats as English). Counts on the dashboard tiles use the same locale.
Invoice PDFs use DejaVu Sans (`PDFHandler` and the invoice blade). The font
file is the full DejaVu Sans (757 KB) and contains Cyrillic, so Russian does
not need a different font.

**Translations.** Machine-assisted (D-LANG-2), written in this session, not
sent to an external service. Placeholders and brand names (DEKA and the
product names) are unchanged. A native speaker should review before anyone
treats the wording as final, especially longer sentences. A few lines are
still English because they are PHP expressions (`$dateFilters`,
`$tripColumns`), not sentences. About 3,600 short strings had no glossary
hit and stayed in English; they are the ones to correct first.

**Verification actually run.** Host PHP 8.3.2 parsed every `ru` and `sw`
file with `token_get_all(..., TOKEN_PARSE)`: 2,184 files, 0 failures. `php -l`
on `config/app.php`, `helpers.php`, `HasRtlSupport.php` and
`HasDashboardMetrics.php` reported no syntax errors. The same line-structure
rule as `translations:check` (line count, then indent and key) matched
English for every `ru` and `sw` file: 0 missing, 0 mismatched. The plugin
suites were not run here; they are left for a local run on
`aureuserp_testing_wp_lang`. Browser check was not run: this worktree is not
the app being served.

## Handoff (2026-11-24): retranslation

*Supersedes the wording quality notes above.* The 2026-11-23 files were
glossary word swaps. Every `ru` and `sw` string is now translated from the
English by ten agents under one brief and glossary, plus the strings added
on develop since. `php -l` on all 2,186 files: 0 errors. Structure and keys
match English for every file. Strings still carrying English: Russian 186
of 14,757 (codes and brand names), Swahili 12. English source mistakes found
while translating are corrected in all seven locales (details in
`docs/change-log.md`). Not run, as the user asked: the plugin suites,
`translations:check` and a browser check. Merge when the suites are green.
