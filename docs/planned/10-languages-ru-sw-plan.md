# Russian and Swahili

Status: planned (user, 2026-11-19). Not started.

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

## Decisions to confirm with the user

- Swahili: one variant for Kenya and Tanzania, or separate ones.
- Whether a native speaker reviews each language before release.

## Verification

Translation structure test, the full suite of each plugin touched, and a
browser check in each language on login, Overview, an invoice and its PDF.
