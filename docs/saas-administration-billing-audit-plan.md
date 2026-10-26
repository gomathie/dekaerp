# SaaS Administration and Billing Audit / Adoption Plan

Date: 2026-10-26  
Branch inspected: `develop`  
Scope: architecture and implementation plan only; no application behaviour or schema was changed.

## Executive verdict

DEKA ERP already has a useful multi-company administration foundation: company-scoped Eloquent models, a validated company switcher, protected platform roles, company-admin invitations, private tenant storage, API authorization, and per-company plugin grant records. Those foundations should be extended rather than replaced.

The requested SaaS control plane is not complete, however:

- Tenant data isolation is reasonably mature at the application layer, but roles are assigned globally to a user rather than to the user's membership in each company. A user cannot safely be an administrator in one company and an ordinary user in another.
- Plugin grants can be edited per company, but no non-Plugin-Manager production code consumes `PluginAccess`. Revoking a grant therefore does not block a plugin's direct pages, API routes, policies, jobs, or services.
- Every installed plugin is currently granted to every newly created company; there is no configured default set.
- There is no SaaS plan, subscription, billing interval, company user limit, recurring platform invoice, customer credit, deposit, or platform payment-allocation domain.
- Existing Accounts/Accounting/Payments code is a tenant's operational ledger. It offers patterns to reuse, but using its tenant-scoped invoices as DEKA's platform invoices would mix two accounting authorities and expose platform billing to tenant accounting permissions.

The correct delivery order is therefore: close authorization gaps first, then add limits and subscription terms, then billing and money movement, and only then expose the complete administration screens.

## Status and evidence scale

- **Exists**: the requirement is implemented and the inspected paths/tests support the claim.
- **Partial**: useful implementation exists, but a stated path or invariant is absent.
- **Missing**: no production domain implementation was found.
- **Broken**: an implementation presents the capability but does not enforce its central invariant.
- **Strong**: production paths, constraints, and focused regression tests agree.
- **Moderate**: production code and some tests exist, but an important edge or entry point is not pinned.
- **Weak**: code shape exists but enforcement coverage is incomplete.
- **Unverified**: static inspection only or no representative test exists.

## Current architecture

1. `CompanyContext` derives allowed companies from `user_allowed_companies`, intersects session-selected IDs with that allow-list, and rejects unauthorized switches (`plugins/webkul/support/src/Services/CompanyContext.php:36`, `:70`, `:129`).
2. Company-owned models opt into `BelongsToCompany`, which installs `CompanyScope` and stamps the current company on create (`plugins/webkul/support/src/Traits/BelongsToCompany.php:10`). The scope admits active-company rows and intentionally shared `company_id IS NULL` rows (`plugins/webkul/support/src/Models/Scopes/CompanyScope.php:29`).
3. Users may belong to many companies, but the membership table contains only user/company IDs (`plugins/webkul/support/database/migrations/2024_12_10_100944_create_user_allowed_companies_table.php:14`). Spatie roles are installation-wide because `permission.teams` is false (`config/permission.php:126`).
4. Platform roles are Super Admin and Multi-Company Admin; tenant administration is the Company Admin role. Assignment validation and company containment live in `MultiCompanyAdminService`, while creation/editing are wrapped in Filament transactions and audit events.
5. Per-company plugin grants are represented by `company_plugin`; `PluginAccess` can answer grant questions, and platform forms can edit the pivot. The grant is not connected to plugin entry points.
6. The Accounts and Payments plugins implement company-owned invoices, journal lines, payments, reconciliation, and reversals. They use precise database decimals, for example `accounts_account_moves.amount_total decimal(15,4)` and `accounts_account_payments.amount decimal(15,4)` (`plugins/webkul/accounts/database/migrations/2025_02_11_055303_create_accounts_account_moves_table.php:64`, `plugins/webkul/accounts/database/migrations/2025_02_11_055302_create_accounts_account_payments_table.php:40`), but parts of the PHP calculation layer use floats. This is not a suitable money representation for the new platform ledger.
7. There is no recurring SaaS billing scheduler. The only application schedule declaration found is the example `inspire` command (`routes/console.php:6`).

## Requirement evidence matrix

