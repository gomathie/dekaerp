# Change Log (Agent-Assisted Work)

A running record of code changes made during AI-agent-assisted work sessions,
with the reasoning behind each one. This is distinct from
[`CHANGELOG.md`](../CHANGELOG.md) at the repo root, which tracks user-facing
release notes per version. See [`docs/agent-reminders.md`](agent-reminders.md)
for the task/question log this change log is paired with.

---

## 2026-09-03 (PayAction + API token management)

### #1481 Pay modal - fixed properly, scoping kept

The earlier pass fixed the root cause inside
`PaymentRegister::getBatchAvailablePartnerBanks()` but left the action alone,
because upstream's rework looked like it would drop this fork's company
filter. Reading the fork's filter properly showed the opposite:

```php
$bankAccountIds = Journal::where('type', JournalType::BANK)
    ->where('company_id', $companyId)->pluck('bank_account_id');
```

That restricts the dropdown to bank accounts attached to **the company's own
BANK journals** - correct when a customer pays you, wrong when you pay a
vendor, where the recipient is the *vendor's* account and appears on none of
your journals. Hence the blank field.

Upstream's version resolves both directions through
`getBatchAvailablePartnerBanks()`: the journal's account for RECEIVE, the
partner's accounts filtered to the batch's company for SEND. So it is still
company-scoped - by the **invoice's** company, which is more accurate than the
UI's current one. It also adds `withTrashed()`, a null-safe `bank?->name`, and
resolves a default so the field is not blank. Adopted, with the label rename
to `recipient-bank-account` across four locales.

### API token management screen

`Settings -> API Tokens`
(`Webkul\Security\Filament\Clusters\Settings\Pages\ManageApiTokens`), matching
the convention of the other pages in that cluster - they live under
`Security\Filament\Clusters\Settings\Pages` while referencing the support
plugin's `Settings` cluster.

Issues a token against a chosen user with a label and scopes
(`read`/`write`/`*`), lists live tokens with the user each acts as, last-used
and expiry, and revokes singly or in bulk. The plaintext token renders once in
a warning panel, because Sanctum stores only a hash.

Gated on `view_any_security_user` - the same permission as user
administration, since a token is only ever as powerful as the user behind it.
Deliberately a Page, not a Resource: a new Resource needs Shield permissions
generated before anyone can reach it, and `shield:generate` cannot be run here.

Verified against the installed Sanctum: `abilities` casts to json,
`expires_at` to datetime, `createToken(string, array)` takes abilities, and no
custom token model is registered. Strings in four locales, parity checked.
`docs/api-access.md` updated - it previously said no such screen existed.

## 2026-09-03 (sequences + tax formulas ported from v1.6.0)

Two feature ports the user asked for, plus #1500 which turned up along the way.

### Document sequences (#792)

Numbering was `{prefix}{journal code}/{year}/{database id}` - gaps whenever a
row was deleted or a create failed, no yearly reset, nothing configurable.
Now driven by a `sequences` table with prefix, padding, step and reset
frequency, editable in a new admin screen.

Ported: the `sequences` migration, `Sequence`, `SequenceService`,
`SequenceResetFrequency`, `SequenceResource` + `ManageSequences`, and lang for
all four locales (upstream shipped all four). Consumers: accounts (`Move`,
`Journal`), sales and purchases (`Order`), inventories (`Scrap`, `Operation`,
`OperationType`, `Warehouse`), manufacturing (`Order`, `Warehouse`), plus the
`Company` force-delete hook that releases a deleted tenant's sequences.

**Numbering continuity.** `SequenceService::initialFromNames()` reads existing
names, takes the trailing digits and starts the counter at max+1, so existing
documents keep their numbers and new ones continue the run. The shape is
unchanged - `CODE/YEAR/N` before and after.

**One visible change: padding defaults to 5.** `INV/2026/42` becomes
`INV/2026/00043`. Structure is identical, but the zero-padding is new. It is a
per-sequence column editable in the admin screen, so it can be set to 1 to
keep the old look - no code change needed. Flagged for the user to decide.

**Seed-migration ordering was checked per plugin, not assumed.** Two landed in
the wrong place when inserted and were moved: purchases (mid-list, would have
run before later schema migrations) and inventories (before the fork-only
`provision_company_virtual_locations`). Both now run last, as upstream does.

