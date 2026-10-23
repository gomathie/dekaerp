# Change Log (Agent-Assisted Work)

A running record of code changes made during AI-agent-assisted work sessions,
with the reasoning behind each one. This is distinct from
[`CHANGELOG.md`](../CHANGELOG.md) at the repo root, which tracks user-facing
release notes per version. See [`docs/agent-reminders.md`](agent-reminders.md)
for the task/question log this change log is paired with.

## 2026-10-23 (Company administrator: an address of its own, and visible on edit)

Asked for two things: a new company should be created with its administrator at
once, "whether it's the company email or not", and editing a company should show
its administrator so support can act.

### Most of this already existed

`Security\Services\CompanyAdministratorInviter` has invited a Company Admin on
company creation since 2026-10-21, and its docblock named the exact gap:

> The company's own address is the only one this form collects. A dedicated
> administrator field would be better, but the company form is inherited and
> monolithic, so adding one belongs in its own change.

That obstacle is gone - `CompanySchemaRegistry` (2026-10-23) lets another plugin
contribute into the company form without `support` referencing it. So this is
that deferred change rather than a new build.

### What changed

* `CompanyAdministratorInviter::invite()` takes an optional `$email`. It falls
  back to the company's address, so every existing caller is unaffected.
* `Security\CompanyAdminContributions` contributes an "Administrator" section
  into the company form through `CompanySchemaRegistry`, with the same static
  `$registered` guard as `PluginManager\CompanyContributions` - contributions
  accumulate and `packageBooted()` can run twice.
  * On **create**: an `Administrator email` field, `dehydrated(false)` because
    it is not a company column, with the company email as its placeholder.
  * On **edit/view**: each Company Admin with name, address, a deactivated
    marker and a direct link to that user, plus any **pending invitation**.
* `Security\...\Pages\CreateCompany::afterCreate()` reads the field from the raw
  form state and passes it through; the notification and the mail-failure
  recovery link now use the invited address rather than the company's.

Visible only to a super admin or Multi-Company Admin. A company's own
administrator has no business reading the other administrators of their company
through this section.

### Password changes: a link, not a second set of fields

The request included changing the administrator's password here. The section
links to the user instead, deliberately:

1. `UserResource` already implements this completely - confirmation field,
   `min:8`, and the `must_change_password` toggle wired to `User::updating()`,
   which clears the flag when a password changes unless the flag is set in the
   same save. A second set of password fields would be a second place to get
   credential handling right, and the two would drift.
2. The existing flow deliberately never mints or emails a password: the invitee
   sets their own through `AcceptInvitation`. Adding support-set passwords to
   the company form would reverse that quietly.

Open with the user: whether to put the password fields inline anyway.

### Two bugs avoided by checking the schema rather than assuming

* The first draft queried `invitations.company_id` and `accepted_at`. Neither
  exists: `user_invitations` has `default_company_id` and `company_ids`, and
  `UserInvitationService` **deletes** the row on acceptance, so a surviving row
  *is* a pending invitation. Left unchecked, the edit panel would have thrown.
* The administrator lookup reads `withoutGlobalScopes()`. `User` is both
  company- and ownership-scoped, so a scoped read would have shown **nothing**
  in exactly the support scenario the section exists for.

### Verification

`php -l` clean on 9 files; Pint passed; three tests appended to
`CompanyAdministratorInvitationTest` (an administrator address preferred over
the company's, the fallback when none is given, and a blank address with no
company email reported as `MISSING_EMAIL`).

## 2026-10-23 (Contacts: import from a spreadsheet)

Asked for an upload option on Contacts, with a downloadable example to fill in
and re-upload. Both CSV and Excel are accepted; CSV is the default template.

### Shape

One service, `Partner\Services\ContactImporter`, whose `columns()` is the
single source of truth: the template's heading row and the per-row validation
are both generated from it, so a column cannot be added to one and forgotten in
the other. A test asserts the two match, and the strongest test downloads the
example file and imports it back unchanged - that is what catches a heading the
parser cannot match or example data that fails the importer's own rules.

The rules mirror `Partner\Http\Requests\PartnerRequest`, the only place in the
application that declares validation for a partner, so an imported contact is
held to the same standard as one created through the API. That is where the
tight `max:20` on phone, mobile and zip comes from - the columns themselves are
wider.

| Decision | Why |
|---|---|
| Synchronous, capped at 2,000 rows | Production is `QUEUE_CONNECTION=database`, but whether a Laravel Cloud worker runs is a dashboard setting. A queued import with no worker never finishes and never says so. |
| Bad rows reported by spreadsheet row number, good rows still imported | The person fixes the file and re-uploads. The row number counts the heading row, so it matches what they see. |
| Duplicate email skipped, not updated | Silently overwriting customer data on an upload is worse than reporting it. |
| Duplicate and parent lookups use `withoutGlobalScopes()` | Partner carries both CompanyScope and OwnershipScope. A scoped lookup reports "no match" for a contact that exists but is invisible, and then creates a second one. |
| `company_id` left null | See the next section - contacts are shared by upstream design, and the form does the same. An import that stamped the active company would make imported contacts behave differently from typed ones. |
| CSV template written with a BOM | Excel otherwise opens it as the local codepage and accented names arrive as mojibake. |
| Excel template offered too | Excel mangles CSV in ways that matter to these columns: it strips the leading zero from a zip and can reinterpret `+233...`. |

### On the formula-injection guard

A value starting with `=`, `@`, tab or newline is **refused and reported**, not
rewritten. `+` and `-` are deliberately not guarded: every international phone
number in the template's own example rows starts with `+`, so guarding them
would reject ordinary contact data. There is a test pinning that.

The usual advice - prefix the value with an apostrophe - was rejected because
it is wrong for this application. It is invisible in Excel, but this ERP renders
contact names as HTML, where it would show as a literal apostrophe on every
screen; silently editing a customer's name is worse than refusing the row.

There is no server-side execution risk either way:
`maatwebsite/excel`'s `ReaderFactory` calls `setReadDataOnly()` from
`config('excel.imports.read_only', true)`, and no `config/excel.php` overrides
it, so a formula arrives as its cached value and is never calculated. What the
guard protects is the step after: a stored `=cmd|...` exported to CSV later and
opened in Excel by a colleague.

Also added server-side file validation. `acceptedFileTypes()` is only a browser
hint and is trivially bypassed, so the upload now also carries
`['file', 'mimes:xlsx,xls,csv,txt', 'max:10240']`. Laravel's `mimes` sniffs the
file's contents rather than trusting its extension, which is what catches a
renamed file, so no extra package is needed. `txt` is listed because a CSV
written by Excel frequently sniffs as `text/plain`.

### Verification

`ContactImportTest` 11 passed / 35 assertions; `PartnerFeature` 85 passed / 192
assertions with no regressions; `php -l` clean on 13 files; Pint passed.

Two of those tests failed first, and both were defects in the test rather than
the feature: the suite's database is seeded with the world's countries, so
creating a second "Ghana" left the importer resolving the country to the seeded
row while the state hung off the duplicate. Worth recording because the failure
looked like a product bug and was not.

## 2026-10-23 (Per-company contacts: why it is a fork, and the report that decides it)

Asked for contacts to belong to a company, since each tenant invoices its own
customers, with the rule: keep the company it was created in, otherwise the
oldest company.

**The request conflicts with a deliberate upstream decision, and that was not
obvious.** Commit `57e234223` by the AureusERP maintainer, "Fixed shared
partner issue" (2026-07-30), did three things at once:

* added `Partner::autoAssignsCompany()` returning **false**;
* shipped `2026_07_30_100000_null_company_on_non_user_partners`, which nulls
  `company_id` on every partner not tied to a user;
* removed the `?? current_company_id()` fallback from `AddressResource` and
  `ManageContacts`.

The bug it fixed is the one project memory describes independently: a child
address inherited `$parent->company_id ?? current_company_id()`, so whenever
the parent was shared the child was filed under **whoever created it**.
Upstream's answer was to treat contacts as shared master data. `CompanyIsolationTest`
pins it: "leaves a new partner shared instead of stamping the active company".

So scoping contacts per company is a fork, with three consequences:

1. Those three upstream files need re-patching on every future upstream merge.
2. A contact referenced by two companies' documents cannot be filed under one
   without the other losing sight of it - silently, because a scoped relation
   read returns `null` rather than raising.
3. The child-inheritance bug upstream fixed has to be re-solved, by applying
   `Webkul\Support\Traits\InheritsParentCompany` to `Partner`. That is in fact
   a better fix than upstream's, because it reads the parent scope-free.

None of that makes the goal wrong, but it is a one-way data migration against a
deliberate design, so what decides it is data rather than opinion.

### `partners:company-report` (read-only)

`Partner\Console\Commands\ContactCompanyReport` reports how many contacts are
unassigned, how each would resolve under the agreed rule, and the decisive
number: how many contacts are referenced by **more than one** company. It
writes nothing, and it is meant to be run against production before any
backfill. It discovers the tables to check from the schema rather than
hardcoding them, because which of the sixteen partner-referencing plugins are
installed varies per deployment.

Two bugs surfaced only by actually running it, both of which would have made the
report lie:

* The discovery matched `companies`, because **`companies.company_id` is a
  unique `character varying`** - the company's own registration identifier, a
  text input on the company form - and nothing to do with owning a tenant.
* The resulting UNION then died with "bigint and character varying cannot be
  matched".

Fixed by requiring both columns to be an integer type, which excludes that
column on principle rather than by naming it, so any other table that reuses
the name for a label is excluded too.

**Status: nothing was backfilled and no behaviour changed.** The flip of
`autoAssignsCompany()`, the backfill migration, `InheritsParentCompany` on
`Partner` and the two test inversions are one deliberate change, pending the
report's conflict count from production.

### Also found, not fixed

`Company::boot()` passes `'parent_id' => $company->parent_id` into
`Partner::create()`/`updateOrCreate()` (Company.php:184 and :229).
`companies.parent_id` references a **company**; `partners.parent_id` references
a **partner**. A child company's own partner row therefore gets whatever
partner happens to share that id. Pre-existing and unrelated to this work.

## 2026-10-23 (Correction: the per-company plugins entry overstated its verification)

The entry below for the per-company plugin grants recorded the work as verified.
It was not fully verified: `ResourceStructureSnapshotTest`, which records the
declared shape of every resource form, was never run against it, and the
`company_modules` checkbox list contributed into the Company form was missing
from the snapshot. A later full `SupportFeature` run failed on it - 2 failed,
171 passed.

Resolved by regenerating `security.json` and `support.json` through the
procedure the test documents (delete and re-record, never hand-edit). Both now
differ from their previous contents by exactly the intended section, with the
same resource counts (30 and 21). `SupportFeature` is now 173 passed / 891
assertions, zero failures.

A trap worth recording: regenerating with `--filter` produced a fixture with
**50** resource keys instead of 30, because a filtered run installs a different
set of plugins and recorded Employee and TimeOff permission trees the full suite
never sees. Committing that would have broken the next full run with a
20-resource phantom diff. The test's own CAVEAT warns about this. Fixtures must
be regenerated by the full suite that will later compare them.

## 2026-10-23 (Filament 5.10.3 upgrade: investigated, not applied)

Asked whether Filament can be updated. Answer: yes, cleanly, but it was
deliberately not done in the same working tree as the day's fixes.

### What is available

Locked at `v5.7.6`; `v5.10.3` is released. Filament is a monorepo of
version-locked sibling packages, so a partial update of `filament/filament`
alone cannot resolve - composer reports each sibling "fixed to v5.7.6 by a
partial update".

Listing the twelve `filament/*` packages explicitly resolves only to **v5.8.4**.
The cap is `livewire/livewire`, locked at `v4.3.4` and not in the set. Adding it
reaches the full target, and **nothing outside Filament and Livewire moves**:

| Packages | From | To |
|---|---|---|
| 12 × `filament/*` | v5.7.6 | v5.10.3 |
| `livewire/livewire` | v4.3.4 | v4.4.8 |

The command, as a smallest-explicit-set update with no `--with-all-dependencies`
(per the dependency rule in AGENTS.md):

```
composer update filament/actions filament/filament filament/forms \
  filament/infolists filament/notifications filament/query-builder \
  filament/schemas filament/spatie-laravel-settings-plugin filament/support \
  filament/tables filament/upgrade filament/widgets livewire/livewire
```

