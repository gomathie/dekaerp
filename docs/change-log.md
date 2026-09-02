# Change Log (Agent-Assisted Work)

A running record of code changes made during AI-agent-assisted work sessions,
with the reasoning behind each one. This is distinct from
[`CHANGELOG.md`](../CHANGELOG.md) at the repo root, which tracks user-facing
release notes per version. See [`docs/agent-reminders.md`](agent-reminders.md)
for the task/question log this change log is paired with.

---

## 2026-09-03 (later)

### Selective backport from upstream v1.6.0

Upstream released v1.6.0; a copy sits at `aureuserp-1.6.0/` (gitignored). 651
files differ under `plugins/`, most of it v1.6.0's "refactored resource
folder/file structure across all plugins". That restructure is not worth
taking into a fork this diverged, so this is a cherry-pick of the fixes that
matter, each checked against what the fork already has.

**[Security] Chatter change-summary XSS.** Upstream escaped the values in the
change summary; the fork still rendered them with `{!! !!}`
(`content-text-entry.blade.php` lines 164/181). Field values are user-supplied
- a partner name or note containing markup executed in the browser of any
admin who opened that record's chatter. Now `{{ }}`.

Deliberately *not* taken wholesale: the same file's other diff is upstream
still using `Storage::url()` where the fork uses
`Storage::disk('public')->url()`. Copying the file would have reverted the
tenant-S3 fix from `c6d188ee0`. Only the two escaping changes were applied.

