# Agent review, 2026-11-11

Review, verification, and the WP-14 specification. No application code was changed.

## The five most important findings

1. **High. The local admin login page is a 500.** Opening `http://localhost/admin/login` throws `MissingSettings`: `UserSettings` is missing `company_admin_business_role_id`. `SecurityPlugin::register()` reads that settings object on every panel request (`plugins/webkul/security/src/SecurityPlugin.php:46`). The settings migration is registered (`SecurityServiceProvider` setting file `2026_11_11_000001_add_company_admin_business_role_to_user_settings`). The running local database `dekaerp` has not applied it. Any environment that serves this code before that migration does the same. Suggested fix: run migrations, including that settings migration, before traffic. Do not serve the new `UserSettings` property first. TESTED in the browser. The shared `dekaerp` database was not migrated from this review.

2. **Medium. A company administrator's readable password survives losing the role.** `CompanyAdminPasswordVault::capture()` stores the encrypted password while `isCompanyAdmin()` is true, and `forget()` runs only from that same method (`plugins/webkul/security/src/Services/CompanyAdminPasswordVault.php:54`). `User::saved` calls `capture()` only when a password was just set (`plugins/webkul/security/src/Models/User.php:357`). Removing the Company Admin role does not touch the vault. `reveal()` then returns the password to any active Super Admin who types their own password (`CompanyAdminPasswordVault.php:84`). It does not check that the target is still a Company Admin. The comment on `capture()` says a stale copy never outlives the role. That is true only on the next password save. Suggested fix: forget the row when the Company Admin role is detached, and refuse `reveal()` unless the target is still a Company Admin. REVIEWED BUT NOT EXECUTED.

3. **Medium. Deleting `support.json` makes the next structure test record a new baseline instead of comparing one.** `ResourceStructureSnapshotTest` writes a missing fixture and returns (`plugins/webkul/support/tests/Feature/Structure/ResourceStructureSnapshotTest.php:71`). `support` is in the dataset (line 96). The next run that reaches this test, including a run that did not install the same plugins, becomes the new baseline. The project rule is to regenerate that fixture only from the full suite that will compare it. Suggested fix: put the fixture back from the commit that removed it, unless that deletion was itself the result of a full SupportFeature run that was meant to re-record. REVIEWED BUT NOT EXECUTED. The fixture was not restored.

4. **High for accounting, not yet a confirmed local failure. Upstream now refuses an unbalanced journal entry. This tree does not.** Since the last recorded upstream SHA `36744b597` (2026-10-19), commit `99fd7c7e8` (2026-10-23) adds that check in `MoveWorkflow`. Local `MoveWorkflow::assertPostable()` checks partner, bank, negative total, date, state, lines, deprecated accounts, journal, and currency (`plugins/webkul/accounts/src/Services/MoveWorkflow.php:239`). It does not compare debit and credit. Suggested fix: port that one check, with a test that pins the refusal, as its own change. Do not merge the surrounding upstream history. REVIEWED BUT NOT EXECUTED.

5. **Medium. Phone search, once focused, can crowd the top bar again.** At `max-width: 767px`, `.fi-global-search-field:focus-within` grows to `11rem` (`resources/views/vendor/filament-panels/livewire/topbar.blade.php:783`). On a 390px screen that sits beside the menu button, a 5.5rem company name, and the bell and avatar. Suggested fix: keep the focused field inside the space that remains after those items, or overlay it. REVIEWED from the CSS. Not retested in the browser, because the login page 500'd before a session could start.

## 1. Code review

Range: `develop` commits `5160cc983` through `ab6379c47` (the last 12 of `git log --oneline -15 develop`). Read from the tree. No fixes applied.

Reviewed and not reported as defects:

- Company routes. Support `CompanyResource` sets `$isDiscovered = false`. Security's subclass sets `$isDiscovered = true`. Filament skips a resource whose `isDiscovered()` is false, so Security's create and edit pages own `/admin/companies`. REVIEWED.
- `CompanyAdministratorInviter::administratorRoleIds()` returns Company Admin plus the configured business role only when that role is eligible: not a system role, not Super Admin, not Multi-Company Admin, not Company Admin itself, and none of `User::PLATFORM_ONLY_ABILITIES`. The Users settings select uses the same filter. REVIEWED. No bypass found.
- Business-admin backfill `2026_11_11_000001_make_existing_business_admins_company_admins`. Only a company with no Company Admin, and exactly one qualifying Admin2 member, is updated. Ambiguous companies are logged. `down()` is empty on purpose. REVIEWED.
- `CompanyMembers::scope()` limits users to members of `CompanyContext::activeIds()` (`plugins/webkul/support/src/Filament/CompanyMembers.php:20`). An empty active list is fail-closed. It does not filter `is_active`. Low. A picker can still offer a deactivated member. Suggested fix: add `where('is_active', true)` if every caller wants that. REVIEWED.
- Discount-code constraint migration `2026_11_11_000001_require_company_for_company_list_discount_codes`. A deferred constraint trigger, so the code row can be inserted before its company rows. PostgreSQL only. No tenant leak seen in `DiscountCodeService::redeem()`, which locks the company and checks the company list. REVIEWED. Not changed.