Service providers were **edited, not copied** - each also carries the
product-usage registry, which is still deliberately not ported. Only the
sequence hunks were taken. An `OperationType` import was missing in the
inventories provider; without it `OperationType::class` would have resolved to
a non-existent class in the provider's own namespace and the uninstall purge
would have silently matched nothing.

### #1500 - found after all

Earlier this was recorded as "could not be located in the v1.6.0 source". That
was wrong: it is not a `disabled()` on the currency field, it is
`Webkul\Account\Observers\CompanyObserver`, which throws a ValidationException
when `currency_id` changes while journal items exist. Ported with its lang
strings and registered via a `registerObservers()` guarded by
`Package::isPluginInstalled`.

### Tax formulas (#153) - and a money-math change to validate

The `formula` column already existed in this fork's create migration, so no
schema change was needed. Ported `TaxFormulaEvaluator`,
`InvalidTaxFormulaException`, the `Tax` model, `TaxRequest` validation, the V1
API resource, and the resource UI with its `Schemas/`/`Tables/` classes and
four locales.

The evaluator was security-checked before adoption: user-entered formulas are
parsed by a hand-written tokenizer with a whitelist of three variables
(`price_unit`, `quantity`, `price_subtotal`) and two functions (`min`, `max`).
**No `eval`, no dynamic invocation** - which is the only acceptable design for
a user-supplied expression field.

**`TaxComputer` changes computed tax on multi-tax lines.** Two things:

- *Deterministic ordering* - `sortBy([sort, id])` and `orderBy('sort')
  ->orderBy('id')`. Without the id tiebreaker, taxes sharing a `sort` value
  computed in whatever order the database returned them. An unambiguous fix.
- *Batching* - previously every tax was its own batch of one; taxes sharing
  amount type, price-include and base-affected characteristics are now grouped.
  This alters how the base is derived when several taxes apply to one line.

**This is the highest-risk change in the whole backport and it is untested
here.** It affects money on new invoices. It wants the Pest suite plus a
deliberate check against known multi-tax scenarios before production.

## 2026-09-03 (v1.6.0 audit completed - the 11 plugins missed earlier)

The earlier passes worked from an incomplete plugin list. These eleven were
never triaged: accounting, blogs, chatter, fields, maintenance, manufacturing,
plugin-manager, security, support, time-off, timesheets. All 21 plugins have
now been examined.

### [Security] The other half of the chatter XSS fix

The Blade view was escaped in the first pass, but
`ChatterNotificationService::buildChangeSummary()` builds the *same* change
rows for notifications and did not escape them - so field values still reached
notification bodies raw. Now `e($label)`, `e((string) $old)`, `e((string)
$new)`, matching upstream exactly. The release note's single "escaped HTML
entities in chatter change summaries" line covered two files; only finding one
of them would have left the hole open.

### Scope hardening (security + support)

`OwnershipScope` guarded on `! $user`; upstream requires `! $user instanceof
User`. Upstream also adds `CompanyContext::internalUser()` - the authenticated
user only when it is a `Webkul\Security\Models\User` - and routes
`CompanyScope`, `CompaniesScope` and `AllowedCompanyScope` through it.

Checked against this fork's customer portal before applying, since these are
the tenancy boundary: portal users authenticate as `Partner` on the `customer`
guard, so `auth()->check()` on the default `web` guard is already false for
them and both versions bail out identically. The change only differs when a
non-User is authenticated on the default guard - where the fork would have
proceeded and called `allowedCompanies()` on a model that has no such
relation. The console short-circuit in `CompanyScope` is preserved.

### Default currency for money columns (support)

`default_currency_code()` helper, `Currency::getCodeAttribute()` /
`findByCode()` / `resolveDefault()`, and `Table`/`Schema` `configureUsing` in
`HasFilamentDefaults`, so money columns show the configured currency instead
of a hardcoded default. `CurrencySettings::default_currency_id` already
existed here. **The version string in that trait was deliberately left at
1.5.0** - it is displayed in the UI, and this is a selective backport, not an
upgrade to 1.6.0.

### Also applied

- `EditUser`: `->revealable()` on the password fields.
- `User::handlePartner*()`: syncs only the Partner's fillable attributes
  rather than spreading every remaining user column. (`password` and
  `remember_token` are in `$hidden`, so `toArray()` already dropped them -
  no credential was being copied; the change is correctness, not a leak.)
