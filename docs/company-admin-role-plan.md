# Company Admin role, and the tenant leak in the Users list

Raised by the user on 2026-09-24: *"when I create a user for a new company I
onboard, I give them a role like Admin2, but they tend to see other companies.
I wanted a role like Company Admin: sees only the company assigned to him, and
performs his company functions, adding new users and so on, for his company
alone."*

Investigated 2026-09-24. **The role does not exist, and the reason they see
other companies is a defect, not a missing feature.** Documented here to be
fixed after the Logistics work, with one mitigation that can be applied today
without code.

---

## 1. What exists today

| Tier | Who it is for | How it is bounded |
| --- | --- | --- |
| `super_admin` | the owner | `Gate::before` grants everything, including `bypass_company_scope` |
| `Multi-Company Admin` | DEKA ERP staff managing several tenants | holds everything except a denylist (`MultiCompanyAdminService::DENIED_ABILITIES`), and every user/company query is filtered to the companies assigned to them |
| any other role (`Admin2`, `User`, …) | customer staff | company-scoped for **business data**, but see §2 for users |

There is no "Company Admin". The closest thing, `Multi-Company Admin`, was built
for DEKA ERP's own staff: it is deliberately allowed to hold several tenants at
once, and it is denied role and permission management so staff cannot escalate.

## 2. The finding: the Users list is not company-scoped

`UserResource::getEloquentQuery()` routes through
`MultiCompanyAdminService::scopeManageableUsers()`
(`plugins/webkul/security/src/Services/MultiCompanyAdminService.php:57`). That
method has two branches:

- **Multi-Company Admin** — filtered to users whose allowed companies sit inside
  the actor's assigned companies. Correct.
- **everyone else** — `->ownership()` plus "exclude users holding a system
  role". **No company filter at all.**

`ownership()` is not about companies. It applies `OwnershipScope`, which asks
`Bouncer::getAuthorizedUserIds()` (`plugins/webkul/security/src/Bouncer.php:35`):

```php
if ($user->resource_permission == PermissionType::GLOBAL) {
    $authorizedUserIds = null;      // -> OwnershipScope returns early, no restriction
} elseif ($user->resource_permission == PermissionType::GROUP) {
    $authorizedUserIds = $this->getCurrentAccessibleUserIds($user);
} else {
    $authorizedUserIds = [$user->id];
}
```

So for a customer admin whose **Resource Permission is `Global`**:

- the Users list shows **every user in the installation**, across every tenant;
- the "Allowed Company" column on that list names those other tenants' companies,
  which is exactly the symptom reported;
- `UserPolicy::view()` and `::update()` fall through to
  `HasScopedPermissions::hasAccess()`, whose first clause is
  `hasGlobalAccess($user)` — so they can **open and edit** those users too, not
  merely see them. The only records held back are "protected administrators".

With Resource Permission `Individual` the same user sees only themselves, which
is why some users in the screenshot behave and one does not.

**Severity: tenant isolation.** One customer's administrator can read, and
change, another customer's user records. It is not a cosmetic listing problem.

### What is *not* broken

Worth stating, because it narrows the fix:

- Business data is company-scoped normally (`CompanyScope`), and
  `bypass_company_scope` is granted only to `super_admin` by a `Gate::before`.
  It is not a Shield-generated permission, so it cannot be ticked onto a custom
  role by accident.
- The company list (`CompanyResource::getEloquentQuery()`) is filtered to the
  actor's assigned companies.
- The company selectors on the user form (`allowed_companies`,
  `default_company_id`) are filtered by
  `MultiCompanyAdminService::scopeAssignableCompanies()`, so an onboarded admin
  cannot hand a user a company they do not themselves hold.

The leak is specific to the **users** table, because users are not
company-scoped rows: a user belongs to many companies through
`user_allowed_companies`, so `CompanyScope` does not apply to them and the
company filter has to be written by hand — which was done for Multi-Company
Admins and for nobody else.

## 3. Mitigation available today, no code

*Superseded by the fix in 4a, which landed on 2026-09-24. Kept because it is
still the right answer for an installation running an older build.*

Set **Resource Permission = Individual** (or `Group`) on every customer-side
administrator. `User::ownershipSources()` is `creator_id` and `id`, so with
`Individual` the Users list collapses to the user themselves plus anyone they
created, and the cross-tenant view closes.

From the screenshot, `Nestem Consult` is currently `Global` and therefore
exposed; `Bennet Koree` and `Yves Nyundo` are `Individual` and are not.

The cost is that such an admin can then no longer see their *own* colleagues
either, which is the thing they actually need — hence the fix below.

## 4. The fix, after Logistics

Two parts, in this order. The first is the security fix and stands alone.

### 4a. Company-scope the Users list for everyone (the real fix)

Give the non-Multi-Company-Admin branch of `scopeManageableUsers()` the same
company filter the Multi-Company Admin branch already has: a user may only be
listed when their allowed companies intersect the actor's, and never when they
hold a company the actor does not. Then apply the same rule in `UserPolicy`
(`view`, `update`, `delete`) so the policy and the list agree — today the policy
leans on `hasAccess()`, which knows nothing about companies.

This is a behaviour change for existing installations: an admin with `Global`
today sees everyone and will afterwards see only their own companies' users.
That is the intent, and it should be called out in the release notes.

Tests to write, in `plugins/webkul/security/tests/Feature/Authorization/`:

- a `Global` non-super, non-MCA user does not see users of a company they do not
  hold, in the list **or** through the policy;
- they do see their own company's users;
- a user holding two companies sees users of both;
- `Individual` and `Group` still behave as they do now within the company;
- super admin and Multi-Company Admin are unchanged.

### 4b. A provisioned "Company Admin" role

Once 4a lands, "Company Admin" is no longer special machinery - it is a normal
role plus one allowed company. What is worth adding is a **provisioned role
template** so onboarding does not mean hand-ticking permissions:

- user management for their own company (`view_any/view/create/update` on
  `security_user`), their company's business resources, and nothing touching
  roles, permissions, plugins or other companies;
- denied the same escalation surface as Multi-Company Admin - it must not be
  able to grant a role it does not hold, or edit roles at all;
- provisioned by a service beside `MultiCompanyAdminRoleProvisioner`, so it is
  created on install and repaired by migration like that one was.

A Company Admin will need **Resource Permission = `Global`**, which is safe now
that 4a bounds it by company. `User::ownershipSources()` is `creator_id` and
`id`, so an `individual` permission shows them only themselves and the users they
personally created - not the colleagues who were already there. `Global` plus the
company filter is exactly "everyone in my company, nobody else", which is what
was asked for. The provisioner should set it, rather than leaving it to whoever
fills in the form.

Open question for the user before building 4b: should a Company Admin be able to
**create** users at all, or only invite them? Creating a user means choosing a
role, and that is the escalation path Multi-Company Admin was careful about.
The safe default is: they may create users and assign only roles that grant
nothing they do not themselves hold, which is what
`MultiCompanyAdminService::scopeAssignableRoles()` already implements and can be
reused.

## 4c. Found while fixing 4a: ownership rules are untested, everywhere

`OwnershipScope::apply()` begins:

```php
if (app()->runningInConsole()) {
    return;
}
```

`artisan test` is the console, so **the ownership scope is inert in every test in
this repository.** `AllowedCompanyScope` guards the same line with
`&& ! app()->runningUnitTests()`; `OwnershipScope` has no such exemption.

Consequences:

- No test anywhere proves that `individual` or `group` resource permissions
  actually restrict anything. They do in the browser; the suite cannot show it.
- A test that appears to prove an ownership rule is really proving something
  else, which is how `CompanyUserIsolationTest` first came to assert a colleague
  was hidden when the reason was elsewhere.
- Conversely, the company filter added in 4a is proven, because it does not
  depend on ownership.

Not changed here. Adding the `runningUnitTests()` exemption would switch
ownership on for every existing test at once and change the data each one sees -
that is its own piece of work, with its own full-suite run. Until then, ownership
is a production behaviour with no automated cover at all.

**Decided (user, 2026-09-25):**

- **Timing:** after WP-11, with the release hardening.
- **Fallout:** if enabling it fails tests in suites unrelated to this, triage
  each one as a genuine bug or a test that assumed it could see everything, and
  bring the list back **before** changing anything beyond the scope itself.
- **Queued jobs:** investigate and report first. A job runs "in console" and can
  have a user set, so the guard may be hiding a production question and not only
  a testing one. Note that the scope already returns early when there is no
  authenticated user, which is the normal console case - so the
  `runningInConsole()` guard may be broader than whatever it was added for.
  Do not change queue behaviour without saying what would change.

### 4d. Enabled in tests, and what it found - 2026-09-25 - Claude

The exemption was added, and nothing else:

```php
if (app()->runningInConsole() && ! app()->runningUnitTests()) {
    return;
}
```

`runningUnitTests()` is `$this['env'] === 'testing'`, so this is a **test-only**
change: a production queue worker or artisan command is still `runningInConsole()`
with `env !== testing`, and still skips the scope. No production behaviour moves.

Databases used: `aureuserp_testing_own1/2/3`, three suites at a time, one suite
per database.

#### Results

| Suite | Result |
|---|---|
| SecurityFeature | 55 passed |
| SupportFeature | 115 passed |
| EmployeeFeature | 5 passed |
| PartnerFeature | 74 passed |
| PurchaseFeature | 178 passed |
| AccountingFeature | 54 passed |
| **ProjectFeature** | **1 failed**, 76 passed |
| **AccountFeature** | **2 failed**, 519 passed |
| **SaleFeature** | **17 failed**, 126 passed |
| InventoryFeature | 884 passed |
| ManufacturingFeature | 38 passed |
| ProductFeature | 250 passed |
| LogisticsFeature | 177 passed |

**All thirteen suites run: 20 failed, 2,551 passed.** The failures are two
families and sit in three suites; the other ten are untouched. Notably
`InventoryFeature` (884 tests, the largest) and `ManufacturingFeature` pass
despite `Operation` and `Manufacturing\Models\Order` both being ownership-scoped,
so the scope being live is not inherently disruptive - what breaks is narrower
than that.

