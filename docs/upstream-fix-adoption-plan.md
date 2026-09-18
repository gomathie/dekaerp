# Upstream Fix Adoption Plan

> **Status:** Active decision register and implementation plan  
> **Scope:** Whole DEKA ERP application, not Logistics alone  
> **Initial review:** 2026-09-18  
> **Local baseline:** `feature/logistics` at `e0ce9f6a8467`  
> **Common v1.5 baseline:** `8409fde`  
> **Upstream reviewed:** `aureus/master` at `d7d471894`  
> **Next review due:** 2026-09-25, or earlier when a trigger below occurs

## Purpose

This document records which post-v1.5 upstream test fixes and closely related
changes DEKA ERP already has, which gaps should be implemented locally, which
new features may be beneficial, and which upstream changes should not be
copied.

This is not a blanket cherry-pick list. DEKA ERP is a production, multi-company
fork with its own security boundaries, PostgreSQL deployment, tenant storage,
and product direction. Every upstream change must be adapted to the local code
and verified against those constraints.

The initial audit compared the local branch with
[`aureuserp/aureuserp`](https://github.com/aureuserp/aureuserp), including the
[v1.6.0 release](https://github.com/aureuserp/aureuserp/releases/tag/v1.6.0),
the commits explicitly labelled as failing-test fixes, their prerequisite
feature commits, and the corresponding local implementations and tests.

## Audit Boundary

The common v1.5 commit has 92 local-only commits and 215 upstream-only commits
after it. The initial pass traced all 14 upstream commits explicitly titled as
fixing a failing test or failing tests, then included adjacent coverage commits
and prerequisite features where a test fix could not be evaluated alone.

This is a complete register for that test-fix stream, not a claim of full
feature parity across all 215 upstream-only commits. Future reviews use the
recorded SHA to inspect new deltas, while large product features receive their
own comparison before adoption.

The local working tree already contained unrelated Security, plugin-manager,
seeder, helper, and change-log edits during this audit. They were treated as
concurrent work and were neither modified nor used as proof that their pending
behavior is verified.

## Decision Vocabulary

| Decision | Meaning |
|---|---|
| **Required** | A correctness, data-integrity, security, or verification gap that DEKA ERP should close. |
| **Recommended** | Valuable regression protection or UX correctness with limited product risk. |
| **Optional feature** | Useful new behavior, but it needs its own product decision and implementation package. |
| **Already present** | The runtime fix is already in DEKA ERP; tests may still be missing. |
| **Reject** | The upstream approach is weaker or unsafe for this fork. |
| **Defer** | Do not implement until its prerequisite or product decision exists. |

## Executive Recommendation

Implement this work in the following order:

1. **P0: Repair the verification baseline.** Align Composer, CI, and deployment
   on PHP 8.4.1 or newer and PostgreSQL 17 so a clean runner can install the
   current lockfile and exercise the deployed database generation.
2. **P0: Repair employee factories and add an Employee test suite.** The local
   plugin currently has no registered suite and several factories can write
   invalid columns, enum values, or foreign keys.
3. **P1: Add missing accounting regression coverage.** The company-currency and
   payment-register runtime fixes already exist locally but are not protected by
   the upstream regression tests.
4. **P1: Correct authenticated locale persistence and harden Website E2E
   assertions.** These are small, user-visible or test-trust improvements.
5. **P2: Improve translation-reference checking and CI artifacts.** These are
   quality improvements after the primary runners are reliable.
6. **Separate product initiatives:** installer localization, sales price lists,
   translatable Website content, and employee resume attachments. These should
   not be mixed into the test-fix work.

Do not start a broad upstream merge. Port one reviewed work package at a time,
run its focused suites, then run the affected regression suites.

## Findings Register

### UF-001 - CI Runtime and Lockfile Contract

**Decision:** Required, P0  
**Local state:** Open

The repository declares PHP `^8.3` in `composer.json`, and the Pest,
Playwright, and translation workflows provision PHP 8.3. The committed
`composer.lock` contains at least these packages requiring PHP `>=8.4.1`:

| Package | Locked version | PHP requirement |
|---|---:|---:|
| `symfony/filesystem` | `v8.1.5` | `>=8.4.1` |
| `symfony/options-resolver` | `v8.1.0` | `>=8.4.1` |
| `symfony/psr-http-message-bridge` | `v8.1.0` | `>=8.4.1` |

Local Sail and the production image use PHP 8.4, while local and production
PostgreSQL use version 17. The Pest and Playwright workflows still use
PostgreSQL 16. This is a deterministic configuration incompatibility; it is not
a claim that a specific remote Actions run was observed failing.

**Why DEKA ERP needs it:** A CI job that cannot install the committed lockfile
cannot validate any later test fix. Testing a different PostgreSQL major than
production also leaves avoidable compatibility uncertainty.

**Benefits:** Reproducible installs, trustworthy required checks, earlier
dependency failures, and closer production parity.

**Expected files:**

- `composer.json`
- `composer.lock` (lock metadata only, unless dependency resolution requires a
  reviewed update)
- `.github/workflows/pest_tests.yml`
- `.github/workflows/playwright_tests.yml`
- `.github/workflows/translations_check.yml`

**Implementation:**

1. Change the application PHP contract to `^8.4.1` after confirming every
   deployment target supplies at least that patch level.
2. Provision PHP 8.4 in all workflows.
3. Make PostgreSQL 17 the deployment-parity database lane. Retain MySQL as a
   compatibility lane only if DEKA ERP still intends to support it.
4. Refresh lock metadata with the approved Composer runtime; do not perform an
   unrelated dependency upgrade.
5. Verify a clean `composer install`, translation check, focused Pest suites,
   and Playwright bootstrap.

**Acceptance:** A clean CI runner installs the lockfile without platform
ignores, and the PostgreSQL 17 lanes reach and execute their test commands.

### UF-002 - Employee Factories and Missing Test Suite

**Decision:** Required, P0  
**Local state:** Open

DEKA ERP has no `plugins/webkul/employees/tests` directory and no
`EmployeeFeature` suite in `phpunit.xml`. The local employee factories contain
schema-level defects, including:

- `EmployeeFactory::parent_id` and `coach_id` use user IDs even though both
  foreign keys reference `employees_employees`.
- `employee_properties` is emitted even though the table has no such column.
- `distance_home_work_unit` emits `km` or `miles`, while `DistanceUnit` accepts
  `kilometer` or `meter`.
- `gender` calls `randomElement()` without choices.
- `employee_type` emits labels even though the model relation expects an
  `EmploymentType` ID.
- Several sibling factories use renamed/nonexistent fields such as `sequence`,
  `durations_days`, `status`, `user_id`, `open_date`, `start_date`, or `notes`.

Relevant upstream work:

- [`c1d95ca20`](https://github.com/aureuserp/aureuserp/commit/c1d95ca2096c8d56209e3f039a602960517cbb2c)
  aligns employee-related factories with their schemas.
- [`723b66496`](https://github.com/aureuserp/aureuserp/commit/723b66496)
  fixes invalid Employee values and adds factory tests.
- [`d60355fbd`](https://github.com/aureuserp/aureuserp/commit/d60355fbda235ec13c546e4b50300e3b02c2897e)
  adds the Employee suite, helpers, and resume-model coverage.
- [PR #1532](https://github.com/aureuserp/aureuserp/pull/1532) is the upstream
  EmployeeFactory fix set.

**Why DEKA ERP needs it:** Broken factories conceal real model and database
failures and prevent reliable HR, Logistics-driver, API, and authorization
tests. The absence of a suite means regressions can remain invisible while the
overall suite appears healthy.

**Benefits:** Trustworthy fixtures, PostgreSQL foreign-key coverage, an explicit
Employee regression gate, and reusable test setup for Logistics and HR work.

**Expected files:**

- The existing factories under `plugins/webkul/employees/database/factories/`
- `plugins/webkul/employees/tests/Feature/Models/EmployeeFactoryTest.php`
- `plugins/webkul/employees/tests/Feature/Models/EmployeeResumeTest.php`
- `plugins/webkul/employees/tests/Helpers/EmployeeHelper.php`
- `phpunit.xml`

**Implementation:**

1. Compare every upstream factory edit with the current local migration and
   model. Port field fixes, not files wholesale.
2. Keep recursive manager relationships nullable by default; provide explicit
   factory states when a parent or coach is required.
3. Use actual enum values and model IDs. Add multi-company assertions beyond
   upstream where a related factory can cross a tenant boundary.
4. Register `EmployeeFeature` and add basic creation/relationship tests.
5. Add resume-model coverage that does not require the deferred attachment
   feature.
6. Run Employee, Support, Security, and Logistics suites because those packages
   share users, companies, and employee/driver concepts.

**Do not port yet:** `ResumeAttachmentUploadTest`,
`EmployeeResumeAttachmentTest`, or attachment factory behavior. DEKA ERP has
deliberately deferred that feature pending tenant-S3 authorization design.

**Acceptance:** Every non-attachment employee factory can persist on
PostgreSQL, all generated enum and foreign-key values are valid, and the new
suite is registered and green.

### UF-003 - Employee Distance Unit Data Contract

**Decision:** Required investigation; implement based on production data, P0  
**Local state:** Open, and not fixed upstream

The original employee migration defaults `distance_home_work_unit` to `km`, but
the application enum exposes `kilometer` and `meter`. Upstream corrected its
factory but retained the historical migration default, so copying upstream is
not sufficient for DEKA ERP.

**Why DEKA ERP needs it:** Fresh defaults and UI values currently disagree.
Changing only the factory leaves live records and future database defaults
inconsistent.

**Benefits:** Stable filtering, validation, API output, and future distance
calculation without silently changing units.

**Implementation:**

1. Run a read-only production audit of distinct unit values and counts before
   writing a migration.
2. If only `km` exists, normalize it to `kilometer` and change the default in a
   forward migration.
3. If `miles` exists, do not map it to `meter`. Choose explicitly between
   supporting a mile enum value or converting both unit and numeric distance
   values transactionally.
4. Preserve nullable records and provide a reversible `down()` strategy that
   does not corrupt numeric meaning.
5. Add tests for existing data, fresh defaults, API serialization, and form
   options.

**Acceptance:** The database default, stored values, enum, forms, factories,
and API agree, with production data accounted for before deployment.

### UF-004 - Company Currency Guard Coverage

**Decision:** Required regression coverage, P1  
**Local state:** Runtime fix already present; tests missing

The local `CompanyObserver` matches the upstream guard that blocks a company
currency change after accounting entries exist. The final upstream coverage is
spread across [`99ba06883`](https://github.com/aureuserp/aureuserp/commit/99ba06883),
[`4a0bd1b69`](https://github.com/aureuserp/aureuserp/commit/4a0bd1b69), and
[`8262061d9`](https://github.com/aureuserp/aureuserp/commit/8262061d9).

The local `AccountHelper::company()` still uses unordered `firstOrFail()`, so
tests can select a different seeded company when IDs vary.

**Why DEKA ERP needs it:** Company currency is an accounting invariant. A
regression could make historical journal entries inconsistent, especially
across parent/branch companies.

**Benefits:** Data-integrity protection, deterministic tests, and explicit
coverage of hierarchy boundaries.

**Implementation:** Order the helper by ID and adapt the final upstream tests
for: changing before entries, blocking after entries, preserving the original
currency, validation error key, editing unrelated fields, parent-to-branch and
branch-to-parent behavior, and isolation from unrelated companies.

**Expected files:**

- `plugins/webkul/accounts/tests/Helpers/AccountHelper.php`
- `plugins/webkul/accounts/tests/Feature/Workflows/CompanyCurrencyGuardTest.php`

**Acceptance:** The focused test passes in both hierarchy directions and no
test relies on an unspecified first-row order.

### UF-005 - Payment Register Bank Account Coverage

**Decision:** Recommended, P1  
**Local state:** Runtime fix already backported; tests missing

The local Pay/PaymentRegister behavior contains the root fix documented in
`docs/change-log.md`, but it lacks the final upstream tests from
[`c5f2d3017`](https://github.com/aureuserp/aureuserp/commit/c5f2d30171a5b7e719288d78833fb06d81c15dfe)
and [`4a464e118`](https://github.com/aureuserp/aureuserp/commit/4a464e118).

**Why DEKA ERP needs it:** Payment destination selection is financially
sensitive. A blank or wrong bank account can misdirect an otherwise valid
payment workflow.

**Benefits:** Protection for inbound journal accounts, missing-bank behavior,
outbound vendor accounts, batch account resolution, and preferred bill bank
selection.

**Implementation:** Port the final five scenarios and adapt setup to local
company scoping. Do not restore an earlier upstream cross-company BankAccount
test because the current BankAccount model has no `company_id` contract.

**Expected file:**
`plugins/webkul/accounts/tests/Feature/Workflows/PaymentRegisterBankAccountTest.php`

### UF-006 - Authenticated Locale Persistence

**Decision:** Recommended product fix, P1  
**Local state:** Open

The topbar locale switcher sends `?lang=...`. For authenticated users, the local
`SetLocale` middleware applies that language only for the current request, then
clears the session locale. The next navigation falls back to the profile
language. Upstream commit
[`4bb04940a`](https://github.com/aureuserp/aureuserp/commit/4bb04940a)
uses query language, then session language, then user preference, and persists
an explicit query choice in the session. The related assertions were updated in
[`153c40ce9`](https://github.com/aureuserp/aureuserp/commit/153c40ce9).

**Why DEKA ERP needs it:** This is a real UX defect, not merely a test change.
The visible language selector currently appears to work and then silently
reverts.

**Benefits:** Predictable navigation for all supported languages without
silently modifying the user's saved profile preference.

**Implementation:** Adapt the upstream precedence and add tests for guest,
authenticated, invalid-query, persisted-session, profile fallback, and profile
update behavior. Preserve the current profile page behavior that updates both
the stored preference and session. Explicitly accept that a language chosen
before login continues after login; this is the most coherent interpretation
of an explicit user choice.

**Expected files:**

- `app/Http/Middleware/SetLocale.php`
- `tests/Feature/Locale/SetLocaleMiddlewareTest.php`

### UF-007 - Website Publish/Draft E2E Assertions

**Decision:** Recommended test hardening, P1  
**Local state:** Open

The local Website page object conditionally skips publish and draft actions
when their buttons are not visible. That can turn a broken route, missing
action, or permission regression into a passing test. Upstream commit
[`6fb89b844`](https://github.com/aureuserp/aureuserp/commit/6fb89b844)
makes the edit URL and action visibility explicit assertions.

**Why DEKA ERP needs it:** A test that omits the action under test is a false
green.

**Benefits:** Better diagnostics and real coverage of public visibility state
transitions.

**Expected file:** `tests/e2e-pw/pages/06_websiteManagement.ts`

**Acceptance:** Targeted Website page/blog publish and draft specs fail when the
action is unavailable and pass through the real state transition.

### UF-008 - Translation Key Reference Audit

**Decision:** Recommended local improvement, P2  
**Local state:** Partial tooling only

The local `translations:check` command compares each locale with canonical
English. It cannot detect a key that is missing from English too, or a literal
`__()` key in PHP that points to the wrong file/path. Upstream commit
[`33ed6ac32`](https://github.com/aureuserp/aureuserp/commit/33ed6ac32)
fixed roughly 48 references found by such a scan, but did not add a reusable
scanner to the repository.

**Why DEKA ERP needs it:** Locale parity can be green while users still see raw
translation keys.

**Benefits:** Prevents broken labels across Support, Accounts, Employees,
Fields, Logistics, and future plugins; turns a one-off upstream audit into a
repeatable local quality gate.

**Implementation:** First compare each of the upstream key corrections against
local code. Then extend the existing command, preferably behind a
`--references` option initially, to validate statically resolvable literal
translation calls against English files. Dynamic keys must be explicitly
ignored or allowlisted to avoid false positives. Add command tests before
making it a required CI gate.

### UF-009 - Installer Country and Currency Resolution

**Decision:** Optional feature with practical value, P2  
**Local state:** Not implemented

Upstream's failing-test commit
[`fa033899f`](https://github.com/aureuserp/aureuserp/commit/fa033899f)
only makes sense with the preceding installer feature beginning at
[`98b870ad7`](https://github.com/aureuserp/aureuserp/commit/98b870ad7).
Follow-up commits also refactor the behavior and add an environment mismatch
warning. The local installer currently seeds the first/default currency and has
no `--country` or `--currency` options.

**Why it may be worth implementing:** New installations should not quietly
start with the wrong company country or accounting currency.

**Benefits:** Correct regional defaults, deterministic noninteractive installs,
and fewer post-install accounting corrections.

**Risks:** Installer changes affect destructive reinstall paths, CI bootstrap,
company seed assumptions, and currency settings. Existing production data does
not benefit automatically.

**Recommendation:** Review the complete upstream issue/commit chain as one
feature. Add explicit `--country` and `--currency` options, safe no-TTY
fallbacks, known/unknown-code tests, and deployment documentation. Do not
cherry-pick `fa033899f` alone.

### UF-010 - Sales Order Price Lists

**Decision:** Optional feature, high business value, separate initiative  
**Local state:** Product PriceList CRUD and company isolation exist; Sales order
integration does not

[PR #1568](https://github.com/aureuserp/aureuserp/pull/1568) adds a substantial
price-list engine and Sales integration. Its merge changes 84 files with more
than 4,000 added lines, including data migrations, a resolver service, API
contracts, partner defaults, quotation behavior, currency synchronization, and
tests. The final test-fix commit is
[`def1834ce`](https://github.com/aureuserp/aureuserp/commit/def1834ce).

**Why it may be worth implementing:** Customer-specific, quantity, category,
and contract pricing can reduce manual quotation errors and make pricing policy
consistent. Automatic currency synchronization is especially useful for
multi-currency customers.

**Benefits:** Central pricing rules, reusable API behavior, fewer manual price
overrides, and auditable customer pricing.

**Risks:** This changes financial behavior and migrates existing price-rule
data. It must define repricing behavior, historical-order immutability,
multi-company access, currency changes, API compatibility, and interaction with
DEKA ERP's unified product-resource direction.

**Recommendation:** Do not cherry-pick the final test fix or the full PR into
the current branch. Create a dedicated design and rollout package with:

1. Current production price-rule and price-list data inventory.
2. Tenant-scope and historical-pricing decisions.
3. Rehearsed forward and rollback migrations on a production-shaped copy.
4. A local resolver contract and currency behavior.
5. Quotation/order UI and API integration.
6. Unit, workflow, API, authorization, and multi-company tests.

### UF-011 - Translatable Website and Blog Content

**Decision:** Optional feature, defer  
**Local state:** Intentionally not backported

Upstream's translatable Website/Blog work introduces package/provider changes,
JSON-backed translated fields, locale-aware search, Filament translatable
drivers, and PostgreSQL-specific migration corrections. The failing-test
commits [`153c40ce9`](https://github.com/aureuserp/aureuserp/commit/153c40ce9),
[`b6769d06d`](https://github.com/aureuserp/aureuserp/commit/b6769d06d),
[`2b3e90ce5`](https://github.com/aureuserp/aureuserp/commit/2b3e90ce5), and
[`4e5fbd305`](https://github.com/aureuserp/aureuserp/commit/4e5fbd305)
are follow-up fixes, not standalone patches.

**Potential benefit:** Localized public pages and blog content matching DEKA
ERP's supported admin languages.

**Risks:** Production text-to-JSON conversion, new dependencies, locale
fallback/search semantics, rollback complexity, and possible overlap with the
separate marketing site.

**Recommendation:** Keep deferred until localized public content is a product
requirement. If approved, treat the complete feature and all four test-fix
commits as one migration project with backups and rehearsal.

### UF-012 - Employee Resume Attachments

**Decision:** Optional feature, defer  
**Local state:** Not implemented

Upstream commit
[`3bfe42832`](https://github.com/aureuserp/aureuserp/commit/3bfe42832)
fixes a ManageResume test around a feature that includes resume attachments.
Porting its route fallback or attachment tests alone would add no local value.

**Potential benefit:** Central HR document storage attached to employee resume
records.

**Risks:** Tenant-S3 paths, MIME and size validation, malware handling,
cross-company authorization, signed-download access, retention, and deletion.

**Recommendation:** Keep attachments as a separate security-reviewed feature.
A non-attachment ManageResume smoke test may be added during UF-002.

### UF-013 - Playwright JSON Results

**Decision:** Optional CI improvement, P2  
**Local state:** HTML/blob reporting already exists

Upstream commit
[`ebbfd5da0`](https://github.com/aureuserp/aureuserp/commit/ebbfd5da0529040607cc840fab89a508811838a6)
adds JSON output for easier machine processing.

**Benefit:** Trend analysis and automated extraction of failed specs without
opening an HTML report.

**Recommendation:** Add after UF-001 and only if a consumer for the JSON artifact
is identified. Existing human-readable reports remain sufficient meanwhile.

### UF-014 - Committed `.env.pgtest`

**Decision:** Reject  
**Upstream reference:**
[`9e6bb3486`](https://github.com/aureuserp/aureuserp/commit/9e6bb3486)

DEKA ERP already blanks `DB_URL` and `DATABASE_URL` in `phpunit.xml` and pins a
throwaway Docker PostgreSQL database. A committed environment file with static
credentials and an application key would duplicate configuration and weaken the
existing managed-database safeguard.

**Benefit of rejecting it:** One authoritative test configuration and lower
risk of running `migrate:fresh` against a managed database.

### UF-015 - `APP_DEBUG=true` in Test Environment

**Decision:** Reject as unnecessary  
**Upstream reference:**
[`512ee6221`](https://github.com/aureuserp/aureuserp/commit/512ee6221959ed3c220ccb35b74f79c68c4728d5)

Tests should assert behavior without depending on debug exception rendering.
Enable debug only for a targeted diagnosis, not as a prerequisite for passing
tests.

### UF-016 - Documentation Drift

**Decision:** Required maintenance, P1  
**Local state:** Open

The audit found status drift between project documents. For example,
`docs/handover-actions.md` still describes remaining optional suites while
`docs/change-log.md` records all ten suites passing on 2026-09-04, and
`docs/restructure-backlog.md` still describes the PHP 8.4 runtime as unavailable
even though Sail is now the documented test path. `AGENTS.md` also contains an
auto-generated Laravel Boost version block that is older than the explicit
DEKA ERP project-memory versions.

**Why DEKA ERP needs it:** Agents make unsafe or duplicated decisions when plan,
handover, and runtime documents disagree.

**Benefits:** Faster onboarding, fewer repeated investigations, and a reliable
handoff between agents.

**Implementation:** During each work package, update the owning plan and any
status document whose statement became false. Do not rewrite historical
records; mark them superseded and link to the current source of truth.

## Already Present Locally

The following upstream runtime work does not need to be reimplemented:

| Area | Local conclusion | Remaining action |
|---|---|---|
| Shared-company Sequence invariant | The local invariant matches [`7d1a68e41`](https://github.com/aureuserp/aureuserp/commit/7d1a68e41). | Keep the existing test registered. |
| Company currency observer | Runtime guard matches the final upstream implementation. | Add UF-004 coverage. |
| Payment-register account selection | Root behavior is already backported and documented. | Add UF-005 coverage before calling it closed. |
| PostgreSQL test isolation | Local `phpunit.xml` is safer than upstream `.env.pgtest`. | Retain and re-check whenever test bootstrap changes. |

Other v1.6 backports and fork-specific security fixes remain documented in
`docs/change-log.md`; this plan does not replace that history.

## Implementation Packages

### Package A - Verification Baseline

**Contains:** UF-001  
**Dependencies:** Confirm deployment PHP patch version  
**Risk:** Medium, because Composer metadata and CI are shared infrastructure  
**Verification:** Composer validation/install, translation command, Pest matrix,
Playwright bootstrap, PostgreSQL 17 service health

### Package B - Employee Reliability

**Contains:** UF-002 and the audit phase of UF-003  
**Dependencies:** Package A  
**Risk:** Medium; factories affect many tests, while any data migration is
production-sensitive  
**Verification:** EmployeeFeature, SupportFeature, SecurityFeature,
LogisticsFeature, migration test on a production-shaped copy

Do not put the UF-003 production migration in the same deployment until the
read-only value audit is complete.

### Package C - Accounting Regression Locks

**Contains:** UF-004 and UF-005  
**Dependencies:** Package A  
**Risk:** Low runtime risk because the initial change is tests/helper ordering  
**Verification:** AccountFeature, AccountingFeature, SupportFeature, and focused
test filters

### Package D - Locale and Website Test Correctness

**Contains:** UF-006 and UF-007  
**Dependencies:** Package A  
**Risk:** Low to medium; locale precedence is user-visible  
**Verification:** locale feature tests, authenticated browser navigation, and
targeted Website Playwright specs

### Package E - Translation and Reporting Quality

**Contains:** UF-008, UF-013, and UF-016  
**Dependencies:** Package A  
**Risk:** Low if the scanner begins as non-blocking  
**Verification:** command tests, current translation parity, false-positive
review, and CI artifact inspection

### Separate Product Proposals

Create independent plans and approval points for UF-009, UF-010, UF-011, and
UF-012. Each proposal must include user value, data migration, authorization,
multi-company behavior, rollback, and deployment rehearsal. None is a
prerequisite for closing the failing-test audit.

## Verification Matrix

| Change area | Minimum focused checks | Broader regression checks |
|---|---|---|
| CI/runtime | Clean Composer install; workflow syntax; service health | Full CI matrix |
| Employee factories | EmployeeFeature | Support, Security, Logistics |
| Employee unit migration | Migration up/down on copied data; model/form/API tests | Employee and Logistics |
| Company currency | `CompanyCurrencyGuardTest` | Account, Accounting, Support |
| Payment register | `PaymentRegisterBankAccountTest` | Account, Accounting |
| Locale | `SetLocaleMiddlewareTest` | Filament locale/navigation tests |
| Website actions | Targeted page/blog Playwright specs | Website E2E group |
| Translation references | Command unit/feature tests | Full `translations:check --details` |

All database-writing tests must use the throwaway Sail PostgreSQL database and
run serially unless the test infrastructure is explicitly redesigned for
parallel isolation.

## Deployment and Rollback Rules

1. Finish or checkpoint the currently dirty Security and plugin-manager work
   before beginning these packages. Do not mix unrelated diffs.
2. Test-only packages can deploy independently after their relevant suites
   pass.
3. UF-003, UF-010, and UF-011 require a production data inventory, backup, and
   rehearsal before migration.
4. Do not edit old migrations to repair production state. Add forward,
   backward-compatible migrations.
5. Never weaken company scoping to make an upstream test pass.
6. A failed port is reverted at the package boundary, not hidden with skipped
   assertions or broad exception catches.

## Recurring Upstream Review Rule

Agents cannot monitor the repository while no session is active. Therefore,
the first active agent after the due date owns the review.

Perform an upstream review when any of these triggers occurs:

- Seven calendar days have passed since `Last reviewed` below.
- Before a production release or dependency refresh.
- Before substantial work in a plugin that upstream changed since the recorded
  SHA.
- When the user asks about upstream fixes, failures, features, or parity.

For every review:

1. Fetch or otherwise inspect the current authoritative upstream state.
2. Compare only the range after the recorded upstream SHA, while also checking
   prerequisites for each interesting commit.
3. Inspect both upstream and local implementations; never classify from commit
   titles alone.
4. Classify each finding as Required, Recommended, Optional feature, Already
   present, Reject, or Defer.
5. Update this register, its baseline SHA/date, affected plans, and the review
   log even when no actionable changes are found.
6. Inform the user concisely of what is new, why DEKA ERP needs or does not need
   it, expected benefits, risks/dependencies, and the recommended priority.
7. Never merge or cherry-pick upstream wholesale without explicit review and
   user approval.

## Documentation Rule for All Agents

At the start of a new workstream, every agent must inventory and read all
project-owned Markdown documents, excluding dependency/generated trees such as
`vendor`, `node_modules`, and build output. The current inventory can be found
with:

```powershell
rg --files -g "*.md" -g "!vendor/**" -g "!node_modules/**" -g "!public/build/**"
```

On a resumed workstream, re-read `AGENTS.md`, `docs/agent-reminders.md`, this
plan, the owning feature plan, the latest relevant `docs/change-log.md`
entries, and every Markdown file changed since the previous handoff. Scan the
full inventory for newly added documents.

Documentation is part of completion:

| Information | Required destination |
|---|---|
| Implementation details, root cause, verification, and failures | `docs/change-log.md` |
| Durable trap or instruction for future agents | `docs/agent-reminders.md` and, when significant, project memory in `AGENTS.md` |
| Feature decisions, package state, owner, and handoff | The owning `docs/*-plan.md` |
| Upstream comparison and adoption decision | This document |
| User-visible released behavior | Root `CHANGELOG.md` only after it is actually released |
| Dashboard/deployment action the user must perform | `docs/handover-actions.md` |

Before finalizing, agents must reconcile contradictory status statements in
documents they touched or explicitly record the contradiction as an open item.
They must not claim tests or production checks that were not executed.

## Upstream Review Log

| Last reviewed | Upstream SHA | Local SHA | Outcome | Next due |
|---|---|---|---|---|
| 2026-09-18 | `d7d471894` | `e0ce9f6a8467` | Initial whole-app test-fix audit. P0 CI/Employee gaps, P1 accounting/locale/E2E gaps, optional product initiatives, and explicit rejects recorded. | 2026-09-25 or earlier trigger |

## Completion Criteria for This Plan

This plan is complete only when:

- Required and Recommended findings are implemented, verified, or explicitly
  declined by the user with the reason recorded.
- Runtime fixes that were already present have regression coverage.
- Production-sensitive migrations have a data audit and rehearsed rollback.
- Optional features have separate approved plans or a recorded defer decision.
- CI can install the committed lockfile and exercise the production-parity
  PostgreSQL lane.
- The review baseline and user-facing summary are current.