## 2. Translations

Command, database `aureuserp_testing_agent2`:

`docker compose run --rm --no-deps -e DB_DATABASE=aureuserp_testing_agent2 laravel.test php artisan translations:check --details`

Result: exit 1. Summary: 108 checks, 102 passed, 6 failed. TESTED. No language file was edited. The checker prints at most 10 keys and then "and N more". The 8 keys it hid are listed below from the English file, and they make up the reported count of 18.

Every other plugin's ar, es, fr, and pt_BR files passed, including logistics, security, accounts, and platform-billing.

### support, all four locales

File `filament/pages/help.php`. ar, es, fr, and pt_BR are each missing the same 18 keys. The locale files still have the older services block, so the guide block is absent and the structure check then reports a long mismatch. The 18 keys:

- `guides.group`
- `guides.superadmin.title`
- `guides.superadmin.description`
- `guides.superadmin.button`
- `guides.admin.title`
- `guides.admin.description`
- `guides.admin.button`
- `guides.admin.sections.title`
- `guides.user.title`
- `guides.user.description`
- `guides.user.button`
- `guides.user.full_heading`
- `guides.user.count`
- `guides.user.category_count`
- `guides.user.callouts.tip`
- `guides.user.callouts.note`
- `guides.user.callouts.important`
- `guides.user.sections.title`

Key order, same four locales, same file: positions 28–32. English has `empty` then the contact keys. The locale has the contact keys and then `empty`.

French only, `filament/resources/currency.php`, two extra keys:

- `table.bulk-actions.delete.notification.error.title`
- `table.bulk-actions.delete.notification.error.body`

### employees, fr only

Extra keys, not in English:

`filament/resources/employee.php`

- `infolist.tabs.resume.title`
- `infolist.tabs.resume.entries.resume.title`
- `infolist.tabs.resume.entries.resume.empty`
- `infolist.tabs.resume.entries.resume.untyped`
- `infolist.tabs.resume.entries.resume.present`
- `infolist.tabs.resume.entries.skills.title`
- `infolist.tabs.resume.entries.skills.empty`
- `infolist.tabs.resume.entries.skills.untyped`

`filament/resources/employee/relation-manager/resume.php`

- `form.sections.fields.attachments`
- `form.sections.fields.file`
- `form.sections.fields.file-helper-text`
- `form.sections.fields.attachment-name`
- `form.sections.fields.add-attachment`
- `table.columns.attachments`
- `infolist.entries.attachments`
- `infolist.entries.file`
- `infolist.entries.attachment-name`

Key order in that relation-manager file: position 2 English `form.sections.fields.type`, French `form.sections.fields.name`; position 3 is the swap of those two.

### products, fr only

Extra files, not in English:

- `exceptions/product-in-use.php`
- `exceptions/variant-in-use.php`

Extra keys in `filament/resources/attribute.php`:

- `table.actions.force-delete.notification.error.more`
- `table.bulk-actions.force-delete.notification.partial.title`
- `table.bulk-actions.force-delete.notification.partial.body`

## 3. Billing screens in the browser

Local app: `dekaerp-laravel.test-1` already listening on port 80, database `dekaerp` from `.env`. This review did not switch that database and did not log into production.

`http://localhost/admin/login` renders the Ignition page for `Spatie\LaravelSettings\Exceptions\MissingSettings` on `Webkul\Security\Settings\UserSettings`, property `company_admin_business_role_id`. See finding 1.

Plans, subscriptions, invoices, payments, discount codes, overdue extensions, the company edit page, and the company account page were not opened. Mobile width 390px was not checked. There was no session to open them with.

## 4. Logistics WP-14 specification

Written in `docs/logistics-plan.md` as "WP-14 detail" under §6a. The status row for WP-14 now says `spec written (2026-11-11, detail under §6a; no code)`. No other status row was changed. No PHP was written.