- `ViewCurrency`: the same delete guard as `EditCurrency`, plus its own
  `view-currency` strings in four locales, parity verified.
- `maintenance/Models/Equipment.php`: trailing whitespace.

### Fork is ahead - upstream not taken

- `UserInvitationMail` uses `temporarySignedRoute(..., now()->addDays(7))`
  here; upstream uses `signedRoute()`, an invitation link that never expires.
- `chatter/Models/Attachment.php` - the tenant-S3 URL fix again.
- `SupportPlugin` - null-guards the sidebar scroll and adds the View
  Transitions enhancement.
- `ImageCacheController` and `ManageBranding` default colours - this fork's
  branding work.
- `plugin-manager/Package.php`'s two new helpers exist to serve upstream's
  slimmer `InstallCommand`; this fork's inline versions are better, so they
  would be dead code.

### Skipped

Translatable Posts (blogs) with the same new composer packages as Website;
sequences (manufacturing, support, Company); the product-usage registry;
restructure throughout accounting, fields, timesheets, time-off, maintenance
and manufacturing.

## 2026-09-03 (final v1.6.0 pass - invoices, partners, projects, website, products, recruitments, sales)

Completes the plugin-by-plugin audit. Every plugin has now been looked at.

### Applied

**Applicant categories listing error (recruitments).** The table did
`->reorderable('sort', direction: 'desc')->defaultSort('sort', 'desc')`, but
`recruitments_applicant_categories` **has no `sort` column** - confirmed
against the migration - so every listing ordered by a column that does not
exist. Upstream removes both calls from this one table while keeping them on
Degrees, Job Positions, Refuse Reasons and Stages, all of which do have the
column. Checked each of those here before removing, so the fix is the cause,
not a guess.

**#1497 also affects quotations (sales).** `QuotationSummary::refreshSummary()`
had the same missing `currency_id` read as the purchase order summary.

**#1489 partner type filters (partners + accounts).** Employees / Customers /
Vendors preset views on the partner list, with the rank-based two hidden when
the accounts plugin is not installed. The accounts Customers and Vendors pages
unset them, since those lists are already filtered. `customers` and `vendors`
strings added for ar/es/pt_BR (`employees` already existed); parity verified.

**Variant generation now reports its errors (products).** The catch showed a
notification and swallowed the exception, so the real cause never reached the
log - or Sentry. Added `report($e)`.

**Invoices: adopted the restructure, deliberately.** See below.

### On the restructure, and why invoices was different

The "restructure" is upstream moving table and form definitions out of the
resource class into `Tables/XxxTable.php` and `Schemas/XxxForm.php`, with the
body copied verbatim. No behaviour changes. It is why ~150 files report as
differing while saying nothing.

Skipping it costs nothing functionally but accrues merge debt: every future
upgrade has to be read through the same noise this audit just waded through.

The rule used here: adopt it where the fork has **no** customisation in that
resource - the extracted body is then provably identical to the inline one -
and defer it where the fork customised the table or form, because that is
where moving code silently drops company scoping. Invoices was the first kind:
its two `ProductResource` classes matched upstream's extracted `ProductsTable`
character for character. **`plugins/webkul/invoices` now has zero divergence
from upstream.** Sales `CustomerResource` is the second kind - the fork adds
its own `contentGrid()` - so it was left alone.

### Skipped, with reasons

- **Website translatable pages** - needs new composer packages (Spatie
  Translatable, LaraZeus) and converts `title`/`content` to JSON columns. A
  data-format migration against live content; a project, not a patch.
- **Products usage registry** - the same all-or-nothing port described in the
  earlier entry (ProductServiceProvider, Attribute, AttributeOption,
  ProductAttribute, Product, ManageAttributes).
- **Sequences** - sales/purchases `Order::name`, unchanged position.
- **Projects and recruitments** - restructure throughout, no behaviour change.

**Fork ahead again:** `Partner::getAvatarUrlAttribute()` uses
`Storage::disk('public')->url()`; upstream still has bare `Storage::url()`.
Taking upstream's file would have reverted the tenant-S3 fix, as it would have
in the chatter view earlier.

## 2026-09-03 (inventories, employees, purchases audit)

Continuing the file-by-file v1.6.0 pass. Same method: triage by diff size,
read both sides, apply only what merges without giving up fork behaviour.

