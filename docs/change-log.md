# Change Log (Agent-Assisted Work)

A running record of code changes made during AI-agent-assisted work sessions,
with the reasoning behind each one. This is distinct from
[`CHANGELOG.md`](../CHANGELOG.md) at the repo root, which tracks user-facing
release notes per version. See [`docs/agent-reminders.md`](agent-reminders.md)
for the task/question log this change log is paired with.

---

## 2026-09-23 (Logistics WP-8a expense records and approval)

WP-8a is implemented on `feature/logistics`: the new `ExpenseApproval` service
owns draft-to-submitted-to-approved/rejected transitions, re-reads the expense
under the company scope before loading or changing it, checks the Logistics
switch itself, and authorises the requested ability. The Finance
`ExpenseResource` follows the split Schemas/Tables/Pages layout, links expenses
to a shipment, trip or vehicle, and stores receipts through the `public` disk
with an allowlisted MIME set, a 10 MB limit, and generated filenames. Receipt
requiredness is read from the selected category in the current company scope.

Added `ApprovalTest.php` for both approval outcomes, missing approve permission,
company isolation, and conditional receipt validation. Syntax checks passed and
Pint passed on the nine new PHP files. The focused Pest run used the dedicated
`aureuserp_testing_wp8a` database, but did not reach assertions: the existing
plugin-install bootstrap remained in `shield:generate` permission generation.
The database was reset after the stalled run; no test result is claimed.

The WP-8a status remains `in progress` until the required focused and full
LogisticsFeature runs complete. No Requests for other packages.

---

## 2026-09-22 (Logistics WP-7 invoicing, and WP-5 finished)

Branch `feature/logistics`. Handoffs: `docs/logistics-plan.md` §7.

### WP-7: charges and shipment invoicing

`ShipmentInvoicer` turns a shipment's billable charges into a customer invoice
by creating the same `Account\Models\Move` that Sales creates and handing it to
`AccountFacade::computeAccountMove()`. Logistics owns no part of invoicing:
numbering, journals, posting and payment state stay in Accounting.

**Two bugs in the hand-off to accounting, found by the tests:**

1. `Move`'s saving hook computes currency *before* journal, so a move created
   with neither dereferences a null journal and dies. Sales never hits it
   because orders always carry a currency; shipments do not. Fixed by supplying
   `$shipment->currency_id ?? $shipment->company?->currency_id` and leaving the
   journal to accounting's own `computeJournalId()`.
2. The scoped re-read ran *after* the charges were loaded, so invoicing another
   company's shipment reported "nothing to invoice" rather than refusing it -
   the charges were scoped out too. The guard now runs first.

**Two ways to silently under-bill a customer**, both closed in the UI:

- A charge with no product yields a move line with no account, which accounting
  treats as a non-product line and computes **untaxed**, with no error. The
  product is now `required()`.
- A tax with no repartition lines contributes nothing, also silently. The tax
  select only offers taxes that have `invoiceRepartitionLines`.

Neither of those is a defect in the invoicer - both are configuration mistakes
that produce a plausible-looking invoice with money missing from it, which is
why they are prevented at entry rather than validated at invoice time.

Waiting time (D14) is deliberately two methods: `waitingTimeSuggestions()`
computes and creates nothing, `addWaitingTimeCharge()` is what a confirmed
suggestion calls. A long wait is often the carrier's own fault, so automatic
detention billing would put invented charges on real invoices.

### WP-5: finished and integrated

Codex built the delivery package and stopped without integrating it. The three
delivery actions are now registered on `ViewShipment` and
`DeliveryProofsRelationManager` on `ShipmentResource` - both WP-2 files, which
is why Codex was asked to request the change rather than make it.

`DeliveryProofsRelationManager::objectKey()` was extracted and made public so
the test derives the storage key from the same code the UI links with. It
previously hand-built `companies/{id}/{path}`, omitting the `root` prefix
`fileUrl()` adds; the two could have drifted apart with every test still
passing, leaving the file present and the link pointing elsewhere.

**Known coverage gap, recorded not fixed:** `phpunit.xml` does not set
`FILESYSTEM_PUBLIC_DRIVER`, so tests always run the `local` driver and the
`tenant-s3` paths - `fileUrl()`'s secure-storage branch and all of
`withShipmentDisk()` - never execute. The driver builds a real S3 driver, so it
cannot be faked cheaply.

### A guard every Logistics service needs

`LogisticsPolicy::recordAbility()` grants on permission plus the per-company
switch. It never checks that the record belongs to a company the user can see -
normally the company scope makes that moot, but a model obtained another way
passes authorisation. Both `DeliveryService` and `ShipmentInvoicer` now re-read
the record under the scope before writing. WP-8a and WP-8b need the same.

### Verification

- `--testsuite=LogisticsFeature`: **88 passed, 408 assertions**
- `--testsuite=AccountFeature`: **521 passed, 1382 assertions** - identical to
  the WP-1 baseline, so writing invoices from Logistics disturbed nothing
- Pint clean

**Not verified:** WP-7's UI layer has no tests of its own. Both relation
managers, `CreateInvoiceAction` and `UnbilledCharges` are exercised only to the
extent that the suite loads them.

---

## 2026-09-21 (Logistics WP-4: trips and dispatch board)

Branch `feature/logistics`. Plan and handoff: `docs/logistics-plan.md` §7.

### Added: DispatchService, trip resource, dispatch board

`DispatchService` is the only place a trip's state changes and the only place
shipments are put onto a trip. `assign()`, `dispatch()`, `start()` and
`complete()` are each wrapped in a transaction and each call
`LogisticsAccess::ensureEnabled()` and `Gate::authorize()` themselves, because
the super-admin `Gate::before` bypass skips policies and the board and future
telematics callers reach the service directly.

Attaching a shipment goes through the service rather than the plain relation.
A bare attach would link the records but leave the shipment `CONFIRMED` and the
trip with no stops - a silently half-assigned trip.

**Refusals and warnings are deliberately different.** No vehicle, no driver, an
archived vehicle, an archived driver or an expired licence all block. Over
capacity only warns and the dispatch still proceeds (D5). Blocking on capacity
would strand real deliveries over an estimate, so it is advisory by design and
the class docblock says so - the instinct on review is to "fix" it into a
refusal.

**Stops take the shipment's company, not the session's.** A dispatcher working
across companies would otherwise stamp their own company onto another company's
stop.

### Fixed during development: a permission substitution in DispatchBoard

`canAccess()` first gated the board on `view_any_logistics_shipment` alone.
Shield generates `page_logistics_dispatch_board` automatically and that is what
grants the screen, so this was substituting a different rule than the plan's
conventions specify. It now requires **both**: the page permission and the
shipment permission. Either alone is wrong - the page permission by itself would
leak customer names and destinations to a user denied the shipment list.

### Verification

- `--testsuite=LogisticsFeature` on `aureuserp_testing_claude`: **76 passed,
  348 assertions**, zero failures. WP-4's own contribution is the 10 tests in
  `tests/Feature/Dispatch/`; the total also includes WP-5's in-progress tests,
  which landed in the tree in parallel.
- Pint: reformatted 15 files (import order, operator alignment) after that run.
  Style only, no behaviour change.

One test failed first time and it was the test, not the code: it read
`$trip->stops()` while the user was active in another company, so the company
scope correctly returned nothing. Rewritten to assert both halves - invisible
through the scope, and carrying the shipment's company when read without it.
That is a stronger assertion than the original.

---