| ID | Requirement | Verdict | Strength | Evidence and gaps | Smallest safe improvement | Validation required |
|---|---|---|---|---|---|---|
| ADM-01 | Strict tenant/company isolation | **Partial** | **Moderate** | `CompanyContext` validates selected IDs; `CompanyScope`, `CompaniesScope`, `AllowedCompanyScope`, policies, private tenant storage, and extensive company-isolation tests exist. `CompanyScope` is intentionally skipped in non-test console execution (`plugins/webkul/support/src/Models/Scopes/CompanyScope.php:14`) and treats null-company rows as shared (`plugins/webkul/support/src/Models/Scopes/CompanyScope.php:34`). No database RLS is relied on; Supabase's Data API is closed. Jobs/commands must therefore establish company context themselves. Sanctum API routes have authentication/ability middleware but no explicit company-context middleware; without a web session `CompanyContext` falls back to the user's default company. | Keep application tenancy. Add a documented company-aware job contract/helper, require an explicit company for tenant writes, and add focused queue/export/API invariants. Define an authorized API company selector or document default-company-only semantics. Tighten nullable `company_id` only model-by-model after classifying genuinely shared rows. | Existing Support and Security isolation suites; job serialization tests; API tests using two companies and a forged company selector; negative storage test; migration audit proving every tenant table is classified shared or owned. |
| ADM-02 | Platform Super Admin, company admin, scoped role assignment | **Partial** | **Strong** for current boundaries; **Weak** for per-company roles | Protected roles, company-contained user queries, assignment validation, deactivation token revocation (`plugins/webkul/security/src/Models/User.php:237`), and audits exist. `CompanyUserIsolationTest` and `MultiCompanyAdminTest` cover direct URL and assignment attacks. However, `User::syncRoles()` assigns roles globally (`plugins/webkul/security/src/Services/UserInvitationService.php:91`) and `permission.teams` is false. A user with two memberships receives the same permissions in both. | Preserve global Super/Multi-Company roles. Add membership-scoped tenant roles on the existing membership row or a companion pivot; resolve tenant permissions against the record/current company. Do not merely enable Spatie teams without proving compatibility with the current multi-active-company UI. | A user admin in A/ordinary in B; direct URL/API denial in B; multi-active list behaviour; role escalation attempts; global platform role regressions. |
| ADM-03 | Company creation creates/invites its administrator | **Partial** | **Strong** for Filament creation | `CreateCompany::afterCreate()` calls `CompanyAdministratorInviter` (`plugins/webkul/security/src/Filament/Resources/CompanyResource/Pages/CreateCompany.php:24`, `:38`). Invitations are signed for seven days (`plugins/webkul/security/src/Models/Invitation.php:42`), revalidate the inviter at acceptance, and are covered by `CompanyAdministratorInvitationTest`. Programmatic/model creation does not invoke the inviter; invitation failure leaves the company valid and reports the reason by design. | Introduce a single onboarding coordinator used by Filament, API, and future imports. Commit the company and seat reservation first, dispatch email after commit, and expose resend/revoke. Preserve the current non-destructive failure behaviour. | UI/API/programmatic onboarding parity; mail failure and retry; duplicate invitation; expired/revoked invitation; administrator is scoped only to the new company. |
| ENT-01 | Per-company plugin entitlements enforced everywhere | **Broken** | **Strong** | `PluginAccess::enabledFor()` and `enabledForCurrent()` exist (`plugins/webkul/plugin-manager/src/Support/PluginAccess.php:34`, `:51`), and company/plugin forms mutate one pivot. Repository search finds `PluginAccess` consumers only inside Plugin Manager and its tests; plugin navigation, policies, API routes, jobs, and services do not enforce it. Logistics independently demonstrates the required policy/service pattern with `LogisticsAccess`. | Make generic entitlement checks a first-class plugin contract. Gate navigation and route entry for UX, policies for record access, and service/job boundaries for non-HTTP execution. Use the record's company for record operations. Add one representative end-to-end denial test per plugin, generated from a shared contract test where possible. | Revoke a real plugin and test hidden navigation, direct page 403, API 403, service exception, queued job no-op/failure, existing data retained, re-enable restores access. |
| ENT-02 | New companies receive only configured default plugins | **Broken** | **Strong** | `CompanyPluginProvisioner::grantInstalledPlugins()` grants every installed plugin (`plugins/webkul/plugin-manager/src/Services/CompanyPluginProvisioner.php:28`), called for every `Company::created` event (`plugins/webkul/plugin-manager/src/PluginManagerServiceProvider.php:58`). The test explicitly expects all installed plugins. | Add an installation-level default marker/set and provision only that set. Preserve the migration's historical backfill for existing companies; do not revoke existing grants during adoption. Validate plugin dependencies when defaults or grants change. | Existing-company grants unchanged; new company gets exact defaults; non-default absent; dependency closure enforced; repeat provisioning idempotent. |
| ENT-03 | Only platform administration can change entitlements; changes auditable | **Partial** | **Moderate** | Company Admin is blocked, but both Super Admin and Multi-Company Admin may change grants (`plugins/webkul/plugin-manager/src/CompanyContributions.php:98`). The write rechecks authorization (`:122`) and flushes request cache. Grant changes are not recorded by `SecurityAuditLogger`. | Decide whether Multi-Company Admin is authorized platform staff. Centralize grant mutation in a transactional service, validate dependency closure, and record before/after plugin IDs and actor. | Forged Filament payload; unauthorized company admin; allowed platform actor; audit record; concurrent updates; cache refresh. |
| SUB-01 | Plans/prices for monthly, quarterly, biannual, annual intervals | **Missing** | **Strong** | No plan, subscription, billing-anchor, interval-price, or next-billing model/migration/service was found. Accounting recurrence enums describe tenant journals, not DEKA SaaS subscriptions. | Add a separate platform-billing domain with plans, interval prices, plan plugins, company subscriptions, and immutable term snapshots. Represent intervals as explicit month counts `1/3/6/12`; store money in integer minor units, never PHP floats. | Unique price per plan/currency/interval; non-negative checks; interval transitions; currency precision; immutable invoice snapshot after plan edits. |
| SUB-02 | Assign company plan/interval and compute predictable next billing date | **Missing** | **Strong** | Companies have identity, currency, status, and contact fields but no subscription or billing-anchor state (`plugins/webkul/support/database/migrations/2024_12_10_092657_create_companies_table.php:14`). | Store subscription status, plan, interval, anchor date/day/time zone, current period, and `next_invoice_at`. Use one calendar-month algorithm with an explicit end-of-month rule and UTC execution. | Anchors on 28–31, leap years, DST boundaries, interval change, pause/resume/cancel, no date drift over repeated periods. |
| LIM-01 | Default company user limit of five, configurable by authorized admins | **Missing** | **Strong** | No user/seat limit field or service exists. User membership is a minimal pivot with no unique pair, status, or timestamps. | Add a company entitlement/limit row (or a backward-compatible extension) with `user_limit` default 5 and a unique membership pair. Expose mutations only to the agreed platform role and audit before/after. | Default/backfill equals 5; lower-limit policy; zero/unlimited semantics; unauthorized writes; audit event. |
| LIM-02 | Enforce active users plus pending invitations on every creation path | **Missing** | **Strong** | Invitations validate only against existing `users.email` (`plugins/webkul/security/src/Services/UserInvitationService.php:29`); surviving rows are pending and acceptance deletes them. No `status`, `expires_at`, reservation, duplicate pending-email constraint, or capacity check exists. Direct create/edit, invitation acceptance, imports, API, reactivation, and membership attachment have no shared capacity guard. | Create one `SeatCapacityService`. Under a transaction, lock the company entitlement row, count distinct active memberships plus valid pending reservations, and reserve/release seats atomically. Call it from every mutation path and recheck on acceptance. Persist invitation lifecycle instead of inferring it from row existence. | Limit-1/limit/limit+1; duplicate pending invite; expired/revoked invite releases seat; acceptance consumes reservation without double counting; reactivation and second-company membership; two concurrent invitations competing for the last seat. |
| BILL-01 | Scheduled, idempotent subscription invoice generation | **Missing** | **Strong** | No SaaS invoice tables/services/jobs or billing schedule exist; `routes/console.php:6` contains only the sample schedule. | Add a daily scheduler that claims due subscriptions in bounded batches. Lock each subscription, derive the period from the anchor, create invoice/header/lines in one transaction, and enforce a unique period key. Advance `next_invoice_at` only with successful invoice creation. | Same job twice; two workers; retry after partial failure; month-end/leap-year anchors; tenant time zones; paused/cancelled subscriptions; operational alert on failed rows. |
| BILL-02 | Manual invoices/payments/deposits, allocation, customer credit, reversals | **Missing** | **Strong** | Tenant Accounts has invoices, payments, partial reconciliation and reversal patterns, but no DEKA-to-company billing ledger. Reusing those rows directly would put platform receivables inside a tenant's company-scoped books. | Add a separate append-only platform receivables ledger. Store invoice/payment amounts in minor units; allocate under row locks; derive invoice balance, unapplied payment, and customer credit from signed entries. Reverse with compensating records linked to the original—never edit/delete posted money movement. | Partial/multi-invoice allocation; over-allocation rejected; cross-company/currency rejected; duplicate request idempotency; reversal and reversal-of-reversal policy; concurrent allocation; audit actor/reason/reference. |
| UI-01 | Platform administration screens | **Partial** | **Moderate** | Filament resources already manage companies/users and company plugin grants. Missing are plan/prices, subscription/anchor, seat usage/limit, platform invoices, payments, allocations, credit, reversals, and consolidated billing/audit visibility. | Extend the existing Company resource with summary/actions and add a dedicated platform Billing cluster. Keep money actions service-backed, authorized, confirmed, reasoned, and idempotent. | Policy tests and direct URLs; form validation; read-only posted records; destructive-action confirmation; pagination/search; audit display; mobile smoke test. |
| UI-02 | Company-admin screens | **Partial** | **Moderate** | Company Admin can manage users within current company boundaries and cannot grant modules. There is no seat meter, invitation lifecycle, subscription/billing view, or platform invoice/payment view. | After membership-scoped roles exist, add tenant-safe user/invite management, seat usage, subscription/entitlement summary, and read-only billing history. Keep plan, limit, entitlement, credit, and reversal mutations platform-only unless explicitly delegated. | Company A cannot enumerate B via list, URL, relation manager, export, search, or API; seat counts match services; billing documents contain only A. |

## Prioritized defects and risk

### P0 — authorization/revenue boundaries

- [ ] **Enforce plugin grants outside Plugin Manager.** Current revocation is cosmetic and can be bypassed by direct URLs, APIs, jobs, or services.
- [ ] **Define and implement company-membership roles.** Global roles overgrant a multi-company user wherever they hold membership.
- [ ] **Define queue/command tenant context.** Production console execution intentionally bypasses Eloquent company scopes, so every tenant job must carry and restore an owning company.

Stop condition: do not treat subscriptions or UI visibility as an access-control boundary until all three have representative negative tests.

### P1 — financial/data-integrity foundations