A note on the run itself, so the next agent does not misread it: `InventoryFeature`
appeared to hang for 1h43m - the container was up but Postgres showed both of its
connections `idle / ClientRead` with no query for that entire time. It was **not**
a hang. Docker had frozen (the same thing recorded against WP-10's run), and the
suite resumed on its own and kept passing. `OneStepDeliveryTest`, the file it
looked stuck on, was run separately and passed all 65 of its tests. Check
`pg_stat_activity` query age before concluding a suite is stuck, and never conclude
it from the log alone, because `--compact` output is buffered and flushes in chunks.

#### First: which models this actually governs

Not all of them. `User`, `Company`, `Team` and `Partner` override
`ownershipScopeIsGlobal()` to `false` - they carry the trait only for the explicit
`ownership()` query scope, which is why a company switcher is not ownership
filtered. The models with a **global** ownership scope are exactly eight:

| Model | Ownership sources |
|---|---|
| `Account\Models\Move` | `creator_id`, `invoice_user_id` |
| `Sale\Models\Order` | `creator_id`, `user_id` |
| `Purchase\Models\Order` | `creator_id`, `user_id` |
| `Inventory\Models\Operation` | `creator_id`, `user_id` |
| `Maintenance\Models\MaintenanceRequest` | `creator_id`, `user_id` |
| `Manufacturing\Models\Order` | `creator_id`, `assigned_user_id` |
| `Project\Models\Project` | `creator_id`, `user_id`, followers |
| `Project\Models\Task` | `creator_id`, `users` relation, followers |

And this matters more than it looks: `users.resource_permission` defaults to
**`individual`** at the column level, and `UserInvitationService` sets
`INDIVIDUAL` explicitly for everyone who is not a Multi-Company Admin. Only the
two administration roles get `GLOBAL`. So for most real users these eight models
*are* filtered to what they created or were assigned, in production, today - with
no automated cover until now.

#### Family 1 - fixtures nobody owns (17 in SaleFeature, all benign)

`OrderDeliveryTest`, `OrderInvoiceTest`, `OrderLineTest`. Every failure is the
same: **404 where 200 or 403 was expected.** The parent `Order` is created by
`createOrderWithDeliveries()` *before* the acting user exists, and `OrderFactory`
sets `creator_id` to `User::query()->value('id')` - the first user in the table,
not the actor. So route-model binding cannot find the order and returns 404.

This is correct product behaviour: an `individual` user has no business seeing a
stranger's order, and 404 rather than 403 is the better answer because it does not
confirm the record exists. These are **test-data defects, not product defects**.
The fix is per-test: create the fixture as the acting user, or set
`resource_permission => GLOBAL` as the other API tests in the same directories
already do.

One nuance worth keeping: `it('forbids listing order deliveries without
permission')` expected 403 and got 404, so ownership now masks the permission
check that test exists to prove. Whoever fixes it should keep the order owned by
the actor so the test still tests permissions.

#### Family 2 - reading an ownership-scoped parent in a model hook (3, real)

These are **not** test-data problems. They are the same defect already recorded in
project memory for `CompanyScope` ("an event listener runs under whoever acted"),
now showing up through `OwnershipScope`.

**`MoveLine::inheritFromMove()`** - `AccountFeature`, 2 failures, a hard crash:

```php
$this->move_name = $this->move->name;        // ErrorException: property "name" on null
$this->company_id = $this->move->company_id; // the tenant boundary, from the same null
```

`$this->move` is a `BelongsTo` on `Move`, which carries **both** `BelongsToCompany`
and `HasOwnershipScope`. When the actor does not own the move, the relation
resolves to null and the saving hook dies. Note this is fragile to *any* global
scope on `Move`, not just this one - `CompanyScope` simply happened not to bite in
these two tests, because both companies are allowed to the actor. The failing
tests are the two that deliberately set the active company to something other than
the invoice's company.

**`TaskStage::creating()`** - `ProjectFeature`, 1 failure, and this one fails
*silently*:

```php
$taskStage->company_id ??= $taskStage->project?->company_id;
```

`Project` is globally ownership-scoped, so `?->` swallows the miss and
`company_id` stays **null**. `CompanyScope` treats a null `company_id` as *shared*
- visible to every company. So the silent path here ends in a row that crosses
the tenant boundary, which is worse than the crash in `MoveLine`.

Neither is changed yet, per the instruction to bring the list back first. The fix
for both is the one already established by `Logistics\Services\ShipmentFromOrder`:
read the parent with `withoutGlobalScopes()` filtered explicitly by the parent
key. Both also deserve a test that pins the derived `company_id`.

Production reachability is narrow but real: it needs an actor who is `individual`,
does not own the parent, and still reaches the child write - most likely through
the API or a service that loaded the parent unscoped and then wrote children. It
is not reachable for `GLOBAL` users or super admins, because the scope returns
early for them.

#### Queued jobs - investigated, nothing to change

The question was whether the console guard hides a production problem as well as a
testing one. It does not, and the reason is worth writing down.

- **Filament's queued exports are safe.** `CanExportRecords` serializes the query
  with `EloquentSerializeFacade::serialize()`, and `eloquent-serialize`'s `pack()`
  calls `$builder->applyScopes()` **before** serializing, then strips the global
  scopes so they are not applied twice. The company and ownership constraints are
  therefore evaluated in the web request, where both scopes are live, and frozen
  into the payload as ordinary where clauses. `PrepareCsvExport` rebuilds that
  query in the worker, where the scopes would be inert - but they have already
  been applied. This matters because WP-11 added exports to Logistics, and because
  `CompanyScope` carries the same console guard: a naive re-query in the worker
  would have crossed tenants.
- **The application owns no queued jobs that read a scoped model.** The only
  `ShouldQueue` class in application code is
  `Chatter\Notifications\ChatterDatabaseNotification`, which carries five scalars
  and runs no query. Every `::dispatch()` call in the plugins is an *event*, not a
  job, and no listener is queued - so they run inside the acting request, where
  the scopes are live. There are no importers at all.

So the console guard is currently only a testing question. It is worth re-checking
the moment a real queued job is added that reads one of the eight models above:
such a job would see every company's and every user's rows.

#### Recommendation

0. ~~Awaiting the user's decision.~~ **Superseded: all five approved and done on
   2026-09-25. See §4e below for what was changed and what the audit in step 5
   turned up.**
1. Keep the exemption. It costs nothing in production and it is the only way any
   ownership rule can ever be tested.
2. Fix the two hook defects (`MoveLine::inheritFromMove()`,
   `TaskStage::creating()`) with `withoutGlobalScopes()` plus a test each. These
   are the only genuine bugs found.
3. Fix the 17 SaleFeature fixtures by owning them, not by disabling the scope.
4. Then add the tests this whole exercise was for: that `individual` and `group`
   actually restrict, on at least one of the eight models.
5. Audit the remaining `->relation?->column` reads inside `creating`/`saving`
   hooks on the eight scoped models; these two were found by tests, and the
   pattern is likely not limited to them.

### 4e. Fixed - 2026-09-25 - Claude

All five steps above carried out (user approved, 2026-09-25). The audit in step 5
found **two more instances of the same defect**, which are fixed here too.

#### The defect, stated once

A model hook derives a child's `company_id` by reading its parent **through the
relation**. The parent carries a global scope, so for an actor who is not entitled
to see that parent the relation resolves to `null`. Two outcomes, both bad:

- `??=` or `?->` swallows it and `company_id` stays **null** - and `CompanyScope`
  reads a null company as *shared*, so the row becomes visible to **every
  company** on the installation. Silent.
- or a `?? current_company_id()` fallback files the row under the **actor's**
  company instead of the parent's. Also silent, and also wrong.
- or nothing swallows it and the save dies on `->name`. Loud, and the least
  harmful of the three.

Which company a row belongs to is a fact about its parent document. It is not a
question about what the actor may look at - that is the policy's job, and it has
already run by the time a saving hook fires.

#### Fixed

| Where | Was | Now |
|---|---|---|
| `Project\Models\TaskStage::creating()` | `$taskStage->project?->company_id` - null company, shared row | `parentProjectCompanyId()`, reads `Project::withoutGlobalScopes()` |
| `Account\Models\MoveLine` saving chain | `$this->move->name` - fatal, and `company_id` from the same null | `loadParentMoveWithoutScopes()` resolves the move onto the relation once, before the chain runs |
| `Project\Models\Task::creating()` | `Project::withoutGlobalScope(CompanyScope::class)` - dropped **only** the company scope, so ownership still hid the project and the task fell back to the actor's company | `withoutGlobalScopes()` |
| `Inventory\Models\Move` creating + saving | `$move->operation?->company_id` - `Operation` is ownership-scoped; null company, shared row | `loadParentOperationWithoutScopes()` |
| `Account\Models\Move` - **all five** line relations (`lines`, `invoiceLines`, `taxLines`, `paymentTermLines`, `roundingLines`) | lines handed out of these relations re-queried their own parent, so `MoveCalculator::productBaseLine()` died on `$line->move->isInvoice(true)` when posting | `->chaperone()` on each |

The `chaperone()` one is worth dwelling on, because it took **three** runs to
land and each run moved the same crash somewhere else:

1. Fixing the model hook (`loadParentMoveWithoutScopes()`) made the new
   regression test pass but left the suite at 2 failures. The hook only helps a
   line that is *being saved*; the crash reappeared in `MoveCalculator`, a
   **service**, reading `$line->move` on lines it had been handed.
2. `->chaperone()` on `lines()` fixed the `roundedBaseAndTaxLines()` path. Still
   2 failures - the stack frame had simply moved from `MoveCalculator:160` to
   `:119`, because `recompute()` reads `invoiceLines`, a *different* relation.
3. `->chaperone()` on all five.

Two lessons, and the second is the one worth keeping:

- **A scoped-parent read is not one bug in one hook.** It recurs anywhere a child
  is handed around without its parent; the model layer is just where it surfaces
  first.
- **When a fix leaves the failure count unchanged, read the stack frame, not the
  count.** Runs 2 and 3 both reported "2 failed, 520 passed" - identical numbers -
  while the defect had actually moved. Taking the count at face value would have
  read as "the fix did nothing".

`chaperone()` is right here rather than defensive null handling: the lines came
out of that move, so the inverse is a fact. It also removes one query per line.

`Task` is the instructive one: whoever wrote it knew a parent read had to escape
`CompanyScope` and said so explicitly - they simply did not know `Project` carries
a *second* global scope. `withoutGlobalScope(CompanyScope::class)` reads as
careful and is not. Prefer `withoutGlobalScopes()` for a parent read in a hook
unless there is a reason to keep one.

`MoveLine` and `Inventory\Models\Move` are resolved onto the relation rather than
read field by field, because their hook chains read the parent many times - about
forty in `MoveLine`, seven in `Move`'s `applyDefaults()`. Fixing only the
`company_id` line would have left the rest fragile. The relation **definition** is
deliberately untouched in both: it is what Filament and every read path use, and
widening it would show a parent to anyone who can see one of its children.

#### Not fixed, deliberately

- `Inventory\Models\ProductQuantity::saving()` reads `$stock->location?->company_id`
  and `Inventory\Models\MoveLine::creating()` reads `$line->move?->company_id`.
  Neither parent (`Location`, `Inventory\Models\Move`) carries `OwnershipScope`, so
  only `CompanyScope` applies and the existing behaviour is unchanged by this work.
  They have the same shape and are worth revisiting, but changing them here would
  be scope creep with no failing test behind it.
- `time-off\LeaveAllocation` and `products\ProductSupplier` use
  `withoutGlobalScope(CompanyScope::class)` on `Employee` and `Product`; neither
  target is ownership-scoped, so they are correct as written.

#### Tests added

- `projects/tests/Feature/Workflows/OwnershipScopeTest.php` - **the coverage this
  whole exercise existed for.** Four tests on `Project`: an `individual` user does
  not see a stranger's project; does see one they were *assigned* (`user_id`, so
  "individual" is not "only what I created"); a `global` user sees both; and
  `global` still does not cross the **company** boundary, because confusing
  ownership with tenancy is how a tenant boundary gets dropped.