## 2026-09-21 (Logistics WP-6: waybill PDF)

Branch `feature/logistics`. Work package WP-6 added a printable shipment
waybill without changing the WP-2-owned shipment view page.

### Added: tenant-correct waybill generation

`PrintWaybillAction` follows the existing Accounts Dompdf/PDF handler pattern
and renders shipment number, customer, sender and recipient, route, cargo,
driver and vehicle, dates, instructions, and a receipt signature area. The
document's letterhead is loaded from the shipment's own company relation, not
from the session's current company.

The action checks the shipment `view` policy both for visibility and again in
the download operation. The execution path re-queries through normal company
scoping before rendering, so passing an arbitrary out-of-scope model instance
cannot bypass the tenant boundary.

### Added: idempotent first-print numbering

The first print locks the shipment row, explicitly ensures the
`logistics.waybill` sequence for that shipment's company, consumes one number,
and saves it to `waybill_no`. Later and concurrent prints reuse the saved value
instead of burning another sequence number.

### Verification

Verified in the Sail container against the dedicated local PostgreSQL database
`aureuserp_testing_wp6`:

- Focused WP-6 tests: 4 passed, 11 assertions.
- Full `LogisticsFeature` suite: 60 passed, 293 assertions.
- Pint was run on the new WP-6 PHP paths and fixed only formatting; final dirty
  and explicit-path checks are recorded in the WP-6 handoff.

The shipment view page was deliberately not edited because WP-2 owns it and
WP-4 may be changing it concurrently. The exact import and header-action line
needed to expose the tested action are in the WP-6 handoff request.

---

## 2026-09-21 (Logistics WP-3: vehicles and drivers)

Branch `feature/logistics`. Work package WP-3 added Fleet resources and
adoption import helpers on top of the WP-1 fleet models.

### Added: fleet resources using the upstream split layout

Vehicle and Driver resources now live under the Fleet cluster at the exact
FQCNs already present in `config/filament-shield.php`. The resource classes are
thin and delegate forms, infolists and tables to `Schemas/*` and `Tables/*`
classes, matching Filament's generator and the upstream AureusERP resource
layout. This avoids creating inline resources that future upstream fixes cannot
patch cleanly.

Vehicles expose registration number, ownership, vehicle type, capacity,
third-party carrier, optional Maintenance equipment linkage, telematics device
reference and active status. Drivers expose either an employee or carrier
contact, licence fields, active status and expiry badges. Licence numbers are
hidden unless the user has `update_logistics_driver`.

### Added: idempotent adoption imports

`FleetImporter` bulk-creates driver profiles from selected employees and
vehicles from selected Maintenance equipment. Each method skips rows already
linked by `employee_id` or `equipment_id`, so running the import twice is safe.
Both methods call `LogisticsAccess::ensureEnabled($source->company_id)` before
creating records, because super-admin Gate bypass can skip policies.

Maintenance equipment does not have a guaranteed vehicle registration field, so
imported vehicles use `serial_no`, then `partner_ref`, then `EQ-{equipment id}`.

### Verification

Verified in the Sail container:

- `docker compose run --rm --no-deps laravel.test php artisan test --testsuite=LogisticsFeature --filter=Fleet`: 4 passed, 45 assertions.
- `docker compose run --rm --no-deps laravel.test php artisan test --testsuite=LogisticsFeature`: 56 passed, 282 assertions.
- `docker compose run --rm --no-deps laravel.test vendor/bin/pint --dirty --format agent`: passed.

---

## 2026-09-20 (plugin install: intermittent failures that succeed on retry)

Branch `feature/logistics`. Reported symptom: installing a plugin sometimes
errors, then works on a later attempt.

### Fixed: an open transaction wrapped a 300-second external process

`PluginResource`'s install action opened `DB::beginTransaction()`, then `exec()`ed
`{plugin}:install` as a **separate PHP process** on its own database connection,
and only committed afterwards.

The transaction bought nothing: the child process's migrations and permission
rows commit independently, so `DB::rollBack()` could never undo an install — it
only ever reverted the local `plugins` row. What it did do was hold this
request's connection open, *idle in transaction*, for as long as the child ran,
up to the 300s timeout. Against a pooled managed database (Supabase) that is
long enough to trip an idle-in-transaction timeout, and long enough for the
locks this connection still held to block the child's own DDL — which is
precisely an install that fails once and then succeeds when retried.

Removed the transaction; `$record->update()` is a single statement and needs
none. The rollback in the `catch` went with it, since it was rolling back work
that had already committed elsewhere.

### Fixed: the cache rebuild raced with the post-install redirect

`Package::refreshPluginCaches()` ran `optimize:clear` synchronously and then
fired a **detached** `php artisan optimize > /dev/null 2>&1 &`, returning
immediately. The redirect straight after an install could therefore reach the
app while that background process was still writing
`bootstrap/cache/config.php` and `routes-v7.php`. Laravel writes those with
`file_put_contents`, which is not atomic, so a concurrent request could
`require` a half-written file and fatal — an error that fixed itself a moment
later once the rebuild completed. The detached command also discarded its own
failures into `/dev/null`.

Now rebuilt in-process with `Artisan::call('optimize')`, so the caches are whole
before anyone is redirected and a failure is reportable. Costs the install
action a few seconds. `rebuildCachesInBackground()` was removed as dead code.

### Already fixed on this branch, listed for the record

- `Role::first()` in `InstallCommand` (unordered, so permissions were synced to
  an arbitrary role) now resolves the panel role by name.
- `Package::phpBinaryPath()` uses `PhpExecutableFinder` (upstream `631dbcdfb`).

### Not verified by tests

Neither path is exercised by the suites: the Filament action is not covered, and
the `optimize` rebuild is guarded by `app()->isProduction()`. `php -l` clean.
This needs confirming on a real install.

---

## 2026-09-19 to 2026-09-20 (Upstream Package A: verified runtime baseline)

Branch `feature/logistics`. Whole-application plan:
`docs/upstream-fix-adoption-plan.md`, Package A / UF-001.

### Fixed: the declared PHP/CI contract did not match the lockfile or production

The root package claimed PHP `^8.3`, and all GitHub workflows provisioned PHP
8.3, while the committed lockfile already contained Symfony packages requiring
PHP 8.4.1 or newer. The PostgreSQL CI lanes also used PostgreSQL 16 while Sail
and production use PostgreSQL 17.

The root contract is now PHP `^8.4.1`; Pest, Playwright, and translation CI use
PHP 8.4; and both database-backed workflows use PostgreSQL 17. MySQL remains as
the existing compatibility lane.

### Security patches found while refreshing the lock contract

`composer audit --locked` exposed four advisories in direct dependencies. The
minimum constraints and lockfile now require:

- Filament 5.7.6, fixing MFA code replay (CVE-2026-84306) and a
  password-validity disclosure for panel-denied accounts (CVE-2026-84307).
- Livewire 4.3.4, fixing client-side state handling that could permit DOM XSS
  (CVE-2026-81887).
- Laravel Excel 3.1.70, preventing a caller-controlled export path from writing
  outside the configured filesystem disk (CVE-2026-84374).

Two broad Composer dry runs were rejected because they would have updated 74
or 50 packages. The applied update changes exactly 14 packages: the twelve
Filament split packages plus Livewire and Laravel Excel. A metadata-only Guava
repository URL canonicalization is the only other lock entry change. The
translation workflow now runs a locked Composer audit on every push and pull
request, without multiplying the audit across database/browser matrix jobs.