- [ ] Centralize atomic seat reservation/enforcement before exposing configurable limits.
- [ ] Add explicit invitation lifecycle and uniqueness semantics.
- [ ] Establish the separate platform-billing ledger and integer-money type/value object.
- [ ] Establish idempotent invoice periods and append-only reversal/allocation rules.
- [ ] Audit plugin, plan, limit, subscription, invoice, payment, allocation, and reversal mutations.

Stop condition: do not ship manual money screens until service-level concurrency and idempotency tests pass against PostgreSQL.

### P2 — workflow and usability

- [ ] Configure plugin defaults and dependency-aware grant UX.
- [ ] Add platform billing/plan/subscription screens.
- [ ] Add company-admin seat, invitation, entitlement, and billing views.
- [ ] Add dashboards, alerts, exports, and reconciliation reports after core invariants are stable.

## Functionality to preserve and reuse

- `CompanyContext`, the three company scopes, `BelongsToCompany`, record policies, and existing isolation test helpers.
- Super Admin bypass and the restricted Multi-Company Admin deny-list; global platform roles should remain global.
- `MultiCompanyAdminService` assignment validation and user-query containment.
- Company Admin invitation flow, signed acceptance, revalidation at acceptance, mail-failure recovery, and audit logging.
- `company_plugin` as the effective entitlement pivot and `PluginAccess` as the read API; connect it to enforcement rather than creating a second generic entitlement store.
- Logistics' `LogisticsAccess` policy/service/job coverage as the reference implementation. Reconcile its separate switch with generic plugin entitlement deliberately; do not leave two authoritative flags.
- `SecurityAuditLogger` (`plugins/webkul/security/src/Services/SecurityAuditLogger.php:12`) and Chatter records for privileged administration events. Financial events also need a durable domain ledger; logs alone are not an accounting record.
- Currency identity and decimal-place metadata, `SequenceService`, Filament resource conventions, Laravel transactions, and existing reconciliation/reversal concepts.
- Tenant S3 prefixing and authorized download boundary (`app/Providers/TenantFilesystemServiceProvider.php:52`, `:94`).

Do **not** reuse tenant `accounts_account_moves` as platform invoices, do not use the current float-based currency calculation helpers for new billing amounts, and do not create a parallel company or user model.

## Target domain boundaries

### Administration

- Existing `companies` and `users` remain canonical.
- Evolve `user_allowed_companies` into a real membership boundary: unique `(user_id, company_id)`, lifecycle/status as required, and membership-role associations.
- Keep Super Admin and Multi-Company Admin on the existing global role mechanism. Restrict tenant/business roles to memberships.
- Add a centralized seat-capacity service used by UI, API, invitation, acceptance, import, reactivation, and membership changes.

### Entitlements

- `plugins` remains installation availability.
- `company_plugin` remains effective company entitlement.
- A configured default set determines grants for newly created companies.
- A single mutation service validates authorization/dependencies, changes grants transactionally, flushes access state, and audits the delta.
- Every plugin supplies a stable plugin key and applies the generic guard at navigation, route/policy, service, and asynchronous boundaries.

### Subscriptions

Proposed additive tables/names are illustrative and should follow final plugin naming conventions:

- `billing_plans`: product definition and lifecycle.
- `billing_plan_prices`: `(plan, currency, interval_months)` with non-negative `amount_minor`; unique tuple.
- `billing_plan_plugins`: included plugin set.
- `company_subscriptions`: company, plan, status, interval, billing anchor/time zone, current period, next invoice time, and price/currency snapshots where required.
- `billing_discounts` and immutable tenant redemption records: Super Admin-generated codes with explicit validity and usage constraints. Invoice generation snapshots every applied discount and amount so later edits or deactivation cannot rewrite posted billing history.
- `company_entitlements` or a backward-compatible company extension: operational `user_limit`, default 5. This remains available even while a migrated company has no subscription row.

Whether plugin charges are included-plan features or additive line items is a product decision. The schema should not silently implement both pricing models.

### Platform receivables

- `billing_invoices` and immutable `billing_invoice_lines`, with company/contact/currency/term snapshots and a unique subscription-period idempotency key.
- `billing_payments` for received funds/deposits, with unique idempotency/reference keys and posted/reversed lifecycle.
- `billing_allocations` for signed payment-to-invoice applications.
- An append-only credit/adjustment entry type if credit can exist without a payment. Otherwise unapplied posted payment is customer credit and should not be duplicated in a mutable balance column.
- Reversals are compensating entries referencing originals. Posted monetary records are never edited or deleted.

Use signed 64-bit integer minor units with currency metadata and checked conversion at boundaries. If currencies with more than the chosen minor-unit precision must be supported, decide that before the first migration; do not fall back to binary floating point.

## Multi-agent execution board

This is the claimable implementation board. An agent takes one package, stays
inside its write scope, verifies its acceptance checks, and leaves it at
`review`. The user reviews and commits; only the user or an integration agent
moves a package to `done`.

### How to claim and complete a package

1. Read `AGENTS.md`, `docs/agent-reminders.md`, `docs/running-tests.md`, the
   evidence matrix above, and the package card below.
2. Claim only a package whose dependencies are `done` and whose decision gates
   are answered. Change only its `Status` and `Owner / database` cells.
3. Read any file, but write only files listed under the package's `Owns` field.
   Record any cross-package need in the handoff instead of editing that file.
4. Pin the current behaviour or failure with a test before changing production
   code. Assert positive preconditions so a zero/absent result cannot pass for
   the wrong reason.
5. Give the package its own PostgreSQL database and pass `-e DB_DATABASE=...`
   on every run. Never use the shared `aureuserp_testing` collision trap.
6. Run the package checks and `vendor/bin/pint --dirty --format agent`. Record
   exact commands, database, pass/assertion counts, failures and checks not run.
7. Fill in the package handoff, set status to `review`, and stop. Do not commit,
   merge, rebase, reset, stash, or edit another package.

### Shared-file rules

- Worker agents do not edit `AGENTS.md`, `docs/agent-reminders.md`, root
  `CHANGELOG.md`, or `docs/change-log.md`. `SAAS-14` owns integration docs.
- A worker edits this plan only in its status row and its own handoff block.
- New migrations must be registered in `hasMigrations([...])`; new plugin test
  directories must have a `phpunit.xml` suite.
- No package adds dependencies, uses a managed database, or stores platform
  invoices in tenant `accounts_account_moves`.
- Posted platform money is append-only. Reversals are compensating entries.
- Cards sharing a production file are sequential even if their domain work
  appears independent.

### Decision gates

Record the user's answer before unblocking dependent packages.