- `projects/.../CompanyIsolationTest.php` - a task stage derived from a project the
  actor does not own. It asserts the actor genuinely cannot see that project
  first, so the test cannot pass for the wrong reason if ownership is ever
  switched off again.
- `accounts/.../DocumentCompanyResolutionTest.php` - a move line on an invoice the
  actor does not own, asserting `company_id`, `move_name` and `journal_id` are all
  inherited. Same "prove the actor cannot see it" guard.
- The 17 `SaleFeature` fixtures now give their actor `global`, matching the
  `actingAsSalesOrderApiUser()` helper that `OrderTest` in the same directory
  already used. Their 404s were correct behaviour; those tests are about the
  delivery, invoice and line sub-resources, not about ownership.

#### Verification

| Suite | Before the fixes | After |
|---|---|---|
| ProjectFeature | 1 failed, 76 passed | **82 passed** (5 new tests) |
| AccountFeature | 2 failed, 519 passed | **522 passed** (1 new test) |
| SaleFeature | 17 failed, 126 passed | **143 passed** |
| InventoryFeature | 884 passed | **884 passed** (re-run for the `Move` change) |
| AccountingFeature | 54 passed | **54 passed** |

**Nothing outstanding: 0 failed across all five.** ProjectFeature and
InventoryFeature were re-run after their models changed, because the first run of
each had started before the edit landed and a mid-run edit is not reliably picked
up. Databases `aureuserp_testing_own1/2/3`.

The other eight suites are unaffected by these fixes and were already green with
the scope live (§4d): Security 55, Support 115, Employee 5, Partner 74,
Purchase 178, Manufacturing 38, Product 250, Logistics 177.

#### Still open

- The positive ownership coverage exists for `Project`, `Task` and `Move` only. The other
  five globally scoped models have none. ~~`group` (team-based) ownership is still
  untested entirely.~~ **Closed by §4f, which found a privilege escalation in that
  branch while covering it.**
- The two `CompanyScope`-only sibling reads noted above
  (`ProductQuantity::saving()`, `Inventory\Models\MoveLine::creating()`).
- Whether `individual` is the right default for `users.resource_permission` at
  all is a product question, not a code one. **Now asked, and open: see §4g for the
  impact analysis and the queries that decide it.** It is the column default *and*
  what `UserInvitationService` sets, so every ordinary user gets it; the eight
  models above are filtered for them in production today.

### 4f. A group user with no team saw everything - 2026-09-26 - Claude

Found while closing the coverage gap §4e left open (`group` was untested
entirely). This is a **silent privilege escalation**, not a coverage problem.

#### The hole

Two pieces of code, each reasonable alone:

```php
// Bouncer::getCurrentAccessibleUserIds() - every user sharing a team with this one
->whereIn('teams.id', $user->teams()->pluck('id'))   // no teams -> matches nothing -> []

// OwnershipScope::apply()
if (empty($userIds)) {
    return;                                          // empty -> no restriction at all
}
```

A user with `resource_permission = group` and **no team** therefore resolved to an
empty authorized-id list, which `OwnershipScope` read as "do not filter". They saw
every row of all eight globally scoped models across their companies - exactly as
if they were `global`. No error, no log, nothing on screen to indicate it.

`CompanyScope` still applied, so this was not cross-tenant. It was a full
within-tenant escalation: every colleague's invoices, orders, projects and tasks.

#### Reachability

Not reachable by creating a user through the panel: `UserResource` makes the teams
field `->required(fn (Get $get) => $get('resource_permission') == PermissionType::GROUP)`.

Reachable afterwards, and this is the realistic path:

- the user is removed from their team;
- or the team is deleted;
- or `resource_permission` is set to `group` by anything that does not go through
  that form - the API, a seeder, a direct update.

In each case the user's view silently **widens**. That is the wrong direction for
a permission to fail in.

#### Fixed

`Bouncer::getAuthorizedUserIds()`, group branch:

```php
$authorizedUserIds = $this->getCurrentAccessibleUserIds($user);

if (empty($authorizedUserIds)) {
    $authorizedUserIds = [$user->id];
}
```

Fails closed to the user's own rows, which is what `group` degrades to when the
group is empty. Deliberately fixed in `Bouncer` rather than by changing
`OwnershipScope`'s `empty()` guard: that guard is also what makes the scope skip
cheaply, and repurposing "empty" to mean "show nothing" would change behaviour for
any future caller that legitimately returns an empty list.

**Behaviour change to be aware of:** any existing `group` user who currently has no
team will see less after this ships than before - specifically, only their own
records. That is the intended correction, but it will look like a regression to
anyone who had grown used to the wider view. Worth checking before deploy:

```sql
select u.id, u.email
from users u
left join user_team ut on ut.user_id = u.id
where u.resource_permission = 'group' and ut.user_id is null;
```

If that returns rows, those users need a team assigned (or a different resource
permission) rather than the old behaviour back.

#### Tests added

Three more in `projects/tests/Feature/Workflows/OwnershipScopeTest.php`:

- a `group` user sees a teammate's project and not a stranger's - the ordinary
  case, and the first test of the `group` branch anywhere in the application;
- a `group` user with **no team** sees only their own - the hole above, pinned;
- an `individual` user sees a task assigned to them through `Task`'s
  `OwnerSource::relation('users')` pivot. That exercises
  `OwnershipScope::applyRelation()`, which nothing had reached before, and is the
  case that matters most for not *over*-restricting: an assignee who cannot open
  their own work would be a support ticket a day.

#### A broken factory found on the way

The task test above would not run: `TaskFactory` declared
`'visibility' => 'public'`, and `projects_tasks` has **no such column** - it never
did in this fork. Any `Task::factory()->create()` threw
`SQLSTATE[42703] Undefined column: visibility`.

It had been noticed and worked around rather than fixed. `TaskTest` carried
`unset($payload['visibility'])` in `taskPayload()` and an `afterMaking()` hook
doing the same in `createTaskRecord()` - two workarounds keeping a broken factory
usable for one test file, and leaving it unusable for every other. The field is
removed from the factory and both workarounds with it.