Composer's normal `filament:upgrade` script republished five tracked CSS/JS
assets. All five match their installed package source byte-for-byte. The local
sidebar, topbar, and table-summary Blade overrides were compared with Filament
5.7.6; their upstream templates did not change from 5.7.3.

### Full-suite finding: plugin installation widened the wrong role

The first complete serial PostgreSQL run finished with 2 failures and 2,422
passes (6,783 assertions) after 38,339.61 seconds. Both failures exposed one
order-dependent authorization defect plus one incomplete test fixture:

- Plugin permission regeneration selected `Role::first()`. The new protected
  `Multi-Company Admin` role is created before `Admin`, so installing a plugin
  could replace its intended eight permissions with every application
  permission. Fresh ERP installation also used `Role::first()` for
  `general.default_role_id`, making the protected role the invitation default.
- The Super Admin promotion test created a user with events disabled and
  therefore without the partner record that every normal user save creates.
  Filament correctly reached the singular relationship and then attempted to
  create a blank partner.

Both installer paths now resolve the configured panel `Admin` role explicitly
by case-insensitive name and `web` guard. The protected-role provisioner uses an
exact sync so stale grants are removed. Migration
`2026_09_20_150533_repair_multi_company_admin_install_state` normalizes that
permission baseline and changes a global default role from `Multi-Company
Admin` to the distinct configured `Admin` role when needed; rollback is
deliberately non-destructive. Regression coverage now exercises fresh install,
repair, Filament promotion, and a real lightweight `contacts:install` run.

### Verification

- `composer validate --no-check-publish`: passed; existing exact Shield
  constraint warning only.
- Clean optimized Composer install under Sail PHP 8.4.23: passed, including
  package discovery and asset publication.
- `composer audit --locked --no-interaction`: no advisories.
- Symfony YAML parse of all four GitHub workflow files: passed under PHP 8.4.
- Playwright locked install and test discovery: passed; npm reported no
  advisories and Playwright listed 220 Chromium tests across 19 files.
- Published asset SHA-256 comparisons: all five match package sources.
- Filament vendor override comparison, 5.7.3 to 5.7.6: no upstream changes.
- `translations:check --details`: command completed; 93/108 locale sets pass.
  Existing translation drift leaves 15 failures across Accounts, Employees,
  Logistics, Products, Security, and Support. No translation files were changed
  in this package.
- Initial full serial PostgreSQL Pest suite: 2 failed, 2,422 passed (6,783
  assertions). Those two failures led to the installer and fixture fixes above.
- Post-fix focused Security suite: 41 passed (121 assertions), including an
  actual plugin permission regeneration; duration 410.53 seconds.
- `vendor/bin/pint --dirty --format agent`: passed after the PHP changes.

Remote GitHub Actions and the Playwright shards have not yet run against this
uncommitted branch. The full 10-hour serial suite was not repeated after the
focused fixes; the remote matrix is the remaining whole-suite confirmation.
Deployment must run the new idempotent Security migration before plugin
installation or invitation workflows resume.

## 2026-09-18 (Logistics WP-2 in progress; security provisioner fix)

Branch `feature/logistics`. Plan and handoff: `docs/logistics-plan.md` §7.

### Fixed: new multi-company admin migration broke every test suite

`MultiCompanyAdminRoleProvisioner::provision()` (new, untracked, written by
another worker today) called `modelKeys()` on the result of `collect()->map()`.
`modelKeys()` is defined on `Illuminate\Database\Eloquent\Collection` only —
`map()` returns a plain `Illuminate\Support\Collection` — so the call always
threw `BadMethodCallException`. Its migration
(`2026_09_18_000002_provision_multi_company_admin_role.php`) runs on every
`migrate:fresh`, so this failed the whole repo's tests, not just Logistics.

```php
// before
$role->permissions()->syncWithoutDetaching($permissions->modelKeys());

// after
$role->permissions()->syncWithoutDetaching($permissions->map->getKey()->all());
```

### Upstream review against aureuserp v1.6.0

We are 215 commits / 1935 files behind `aureus/master`. A blanket sync is not
viable: a large share of that is an upstream refactor of resource structure
across 13 plugins, which would collide with nearly everything this fork has
added. Reviewed for *fixes that affect us* instead.

**Already fixed here, no action:** `8552c0cc4` "Escape HTML entities in change
summaries" — a stored XSS in chatter, where user-controlled old/new field values
were rendered with `{!! !!}` in the activity log and unescaped in notification
mail. Our copy already escapes (`content-text-entry.blade.php` lines 164 and
181) and `ChatterNotificationService` already calls `e()`, at four call sites to
upstream's two. Relevant because every plugin including Logistics logs through
chatter.

**Backported:** `631dbcdfb` "Fix Plugins installation in windows".
`Package::phpBinaryPath()` used `shell_exec('which php')`, which does not exist
on Windows and is disabled on some hosts, so installing a plugin from the
Plugins page could not find a binary — the same path Logistics installs through.

Checked the php-fpm regression risk before taking it, because the old code
hand-rolled a `str_contains(PHP_BINARY, 'fpm')` guard and a candidate list:
`PhpExecutableFinder::find()` returns `PHP_BINARY` only when `PHP_SAPI` is
`cli`, `cli-server` or `phpdbg`, so under fpm it falls through to `PHP_BINDIR`
instead of returning the fpm binary. It covers the case the old list existed
for. Verified in the container: returns `/usr/bin/php8.4`, `is_file` true.

One deliberate deviation from upstream: `find(false)`, not `find()`. The result
is passed through `escapeshellarg()`, and appended SAPI arguments would be
quoted into a single unusable token. Upstream's `is_file()` check hides this by
falling back to `'php'`.

Nothing else upstream is security- or data-integrity-critical for us: the rest
is feature work (sales price lists), translation keys, product soft-delete
visibility, and the refactor. A 1935-file divergence means low-severity fixes
are certainly going unnoticed; real coverage needs a dedicated sync pass.

### Multi-Company Admin is now "super admin minus a denylist"

Goal (user): DEKA ERP staff administer tenant companies without full super-admin
rights, and creating one must not mean ticking hundreds of permissions.

The role previously carried an enumerated grant list (8 `BASE_PERMISSIONS`) with
the denylist applied on top in `User::hasPermissionTo()`. That is subtractive:
it grants almost nothing, and adding the role to an existing admin *removes*
rights rather than adding them — the opposite of the goal. Inverted to grant by
default:

```php
// SecurityServiceProvider::packageBooted()
Gate::before(function ($user, string $ability, array $arguments = []) {
    if (! $user instanceof User || ! $user->is_active || ! $user->isMultiCompanyAdmin()) {
        return null;
    }

    if (app(MultiCompanyAdminService::class)->deniesAbility($ability)) {
        return false;   // denylist wins over every other role
    }

    return $arguments === [] ? true : null;
});
```

Three details carry the security of this:

- **Policies are not bypassed.** A blanket `true` from `Gate::before` skips
  policies, which is where per-company containment lives. Only bare permission
  checks (empty `$arguments`) are granted; a check carrying a model or class
  returns `null` and its policy decides, so `UserPolicy::canManageUser()`,
  `CompanyPolicy::isCompanyAssigned()` and the Logistics policies still apply.
- **`is_active` is re-checked.** The callback runs before
  `User::hasPermissionTo()`, which is where a deactivated user is normally
  stopped, so omitting it would re-admit deactivated staff.
- **Row visibility is unchanged.** `bypass_company_scope` is denied, so the
  company scope still limits every query to assigned tenants.