| Decision | Question | Recommendation | Answer |
|---|---|---|---|
| D-SAAS-01 | Whole-plan, additive-plugin, or base-plus-add-on pricing; which currencies? | Whole-plan prices per currency/interval first | **Accepted 2026-10-26:** whole-plan pricing with a defined included-plugin set; no per-plugin charging initially. Launch plan prices and platform invoices use USD only, represented as explicit currency-specific prices so additional currencies can be added later without implicit FX conversion. |
| D-SAAS-02 | Can tenant roles differ by company, and how do multi-active mutations work? | Membership roles; privileged mutation requires one explicit company | **Accepted 2026-10-26:** tenant roles are scoped to each company membership and each tenant has its own Company Admins. Super Admin and Multi-Company Admin remain global roles. Super Admin assigns the companies each Multi-Company Admin may manage; privileged mutations target one explicit assigned company. |
| D-SAAS-03 | Who consumes a seat? | Active tenant memberships plus valid invitations; exclude platform support identities | **Accepted 2026-10-26:** each active membership consumes one seat in that company, including Company Admin and one seat per company for shared users. Valid pending invitations reserve seats. Inactive users and expired/revoked invitations do not count. Global Super Admin/Multi-Company Admin support access is excluded unless the user also receives an ordinary tenant membership. |
| D-SAAS-04 | What happens over limit or overdue? | Block new seats, apply explicit grace period, never silently deactivate | **Accepted 2026-10-26:** lowering a limit never deactivates existing users; while over limit, block new invitations, activations and membership attachments until usage is reduced or the limit is raised. Overdue tenants retain existing access during a 10-day grace period but cannot add seats or entitlements; after grace they become read-only without data loss, while Super Admin retains recovery access. Company Admin may activate one audited, non-stackable 10-day self-service extension per overdue invoice. Super Admin may grant a separate manual extension. |
| D-SAAS-05 | Issuer, tax/VAT, numbering, due terms, currency and credit-note rules? | Must be supplied before financial migrations | **Accepted 2026-10-26:** issuer defaults to `DEKA` and is configurable by Super Admin in the administration interface. VAT defaults to 15% and Super Admin may edit or disable it. Posted invoices snapshot issuer identity, applied tax rate and amounts so later configuration changes do not rewrite history. Invoice numbers use `DEKA-INV-{compact year}-{six-digit sequence}` and credit notes use `DEKA-CN-{compact year}-{six-digit sequence}`, where the zero is removed from the four-digit year (`2026 → 226`, `2027 → 227`). Each document type has a separate yearly sequence; posted numbers are unique and never reused. Subscription invoices are Net 7; they become overdue on day 8 and the D-SAAS-04 grace period starts then. Credit notes must reference the original posted invoice, may be full or partial, require a reason, and never rewrite/delete the invoice. Overpayments remain customer credit until allocated or refunded. Super Admin issues credit notes or explicitly delegates/revokes that right for a selected Multi-Company Admin, limited to assigned companies. Currency is USD under D-SAAS-01. |
| D-SAAS-06 | Who may change entitlements, limits and money? | Super Admin only initially | **Accepted 2026-10-26:** plugin entitlements, seat limits, subscription terms, invoices, payments, credits and reversals are Super Admin-only and cannot be delegated to Company Admin. Tenant user/invitation operations and billing visibility may be granted separately. Narrow exception: Super Admin may grant/revoke credit-note issuance for selected Multi-Company Admins, limited to their assigned companies. |
| D-SAAS-07 | How does an API client target one of several allowed companies? | Authorized company header/parameter; default only for compatibility | **Accepted 2026-10-26:** expose distinct platform-admin and tenant/company API namespaces. Company API requests identify the company explicitly and verify membership/token access. Super Admin may query all companies; Multi-Company Admin admin-API lists, searches and record operations return only companies assigned by Super Admin. Keep default-company fallback only on legacy unscoped routes during compatibility migration. |
| D-SAAS-08 | Which discount types, duration, eligibility, redemption limits, tax order and administration rights are required? | Audited Super Admin-generated codes; immutable redemptions and invoice snapshots; no retroactive invoice changes | **Partially accepted 2026-10-26:** add discount-code generation so selected tenants can redeem a reduction against platform billing. Detailed rules are being confirmed one question at a time before the package is unblocked. |

### Status board

`Status`: `todo` · `in progress` · `review` · `done` · `blocked`.

| Package | Title | Depends on | Safe alongside | Status | Owner / database |
|---|---|---|---|---|---|
| SAAS-00 | Baseline inventories and characterization | — | no implementation package | in progress | Codex + weaker-agent 2026-10-26 / `aureuserp_testing_saas_00_codex`, `aureuserp_testing_saas_00_weak` |
| SAAS-01 | Membership-role foundation | SAAS-00, D-SAAS-02, D-SAAS-06 | SAAS-03 | blocked | — |
| SAAS-02 | Tenant-aware jobs and API company selection | SAAS-00, D-SAAS-02, D-SAAS-07 | SAAS-03 | blocked | — |
| SAAS-03 | Plugin defaults, dependencies and audited mutations | SAAS-00, D-SAAS-06 | SAAS-01/02 | blocked | — |
| SAAS-04 | Generic plugin enforcement contract | SAAS-03 | — | todo | — |
| SAAS-05A | Plugin enforcement: finance | SAAS-04 | SAAS-05B/C/D | todo | — |
| SAAS-05B | Plugin enforcement: commercial | SAAS-04 | SAAS-05A/C/D | todo | — |
| SAAS-05C | Plugin enforcement: operations | SAAS-04 | SAAS-05A/B/D | todo | — |
| SAAS-05D | Plugin enforcement: people/work tools | SAAS-04 | SAAS-05A/B/C | todo | — |
| SAAS-06 | Seat limit and invitation lifecycle | SAAS-01, D-SAAS-03, D-SAAS-04 | SAAS-05A/B/C/D | blocked | — |
| SAAS-07 | Onboarding and all seat-enforcement paths | SAAS-06 | — | todo | — |
| SAAS-08 | Platform-billing plugin foundation | SAAS-00, D-SAAS-01, D-SAAS-05 | completed SAAS-05 slices, SAAS-06/07 | blocked | — |
| SAAS-09 | Plans, prices and subscriptions | SAAS-08, D-SAAS-04 | — | todo | — |
| SAAS-09A | Discount codes and tenant redemptions | SAAS-09, D-SAAS-08 | — | blocked | — |
| SAAS-10 | Idempotent recurring invoices | SAAS-09, SAAS-09A | — | todo | — |
| SAAS-11 | Payments, allocations, credit and reversals | SAAS-08, D-SAAS-05, D-SAAS-06 | — | blocked | — |
| SAAS-12 | Platform administration screens | SAAS-03, SAAS-07, SAAS-09/10/11 | — | todo | — |
| SAAS-13 | Company-administration screens | SAAS-01, SAAS-07, SAAS-09/10/11 | — | todo | — |
| SAAS-14 | Backfill, shadow mode and release hardening | all implementation packages | — | todo | — |

`SAAS-00` is the only package ready before decisions are answered. After the
relevant decisions, `SAAS-01` or `SAAS-02` may run alongside `SAAS-03`, but
`SAAS-01` and `SAAS-02` must not run together because both affect company
context and authorization. The four `SAAS-05*` rollout slices are parallel-safe
after their shared contract.

### Test database reservation

Use `aureuserp_testing_saas_<package>` in lowercase and record it in the board:

```bash
docker compose up -d pgsql
docker compose exec -T pgsql psql -U sail -d postgres \
  -c "CREATE DATABASE aureuserp_testing_saas_00 OWNER sail;"
docker compose run --rm --no-deps -e DB_DATABASE=aureuserp_testing_saas_00 \
  laravel.test php artisan test --testsuite=SecurityFeature --compact
```

### Work-package cards

#### SAAS-00 — baseline inventories and characterization

**Owns:** new tests under
`plugins/webkul/security/tests/Feature/SaaSAdministration/` and
`plugins/webkul/plugin-manager/tests/Feature/Entitlements/`; its handoff only.
Production code is read-only.

**Deliver:** inventories of every user/membership creation path, every
non-core plugin's navigation/page/API/policy/service/job entry points, and
tenant jobs/exports; characterize global roles across two companies, a real
plugin revocation bypass, and API default-company behaviour with `path:line`
evidence.

**Accept:** focused SecurityFeature and PluginManagerFeature tests pass; no
production change; inventory identifies the future enforcement hook per path.

#### SAAS-00 handoff — 2026-10-26

- Owner / database: Codex plus weaker-agent / `aureuserp_testing_saas_00_codex`
  and `aureuserp_testing_saas_00_weak`.