The third-party Filament plugins (`filament-shield` 4.2.0,
`filament-language-switch`, `filament-icon-picker`,
`filament-daterangepicker-filter`) all declare `^5.0` and do not constrain it.

### Why it was not applied in this tree

1. The working tree already held the day's two production crash fixes, the
   three-audience guides, the per-company plugin section and three new guard
   tests. A 13-package bump on top makes any later regression impossible to
   bisect to one cause or the other.
2. `composer install` runs Filament's upgrade hook and may republish tracked
   files under `public/`. The three local Filament view overrides - the
   summary-row one among them - have to be re-diffed against the new package
   sources afterwards.
3. This application reaches into Filament internals more than most. Three
   separate internals surfaced in this one session: `getMiddleware()` versus
   `getAuthMiddleware()`, `Component::$container` initialisation, and cluster
   route-name generation. `HasTableViews`, the schema registries and the page
   overrides all depend on internal behaviour, so three minor versions is a
   real surface and needs all thirteen suites rather than a spot check.

### The view overrides, measured rather than assumed

Point 2 above was checked rather than left as a worry. There are exactly three
local Filament view overrides, and their package sources are **not** where the
published path suggests - the panels views live in `filament/filament`, not a
`filament/panels` package:

| Override | Package source | Changed hunks at v5.7.6 |
|---|---|---|
| `filament-tables/components/summary/row.blade.php` | `filament/tables/resources/views/components/summary/row.blade.php` | 5 |
| `filament-panels/livewire/topbar.blade.php` | `filament/filament/resources/views/livewire/topbar.blade.php` | 8 |
| `filament-panels/livewire/sidebar.blade.php` | `filament/filament/resources/views/livewire/sidebar.blade.php` | 4 |

The v5.7.6 baseline diff was captured before the upgrade so the post-upgrade
diff can be compared against a known starting point rather than judged from
scratch.

The summary-row override is a narrow, well-commented change (responsive classes
on summary cells). **The sidebar and topbar overrides are not**: they are
substantial rewrites that drop `PanelsRenderHook::SIDEBAR_START`, the
`id="fi-main-sidebar"` anchor and both sidebar collapse buttons, and branch on
`getCurrentPanel()->getId() === 'admin'`. That is the real upgrade risk here,
and it is a silent one - a Blade override keeps rendering the version it was
forked from, so anything Filament changes in those two views across three minor
versions simply does not appear, with no error anywhere. Re-diffing these two is
a required step of the upgrade, not a tidy-up afterwards.

**Decision:** do it as its own change, after the day's fixes are verified and
deployed. Awaiting the user's go-ahead before `composer.lock` moves.

## 2026-10-23 (Two production crashes: a dead argument and hardcoded route names)

Two unrelated 500s reported from production within eleven seconds of each other,
both pre-existing and both the same shape: code that referenced something
nothing answered to.

### Crash 1 - `view_model`, an argument that was discarded but still evaluated

`/admin/recruitments/applications/job-by-positions` fataled with
`Typed property Filament\Schemas\Components\Component::$container must not be
accessed before initialization`, from
`table-views/src/Filament/Concerns/HasTableViews.php:464`:

```php
($this->editTableViewAction(['view_model' => $tableView->getRecord()]))($args)
```

`editTableViewAction()` declares **no parameters**. PHP does not error on extra
arguments to a userland function, so the array was silently thrown away - but
`$tableView->getRecord()` was still evaluated first, and a preset view component
has no schema container, so `getRecord()` reached an uninitialised typed
property and took the whole page down.

`view_model` occurred **exactly once** in the repository: at the point it was
passed. Nothing ever read it. The argument was removed, which fixes the crash
and deletes the dead code in one line. It had already been noted as dead earlier
in the day ("set once, read nowhere") - it was not merely dead, it was fatal.

### Crash 2 - hardcoded route names on clustered pages

`/admin/time-off/dashboard` threw
`Route [filament.admin.pages.time-off-dashboard] not defined.`

Five pages override `getRouteName()` with a hardcoded string.
`Page::getUrl()` calls `route(static::getRouteName($panel))`, while route
*registration* uses Filament's own relative name - two different code paths, so
a hardcoded string only works when it happens to coincide. From the live route
table:

| Path | Registered name | Hardcoded | |
|---|---|---|---|
| `admin/project` | `filament.admin.pages.project-dashboard` | same | ok |
| `admin/recruitment` | `filament.admin.pages.recruitments` | same | ok |
| `admin/time-off` | `filament.admin.pages.time-off-overview` | same | ok |
| `admin/time-off/dashboard/time-off` | `filament.admin.time-off.dashboard.pages.dashboard` | `filament.admin.pages.time-off-dashboard` | **broken** |
| `admin/time-off/reporting/reporting/by-type` | `filament.admin.time-off.reporting.pages.by-type` | `filament.admin.pages.time-off-by-type` | **broken** |

The two broken ones are the two inside a **cluster**: Filament prefixes a
clustered page's route name with the cluster, so the hardcoded names could never
match. `ByType` had the identical bug and had simply never been opened.

The overrides were deleted from both so Filament answers with the name it
registered; the now-unused `Filament\Panel` imports went with them. The three
non-clustered pages were left alone - their names are verified correct, and
changing a working URL carries risk for no gain. They remain a trap: correct
only by coincidence, and adding a cluster to any of them would break it
silently. The hardcoded strings were referenced nowhere else in the codebase, so
removal changes no other call site.

An external analysis of this crash suggested the page was not registered in the
panel and proposed guarding the cluster sub-navigation with
`->visible(fn () => Route::has(...))`. Both were wrong: the route table shows the
page registered, with `Webkul\TimeOff\Filament\Pages\Dashboard` as its action,
and that guard would have **hidden a working page** to silence the symptom.

### Guard added

`support/tests/Feature/Plugins/PageRouteNameTest.php`: for every page the admin
panel registers, assert `Route::has(static::getRouteName($panel))`, and that
`getUrl()` does not throw - the latter being the exact call the cluster
sub-navigation makes. Both assertions count what they checked and fail on zero,
so neither can pass vacuously. It lives in `SupportFeature` because
**`time-off` has no test suite at all**, which is why both of its route bugs
reached production; anything placed in that plugin would never run.

## 2026-10-23 (/admin returned 500 for every user: an unimported middleware)

### Root cause

`app/Providers/Filament/AdminPanelProvider.php` registered the password-change
middleware in `->authMiddleware([...])` as an unqualified
`RequirePasswordChange::class`, with no `use` statement. PHP resolves an
unqualified name against the file's own namespace, so `::class` produced the
string `App\Providers\Filament\RequirePasswordChange`, which does not exist. The
container could not build it and every `/admin` request failed.

`::class` on an unimported name is **not** a compile error - it simply yields
the wrong string. Pint passed, `php -l` passed, and the middleware's own tests
passed, because the middleware class itself was correct. Only the registration
pointed at nothing. Introduced by the must-change-password work earlier the same
day.

### Fix

Added `use Webkul\Security\Http\Middleware\RequirePasswordChange;`. Verified in
the container that the real class resolves and that the wrongly-namespaced one
does not exist.

### Guard added

`support/tests/Feature/Plugins/PanelMiddlewareTest.php` resolves what the panels
actually report - `getMiddleware()` plus `getAuthMiddleware()`, across every
panel - and asserts each namespaced entry is a class that exists, skipping
router aliases such as `auth` and stripping parameters such as `throttle:60,1`.

The first version of the second test asserted against `getMiddleware()` alone
and **failed against correct code**: this middleware belongs to
`authMiddleware`, because it inspects the signed-in user and must run after
`Authenticate` has resolved one. It now pins the bucket *and* the ordering,
which is the property that actually matters.

## 2026-10-23 (Three guides, and per-company plugins from the company side)

### Guides for user, admin and superadmin

Requested: the in-app guide should live in Help, cover what the system can do
including how-tos, and come in three audiences - user, admin, superadmin -
English only until translation is approved.

What was already there: `Guide` (17 categories, 77 imported reference articles,
search, deep links) and a `Help` page whose "Admin Guide" card was **not a guide
at all** - nine static bullet points in `lang/en/filament/pages/help.php` that
linked nowhere. There was no superadmin guide.

- **`Webkul\Support\Enums\GuideAudience`** (`user` / `admin` / `superadmin`),
  mapped onto roles that already exist rather than onto a new concept:
  `Role::COMPANY_ADMIN` administers one tenant, while super admin and
  `Role::MULTI_COMPANY_ADMIN` administer the installation. Access is cumulative
  upward, since an administrator also needs the end-user pages. An unmarked
  category defaults to `user`, the narrowest audience, so it can never leak
  administrative content.
- **`GuideLibrary::visible()` is the entire access boundary.** The guide has no
  policy and no API of its own, so every public accessor - `index`, `category`,
  `articleList`, `article`, `search`, `articleCount`, `firstCategoryId` - was
  routed through it. Documented in the method, so a future accessor reaching for
  `categories()` directly is recognisable as a mistake.
- **Content split across three files.** `guide.json` keeps the 77 imported
  articles, now tagged `audience: user`; `guide-admin.json` and
  `guide-superadmin.json` hold 10 new hand-written English articles. Authored
  content stays out of the generated reference file, so a hand edit never has to
  find its place inside 2,600 lines of machine-shaped HTML, and re-importing the
  reference content cannot silently drop the authored guides.
- **Audience tabs** on the Guide page, deep-linkable via `?audience=`, hidden
  for a viewer with only one guide. An audience or article the viewer is not
  entitled to is **discarded rather than honoured**, so an administrator's
  shared link cannot open the installation guide for an ordinary user.
- **Help page**: one card per entitled guide, each linking into its own tab.

Every claim in the authored articles was checked against the code before being
written. The per-company plugin article states plainly that grants are recorded
but enforcement is applied per plugin as it is rolled out - documenting that
feature as working would have been false.

Two fixes this forced:

- `Help::canViewAdminGuide()` checked **super admin only**, hiding the admin
  guide from `Role::COMPANY_ADMIN` - the people it is written for.
- `Help::guideCategories()` was a **second** copy of the 620KB file read and
  decode. Two readers of one asset meant the Help page silently missed anything
  the library learned to do, the audience filter included. It now uses
  `GuideLibrary`.

Also removed the guide's stale `website` category - five articles (Blog Posts,
Pages, Customers, Configurations, Settings) documenting the CMS removed on
2026-10-21. Verified no dangling links to those five article ids anywhere, and
that `GuideTest` is data-agnostic. 19 categories / 87 articles after the change.

### Per-company plugins, from the company side

Requested: as a super admin, editing a company should show its plugins and let
them be enabled or disabled there - compared to modules on a PILOT account. The
label was kept as "Plugins" rather than "Modules" for consistency with the
existing Plugins page.

- **`CompanySchemaRegistry`** (new, in `support`, beside `CompanyResource`), a
  concrete `AbstractSchemaRegistry` like `PartnerSchemaRegistry` and
  `ProductSchemaRegistry`. `support/CompanyResource::form()` splats
  `renderForm('main.after')` in the main column; `security/CompanyResource`
  extends it, so the section appears in the company edit screen that is actually
  in the menu.
- **`PluginManager\CompanyContributions`** contributes the section. This
  direction matters: `support` is the base plugin and `plugin-manager` depends on
  it, so putting `Plugin` into the company form directly would have created a
  backwards dependency. Neither `support` nor `security` referenced
  `PluginManager` before, and neither does now.
- It writes the **same `company_plugin` rows** as the Companies action on the
  Plugins page. One pivot, two views, deliberately not a second mechanism.
- **`CompanyContributions::syncGrants()` is public and carries its own
  authorization check.** Filament does not save a hidden component, but the
  write re-checks regardless, because the super-admin `Gate::before` skips
  policies and a forged form payload must not land.
- **`register()` is idempotent.** Its own test caught this: the section was
  contributed **twice**, because `packageBooted()` is not guaranteed to run once
  per process - the install flow and the test bootstrapper both re-boot plugin
  providers, and the schema registry is static with no reset. Without the guard
  the company form grows a duplicate copy of the section in production too.
- `User::isCompanyAdmin()` added, mirroring `isMultiCompanyAdmin()`, with no
  super-admin fallback so neither wider role can be mistaken for a tenant-scoped
  one.

This still **does not gate anything** - it is a better surface onto the same
recorded entitlement, and the UI description says so.

### English only