`deniesAbility()` is now the security boundary, so it matches on pattern rather
than on the names known today — anything ending in `_role(s)`/`_permission(s)`,
any `bypass_*`, `force_delete_*`, `page_security_*`, `impersonate`,
`plugin_manager`, `_security_team`. A future plugin's permission touching those
is denied by default.

`MultiCompanyAdminService::scopeAssignableRoles()` gained a Multi-Company Admin
branch. It previously derived assignable roles from the actor's *granted*
permissions, which under the new model would leave a staff admin able to do
nearly everything but assign almost nothing. They may now assign any non-system
role that grants nothing they are themselves denied — which is exactly the
"cannot create an admin with more rights than himself" rule, since they hold
everything the denylist allows. System roles (`Admin`, `super_admin`,
`Multi-Company Admin`) stay excluded, so only a super admin can mint another
staff admin.

**Test impact, not yet run:** `MultiCompanyAdminTest` line ~469, "cannot assign
a company role containing permissions it does not possess", builds its elevated
role from `update_support_currency`. That is no longer a permission the staff
admin lacks, so the case must be rebuilt on a *denied* ability (e.g.
`view_any_role`) to keep testing what it means to test.

### Narrowed: super-admin role identity no longer reads `panel_user.name`

`Role::getSuperAdminRoleNames()` merged `config('filament-shield.panel_user.name')`
into the super-admin list. The fork's own super-admin gate
(`SecurityServiceProvider::packageBooted()`, `Gate::before`) consults only
`filament-shield.super_admin.name` and `'super_admin'`, so reading `panel_user`
here widened super-admin identity beyond what the rest of the codebase
recognises. `isSuperAdmin()` is now an unconditional allow in `UserPolicy`
(view/update/delete/restore), `CompanyPolicy` (all companies) and
`MultiCompanyAdminService::scopeManageableUsers()`, so the blast radius is large.

No behaviour change today: `panel_user.name` is `'Admin'` in
`config/filament-shield.php`, and `'Admin'` is already a literal entry in
`SUPER_ADMIN_ROLE_FALLBACKS`. The change removes the trap where editing that
config later would silently promote whatever role it names.

**Not changed (already correct):** `User::isSuperAdmin()` and
`isMultiCompanyAdmin()` use spatie's `getRoleNames()`, which calls
`loadMissing('roles')` and reads the cached relation — so they cost one query
per user instance, not one per permission check. A memoised boolean was
considered and rejected: `hasPermissionTo()` runs on every Gate check, and
spatie invalidates the `roles` relation via `unsetRelation()` on
`assignRole`/`syncRoles`/`removeRole`, which a cached bool would not honour
when roles change mid-request.

### Fixed: every permission check failed for a model created in memory

`User::hasPermissionTo()` now opens with `if (! $this->is_active) return false;`.
But `users.is_active` gets its `true` from a **column default**, which the
database applies to the written row and never reads back, and `UserFactory` does
not set the attribute. So `User::factory()->create()` returned an instance with
`is_active === null`, and every permission check on it denied. 19 Logistics
tests failed this way; `MultiCompanyAdminTest` passed only because its helper
sets `'is_active' => true` explicitly.

Not test-only: `canAccessPanel()` reads the same attribute, so in production any
`User::create([...])` that omits `is_active` yields an instance treated as
suspended until it is reloaded. Fixed at the root, in the model:

```php
protected $attributes = [
    'is_active' => true,
];
```

`App\Models\User` declares no `$attributes`, so nothing is clobbered, and a row
hydrated from the database still overrides it through `setRawAttributes()` —
genuinely deactivated users stay deactivated.

### Fixed: two PermissionRegistrar instances, so permission caches never cleared

`Webkul\Security\Models\Permission::getPermissions()` reads through the fork's
`Webkul\Security\PermissionRegistrar` — bound as a singleton in
`SecurityServiceProvider`, where the unqualified `PermissionRegistrar::class`
resolves to the fork's class. Every flush site (`SecurityHelper`, `Role`,
`ShieldSeeder`, `MultiCompanyAdminRoleProvisioner`) instead called
`forgetCachedPermissions()` on `Spatie\Permission\PermissionRegistrar`, an
unrelated class holding its own in-memory collection.

So the cache that name lookups actually read was never cleared. A `Permission`
row created after that collection warmed stayed invisible to `findByName()`,
`checkPermissionTo()` swallowed the `PermissionDoesNotExist`, and `can()`
returned false for a permission that existed and was attached to the user.

This was the last failing Logistics test — the only one that authenticates
twice, so its third permission is created after the cache warmed. It also hits
production: `MultiCompanyAdminRoleProvisioner::provision()` creates permissions
and then flushes the wrong registrar, so during install or seeding they can be
unusable for the rest of that request.

All four flush sites now clear both registrars.

**Recommended, not done:** make `Webkul\Security\PermissionRegistrar` extend
Spatie's and alias the container binding so a single instance serves both names.
Rejected for now because `AssignRoleCommand` and `CreateRoleCommand` type-hint
`Spatie\Permission\PermissionRegistrar`, and the fork's class does not extend
it, so an alias would TypeError those commands. It is the right long-term fix
but needs a full-suite run behind it.

### Logistics WP-2 (Shipments and workflow) — verified, 52/52

`--testsuite=LogisticsFeature` = 52 passed (237 assertions). Status `review`.

Getting there took four runs and turned up the three bugs above, none of them
in Logistics. Worth recording why it looked like a Logistics problem for so
long: the first three runs each failed differently, because the security and
support rewrite was landing in the same working tree while the tests ran, so
the tree moved between runs. The signal only became trustworthy once those
edits stopped. Two streams sharing one working tree and one test database
(`aureuserp_testing`) cannot be debugged concurrently — separate worktrees, or
serialise them.

The shipment Confirm test keeps a four-way precondition assertion (permission,
switch, policy, state) compared as one array. It is what finally identified the
registrar bug, and it is cheap to keep: a future failure names the broken link
instead of reporting only that the action was hidden.

---

## 2026-09-17 (Logistics plugin: plan, starter kit, Foundation)

Branch `feature/logistics`. Plan, decisions and handoffs: `docs/logistics-plan.md`.

### Planning
- Reviewed the user's Logistics prompt against the codebase, then wrote the
  Phase 0 report and a package plan agents can work on in parallel.
- Research refined the decisions: Odoo dispatch, fleet, expenses and freight
  add-ons; ePOD practice; ERP cutover practice. All D1–D15 recommendations
  were accepted.
- Adoption design: install is global, so the plugin has its own per-company
  switch.

### WP-1a starter kit and WP-12 enum translations (weaker agent, reviewed)
- `plugins/webkul/logistics/composer.json`, 10 enums with English labels, the
  menu icon (`resources/svg/logistics.svg`, `public/svg/logistics.svg`), and
  ar/es/fr/pt_BR translations of the enum labels.
- Verified with host PHP: lint, a script checking enum values, transitions and
  colours, and a translation parity script. The icon was reviewed from source
  only.

### WP-1 Foundation
- **Tables (19, all new, `logistics_*`):** service/vehicle/package types and
  expense categories (shared); company settings; drivers; vehicles; shipments
  and lines; trips and the trip↔shipment pivot; stops; shipment events
  (append-only); delivery proofs; stop links (token hash only); charges and
  the charge↔tax pivot; expenses; the shipment↔invoice pivot. The only
  foreign keys to other plugins' tables point at partners, employees,
  products, currencies, units, users, companies and the accounts tables.
  Optional integrations (sales order, maintenance equipment) are plain
  indexed columns.