- Status: in progress; runtime verification is still running.
- Characterized findings:
  - **Global tenant roles are real here.** Teams are disabled
    (`config/permission.php:126`), the membership pivot has only user/company
    IDs
    (`plugins/webkul/support/database/migrations/2024_12_10_100944_create_user_allowed_companies_table.php:14`),
    and invitation acceptance writes one global role assignment after syncing
    memberships
    (`plugins/webkul/security/src/Services/UserInvitationService.php:90`).
    `GlobalTenantRoleCharacterizationTest.php` proves the same granted
    permission follows one user from company A to company B while an ungranted
    control permission remains denied.
  - **Plugin revocation is not an enforcement boundary.** Routes are loaded
    when a package is globally installed
    (`plugins/webkul/plugin-manager/src/PackageServiceProvider.php:204`), the
    Sales API checks only its policy
    (`plugins/webkul/sales/src/Http/Controllers/API/V1/OrderController.php:72`),
    and that policy checks only the permission
    (`plugins/webkul/sales/src/Policies/OrderPolicy.php:19`).
    `PluginEnforcementCharacterizationTest.php` first proves the Sales grant
    changed from present to absent, then records that the authorized request
    still returns 200. SAAS-04 must invert that final expectation.
  - **API company selection is implicit.** With no session selection,
    `CompanyContext` chooses an allowed default company
    (`plugins/webkul/support/src/Services/CompanyContext.php:78`, `:91`).
    `ApiDefaultCompanyCharacterizationTest.php` pins that fallback for a user
    who belongs to two companies. No production API company selector or
    selector middleware was found.
- User, membership and seat-mutation inventory:

  | Path | Current mutation | Future owner / hook |
  |---|---|---|
  | ERP installer | Creates or updates the default administrator, attaches the default and then all companies, and assigns the global administrator role (`plugins/webkul/plugin-manager/src/Console/Commands/InstallERP.php:255`, `:257`, `:264`, `:267`). | SAAS-06 must explicitly classify the platform installer identity as seat-counted or exempt; SAAS-07 calls the capacity service before any tenant membership attachment. |
  | Filament user create/edit | `UserResource` persists global `roles` and `allowedCompanies` relationships (`plugins/webkul/security/src/Filament/Resources/UserResource.php:148`, `:241`); create/edit validate administration state around Filament relationship persistence (`plugins/webkul/security/src/Filament/Resources/UserResource/Pages/CreateUser.php:27`, `plugins/webkul/security/src/Filament/Resources/UserResource/Pages/EditUser.php:106`). | SAAS-01 replaces tenant role persistence; SAAS-07 guards create, activation and membership changes before save. |
  | Invitation creation/acceptance | `UserInvitationService::create()` stores the pending role/company request; `accept()` creates the user then syncs companies and roles (`plugins/webkul/security/src/Services/UserInvitationService.php:21`, `:65`, `:70`, `:90`). | SAAS-06 reserves/rechecks/releases seats; SAAS-01 resolves tenant roles from memberships; SAAS-07 keeps acceptance idempotent and recoverable. |
  | Company onboarding | Only the Security Filament create page invokes `CompanyAdministratorInviter` after company persistence (`plugins/webkul/security/src/Filament/Resources/CompanyResource/Pages/CreateCompany.php:24`, `:38`). Direct model/factory/programmatic company creation does not create or invite an administrator. | SAAS-07 introduces one onboarding coordinator for UI/API/programmatic paths without moving invitation work into a global model event. |
  | New-company plugin grants | A global `Company::created` listener calls the provisioner (`plugins/webkul/plugin-manager/src/PluginManagerServiceProvider.php:58`), which grants every globally installed plugin (`plugins/webkul/plugin-manager/src/Services/CompanyPluginProvisioner.php:28`, `:40`). | SAAS-03 changes only the grant-selection/mutation service; existing companies retain grants. |

  No production user REST-create endpoint or bulk user importer was found.
  Future import/API paths must use the SAAS-07 coordinator rather than writing
  `users` or `user_allowed_companies` directly.
- Plugin entry-point inventory:
  - Grantable/non-core Filament plugin IDs found: `accounting`, `accounts`,
    `barcode`, `contacts`, `employees`, `inventories`, `invoices`, `logistics`,
    `maintenance`, `manufacturing`, `partners`, `payments`, `products`,
    `projects`, `purchases`, `recruitments`, `sales`, `time-off`, `timesheets`,
    and portal-only `website`. Their `*Plugin.php` registrations are the
    navigation/page discovery boundary; SAAS-04 must provide the shared page
    access/navigation contract before the SAAS-05 slices apply it.
  - Production route files exist for non-core `accounts`, `barcode`,
    `inventories`, `logistics`, `partners`, `products`, `projects`, `purchases`,
    and `sales`. `PackageServiceProvider` applies API throttle/ability
    middleware (`plugins/webkul/plugin-manager/src/PackageServiceProvider.php:217`)
    but no company-plugin middleware. SAAS-04 owns that middleware contract;
    each SAAS-05 slice maps its route files to a stable plugin key.
  - Policies exist throughout every data-bearing business plugin. The
    representative Sales policy combines permissions and record company access
    but not entitlement (`plugins/webkul/sales/src/Policies/OrderPolicy.php:19`,
    `:47`). SAAS-04 should add one reusable policy guard, while record operations
    check the record's company rather than only the current navigation context.
  - Service/event entry points likewise use global installation state. The
    Manufacturing warehouse observer, for example, checks
    `Package::isPluginInstalled('manufacturing')`
    (`plugins/webkul/manufacturing/src/Observers/WarehouseObserver.php:38`).
    SAAS-05 slices must guard externally callable services and integrations at
    the owning record/company boundary.
- Tenant asynchronous/export inventory:
  - The only application `ShouldQueue` class found is the recipient-specific
    Chatter database notification
    (`plugins/webkul/chatter/src/Notifications/ChatterDatabaseNotification.php:11`).
    Two observers defer until commit but are not queued: Inventory company
    provisioning
    (`plugins/webkul/inventories/src/Observers/CompanyObserver.php:9`) and
    Manufacturing warehouse synchronization
    (`plugins/webkul/manufacturing/src/Observers/WarehouseObserver.php:10`).
  - Nine Filament exporters can execute through Filament's export jobs: Accounts
    bill/invoice/payment, Accounting journal entry/item, and Logistics delivery
    performance/shipment profitability/shipment register/trip history. Their
    action boundaries are at
    `plugins/webkul/accounts/src/Filament/Resources/InvoiceResource.php:760`,
    `plugins/webkul/accounting/src/Filament/Clusters/Accounting/Resources/JournalEntryResource.php:410`,
    and `plugins/webkul/logistics/src/Filament/Clusters/Reporting/Pages/ShipmentRegister.php:90`
    (with sibling actions for the remaining exporters). None declares a
    serialized company-context contract in application code.
  - There are no application domain classes under a `Jobs` namespace. Console
    execution deliberately bypasses `CompanyScope` outside tests
    (`plugins/webkul/support/src/Models/Scopes/CompanyScope.php:14`), so SAAS-02
    must require an explicit company payload/context wrapper for every future
    tenant job/command and pin exporter restoration with two-company tests.
  - `FleetImporter` is synchronous and copies Employee/Maintenance records into
    Logistics (`plugins/webkul/logistics/src/Services/FleetImporter.php:19`,
    `:63`); it is a SAAS-05C service-boundary enforcement target, not a user-seat
    import.
- Implemented: added three characterization tests across the two owned test
  directories; no production behavior or schema changed.