The spec covers `logistics_documents` and `logistics_vehicle_assignments`, `document_check` default `warn` and `document_alert_days` default 30 on the company settings model attributes, `DocumentChecker::assertForDispatch()` called from `DispatchService::assertDispatchable()` after the existing licence check, warn shown by the dispatch action, block thrown as `TripNotDispatchable`, O1 posted as one vehicle expense per period through the existing expense path, O4 as an assignment log with one open driver per vehicle, the expiry widget, the permission names, and the behaviour-named tests. GPS sync is out of this package (D21).

## 5. Upstream review

Last recorded upstream commit: `36744b597`, 2026-10-19, "Merge pull request #1629". Current `aureuserp/aureuserp` master tip seen from the GitHub API: `c31a0db21`, 2026-11-06. Nothing was merged or cherry-picked.

| Change | Does DEKA need it | Benefit | Risk | Priority |
| --- | --- | --- | --- | --- |
| `99fd7c7e8` block posting an unbalanced journal entry | Yes, as a single port. Local `assertPostable()` has no debit/credit check | Stops a journal that does not balance from becoming posted | Small if the check matches how this fork already computes line totals. A too-strict comparison could refuse legitimate tax or rounding lines | High |
| `10776eb0e` reversal debit/credit swap | Look before porting. Not compared line by line here | A reversed entry that swaps the wrong side misstates the books | Porting the wrong side of a reversal is worse than waiting | Medium, after a local read of `MoveWorkflow::reverse()` |
| `38268a2b8` work-order duration division by zero | Yes, as a small guard. Local `WorkOrder.php:230` divides by `time_efficiency`, and line 240 divides by capacity | A work center with efficiency 0 or capacity 0 does not fatal the screen | Low | Medium |
| `c0d8c750e` Filament 5.8.1 to 5.10.2, plus the Livewire 4 notification and locator follow-ups | No, not in this review. Already deferred locally on 2026-10-23 | Newer Filament bugfixes | This fork has local Filament view overrides. A bump republishes assets and needs a full re-diff | Deferred |
| `3ea4b46de` replenishment answers 404 on a direct URL when the resource is incomplete | Compare. Local `ReplenishmentResource` has no `canAccess()` 404 in the file that was read | Closes a URL that should not open | Low if the local resource is already complete | Low |
| `dd55acdc3` and `e53bbd756` partner invoice and bill tabs, permissions, credit notes | Optional, own comparison. This fork already has per-company contacts and a logistics shipments tab on the partner infolist | Customers see invoices on the contact | Copying the upstream tab can list another company's moves, or duplicate the local contribution | Low, product decision |
| CRM plugin (`5eb8e71b4`), branding palettes, compact admin theme, POS readme and lot handling, dashboard widgets | No | New product surface | A wholesale merge fights the fork's company, billing, and permission rules | Reject as a merge. Optional features only if asked |

No other post-`36744b597` commit rose to a required port from the commit subjects. UI compaction, theme CSS, and changelog commits are not actionable.

## Commands

| Command | Database | Result |
| --- | --- | --- |
| `git log --oneline -15 develop` | — | Exit 0. Used to choose the review range |
| `gh api` compare and commit lists for `aureuserp/aureuserp` since `36744b597` | — | Exit 0. Tip `c31a0db21` |
| `docker compose ps` | — | `laravel.test` up on port 80, `pgsql` healthy |
| `CREATE DATABASE aureuserp_testing_agent2 OWNER sail` | `aureuserp_testing_agent2` | `CREATE DATABASE` |
| `php artisan translations:check --details` | `aureuserp_testing_agent2` | Exit 1. 108 checks, 102 passed, 6 failed. See section 2 |
| Browser `http://localhost/admin/login` | `dekaerp` (the running app, not this agent's database) | 500 `MissingSettings` |

No Pest suite was run. Nothing above is claimed as a passing test suite.

## What was not done

- Billing screens were not clicked. The login page 500'd, and the shared local database was left unmigrated so another session on `dekaerp` is not disturbed.
- Production was not opened and no password was copied into this file.
- `aureuserp_testing`, `aureuserp_testing_claude`, `aureuserp_testing_claude_vault`, `aureuserp_testing_claude_backfill`, and `aureuserp_testing_saas11` were not used.
- No code, test, migration, view, or translation file was edited.
- The shared workflow skill library was not updated. This task allowed writes only in this file and in `docs/logistics-plan.md`.