- **Models (16)** use `BelongsToCompany`. Shared configuration models don't
  auto-assign a company. Child rows use `InheritsParentCompany`: the company
  comes from the parent on `saving`, and a mismatch throws. `Expense` refuses
  links to another company's shipment, trip or vehicle. Shipment and trip
  numbers come from per-company sequences (`LogisticsSequences` ensures the
  company counter first).
- **Per-company switch:** `logistics_company_settings.is_enabled`,
  `LogisticsAccess` (a scoped singleton; `enabledFor`, `enabledForCurrent`,
  `ensureEnabled`), and `CompanyProvisioner` (readiness report, sequences,
  LOG-* service products, default journals; enable and disable, safe to
  repeat).
  - **Why not Spatie settings:** `CompanyAwareSettingsRepository` falls back to
    the default company's row, which would have enabled Logistics for every
    company without its own row.
- **Policies:** `LogisticsPolicy` requires the permission and the switch
  (listing and creating check the user's active companies; changes check the
  record's company). `ConfigurationPolicy` doesn't depend on the switch, and
  shared rows can only be changed by users who see all companies. Shipment
  child records follow shipment permissions. The Shield config holds every
  permission for later packages, with custom abilities inside
  `resources.manage`.
- **Uninstall:** `UninstallGuard` runs in `startWith` and refuses while any
  shipment exists, unless `LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true`. It runs
  in both the console command and the Plugins page. Chatter and sequences are
  purged afterwards.
- **UI:** a Logistics menu group, five clusters (the operational ones stay
  hidden unless the switch is on), four configuration screens with a
  shared-or-company field, and the settings page (Enable and Disable actions
  with a readiness report).
- **Seeders:** shared rows only, keyed by `code`, no fixed ids. Nothing is
  created per company at install.
- **Existing files changed:**
  - `bootstrap/providers.php`: registers the provider.
  - `phpunit.xml`: adds the `LogisticsFeature` suite.
  - `plugins/webkul/support/src/Enums/NavigationGroup.php`: adds the Logistics
    case and icon.
  - `lang/{en,ar,es,fr,pt_BR}/admin.php`: menu label.
- **Planned changes not needed:** `UninstallCommand`,
  `CompanyAwareSettingsRepository`.

### Verification
- `php -l` is clean on all 191 plugin PHP files and the changed core files.
- Autoloader regenerated in the Sail image (`--no-scripts`).
- **`LogisticsFeature`: 30 passed (160 assertions)**, covering:
  - install;
  - migrations only create `logistics_*` tables;
  - seeders can run twice;
  - the uninstall guard and how it is wired;
  - company-scoping invariants (16 models);
  - policies and the switch;
  - per-company numbering;
  - child-company inheritance and mismatch refusal;
  - append-only events;
  - the adoption scenarios.
- Screen render tests: **8 passed** (the four configuration lists, permission
  refusals, shared types visible to a company user, and enabling plus saving
  from the settings page).
- Regression: **`AccountFeature` 521 passed**, **`SupportFeature` 115 passed**
  on a clean test database.

### Test-harness finding (not a Logistics defect, left unfixed)

Running `SupportFeature` straight after a suite that installs Products fails 3
`UOMTest` cases. The app boots and reads the `plugins` table left behind by the
previous suite, so Products looks installed and its `UOMObserver` is registered;
`migrate:fresh` then drops `products_products`, which that observer queries, and
the delete endpoint returns 500. Dropping and recreating `aureuserp_testing`
first makes all 115 pass. The fix belongs in `TestBootstrapHelper` (reload
`Package::$plugins` after `migrate:fresh`, and register plugin observers only for
plugins installed in the current database). Recorded in the WP-1 handoff.

### Environment fixes found while verifying
- Port 5433 was held by another local project, so `docker compose up`
  couldn't recreate pgsql and tests hung. Fix: `FORWARD_PGSQL_PORT=5436`.
- `composer dump-autoload` with scripts hangs in a network-less container.
  Fix: `--no-scripts`.

---

## 2026-09-17 (card-grid menus behind the next card)

`plugins/webkul/support/resources/css/grid-layout.css`: the hover lift uses
`transform`, which makes the hovered card its own stacking context. Filament
cards are `position: relative` and paint in document order, so the next card
covered the open "⋮" dropdown (z-index 20 only counts inside the card). A
hovered or focused card now gets `z-index: 10`. `support.css` was rebuilt
from the project root (506 selectors), and the public copy was updated.

---

## 2026-09-14 (mobile experience, steps 1-3)

A static audit (the app could not be opened at phone width here) found three
causes. What was already fine was left alone: table repeaters already turn into
cards, report tables already scroll, and form grids already stack.

### 1. Grid cards jumped when tapped

`support/resources/css/grid-layout.css` - card lift, image zoom and icon scale
were bare `:hover` rules. Touch browsers apply `:hover` on tap and keep it
applied, so a tapped card jumped 4px and stayed lifted. The rules now sit inside
`@media (hover: hover) and (pointer: fine)`. There is no change on desktop.

`support.css` was rebuilt with the project-root command. **The committed
`resources/dist/support.css` was itself a broken plugin-directory build (238
selectors, 56KB)**; the rebuild has 505. `public/css/support/support.css` (the
copy actually served) was compared selector-by-selector against the one in git:
the only two classes it lost, `border-white/40` and `hover:bg-white/10`, occur
nowhere in current source.

### 2. Installable from the home screen

- `public/manifest.webmanifest`, plus icons in `public/images/icons/`
  (192/512 "any", 512 "maskable" with safe-zone padding, 180 apple-touch),
  rendered from `public/images/logo.svg` with `rsvg-convert`/Imagick in the
  Sail image.
- `resources/views/filament/components/web-app-meta.blade.php`, injected at
  `PanelsRenderHook::HEAD_END` in `AdminPanelProvider` (so it covers the login page).
- `display` is **`minimal-ui`, not `standalone`**, on purpose: this panel opens
  PDFs and downloads, and a chromeless app has no back button to leave them.
  For the same reason there is no `apple-mobile-web-app-capable` tag. On iOS
  the icon opens a Safari tab.
- Admin panel only. The customer portal has a different start URL and was not
  changed.

### 3. List tables showed 9-11 columns on a phone

None of the 221 tables used responsive visibility. For the 13 widest lists,
3-4 key columns (number, partner, total/qty, status) always show; the rest
get `->visibleFrom('sm'|'md'|'lg')`. **Every column is visible from 1024px up, so
desktop is unchanged.** Columns hidden by default were not touched.
Files: accounts `InvoiceResource` (covers invoices and credit notes in every
cluster), `BillResource` (bills and refunds), `PaymentResource`; accounting
`JournalItemResource`; inventories `QuantityResource`, `ScrapResource`,
`OperationsTable`, and both `ManageMoves` pages; purchases `OrderResource`;
sales `QuotationResource`; manufacturing `ManufacturingOrderResource`;
projects `TaskResource`.

**Root-cause fix that step 3 depends on:** Filament's summary (totals) row does
not apply `visibleFrom`/`hiddenFrom` to its cells, and folds leading columns
into its heading colspan, so on tables with totals the sums would have landed
under the wrong columns. Overridden in
`resources/views/vendor/filament-tables/components/summary/row.blade.php`
(copied from Filament v5.7.3, changes marked `DEKA`): cells carry their
column's responsive classes, and the colspan stops at the first responsive
column. **Re-diff this file when upgrading Filament.**

### Verification

- `php -l` on all 13 edited PHP files (Sail image): clean.
- New test `InvoiceResourceTest` › "hides summary cells together with the columns
  hidden on narrow screens". It asserts the summary row has no colspan and
  carries `md:`/`lg:fi-visible`; against the vendor view it would fail.
- Passed: that test, plus the invoice, bill and quotation list tests (4/4).
- Not verified: rendering on a real phone. Check at 390px in devtools and
  install from Android Chrome after deploy.

---

## 2026-09-04 (every suite run - 2479 tests passed)

`AccountingFeature`, `ProjectFeature`, `ProductFeature`: **381 passed, 805
assertions, zero failures**, 4792s.

Run despite those plugins being barely touched, because three of the backport's
changes are global rather than local:

- the four company-scope classes apply to every scoped model in the app;
- `HasFilamentDefaults` now calls `Table::configureUsing()` and
  `Schema::configureUsing()`, which reaches **every** Filament table and form;
- `Currency::getCodeAttribute()` feeds `default_currency_code()`, which those
  defaults call.

Collateral damage from any of those would surface in plugins never edited -
which is exactly the shape of the one failure this session did find. None
appeared. `ProductFeature` also covers the `report($e)` added to
`GenerateVariantsAction`.

### Final tally

**2479 tests passed across all ten suites. One failure, found and fixed.**

| Run | Passed |
| --- | --- |
| Inventory + Sale + Purchase + Manufacturing | 1243 |
| `AccountFeature` | 517 |
| Accounting + Project + Product | 381 |
| `SupportFeature` | 115 |
| Targeted runs (partners, workflows, tax, sequences, scoping, portal) | 223 |

Every plugin the backport touched, and every plugin it could have reached
through shared code, is green.

The three gaps stated earlier are unchanged and structural: the sequence seed
migration against real invoice history, `include_base_amount`, and the old
customer-portal path.

## 2026-09-04 (verification finished - 2098 tests passed)

`InventoryFeature`, `SaleFeature`, `PurchaseFeature`, `ManufacturingFeature`
in one sequential run: **1243 passed, 3374 assertions, zero failures**,
15699s.

That completes coverage of every plugin the backport touched. These four hold
the scrap, sales-order, purchase-order and manufacturing-order sequence
consumers, both `OrderSummary` currency fixes, the vendor-email behaviour
change, the `late` preset view and the dashboard widget.

### Session totals

| Suite / run | Passed |
| --- | --- |
| Inventory + Sale + Purchase + Manufacturing | 1243 |
| `AccountFeature` (full) | 517 |
| `SupportFeature` (full) | 115 |
| sales + manufacturing + partners (targeted) | 111 |
| accounts workflows (targeted) | 48 |
| inventories + purchases (targeted) | 42 |
| tax + sequence (targeted) | 7 |
| scoping + portal re-run | 6 |
| `BalanceSheetTest` | 6 |
| `TaxInclusiveBatchTest` | 3 |
| **Total** | **2098 passed, one failure found and fixed** |

Six plugins are now green across every file: accounts, support, inventories,
sales, purchases, manufacturing - plus partners in full and targeted files
elsewhere.

### The three gaps that remain, unchanged

1. **The sequence seed migration against real data** - the deploy gate. A
   fresh database holds no invoices numbered under the old scheme.
2. **`include_base_amount`** - no helper affordance; only matters if invoices
   use the flag.
3. **The old customer-portal path** - the fix is verified, the bug it fixes
   stays an inference.

## 2026-09-03 (tax-inclusive branch verified - verification complete)

`TaxInclusiveBatchTest`: **3 passed, 7 assertions**.

```
✓ it keeps a tax-inclusive line total equal to the price entered
✓ it batches two tax-inclusive taxes against one shared base
✓ it does not mix inclusive and exclusive taxes into one batch
```

The `price_include` branch of `sharesBatch()` behaves correctly after the
rework: inclusive tax is carved out of the entered price rather than added,
two inclusive taxes share one base, and inclusive and exclusive taxes are kept
in separate batches. That was the last item on the unverified list that could
be closed from here, and it removes the manual staging check for
tax-inclusive pricing.

**Session total: 855 tests passed, 2572 assertions, one failure found and
fixed.**

### What remains genuinely unverifiable here

1. **The sequence seed migration against real data.** A fresh test database
   holds no invoices numbered under the old scheme, which is exactly what
   `initialFromNames()` must read. This is the one remaining deploy gate.
2. **`include_base_amount`.** The tax helper has no affordance for it, and
   adding one risks testing the scaffolding rather than the behaviour. Only
   matters if invoices use that flag.
3. **The old customer-portal code path.** Demonstrating the bug needs the
   guard reverted, which was blocked. The fix is verified; the bug it fixes
   remains an inference from reading.

## 2026-09-03 (full AccountFeature green)

**517 passed, 1370 assertions, zero failures**, 4791s. All 38 files.

This is the plugin the backport changed most consequentially - the
`TaxComputer` batching rework and the `#1478` payment-state enum fix - and the
whole suite now exercises them across invoice, bill, credit-note, refund and
payment workflows rather than the handful of files targeted earlier.

### Session total

**852 tests passed, 2565 assertions, one failure found and fixed.**

| Suite / run | Passed |
| --- | --- |
| `AccountFeature` (full) | 517 |
| `SupportFeature` (full) | 115 |
| sales + manufacturing + partners | 111 |
| inventories + purchases (targeted) | 42 |
| accounts workflows (targeted, earlier) | 48 |
| tax + sequence (targeted) | 7 |
| scoping + portal re-run | 6 |
| `BalanceSheetTest` | 6 |

## 2026-09-03 (closing the last untested tax branch)

`TaxInclusiveBatchTest` - three tests over the `sharesBatch()` branch nothing
else reached.

`TaxGroupTest` only ever exercises tax-*exclusive* taxes, so how the rework
batches **tax-inclusive** ones was unverified - and that is arithmetic which
lands on customer invoices. This was recorded as the item needing a manual
staging check; the tests replace it.

- A tax-inclusive line totals exactly the price entered: 100 on an inclusive
  10% tax means the customer pays 100, the tax carved out rather than added.
- Two inclusive taxes share one base - both carved out of the same 100, total
  still exactly 100. Had batching regressed so one compounded onto the other,
  this fails.
- Inclusive and exclusive taxes are **not** batched together: `sharesBatch()`
  refuses to group taxes whose `price_include` differs, so the exclusive one
  adds on top while the inclusive one is carved out.

They assert **invariants** - total equals the entered price, and untaxed + tax
reconciles to total - rather than hardcoded figures. A regression in batching
breaks those relationships regardless of rounding, which cannot be predicted
from here with confidence.

Queued behind the full `AccountFeature` run; suites share one database.

`include_base_amount` remains uncovered - the helper has no affordance for it,
and inventing one risks testing my own scaffolding rather than the behaviour.
If invoices use that flag, it still wants a look on staging.

## 2026-09-03 (SupportFeature complete - 335 passed overall)

Full `SupportFeature` suite: **115 passed, 720 assertions**, 1042s. Every file.

This is the plugin the backport touched hardest, and the suite covers it:

- `CurrencyTest`, `CurrencyRateTest` - the `Currency` additions
  (`getCodeAttribute`, `findByCode`, `resolveDefault`) behind
  `default_currency_code()`.
- `ResourceGlobalSearchSmokeTest` - every support resource still constructs,
  which is what exercises the `Table`/`Schema` `configureUsing` defaults added
  to `HasFilamentDefaults`.
- `CompanyIsolationTest`, `CompanyScopingInvariantsTest`,
  `PortalCompanyScopeTest` - all three company scopes and `CompanyContext`.
- `SetLocaleMiddlewareTest`, `ProfileLanguageUpdateTest` - untouched, but they
  confirm nothing in the port disturbed locale handling.

### Session total

**335 tests passed, 1195 assertions, one failure found and fixed.**

Every file changed by the backport now sits behind at least one passing test,
except the three gaps recorded earlier: the untested `sharesBatch()` branches,
the sequence seed migration against real data, and the old portal code path.

## 2026-09-03 (logging fixed in production)

`LOG_CHANNEL` pointed at `nightwatch`, a channel with no package behind it
since Nightwatch was dropped. Laravel does not error on an undefined channel -
`LogManager::get()` catches and falls back to the emergency logger, which
writes to `storage/logs/laravel.log`. On Cloud that file is ephemeral and
feeds nothing, so every `Log::` call had been going nowhere readable.

Applied on Laravel Cloud, and mirrored into `.env.laravel-cloud`:

```
SENTRY_LARAVEL_DSN=<dsn>
SENTRY_TRACES_SAMPLE_RATE=0.1
SENTRY_PROFILES_SAMPLE_RATE=0.1
SENTRY_ENABLE_LOGS=true
SENTRY_SEND_DEFAULT_PII=false
LOG_CHANNEL=stack
LOG_STACK=laravel-cloud-socket,sentry_logs
LOG_LEVEL=info
```

`laravel-cloud-socket` is first in the stack deliberately - it is what feeds
the platform Logs tab, per the warning in `config/logging.php`. Nightwatch
vars and token removed from Cloud and from the mirror.

**Trailing comments had to come out of the values.** An earlier hand-off block
carried explanatory `#` comments on the same line as the value, and they were
pasted into Cloud. `SENTRY_ENABLE_LOGS` is the one that breaks: the SDK
compares `enable_logs === true`
(`sentry-laravel/src/Sentry/Laravel/ServiceProvider.php:172`) and Laravel's
`Env` only converts the exact string `'true'`
(`Illuminate/Support/Env.php:257`). With a comment appended, the value is a
longer string, the strict comparison fails, and Sentry logs are off while the
config reads as though they are on. The sample rates survived only because
`(float)` takes the leading numeric. Lesson: never put a comment on the same
line as a value in a block meant for copy-paste.

**Still to verify** (needs a redeploy, cannot be checked from here): one
`Log::info('logging check')` should appear in both the Cloud Logs tab and
Sentry Logs. Reaching Sentry but not the Logs tab means
`laravel-cloud-socket` is not resolving; the reverse means
`SENTRY_ENABLE_LOGS` still is not parsing as a boolean.

`SENTRY_PROFILES_SAMPLE_RATE` remains inert on Cloud - profiling needs
Excimer, which only the Dockerfile installs, and Cloud does not build from it.

## 2026-09-03 (verification complete: 220 passed, 475 assertions)

Final run - `sales/OneStepSaleOrderTest`,
`manufacturing/ManufacturingOrderTest`, and the whole `partners` feature
directory: **111 passed, 250 assertions**, 2792s. All green, including
partners' own company isolation and scoping invariants.

### Totals across the session

| Run | Result |
| --- | --- |
| `BalanceSheetTest` | 6 passed |
| `TaxGroupTest` + `InvoiceSequenceTest` | 7 passed |
| `InvoiceTest` + `CreditNoteTest` + `CurrencyTest` | 48 passed |
| Company scoping (support + accounts) | 14 passed, **1 failed** |
| Invariants re-run + `PortalCompanyScopeTest` | 6 passed |
| `ScrapTest` + inventories invariants + `PurchaseOrderTest` | 42 passed |
| sales + manufacturing + partners | 111 passed |
| **Total** | **220 passed, 475 assertions, 1 failure found and fixed** |

Every sequence consumer is now exercised: accounts (`Move`), sales and
purchases (`Order`), inventories (`Scrap`), manufacturing (`Order`).

### What remains unverified, and why tests here cannot reach it

1. **`sharesBatch()` branches.** Every tax test uses one configuration. A line
   mixing tax-inclusive and tax-exclusive taxes, or using
   `include_base_amount`, is untested. This is the one that moves money.
2. **The sequence seed migration against real data.** A fresh database cannot
   hold invoices numbered under the old scheme, which is precisely what
   `initialFromNames()` has to read. Run it against a copy of production.
3. **The old portal code path.** Demonstrating the bug needs the guard
   reverted; that edit was blocked, so it stays an inference from reading.
4. **The untouched suites** - most of AccountFeature, and the project, product
   and website suites. Not run for time, not for any other reason.

## 2026-09-03 (inventories + purchases verified)

`inventories/ScrapTest`, `inventories/CompanyScopingInvariantsTest`,
`purchases/PurchaseOrderTest`: **42 passed, 93 assertions**, 1113s.

Covers the scrap and purchase-order sequence consumers, and the purchase-order
workflow the vendor-email change sits in.

`inventories/CompanyScopingInvariantsTest` **passed**, which is the useful
signal: the gap that failed in the support plugin does not repeat here.
`Sequence` lives in support, and the inventories scoped models
(`Scrap`, `Operation`, `OperationType`) all stamp a company normally, so
nothing needed declaring.

**Running total: 109 passed, 225 assertions, one failure found and fixed.**

## 2026-09-03 (the suite caught a gap in the sequence port)

`CompanyScopingInvariantsTest` **failed** on the first company-scoping run:

```
Failed asserting that two arrays are identical.
+    0 => 'Webkul\Support\Models\Sequence'
```

The sequence port added a company-scoped model that deliberately does not
stamp a company - `Sequence::autoAssignsCompany()` returns false, because a
null `company_id` is load-bearing: `SequenceService::next()` falls back to a
global sequence, and `Company::forceDeleting` nulls the column rather than
deleting the row. The test asserts every scoped model either stamps a company
or is *declared* shared, and `Sequence` was neither.

Checked which side was wrong before touching either. The code is right; the
declaration was missing. **Upstream's own copy of this test declares
`Sequence::class`** in `$shared` - they updated the test alongside the
feature, and the port took the feature without it. The file is now identical
to upstream. This adds coverage rather than removing it: membership of
`$shared` brings two further assertions to bear, checking the model stays
shared-capable with a nullable column.

Re-run: **6 passed** (invariants 4, portal 2).

### New: PortalCompanyScopeTest

The customer portal authenticates Partners on the `customer` guard, and
Filament's `Authenticate` middleware calls `Auth::shouldUse()` with the panel
guard - so on portal routes `auth()->user()` is a **Partner**. `Partner` has
neither `hasRole()` nor `allowedCompanies()`, both of which the old scope
path called. Nothing in the suite authenticated on that guard, so the path was
entirely unexercised.

Two tests now cover it: a portal Partner is not mistaken for an internal user,
and querying a company-scoped model on that guard does not raise
`BadMethodCallException`.

**Honest limit:** these pass against the current code, which proves the path
works now. They do **not** prove the old code failed - demonstrating that
needs the guard temporarily reverted, and that edit was blocked by the
permission classifier (reasonably, since it looks like undoing a fix). So
"this fixed a live portal 500" is a well-supported inference from reading the
code, not an executed result.

**Running total: 67 passed, 132 assertions, one failure found and fixed.**

## 2026-09-03 (decision: sequence padding)

**Padding stays at 5** - user's call, 2026-09-03. Invoice numbers become
`INV/2026/00043` where the old scheme produced `INV/2026/42`. Same structure,
zero-padded.

No code change: `padding` defaults to 5 on the `sequences` table, nothing
overrides it (`Journal::sequenceDefaults()` sets name, prefix, reset frequency
and initial_from only), and `Sequence::consumeNumber()` applies it through
`str_pad`. Recorded so it reads as a decision rather than an unnoticed
default - and it stays editable per sequence in Settings, so a single sequence
can be set back to 1 without touching code.

## 2026-09-03 (verification totals)

**61 tests passed, 125 assertions**, across three runs in the Sail container.

| Run | Result |
| --- | --- |
| `BalanceSheetTest` | 6 passed, 6 assertions |
| `TaxGroupTest` + `InvoiceSequenceTest` | 7 passed, 14 assertions |
| `InvoiceTest` + `CreditNoteTest` + `CurrencyTest` | 48 passed, 111 assertions |

Which backport changes each one actually exercises:

- **Tax batching** - `TaxGroupTest` holds `amount_tax` at 30.0 for 10% + 5%
  children on a 200 base. Compounding would read 31.0.
- **Sequence numbering** - `InvoiceSequenceTest`: numbers come from the
  sequence, the sequence is scoped to the journal's company, the counter
  advances, and a sequence built against existing documents continues past
  them.
- **#1478 payment state** - `InvoiceTest` and `CurrencyTest` assert
  `payment_state === PaymentState::PAID`, and `CreditNoteTest` asserts
  `REVERSED`. These are the comparisons that could never be true before the
  enum was corrected.
- **Currency alignment in `AccountingSetupService`** - covered incidentally by
  `CurrencyTest`.
- **`ManageApiTokens`** - not test-covered, but `route:list` shows
  `admin/settings/manage-api-tokens` registered, and the Filament page tests
  pass with it in place, so panel construction is unaffected.

**Still not verified.** `sharesBatch()` branches on `price_include` and
`include_base_amount`; every test above uses one configuration, so a line
mixing tax-inclusive and tax-exclusive taxes is unproven. The sequence seed
migration has not been run against real data - a fresh-database test cannot
reproduce a table of invoices numbered under the old scheme. And the other
seven suites, and every plugin outside `accounts`, remain unrun.

## 2026-09-03 (first real verification results)

`TaxGroupTest` + `InvoiceSequenceTest`: **7 passed, 14 assertions**, 319s.

```
PASS  TaxGroupTest
  it sums the child taxes of a group tax on the subtotal
  it creates a separate tax line for each child of a group tax on post
  it keeps a group-taxed invoice balanced on post
PASS  InvoiceSequenceTest
  it numbers a posted invoice from a sequence rather than its database id
  it creates a sequence scoped to the journal and its company
  it advances the counter so two invoices never share a number
  it continues from documents that already exist rather than restarting at one
```

**What this actually proves.** The tax batching rework did not change the
figures for a group tax: 10% and 5% children on a 200 base still produce
`amount_tax = 30.0`, both against the same base. Compounding would have given
31.0. And the sequence port numbers documents correctly, scopes the sequence
to the journal's company, advances without collision, and continues past
documents that already exist.

**What it does not prove.** `sharesBatch()` branches on `price_include` and
`include_base_amount` as well as amount type; these three tests exercise one
configuration. A multi-tax line mixing tax-inclusive and tax-exclusive taxes,
or one using `include_base_amount`, is untested here. If invoices in
production use those, exercise them before deploying the tax change.

## 2026-09-03 (test runtime fixed; numbering test added)

### The suite runs again

`php artisan` cannot boot on this host - PHP 8.3.2 against a `vendor/` needing
`>= 8.4.1` - which is what has gated verification through this whole backport.
The project already ships the answer: the `laravel.test` Sail service and its
`sail-8.4/app` image. Three environment traps were stopping it, none of them
code:

1. **`docker compose run -u root` does nothing** - Sail's entrypoint re-drops
   to `sail` (uid 1000).
2. **Root-owned files cannot be overwritten.** The bind mount presents host
   files as `root` inside the container. Creating a *new* file is fine because
   the directories are world-writable, but `file_put_contents` on an existing
   one fails - and the bootstrap rewrites `storage/installed` every run.
   Deleting it lets the container recreate it as its own user. `chmod` from Git
   Bash is a no-op on NTFS, so that is not the fix.
3. **An interrupted run poisons the test database** - the next run fails with
   `relation "..." already exists`, which looks like broken code and is not.
   Drop and recreate `aureuserp_testing` between runs.

First green result: `BalanceSheetTest` - 6 passed, 6 assertions.

Written up in `docs/running-tests.md` with the reset commands.

### What the suite does and does not cover

Checked rather than assumed, because a green run is only worth what it
exercises:

- **Tax batching is covered.** `TaxGroupTest` puts a group tax with 10% and 5%
  children on a 200 base and asserts `amount_tax = 30.0` - both applied to the
  same base. Had batching started compounding them the figure would be 31.0
  and the test fails. So the riskiest change in the backport is genuinely
  exercised.
- **Document numbering was covered by nothing at all.** No test in any plugin
  asserts an invoice number, while the sequence port rewrites how every
  invoice, order and scrap is numbered.

### New: InvoiceSequenceTest

Four tests over the gap: a posted invoice takes its number from a sequence
rather than its row id; the sequence is scoped to the journal **and its
company**; two invoices never share a number; and a sequence built from
scratch against invoices that already exist continues past them instead of
reissuing a number in use.

That last one is a *proxy* for the real migration, not the thing itself. It
deletes the sequence and re-posts, which exercises `initialFromNames()`, but
it cannot reproduce a table full of historical invoices numbered under the old
scheme. Running the seed migration against a copy of production data is still
the check worth doing before the sequence change is deployed.

### Honest note on the AccountFeature run

A full-suite run was started and then **stopped after six minutes** - 38 test
files at roughly a minute each was going to take hours. It produced no results
(118 bytes of container chatter). Nothing about AccountFeature as a whole has
been verified; only the targeted files named above.

## 2026-09-03 (restructure: analysed, deliberately not applied)

The last open item I could act on was the v1.6.0 resource restructure - ~150
files where upstream moved `form()`/`table()`/`infolist()` bodies verbatim
into `Schemas/` and `Tables/` classes.

Rather than eyeball them, both sides were normalised to code lines (imports,
namespace, class scaffolding, whitespace removed) and compared as sets. Of 121
restructured resources: **70 carry no fork content at all** (every line exists
in upstream's resource plus its extracted classes, so nothing here would be
lost) and **51 do** - including `InvoiceResource` (19 fork-only lines),
`purchases/OrderResource` (31) and `projects/TaskResource` (33).

**Not applied, on purpose.** Three reasons:

1. The check compares *sets*, so it proves no content is lost but **not that
   order is preserved**. In Filament, field order is display order - a
   mechanical adoption could silently reorder 70 screens.
2. It buys nothing functional. Every fix worth having is already in; this is
   merge-debt reduction.
3. Nothing in this backport has been run yet. Adding ~200 untestable file
   operations on top, on a live ERP, makes the eventual test run harder to
   attribute.

The analysis is the deliverable: `docs/restructure-backlog.md` lists both
sets, the rule for adopting, the method, and its known limit. Do it after the
runtime is fixed, plugin by plugin.

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