- Files changed: `plugins/webkul/security/tests/Feature/SaaSAdministration/GlobalTenantRoleCharacterizationTest.php`,
  `plugins/webkul/security/tests/Feature/SaaSAdministration/ApiDefaultCompanyCharacterizationTest.php`,
  `plugins/webkul/plugin-manager/tests/Feature/Entitlements/PluginEnforcementCharacterizationTest.php`,
  and this handoff.
- Tests run with pass/assertion counts: pending active Sail runs; all three new
  files are already covered by the existing `SecurityFeature` and
  `PluginManagerFeature` directory suites in `phpunit.xml`.
- Reviewed but not executed: full SecurityFeature/PluginManagerFeature suites,
  browser navigation, queued Filament export execution, and concurrent context
  restoration.
- Failures investigated: none yet; the first isolated run is still in the known
  slow ERP/Shield bootstrap.
- Security/tenant/concurrency review: tests use two real company memberships and
  positive/negative permission controls. The plugin test proves both grant
  presence and revocation before exercising the bypass. No managed database or
  production environment is involved.
- Risks and rollback: tests intentionally describe insecure/incomplete current
  behavior and must be inverted by SAAS-01/02/04, not preserved through
  compatibility code. Rollback is deletion of the new tests/handoff only.
- Cross-package requests: SAAS-01 owns membership roles; SAAS-02 owns explicit
  API/job context; SAAS-03 owns default/dependency-aware grants and audited
  mutations; SAAS-04 owns the shared enforcement contract; SAAS-05A-D own
  plugin-by-plugin adoption; SAAS-06/07 own capacity and every mutation path
  listed above.
- Suggested next package: answer D-SAAS-02/D-SAAS-06/D-SAAS-07, then run SAAS-01
  or SAAS-02 alongside SAAS-03 as allowed by the board.

#### SAAS-01 — membership-role foundation

**Owns:** membership migrations/models/relations in Support/Security;
membership permission resolver; affected Security policies, assignment
services, UserResource pages and focused tests. List exact files when claiming.

**Deliver:** unique membership pair with safe duplicate preflight;
membership-role storage; global Super/Multi-Company roles preserved; tenant
roles resolved against record/current company; unified create/edit/invitation
assignment; regression fix for the CreateUser/EditUser Company Admin mismatch.

**Accept:** one user is admin in A and ordinary in B across list, direct policy,
Filament and API; existing Super Admin, MultiCompanyAdminTest,
CompanyUserIsolationTest and invitation tests pass.

#### SAAS-02 — tenant-aware jobs and API selection

**Owns:** new Support company-context execution helper/middleware, API company
selection middleware/config and focused Support/API tests. Existing jobs outside
Support require a recorded ownership request.

**Deliver:** `try/finally` context restoration; fail-closed tenant writes with
no company; authorized API selector; documented compatibility fallback; one
representative serialized job/export.

**Accept:** assigned company works; forged company is 403; no-context tenant
write fails; prior context returns after success/exception; relevant Support
and API tests pass.

#### SAAS-03 — plugin defaults and audited mutations

**Owns:** Plugin Manager services/models/provider/form actions, Plugin Manager
migrations and tests. Other plugin directories are read-only.

**Deliver:** configured defaults; historical grants preserved; one
transactional mutation service; dependency closure/revocation rules;
authorization per D-SAAS-06; before/after audit; cache flush.

**Accept:** existing companies unchanged; new company receives exact defaults;
dependency, forged-write, audit and idempotency tests pass.

#### SAAS-04 — generic plugin enforcement contract

**Owns:** Plugin Manager middleware/contracts/concerns/exceptions, provider
integration and contract tests. Other plugin directories are read-only.

**Deliver:** stable plugin key; navigation, route, current-company,
record-company and service/job guards; explicit core/non-entitleable manifest;
fail-closed no-company semantics.

**Accept:** a fixture proves hidden navigation, direct route denial, record
denial and service/job denial; re-enabling restores access. Do not bulk-edit
real plugins in this package.

#### SAAS-05A/B/C/D — plugin rollout slices

Each slice applies SAAS-04 without inventing another guard:

- **05A:** `accounts`, `accounting`, `invoices`, `payments`.
- **05B:** `contacts`, `partners`, `products`, `sales`, `purchases`.
- **05C:** `inventories`, `manufacturing`, `maintenance`, `logistics`,
  `barcode`; reconcile `LogisticsAccess` to one authoritative entitlement.
- **05D:** `employees`, `recruitments`, `projects`, `timesheets`, `time-off`,
  `table-views`, `fields`, `analytics`, `full-calendar`, and `website` only
  when SAAS-04 marks them entitleable.

**Owns:** only listed plugin directories and slice tests. No slice edits Plugin
Manager, Support, Security, `phpunit.xml`, or another slice.

**Accept per plugin:** assert installed and granted; revoke; navigation hides;
direct page/API and one write service deny; data remains; restore access. Run
and report each touched plugin suite separately.

#### SAAS-06 — seat limit and invitation lifecycle

**Owns:** Support/Security entitlement and invitation-lifecycle migrations;
`SeatCapacityService`; invitation models/services and focused tests. Broad UI
and entry-point wiring belong to SAAS-07.

**Deliver:** default/backfill 5; approved unlimited semantics; explicit pending,
expired, revoked and accepted invitation state; duplicate-pending protection;
locked active-membership plus reservation count; audited limit change.

**Accept:** boundary cases, release-on-expiry/revoke, acceptance without double
counting, and two-connection last-seat race pass; migration does not deactivate
an existing over-limit company.

#### SAAS-07 — onboarding and all seat-enforcement paths

**Owns:** onboarding coordinator; Security company/user Filament pages;
invitation acceptance; import/API/services identified by SAAS-00; focused tests.

**Deliver:** UI/API/programmatic company onboarding; capacity service called by
create, invite, accept, activation, membership attach and imports; after-commit
email; resend/revoke; one consistent capacity error.

**Accept:** one test per inventory path; mail failure remains recoverable;
company remains durable; no path exceeds capacity; existing administrator
invitation and user-isolation suites pass.

#### SAAS-08 — platform-billing foundation

**Owns:** new `plugins/webkul/platform-billing/`, `bootstrap/providers.php`, its
`phpunit.xml` suite, and only its foundation migrations/models/enums/value
objects/tests. This package fixes the final plugin name and namespace.

**Deliver:** registered provider/plugin/test suite; integer-minor-unit Money;
table-name and FK/index conventions; policies denying tenant writes; migration
registration/structure tests. Do not alter tenant Accounts tables.

**Accept:** empty and production-shaped installs; Money rejects floats,
currency mismatch and overflow; migration guard and PlatformBillingFeature pass.

#### SAAS-09 — plans, prices and subscriptions

**Owns:** Platform Billing plan/price/subscription migrations, models, services
and tests; no screens or invoice scheduler.

**Deliver:** 1/3/6/12-month prices; included plugins; subscription status,
anchor/time zone/period/next invoice; transitions and audits; immutable terms;
approved synchronization to effective plugin and seat entitlements.

**Accept:** uniqueness/check constraints; month-end, leap-year and DST data
sets; no period drift; invalid transitions fail; plan edits preserve snapshots.

#### SAAS-09A — discount codes and tenant redemptions

**Owns:** Platform Billing discount/redemption migrations, models, services and
tests; no invoice scheduler or screens outside the minimal domain interface.

**Deliver:** Super Admin-generated redeemable codes; explicit eligibility,
validity, usage and duration rules from D-SAAS-08; atomic redemption; auditable
activation/deactivation; deterministic discount calculation exposed to SAAS-10.
Posted invoice discount snapshots remain immutable when a code later changes.

**Accept:** forged, expired, inactive, ineligible, exhausted and duplicate
redemptions fail; concurrent final-use redemption cannot exceed its limit;
cross-company use is denied; amount bounds and rounding are exact integer-money
calculations; changing or disabling a code does not alter posted invoices.