### employees - #1491, a real foreign-key bug

`Employee::handlePartnerCreation()` and `handlePartnerUpdation()` both wrote
`'parent_id' => $employee->parent_id` into the **Partner** they create.
Confirmed against the migrations: `employees_employees.parent_id` is a FK to
`employees_employees` (the manager), while `partners_partners.parent_id` is a
FK to `partners_partners`. So an employee id was being written into a partner
foreign key - a violation when no partner holds that id, and worse when one
does, because it silently parents the partner to an unrelated record. Both
lines removed; the file now matches upstream exactly.

### purchases

**#1497 currency in the order summary.** `OrderSummary::refreshSummary()`
never read `currency_id` off the totals, so the component kept whatever
currency it was initialised with and showed converted totals against the wrong
symbol.

**Vendor email warning.** Previously, sending a PO to vendors with no email
address reported success in green and attached the PDF to chatter, while
nothing was sent. `mailVendors()` now returns how many were actually mailed,
callers skip the chatter attachment when that is zero, and the notification is
danger / warning / success according to how many vendors lacked an address.
Four code files from upstream plus `warning` and `danger` strings for both
actions across ar/es/pt_BR (upstream shipped en only). Key parity verified by
flattening all four locales and diffing paths.

### inventories

**Late operations.** Added the `late` preset view to `OperationResource` and
its label in four locales. It pairs with the dashboard fix: the widget's late
card linked to `getUrl('todo')` - the wrong view - and upstream points it at
`late`, which only resolves now that the view exists. The widget's other card
also now counts operations via `baseQuery()` rather than ASSIGNED *moves*,
which matches the label it carries.

**Fork is ahead:** `Inventory\Models\Move` uses
`$move->operation?->confirmAdditionalMoves()`; upstream dropped the null-safe
call. Not applied - upstream would fatal where this fork does not.

**Skipped as feature ports:** sequences (Scrap, Warehouse, Operation,
OperationType, Order, and the service providers), the resume-attachments
feature in employees - which would also need tenant-S3 thought for its
FileUpload - and the product-usage registry. Everything else in the three
plugins was the resource restructure: table and form definitions moved into
`Tables/` and `Schemas/` classes, leaving the originals slimmer.

## 2026-09-03 (accounts audit)

### v1.6.0 backport: full file-by-file pass over the accounts plugin

35 files differed. Triaged by diff size - everything over ~100 lines was the
resource restructure (form/table moved into `Schemas/`, `Tables/`), the small
ones were real fixes.

**#1478 payment state never updated - applied, two parts.**

`Payment.php` compared `$invoice->payment_state === PaymentStatus::PAID`.
`Move` casts `payment_state` to **`PaymentState`**, and PHP enum identity
across two different enum classes is never true no matter that both cases
carry the value `'paid'`. So the branch could not fire and a fully paid
invoice left its payment in "in process". Now compares against
`PaymentState::PAID`.

`PaymentRegistrar` then re-saves the payments matched to a move after
recomputing its state, so the payment follows the invoice.

**AccountingSetupService - two multi-company fixes, applied.**
`copyJournals()` carried `bank_account_id` in `JOURNAL_ACCOUNT_COLUMNS` and
remapped it through `$accountMap`. That map holds chart-of-accounts ids, not
bank account ids, so a new company's journals inherited the *template
company's* bank account. Removed from the list and explicitly nulled. Also
aligns a copied row's `currency_id` to the target company, so a journal is no
longer denominated in a currency its own company does not use. (Placement
before `array_merge($data, $overrides)` matches upstream, so an explicit
override still wins.)

**Skipped as pure refactor** (no behaviour change): `getMoveType()` extraction
in CreateInvoice/CreateBill/CreateCreditNote/CreateRefund, import ordering in
AccountPartnerSchema, and the unused `company_currency` accessors on
PaymentRegister.

**Skipped as feature ports**, each pulling in classes the fork does not have:
sequences (Move, Journal, AccountServiceProvider, + 37 files project-wide),
tax formulas (Tax, TaxRequest, TaxComputer, TaxResource, EditTax, and the V1
API resource), the product-usage registry, and partner type filters
(ListCustomers, ListVendors).

**Still needing a decision:** `PayAction`. Upstream's rework replaces the
fork's company-scoped journal filter with its own resolution helpers -
different semantics under multi-company.

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
