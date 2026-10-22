# Upstream Fix Adoption Plan

> **Status:** Active decision register and implementation plan  
> **Scope:** Whole DEKA ERP application, not Logistics alone  
> **Initial review:** 2026-09-18  
> **Local baseline:** `feature/logistics` at `e0ce9f6a8467`  
> **Common v1.5 baseline:** `8409fde`  
> **Upstream reviewed:** `aureus/master` at `36744b597`  
> **Package A implemented:** 2026-09-20; focused local verification complete, first remote CI run pending
> **Next review due:** 2026-10-27, or earlier when a trigger below occurs

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
**Local state:** Implemented locally on 2026-09-20; first remote CI run pending

Before Package A, the repository declared PHP `^8.3` in `composer.json`, and
the Pest, Playwright, and translation workflows provisioned PHP 8.3. The
committed `composer.lock` already contained at least these packages requiring
PHP `>=8.4.1`:

| Package | Locked version | PHP requirement |
|---|---:|---:|
| `symfony/filesystem` | `v8.1.5` | `>=8.4.1` |
| `symfony/options-resolver` | `v8.1.0` | `>=8.4.1` |
| `symfony/psr-http-message-bridge` | `v8.1.0` | `>=8.4.1` |

Local Sail and the production image used PHP 8.4, while local and production
PostgreSQL used version 17. The Pest and Playwright workflows still used
PostgreSQL 16. This was a deterministic configuration incompatibility; it is
not a claim that a specific remote Actions run was observed failing.

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

**Implementation record (2026-09-19 to 2026-09-20):**

- Raised the root PHP contract from `^8.3` to `^8.4.1`, provisioned PHP 8.4
  in all three workflows, and aligned the Pest and Playwright PostgreSQL lanes
  with PostgreSQL 17.
- Added `composer audit --locked --no-interaction` to the push/pull-request
  translation workflow so a vulnerable committed lockfile fails CI once,
  rather than once per database or browser shard.
- A lock refresh exposed four advisories in three direct dependencies. Applied
  only the patched releases: all twelve Filament split packages from 5.7.3 to
  5.7.6, Livewire from 4.3.3 to 4.3.4, and Laravel Excel from 3.1.69 to
  3.1.70. The root constraints now preserve those minimum patched versions.
