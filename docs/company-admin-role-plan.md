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
that is its own piece of work, with its own full-suite run, and it belongs with
WP-13 hardening or a dedicated task. Worth doing: until then, ownership is a
production behaviour with no automated cover at all.

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