Per instruction, no guide content was translated. The new `guide-audience`
labels and the company section labels ship in all five locales with English
values, which keeps key parity green. The `help.php` `guides` tree was already
English-only (en 146 lines against 70 elsewhere) and passes via Laravel's
fallback, so the new superadmin strings follow that existing shape.

## 2026-10-23 (Migration registration: the last four allowlist entries)

`KNOWN_UNREGISTERED_MIGRATIONS` in
`support/tests/Feature/Plugins/MigrationRegistrationTest.php` had five entries.
Four were resolved; the fifth needs production data checked first.

### The three employees calendar migrations - deleted as superseded

`support/2026_04_02_000001_create_calendars_table` owns these tables now: it
renames the legacy `employees_calendar*` tables where they exist and creates
`calendars`, `calendar_attendances` and `calendar_leaves` where they do not.
Confirmed against the live schema - the migrations table shows
`create_calendars_table` ran and no `employees_calendar%` migration appears at
all. The three `employees` files only ever duplicated that work, so they were
deleted. `Webkul\Employee\Models\Calendar` and its siblings stay: they are thin
back-compat subclasses of the `Webkul\Support` models.

### The time-off leave-type share - registered, and it was a live defect

`time-off/2026_08_04_100000_share_default_time_off_leave_types` is the companion
repair to the seeder change in the **same commit** (`b9ac266e6`, 2026-08-04):
the seeder began inserting the default leave types with a null `company_id` so
every company sees them under `CompanyScope`'s NULL-is-shared rule, and the
migration nulls `company_id` on databases seeded before that.

Left out of `hasMigrations()`, it never ran. So on any database seeded before
2026-08-04, "Paid Time Off", "Sick Time Off", "Unpaid" and "Compensatory Days
test" stayed owned by the first company and **no other company could see them**.
Now registered. It is a no-op on a fresh install, and `2026_08_04` sorts after
the table-creating migrations so ordering is safe.

### Verified by execution, not by reasoning

An initial claim that the SupportFeature run exercised the new registration was
**wrong**: `PackageServiceProvider` loads a plugin's migrations only when the
package `isCore` or `isInstalled()`, and `time-off` is neither in the test
environment - `time_off_leave_types` does not exist there at all.

`support/tests/Feature/Workflows/SharedTimeOffLeaveTypesTest.php` therefore
installs the plugin, asserts the defaults are shared, then **dirties them to a
company id to reproduce a pre-2026-08-04 database and runs the migration** to
prove the repair, and checks the two deliberately company-owned types
("Training Time Off", "Parental Leaves") are left alone. It asserts the rows
exist first, so a "company_id is null" check cannot pass by counting nothing.
4 passed, 12 assertions.

It lives in the support suite because `time-off` has no tests and no
`phpunit.xml` testsuite - anything placed there would never execute, which is
how the migration went unnoticed in the first place.

### Related, found and deliberately not changed

- `employees/database/factories/CalendarFactory.php` is broken, not merely
  redundant: its model resolves to the `calendars` table but it fills `tz`,
  `status` and `user_id`, columns that do not exist and never did in that
  migration either. `support`'s own `CalendarFactory` has the correct ones.
  Unreferenced, so inert until someone writes an employees calendar test.
- `employees/database/seeders/CalendarSeeder.php` and
  `CalendarAttendanceSeeder.php` write to `employees_calendars` and
  `employees_calendar_attendances`, which do not exist after the rename. Not in
  `employees`' `DatabaseSeeder`, and `support` has registered equivalents.

### Still allowlisted

`support/2026_03_09_000001_add_unique_index_to_companies_name` - adds a UNIQUE
index on `companies.name`, which would fail on any installation already holding
two companies with the same name. Needs checking against production data.

## 2026-10-23 (AGENTS.md claimed an unfixed P0 that is not open)

`AGENTS.md` told every new agent that "eight findings are still open, including
UF-017, an unfixed P0 XSS in Chatter change summaries, and UF-018/UF-021/UF-022
(financial correctness and data integrity)".

All 24 Local states were re-read. **No P0 or P1 is open.** UF-017 is *not
applicable* - this fork was never exposed, and `ChatterEscapingTest` pins that
decision. UF-018/020/021/022/023 are fixed; UF-019 and UF-024 were already
handled; UF-011 to UF-015 were rejected or deferred. What remains is UF-003
(open, and not fixed upstream either), UF-009 (investigated, needs an
install-flow decision), UF-010 (partial), plus UF-001 and UF-007 awaiting a
first remote CI run and browser execution.

The wording predated the 2026-10-22 work recorded in this same change log. It
would have sent the next agent hunting a P0 XSS that does not exist and treating
fixed financial bugs as live in production. Corrected in both places in
`AGENTS.md`. In the register itself the count was marked superseded with a date
rather than rewritten, per the keep-history rule.

## 2026-10-23 (Merge of develop into production: two conflicts)

A conflicted merge of `develop` into `production` was found mid-session, with
`.git/MERGE_HEAD` present and HEAD on `production` - the deploy branch - while a
test suite was running against the same tree. One conflicted file,
`SecurityServiceProvider.php`, carried live `<<<<<<<` markers and therefore did
not parse, so **every** artisan command and test run failed with
`syntax error, unexpected token "<<"`. A `View [pages.guide] not found` failure
seen in a suite around this time was caused by the merge landing underneath a
running run, not by the code under test; a later clean run passed every guide
test.

Resolutions:

- `SecurityServiceProvider.php`: took develop's side, a strict superset. Both
  sides register `provision_company_admin_role`; develop also registers
  `2026_10_21_000002_add_must_change_password_to_users_table`. Taking the other
  side would have silently un-registered that migration - and an unregistered
  migration never runs.
- `docs/handover-actions.md`: kept develop's three corrected "Optional, no
  urgency" bullets **and** production's ~395 extra lines (the 20 October POD
  security verification report). Neither side was a superset.

### Branch divergence worth knowing

`production..develop` is empty, so nothing on develop is missing from
production. But production is **16 commits ahead**, including
`84bf3fe55 feat(logistics): implement fragment-based POD token flow`,
`b76bdc8c3 ...stop link flow (WP-5b)` and two proxy-check-route commits -
security work that exists only on production, showing up as +216 and +54 lines
in the two `stop-link` views. Anyone doing logistics work on `develop` is
editing the older, token-leaking version of those views until production is
back-merged.

## 2026-10-23 (French waybill lang file did not parse)

The one actionable item left from the 2026-10-22 investigation, recorded there
under "Pre-existing drift found, deliberately not fixed". Found while running
`php -l` over every `resources/lang` file in the repo.

### Root cause

`plugins/webkul/logistics/resources/lang/fr/documents/waybill.php` used
typographic quotes (`U+2018` / `U+2019`, i.e. `‘` and `’`) as PHP **string
delimiters** on 11 key/value lines — `confidential-goods`, the `labels` array
opener, and nine label entries. PHP only accepts `'`, `"`, and the heredoc
forms, so the file did not parse at all: `php -l` reported
`syntax error, unexpected identifier "confidentielle", expecting "]"` on line 10.

Consequences, all silent until something touched French:

- Any load of the `logistics::documents/waybill` group with the locale set to
  `fr` is a fatal PHP parse error, so printing a waybill in French returned a
  500. Every other locale was unaffected, which is why it survived review.
- `php artisan translations:check` counted it as `1 parse error(s)`, so the
  Translations Check workflow — which runs `translations:check --details` and
  exits non-zero on any failure — has been red on every push since the file
  landed in `a3eec3a10` (2026-10-20, "add confidential shipment fields and
  product line types"). It was one of eleven pre-existing parity failures, not
  the only one, so CI staying red was not itself a signal.

The typographic quotes almost certainly came from writing French prose into the
file through an editor with smart-quote substitution; the *content* was already
correct.

### Fix

Only the delimiters changed — 11 lines, ASCII `'` on both sides. The single
surviving `U+2019` is inside the string `'Numéro d’expédition'`, where it is
legitimate content and parses fine. No wording was touched in any locale.

Fixed in commit `6f827a879`.

### Regression test

`plugins/webkul/logistics/tests/Feature/Documents/WaybillTest.php` gained
`it('loads the waybill translations in every supported locale')`. It sets the
locale to each of `en`, `ar`, `es`, `fr`, `pt_BR` in turn and loads
`logistics::documents/waybill.labels`, asserting the group is an array holding
`waybill-number`, `shipment-number`, `consignor` and `date`; that `title`
resolves to something other than the raw key (which is what Laravel returns
when a file is missing or unloadable); and that the five titles are distinct.

Loading the group is the point: a parse error in one locale file makes
`trans()` throw, so this test fails for exactly the failure that shipped. It
also pins that no locale is left behind when the group is extended, which is
the more common way this file will break next.

### Verification

- `php -l` on all five `documents/waybill.php` locale files: parse OK.
- Structural check that the four asserted label keys exist in all five locales:
  4/4 each.
- `title` values are distinct across the five locales (`Waybill` / `بوليصة الشحن`
  / `Carta de porte` / `Lettre de voiture` / `Conhecimento de transporte`), so
  the uniqueness assertion is a real assertion and not a tautology.

## 2026-10-22 (Register close-out: UF-008, UF-009, UF-012, UF-016)

The last four open entries in `docs/upstream-fix-adoption-plan.md`. Each got a
verdict with file:line evidence before any change; two produced work, two
produced a recommendation and were deliberately left alone.

### UF-016 — documentation drift (fixed)

Reconciled by marking superseded claims, not deleting them:

- **`AGENTS.md`** — the `boost:install` version block still reads Laravel 11 /
  Filament 4 / Livewire 3 / PHP 8.3.21. It now carries a dated banner at the top
  pointing at `composer.lock` and the project-memory line: Laravel 13.21.1,
  Filament 5.7.6, Shield 4.2.0, Livewire 4.3.4, Pest 4.7.5, PHPUnit 12.5.30,
  PHP >= 8.4.1 — all re-read from `composer.lock` / `composer.json` today.
- **`docs/handover-actions.md` §5** — three false statements: "Filament 5.7.6
  needs `composer update`" (it is locked), restructure totals of 70/51 (the
  backlog's own 2026-10-21 note makes them 68/49), and "remaining suites: most
  of `AccountFeature`, plus project and product" (superseded 2026-09-29, when
  all thirteen suites ran green together — AccountFeature 526, ProjectFeature
  86, ProductFeature 250, zero failures).
- **`docs/restructure-backlog.md`** — already reconciled on 2026-10-21; the four
  dead entries are now also marked `REMOVED 2026-10-21, moot` inline, because a
  header note is easy to skim past when the list is the thing being worked from.
- **`docs/running-tests.md`** — documented one shared database and serial runs
  only. Now documents the per-work-package database and the `-e DB_DATABASE`
  override (`phpunit.xml` sets it without `force="true"`, so the env var wins),
  and keeps the interleaving warning scoped to one database.
- **`docs/landing-page-brief.md`** — "Website and blog pages managed from the
  same admin" was false: Blogs and the CMS pages were removed 2026-10-21 and
  `plugins/webkul/website` has no admin resources, only portal actions/pages.
  Replaced with the customer-portal line, correction dated inline.
- **`docs/logistics-plan.md`** — found while checking, not in the original list:
  its table of plugins that subclass `PartnerResource` still credited `website`
  with "addresses, contacts". The plugin no longer subclasses anything; it
  contributes registry entries from `website/src/PortalContributions.php`. The
  argument the table supports is unaffected.

Left alone as still true: `unified-product-resource-plan.md`'s
"Contacts/Website partner screens", and the historical Website references in
this file and in the adoption plan.

### UF-008 — translation reference audit (scanner existed; extended; audit not fixed)

> **Superseded later the same day.** The 84 references this entry left broken
> were fixed; see "UF-008 follow-up: the 84 broken translation references
> fixed" below. The scanner work described here is unchanged.

The scanner was already there as `--references` on the existing command
(`plugin-manager/src/Console/Commands/FindMissingTranslations.php:259`), with
three command tests under `PluginManagerFeature` — so the register's "partial
tooling" line was stale.

**Extended:** root-scope references (`__('admin.key')`, no `::`) were skipped
outright. They now resolve against `lang/en` and are reported when the key is
missing. A missing root *file* is still not an error, because `auth`,
`validation`, `pagination` and `passwords` are translated inside the framework —
reporting those would be a false positive that makes the mode unusable. Three
tests pin it. CI runs `translations:check --details` (locale parity, no
`--references`), so this changes nothing in CI.