**Pay modal bank field (#1481), partially.** `PaymentRegister::
getBatchAvailablePartnerBanks()` did `collect($journal->bankAccount)` -
passing a model to `collect()` wraps its *attributes*, so callers got a
collection of columns and `pluck('id')` matched nothing. That is the blank
bank field. Also made the partner lookup null-safe. The company filter line is
identical upstream and downstream, so this merged cleanly.

The rest of upstream's `PayAction` rework was **not** applied: it replaces the
fork's company-scoped journal filter with its own resolution helpers.
Different semantics in a multi-company install, and untestable from here.

**Saved filter views (#1490).** `HasTableViews` gains a
`shouldMountInteractsWithTable` guard and fills the filter form / handles
deferred filters. The fork's copy was otherwise identical to upstream, so the
file was taken as-is.

**Currency deletion (#1514).** `EditCurrency` now catches the FK
`QueryException` and shows a notification instead of a 500. Upstream shipped
the new strings for `en` only; `ar`, `es` and `pt_BR` were added here, nested
under `notification` to match the key the action actually calls
(`...delete.notification.error.title`) - verified by flattening all four files
and diffing key paths.

**Livewire nesting depth** raised 10 -> 30, fixing deeply nested repeater
errors.

**Second pass - the four remaining items.** One more fix applied; the other
three turned out to be feature ports rather than patches.

- *Applied:* `Company::saving()` did `$company->currency->update(...)`, which
  fatals when a company has no currency. Now `?->`.
- *#1500 company currency lock* - **could not be located in the v1.6.0
  source.** No `disabled()` on `currency_id` anywhere in the tree, no matching
  strings in the company or accounts lang files, and `EditCompany`'s only diff
  is an import swap. Either it lives somewhere the release note does not
  suggest, or the note overstates what shipped. Not applied; worth checking
  the upstream issue before assuming the fork lacks protection.
- *#193 attribute deletion* - the fork **already blocks** this; its lang file
  carries the error strings. v1.6.0 only enriches the message to name the
  blocking products, and that enrichment is part of the subsystem below.
- *#1187 / product-and-variant-in-use* - needs four new classes
  (`ProductInUseException`, `VariantInUseException`, `ProductUsageRegistry`,
  `VariantUsage`) plus registrations inside the `accounts`, `inventories` and
  `manufacturing` service providers, which all carry fork changes. ~12 files.
  **All-or-nothing:** with the registry unpopulated, `isInUse()` returns false
  and every guard silently passes - protection that looks present and is not.
  Left for a scoped change that can run the Pest suite.
- *#153 tax computation* - a 520-line diff covering v1.6.0's new custom tax
  formulas, batching and validation, on top of the resource restructure. A
  feature, not a fix.

**Checked and deliberately skipped:**

- *#1506 plugin install on Windows* - the fork is **ahead**. It already
  short-circuits `buildTimeoutCommand()` on Windows, and adds `--force` on
  migrate/db:seed, exit-code checks, and a configurable permission timeout
  that v1.6.0 still hardcodes at 60s. Upstream only moved the OS branch into
  `Package`. Taking it would have been a downgrade.
- *"Use the Webkul User model instead of App\Models\User"* - cosmetic here.
  `Webkul\Security\Models\User extends App\Models\User` in both versions, so
  `HasApiTokens` is inherited either way and the API is unaffected.
- The plugin-wide folder restructure, and the Filament 5.7.6 upgrade.

**Not verified at runtime** - same PHP 8.4 / database blocker as before. Lint
and Pint are clean, and the translation key parity was checked
programmatically, but the table-views and currency changes touch live UI paths
and want the Pest suite before deploying.

## 2026-09-03

### Review fix: public branding route was an unauthenticated arbitrary-file read

**Files:** `app/Http/Controllers/BrandingController.php`, `routes/web.php`,
`database/migrations/2026_09_03_000001_reset_invalid_branding_settings.php`

Reviewing a branding fix written elsewhere in the project. The diagnosis
behind it was correct - `settings` rows named uploaded logos that no longer
existed, and there was no route serving `branding/`, so the login page got a
404 and no logo. The middleware fallback and the cleanup migration were sound.
The route was not.

**The hole.** `Route::get('/branding/{path}')->where('path', '.*')`, no auth,
and the controller passed that raw `$path` to `Storage::disk('public')` *and*
to the raw `s3` disk, plus looped every company id looking for a match. Under
`tenant-s3` that disk holds invoice PDFs, quotation PDFs, chatter attachments
and avatars, so:

    GET /branding/companies/3/pdfs/invoice-27-08-2026.pdf

served another tenant's invoice to an anonymous caller, with filenames
(`invoice-DD-MM-YYYY.pdf`) that enumerate by date. The `str_contains($path,
'..')` guard was irrelevant - object keys do not need traversal. This bypassed
`SecureStorageController`, whose whole purpose is that authorization is by
company rather than URL secrecy.

**Fixed** by making the endpoint incapable of expressing anything but a
filename: the route takes `{filename}` constrained to `[A-Za-z0-9._-]+` (no
slashes), and the controller `basename()`s it anyway so the guarantee does not
rest on the route definition, rejects dotfiles, and only ever resolves
`branding/{filename}`. The raw-`$path` candidates and the company loop are
gone - which also removes a DB query plus one S3 HEAD per company on every
unauthenticated request to a missing asset.

Verified the constraint against the exploit paths directly: `logo.svg` and the
ULID filenames match; `companies/3/pdfs/...`, `pdfs/...`, `users/avatars/...`,
`_system/branding/...` and `../../.env` all fail to match.

Known consequence, and the right trade-off: a logo uploaded with a company in
context lives under `companies/{id}/branding/` and so resolves on
authenticated pages but not on the login page, which reads `_system/branding/`
and falls back to the bundled logo. An anonymous visitor has no company, and
guessing one by scanning tenants is what created the leak.

**Migration hardened too.** It reset any setting it could not find - but
migrations run with no company in context, so the public disk resolves
`_system/` and would have reported every company-scoped logo missing, wiping
valid settings. It now resolves keys under the row's own `company_id`, and
distinguishes `found` / `missing` / `unknown`: a store that cannot be reached
leaves the row alone instead of treating "could not check" as "not there".

## 2026-09-02 (later)

### API hardening ahead of giving clients access

**Files:** `plugins/webkul/plugin-manager/src/PackageServiceProvider.php`,
`app/Providers/AppServiceProvider.php`,
`app/Http/Middleware/EnforceApiTokenAbilities.php` (new), `bootstrap/app.php`,
`plugins/webkul/security/src/Http/Controllers/API/V1/AuthController.php`,
`config/api.php` (new), `config/scribe.php`, `.env.example`,
`docs/api-access.md` (new)

**Rate limiting — the API had none.** Plugin routes load through
`loadRoutesFrom()` in the shared `PackageServiceProvider`, which never applied
Laravel's `api` middleware group, so `throttle:api` reached none of them; only
`login` carried a throttle of its own. The `api` file is now registered inside
a `Route::middleware(['throttle:api', 'api.abilities'])` group at that one
place, so all nine plugins - and any added later - inherit it. The
`loadRoutesFrom()` cached-routes guard is mirrored explicitly, since going
around that helper also goes around its check.

Laravel defines no `api` limiter by default and this app never added one, so
`throttle:api` would have thrown rather than throttled. Defined it alongside
the existing `login` limiter, keyed on the Sanctum token id so one client's
integration cannot exhaust another's budget, at `API_RATE_LIMIT` (120/min).

**Token scoping.** `createToken('api-token')` passed no abilities, so every
token was `['*']`. `login` now accepts `token_name` and `abilities`
(`read`/`write`/`*`), enforced by `EnforceApiTokenAbilities`, which maps HTTP
method onto ability rather than annotating several hundred endpoints. It is
registered as the `api.abilities` alias so the plugin loader does not depend
on a class in `app/`. **`abilities` still defaults to `['*']`** - the API is
live and in use, so existing integrations must not break; scoping is opt-in.
Tokens holding `*` bypass the check entirely.

**JSON errors on the wrong prefix.** The eight handlers in `bootstrap/app.php`
tested `$request->is('api/*')`, which never matches `admin/api/v1/...`, so API
clients got HTML error pages unless they happened to send
`Accept: application/json`. Now `is('api/*', 'admin/api/*')`.

**Docs visibility made a decision rather than a default.** `/api/docs`
returned 200 unauthenticated, publishing the whole endpoint surface. Left
public - reasonable for a product customers integrate against - but now
switchable with `SCRIBE_DOCS_PRIVATE=true`, with the trade-off written down.

**Verification.** `php -l` on every touched file and `pint --dirty` clean.
`Route::middleware(...)->group($file)` confirmed against
`Router::loadRoutes()`/`RouteFileRegistrar`, which accept a path. Runtime
verification was **not** possible: `route:list` needs a database because
plugin route registration calls `isInstalled()`, and the only PHP 8.4 image
available locally carries another project's entrypoint (it ran `key:generate`
- confirmed it did not touch this repo's `.env`). The Pest suite, run
serially against a real database, is the gate this needs before deploying.

## 2026-09-02

### Sentry: finished the wiring that was still missing

**Files:**
- `.env.example`
- `docker/production/Dockerfile`
- `docker/production/entrypoint.sh`
- `docker/production/php.ini`

**Context:** Working from Sentry's onboarding doc for this project. Most of it
was already in place from `076735636` — `sentry/sentry-laravel` 4.27.0 is
installed, `config/sentry.php` is published, `Integration::handles()` is in
`bootstrap/app.php`, and the `sentry_logs` channel is defined in
`config/logging.php`. These are the remaining gaps.

**Excimer (profiling).** `pecl download excimer` + build, added to the
production image in the same layer style as imagick and placed before
`php-dev`/`php-pear` are purged. Wrapped in an `if/else` so a build failure
prints a warning and continues instead of failing the image: profiling is
optional and the SDK already degrades to a logged warning when the extension
is absent (`vendor/sentry/sentry/src/Profiling/Profiler.php:76`), so it should
not be able to block a deploy. Failure path simulated and confirmed to exit 0.

**Env knobs.** `SENTRY_PROFILES_SAMPLE_RATE` and `SENTRY_ENABLE_LOGS` added to
`.env.example`, both off by default, with comments on what turning each one on
costs. Logs also need `LOG_CHANNEL=stack` and `LOG_STACK=stderr,sentry_logs` —
the production entrypoint defaults `LOG_CHANNEL` to `stderr`, so naming the
flag alone does nothing.

**Entrypoint passthrough.** `SENTRY_*` container vars are now mirrored into
`.env` alongside the `APP_*` ones. Not strictly required — `clear_env = no` in
`php-fpm.conf` already exposes them to the workers, and Laravel's dotenv is
immutable so it never overwrites a real environment variable — but it matches
how every other runtime var is handled, and it stops `.env` from showing an
empty DSN on a container that is happily sending events.

**`zend.exception_ignore_args` — deliberately NOT set to Off.** Sentry's guide
asks for `Off` so stack traces carry function arguments. Set explicitly to
`On` instead, with the reasoning in the file: those arguments are customer
records, invoice payloads and credentials passed to auth calls, which is the
same data `SENTRY_SEND_DEFAULT_PII=false` was set to keep out of Sentry.
Errors still carry file, line and the full frame list. Flagged for the user
rather than decided silently.

**Still needs the user:** `SENTRY_LARAVEL_DSN` has to be set as an environment
variable on the host that runs the image. It is deliberately not committed —
`.env.example` keeps the empty placeholder, matching the existing convention.

## 2026-08-28

### Feature: In-app "What's New" page, reading CHANGELOG.md

**Files:**
- `plugins/webkul/support/src/Filament/Pages/WhatsNew.php` (new)
- `plugins/webkul/support/resources/views/pages/whats-new.blade.php` (new)
- `plugins/webkul/support/resources/lang/{en,ar,es,pt_BR}/filament/pages/whats-new.php` (new)
- `.dockerignore`

**What:** A new admin panel page (Help nav group, sorted above Help) that
renders `CHANGELOG.md` as collapsible release-note sections — one `<details>`
per version, latest expanded, each with its Features/Improvements/Fixes/
Upgrade groups. `CHANGELOG.md` stays the single source of truth; nothing is
duplicated into a second, easily-stale copy.

**Parsing:** `WhatsNew::parseChangelog()` walks the file line by line —
`# ` starts a new release (its version is the trailing `vX.Y.Z[-SUFFIX]`
token), `### ` starts a section (label = heading text with the leading emoji
stripped), `* ` appends an item to whichever section came last. Verified
against the live file with a standalone script (see
`docs/agent-reminders.md` — `php artisan` doesn't run locally right now):
correctly found all 10 releases (v1.0.0–v1.5.0) with matching item counts per
section. Items render through `Str::inlineMarkdown()` for inline formatting
(bold, code, links) rather than being duplicated as plain text.

**Caching:** `Cache::remember('support.whats-new.'.filemtime(...), ...)` —
keying on the file's mtime means an edit to `CHANGELOG.md` invalidates the
cache automatically (new key) without needing `cache:clear`, while repeat
requests on an unchanged file skip re-parsing ~800 lines of markdown.

**`.dockerignore` fix (would have shipped broken otherwise):** the file
excludes all `*.md` from the production image except `LICENSE` — including
`CHANGELOG.md`, which this page depends on at runtime. Without an exception,
the page would deploy and render its empty state on every production
install. Added:

```diff
 *.md
 !LICENSE
+!CHANGELOG.md
```

**Scope note:** Per the user's choice, this reads the user-facing
`CHANGELOG.md`, not the internal `docs/change-log.md` — the latter has code
diffs and file paths that aren't meant for an end-user-facing screen.

## 2026-08-27

### Fix: Invoice print action downloaded a local path that doesn't exist on production (500 error)

**Files:**
- `plugins/webkul/accounts/src/Filament/Resources/InvoiceResource/Actions/PreviewAction.php`

**Symptom:** `POST /livewire-70e0a63d/update` returned `500 Internal Server
Error` when clicking Print from the invoice Preview modal
(`/admin/invoices/customers/invoices/1`).

**Root cause:** The action saved the generated PDF through the `public`
filesystem disk (`Storage::disk('public')->put(...)`, inside
`Webkul\Support\Traits\PDFHandler::savePDF()`), but then tried to serve it
back with `response()->download(storage_path('app/public/'.$pdfPath))`.
`storage_path()` only ever resolves a path on the local filesystem. In
production, `FILESYSTEM_PUBLIC_DRIVER` is `tenant-s3` (see
`config/filesystems.php`), so the `public` disk actually writes to S3 — the
file never existed at that local path, and Symfony's `download()` threw a
`FileNotFoundException`, surfacing to the user as a generic Livewire 500.

**Fix:** Read the file back through the same disk it was written to, using
Laravel's disk-agnostic `download()` helper (streams via `readStream()` for
any adapter, not just local):

```php
// before
return response()->download(storage_path('app/public/'.$pdfPath));

// after
return Storage::disk('public')->download($pdfPath);
```

**Follow-up (not done, flagged only):** The same write-via-disk /
read-via-`storage_path()` mismatch exists in
`plugins/webkul/chatter/src/Traits/HasChatter.php::addAttachments()`
(~line 620), plus a related disk mismatch in `removeAttachment()` (uses the
default disk with a `'public/'` prefix instead of `Storage::disk('public')`).
Left untouched since it's a different feature (chatter attachments) than the
reported bug — worth a dedicated pass if attachment upload/removal is also
misbehaving in production.