This is the third factory defect found this way (after the five in `employees`,
and `ShipmentFactory`'s `expected_delivery_at` in WP-11). **A factory that no
test outside one file uses is not known to work.** Worth a pass over the factories
of any model that has no direct factory test.

#### Verification

| Suite | Result |
|---|---|
| ProjectFeature | **86 passed** (4 new tests; was 82) |
| SecurityFeature | **55 passed** - the `Bouncer` change breaks nothing |

Databases `aureuserp_testing_own1` and `own2`. Pint and `php -l` clean.

No existing test anywhere created a `group` user, which is why this survived: the
whole branch had no coverage, so there was nothing to fail. That is also why the
blast radius of the `Bouncer` change is small - the only behaviour that moves is
the case that was broken.

#### Followers, and why that branch was unreachable in tests

`OwnerSource::followers()` (`applyFollowers()`) is now covered too: an
`individual` user sees a project they follow and not one they do not.

It needed a detour worth recording. `applyFollowers()` matches on the follower's
**partner**, not the user:

```php
$partnerIds = User::whereIn('id', $userIds)->pluck('partner_id')->filter()->all();

if (empty($partnerIds)) {
    return;                 // no partner -> following never counts as ownership
}
```

In production every user has a `partner_id`, because `User::saved()` creates a
Partner for them. But `SecurityHelper::createUser()` builds users inside
`User::withoutEvents()`, and these suites call `SecurityHelper::disableUserEvents()`
in `beforeEach`, so that hook never fires and every test user has
`partner_id = null`. The branch was therefore unreachable by construction - it
would have returned early in any test that tried. The new test attaches a Partner
explicitly.

Two things follow from that. First, **disabling model events in a helper silently
disables the behaviour those events provide**, and a test can then only prove
things about a model that production never produces. Second, anything else keyed
on `users.partner_id` is equally untested for the same reason - worth a look if a
partner-keyed rule ever misbehaves.

#### Dead code, reported not removed

`OwnerSource::pivot()` and `OwnershipScope::applyPivot()` are **unreachable**: a
repository-wide search finds the factory method, the `KIND_PIVOT` constant and the
`match` arm, and **no caller anywhere**. `Task::users()` - a `belongsToMany` over
`projects_task_users` - is exactly the shape `pivot()` was written for, but `Task`
declares `OwnerSource::relation('users')` instead and gets the same result through
`whereHas`. So `pivot` is a redundant second way to say the same thing.

Left in place deliberately: removing it is a cleanup with no functional benefit,
and `OwnerSource` is a support class a future plugin could reasonably use. Writing
a test for it would be covering a path no model reaches. **Recommendation: delete
it in a cleanup pass, or use it in place of `relation('users')` on `Task` - but
pick one, rather than leaving two mechanisms for one job.**

### 4g. RESOLVED: is `individual` the right default? - 2026-09-26

**Status: decided 2026-09-28 - see section 4h.** The user asked to see the impact
before choosing (2026-09-26), and then chose company-wide visibility. This section
is kept as the analysis that informed it; the "what the code does today" below
describes the state *before* 4h, not now.

#### What the code does today

`users.resource_permission` is `individual` by default - both the column default in
`2024_11_26_053234_add_resource_permission_column_to_users_table.php` and what
`UserInvitationService` writes for everyone who is not a Multi-Company Admin. Only
two paths set anything wider: `CreateUser` sets `global` when the new user gets the
Multi-Company Admin or Company Admin role, and the same for MCA on invitation.

So, by default, a user sees only rows where they are the creator or the assignee on
these eight tables:

| Table | Visible to an `individual` user when |
|---|---|
| `accounts_account_moves` | `creator_id` or `invoice_user_id` is them |
| `sales_orders` | `creator_id` or `user_id` is them |
| `purchases_orders` | `creator_id` or `user_id` is them |
| `inventories_operations` | `creator_id` or `user_id` is them |
| `maintenance_requests` | `creator_id` or `user_id` is them |
| `manufacturing_orders` | `creator_id` or `assigned_user_id` is them |
| `projects_projects` | `creator_id`, `user_id`, or they follow it |
| `projects_tasks` | `creator_id`, assigned via `projects_task_users`, or they follow it |

Everything else in the application is unaffected - products, partners, companies,
settings, all the configuration resources. This is not a general read restriction;
it is these eight document types.

**Who escapes it entirely:**

- `resource_permission = global` - `Bouncer` returns `null` and the scope skips;
- **super admins** - `SecurityServiceProvider` has a `Gate::before` granting
  `bypass_ownership_scope` to the super-admin role;
- Multi-Company Admins, but only because `CreateUser` gives them `global`. They are
  on `MultiCompanyAdminService::DENIED_ABILITIES` for `bypass_ownership_scope`
  itself, so the permission route is closed to them.

`CompanyScope` is a separate and stricter boundary that applies regardless. Nothing
in this question touches tenant isolation.

#### The three options, and what each would change

**Keep `individual`.** Staff see their own work; a manager needs `group` (with a
team) or `global`. Costs nothing to leave. The risk is the one that motivated this
whole thread: it is a *silent* restriction. A user who cannot find last month's
invoice has no indication why, and neither does whoever they ask. If this is the
intent, the product should say so on screen somewhere.

**Default to `global`.** Everyone sees their company's documents; `CompanyScope`
remains the boundary. This is the behaviour most small-business users expect, and
it removes a class of confusing support tickets. It is a **widening** change, so it
needs saying plainly: every existing `individual` user would gain visibility of
every colleague's invoices, bills, orders and stock moves in their companies.
Whether that is acceptable is a per-customer policy question, which is why the
third option exists.

**Per-company setting.** Correct in principle for a multi-tenant product, and the
plugin already has the pattern for it (`logistics_company_settings`, per
`docs/logistics-plan.md`). It is real work: a setting, a resolution point in
`Bouncer`, and a decision about what happens to users whose explicit permission
disagrees with their company's default. Worth scoping separately rather than
bolting on.

#### Now demonstrated, not inferred

`accounts/tests/Feature/Workflows/OwnershipScopeTest.php` was added on 2026-09-26
and pins the behaviour on the money documents, which had no ownership coverage at
all. Three tests, all passing:

- **an `individual` user cannot see a colleague's invoice** - same company, so it
  is ownership doing it and not `CompanyScope`;
- an `individual` user *can* see an invoice raised by someone else and assigned to
  them via `invoice_user_id`, `Move`'s second ownership source, which nothing had
  exercised;
- a `global` user sees both.

So the first bullet is the production behaviour as of today, for every user created
without an administration role. It is no longer a reading of the code.

Full `AccountFeature` on `aureuserp_testing_own2`: **525 passed, 0 failed** (522
before, plus these three).

The tests set `resource_permission` explicitly, so they stay valid whichever way
this question is decided - they describe what each setting *does*, not what the
default ought to be.

#### Queries to answer it

Read-only. Run against production (Supabase SQL editor). No credentials are in this
repository and none were used to write these.

```sql
-- 1. The headline: how is resource_permission actually distributed?
select coalesce(resource_permission, '(null)') as permission,
       count(*) as users,
       count(*) filter (where is_active) as active
from users
group by 1
order by 2 desc;

-- 2. Which tenants this affects, and how unevenly.
select c.id, c.name,
       count(*) filter (where u.resource_permission = 'individual') as individual,
       count(*) filter (where u.resource_permission = 'group')      as "group",
       count(*) filter (where u.resource_permission = 'global')     as global
from users u
join companies c on c.id = u.default_company_id
where u.is_active
group by c.id, c.name
order by individual desc;

-- 3. The population already exempt, so not affected by any option.
select r.name as role, count(distinct mhr.model_id) as users
from roles r
join model_has_roles mhr on mhr.role_id = r.id
where lower(r.name) in ('super_admin', 'multi company admin', 'company admin')
group by r.name;

-- 4. Group users with no team. These see LESS once the 4f fix ships
--    (own rows only, instead of everything). Assign a team or change
--    their permission - do not restore the old behaviour.
select u.id, u.email
from users u
left join user_team ut on ut.user_id = u.id
where u.resource_permission = 'group' and ut.user_id is null;

-- 5. What is actually hidden: the share of documents an average individual
--    user cannot see. Run per table; invoices are the one people notice.
select count(*)                                            as total,
       count(distinct creator_id)                          as distinct_creators,
       round(100.0 * count(*) / greatest(count(distinct creator_id), 1), 1)
         as rows_per_creator
from accounts_account_moves
where deleted_at is null;
```

Query 5 is the one that makes it concrete. If `distinct_creators` is 1 or 2 -
everything entered by one bookkeeper - then `individual` is harmless today and will
bite the moment a second person is hired. If it is spread across many users, the
restriction is already active and people are already living with it.

Substitute the other seven table names into query 5 as needed:
`sales_orders`, `purchases_orders`, `inventories_operations`,
`maintenance_requests`, `manufacturing_orders`, `projects_projects`,
`projects_tasks` (the last two also count followers, so their visible share is
wider than the query suggests).

#### Recommendation

Run 1, 2 and 5 first; they are enough to decide. My expectation, stated so it can be
checked rather than trusted: most DEKA ERP tenants are small enough that documents
cluster on one or two creators, which means `individual` is currently invisible and
will surface as "why can't my new colleague see our invoices?" as tenants grow. If
that is what the numbers show, **default to `global` and let customers who want
tighter visibility opt into `group` or `individual` per user** - the narrow setting
is then a deliberate choice rather than an accident of a column default.


### 4h. DECIDED: company-wide visibility is the default - 2026-09-28 - Claude

**User decision (2026-09-28):** "all users in the same company should be able to see
each others invoices and details in the company."

Clarified by the user in the same exchange: *"if its currently individual, and can
be given as rights then its also good"* - so the narrow setting should **remain
available to grant**, it just must not be what everyone gets by accident.

That settles §4g. `CompanyScope` is the boundary. `OwnershipScope` is not removed or
disabled; it becomes an opt-in narrowing instead of the silent default.

**What stays grantable.** Nothing about the feature is taken away:

- `PermissionType` keeps all three cases - `global`, `group`, `individual`;
- the Users page still offers all three (`->options(PermissionType::class)`), so an
  administrator can set any user to `individual` or `group` at any time;
- `EditUser` can change it on an existing user, with the existing guard that stops
  someone editing their own;
- every rule those settings drive still works and is covered by tests
  (`accounts/.../OwnershipScopeTest.php`, `projects/.../OwnershipScopeTest.php`).

The only things that changed are the **default** for a new user and the **existing
`individual` rows**. "Individual" is now a deliberate choice someone makes, which is
what it should always have been.

#### What the old value actually came from

Worth recording, because it was not one setting in one place, and the panel was
never the problem:

| Path | Before | Now |
|---|---|---|
| Users page form (`UserResource`) | already `->default(PermissionType::GLOBAL->value)` | unchanged |
| `CreateUser` | forces `global` for the two administration roles | unchanged |
| `UserInvitationService::accept()` | hard-coded `INDIVIDUAL` for everyone who was not a Multi-Company Admin | `GLOBAL` for everyone |
| `users.resource_permission` column default | `individual` | `global` |
| Existing `individual` rows | - | updated to `global` by migration |

So a user created through the Users page was already company-wide. The ones that
were not were **invited** users - which is the onboarding path - and anything
created without the field, which includes seeders and factories. That is why the
complaint was about onboarded users specifically.

#### Changed

- `database/migrations/2026_09_28_090000_default_users_to_company_wide_visibility.php`
  - column default `individual` -> `global`, and existing `individual` rows updated.
- `Webkul\Security\Services\UserInvitationService::accept()` - writes `GLOBAL`. The
  Multi-Company Admin lookup that used to choose between the two values is gone
  along with its now-unused `Role` import, since the role no longer changes the
  outcome here.
- Docblocks in `Account\Models\Move`, `Account\Models\MoveLine` and
  `Inventory\Models\Move` said "defaults to `individual`" as the reason their
  scoped-parent reads mattered. Corrected: the default is `global`, but
  `individual` and `group` users still exist, so those fixes are still load-bearing
  and must not be reverted on the strength of the new default.

#### `group` rows are deliberately left alone

The migration updates `individual` only. Nobody ever *chose* `individual` - it was a
column default and a hard-coded service value - whereas `group` has to be selected
in the form (which defaults to `global`) **and** given a team, so it is a real
decision by whoever configured that user. Widening it would silently override them.

If any exist, they keep a narrower view than their colleagues. Find them with:

```sql
select u.id, u.email, u.resource_permission
from users u
where u.resource_permission <> 'global';
```

Anything that comes back is now a deliberate exception rather than an accident.
Decide per user whether it should stay.

#### The `down()` migration restores the default but not the rows

Stated explicitly because it is asymmetric on purpose. Once everyone is `global`
there is no way to tell which users were `individual` because a customer wanted
them narrow and which were `individual` only because of the old default. Reverting
every row would be a guess, and a guess in the direction of hiding records from
people who can currently see them. The rollback therefore restores the column
default only; anyone who needs a narrower view is set back per user.

#### Before deploying

1. This **widens** visibility. Every current `individual` user will see their
   colleagues' invoices, bills, sales and purchase orders, stock operations,
   manufacturing orders, projects and tasks within their own companies. Tenant
   isolation is untouched - `CompanyScope` is a separate scope and no part of this
   changes it.
2. Run the query above afterwards and confirm the remaining non-`global` users are
   intended.
3. Nothing needs re-provisioning. Roles, permissions and company assignments are
   unaffected.

#### A migration trap, recorded

The first version of the migration used the project's usual convention -
`$table->enum('resource_permission', [...])->default('global')->change()` - and it
**cannot work on PostgreSQL**. Laravel renders an enum change as a single statement
containing an inline check constraint:

```sql
alter table "users" alter column "resource_permission" type varchar(255)
  check ("resource_permission" in ('group','individual','global')), ...
```

Postgres rejects it with `syntax error at or near "check"`. The failure mode is
what makes it worth writing down: **every test in the suite fails with 0
assertions**, because the migration dies during `migrate:fresh` and no test ever
runs. That reads like catastrophic breakage rather than one bad DDL statement - 55
failures in Security, 47 and counting in Account, all from one line.

The column's type and constraint were never changing here, only its default, so the
fix is the narrow statement:

```php
DB::statement("ALTER TABLE users ALTER COLUMN resource_permission SET DEFAULT '...'");
```

Valid on Postgres and on MySQL 8. Both test databases were dropped and recreated
before re-running, because an interrupted `migrate:fresh` leaves a half-built
schema.

#### Tests

`accounts/tests/Feature/Workflows/OwnershipScopeTest.php` gains
**"it lets a colleague see the company invoices by default"**, which creates a user
with no explicit `resource_permission`, asserts it resolves to `global`, and asserts
that user can see an invoice a colleague raised. Under the old default that test
fails - it is precisely the reported complaint, written down.

The three tests already in that file set the permission explicitly, so they keep
describing what `individual`, `invoice_user_id` and `global` each do. Same for the
six in `projects`. Ownership is still fully covered as a *feature*; it is just no
longer the default.

Both halves of the requirement are therefore pinned by passing tests side by side:

- **"it lets a colleague see the company invoices by default"** - the new behaviour;
- **"it hides a colleagues invoice from an individual user"** - `individual` still
  works when granted.

#### Verification

| Suite | Result |
|---|---|
| AccountFeature | **526 passed, 0 failed** (523 + 3; includes the new default test) |
| SecurityFeature | **55 passed, 0 failed** - the invitation change breaks nothing |

Databases `aureuserp_testing_own1` and `own2`, both dropped and recreated after the
bad first migration. Pint and `php -l` clean.

### 4i. The parent-company rule promoted out of Logistics - 2026-09-29 - Claude

**User direction (2026-09-29):** use this codebase's own rule rather than a new
helper.

The rule already existed, written for Logistics: *child rows take their parent's
company, never the session's* - `InheritsParentCompany`, recorded in project
memory. It is the right rule for the two inventories defects found in §4f/§4h
follow-up. But it could not simply be reused, and the reason is worth recording.

#### Why it had to move first

`Webkul\Logistics\Models\Concerns\InheritsParentCompany` lives in an **optional**
plugin. Logistics is installable and uninstallable; `inventories` is core. Having a
core model `use` a Logistics trait inverts the dependency and breaks the moment
somebody uninstalls Logistics.

So "reuse the rule" meant promoting it to the package both sides already depend on,
next to `BelongsToCompany`:

| Moved to | From |
|---|---|
| `Webkul\Support\Traits\InheritsParentCompany` | `Webkul\Logistics\Models\Concerns\InheritsParentCompany` |
| `Webkul\Support\Exceptions\CompanyMismatchException` | `Webkul\Logistics\Exceptions\CompanyMismatchException` |
| `support::exceptions.company-mismatch` (en, ar, es, fr, pt_BR) | `logistics::exceptions.company-mismatch` |

Nothing on the Logistics side was rewritten mid-review:

- the old trait is now a one-line alias that `use`s the Support one. Laravel's
  `bootTraits()` walks `class_uses_recursive()` and de-duplicates by method name,
  so `bootInheritsParentCompany` still registers exactly once for the six Logistics
  models that use it. They were left untouched.
- the old exception now **extends** the Support one, overriding `between()` only to
  keep the Logistics wording and its four translations, which `Expense` still
  raises from its own check. One hierarchy instead of two unrelated classes with
  the same name.
- `RecordIntegrityTest` asserted on the Logistics class. `ShipmentLine` goes through
  the trait and now raises the Support type, while `Expense` raises the Logistics
  subclass, so the assertion moved up to the parent class, which covers both.

#### One correction made while promoting it

The trait read the parent with `withoutGlobalScope(CompanyScope::class)` - only the
company scope. That is exactly the trap `Project\Models\Task` fell into (§4e): several
plausible parents also carry a global `OwnershipScope` - accounts `Move`,
inventories `Operation`, manufacturing and sales `Order`, `Project` - and dropping
one scope leaves the parent invisible to anyone who does not own it, at which point
`company_id` comes from the actor instead of the parent.

Now `withoutGlobalScopes()`. This is a no-op for the existing Logistics callers,
whose parents (`Shipment`, `Trip`) are not ownership-scoped, so promoting it changes
nothing there - but it stops the shared rule carrying a known trap into whatever
uses it next.

#### Applied to the two inventories defects

**`Inventory\Models\ProductQuantity`** - stock rows. Was
`company_id = $stock->location?->company_id ?? $stock->company_id`. `Location` is
company-scoped, and `autoAssignsCompany()` returns **false** here, so a location
outside the session's active companies left `company_id` **null** - and
`CompanyScope` reads null as *shared*, making that stock row visible to every
company on the installation. The bespoke `?? $stock->company_id` fallback could not
save it because there was nothing to fall back to.

**`Inventory\Models\MoveLine`** - two separate problems in one hook:

- `company_id ??= $line->move?->company_id` was **dead code whenever a company was
  active**. `boot()` calls `parent::boot()` first, so `BelongsToCompany`'s creating
  hook had already filled `company_id` from `CompanyContext`, and `??=` then did
  nothing. Lines took the *session's* company, so a line could sit under a move
  belonging to a different company. The trait fixes this properly by hooking
  "saving", which fires before "creating".
- `state ??= $line->move?->state` is **not** covered by the trait and nothing else
  backfills it. The same scoped read left new lines with **no state at all**, and
  `inventories_move_lines.state` is `nullable()`, so they saved silently. Fixed with
  `parentMoveState()`, a scope-free read, for the same reason the trait reads the
  parent scope-free.

#### `Builder::value()` applies the model's casts

Mine, caught by the suite. `parentMoveState()` was typed `?string`, on the
assumption that `value('state')` returns the raw column:

```
TypeError: MoveLine::parentMoveState(): Return value must be of type ?string,
Webkul\Inventory\Enums\MoveState returned
```

`Builder::value()` resolves through `first()` and therefore through the model, so
the `state` cast is applied and a `MoveState` enum comes back. Typed `?MoveState`
now.

Worth knowing because the failure was **loud but mislabelled**: 13 tests failing
across deliveries, receipts and dropships, all named after *action visibility*
("hides the validate and cancel actions on a done receipt"), which reads like a
state-machine regression rather than a return type. Running one of them alone gave
the TypeError in one line. **When a change produces many failures with a common
theme, run a single one for the exception before theorising about the theme** - the
theme was a red herring, the cause was one type hint.

The other helpers written in this workstream are unaffected: they read `company_id`,
which has no cast.

#### Verification

`LogisticsFeature`: **177 passed, 0 failed** - the trait move and the alias are
transparent to the six Logistics models, and the two `RecordIntegrityTest`
mismatch tests pass against the parent exception class, confirming the hierarchy
works for both the trait's throw and `Expense`'s own.

`InventoryFeature`: **884 passed, 0 failed** - the guard for the two changed models,
clean through the region where the type error had produced 13 failures. Plus a
focused run of the 20 action tests that had failed: all 20 pass.

Databases `aureuserp_testing_own1` and `own2`, `own2` dropped and recreated after
the interrupted run. Pint and `php -l` clean on all eight changed files.

Notably the trait now **throws** on a genuine parent/child company mismatch where
the old inventories code silently overwrote, and 884 + 177 tests show no legitimate
path reaching it. That is the point of the change: a child that disagrees with its
parent about which tenant it belongs to is a bug to surface, not to paper over.

#### Was left open here, closed in section 4j

**Resolved 2026-09-29: the saving hook was confirmed by test and fixed; the
`creating` hook suspicion was disproved. See section 4j.** The reasoning below is
what it looked like before the test existed.

`manufacturing\Models\Move::saving()` is the same family but the **destructive**
variant - it uses `=`, not `??=`:

```php
$move->warehouse_id    = $move->operationType?->warehouse_id;
$move->mo_operation_id = $move->bomLine?->operation_id;
```

`manufacturing\Models\Move extends Inventory\Models\Move`, so `operationType`
resolves to the company-scoped `Inventory\Models\OperationType`. Because the
assignment is unconditional, an unreadable parent does not merely fail to populate -
it **overwrites an existing value with null on every save**. Its `creating` hook also
reads manufacturing `Order`, one of the eight ownership-scoped models, and mis-branches
(`if (! $mo || ...)`) when that is hidden.

Verified by reading; reachability not yet proven, since it needs a cross-company save.
Not changed here - it needs its own test first, and guessing is what checking
`MoveLine` saved me from.

### 4j. The destructive variant, tested and fixed - 2026-09-29 - Claude

§4i left `Manufacturing\Models\Move` alone on the grounds that it needed a test
first. That test now exists, and it settled the question in both directions - one
suspicion confirmed, one disproved.

#### Confirmed: the saving hook wiped `warehouse_id`

```php
$move->warehouse_id    = $move->operationType?->warehouse_id;   // = , not ??=
$move->mo_operation_id = $move->bomLine?->operation_id;
```

`OperationType` carries `CompanyScope`, so a save made while a different company was
active resolved it to null - and because the assignment is unconditional it did not
merely fail to populate the field, it **overwrote the stored value**. Eloquent fires
`saving` on every `save()`, dirty or not, so a plain re-save was enough.

The test named it exactly:

```
it keeps the warehouse when the operation type is outside the active companies
Failed asserting that null is identical to 22.
```

`warehouse_id` 22 -> null, from nothing but `$move->save()` under another company.
This is the only member of this family found in the whole audit that **destroys**
data rather than failing to populate it; every other case used `??=` and could only
leave a field unset.

Fixed with the same scope-free parent read, and with `?? $move->warehouse_id` so the
stored value survives when the parent genuinely cannot be found:

```php
$move->warehouse_id    = $move->parentOperationTypeWarehouseId() ?? $move->warehouse_id;
$move->mo_operation_id = $move->parentBomLineOperationId() ?? $move->mo_operation_id;
```

The helpers return null only when there is no `operation_type_id` / `bom_line_id` at
all, so the caller can tell "no parent" from "parent not visible from here" and keep
what it has in the second case.

#### Disproved: the creating hook's mis-branch

§4i also suspected the `creating` hook, which reads
`$move->rawMaterialOrder ?? $move->order` - manufacturing `Order` carries
`OwnershipScope` - and returns early when that is null, skipping the name, origin,
procurement group, locations, schedule and deadline it derives.

**Not reachable on this path, and the test says so.** `it derives a component move
from an order the actor does not own` passes: it asserts the actor genuinely cannot
see the order (so the early return really is taken) and yet the move still comes out
named, because `Order::getMoveRawValues()` supplies those values to
`Move::create()` independently of the hook. The hook's derivation is redundant on
this route rather than load-bearing.

Left alone deliberately. Guessing is what this test was written to avoid, and the
guess would have been wrong: "fixing" the creating hook would have changed working
code on the strength of a misreading. The early return remains a latent oddity - a
route that reached `Move::create()` *without* pre-computed values would hit it - but
nothing demonstrates such a route today, and there is now a test standing where the
question was.

#### Verification

`ManufacturingFeature`: **41 passed, 0 failed** (38 before, plus these three), on a
freshly recreated `aureuserp_testing_own1`. The previously failing test passes.

`InventoryFeature` was deliberately **not** re-run for this change. The hook lives in
`Manufacturing\Models\Move::boot()`, and Eloquent registers model events per class,
so inventories `Move` instances are untouched; no inventories file changed either.
Its 884 from §4i stands.

Pint and `php -l` clean.

#### The family, closed

Every instance found by the audit is now either fixed or explained:

| Where | Shape | Outcome |
|---|---|---|
| `Project\Models\TaskStage` | `?->` -> null company -> shared row | fixed (§4e) |
| `Project\Models\Task` | dropped only `CompanyScope` -> actor's company | fixed (§4e) |
| `Account\Models\MoveLine` + `Move::lines()` etc. | null parent -> crash | fixed (§4e) |
| `Inventory\Models\MoveLine` | session's company; null `state` | fixed (§4i) |
| `Inventory\Models\ProductQuantity` | null company -> shared row | fixed (§4i) |
| `Manufacturing\Models\Move` saving | **overwrote** stored values with null | fixed here |
| `Manufacturing\Models\Move` creating | suspected mis-branch | **disproved**, test added |
| `time-off\LeaveAllocation`, `products\ProductSupplier` | `withoutGlobalScope(CompanyScope::class)` | correct as written - neither parent is ownership-scoped |

The rule they all now follow lives in one place,
`Webkul\Support\Traits\InheritsParentCompany`, so the next child model gets it for
free rather than re-deriving it.

### 4k. PLANNED: contacts, products, categories and tags per company - 2026-10-29 - Claude

**Status: plan only. No code or data has changed.** It waits on the read-only
production report below; Supabase access had not been granted at the time of
writing.

#### Why

The user decided this on 2026-10-29, ahead of onboarding a client from a
different sector. Today these records are visible to **every** company:

- **Contacts:** `Partner::autoAssignsCompany()` returns false, so a new
  contact is saved with no company. Upstream's
  `2026_07_30_100000_null_company_on_non_user_partners` also removed the
  company from every existing contact not tied to a user. That includes
  each company's own contact record (`companies.partner_id`).
- **Products:** `Product::autoAssignsCompany()` returns false, and the
  form's company field is optional with no default.
- **Contact tags, product categories, product tags:** these tables
  (`partners_tags`, `products_categories`, `products_tags`) have **no
  `company_id` column at all**.
- **The scope:** `CompanyScope` always adds `OR company_id IS NULL`, so a
  record with no company is shared by design.

Unrelated clients would see each other's customers, vendors and catalogue.

#### Decisions taken (user, 2026-10-29)

| Question | Answer |
|---|---|
| Scope contacts and products per company | Yes |
| Categories and tags too | Yes: contact tags, product categories and product tags |
| A record **already used by two or more companies** | **Copy per company.** Each company gets its own copy and its documents are re-pointed to it. Runs only after the user has reviewed the production report |
| Attributes, units, industries, titles, banks | Not decided; they stay shared setup data unless the user asks |

This is a deliberate **fork from upstream** (see the partners note in AGENTS.md
and commit `57e234223`). Every upstream merge that touches `Partner`,
`AddressResource` or `ManageContacts` has to be re-patched.

#### What changes in code

1. **Stamping.**
   - `Partner` and `Product` drop their `autoAssignsCompany(): false`
     override, so `BelongsToCompany` stamps the creating company.
   - Contact tags, product categories and product tags get a nullable
     `company_id`, `BelongsToCompany`, and an index.
   - Because the scope lives on the base models, it reaches every subclass
     at once: about 10 contact models (accounts `Customer`/`Vendor`,
     `contacts\Address`, the sales, purchases, invoices, accounting and
     website `Partner`s), 8 product models and 6 category models.
2. **Child records take their parent's company** through
   `InheritsParentCompany`, not the session's company: contact addresses
   (`parent_id`), product variants (`parent_id`), sub-categories
   (`parent_id`) and product packagings (`product_id`). `Packaging` is also
   declared shared today, in products' invariants test.

   *The existing guard (found 2026-10-29):* each plugin has a
   `CompanyScopingInvariantsTest` with a `$shared` list. Partners lists
   `Partner`; products lists `Packaging` and `Product`. Removing a model from
   that list automatically asserts it is stamped with the active company, and
   any model that newly uses `BelongsToCompany` is picked up without listing
   it. Phase C moves those three off their lists.
3. **Records that must stay readable across companies.** These are read
   scope-free, because a scoped relation read returns null silently (the
   §4e family):
   - each company's own contact record, which the company list shows
     (`partner.avatar` and the address);
   - the contact record behind each user (chatter, messages);
   - `companies.partner_id` reads in the switcher and the guides.

   Each read site is listed and tested before the scope is switched on.
4. **Upstream re-patches.**
   - `AddressResource` and `ManageContacts` get back the company handling
     upstream removed.
   - `CompanyIsolationTest`, which pins contacts as shared, is **inverted**
     in the same change, with a docblock explaining why.
5. **Guests and portal customers are unaffected:** `CompanyScope` does
   nothing without a staff user. A staff member browsing the website
   storefront sees their own company's products, which must be confirmed
   in the browser.

#### Data: backfill, then copy where needed

A read-only report decides everything below. Run it before any migration:
`php artisan partners:company-report` for contacts, plus a matching report to
be written for products, categories and tags.

1. **Used by exactly one company:** set `company_id` to that company.
2. **Used by two or more companies:** create one copy per company, re-point
   that company's documents to its copy, and keep the original for the
   first company. This is the one step that rewrites historical document
   references. It runs in a transaction, logs every re-pointed row, and
   ships with a rollback that restores the original references.
3. **Used by no company:** assign it to the installation's default company.
   Rows left without a company would stay visible everywhere.
4. Each company's own contact record gets its own company.

#### Phases, each with its own test run

| Phase | Content | Gate |
|---|---|---|
| A | Production report (read-only) | User reviews the numbers |
| B | Schema: `company_id` on the three tag and category tables | Migration tested up and down |
| C | Stamping, parent inheritance, scope-free read sites, upstream re-patches, inverted `CompanyIsolationTest` | Full suites green |
| D | Backfill and copy migration, a dry-run command first | Dry-run output reviewed by the user; Supabase backup taken |
| E | Production check with a throwaway company before the real client | Checklist in the SaaS plan's production section |

#### Risks

- A scoped relation read returning null is silent. Every
  `->partner` / `->product` / `->category` read in listeners, jobs and
  cross-plugin services must be reviewed (AGENTS.md: "An event listener
  runs under whoever acted").
- Copying a contact splits its history: chatter and activities stay on the
  original.
- Product variants, price lists and supplier rows must be copied together
  with their product, or the copy is incomplete.

#### Phase A: production report - 2026-10-29

Read-only `SELECT`s run through the Supabase connector. Nothing was written.
Only counts and record ids were read, no names or contact details.

**The installation:** 5 companies and 4 users. The latest migration is
`2026_10_22_000001`, so none of the SAAS migrations has run.

**Contacts:** 19 live.

- Each company's own contact record (5) and each user's contact record (4)
  already carries the right company.
- The **10 with no company** are all employee work contacts
  (`sub_type = employee`), created on 2026-08-25 by user 1. They cover
  employees 1 to 10.
- **None is used by more than one company**, so no copies are needed.
  Contact 5 is used by company 2 (a sales order and an invoice), contact 14
  by company 1 (a logistics shipment), and the other eight by no document.

**Employees: a gap the plan did not list.** All 10 employees in
`employees_employees` have `company_id` **null**, and also no user,
department or job position, so they are visible to every company. They look
like seeded or test data, but that has to be confirmed. Per-company
employees were not part of the request.

**Products:** 7 live.

- Six belong to company 1: the `LOG-*` service products the Logistics
  provisioner created.
- Product 1 has no company. It is used only by company 2 and was created by
  company 2's administrator, so it simply becomes company 2's. No copies are
  needed.

**Product categories:** 10, one tree, apparently the seeded default.

- The root (1) is used by company 1's six products.
- Its child (2) is used by company 2's product.
- The other eight are unused.
- Because a child cannot sit under another company's parent, company 2 needs
  its own copy of the 1 > 2 branch, with product 1 re-pointed to it.
- New companies need a default category provisioned too: the product form
  defaults to `Category::first()`, which scoping would otherwise empty.

**Tags:** contact tags 0, product tags 0. Nothing to backfill.

**Two other findings that affect deploying:**

- `user_allowed_companies` has **no duplicate pairs**, so
  `2026_10_28_000001` (the unique membership index) will run.
- User 2 holds the client role `Admin2` in **two companies** (default:
  company 2). Under the 2026-10-29 switcher rule, user 2 is fixed to company
  2. Users 1 (`Admin`, 4 companies), 3 and 4 are unaffected.

**What this means for the backfill:** the "copy per company" machinery is
needed only for one category branch. Everything else is a one-line
assignment. Phase D can therefore be a reviewed data migration with explicit
ids for this installation, not a general copy engine. The general case
returns only if a future installation has shared records.

#### Decisions on the report (user, 2026-10-29)

**Employees 1 to 10 are example data and stay shared** (`company_id` null),
together with their work contacts 5 to 14. They are deliberately excluded
from the backfill. This is the one intended exception to "nothing stays
shared".

**Existing contacts are not reassigned (user, 2026-10-29, general rule):**

- A contact that has **no company today stays shared** across companies.
- A contact that **already belongs to a company stays there.**
- Only contacts created from now on are stamped with their creator's company
  (phase C).

So the backfill assigns **no** contact. In this installation the only
contacts with no company are the employee work contacts 5 to 14, so the
rule and the decision above agree. It also covers any other installation:
the backfill never moves a contact between companies, and never takes a
contact away from the companies that can see it today.

**New employees are per company. Already true, no code change needed:**

- `Employee` uses `BelongsToCompany` with no opt-out, so a record created in
  the UI is stamped with the current company.
- The trait throws `MissingCompanyContextException` instead of saving a null
  company while a staff user is acting.
- The shared ten were created with nobody acting (a seeder or console
  command), which the trait allows on purpose.

**Remaining gap, for phase C.** Stamping happens only on *create*. Editing a
record and clearing an optional "Company" field saves it as shared again.
The company field on the employee, product and contact forms becomes
required. That is still to verify: it must not break the form for a user
who sees only one company.

**User 2:** remove the extra membership. User 2 holds companies 2 (default)
and 3, so company 3 goes. This is done through the user form, not SQL, so it
is audited and caches refresh.

**New companies get no seat limit by default.** Recorded in the SaaS plan
(RF-1 superseded).

**Revised backfill, phase D, explicit ids for this installation:**

| Record | Action |
|---|---|
| Contacts 5 to 14 (employee work contacts) | Stay shared (decision above) |
| Product 1 | `company_id = 2` |
| Categories 1 and 2 (the branch company 2 uses) | Copy for company 2, re-point product 1 to the copy of 2 |
| Categories 1 to 10 (the originals) | `company_id = 1`. Company 1 created them and uses the root |
| Tags | Nothing to do |

#### Still open

- Whether attributes and units should also become per company.

#### Phase B/D handoff - 2026-10-29 (GitHub Copilot / phase B+D package)

**Scope of this package:** phase B (a `company_id` on product categories, product
tags and contact tags) and phase D (the reviewed backfill command). Phase C
(contacts, products, packagings, employees) is a separate package and is
already committed (`12161e743`).

**Status: code complete, verification in progress.** Pint and a syntax lint have
run; the three suites have not all finished at the time of writing, so this
block carries no pass/assertion counts yet. It is updated below as each run
finishes - do not read a missing count as a pass.

##### Why

§4k decided that categories and tags belong to a company. The three tables had
**no `company_id` column at all**, so every company saw every category and
every tag, and `CompanyScope`'s "null means shared" made that unavoidable
without new columns.

Two failures were found while tracing, both of which would have shipped
silently:

1. `Category::creating` did `Auth::user()->id`. With nobody acting (a company
   provisioner, a seeder, a console command) that reads a property off null,
   which Laravel turns into an exception - and the products provisioner creates
   a category from `Company::created`, exactly that situation.
2. `products_tags.name` and `partners_tags.name` carried a **global** unique
   index. Once tags are per company, the second client cannot create a tag named
   like the first client's: the database refuses, and the partners tag API has
   no validation rule for it, so it surfaced as a 500.

##### How

- **Schema.** `products` `2026_10_29_120000_...` adds a nullable indexed
  `company_id` (FK `companies`, `nullOnDelete`) to `products_categories` and
  `products_tags`, and replaces `unique(name)` with `unique(company_id, name)`.
  `partners` `2026_10_29_120001_...` does the same for `partners_tags`. Both are
  named in their provider's `hasMigrations()` list. Nullable is deliberate: the
  existing rows are filed by the reviewed backfill, not by a migration that also
  runs on test and fresh databases. `down()` restores `unique(name)`.
- **Models.** `Product\Models\Category` gains `BelongsToCompany` +
  `InheritsParentCompany` (`parent_id`), `company_id` in `$fillable`, a
  `company()` relation, and an `Auth::id()` creator (not `Auth::user()->id`).
  `Product\Models\Tag` and `Partner\Models\Tag` gain `BelongsToCompany`,
  `company_id` and `company()`. All six `Category` subclasses (accounting,
  accounts, inventories, invoices, purchases, sales) and `contacts\Models\Tag`
  needed no change - checked, they only add relations and `mergeFillable`.
- The three parent lookups in `Category` (`validateNoRecursion`,
  `handleProductCategoryData`, `getCompleteName`) now read the parent with
  `withoutGlobalScopes()`. With the scope in place a parent from another company
  returns null, and the "no parent found" branch then **clears `parent_id` and
  resets `parent_path`** - the category silently moves to the root and its
  `full_name` loses its ancestors.
- **Default category for a new company.** `Product\Services\CompanyCategoryProvisioner`
  (one root category named by `DEFAULT_CATEGORY_NAME`, idempotent, plus
  `defaultCategoryId()`), driven by `Product\Observers\CompanyObserver::created`
  and registered in `ProductServiceProvider::packageBooted()` next to
  `UOM::observe()` - the way inventories contributes its virtual locations. Not
  in `Support\Models\Company`: `support` is the base plugin and must not depend
  on products.
- `ProductCategorySeeder` stamps its ten seeded categories with the
  installation's first company. The install-time company row is inserted with
  raw SQL, so `Company::created` never fires for it: without this, a fresh
  installation keeps a **shared** taxonomy and every later company sees the
  first client's sectors, which is the leak this package exists to close.
- **Forms.** No company field was added to any category or tag resource - they
  have none, and stamping covers create, so an edit cannot clear it. Only the
  `name` uniqueness rules moved from global to per company
  (`CategoryResource`, partners `TagResource`, via `modifyRuleUsing`).
- **Backfill.** `products:company-backfill {--dry-run}`,
  `Product\Console\Commands\ProductsCompanyBackfill`, registered with
  `hasCommand()`. It refuses unless the database matches the state §4k was
  written from (ids 1-10 with their parent links, `company_id` null on all of
  them and on product 1, product 1 pointing at category 2, companies 1 and 2
  present, both tag tables empty), printing every difference. Then one
  transaction: categories 1-10 -> company 1; copies of 1 and 2 for company 2
  (created through the model so `parent_path`/`full_name` are derived, not
  restated); product 1 -> company 2 and re-pointed at the copy of 2. It prints
  the applied changes and a rollback summary, and a second run reports "Nothing
  to do".
- **Factories.** `CategoryFactory`, `Product\...\TagFactory` and
  `Partner\...\TagFactory` use `HasCompanyDefault` (as `ProductFactory` and
  `PackagingFactory` do), so `->company()` is not dropped by `$fillable`
  filtering and a factory row created with nobody acting is not shared.
- **Tests.** `products/tests/Feature/Workflows/CategoryCompanyScopingTest.php`,
  `.../Workflows/TagCompanyScopingTest.php`,
  `.../Feature/Schema/CompanyIdOnCategoriesAndTagsMigrationTest.php`,
  `.../Feature/Console/ProductsCompanyBackfillTest.php`, and
  `partners/tests/Feature/Workflows/TagCompanyScopingTest.php`.

##### Benefit

A second client can no longer see, edit or pick the first client's product
categories, product tags or contact tags; two clients may each have a tag called
"Fragile" or a root category called "All"; a sub-category can no longer be filed
under another company's parent (refused, not silently re-parented); every new
company starts with its own root category, so the required category select on the
product form is never empty; and the production data change is a reviewed,
idempotent, rollback-documented step instead of a migration.

##### Trade-off / risk

- Categories and tags now carry a `company_id` that `CompanyScope` honours: any
  cross-company `->category` read in a listener, job or cross-plugin service
  returns **null** rather than raising. Consumers found and **left alone** (not
  this package's files, reported to the user):
  - `Logistics\Services\CompanyProvisioner::rootCategoryId()` reads
    `Category::query()->whereNull('parent_id')` with no company filter. Once
    categories are scoped this either finds nothing or returns *another
    company's* root, and the `LOG-*` service products are filed under it. It
    should call `CompanyCategoryProvisioner::defaultCategoryId($company->id)`.
  - `Inventory\Models\Location` (`categoryAncestry()`) uses `Category::find()`.
- Three **global** tag-name uniqueness rules survive outside this package and
  will refuse a legitimate per-company duplicate from those screens:
  `ProductResource/Schemas/ProductForm.php` (`unique('products_tags')`, a
  DO-NOT-TOUCH file), `PartnerResource/Schemas/PartnerForm.php`
  (`unique('partners_tags')`) and `products/Http/Requests/TagRequest.php`
  (`unique:products_tags,name`). The database now allows the duplicate, so these
  are validation refusals, not 500s.
- `ProductResource/.../ProductForm.php` does not list `category_id` as a
  company-dependent field, so changing a product's company keeps the previous
  company's category (DO-NOT-TOUCH file).
- `down()` restores `unique(name)`, which fails if two companies already hold the
  same tag name. Correct for a rollback of a just-deployed change; not a
  general-purpose downgrade path.
- A row with a null `company_id` is still *shared* by design. After this
  package the seeded tree and the ten production categories are stamped, but
  companies 3-5 (this installation) still have no category of their own - the
  backfill's table does not include them, so their product form select starts
  empty until someone creates one.
- `Company::observe()` is registered in `packageBooted()`, which can run more
  than once per process (AGENTS.md); the provisioner is idempotent, so a double
  registration costs one extra lookup, not a second category.
- Structure snapshots: **no resource gained or lost a field**, so
  `products.json` and `partners.json` are unchanged and were not regenerated.

##### Verification

- Pint (`vendor/bin/pint --format agent`, 21 changed files): fixed
  line endings, brace position on the two anonymous migration classes, and
  `! $x` spacing; re-run clean.
- `php -l` on all 21 changed files: no syntax errors.
- Test database: `aureuserp_testing_saas_4k_b` (this package's own).
- Suites ProductFeature / PartnerFeature / InventoryFeature: **in progress at the
  time of writing**; counts added below when each finishes. Do not treat the
  absence of a count here as a pass.
- 2026-10-30 focused Partners rerun on `aureuserp_testing_saas_4k_b`:
  `TagCompanyScopingTest.php` **2 passed (4 assertions)**, 74.09 seconds.
  Partners is core and has no `partners:install` command; this test now uses
  the same `beforeEach` as its sibling Partners workflow tests, with core
  installation supplied by `Tests\TestCase`.
- The backfill was **not** run against production, and its dry-run output below
  is from a local database seeded to the expected state by the test, not from
  Supabase.

##### Rollback

- Code: revert the commit; the new columns are additive and nullable, so the
  previous code runs unchanged.
- Schema: `migrate:rollback` runs both `down()` methods (tested).
- Data, if the backfill has been applied:
  `delete from products_categories where id in (<copy1>, <copy2>);`
  `update products_products set category_id = 2, company_id = null where id = 1;`
  `update products_categories set company_id = null where id in (1,...,10);`
  The command prints these with the real ids.

##### Proposed protected decisions (for the owner of AGENTS.md to place)

- Categories, product tags and contact tags belong to the company that creates
  them. A sub-category takes its **parent's** company and a mismatch is refused
  (`CompanyMismatchException`); it is never silently re-parented.
- A tag name is unique **per company**, not globally. Do not restore
  `unique(name)`.
- Every company gets one root category (`CompanyCategoryProvisioner`), created
  from the products plugin's `Company::created` observer. The product form's
  `Category::first()` default has no other fallback.
- The products seeders stamp their categories with the installation's first
  company; a fresh installation must not leave a shared taxonomy.

#### Read-audit handoff - 2026-10-29 (Codex)

I audited reads of `Partner`, `Product`, `Packaging`, `Category` and both
`Tag` models under `plugins/webkul/*/src`. Ordinary Filament, Livewire and API
queries are **SAFE** when they use the active company-scoped model query; the
`api.company` middleware and `CompanyScope` are the boundary. The rows below
are the cross-context or potentially cross-context reads that needed a more
specific classification.

| Classification | Location / flow | Finding and disposition |
|---|---|---|
| SAFE | Products `CompanyCategoryProvisioner`, `ProductsCompanyBackfill`, product `CompanyObserver` | Explicit company/id reads with `withoutGlobalScopes()`; provisioning and the named installation backfill do not depend on the actor's company. |
| SAFE | Logistics `StopLinkService` public token flow | Reads are deliberately scope-free and keyed by the signed/public stop-link token; there is no staff company session to apply. All parent queries now use `withoutGlobalScopes()`. |
| SAFE | Website `PortalAccess`, Partners `ContactImporter` | Portal email uniqueness and importer duplicate/parent resolution are deliberately global identity checks; both explicitly bypass tenant scopes and document why. Imported partners remain shared by decision. |
| SAFE | Barcode Livewire selectors; Products, Inventory, Accounts, Sales, Purchases and Projects API controllers; Filament resources and ordinary model relations | These are current-company UI/API reads. Their scoped query builders and relation loading preserve the active-company boundary; no cross-company bypass was added. |
| FIXED | Logistics `CompanyProvisioner::rootCategoryId` | The previous unfiltered first root could place `LOG-*` products under another company's category. It now provisions/returns the requested company's root through `CompanyCategoryProvisioner`; covered by the Logistics adoption isolation test. |
| FIXED | Inventory `Location::categoryAncestry` | Putaway can inspect a product loaded under another company context. Category ancestry now uses explicit scope-free id lookups; covered by `CrossCompanyCategoryReadTest`. |
| FIXED | Products `ProductSupplier` creating hook | A supplier row inherits the product's company, not the actor's. The product lookup is now scope-free; covered by `CrossCompanyParentReadTest`. |
| FIXED | Sales `Order::handleOrderCreation` | Shipping/invoice defaults follow the explicitly selected partner even when the actor is in another company; covered by `CrossCompanyParentReadTest`. |
| FIXED | Sales `SendSMSNotificationListener` | Payment workers have no reliable active company context. Order, invoice and customer reads now follow the paid move/order key with all scopes removed; listener test coverage is pending the full Sales-feature run. |
| FIXED | Chatter `HasChatter` and `ChatterNotificationService` | Responsible-user contacts and follower email recipients may belong to the record's company rather than the actor's. Explicit partner reads/eager loads now survive that context; covered by `CrossCompanyReadTest`. |
| FIXED | Products `UOMObserver` | Deleting a shared UOM must find products in every company. The observer now checks products without tenant scopes; covered by the cross-company UOM deletion test. |
| DECIDE | Support `Company` parent/contact mapping | `Company::booted` still assigns the branch company's `parent_id` to a `Partner` parent field. This is an existing cross-domain relationship decision and was intentionally not edited. |
| DECIDE | Accounts `MoveWorkflow::sendByEmail`, Sales `OrderMailer`, Purchases `OrderWorkflow` and the matching send-email recipient selectors | Explicit recipient ids can name a partner outside the current company. Decide whether cross-company outbound email is allowed; if it is, make the bypass explicit and audit it, and if not, reject ids outside the active company. No change was made in this package. |
| DECIDE | Products `ProductResource` product-company/category edit behavior | The resource's category default and company change behavior need an owner decision about whether a company change should re-home or refuse an existing category. This DO-NOT-TOUCH file was not edited. |
| DECIDE | Products `Product::saved` variant propagation | The model currently removes only `CompanyScope` while propagating a template's company to variants. Because this is a DO-NOT-TOUCH model and the variants also participate in ownership rules, replacing it with `withoutGlobalScopes()` needs the owning agent's review. |

**Audit totals:** 5 SAFE groups, 7 FIXED groups, and 4 DECIDE groups. The
SAFE groups include the current-context reads across the full plugin source
tree; the FIXED/DECIDE rows are the places where a normal scoped read would be
wrong, ambiguous or security-sensitive across company boundaries.

**Protected decisions to carry forward:** cross-context event/model-hook reads
must use `withoutGlobalScopes()` plus an explicit foreign key; a public stop
link is keyed by its token rather than a staff session; logistics service
products always use the requested company's provisioned root; and the
backfill provisions one idempotent root for every existing active company that
has none. The selected-recipient email behavior remains a product decision.

## 5. Notes for whoever picks this up

- `assignedCompanyIds()` is simply the actor's `user_allowed_companies` rows and
  already works for any user, not only Multi-Company Admins. 4a can reuse it
  as-is.
- Do not solve this by giving the new role `bypass_company_scope`. That grants
  every company, which is the opposite of the requirement.
- Watch the super-admin `Gate::before`: it returns `true` before policies run,
  so any rule written only in a policy is invisible to super admins - fine here,
  but it means tests must use a non-super actor to prove anything.
- `users.is_active` and the `$attributes` trap: a `User` created in memory
  carries the column default only because the model declares it. Keep that in
  mind when writing fixtures for these tests.