**The audit result is the finding.** `translations:check --references` reports
13 of 29 groups failing — **84 invalid references**: accounts 27, support 14,
employees 7, purchases 6, recruitments 6, time-off 5, fields 4, inventories 4,
manufacturing 4, accounting 2, partners 2, projects 2, chatter 1. Spot-checked
as genuine: `accounts::enums/display-type.*` has no EN file at all,
`chatter::app.*` has no `resources/lang/en/app.php`, and
`fields::...textarea.helper-text` exists at a different depth. Upstream
`33ed6ac32` fixed ~48 of these across accounts, employees, fields and support;
that commit is not ported.

Not fixed here on purpose: each fix adds or moves keys in five locales per
plugin, and this project checks translation source structure line by line, so a
mis-shaped edit looks like a structure change. Own change, own run.

### UF-009 — installer country/currency (recommended, not implemented)

`erp:install` has no `--country`/`--currency` and no `configureLocalisation()`;
the default currency is `Currency::active()->first()`
(`InstallERP.php:422`). The helpers upstream relies on are already local
(`Currency::findByCode()` :41, `resolveDefault()` :50).

`erp:install --force` bootstraps every test run
(`TestBootstrapHelper.php:248`), both CI workflows and the production image —
which is why upstream's second commit is titled "Fixed failing test": an
unguarded interactive prompt hangs a bootstrap rather than failing it. The port
sketch, resolution order and six-case test matrix are recorded in the plan.
Nothing was changed and **no reinstall path was exercised**.

### UF-012 — employee resume attachments (deferral confirmed)

`employees_employee_resumes` has no file, path, disk, size or MIME column and
the plugin has no upload code; `ManageResume` is a text-only form. Nothing in
migrations, models, policies, translations or tests references a resume file.
Deferral stands: the work is the security review (tenant-S3 disk choice, MIME
and size validation, cross-company authorization on download, retention), and
the fork already has `DeliveryProofsRelationManager` and Chatter attachments to
reuse when it is done.

### Verification

- `plugins/webkul/plugin-manager/tests/Feature/TranslationReferenceCommandTest.php`
  on isolated database `aureuserp_testing_uf1316`: **6 passed, 12 assertions** —
  the 3 existing tests plus 3 new ones (root key present, root key missing,
  framework-provided root file).

Two defects in that file were found only because it had never run on its own,
and were fixed rather than worked around:

- It never required `TestBootstrapHelper`, so `php artisan test <this file>`
  died in `TestCase::setUp()` with `Class "TestBootstrapHelper" not found`; it
  passed only when another file in `PluginManagerFeature` loaded the helper
  first. The `require_once` now matches `PluginAccessTest.php`.
- `expectsOutputToContain('messages.missing')` could never pass. A reference
  line reads `- path:3 ns::messages.missing`, and PendingCommand hands each
  `doWrite` call to the **first** registered substring expectation it matches,
  so `src/Reference.php:3` consumed that line and `messages.missing` never saw
  it. The assertions are now one per output line (`:3`, the key message, `:4`,
  the missing-file message), which asserts the same facts.

- `php artisan translations:check --references` and `--references --details`
  run in Sail: 29 groups, 16 pass, 13 fail, 84 invalid references. Both runs
  were read-only against the local Sail database.
- `php -l` on both changed PHP files; Pint clean; `git diff --check` clean.
- No production or managed database was used.

Also corrected in this file: the UF-006 verification line says 21 tests / 35
assertions. The suite as committed — including the four tests added after that
run — was re-run on `aureuserp_testing_uf006` and passed **25 tests / 43
assertions**. That line is superseded, not wrong at the time.

## 2026-10-22 (UF-008 follow-up: the 84 broken translation references fixed)

The scanner added earlier today reported 84 invalid translation references
across 13 plugins. Each one renders as a raw key on screen, because Laravel
returns the key when a translation is missing. All 84 are now resolved and
`php artisan translations:check --references` reports **29 checks, 29 passed,
0 failed**.

### The classification mattered more than the edits

Every reference was decided **before** anything was written. Creating a lang
file for a reference that was merely misspelled makes the scanner go green
while the text sits in the wrong place and the real file is still unused, so
the two cases were separated first:

- **Missing file or key (created).** `accounts::enums/display-type.*` - no
  `accounts/resources/lang/en/enums/display-type.php` existed at all (18 refs);
  `accounts::system.tax-formula.*` - keys absent from `system.php` (9 refs);
  `support::enums/company-status.*`; the three
  `manufacturing::models/work-center-*` files; scalar keys in support
  `company.php` and `bank.php`, employees `employee.php`, chatter
  `activity-action.php`, accounting `journal-entry.php`, partners
  `industry.php`/`tag.php`, projects `view-task.php`, the time-off
  `leave-type`/`mandatory-days` filters, and recruitments `job-position.php`.
- **Wrong path (the reference was fixed, lang files untouched).** accounting's
  `BankAccountsRelationManager` pointed at a `bank-account-relation-manager.php`
  that exists only under `accounts::`; `purchases`' `ViewOrder` pointed at
  `inventories::.../view-order.php` although purchases has its own copy;
  `inventories`' `ManageOperations` asked for `delete.notification.title` where
  the file has `success`/`error`; `inventories`' `EditProduct` asked for
  `error.available-quantity`; `recruitments`' `CandidateResource` omitted the
  `actions.` segment of `table.actions.empty-state-actions`; `fields`
  `FieldResource` used kebab-case `textarea.helper-text`/`hint-color`/`hint-icon`
  where the lang file uses camelCase; `purchases` `Requisition` asked for
  `starts-at`/`ends-at` where the log-attribute keys are `starts_at`/`ends_at`;
  chatter's `ChatterPanel` asked for a `chatter::app.*` file that does not exist.
- **Typo'd key names (fixed in the lang files, 5 locales).** `fields`
  `datetime.first-fay-of-week` -> `first-day-of-week`. `inventories`
  `edit-product` referenced `error.available-quantity`, while a translated
  `error.qty-not-zero` sibling sat unreferenced in all five locales - the
  reference was pointed at the existing string rather than a duplicate created.

Root cause of the whole family: references are plain strings, so nothing checks
them at build time, and the `--references` mode that now does was added today.

### Wording chosen rather than derived

Upstream commit `33ed6ac32` had already fixed about 48 of these, so its wording
was reused wherever a key matched. Where no source existed, the wording is the
obvious reading of the key and is worth a native-speaker pass:

- `accounts` `enums/display-type.php` - Product / COGS / Tax / Payment term /
  Account / Category / Rounding / Deposit / Deduction.
- `support` `enums/company-status.php` - Active / Inactive.
- `manufacturing` `models/work-center-loss-type|productivity-log|productivity-loss`
  - field labels derived from the model columns.
- chatter `activity-action.setup.form.fields.body` - "Body".
- support `company.php` bulk force-delete error - "Some companies could not be
  force deleted." / "No companies were force deleted."
- employees `categories` - "Categories"; the six `*-copy-message` strings -
  "Copy of ...".

### Task 2 - dead accounts migration deleted

`plugins/webkul/accounts/database/migrations/2025_11_21_100010_create_accounts_account_companies_table.php`
duplicated the registered `2025_01_30_054955_create_accounts_account_companies_table.php`
(same `Schema::create('accounts_account_companies')`), so registering it would
have broken `migrate:fresh` with "relation already exists". It is deleted, not
registered. The registered version is the one that matches real usage -
`Account::companies()` declares no `withTimestamps()` and
`AccountingSetupService` inserts no timestamps - so the surviving migration is
the correct one. Its entry and docblock bullet were pruned from
`KNOWN_UNREGISTERED_MIGRATIONS` in
`plugins/webkul/support/tests/Feature/Plugins/MigrationRegistrationTest.php`,
whose second test fails when an allowlist entry has been resolved. The other
allowlist entries are untouched; in particular
`support/2026_03_09_000001_add_unique_index_to_companies_name` adds a UNIQUE
index on `companies.name` and needs a production data check before anyone
registers it.

### Verification (actually run)

- `php artisan translations:check --references` in Sail on
  `aureuserp_testing_uf4`: **29 checks, 29 passed, 0 failed**, "All translations
  are synchronized with EN!".
- `php artisan translations:check --details` (locale parity, whole repo, all
  plugins): 104 checks, 93 passed, 11 failed - all pre-existing, detailed below.
- `MigrationRegistrationTest` on `aureuserp_testing_uf4`:
  **2 passed, 4 assertions**, 486s (the first test runs `migrate:fresh`, which
  succeeded without the deleted migration).
- `php -l` on the 10 changed PHP files outside `resources/lang`, and on all 105
  referenced PHP files (lang included) for BOM: clean, no BOM. Every
  `resources/lang` file in the repo also lints clean except one pre-existing
  failure (below). A PowerShell `Set-Content -Encoding UTF8` rewrite of
  `ViewOrder.php` had introduced a BOM; it was stripped and re-verified.
- Pint clean, `git diff --check` clean. Local Sail database only; no managed or
  production database was touched.

### Pre-existing drift found, deliberately not fixed

- **`plugins/webkul/logistics/resources/lang/fr/documents/waybill.php` does not
  parse.** It uses typographic quotes (`U+2018`/`U+2019`) as PHP string
  delimiters, so `php -l` fails at line 10 - and any load of
  `logistics::documents/waybill` in French fatals. Introduced by `a3eec3a10`
  (2026-10-20). Out of scope here (logistics lang files were excluded from this
  pass); one-line fix per string.
  **Superseded 2026-10-23 — fixed, with a regression test; see the 2026-10-23
  entry above.**
- Locale parity gaps, all older than today: support `filament/pages/help.php`
  (EN 147 lines, locales 71), support `fr/filament/resources/currency.php` (two
  extra `bulk-actions.delete.notification.error.*` keys), employees
  `fr/filament/resources/employee.php` + `relation-manager/resume.php` (French
  resume-attachment keys with no EN counterpart), products `fr` (two extra
  `exceptions/*-in-use.php` files and three `attribute.php` keys), security
  `user.php` (7 `form.validation.*` keys) and `livewire/accept-invitation.php`
  (1 key) missing in all four locales.
- `recruitments` `job-position.php` `company-modal-title` is also used for the
  recruiter (User) modal that reuses the same action.

## 2026-10-22 (Per-company plugin availability)