#### SAAS-10 — recurring invoices

**Owns:** Platform Billing invoice migrations/models/services/jobs/commands,
scheduler registration and tests; no payment/allocation files or screens.

**Deliver:** immutable invoice/lines/legal snapshots; unique period key;
locked generator; bounded due-job; retries/telemetry; dry-run; advance next date
only with successful invoice.

**Accept:** duplicate call/retry/two workers produce one invoice; failure
advances nothing; paused/cancelled skip; dates match SAAS-09.

#### SAAS-11 — payments, allocations, credit and reversals

**Owns:** Platform Billing payment/allocation/adjustment/reversal migrations,
models/services/commands/tests; SAAS-10 invoice generation files are read-only.

**Deliver:** posted deposits/payments; signed allocations; derived balance and
unapplied credit; idempotency; deterministic locks; cross-company/currency
rejection; compensating reversals; actor/date/reference/reason audit fields.

**Accept:** partial/multi-invoice allocation, over-allocation rejection,
concurrent no-overspend, duplicate request safety, reversal net-zero, immutable
posted rows and zero-drift reconciliation all pass.

#### SAAS-12 — platform administration screens

**Owns:** Platform Billing Filament cluster/resources/pages/translations and
explicit company-resource contributions. Domain rules remain in prior services.

**Deliver:** plan/prices, subscription/anchor, seat limit/usage, entitlement,
invoice, payment/deposit, allocation, credit, reversal and audit screens.

**Accept:** policies/direct URLs/forged payloads; confirmation and reason for
money actions; posted records read-only; pagination, empty/error states,
Filament tests and browser smoke path.

#### SAAS-13 — company-administration screens

**Owns:** Security tenant user/invitation/seat UI and Platform Billing
tenant-facing read resources/translations. Platform mutation services are
read-only.

**Deliver:** users/roles/invitations, seat usage, subscription/entitlement
summary, and read-only invoices/payments/allocations/credit.

**Accept:** Company A cannot enumerate B via list, URL, relation, search,
export or API; membership roles apply; displayed balances match services;
platform mutations remain server-side denied.

#### SAAS-14 — backfill, shadow mode and release hardening

**Owns:** bounded backfill/dry-run commands, rollout flags, reconciliation and
health checks, deployment docs, change logs and cross-package verification.

**Deliver:** duplicate-membership repair; limit/default/subscription backfills;
shadow seat/entitlement/invoice calculations; metrics/alerts; security review;
production-copy migration rehearsal; non-destructive rollback.

**Accept:** affected suites/migration guards pass; rehearsal records runtime and
locks; reconciliation is clean; flags are independently reversible; no
compatibility read is removed before one billing cycle and approval.

### Handoff template

Append this under the claimed card:

```markdown
#### SAAS-XX handoff — YYYY-MM-DD

- Owner / database:
- Status: review | blocked
- Implemented:
- Files changed:
- Tests run with pass/assertion counts:
- Reviewed but not executed:
- Failures investigated:
- Security/tenant/concurrency review:
- Risks and rollback:
- Cross-package requests:
- Suggested next package:
```

### Copy-ready agent assignment prompt

Replace `SAAS-XX` and paste this with the repository context:

```text
You are assigned work package SAAS-XX in
docs/saas-administration-billing-audit-plan.md.

Read AGENTS.md, docs/agent-reminders.md, docs/running-tests.md, the audit
plan's evidence matrix, multi-agent rules, status board, and the complete
SAAS-XX card before changing anything.

Confirm all dependencies are marked done and all required D-SAAS decisions
are answered. If they are not, do not implement: report the exact blocker.
Claim only SAAS-XX in the status board and reserve the database
aureuserp_testing_saas_xx. Read any file, but edit only the paths owned by
SAAS-XX. Record cross-package needs instead of editing another package's files.

Inspect and trace first, state the smallest implementation plan and exact
expected files, then implement. Add a regression/characterization test with
positive preconditions. Register every migration and test suite. Run every
test with -e DB_DATABASE=aureuserp_testing_saas_xx, run Pint, review the final
diff, complete the package handoff template with exact counts and failures,
set SAAS-XX to review, and stop. Do not commit or alter git history.
```

## Phase checklist and exit gates

### Phase 0 — decisions and executable invariants

- [ ] Answer the blocking questions at the end of this document.
- [ ] Write architecture decision records in this document or the eventual implementation PR for membership roles, plugin pricing, money representation, and platform-ledger separation.
- [ ] Inventory every user-creation/membership path, plugin entry point, and tenant queue/job. Turn each inventory into a test matrix before changing schema.
- [ ] Add characterization tests for current company creation, role assignment, invitation, plugin grant mutation, and cross-company API access.

Exit: agreed invariants and red tests demonstrate the known broken/missing behaviour without weakening existing tests.

### Phase 1 — membership authorization and tenant execution context

- [ ] Add unique membership constraints and safe duplicate cleanup/reporting.
- [ ] Add membership-role storage while preserving global platform roles.
- [ ] Introduce a company-aware permission resolver used by policies and admin services.
- [ ] Make ambiguous multi-active-company mutations fail closed or resolve to one explicit company according to the product decision.
- [ ] Create a reusable company-aware job/context wrapper with `try/finally` restoration.
- [ ] Cover APIs, exports, imports, global search, relation managers, notifications, and queued jobs.

Exit: admin-in-A/user-in-B tests pass across UI, policy, API, and service paths; platform roles retain current access.

### Phase 2 — enforce plugin entitlements

- [ ] Add configured default plugins and dependency validation.
- [ ] Replace grant writes with one authorized, audited service.
- [ ] Add generic plugin route/policy/service/job guards and navigation visibility.
- [ ] Adapt Logistics to one authoritative entitlement decision while preserving its provisioning/settings semantics.
- [ ] Keep existing companies' grants unchanged; only new-company defaults change.

Exit: revoking a representative plugin prevents every entry path without deleting data; re-enabling restores access.

### Phase 3 — atomic user limits and invitation lifecycle

- [ ] Add `user_limit` with a default/backfill of 5 and explicit unlimited semantics if required.
- [ ] Add invitation status, expiry, revocation, acceptance metadata, and duplicate-pending protection.
- [ ] Implement the locked seat reservation/count service.
- [ ] Route direct create, invitation create/accept, imports, API, activation, and membership attach through it.
- [ ] Add platform seat-limit editing and company-admin seat usage/invite management.

Exit: all creation paths return the same capacity error; concurrent last-seat tests cannot exceed the limit.

### Phase 4 — plans and subscriptions

- [ ] Add plan, price, included-plugin, and subscription schema with checks/unique indexes/FKs.
- [ ] Implement interval and anchor value objects; use one tested next-date algorithm.
- [ ] Add assignment/change/pause/resume/cancel services with audit events and immutable term snapshots.
- [ ] Decide and implement the relationship between subscription terms and effective `company_plugin`/`user_limit` values.
- [ ] Add platform plan and company-subscription screens.

Exit: interval/date/currency/transition tests pass, and changing a plan cannot rewrite historical invoices.

### Phase 5 — scheduled invoices

- [ ] Add platform invoice and immutable line models with unique period key.
- [ ] Build one invoice-generation service callable manually and by a job.
- [ ] Add a daily scheduler, bounded due-subscription batches, row locks, retry policy, and failure telemetry.
- [ ] Add document numbering, tax treatment, due-date terms, and delivery only after legal/product decisions are made.
- [ ] Add dry-run/backfill reporting for migrated companies before enabling generation.

Exit: retries and concurrent workers produce exactly one invoice per due period and advance the subscription exactly once.