- The addressed advisories are Filament MFA code replay
  ([CVE-2026-84306](https://github.com/advisories/GHSA-r3j6-gpjw-qfjr)),
  Filament password-validity disclosure
  ([CVE-2026-84307](https://github.com/advisories/GHSA-xwpv-pqxp-5v36)),
  Livewire DOM XSS
  ([CVE-2026-81887](https://github.com/advisories/GHSA-g3hc-697w-wm82)),
  and Laravel Excel export path traversal outside the configured disk
  ([CVE-2026-84374](https://github.com/advisories/GHSA-c7r6-vx3h-w5g2)).
- Rejected dry runs that would have refreshed 50-74 unrelated packages.
  The committed lock delta is limited to the 14 package patches above plus a
  metadata-only canonical repository URL change for the same locked Guava icon
  picker release.
- Composer's `filament:upgrade` hook republished five tracked frontend assets.
  Each public file hashes exactly to its installed Filament or Website package
  source. The three local Filament Blade overrides were re-diffed against
  5.7.6; none of their upstream source views changed from 5.7.3.
- The first full Pest run exposed an existing order-dependent authorization
  bug: plugin installation and fresh-install defaults selected `Role::first()`,
  which now resolves to the protected `Multi-Company Admin` role. Both paths
  now select the configured `Admin` role by name and guard. An idempotent
  Security migration restores the protected role's exact eight-permission
  baseline and repairs an affected global default role setting.

**Local verification so far:**

- `composer validate --no-check-publish` passed, with only the existing exact
  `filament-shield` constraint warning.
- A clean `composer install --no-interaction --prefer-dist
  --optimize-autoloader` completed under Sail PHP 8.4.23, including package
  discovery and Filament asset publication.
- `composer audit --locked --no-interaction` reports no advisories.
- Symfony YAML parsed all four workflow files successfully under PHP 8.4.
- The Playwright lock installed with no npm advisories, and
  `playwright test --list` discovered all 220 Chromium tests in 19 files.
- `translations:check --details` executed fully: 93 of 108 locale sets pass.
  The 15 failures are existing key/structure drift in Accounts (fr), Employees
  (fr), Logistics (all four non-English locales), Products (fr), Security (all
  four), and Support (all four). They are not caused by this dependency patch
  and remain tracked under the translation work package.
- The initial full serial PostgreSQL Pest run completed with 2 failures, 2,422
  passes, and 6,783 assertions in 38,339.61 seconds. Both failures were
  investigated: the role-ordering defect above and a user fixture that skipped
  its normal partner-creation event.
- After the fixes, the focused Security suite passed all 41 tests and 121
  assertions in 410.53 seconds. It covers fresh ERP installation, the repair
  migration, Filament user promotion, exact protected-role permissions, and a
  real `contacts:install` permission refresh.
- The 10-hour full serial suite was not repeated after the focused repairs.
  Remote GitHub Actions and the Playwright matrix remain the final
  whole-application acceptance signal.

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
**Local state:** **Fixed 2026-10-22**

> Added plugins/webkul/accounts/tests/Feature/Workflows/CompanyCurrencyGuardTest.php
> with coverage for permitted changes, blocked changes after journal entries,
> preserved currencies, validation field errors, unrelated edits, parent/branch
> hierarchies, and unrelated-company isolation. AccountHelper::company() now
> orders by ID so hierarchy tests consistently select the seeded baseline
> company. The existing CompanyObserver required no runtime change. The
> focused test passed: 8 tests, 8 assertions.

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

---

*The following UF entries (UF-017 through UF-024) were identified during the
v1.6.0 release review on 2026-10-20. They were not captured in the initial
audit because the initial pass focused on failing-test commits rather than
the full feature and fix set. Upstream commit SHAs must be confirmed during
implementation by searching the aureuserp/aureuserp master history.*

### UF-017 - XSS in Chatter Change Summaries

**Decision:** Required, P0 — security  
**Upstream reference:** v1.6.0 release note: "[Security] Escaped HTML entities
in chatter change summaries to prevent XSS." Issue/commit TBD.  
**Local state:** **Not applicable — investigated 2026-10-22, this fork was
never exposed.** Guarded by a regression test rather than a code change; see
below.

### UF-017 investigation, 2026-10-22

Every chatter rendering surface was traced. None of them puts user-controlled
text into unescaped HTML:

| Surface | How it renders | Verdict |
|---|---|---|
| `old_value`, `new_value`, field name in change summaries | `{{ }}` | Escaped |
| Step `$icon` | hardcoded `match()` over static SVG path data, keyed on `$change['type']` | No user input reaches it |
| Activity bodies | translation strings with `getModelTitle()` interpolated; all 37 implementations return a literal or a `match()` over an enum, never a model attribute | No user text reaches them |
| Message `body` and `subject` | `sanitizeHtml()`, or Filament's `RichContentRenderer` for rich-editor documents | Sanitised; the renderer is the legitimate rich text the upstream note warns against breaking |

Because there is nothing to fix, the risk is that someone reintroduces it. A
test asserting "Blade escapes `{{ }}`" would be tautological, so
`plugins/webkul/chatter/tests/Feature/ChatterEscapingTest.php` pins the
decision instead:

1. Every `{!! !!}` in a chatter view must match an allowlisted sink
   (`sanitizeHtml()`, `ChatterMentions::render(`, `$icon`). A new unescaped
   sink fails until it is justified and added.
2. The three change-summary expressions are asserted to still use `{{ }}`, so
   switching one to `{!! !!}` fails with a message saying why it matters.
3. No `getModelTitle()` may `return $this->someAttribute;`, which is the one
   way user text could reach the unescaped activity-body sink.

Verified against 12 chatter views and 37 `getModelTitle()` implementations
across 324 model files. A `ChatterFeature` suite was added to `phpunit.xml`;
the plugin had tests nowhere before, so anything written here would never have
run.

Upstream escaped HTML entities written into chatter change-summary strings.
Unescaped user-controlled content rendered as HTML in the activity log can
allow stored XSS against any user who views a record's chatter.

**Why DEKA ERP needs it:** DEKA ERP uses the chatter on shipments, trips,
expenses, invoices, and most entity types. An unescaped string is a stored
XSS vector for every tenant.

**Benefits:** Eliminates a stored-XSS surface in one of the most-visited
parts of the UI.

**Implementation:**

1. Find the upstream commit, identify the exact escaping change, and
   determine which chatter helper or observer is the entry point.
2. Check local `ChatterCleanupService` and any chatter-writing path in
   `Webkul\Chatter` for the same unescaped write.
3. Apply the equivalent escaping. Do not break Markdown or rich-text that is
   legitimately rendered.
4. Add a test that injects an HTML payload via a model update and asserts the
   rendered chatter entry is escaped.

**Acceptance:** A `<script>` or `<img onerror>` payload in a tracked field
value appears in the chatter as literal text, not as executable HTML.

### UF-018 - Payment State Not Updating After Invoice is Paid

> **Already handled — confirmed 2026-10-22.** `Move::computePaymentState()`
> (`Models/Move.php:675`) derives `payment_state` from the reconciliation
> matchings and `amount_residual`, and it is called after a payment is
> registered (`Services/PaymentRegistrar.php:342`) as well as from the move's
> own recompute path (`Move.php:510`). Paying an invoice therefore advances
> the state. `BLOCKED` is deliberately preserved rather than reset to
> `NOT_PAID`.
>
> **A warning for whoever audits this next.** A grep for writes to
> `payment_state` using array-key patterns (`'payment_state' =>`) finds only
> factories and API resources, which makes it look as though nothing in
> application code ever sets it - a false positive that reads like a P1
> financial bug. The real assignments are object-property writes
> (`$this->payment_state = ...`) inside `computePaymentState()`. Search for
> the method, not the column.
>
> Still worth adding the regression test the implementation notes below ask
> for; the behaviour is currently unpinned.

**Decision:** Required, P1 — financial correctness  
**Upstream reference:** v1.6.0 issue #1478. Commit TBD.  
**Local state:** Open — not investigated

Upstream fixed a defect where paying an invoice did not advance its state
field, leaving paid invoices still showing as "open" or "draft".

**Why DEKA ERP needs it:** Payment state drives invoice filtering, aging
reports, and the Logistics invoicing screen. A stale state can re-queue
already-paid shipment invoices for billing.

**Benefits:** Correct invoice lifecycle state for every payment path.

**Implementation:**

1. Find the upstream commit and the state-machine path it repairs.
2. Check local `Account\Models\Move` state transitions and the payment
   registration flow for the same defect.
3. If already fixed locally (it may be part of the v1.5 backport), add a
   regression test that pays an invoice and asserts `state === 'posted'` +
   `payment_state === 'paid'`.

### UF-019 - Livewire max_nesting_depth 10 → 30

> **Already implemented — confirmed 2026-10-22.** `config/livewire.php:295`
> already sets `'max_nesting_depth' => 30` inside the `payload` block. The
> key is real in the installed Livewire 4.3.4 and is read as
> `livewire.payload.max_nesting_depth` by
> `HandleComponents::updateProperty()`, so the published value takes effect.
> Nothing to do. The "Local state" line below was stale.
>
> Two details worth keeping: the key is nested under `payload`, not top level,
> so setting a top-level `max_nesting_depth` would silently do nothing; and
> the guard is `if ($maxDepth !== null && ...)`, so a published config that
> omits the key disables the limit entirely rather than falling back to 10.

**Decision:** Required, P1 — reliability  
**Upstream reference:** v1.6.0 release note. Config location TBD.  
**Local state:** Open — not investigated

Upstream increased Livewire's `max_nesting_depth` from 10 to 30 to fix
crashes in deeply nested repeater components.

**Why DEKA ERP needs it:** Logistics stops, trip forms, and shipment line
repeaters are multi-level. Any user who adds enough nested components will
hit the current limit silently (Livewire renders an error without a useful
message).

**Benefits:** Prevents cryptic repeater failures on complex forms without
architectural changes.

**Implementation:**

1. Find the upstream config change (likely `config/livewire.php` or a
   `Livewire::configure()` call in a service provider).
2. Apply the same change locally. Check whether DEKA ERP already publishes
   a `config/livewire.php`; if not, publish it first.
3. Add a smoke test or note in manual QA that a five-stop trip with line items
   renders without a nesting error.

### UF-020 - Saved Filter Views Throw LogicException

**Decision:** Required, P1 — UX correctness  
**Upstream reference:** v1.6.0 issue #1490. Commit TBD.  
**Local state:** **Fixed 2026-10-22**

> `SavedView::getModel()` now honours Filament's `?string` model contract in
> `plugins/webkul/table-views/src/Filament/Components/SavedView.php:20-29`,
> while `HasTableViews.php:464` passes the saved record through
> `getRecord()`. The previous implementation returned a `TableView` object
> from a `?string` method while rendering the edit action, which made saved
> views fail during hydration. `SavedViewTest.php` pins the contract: 1 test,
> 2 assertions passed.

Saved Filter Views across list resources threw a `LogicException`. This
makes any saved filter unusable for the session.

**Why DEKA ERP needs it:** Power users and operations staff rely on saved
filters to segment shipments, expenses, and trips by company, driver, or
state. A runtime crash blocks those workflows.

**Benefits:** Reliable filter state across every list resource.

**Implementation:**

1. Find the upstream fix and the exception trigger (likely a null state or
   missing key during filter hydration).
2. Reproduce locally if possible (create a saved filter, navigate away, return).
3. Port the minimal fix. Do not restructure the filter pipeline.
4. Add a test that saves and reloads a filter without throwing.

### UF-021 - FK Violation on partners_partners.parent_id Editing an Employee

**Decision:** Required, P1 — data integrity  
**Upstream reference:** v1.6.0 issue #1491. Commit TBD.  
**Local state:** **Fixed 2026-10-22**

> `plugins/webkul/employees/database/migrations/2024_12_12_063353_create_employees_employees_table.php:101`
> constrains `parent_id` to `employees_employees.id`, and
> `plugins/webkul/employees/src/Models/Employee.php:169-172` defines the same
> employee-to-employee relation. `EmployeeFactory.php:43-44` was instead
> assigning user IDs; its defaults now use `null`, while an explicit manager
> uses an employee ID. The complete `FactoryTest.php` passed: 6 tests,
> 10 assertions.

Editing an employee could trigger a foreign key violation on
`partners_partners.parent_id`. The defect likely arises from the Employee
observer or partner-sync code writing a User ID into a column that expects a
Partner ID.

**Why DEKA ERP needs it:** Saving an employee with a manager selection
currently risks a FK constraint error that aborts the save silently or with
a 500.

**Benefits:** Reliable employee editing across all HR workflows.

**Implementation:**

1. Trace the upstream commit to identify which column receives the wrong ID type.
2. Check local `Employee` model, its observer, and any
   `partner_id` / `parent_id` assignment path.
3. Apply the fix and add a factory test that creates an employee with a manager
   and persists without a FK error.

### UF-022 - Currency Conversion Not Working in RFQ / Purchase Order

**Decision:** Required, P1 — financial correctness  
**Upstream reference:** v1.6.0 issue #1497. Commit TBD.  
**Local state:** Open — not investigated

Currency conversion was not applied when creating or editing an RFQ or
Purchase Order in a currency different from the company's base currency.

**Why DEKA ERP needs it:** DEKA ERP companies can operate in multiple
currencies. Incorrect conversion produces wrong committed costs and payable
amounts.

**Benefits:** Correct multi-currency RFQ and PO line totals.

**Implementation:**

1. Find the upstream fix and identify the conversion call that was missing
   or bypassed.
2. Check local `Purchase\Models\Order` / `OrderLine` and the currency-rate
   resolution path.
3. Port the fix and add a test that creates a PO in a non-base currency and
   asserts converted totals.

### UF-023 - Exception Deleting an Active Currency

**Decision:** Recommended, P2  
**Upstream reference:** v1.6.0 issue #1514. Commit TBD.  
**Local state:** **Fixed 2026-10-22**

> Individual currency delete actions already caught `QueryException` in
> `plugins/webkul/support/src/Filament/Resources/CurrencyResource.php:272-295`
> and the edit/view pages. The remaining unhandled path was the default bulk
> delete action at `CurrencyResource.php:300`; it now catches the same database
> failure and sends the existing user-facing error notification. The
> `CurrencyDeleteTest.php` bulk-action regression passed: 1 test, 7 assertions.

Deleting a currency that is still marked active (or still in use) threw an
unhandled exception instead of returning a validation error.

**Why DEKA ERP needs it:** An admin who tries to remove a redundant currency
currently gets a 500. The correct behavior is a user-facing error.

**Benefits:** Graceful error handling for an infrequent but legitimate
administrative action.

**Implementation:** Locate the upstream guard, apply it locally, and add a
test that attempts deletion of an active currency and asserts the error
message.

### UF-024 - Product Name Replaced by Variant ID After Creating Variants

**Decision:** Recommended, P2  
**Upstream reference:** v1.6.0 issue #1187. Commit TBD.  
**Local state:** **Already handled — confirmed 2026-10-22**

> `Product::generateVariants()` at
> `plugins/webkul/products/src/Models/Product.php:194-282` derives each
> variant name from `$this->name` and only writes variant records; it never
> assigns a variant ID to the parent name. `ProductVariantNameTest.php` ran
> generation twice and passed: 1 test, 2 assertions. No product code change
> was necessary.

After creating product variants, the product name field was overwritten with
the variant's internal ID.

**Why DEKA ERP needs it:** Affects the Products plugin used by Logistics
service products and any catalog item with variants.

**Benefits:** Correct product display names in all downstream resources.

**Implementation:** Find the upstream fix, check local `Product` attribute
write path, port the fix, and add a test that creates a variant and asserts
the parent product name is unchanged.

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
**Status:** Implemented locally 2026-09-20; focused verification complete,
remote CI confirmation pending
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

### Package F - Resource Structure Alignment

**Contains:** the Filament resource file layout only (no upstream features or
fixes)
**Dependencies:** Package A, and the Logistics plugin finished and merged
**Risk:** Medium by volume (227 resource classes, 139 of them with inline
schemas), low by nature - the refactor is meant to change no behaviour, and is
verified against that
**Plan:** `docs/upstream-structure-alignment-plan.md`, which lives on the
**`chore/resource-structure`** branch, not here. Read it there before doing any
of this work:

```bash
git show chore/resource-structure:docs/upstream-structure-alignment-plan.md
```

Do not start Package F on `feature/logistics` or `main`. It has its own branch
so a 400-file mechanical refactor never mixes with feature work, and so it can
be reverted as one merge. The scope decision is recorded there: **all 227
resources**, not only the 139 with inline schemas.

Recorded here because it is upstream-driven: upstream v1.6 moved every resource
to `Schemas/`+`Tables/`, and while ours stay monolithic, every future upstream
fix touching a form or table must be re-implemented by hand rather than applied
as a patch. The governing rule is that our files are **restructured**, never
replaced with upstream's - 59 of our resources carry company scoping, 33 carry
custom fields and 14 carry responsive columns that upstream's versions do not.

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

> **Superseded on 2026-10-21, user decision.** The seven-day due date below no
> longer applies. This document was briefly deleted (commit ca1ff8905) to reduce
> scope, then restored because it is the only record of eight open findings,
> including UF-017, an unfixed P0 XSS. It is kept as a **bug register, not a
> schedule**: the calendar trigger is dropped, the event-driven triggers below
> still stand, and no agent owes a review merely because time has passed.

Agents cannot monitor the repository while no session is active. Therefore,
the first active agent after the due date owns the review.

Perform an upstream review when any of these triggers occurs:

- ~~Seven calendar days have passed since `Last reviewed` below.~~ (dropped
  2026-10-21; see the note above)
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
| 2026-10-20 | v1.6.0 release | `d66a7f0e1` | v1.6.0 full changelog review. Eight gaps not captured by the initial failing-test pass added as UF-017 through UF-024: XSS in chatter (P0 security), payment state bug (P1), Livewire nesting depth (P1), saved filter crash (P1), Employee parent_id FK (P1), RFQ/PO currency conversion (P1), active-currency delete error (P2), product variant name bug (P2). Upstream commit SHAs TBD during implementation. | 2026-10-27 or earlier trigger |

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