Installing a plugin was global, so enabling one for a single customer enabled it
for every customer (user request, 2026-10-21: availability should follow each
company's subscription).

**Built and tested.** `company_plugin` pivot, where a row means that company may
use that plugin and no row means it may not — no boolean, because that adds a
third state ("row saying false" versus "no row") nothing needs. Read through
`PluginManager\Support\PluginAccess`, modelled on `LogisticsAccess`: scoped
singleton, read without the company scope so a record's own company can be
checked when it is not the active one, `Schema::hasTable` guard for boot before
migration, and only booleans leaving the class. `CompanyPluginProvisioner` grants
the installed set on `Company::created`. A "Companies" action on the Plugins page
manages grants; before it, they could only be changed with SQL.

The migration backfills every existing company × installed plugin, and new
companies are granted the same set, so **this changes nothing on deploy** —
removing access is the deliberate act. `PluginManagerFeature`: 8 tests, 18
assertions.

**Not done: the gating itself.** Nothing calls `PluginAccess` yet, so grants are
recorded but do not affect what anyone sees. Two findings shaped how that must be
done, both now in AGENTS.md:

1. Gating inside `Plugin::register()` is the obvious one-liner and is wrong.
   Production runs `Artisan::call('optimize')`, which caches routes, so the
   company active when the cache was built would decide the routes for everyone.
   `LogisticsPlugin::register()` checks only `isPluginInstalled` for this reason.
   Per-company gating has to sit where it is evaluated per request: cluster and
   page `canAccess()`, widget `canView()`, resource `canViewAny()`, the policies,
   and any service that re-checks because the super-admin `Gate::before` skips
   policies.
2. It is all-or-nothing per plugin. Hiding navigation while policies still allow
   access leaves records reachable by direct URL and through the API — it looks
   gated and is not, which is worse than not having started.

Surveyed as a pilot, the Projects plugin alone is 2 clusters, 3 pages, 5 widgets,
8 policies and its resources: roughly 20 files. Deliberately left unstarted
rather than shipped half-gated.

## 2026-10-22 (Upstream findings UF-020–UF-024)

Investigated the four stale v1.6.0 upstream findings assigned for local adoption.

- UF-020 was real in the table-views plugin: `SavedView::getModel()` returned
  an Eloquent record from a method whose Filament contract requires a model
  class string. The method now honours that contract, and the table-view action
  uses `getRecord()` when it needs the saved record.
- UF-021 was real in the employee factory path: `parent_id` and `coach_id`
  defaulted to user IDs even though both foreign keys reference employees.
  The defaults are now nullable; explicit managers remain employee records.
- UF-023 was real for bulk currency deletion: the default bulk action had no
  local error notification for a database restriction, while individual
  delete paths already handled it. The bulk action now catches the query error
  and sends the existing currency-in-use notification.
- UF-024 was already handled. Variant generation already derives names from
  the parent name and does not mutate the parent, so no product code was
  changed.
- UF-004 was completed as the next accounting regression package. The existing
  company-currency observer now has coverage for normal changes, blocked
  changes after journal entries, hierarchy propagation, and unrelated-company
  isolation. The account test helper now selects its baseline company by ID.
- UF-005 was completed with five payment-register bank-account regression
  scenarios. The tests cover inbound journal accounts, missing inbound
  accounts, outbound vendor accounts, and preferred bill bank accounts. The
  upstream cross-company bank-account scenario was intentionally excluded
  because this fork's BankAccount schema has no company_id contract.
- UF-006 was completed by persisting valid locale-switcher choices in the
  session and defining the precedence as query, session, user preference,
  then application fallback. Profile language updates continue to update both
  the stored preference and session.
- UF-007 was implemented by making Website page and blog publish/draft helpers
  assert their edit URL and required action visibility instead of silently
  skipping unavailable actions.
- UF-018 was pinned with an invoice payment regression test asserting that the
  normal payment registrar leaves the invoice posted, paid, and fully
  reconciled.
- UF-022 was completed by adopting the upstream purchase-form conversion fix
  and persisting the order currency rate used for company-currency totals.
  Regression coverage verifies fallback costs, vendor prices, vendor
  filtering, UOM conversion, and exact converted values.

Verification on the isolated Sail PostgreSQL database
`aureuserp_testing_uf2`: `SavedViewTest.php` passed 1 test / 2 assertions;
`FactoryTest.php` passed 6 / 10; `CurrencyDeleteTest.php` passed 1 / 7; and
`ProductVariantNameTest.php` passed 1 / 2. The table-views plugin was added to
`phpunit.xml` because it previously had no test suite. No production or
managed database was used.

UF-004 verification used isolated database aureuserp_testing_uf4:
CompanyCurrencyGuardTest.php passed 8 tests / 8 assertions. No production
database was used.

UF-005 verification used isolated database aureuserp_testing_uf5:
PaymentRegisterBankAccountTest.php passed 5 tests / 11 assertions. No
production database was used.

UF-006 verification used isolated database aureuserp_testing_uf6:
SetLocaleMiddlewareTest.php and ProfileLanguageUpdateTest.php passed 21 tests /
35 assertions. No production database was used.

UF-007 Playwright discovery passed for the two Website spec files and listed
18 tests. Browser execution remains pending because the configured local app
at 127.0.0.1:8000 was unavailable.

UF-018 verification used isolated database aureuserp_testing_uf18:
InvoiceTest.php passed 1 focused test / 3 assertions. No production database
was used.

UF-022 verification used isolated database aureuserp_testing_uf22:
OrderCurrencyConversionTest.php passed 8 tests / 8 assertions. No production
database was used.

## 2026-10-20 (Logistics WP-5b: fragment-based POD token flow + WP-13 final closure)

### WP-5b: fragment-based POD token flow

**Problem resolved.** POD stop-link tokens were in the URL path
(`/logistics/pod/<token>`), which every HTTP access log layer retains.
Laravel Cloud keeps request paths with no redaction option, making log-scrubbing
an unreliable compensating control.

**Solution.** `StopLinkService::issue()` now returns a fragment URL:
`route('logistics.stop-link.landing').'#'.$token`. The fragment is stripped by
browsers before the HTTP request is sent — the token never reaches any server and
never appears in any log. Three new routes handle the flow:

- `GET /logistics/pod` — static landing page; JS reads `location.hash`, clears
  it immediately with `history.replaceState`, POSTs the token to the exchange
  endpoint.
- `POST /logistics/pod/exchange` — receives token in POST body, returns the
  capture form with token in a hidden field.
- `POST /logistics/pod` — receives token + form fields, validates and captures.

Old path-based routes (`/logistics/pod/{token}`) remain for backward
compatibility. Sentry redaction covers them.

The rate limiter reads the token from the route param (old flow) or POST body
(new flow). The landing page has no token and gets only the per-IP limit.

On validation failure, `submit()` re-renders the form directly (no redirect) with
the token still in the hidden field. A redirect on an anonymous endpoint would
send the driver to a blank page with no way back.

**Files changed:** `StopLinkService`, `StopLinkController`,
`LogisticsServiceProvider`, `routes/web.php`, `stop-link/landing.blade.php`
(new), `stop-link/form.blade.php` (new), `StopLinkTest.php` (4 new tests +
migration to exchange/submit flow), `stop-link.php` lang files (5 locales,
`landing.*` keys added).

**Verified:** LogisticsFeature 195/195, 751 assertions. `translations:check`
4/4 pass. Pint passed.

### WP-13: final closure

- Supabase Security Advisor: **passed** — no new findings on the 19 logistics
  tables. The 2026-09-02 revoke of `anon`/`authenticated` grants covers all
  tables created since.
- Supabase Performance Advisor: **passed**.
- `§0b` (POD tokens in edge logs): **resolved in code** by WP-5b above.
  `docs/handover-actions.md` §0b updated accordingly.
- `CHANGELOG.md` v1.7.0 updated to name the fragment-token security property.
- `docs/logistics-plan.md` WP-5b → `done`, WP-13 → `done`.

WP-13 is closed. `feature/logistics` is ready to merge.

## 2026-10-19 (development-tool review)

Reviewed Agent Skills, Context7, Graphify, 21st MCP and Strix against the actual
DEKA ERP stack, repository rules and current Codex client. Added Context7's
read-only HTTP MCP endpoint to `.mcp.json` and an ignored, project-scoped Codex
configuration at `.codex/config.toml`. No API key is stored; anonymous access is
used, with an optional user-held key only if higher rate limits are later needed.

The remaining tools were not installed. Addy Osmani's general lifecycle skills
repeat the repository's existing inspection, testing, review and security rules,
and its git workflow conflicts with the user-owned commit process. 21st MCP is a
React component service and does not match Filament/Livewire/Blade. Graphify's
official local PHP-capable engine is useful at this repository's size, but this
machine has no usable Python/uv runtime and the current Codex installer has open
project-mutation/path defects; hosted Graphify additionally needs OAuth and an
indexed repository. Strix requires Python 3.12, Docker and an LLM login/key, while
active testing also needs an explicitly authorized isolated target and synthetic
credentials.

Verification and rollback details are recorded in the handoff for this session.

## 2026-09-30 (Logistics WP-12 translation completion)

Completed the Logistics interface and document translations for Arabic,
Spanish, French and Brazilian Portuguese. Each locale gained the 17 files added
after WP-12's foundation slice, while `exceptions.php` gained 16 messages and
the company-settings page gained seven fields/help texts. The new values reuse
the existing Logistics vocabulary, preserve interpolation and pluralization
tokens, and mirror English key order and structure.

The first post-translation checker run revealed a checker/source interaction not
present in the original backlog: bare numeric TTL keys made the checker compare
the entire translated source line. The five TTL keys are now written as quoted
numeric strings in English and all four locales. PHP still exposes integer keys,
confirmed by the existing stop-link regression, while translated hour labels no
longer produce false structure mismatches.

Verification: the Sail Logistics translation checker passed **4/4 locale sets**
with zero issues; the focused TTL regression passed **1 test / 1 assertion** on
`aureuserp_testing_wp12`; explicit-path Pint and final dirty Pint both passed.
The Arabic stop-link and waybill RTL wiring was reviewed statically. No browser,
rendered-PDF linguistic review or full LogisticsFeature run was performed for
this translation-only slice.

## 2026-09-30 (Logistics WP-5b: independent adversarial security review)

The independent pass found that the public POD flow only worked in tests because
the test process retained the issuing user's scoped company context and Sanctum
identity. A real anonymous request had neither: `asIssuer()` tried to activate the
link company before authenticating its issuer and failed authorization before it
could capture anything.

The service now authenticates on the web guard first, rebuilds `CompanyContext` for
that issuer, validates the tenant assignment, and restores the prior guard, user and
company context afterward. Issuance and capture serialize on the shipment before the
link row, issuance enforces an out-for-delivery shipment and open delivery stop, and
lost issuer authority or a disabled company produces the same public 404 as every
other unusable token. Public success/refusal pages now send no-store, no-referrer,
anti-framing and nosniff headers. A cross-token per-IP throttle regression was added
and its config now states the `TRUSTED_PROXIES` dependency plainly.

Focused `StopLinkTest` passed **33 tests / 104 assertions** before the last limiter
case; the complete Logistics run then passed all **34 stop-link tests**. The complete
suite reported **190 passed, 1 failed / 734 assertions**. Its only failure was an
outside-WP-5b timing-sensitive assertion in `ShipmentWorkflowTest`: an inherited
`occurred_at ASC` relationship order outranked its appended `id DESC`. The exact case
reran green at **1 test / 5 assertions**. The full suite is not claimed green. The
outside-owner one-line correction is in the WP-5b handoff request. Final
`vendor/bin/pint --dirty --format agent` passed after all executable review changes.

WP-5b stays at `review`: independent application-code review is complete, while
`docs/handover-actions.md` 0b (edge logs) and 0c (trusted proxies) still require
production access.

## 2026-09-30 (Logistics WP-13 hardening and release preparation)

WP-13 completed the locally provable release work and moved to `review`; the
production-copy rehearsal, Supabase advisor, independent stop-link review,
customer-page decision and translation package remain explicit release gates.

### Review fixes

- Audited action/policy authorisation and every tenant-facing list, view, search,
  widget, report, export and file path. Waybill printing, stop-link revocation and
  charge creation now repeat their company-switch/ability checks where work is
  performed.
- Added a shared owning-company storage context for POD files and expense
  receipts. Tenant-S3 URLs now use the authenticated secure-storage route and
  receipt uploads retain random names, MIME/size validation and Filament's
  existing-path tampering protection.
- Fixed the unbilled currency eager load, added missing table eager loads,
  removed an unsafe generic trip detach that left stops and shipment state
  behind, dropped two unused pre-release shipment total columns, and pinned all
  Logistics page permission names.
- Added `docs/logistics.md` and v1.7.0 user-facing release notes.

### Verification

The same-day inherited release matrix was green before WP-13 (Logistics 177;
Account 526; Inventory 884; Sale 143; Security 55; Support 115; Project 86;
Manufacturing 41; Product 250; Purchase 178; Partner 74; Accounting 54;
Employee 5). A post-edit full Logistics rerun was attempted but interrupted and
is not claimed: 25 tests displayed green before cancellation. Eight directly
affected regressions subsequently passed across two commands with 27 assertions;
the split run followed correction of two test-fixture API errors. Pint passed on
tracked changes and explicit new PHP paths. The final Filament
`preventFilePathTampering()` guard was source-reviewed against the installed
vendor code and formatted, but its added schema assertion was not rerun in Pest.

The rendered public POD form was exercised in installed Chrome at 320x568 and
390x844. Both had zero horizontal overflow, nonblank screenshots, correctly sized
canvas pixels, signature capture and disabled repeat submission.

`translations:check --plugin=logistics --details` was also run. It correctly
failed, but found more than the old WP-12 note: ar, es, fr and pt_BR each have 17
missing files, 23 missing keys and 2 structural mismatches. Those language-owned
files were not changed in WP-13.

## 2026-09-29 (Everyone in a company sees the company's records, and the scoped-parent family)

One decision and one bug family, connected: switching the ownership scope on in
tests exposed both. Full detail and the decision trail in
[`docs/company-admin-role-plan.md`](company-admin-role-plan.md) sections 4d-4j.

### The decision: company-wide visibility is the default

**User, 2026-09-28:** *"all users in the same company should be able to see each
others invoices and details in the company"*, and *"if its currently individual, and
can be given as rights then its also good"*.

`users.resource_permission` defaulted to `individual`, which makes `OwnershipScope`
filter eight document models - invoices, bills, sales and purchase orders, stock
operations, manufacturing orders, projects, tasks - down to rows the user created or
was assigned. Colleagues in the same company could not see each other's invoices.

The old value arrived by two routes, and **the admin panel was never one of them**:
the Users page form already defaulted to `global`. What produced `individual` was
`UserInvitationService::accept()`, which hard-coded it for every non-Multi-Company
Admin - the onboarding path - plus the column default for anything created without
the field.

Both changed, and existing `individual` rows were migrated. `group` rows were left
alone: nobody ever *chose* `individual`, but `group` has to be picked in the form
**and** given a team, so it is a real decision by whoever configured that user.

Nothing was taken away. `PermissionType` keeps all three cases, the Users page still
offers them, and every rule they drive still works and is tested. "Individual" is now
a deliberate choice rather than an accident of a column default.

**Before deploying:** this *widens* visibility. Tenant isolation is untouched -
`CompanyScope` is a separate scope. Afterwards, confirm the remaining exceptions are
intended: `select id, email, resource_permission from users where resource_permission <> 'global'`.

### Why ownership was never tested, and what happened when it was

`OwnershipScope::apply()` began with a bare `app()->runningInConsole()`, and
`artisan test` **is** the console - so the scope was inert in every test in the
repository. `CompanyScope`, `CompaniesScope` and `AllowedCompanyScope` all guard that
line with `&& ! runningUnitTests()`; this one was the outlier. No test could show
that `individual` restricted anything, or that it did not over-restrict.

Adding the exemption is test-only (`runningUnitTests()` is `env === 'testing'`, so
production console still skips it). With it live: **2,551 passed, 20 failed** across
all thirteen suites. 17 were fixtures owned by the wrong user - correct product
behaviour, fixed by owning them. 3 were real defects, and they opened the family
below.

Also fixed while covering the branch nobody had tested: a `group` user with **no
team** resolved to an empty authorized-id list, which `OwnershipScope` read as *no
restriction* - so they saw everything, exactly as if they were `global`, silently.
`Bouncer` now fails closed to their own rows.

### The family: reading a scoped parent inside a model hook

Six models derived a child's `company_id` (or state) by reading the parent **through
a relation**. The parent carries a global scope, so for an actor not entitled to see
it the relation returns null, and then one of three things happens - all silent bar
the last:

- `??=` or `?->` swallows it and `company_id` stays **null**, which `CompanyScope`
  reads as *shared*: the row becomes visible to every company;
- a `?? current_company_id()` fallback files the row under the **actor's** company;
- or nothing swallows it and the save dies.

Fixed: `Project\Models\TaskStage`, `Project\Models\Task`,
`Account\Models\MoveLine` (plus `chaperone()` on all five of `Move`'s line
relations), `Inventory\Models\MoveLine`, `Inventory\Models\ProductQuantity`,
`Manufacturing\Models\Move`.

`Task` is the instructive one: its author *knew* the parent read had to escape
`CompanyScope` and said so - they just did not know `Project` carries a **second**
global scope. `withoutGlobalScope(CompanyScope::class)` reads as careful and is not.

`Manufacturing\Models\Move` was the only **destructive** member: `=` rather than
`??=`, so an unreadable operation type overwrote a stored `warehouse_id` with null on
every save. A test caught it going from 22 to null on a plain `save()`.

### The rule now lives in one place

`InheritsParentCompany` already stated the rule - *child rows take their parent's
company, never the session's* - but lived in **Logistics**, an optional plugin. Core
`inventories` cannot depend on that. It moved to
`Webkul\Support\Traits\InheritsParentCompany` with its exception and five
translations; the Logistics trait is now a one-line alias and the Logistics exception
extends the Support one, so the six models using it were untouched.

Corrected while moving: it read parents with `withoutGlobalScope(CompanyScope::class)`
- the `Task` trap exactly. Now `withoutGlobalScopes()`, a no-op for existing callers
but it stops the shared rule carrying a known trap forward.

### Three mistakes of mine, each with a transferable lesson

- **A factory default can make a test assert the opposite of its name.**
  `ShipmentFactory` fills `expected_delivery_at`, so a WP-11 test for "delivered with
  no promised date" silently exercised the promised-date branch and passed green.
  Null the column explicitly whenever a test turns on its absence.
- **`->change()` on an enum column cannot work on PostgreSQL.** Laravel emits an
  inline `check (...)`, which Postgres rejects. The failure mode is the danger:
  `migrate:fresh` dies, so **every** test fails with 0 assertions - 55 in one suite,
  47 in another, from one line. Use `ALTER COLUMN ... SET DEFAULT` when only the
  default changes.
- **`Builder::value()` applies the model's casts.** I typed a helper `?string` and
  got a `MoveState` enum, breaking 13 tests - all of which surfaced as
  *action-visibility* failures, which reads like a state-machine regression. Running
  **one** of them alone gave the TypeError in a single line. When many failures share
  a theme, get the exception from one before theorising about the theme.

And twice, writing the test first stopped me changing working code: `Inventory\MoveLine`'s
company derivation and `Manufacturing\Move`'s creating hook both turned out not to be
broken the way reading suggested.

### Verified

| Suite | Result |
|---|---|
| InventoryFeature | 884 passed |
| AccountFeature | 526 passed |
| ProductFeature | 250 passed |
| PurchaseFeature | 178 passed |
| LogisticsFeature | 177 passed |
| SaleFeature | 143 passed |
| SupportFeature | 115 passed |
| ProjectFeature | 86 passed |
| SecurityFeature | 55 passed |
| AccountingFeature | 54 passed |
| ManufacturingFeature | 41 passed |
| PartnerFeature | 74 passed |
| EmployeeFeature | 5 passed |

Zero failures. New coverage: `projects/.../OwnershipScopeTest.php` (6),
`accounts/.../OwnershipScopeTest.php` (4),
`manufacturing/.../ScopedParentReadTest.php` (3), plus regression tests in
`CompanyIsolationTest`, `DocumentCompanyResolutionTest` and `RecordIntegrityTest`.
Ownership is now covered as a *feature* - `individual`, `group`, `global`, column
sources, the `users` pivot and followers - rather than being the untested default.

## 2026-09-25 (Logistics WP-11: reports)

Five report pages in a new `Reporting` cluster, each a filtered Filament table
with an export: shipment register, delivery performance, shipment profitability,
and trip history by vehicle and by driver. No migrations - reports read what the
other packages already store.

### What the numbers are allowed to say

The judgement calls here are all about not producing a figure that looks
authoritative and is not.

- **Costs are approved and billed expenses only.** A draft or submitted expense
  is a claim someone has made. Counting it would let a margin move on paperwork
  rather than on work.
- **"Carrier cost" is not a separate term.** Carrier spend is already an expense
  in the `SUBCONTRACTOR` category, which is what the shipment form tells the user
  ("Carrier costs are recorded as expenses"). Charges minus expenses covers it;
  a third term would double-count.
- **Nothing is converted between currencies.** Each figure is in the shipment's
  own currency and the currency is a named column. A column that silently added
  several currencies would be meaningless while looking exact.
- **A delivery with no promised date is "No date promised", not "on time".**
  Scoring it would invent a commitment nobody made - and it would flatter the
  statistic, which is the direction these mistakes always run. The page's
  subheading states the exclusion so the percentage cannot be misread as
  covering everything.
- **Distance is blank unless both odometer readings exist.** One reading says
  nothing about distance; printing it as one is worse than printing nothing.

Profitability sits behind `view_financials_logistics_shipment` as well as its
page permission: an operations user who may read a register has no business
reading margins. Both its figures come from `withSum` subqueries rather than
loading charges and expenses into memory, because a quarter's register would
otherwise pull tens of thousands of rows to add up two columns.

### A test that could not have failed

The test for "delivered, but nobody promised a date" passed for the wrong
reason. `ShipmentFactory` fills `expected_delivery_at` with now + 2 days, so the
shipment under test *had* a promised date and was scored `on-time` - the branch
the test existed for was never reached. The fix is one explicit `null`, but the
general lesson is worth keeping: **a factory default is enough to make a test
assert the opposite of what its name claims.** Worth checking whenever a test
turns on a column being absent.

A second test in the same file had the same shape of defect: it read
`getTable()->getQuery()`, the *unfiltered* base query, while claiming to prove
the export respects the filters. It would have passed whatever the filters did.
It now reads `getTableQueryForExport()`, which is the exact method Filament's
`CanExportRecords` calls.

### A permission nobody was checking

Nothing in the application proved that Shield **generates** the `page_logistics_*`
permission names the pages check for. Every test grants them by name, so a page
naming a permission Shield never generates would pass every test and be
impossible to open in production - `canAccess()` false for everyone, including a
role with every box ticked.

The names are not Shield's defaults. Shield would produce `view_<class>`; this
application installs its own key builder in `Webkul\PluginManager\PermissionManager`
that produces `page_<plugin>_<class>`. Two independent things have to agree, and
nothing was holding them together. A new test pins the five report pages. The
other Logistics pages are still unpinned and are noted for WP-13.

### Verified

Full `LogisticsFeature` suite on `aureuserp_testing_wp11`: **176 passed, 676
assertions**, no failures. Then the reports file alone at **9 passed, 38
assertions**, run afterwards because the last two edits to the test file landed
after the full run had already collected its tests. Pint clean on every changed
file.

One thing to watch when reproducing this: `--filter="Reports\\\\ReportsTest"`
matched nothing and **exited 0** anyway. "No tests found" is not a pass - check
the test count rather than the exit status.

## 2026-09-25 (Company Admin role, and avatars without a third party)

Two decisions the user made after the tenant-isolation fix landed
(`docs/company-admin-role-plan.md` §4b).

### Company Admin, provisioned

A role to assign when onboarding a tenant, so it no longer means hand-ticking
permissions each time. It is an ordinary role: it holds seven permissions and
gets **no** `Gate::before` bypass, unlike Multi-Company Admin.

Deliberately **not** a system role. System roles can be assigned by nobody but a
super admin, which would stop DEKA staff handing this out while onboarding - the
one job it exists for. What contains it is machinery that already applies to
every non-super actor:

- the company scoping added on 2026-09-24, so it sees only its own company's
  users;
- `scopeAssignableRoles()`, which lets an actor assign only roles whose
  permissions are a subset of their own, so it cannot create someone more
  powerful than itself;
- `scopeAssignableCompanies()`, so it cannot grant a company it does not hold
  and therefore cannot grow its own reach by creating a user.

It can view, create and update users, and archive one at a time. It has no
`delete_any`, no `force_delete`, no role or permission management, no
`page_security_*` and no scope bypasses.

**No business permissions.** A role enumerating every resource would grow
silently into a near-super-admin each time a plugin is installed, and what a
given customer's administrator should be able to *do* differs per customer.
Assign business roles alongside this one.

`CreateUser` gives the role `global` resource permission, as it already did for
Multi-Company Admin: ownership on the User model matches `creator_id` and `id`,
so anything narrower would show an administrator only themselves and the users
they personally created - never the colleagues already there.

### Avatars: no names sent to ui-avatars.com

Filament's default `UiAvatarsProvider` builds the initials image by requesting
ui-avatars.com **with the person's name in the URL**. That was one request per
page for the signed-in user; after the Users table began falling back to it, one
per row. A page of fifty would have sent fifty customer employee names to a
third party, and rendered nothing offline.

`Webkul\Support\Filament\AvatarProviders\DefaultAvatarProvider` serves one local
SVG instead, registered on **both** panels - fixing only the table would have
left the user menu still calling out. Everyone without an uploaded picture looks
the same, which was the accepted trade.

### A mistake worth recording

The seeder call was added with a shell one-liner, which silently dropped the
`use` statement because of the namespace backslashes - the trap already in
AGENTS.md, hit for the third time in one session. `CompanyAdminRoleProvisioner`
then resolved inside the seeder's own namespace and **every test in the Security
suite failed at once**, because the seeder runs during install. The body of the
change had been checked; the import had not. Write PHP with the editor, and
verify the part that is easy to miss.

### Verification

- `SecurityFeature`: **55 passed, 158 assertions** - five new Company Admin
  tests covering the permission baseline, idempotent provisioning, company
  containment, and the two escalation boundaries.
- Pint passed on every changed file.

---

## 2026-09-24 (Tenant isolation in the Users list, and four broken employee factories)

Raised by the user: *"when I create a user for a new company I onboard, I give
them a role like Admin2, but they tend to see other companies."* Investigated,
and the reason turned out to be a defect rather than a missing role. Full
write-up and the plan for the "Company Admin" role itself:
`docs/company-admin-role-plan.md`.

### The leak

`UserResource::getEloquentQuery()` routes through
`MultiCompanyAdminService::scopeManageableUsers()`, which company-scoped its
query for **Multi-Company Admins only**. Every other actor got `->ownership()`
and no company filter at all - and `ownership()` is not about companies. For a
user whose resource permission is `global`, `Bouncer::getAuthorizedUserIds()`
returns null and `OwnershipScope` returns early with no restriction whatsoever.

So a customer's administrator with a custom role listed every user in the
installation, across every tenant, with those tenants' companies named in the
"Allowed Company" column. `UserPolicy::view()` and `::update()` fell through to
`hasAccess()`, whose first clause is `hasGlobalAccess()`, so they could open and
edit those users as well.

Users are the one place this had to be written by hand: a user belongs to many
companies through `user_allowed_companies`, so they are not company-scoped rows
and `CompanyScope` never reached them.

### The fix

The non-Multi-Company-Admin branch now gets the same containment the other
branch always had - the target must share an active company with the actor and
hold no company the actor does not. It is applied **on top of** `ownership()`,
never instead of it, so this can only narrow what someone sees.

`UserPolicy` applies the same rule in `view`, `update`, `delete`, `forceDelete`
and `restore`, through one helper so it cannot be forgotten in one method, and
built from the same query as the list: a record hidden from the list is not
reachable by its URL either.

Two judgement calls, both recorded in the plan: a user who belongs to the
actor's company *and* another is hidden, because listing them would name the
other tenant on the row; and an actor can always see their own row, which
reveals nothing.

### Found while fixing it: ownership rules are untested everywhere

`OwnershipScope::apply()` returns early on `app()->runningInConsole()`, and
`artisan test` is the console. `AllowedCompanyScope` guards the same line with
`&& ! app()->runningUnitTests()`; `OwnershipScope` has no such exemption, so the
ownership scope is **inert in every test in this repository** and no test proves
that `individual` or `group` restricts anything. A test of this package first
appeared to prove ownership stayed narrow when the reason was elsewhere. Not
changed here - switching it on would alter the data every existing test sees.
Recorded for WP-13.

### Employee factories: four defects, none reachable before

`Employee::factory()->create()` could not complete anywhere in this repository,
which is why nothing had noticed:

- `DepartmentFactory` defaulted `manager_id` to `Employee::factory()`, while
  `EmployeeFactory` defaults `department_id` to `Department::factory()`. Each
  employee made a department that made an employee, until PHP's stack ran out.
- `WorkLocationFactory` set `location_type` to a random word although the model
  casts it to an enum, plus a `user_id` column that does not exist and `active`
  where the column is `is_active`.
- `DepartureReasonFactory` inserted `sequence` (the column is `sort`) and a word
  into the integer `reason_code`.
- `EmployeeJobPositionFactory` set `status` and `open_date`, neither of which
  exists in the employees schema or in the columns the recruitments plugin adds.

A new `EmployeeFeature` suite holds five guard tests naming each defect, so they
fail loudly rather than lurking. CI runs `vendor/bin/pest` with no suite filter,
so it is picked up without a workflow change.

### Sentry: POD tokens redacted before events leave

Sentry records the request URL regardless of `send_default_pii`, which governs
cookies, the client IP and the body. A stop link's token is in the URL path, so
any exception on that route sent a live credential to a third party.
`AppServiceProvider::redactStopLinkTokensFromSentry()` rewrites it, chained onto
the existing before_send rather than replacing it, and registered in
`booted()` because the Sentry client does not exist until its own provider runs.
It is not in `config/sentry.php`: a closure there would break `config:cache`.

### The broken avatars in the Users table

Not a storage problem - the symlink, APP_URL and disk config were all fine, and
no partner has ever had an avatar (`count(avatar) = 0`). `defaultImageUrl`, which
is what Filament renders when the state is empty, was pointed at
`$record->avatar_url`: the same null it was meant to replace. Filament emitted an
`<img>` with no usable src, and the browser drew a broken-image icon. It now
falls back to `Filament::getUserAvatarUrl()`, the avatar the panel already shows
for the signed-in user.

**Note for the user:** that provider is Filament's `UiAvatarsProvider`, which
builds the initials image by calling ui-avatars.com with the user's name in the
URL. Today that is one request per page for the signed-in user; with this fix it
is one per row. A local provider rendering initials as inline SVG would avoid
sending names to a third party, and is a small change if wanted.

### Verification

- `SecurityFeature`: **50 passed, 141 assertions** - 9 new isolation tests plus
  every pre-existing Security test, including the Multi-Company Admin suite.
- `EmployeeFeature`: **5 passed, 8 assertions**.
- `LogisticsFeature` after the factory changes: **161 passed, 629 assertions**.
- Pint passed on every changed file.

---

## 2026-09-24 (Logistics WP-5b: security review, and the four decisions it produced)

Branch `feature/logistics`. Review and decisions: `docs/logistics-plan.md` §7.
Test database `aureuserp_testing_wp5b`.

WP-5b's spec requires a security review before the package leaves `review`,
because it is the only public entry point in the plugin. The review was done by
the agent that wrote the package, which the user chose knowingly. It raised four
risks; the user decided each one, and the pass then found four defects.

### What the user decided

- **Attribution.** Record the driver *and* ask for the recipient's ID, both
  optional per company, both off by default.
- **Token in the URL.** Accept, but let each company choose how long a link
  lives.
- **Throttling.** Key on the token, not the IP: this is a multi-tenant
  application and drivers share carrier NAT addresses, so one company's drivers
  must not spend another's budget. Keep a per-IP layer for abuse, make the limit
  configurable per tenant, answer with 429 and Retry-After.
- **Review.** A fresh adversarial pass by the authoring agent.

### What that changed

`logistics_company_settings` gains `capture_driver_on_pod`,
`capture_recipient_id` and `stop_link_rate_limit`; `logistics_delivery_proofs`
gains `driver_id` and `recipient_id_reference`. The migration is additive rather
than folded into WP-1's, which have already run, and every option is off by
default so an existing company's proofs are unchanged.

The driver is taken from the **stop's trip**, never from the request: whoever
holds the link is not necessarily the driver, and a field they could fill in
would be a claim rather than a record. A test posts a forged `driver_id` and
asserts it is ignored. The recipient ID is required by `DeliveryService` when
the company asks for one - the page is not the boundary - and when the option is
off, a crafted post carrying the field stores nothing. The column is free text
named `recipient_id_reference`, deliberately not ID-specific: what counts as
identification differs by country, and it must not become a national-ID field by
assumption.

The throttle now applies two limits at once, per token and per IP, with the
token **hashed into the cache key** - keying on the plaintext would put a live
credential into the cache store.

### Four defects the pass found

1. **The signature temp file was never deleted.** Decoded to `tempnam()`, and
   storing copies rather than moves, so every capture with a signature left a
   file behind: unbounded growth driven by an unauthenticated endpoint.
2. **Revocation was unreachable.** `revoke()` existed but nothing called it, so
   the only way to kill a link was to issue a replacement - wrong for the case
   that matters, a URL sent to the wrong number.
3. **A shipment leaving OUT_FOR_DELIVERY gave the driver a 500.** Link issued,
   shipment then held or cancelled or delivered by someone else, driver submits:
   `InvalidShipmentTransition` is a plain RuntimeException with no status and
   the app's generic handler only answers JSON. An ordinary operational sequence
   produced a server error at a customer's door and a false Sentry alert. It now
   reports the same refusal as an expired link, which is also the right answer
   for an unauthenticated caller: whether a shipment was cancelled is not theirs
   to learn. The link is not spent, so it works again if the shipment goes back
   out.
4. **`CompanySetting` had no `$attributes`.** `forCompany()` returns an unsaved
   instance for a company with no row, so `require_pod_for_delivery` read falsy
   although the schema defaults it to true - quietly dropping a delivery control.
   Same trap as `users.is_active` and `logistics_shipments.state`.

### A migration invariant, tightened rather than relaxed

WP-1's `InstallTest` asserted that every migration contains no `Schema::table(`
and exactly one `Schema::create('logistics_*')`. The additive migration failed
it. The rule protects something real - `UninstallCommand` drops `logistics_*`
and nothing else, so a column added to another plugin's table would survive
uninstall - but it could not tell "altering a table I own", which is safe
because the column goes away with the table, from "altering someone else's",
which is the actual danger.

The test now asserts the real property: every table named in any
`Schema::create|table|rename|drop` call starts with `logistics_`, and no raw
`DB::`. That rejects things the old rule allowed, such as `Schema::rename` or
`dropIfExists` on a foreign table, while permitting the one case that is safe by
construction.

### Verification

- `StopLinkTest` is now 26 tests. Full `LogisticsFeature` on
  `aureuserp_testing_wp5b`: **161 passed, 0 failed, 629 assertions**.
- Pint passed on every changed file.

### Residual risks, accepted and recorded

The token travels in the URL path, so it reaches web-server and proxy logs and
browser history, and anyone the message is forwarded to can complete the
delivery; single use, the chosen TTL, `no-referrer` and revoke-on-reissue reduce
this but do not remove it, and the paths are worth scrubbing from log retention
at the edge. A forwarded link also exposes the shipment reference and the stop's
contact name. `rateLimitFor()` costs one indexed query per request including for
tokens that do not exist, bounded by the per-IP limit.

An independent review before release would still be worth having: three of these
four defects were things the authoring agent had looked straight past while
writing the original tests.

---

## 2026-09-24 (Logistics WP-5b: one-time POD capture links)

Branch `feature/logistics`. Handoff: `docs/logistics-plan.md` §7. Test database
`aureuserp_testing_wp5b`.

Drivers have no logins (D6), so proof of delivery is captured through a
single-stop URL the dispatcher sends by SMS or WhatsApp (D15). No messaging
provider is built in: the action shows the link to copy and share, which also
keeps the credential out of a third-party message log this application would
then be responsible for.

Only a SHA-256 hash of the token is stored. A link expires after the company's
`stop_link_ttl_hours`, works once, and can be revoked; issuing a new link for a
stop revokes any earlier unused one, so a URL already sent or leaked stops
working.

### How an unauthenticated request is authorised

The token is the credential, and `capture()` exchanges it for the identity of
the dispatcher who issued the link, through `Auth::guard('web')->onceUsingId()`,
which does not touch the session. **Nothing in `DeliveryService` is relaxed** -
its switch check, both `Gate::authorize()` calls, the company scope and the POD
validation all run exactly as they do for a user in the panel.

The alternative, a "skip the checks when there is no user" path through
`DeliveryService`, was rejected: it would leave a second and weaker way into the
same service, and the next caller to forget a flag gets it for free. Borrowing
the issuer's identity also gives the right failure modes - deactivate the
dispatcher, or take away their `capture_pod` permission, and their outstanding
links stop working.

The guard is named rather than taken from `Auth::`'s default deliberately. Which
guard is default depends on what ran earlier in the process, and the API's token
guard has no `onceUsingId()` at all; the test run produced exactly that
`BadMethodCallException` before the fix, on seven of fifteen tests.

### Refusals say nothing

Unknown, expired, used and revoked tokens all produce the same 404 carrying no
detail, because saying which one tells a caller whether a token ever existed and
turns the endpoint into an oracle for guessing. The refusal page names neither
the shipment nor the company, which is asserted.

The link is consumed only once the delivery has succeeded, inside the
transaction that locks its row, so two simultaneous submissions cannot both
spend it and a rejected photo leaves the driver able to retry rather than locked
out at a customer's gate.

### Session, CSRF and throttling

Middleware is declared in the plugin's route file, not inherited:
`PackageServiceProvider` loads plugin web routes with a bare `loadRoutesFrom()`
that applies no group at all. Without it the form would have had no session and
therefore **no CSRF protection**, and no throttling on an unauthenticated
endpoint that accepts file uploads. The limiter (20 a minute per IP) lives in
the plugin's provider so it goes away with the plugin.

The capture page is `noindex` and `no-referrer`, since the URL is a credential,
and its CSS is inline because it opens on a driver's phone and must not depend
on the admin asset pipeline.

### A gap in shared test infrastructure

`TestBootstrapHelper::loadPluginRoutes()` only ever loaded `routes/api.php`. No
plugin had web routes before, so a plugin's routes were never registered in
tests and every `route()` call threw `RouteNotFoundException`. It now loads both
files. That helper is shared by every suite, so `SupportFeature` was run as well
as `LogisticsFeature`.

### Verification

- 15 tests in `tests/Feature/StopLinks/StopLinkTest.php`: **15 passed, 45
  assertions**.
- Full `LogisticsFeature` on `aureuserp_testing_wp5b`: **140 passed, 0 failed,
  551 assertions** - the 125 from WP-10 plus these 15.
- Pint passed on every changed file.

### Not done

**The security review this package's spec requires has not been done.** WP-5b is
the only public entry point in the plugin, and what is described here is the
build to be reviewed, not the review. The residual risk to put first: the token
travels in the URL path, so it can reach web-server logs, proxy logs and browser
history. Single use, the TTL, `no-referrer` and revocation on re-issue reduce
that, but do not remove it, and moving the token out of the URL would break the
"send a link by WhatsApp" use case D15 is built on.

---

## 2026-09-24 (Logistics WP-10 dashboard: the page, and a Shield bypass I wrote)

Branch `feature/logistics`. Handoff: `docs/logistics-plan.md` §7. Test database
`aureuserp_testing_wp10`.

WP-10's three widgets and their seven tests were already on the branch. What was
missing was the first thing the package's spec names: the Logistics dashboard
page. Without it the widgets were reachable only through panel-wide widget
discovery, which drops them onto the main dashboard with no page of their own.

`Webkul\Logistics\Filament\Pages\Dashboard` follows the Projects dashboard
already in this repo - `BaseDashboard`, `HasPageShield`, a `$routePath`,
`NavigationGroup::Dashboard`, `getWidgets()` - with one addition: access needs
the page permission **and** `LogisticsAccess::enabledForCurrent()`, so a company
that has not switched Logistics on never sees it. The page does not decide which
widgets a user may see; each widget keeps its own `canView()`, so the finance
widget hides itself from anyone without `view_financials` here exactly as it does
on the main dashboard.

### A Shield bypass, written and then caught by its own test

The first version of `canAccess()` read:

```php
return parent::canAccess() && LogisticsAccess::enabledForCurrent();
```

A `canAccess()` written on the class **replaces** the one `HasPageShield`
provides, because a class method beats a trait method. `parent::canAccess()`
therefore reached Filament's `Page::canAccess()`, which returns true. The page
permission was never checked: the dashboard opened for any authenticated user of
an enabled company, whatever their role.

The test written for that case - "it forbids the Logistics dashboard without its
page permission" - failed with "Failed asserting that true is false", which is
what a test is for. The fix checks the permission explicitly, as the
`UnbilledCharges` page already does:

```php
return (auth()->user()?->can('page_logistics_dashboard') ?? false)
    && LogisticsAccess::enabledForCurrent();
```

A grep over the whole repository confirms no other page combines `HasPageShield`
with its own `canAccess()`, so this was the only instance. The trap is now in
`AGENTS.md`, because it will come up again the next time someone adds one
condition to a Shield-guarded page.

### Verification

- Four new page tests: opens for a permitted user of an enabled company,
  forbidden without `page_logistics_dashboard`, forbidden for a company that has
  not enabled Logistics, and lists its three widgets.
  `tests/Feature/Dashboard/WidgetsTest.php` now 11 tests, all passing.
- Full `LogisticsFeature` on `aureuserp_testing_wp10`: **125 passed, 0 failed,
  506 assertions, 2625 s** - the 121 of WP-8a plus these four. The run's wall
  time was much longer because Docker was frozen across the machine's sleep;
  Pest's own 2625 s is the run, and the summary line confirms it completed.
- Pint passed on every changed file.

### Note

The widgets appear on the panel's main dashboard as well, because
`LogisticsPlugin` discovers them - the same as the Projects plugin. Each widget
guards itself with the company switch and a permission, so a company without
Logistics sees nothing there.

---

## 2026-09-23 (Logistics WP-8a finished: receipt enforcement and two broken tests)

Branch `feature/logistics`. Handoff: `docs/logistics-plan.md` §7. Test database
`aureuserp_testing_wp8a`, dropped and recreated because the previous owner's run
had stalled on it.

WP-8a's owner became unavailable partway through the package, so the user asked
for it to be finished here. The resource, service and state machine that owner
built are unchanged; what follows is the verification they could not run and the
three defects it exposed.

### `requires_receipt` was enforced only by the form

`ExpenseForm` marks the receipt upload required when the selected category
demands one, but a form is not a boundary. An API write, an import or a direct
call to `ExpenseApproval` walked an unevidenced expense straight through to
`approved` - the point at which the company agrees to pay it. For an evidence
control on money, that is the wrong place to stop.

`ExpenseApproval` now refuses the move to `submitted` **and** to `approved`:

- Both points, because an expense can reach `submitted` without passing through
  `submit()`: a seeded record, an import, an API write.
- Rejection is deliberately still allowed without a receipt. A missing receipt
  is often the reason for rejecting a claim.
- The category is read `withoutGlobalScopes()` by key. Read through the
  relation, a queued job or console command running outside the expense's
  company would see no category at all and treat that as "no receipt needed" -
  the rule failing open, which is the wrong way round.
- The refusal is a `ReceiptRequired` exception, following the existing
  `NothingToInvoice` pattern, caught in `ViewExpense` and shown as a warning
  notification naming the category rather than an error page.

### Two tests that could not have passed

`ApprovalTest`'s form test granted only `create_logistics_expense`. Filament's
`Resource::canAccess()` returns `canViewAny()`, so `CreateExpense` never
mounted, and every assertion in that test failed on a null component -
"Attempt to read property `form` on null" - rather than on the form it meant to
check. Added `view_any_logistics_expense`, with a comment so it does not get
re-broken.

The same test then asserted on
`FileUpload::make('receipt_path')->getAcceptedFileTypes()` - a freshly
constructed component carrying none of the resource's configuration, which
returns null. It asserted nothing. It now checks the component the page actually
mounts, through `assertSchemaComponentExists`, covering the MIME allowlist, the
10 MB cap and the `public` disk: all three claimed in the original handoff and
none of them previously tested.

### Verification

- Four new tests: refusal on submit, refusal on approve for an expense that
  never passed through `submit()`, success once a receipt is attached, and
  rejection still going through without one.
- Full `LogisticsFeature` on `aureuserp_testing_wp8a`: **121 passed, 0 failed,
  500 assertions, 2161 s**. No failures anywhere in the plugin.
- Pint passed on every changed file.

### Requests for other packages

- **WP-8b:** the "at least one of shipment, trip or vehicle" rule is form-only,
  as `requires_receipt` was. It is a data-shape rule rather than an evidence
  control, so it was left alone here, but WP-8b cannot post a vendor bill for an
  expense that points at nothing.
- **WP-13:** `Expense` declares no `$attributes`, so it has the same
  null-in-memory gap for database-default columns that `Shipment` had.

---

## 2026-09-23 (Logistics WP-9 sales quotation link)

Branch `feature/logistics`. Handoff: `docs/logistics-plan.md` §7. Test database
`aureuserp_testing_wp9`.

A confirmed sales order carrying one of a company's six `LOG-*` service products
now creates a **draft** shipment, with the order's customer, currency and lines
copied across as charges. The charges are stored `is_billable = false`, because
Sales already invoices that order and billing them again from Logistics would
charge the customer twice for the same work.

No Sales file was edited. `OrderWorkflow::confirm()` already dispatches
`OrderConfirmed`, so Logistics only listens. `CreateShipmentFromOrder` swallows
and `report()`s any failure: the order is the customer's commitment, the
shipment is a convenience, and confirming an order must not fail because
Logistics could not make one.

### Multi-tenancy: why the listener registers unconditionally

Plugin installation is global - the `plugins` table has no company column - so
"is Sales installed" says nothing about whether a given company sells, and
"is Logistics installed" says nothing about whether it ships. Guarding
registration on `Package::isPluginInstalled('sales')` would also query the
database at register time, which is what made `package:discover` hang for 300 s
once already, and `class_exists()` decides nothing in a monorepo where every
plugin's classes are autoloadable.

So the listener is always registered and the decision lives once, in
`ShipmentFromOrder::shouldConvert()`, where it is testable:

| Tenant | Logistics enabled | Confirming a sales order |
| --- | --- | --- |
| Sales only | no | declined on the per-company switch |
| Sales + Logistics | yes | draft shipment created |
| Logistics only | yes | event never fires - that tenant places no orders |
| Sales not installed | either | its tables do not exist; the event never fires |

Logistics declares no dependency on Sales (only `products`, `employees`,
`accounts`), and `logistics_shipments.sale_order_id` is a plain indexed column
rather than a foreign key, so a logistics-only deployment installs cleanly.

Two related corrections in the same service:

- **The "no backfill" rule is enforced by when the code runs.** The rule is "no
  shipment for an order *confirmed* before the company enabled Logistics", and
  the listener only fires on confirmation, so the switch read at that moment is
  the whole rule. An earlier draft compared the order's `date_order`, which
  wrongly blocked a quotation drafted before adoption and confirmed after it -
  precisely the open work a company adopting Logistics has in hand. Orders
  already confirmed before adoption are still converted deliberately, through
  the "From sales order" picker.
- **`serviceLines()` now reads the order's lines with global scopes removed**,
  filtered by `order_id`, as `existingShipment()` already did. `OrderLine` is
  company-scoped and an event fires under whoever acted, so a queued or console
  confirmation under a different active company would have found no lines and
  silently skipped the shipment.

### Bug found while testing: null column defaults on Shipment

`logistics_shipments` fills `state`, `transport_mode`, `priority` and eight
other columns by database default, and `Shipment` declared no `$attributes`. A
freshly created shipment therefore carried `null` for all of them in memory
until it was reloaded: `ShipmentFromOrder::convert()` returned a shipment whose
`state` was null rather than `DRAFT`, and `->state->value` on it would have been
a fatal error. This is the same trap as `users.is_active`, recorded in project
memory, and the fix is the same - `protected $attributes` mirroring the
migration's defaults, with a docblock saying to keep the two in step.

The same gap exists on `Trip`, `Expense`, `Stop`, `ShipmentCharge`, `Vehicle`
and `Driver`. Not changed here: outside WP-9, and each needs its own test run.
Recommended for WP-13.

### Verification

- `tests/Feature/Sales/ShipmentFromOrderTest.php`, 10 tests: the conversion and
  its contents, the per-company switch off, switching Logistics back off,
  an order with no logistics services, another company's `LOG-*` products,
  converting only once, the duplicate check surviving a different active
  company, an old quotation confirmed after adoption, the real `OrderConfirmed`
  dispatch, and a Logistics failure not breaking order confirmation.
- Focused run on `aureuserp_testing_wp9`: **9 passed, 1 failed** - the failure
  was the null `state` above, fixed at its cause. All 10 pass after the fix.
- Full `LogisticsFeature` on a freshly recreated `aureuserp_testing_wp9`:
  **116 passed, 1 failed, 483 assertions, 2488 s**. An earlier attempt was
  interrupted when the machine's Docker daemon stopped, so the database was
  dropped and recreated before this run rather than reused.
- The one failure belongs to **WP-8a**, not WP-9:
  `tests/Feature/Expenses/ApprovalTest.php:97` fails with "Attempt to read
  property `form` on null", because the Livewire component never mounted.
  Filament's `Resource::canAccess()` returns `canViewAny()`, so `CreateExpense`
  needs `view_any_logistics_expense` as well as `create_logistics_expense`.
  Reported to that package's owner; not changed here.
  **Superseded the same day** - WP-8a's owner became unavailable and the user
  asked for it to be finished here. See the WP-8a entry above: the suite is now
  121 passed, 0 failed.
- Pint passed on all four WP-9 files.
- Note for future runs: the suite now installs the Sales plugin. The first Sales
  test pays 496 s for it, about a fifth of the suite's total, on top of the
  515 s the first test of the suite already pays for the base install.

### Requests for other packages

- **WP-2 / WP-13:** `ShipmentResource::saleOrderOptions()` lists every order of
  the customer despite its docblock saying "Confirmed sales orders", and does
  not exclude orders that already have a shipment. The listener cannot
  duplicate, since `existingShipment()` sees the `sale_order_id` the picker
  sets, but a user can hand-create a second shipment for one order.

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