### Phase 6 — manual payments, deposits, credit, allocation, reversal

- [ ] Add append-only payment/allocation/adjustment/reversal schema with local check constraints and idempotency keys.
- [ ] Implement posting, allocation, unallocation/reversal, and derived-balance services under deterministic row locks.
- [ ] Require actor, effective date, reference, reason, company, and currency on privileged money actions.
- [ ] Add platform screens with confirmation and read-only posted records.
- [ ] Add company read-only invoices, receipts/payments, allocations, and credit views.

Exit: accounting-equation/invariant tests, concurrency tests, and audit reconciliation reports pass; posted records cannot be destructively edited.

### Phase 7 — rollout and hardening

- [ ] Backfill memberships, limits, defaults, and subscriptions in bounded idempotent commands with dry-run output.
- [ ] Run shadow seat/entitlement/billing calculations and compare with intended state before enforcement.
- [ ] Enable enforcement by capability/tenant cohort; monitor denials, duplicate-key conflicts, invoice failures, and ledger reconciliation.
- [ ] Remove compatibility reads only after at least one successful billing cycle and explicit approval.

Exit: production reconciliation is clean, alerts are actionable, and rollback has been rehearsed without deleting new ledger data.

## Migration and tenant-isolation strategy

1. Use additive migrations first. Do not rename/drop current membership, role, grant, or accounting structures in the same release that introduces replacements.
2. Before adding the membership unique key, report and deterministically merge duplicate `(user_id, company_id)` rows. Add indexes for both FK directions; the current pivot has neither a pair uniqueness constraint nor timestamps/status.
3. Backfill all existing companies to `user_limit = 5` without enforcing until counts and exceptions are reviewed. Do not silently deactivate users when a company is already over five.
4. Preserve every current `company_plugin` row. The default-plugin change applies to companies created after activation; historical cleanup is a separate explicit operation.
5. Backfill subscription state as draft/migration-pending unless commercial terms are known. Never infer paid plan/prices from installed plugins alone.
6. Every new tenant-owned table must have a non-null company FK unless explicitly classified shared. Index company FKs and common `(company_id, status/date)` access patterns.
7. Keep platform billing tables outside normal tenant accounting scopes but require explicit `company_id` filtering in their policies/services. Platform users may query all; company users receive company-scoped read-only queries.
8. Production is PostgreSQL. Use database constraints for row-local invariants and uniqueness, transactions plus deterministic locks for cross-row money/seat invariants, and avoid long transactions or network/email calls while locks are held.
9. Do not expose billing tables through Supabase's Data API. Continue the server-only application boundary; database RLS can be evaluated later as defense in depth, not assumed as current protection.
10. Rollback means disable new writes/enforcement and return UI to compatibility reads. Do not down-migrate or delete posted financial records in production.

## Test strategy

### Characterization and security

- Preserve and extend `CompanyIsolationTest`, `CompanyScopingInvariantsTest`, `CompanyUserIsolationTest`, `MultiCompanyAdminTest`, and `CompanyAdministratorInvitationTest`.
- Add contract tests that enumerate tenant models and plugin entry points, so a new model/page/API/job cannot omit company or entitlement enforcement silently.
- Test list queries and direct object references separately; hidden navigation is never proof of authorization.
- Exercise web, Sanctum API, exports/imports, global search, signed links, storage downloads, and queued work with two companies.

### Capacity and concurrency

- Run PostgreSQL integration tests with two independent database connections/processes competing for the last seat.
- Assert the precondition (exactly one seat remains) before racing, then assert active memberships plus valid reservations never exceed the limit.
- Repeat the pattern for duplicate invoice periods and concurrent payment allocations.

### Dates and money

- Data-set interval tests for 1/3/6/12 months, dates 28–31, February/leap years, DST changes, and repeated periods to detect drift.
- Integer-money tests for zero, maximum supported amount, currency minor units, split allocation, rounding boundaries, and sum-of-lines equality.
- Property/invariant tests: invoice balance equals charges minus signed allocations/adjustments; payment unapplied equals posted payment minus signed allocations; reversal plus original nets to zero.

### UI and operations

- Filament policy and direct-route tests for platform and company roles.
- Browser smoke tests for plan assignment, seat usage, invoice generation, payment allocation, reversal confirmation, and tenant read-only views.
- Migration tests on an anonymized production-shaped PostgreSQL clone: runtime, locks, duplicate cleanup, over-limit report, and rollback/feature-disable path.
- Run plugin PHPUnit suites serially against a dedicated database, then Pint and the broader affected suite set. Never run concurrent `migrate:fresh` processes on one database.

## Operational controls

- Metrics: entitlement denials by plugin/company/path, seat-capacity denials, pending invitation age, due subscriptions, invoices generated/skipped/failed, duplicate-period conflicts, unapplied credit, allocation failures, and unreconciled reversal chains.
- Alerts: scheduler has not run, oldest due subscription exceeds threshold, invoice generation repeatedly fails, ledger invariant mismatch, or tenant job executes without company context.
- Structured logs must include actor ID, company ID, subscription/invoice/payment IDs, idempotency key, and correlation ID—never passwords, signed invitation URLs, tokens, or unnecessary customer data.
- Reconciliation command/report: recompute invoice balances and company credit from immutable entries and compare any cached/materialized values.
- Feature flags: membership-role enforcement, plugin enforcement, seat enforcement, invoice scheduling, and manual money screens should be separately reversible.

## Known related defects discovered during audit

- `CreateUser` sets `resource_permission = GLOBAL` for both Multi-Company Admin and Company Admin, but `EditUser` does so only for Multi-Company Admin (`plugins/webkul/security/src/Filament/Resources/UserResource/Pages/CreateUser.php:40`, `plugins/webkul/security/src/Filament/Resources/UserResource/Pages/EditUser.php:110`). Promoting an existing restricted user to Company Admin may therefore behave differently from creating/inviting one. Confirm with a regression test before changing it.
- `user_allowed_companies` has no unique `(user_id, company_id)` constraint. Eloquent `sync` commonly avoids duplicates, but the database does not protect imports, raw writes, or races.
- Invitation uniqueness covers existing users only, not multiple pending invitations for the same company/email, and invitation expiry is encoded only in each generated signed URL.
- Plugin grant changes are not recorded through `SecurityAuditLogger`.
- Multi-company API semantics are implicit: API routes authenticate Sanctum tokens but do not select a company, while `CompanyContext` normally reads the web session and otherwise falls back to the user's default company. This is fail-narrow rather than a demonstrated cross-tenant leak, but integrations cannot deliberately and audibly target another allowed company.

These are in scope for the relevant phases; no fixes were made during this audit.

## Blocking product/security questions

1. Are tenant prices whole-plan prices, additive per-plugin prices, or a base plan plus plugin add-ons? Are multiple billing currencies required at launch?
2. Must a user's roles differ by company? If yes, should multi-company list views aggregate records with different permissions, or should privileged work require one selected current company?
3. Who counts toward the user limit: all active company memberships, Company Admin, invited users, DEKA staff assigned for support, API/service accounts, and users shared across several companies?
4. What happens when a company is already over a newly lowered limit, or when a subscription is overdue—block only new seats, suspend login, disable plugins, or allow a grace period?
5. What legal invoice rules apply: issuing entity, tax/VAT, numbering, currency, due terms, credit notes, and required immutable address/tax snapshots?
6. Is Multi-Company Admin authorized to change subscription, limits, plugin entitlements, payments, and reversals, or are those operations Super Admin only? Should reversals require four-eyes approval?

Until these are answered, Phase 0–2 security work can proceed, but schema/API commitments for prices, seat counting, and financial documents should not.
