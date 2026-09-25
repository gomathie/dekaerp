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

0. **Awaiting the user's decision.** Nothing below is done; the triage was brought
   back first, as asked. The only change made is the guard itself.
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
