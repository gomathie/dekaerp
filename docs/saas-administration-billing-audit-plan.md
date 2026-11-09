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
| D-SAAS-02 | Can tenant roles differ by company, and how do multi-active mutations work? | Membership roles; privileged mutation requires one explicit company | **Accepted 2026-10-26:** tenant roles are scoped to each company membership and each tenant has its own Company Admins. Super Admin and Multi-Company Admin remain global roles. Super Admin assigns the companies each Multi-Company Admin may manage; privileged mutations target one explicit assigned company. **Addendum 2026-10-28 (resolution semantics):** tenant permissions are evaluated **record-scoped** — against each record's own `company_id` — never against the session's current company, because `CompanyContext::currentId()` is "first active company, else default" and is a display convention. `hasPermissionTo()` with no company answers only the navigation question, using the session's active companies; policies and services that know a record must call `hasPermissionInCompany()`. List queries still admit every active company (`CompanyScope`) and need the resolver applied per resource — recorded as a cross-package request from SAAS-01. |
| D-SAAS-03 | Who consumes a seat? | Active tenant memberships plus valid invitations; exclude platform support identities | **Accepted 2026-10-26:** each active membership consumes one seat in that company, including Company Admin and one seat per company for shared users. Valid pending invitations reserve seats. Inactive users and expired/revoked invitations do not count. Global Super Admin/Multi-Company Admin support access is excluded unless the user also receives an ordinary tenant membership. |
| D-SAAS-04 | What happens over limit or overdue? | Block new seats, apply explicit grace period, never silently deactivate | **Accepted 2026-10-26:** lowering a limit never deactivates existing users; while over limit, block new invitations, activations and membership attachments until usage is reduced or the limit is raised. Overdue tenants retain existing access during a 10-day grace period but cannot add seats or entitlements; after grace they become read-only without data loss, while Super Admin retains recovery access. Company Admin may activate one audited, non-stackable 10-day self-service extension per overdue invoice. Super Admin may grant a separate manual extension. |
| D-SAAS-05 | Issuer, tax/VAT, numbering, due terms, currency and credit-note rules? | Must be supplied before financial migrations | **Accepted 2026-10-26:** issuer defaults to `DEKA` and is configurable by Super Admin in the administration interface. VAT defaults to 15% and Super Admin may edit or disable it. Posted invoices snapshot issuer identity, applied tax rate and amounts so later configuration changes do not rewrite history. Invoice numbers use `DEKA-INV-{compact year}-{six-digit sequence}` and credit notes use `DEKA-CN-{compact year}-{six-digit sequence}`, where the zero is removed from the four-digit year (`2026 → 226`, `2027 → 227`). Each document type has a separate yearly sequence; posted numbers are unique and never reused. Subscription invoices are Net 7; they become overdue on day 8 and the D-SAAS-04 grace period starts then. Credit notes must reference the original posted invoice, may be full or partial, require a reason, and never rewrite/delete the invoice. Overpayments remain customer credit until allocated or refunded. Super Admin issues credit notes or explicitly delegates/revokes that right for a selected Multi-Company Admin, limited to assigned companies. Currency is USD under D-SAAS-01. |
| D-SAAS-06 | Who may change entitlements, limits and money? | Super Admin only initially | **Accepted 2026-10-26:** plugin entitlements, seat limits, subscription terms, invoices, payments, credits and reversals are Super Admin-only and cannot be delegated to Company Admin. Tenant user/invitation operations and billing visibility may be granted separately. Narrow exception: Super Admin may grant/revoke credit-note issuance for selected Multi-Company Admins, limited to their assigned companies. *Second narrow exception, 2026-11-07 (user, D-SAAS-08):* a Multi-Company Admin may create discount codes for their assigned companies only. |
| D-SAAS-07 | How does an API client target one of several allowed companies? | Authorized company header/parameter; default only for compatibility | **Accepted 2026-10-26:** expose distinct platform-admin and tenant/company API namespaces. Company API requests identify the company explicitly and verify membership/token access. Super Admin may query all companies; Multi-Company Admin admin-API lists, searches and record operations return only companies assigned by Super Admin. Keep default-company fallback only on legacy unscoped routes during compatibility migration. |
| D-SAAS-08 | Which discount types, duration, eligibility, redemption limits, tax order and administration rights are required? | Audited eligible codes; immutable redemptions and invoice snapshots; no retroactive invoice changes | **Accepted 2026-11-07 (user):** percentage off, fixed USD amount off, or free months; per-code duration of one invoice, N months, or forever; eligibility for any company or a named company list; discount before VAT; optional total redemption limit, always at most once per company; optional redemption end date that does not stop an existing redemption; one active discount per company, with no stacking. Super Admin may create codes; Multi-Company Admin may create them only for assigned companies, and those codes can never reach another company. A company's own admin, Super Admin, or an MCA acting for that company may redeem. Redemptions are immutable and snapshotted on invoices, never retroactive. |
| D-SAAS-09 | Subscription activation: plan changes, modules, seats and non-payment | Answers the SAAS-09 handoff's open questions | **Accepted 2026-11-07 (user).** **Plan and price changes** take effect at the next billing period. The current period finishes at the old price, with no mid-period proration. **Modules:** starting or changing a plan adds the plan's modules and their dependencies. Modules granted by hand stay, and nothing is revoked automatically. **Seats:** each plan carries a seat limit. Starting or changing the plan sets the company's limit through `SeatCapacityService::setLimit()`, and Super Admin can still override it per company. **Non-payment:** after the D-SAAS-04 grace period the company becomes read-only. Its users can sign in and see data but cannot change anything until it is paid; installation staff are unaffected. **One active subscription per company**, enforced in the database; drafts may sit alongside it, and a plan change replaces the active one at the next period. **Cancellation** stops renewal: the company keeps access until the paid period ends, then becomes read-only. Super Admin may reactivate. There is no pause state for now. |
| D-SAAS-10 | How are platform payments, reversals and credit recorded? | Manual Super Admin entry; one money-in row; derived credit | **Accepted 2026-11-09 (user).** A Super Admin enters a payment by hand. There is no payment provider. One Super Admin posts a reversal, with actor, date, reference and reason, and no second approver. A Multi-Company Admin's credit-note grant does not cover payment reversals. The Super Admin supplies a reference, stored as the idempotency key, unique on `(company_id, idempotency_key)`; a retry returns the existing posting. There is one posted money-in row. Its unallocated part is the company's credit. A refund of that credit is its own immutable row referencing the payment, and it moves no money to a bank. SAAS-08 counts as satisfied. SAAS-10 is a dependency of SAAS-11. |
| D-SAAS-11 | What is a test account, and who chooses it? | Installation staff choose at creation; trial then commercial only | **Accepted 2026-11-09 (user).** The type is `test` or `commercial`, chosen only by installation staff who may create the company, inside `CompanyOnboarding::create()`. A tenant Company Admin is not offered the choice. When the test days end, the company is read-only and its data stays; staff can add days or switch the type. A test company is not charged and gets no platform invoice. Switching to commercial picks a plan and price. Billing starts at the next period, with no back-billing of the trial. There is no switch from commercial back to test. Existing companies are backfilled as `commercial`. The columns are `account_type` and `test_ends_at` on the company. Adding days is an audited update by a Super Admin, or by a Multi-Company Admin for an assigned company. Impersonation is a separate slice. |
| D-SAAS-12 | May the read-only guard block platform billing's own writes? | Exempt only the billing records the invoice job must save | **Accepted 2026-11-09 (user).** Only platform billing's own subscription and invoice writes pass `CompanyReadOnlyWriteGuard`. Claude is implementing that exemption for `Subscription`, `Invoice` and `InvoiceLine`. |
| D-SAAS-13 | When do the D-SAAS-04 grace extensions exist? | A package of their own, after payments and account type | **Accepted 2026-11-09 (user).** Company Admin and Super Admin extensions are a separate small package, SAAS-10B, done next after this one. They change when `isReadOnly()` becomes true. They are not part of SAAS-11 or the account-type work. |

### Status board

`Status`: `todo` · `in progress` · `review` · `done` · `blocked`.

| Package | Title | Depends on | Safe alongside | Status | Owner / database |
|---|---|---|---|---|---|
| SAAS-00 | Baseline inventories and characterization | — | no implementation package | done | Verified 2026-10-27: 12 tests, 68 assertions, 0 failures / `aureuserp_testing_saas_00_verify` |
| SAAS-01 | Membership-role foundation | SAAS-00, D-SAAS-02, D-SAAS-06 | SAAS-03 | review | Copilot 2026-10-28; Claude review 2026-10-29 / `aureuserp_testing_saas_review`; all 17 suites green on `7bbf65741`, deployed in production batch 28 and verified 2026-11-03 |
| SAAS-02 | Tenant-aware jobs and API company selection | SAAS-00, D-SAAS-02, D-SAAS-07 | SAAS-03 | review | Copilot 2026-10-28; Claude review 2026-10-29 / `aureuserp_testing_saas_review`; all 17 suites green on `7bbf65741`, deployed in production batch 28 and verified 2026-11-03 |
| SAAS-03 | Plugin defaults, dependencies and audited mutations | SAAS-00, D-SAAS-06 | SAAS-01/02 | review | Copilot 2026-10-28; Claude review 2026-10-29 / `aureuserp_testing_saas_review`; all 17 suites green on `7bbf65741`, deployed in production batch 28 and verified 2026-11-03 |
| SAAS-04 | Generic plugin enforcement contract | SAAS-03 | — | review | Copilot 2026-10-28; Claude review 2026-10-29 / `aureuserp_testing_saas_review`; all 17 suites green on `7bbf65741`, deployed in production batch 28 and verified 2026-11-03 |
| SAAS-05A | Plugin enforcement: finance | SAAS-04 | SAAS-05B/C/D | review (verified 2026-11-05/06: every suite run; review findings 1–4 resolved. See "Suite runs" and "Remaining suites") | Codex 2026-11-05 / isolated Sail databases `aureuserp_testing_verify_*` |
| SAAS-05B | Plugin enforcement: commercial | SAAS-04 | SAAS-05A/C/D | review (verified 2026-11-05/06: every suite run; review findings 1–4 resolved. See "Suite runs" and "Remaining suites") | Codex 2026-11-05 / isolated Sail database `aureuserp_testing_verify_product` |
| SAAS-05C | Plugin enforcement: operations | SAAS-04 | SAAS-05A/B/D | review (verified 2026-11-05/06: every suite run; review findings 1–4 resolved. See "Suite runs" and "Remaining suites") | Codex 2026-11-05 / isolated Sail databases `aureuserp_testing_verify_*` |
| SAAS-05D | Plugin enforcement: people/work tools | SAAS-04 | SAAS-05A/B/C | review (suites executed 2026-11-05/06; TimesheetFeature failed and reproduced alone; see "Remaining suites") | Codex 2026-11-05 / isolated Sail databases `aureuserp_testing_verify_*` and `aureuserp_testing_a7_*` |
| SAAS-06 | Seat limit and invitation lifecycle | SAAS-01, D-SAAS-03, D-SAAS-04 | SAAS-05A/B/C/D | review | Copilot 2026-10-28; Claude review 2026-10-29 / `aureuserp_testing_saas_review`; all 17 suites green on `7bbf65741`, deployed in production batch 28 and verified 2026-11-03 |
| SAAS-07 | Onboarding and all seat-enforcement paths | SAAS-06 | — | review (complete) | Codex 2026-11-09 on `aureuserp_testing_saas07b`; see onboarding and mail handoff below |
| SAAS-08 | Platform-billing plugin foundation | SAAS-00, D-SAAS-01, D-SAAS-05 | — | review (foundation complete) | Copilot 2026-10-28; all 17 suites green on `7bbf65741`, deployed in production batch 28 and verified 2026-11-03 |
| SAAS-09 | Plans, prices and subscriptions | SAAS-08, D-SAAS-04 | — | review (activation built; cross-plugin read-only enforcement remains open) | Codex 2026-11-07 / `aureuserp_testing_saas09`; read-only rollout assessment 2026-11-09 below |
| SAAS-09A | Discount codes and tenant redemptions | SAAS-09, D-SAAS-08 | — | review (2026-11-09 / `aureuserp_testing_saas10`; gaps listed in the handoff, not fixed here) | commit `4f6284396` |
| SAAS-10 | Idempotent recurring invoices | SAAS-09, SAAS-09A | — | review (2026-11-09 / `aureuserp_testing_saas10`; invoice tests passed; four read-only-guard failures left untouched) | commit `4d7b611b9`; schedule-hook fix still uncommitted |
| SAAS-10B | Grace extensions that move the read-only instant | SAAS-10, D-SAAS-04, D-SAAS-13 | — | todo | — |
| SAAS-11 | Payments, allocations, credit and reversals | SAAS-08, SAAS-10, D-SAAS-05, D-SAAS-06, D-SAAS-10 | — | in progress (2026-11-09 / `aureuserp_testing_saas11`) | — |
| SAAS-12 | Platform administration screens | SAAS-03, SAAS-07, SAAS-09/10/11 | — | todo | — |
| SAAS-13 | Company-administration screens | SAAS-01, SAAS-07, SAAS-09/10/11 | — | todo | — |
| SAAS-15 | Backfill, shadow mode, release hardening, tenant impersonation, and test-or-commercial account type | All implementation packages | — | in progress (account type only, 2026-11-09 / `aureuserp_testing_saas11`; impersonation stays out) | — |

Additional requirement: Tenant impersonation

Super Admins and authorized Multi-Company Admins should be able to impersonate a Tenant Admin or Tenant User for troubleshooting, support, and verification purposes.

Requirements:

Allow authorized administrators to select a company/tenant and a specific Tenant Admin or Tenant User to impersonate.
Make impersonation access a configurable, assignable permission or right, granted only to authorized roles or individual administrators.
Enforce tenant isolation so administrators can only impersonate users within the companies they are authorized to access.
Clearly indicate when impersonation is active and identify the original administrator.
Provide a secure mechanism to end impersonation and return to the original administrator session.
Record audit logs for impersonation start and end events, including the original administrator, target user, tenant, timestamp, and relevant actions performed during impersonation.
Prevent impersonation from bypassing tenant-level permissions, security controls, or authorization restrictions.
Do not expose the target user's password, authentication credentials, or other secrets.
Require appropriate authorization and, where applicable, re-authentication before starting impersonation.
Ensure all impersonation activity is auditable and that sensitive or high-risk operations remain subject to appropriate safeguards.

Acceptance criteria:

Super Admins and authorized Multi-Company Admins can impersonate eligible tenant users when granted the required permission.
Administrators without the assigned permission cannot start impersonation.
Tenant boundaries and the target user's effective permissions remain enforced.
The original administrator can securely terminate impersonation and resume their own session.
Impersonation sessions and relevant actions are recorded in the audit trail.
Impersonation is covered by automated authorization, tenant-isolation, and session-security tests.
Implement this as a dedicated permission such as tenant.impersonate, rather than automatically granting it to every administrator. This makes access explicitly assignable and auditable.


`SAAS-00` is the only package ready before decisions are answered. After the
relevant decisions, `SAAS-01` or `SAAS-02` may run alongside `SAAS-03`, but
`SAAS-01` and `SAAS-02` must not run together because both affect company
context and authorization. The four `SAAS-05*` rollout slices are parallel-safe
after their shared contract.

### Review outcomes — 2026-10-28 (user review of the first five packages)

Recorded because each one changes either code or the expectations of the next
package. The user reviewed SAAS-01/02/03/04/06 before any suite was run.

| # | Finding | Disposition |
|---|---|---|
| RF-1 (superseded 2026-10-29) | *Superseded: the user decided that **new** companies are unlimited too. There is no screen to raise a limit before SAAS-12/13, so a default of 5 would stop the client being onboarded at their sixth user. The migration now adds the column with no default; `SeatCapacityTest` "leaves a new company unlimited by default" replaces "defaults a company to five seats", and capacity tests state their limit explicitly through `seatCapCompany()`. Original finding follows.* **Seat limit of 5 on existing companies** would stop any production tenant already at five or more people from inviting anyone on the day SAAS-06 deploys. | **Fixed in the migration.** `2026_10_28_000004_add_seat_limit_to_companies_table` now leaves *existing* companies unlimited (`seat_limit = null`) and keeps 5 only as the column default for companies created from now on. A limit is a commercial decision that has not been made per tenant; setting one stays deliberate and audited (`SeatCapacityService::setLimit()`). Per-company limits are the SAAS-12/13 screens. |
| RF-2 | **Per-company roles are stored but no policy consults them yet.** Admin in A / ordinary in B still leaks across a multi-active session. | **Accepted as deferred.** `TenantRoleResolver` and `User::hasPermissionInCompany()` exist and are tested; the adoption rewrite (policies + per-resource list restriction) belongs to **SAAS-05** and **SAAS-13**. The SAAS-01 handoff's cross-package request is the same work and now explicitly names both packages. |
| RF-3 | **Extra queries per permission check** — a denied check now costs more, once per row on a Filament page. Measure before caching. | **Measured, no cache added.** `plugins/webkul/security/tests/Feature/SaaSAdministration/PermissionCheckQueryBudgetTest.php` pins a ceiling of 4 queries for a granted *and* a denied check, and asserts a repeated check does not grow. The numbers are in the test's failure output; re-measure there before introducing caching. |
| RF-4 | **Multi-Company Admins lose the company form's modules section.** | **Confirmed correct** per D-SAAS-06 (entitlements are Super Admin only). No change; the narrowing stays. |

**RF-1 disposition correction (2026-11-03).** The original disposition's
statement that the column keeps a default of five for new companies is
superseded. The user decided on 2026-10-29 that new and existing companies are
unlimited by default (`seat_limit = null`); a limit is applied deliberately
through `SeatCapacityService::setLimit()`.

**RF-3 correction (2026-10-29).** The budget test above had not passed when RF-3
was written: its first run measured **6** queries and failed. The extra queries
were per-request warm-up, not per-row cost (see the run block below), so the test
now measures after one cold check. The ceiling of 4 is unchanged.

### Review run — 2026-10-28/29 (Claude, `aureuserp_testing_saas_review`)

The first time any of SAAS-01/02/03/04/06 code was executed. Focused filter
`SaaSAdministration|Entitlements|CompanyExecutionContextTest|CompanyAdministratorInvitationTest|MultiCompanyAdminTest|CompanyUserIsolationTest`:
**128 passed, 6 failed (425 assertions, 5,158 s)**. Each failure was run and read
on its own before anything changed.

| # | Failing test | Cause | Kind | Change |
|---|---|---|---|---|
| R-1 | `CompanyExecutionContextTest` "lets the installation administrator select any company" | `ResolveApiCompany` let any `isSuperAdmin()` user select any company. `Admin` is in `Role::getSuperAdminRoleNames()` but does not hold `bypass_company_scope` (only `super_admin` does, per company-admin-role-plan §1), so `CompanyContext` intersected the forced company with the Admin's memberships and resolved to **no company**: a 200 with `company: null` instead of a refusal. | **Code defect** (two definitions of "may see every company") | The selector now accepts exactly `CompanyContext::allowedIds()`, which already lists every company for a bypassing user. An Admin asking for a company outside their memberships gets a 403. Test renamed to "lets a user who sees every company select any company" (grants the bypass permission explicitly), plus a new test "refuses an Admin role holder a company outside their memberships". |
| R-2 | `PluginEntitlementServiceTest` "refuses to revoke a plugin a still-granted plugin depends on" | `applyForCompany()` closed the requested set over dependencies *before* `guardRevocations()`, so a dependency the administrator unticked was re-added and never appeared as revoked. The guard could not fire; the untick was silently ignored. | **Code defect** | `guardRevocations()` now compares `$before` against the **requested** set. The class docblock's "refused, with the dependents named" rule now holds. |
| R-3 | `MultiCompanyAdminTest` "creates a company user through the Filament relationship form" | Read `roles()` (global pivot). Since SAAS-01 a tenant role is stored on the membership; the sibling invitation test had been updated, this one had not. | Stale test | Reads `effectiveRoleIds()` and pins the global pivot as empty. |
| R-4 | `MembershipUniquenessCharacterizationTest` | The refused duplicate insert aborted the PostgreSQL transaction the test runs in, so the following count failed with `25P02`. Introduced by the SAAS-01 inversion in the earlier review. | Test defect | Insert wrapped in `DB::transaction()` (savepoint). |
| R-5 | `PermissionCheckQueryBudgetTest` "measures what one permission check costs" | 6 > 4 on the first check after `forgetCachedPermissions()`. The SQL (now printed in the failure message) showed four one-time queries: Spatie direct permissions (once per model), the membership-table probe (memoised), and `CompanyContext` allowed companies (two, memoised). A warm check is 2: `resolvableCompanyIds` + the `exists`. | Measurement error | One cold check first; the ceiling of 4 applies to warm checks. No caching added. |
| R-6 | `PluginEntitlementServiceTest` "grants a newly installed plugin to every company" | Expected 2, got 3: the installation's default company exists too. | Test premise | Expects `Company::withoutGlobalScopes()->count()`. |

Fixing R-2 exposed a seventh: "adds the dependencies of a granted plugin" passed
only because a new company is provisioned with every installed plugin, so the
dependency was already granted. It now starts from a company holding neither.

**Verification actually run:** the 12 affected tests and neighbours (guest 401,
default-company fallback, member selection): 10 passed / 2 failed (the two
above), then those two plus the second budget test: **4 passed, 18 assertions**.
Pint clean on every changed file. **Not run yet:** the full SecurityFeature,
PluginManagerFeature, SupportFeature, SaleFeature and PlatformBillingFeature
suites, so the packages stay in `review`.

**Benefit.** An API client can no longer get a successful response with no
tenant. A plugin's dependency can no longer be quietly kept after an
administrator removed it. Two tests that could not fail now can.

**Trade-off / risk.**

- An `Admin` who previously reached another company through the header now
  gets 403. Nothing in the repository relied on it (only the test above
  sent the header as an Admin); external integrations using it would see the
  change. Granting `bypass_company_scope` restores it deliberately.
- Unticking a required plugin on the **company form** now throws. The
  error is keyed `company_ids` (the Plugins page field), so the company
  form may not show it, and the form's own fields save before the modules
  section. Follow-up: map the message onto `company_modules` or validate
  before save. *Resolved 2026-10-29, see "Company-form follow-up" below.*

**Rollback.** Restore the `isSuperAdmin()` branch in `ResolveApiCompany`
and pass `$wanted` to `guardRevocations()`; the test changes are
independent.

**Protected decisions added** (AGENTS.md): the selector accepts exactly
`allowedIds()`; a needed dependency's untick is refused.

#### Company-form follow-up — 2026-10-29 (Claude, commit `87c9cc880`)

**Why.** R-2 made the revocation guard live, which exposed two company-form
paths it had been hiding:

- **Edit:** the refusal came from the write, *after* the company's own
  fields saved (`EditCompany` has no database transaction), keyed to a field
  the company form does not have.
- **Create:** a new company is provisioned with every installed plugin
  before the form's choice is applied, so ticking a plugin without its
  dependency read as revoking the dependency, and the create would have
  failed after the company existed.

**How.**

- `PluginEntitlementService::resolveForCompany()` is now the single
  resolution step, shared by the write and a new validate-only
  `assertCanSyncForCompany()`, so the two cannot disagree.
- The modules `CheckboxList` calls `assertCanSyncForCompany()` as a field
  rule on edit.
- On create (`wasRecentlyCreated`), the choice is closed over its
  dependencies with `withDependenciesOf()` before syncing.

**Benefit.** The refusal now shows on the modules field and nothing saves. A
new company gets its chosen plugins' dependencies instead of a failed create.

**Tests.** Two new `CompanyModulesTest` page tests, the first Livewire tests
for the company pages:

- an edit that unticks a needed plugin is refused on the field, and the
  company's name is unchanged;
- a create that ticks only the child grants the parent too.

Also corrected, and not caused by this work:

- **Two `CompanyModulesTest` section tests** assumed the modules section
  was alone in `main.after`. Security's Administrator section (2026-10-23)
  shares the slot. They now find the section by heading and still assert
  exactly one.
- **The audit test** ("records the actor and the before/after grants")
  passed or failed depending on run order. A temporary diagnostic showed
  that when its plugin was the only one installed, the new company already
  held it. The sync changed nothing, so correctly no audit was written. It
  now removes that grant first.

**Verification actually run** (`aureuserp_testing_saas_review`): the two
test files, 14 passed / 3 failed (the three corrected above). The audit test
alone failed, which is how its cause was found. Then **PluginManagerFeature,
full suite: 42 passed, 133 assertions, 0 failures** (1,071 s). Pint clean.

**Trade-off / risk.** The company form's validation is now one database read
heavier on save, for a Super Admin only.

**Rollback.** Drop the `->rule(...)` and the `wasRecentlyCreated` branch in
`CompanyContributions`. The service methods are additive.

#### Company switcher restricted to installation staff — 2026-10-29 (commit `57687b905`)

**Why.** This is a user decision, taken while preparing to onboard a client
from a different sector. Any user who belonged to several companies could
widen one view across them through the switcher, and `company-context/set`
accepted the switch from any active user.

**How.**

- `CompanyContext::canSwitchCompanies()` holds the rule: an active Super
  Admin, or a Multi-Company Admin. The second was confirmed by the user,
  since DEKA staff work across the clients assigned to them.
- `SetCompanyContextRequest::authorize()` refuses everyone else with a 403,
  for both a switch and a reset.
- The render hook passes `canSwitch` to the view. Everyone else sees their
  company's name with no form.
- The internal, self-restoring context changes in `StopLinkService` and
  `Logistics\Support\CompanyStorage` are untouched.

**Benefit.** A client user can no longer change which companies are active,
from the UI or by posting the form by hand.

**Trade-off / risk.**

- A client user who holds several memberships is now limited to their
  default company. To move them, change their default company.
- A session that already holds several active companies keeps them until
  that session ends. Only staff can write the key from now on.

**Verification actually run.** `CompanySwitcherTest`, five tests: a client
user is refused a switch and a reset, a Super Admin can switch, and the
header has no form for a client user but has one for a Super Admin. **5
passed, 15 assertions.** The existing Multi-Company Admin switch tests in
`MultiCompanyAdminTest` are unchanged. The full SupportFeature rerun is in
the table below.

**Rollback.** Revert `57687b905`.

**Also in this round.**

- `MustChangePasswordTest` fix (`4b2e6e1bd`).
- Production merged into develop (`52fccc235`, no conflicts), so the next
  deploy merges cleanly.
- The two stop-link views that merge brought back (`form`, `landing`) were
  deleted. Develop replaced them with `show`, `done` and `expired`, and
  nothing renders them.

### Full-suite runs — 2026-10-29 (`aureuserp_testing_saas_review`)

| Suite | Result |
|---|---|
| PluginManagerFeature | 42 passed, 133 assertions, 0 failures |
| SecurityFeature | 107 passed, 2 failed (355 assertions, 1,088 s). Both failures were in `MustChangePasswordTest`, a test defect present since 2026-10-21 and unrelated to SAAS: its fake `$next` returned a string, which `RequirePasswordChange::handle()`'s `Response` return type rejects before any assertion runs. The fake now returns `response('passed-through')` and the test asserts the body. File rerun: 47 passed, 131 assertions (includes `MultiCompanyAdminTest`, which the file `require`s), 0 failures. |
| SupportFeature | First run: 185 passed, 2 failed (931 assertions, 1,783 s). Both failures were stale structure snapshots, `security.json` and `support.json`, from two intended changes: the company form's Administrator section (`administrator_email`, 2026-10-23) and SAAS-01's `effective_role_names` replacing `roles.name` on the user infolist. Both fixtures were deleted and re-recorded from the full suite, per the snapshot test's own rule. Re-run: **192 passed, 946 assertions, 0 failures** (1,766 s), including `CompanySwitcherTest`. The re-recorded fixtures differ from the old ones (`57687b905`) by exactly those two changes. |
| SaleFeature | 143 passed, 434 assertions, 0 failures (2,914 s) |
| PlatformBillingFeature | First run: 17 passed, 1 failed. **A real defect (SAAS-08):** `Money::percentage()` divided by the rate's 10,000 scale but not by 100, so 15% of 100.00 returned 1,500.00. It also read the rate through `(float)`, despite its "integer maths end to end" comment. Fixed: the rate is parsed from its digits, and one divisor of 100 × 10,000 is used. New test "applies fractional, four-decimal and negative-amount rates exactly". No caller existed outside the test, so no stored amount was affected. Re-run: **19 passed, 46 assertions, 0 failures.** |
| Seat-capacity file (after the no-default change) | 18 passed, 55 assertions, 0 failures |

**All five suites green on 2026-10-29**, plus the targeted reruns above. The
packages stay in `review` only until the user commits and decides the deploy.

---

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
  and `aureuserp_testing_saas_00_weak`; verification on
  `aureuserp_testing_saas_00_verify`.
- Status: **done** — 12 tests, 68 assertions, 0 failures (2026-10-27).
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
- Implemented: eight characterization test files (twelve tests) across the two
  owned test directories; no production behavior or schema changed.
  *(Correction 2026-10-28: an earlier wording here counted the onboarding test
  separately from the eight files it belongs to.)* Since then SAAS-01 inverted
  `MembershipUniquenessCharacterizationTest` and
  `ResourcePermissionMismatchCharacterizationTest`, SAAS-04 inverted
  `PluginEnforcementCharacterizationTest`, and SAAS-06 inverted
  `InvitationUniquenessCharacterizationTest`; see the review block after the
  SAAS-06 handoff.
- Files changed (8 test files):
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/GlobalTenantRoleCharacterizationTest.php`
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/ApiDefaultCompanyCharacterizationTest.php`
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/CompanyCreationOnboardingCharacterizationTest.php`
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/ResourcePermissionMismatchCharacterizationTest.php`
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/MembershipUniquenessCharacterizationTest.php`
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/InvitationUniquenessCharacterizationTest.php`
  - `plugins/webkul/plugin-manager/tests/Feature/Entitlements/PluginEnforcementCharacterizationTest.php`
  - `plugins/webkul/plugin-manager/tests/Feature/Entitlements/DefaultPluginGrantCharacterizationTest.php`
  - This handoff.
- Tests run with pass/assertion counts (2026-10-27, database
  `aureuserp_testing_saas_00_verify`):
  - SaaSAdministration filter: **8 passed, 54 assertions**, 151 s.
  - Entitlements filter: **4 passed, 14 assertions**, 608 s.
  - Total: **12 passed, 68 assertions, 0 failures.**
- Reviewed but not executed: full SecurityFeature/PluginManagerFeature suites,
  browser navigation, queued Filament export execution, and concurrent context
  restoration.
- Failures investigated during verification:
  - `CompanyCreationOnboardingCharacterizationTest` expected zero memberships
    after factory creation, but `Company::created` (at
    `support/Models/Company.php:198`) auto-attaches the creator. Fixed: test now
    expects 1 membership (creator) and 0 invitations/administrators.
  - `ResourcePermissionMismatchCharacterizationTest` called
    `Role::isCompanyAdminRole()` which does not exist. Fixed: replaced with a
    direct `Role::COMPANY_ADMIN` name comparison.
  - `DefaultPluginGrantCharacterizationTest` had no `beforeEach` to install
    plugins, so `$installedPluginIds` was empty on a fresh database. Fixed:
    added `TestBootstrapHelper::ensurePluginInstalled('sales')`.
- Security/tenant/concurrency review: tests use two real company memberships and
  positive/negative permission controls. The plugin test proves both grant
  presence and revocation before exercising the bypass. The Livewire test
  exercises CreateUser and EditUser with real form submissions. No managed
  database or production environment is involved.
- Risks and rollback: tests intentionally describe insecure/incomplete current
  behavior and must be inverted by SAAS-01/02/04/06, not preserved through
  compatibility code. Rollback is deletion of the new tests/handoff only.
- Cross-package requests: SAAS-01 owns membership roles and the
  CreateUser/EditUser resource_permission mismatch; SAAS-02 owns explicit
  API/job context; SAAS-03 owns default/dependency-aware grants and audited
  mutations; SAAS-04 owns the shared enforcement contract; SAAS-05A-D own
  plugin-by-plugin adoption; SAAS-06 owns invitation lifecycle (duplicate
  protection, expiry); SAAS-07 owns capacity, the membership unique constraint,
  and every mutation path listed above.
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

#### SAAS-01 handoff — 2026-10-28

- Owner / database: Copilot / **no database used** — code written on request, the
  suite is to be run next. Reserve `aureuserp_testing_saas_01`.
- Status: **review**, with the explicit caveat that **no test has been executed**.
  Only `php -l` was run over every changed file (all clean).
- Decision taken with the user before coding: D-SAAS-02 addendum above —
  record-scoped resolution, `hasPermissionInCompany()` for policies.
- Storage chosen: a **companion pivot** (`company_membership_roles`:
  user + company + role) rather than a `role_id` column on the membership row.
  Reason: a user may hold more than one tenant role, and a single column would
  silently drop the second one for anybody who has two today.
- Platform roles stay in `model_has_roles`. Super Admin, Multi-Company Admin and
  the install administrator role administer the installation, not one tenant;
  `Role::getSystemRoleNames()` is the existing predicate, so no new list was
  invented.
- `User::hasPermissionTo()` became a **union**: Spatie (global store) first, then
  the membership store. It can therefore only add to what the previous
  implementation returned, which is what makes the migration safe to apply to a
  live installation.
- Migration 3 is the only data change and is deliberately conservative: each
  moved role is granted in **every** company the user is a member of (exactly the
  access they had a second earlier); a user with a tenant role but **no**
  membership row keeps the global grant and is logged rather than silently
  stripped. `down()` writes the union back to `model_has_roles`.
- Known behaviour change, deliberate: after the migration a tenant role is no
  longer in `model_has_roles`, so `hasRole('Company Admin')` is false for a
  Company Admin. `isCompanyAdmin()` and `hasPermissionInCompany()` are the
  supported questions. The one test asserting the old storage
  (`CompanyAdministratorInvitationTest`) was updated to assert the permission
  instead.
- Fixed the audited regression: `EditUser` now sets `resource_permission = global`
  for Company Admin as well as Multi-Company Admin, matching `CreateUser` and
  invitation acceptance.
- Also updated, because the migration would otherwise break them:
  `MultiCompanyAdminService::scopeAssignableRoles()` (a Company Admin's
  permissions now come from the membership store, so without the merge they could
  assign nothing), `userSnapshot()` and the self-administration guard (both read
  the union, or an MCA editing themselves looks like an escalation), and the
  Users list/infolist columns (a tenant role would otherwise render as an empty
  cell).
- Files changed:
  - `plugins/webkul/security/database/migrations/2026_10_28_000001_add_unique_membership_to_user_allowed_companies.php`
  - `plugins/webkul/security/database/migrations/2026_10_28_000002_create_company_membership_roles_table.php`
  - `plugins/webkul/security/database/migrations/2026_10_28_000003_scope_tenant_roles_to_company_memberships.php`
  - `plugins/webkul/security/src/Services/TenantRoleResolver.php` (new)
  - `plugins/webkul/security/src/Services/TenantRoleAssigner.php` (new)
  - `plugins/webkul/security/src/Models/User.php`
  - `plugins/webkul/security/src/Services/MultiCompanyAdminService.php`
  - `plugins/webkul/security/src/Services/UserInvitationService.php`
  - `plugins/webkul/security/src/SecurityServiceProvider.php` (migration registration + scoped binding)
  - `plugins/webkul/security/src/Filament/Resources/UserResource.php`
  - `plugins/webkul/security/src/Filament/Resources/UserResource/Pages/CreateUser.php`
  - `plugins/webkul/security/src/Filament/Resources/UserResource/Pages/EditUser.php`
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/TenantRoleScopingTest.php` (new)
  - `plugins/webkul/security/tests/Feature/Authorization/CompanyAdministratorInvitationTest.php`
- Tests to run (in this order), one at a time, `-e DB_DATABASE=aureuserp_testing_saas_01`:
  1. `TenantRoleScopingTest` alone — 3 tests.
  2. `MultiCompanyAdminTest`, `CompanyUserIsolationTest`,
     `CompanyAdministratorInvitationTest`, `ResourcePermissionMismatchCharacterizationTest`,
     `GlobalTenantRoleCharacterizationTest`.
  3. `--testsuite=SecurityFeature`, then `PluginManagerFeature` and
     `SupportFeature` (the Users page and `isCompanyAdmin()` are used by guides).
- Risks to check first when the suite runs:
  - The `roles` form field is no longer a Filament relationship, so a create/edit
    that fails to persist roles points at `afterCreate`/`afterSave`, not the form.
  - `GlobalTenantRoleCharacterizationTest` is expected to stay **green**: the
    legacy `syncRoles()` path is still honoured by design. Its docblock said
    SAAS-01 would make it fail; that expectation is now wrong and should be
    reworded rather than the test deleted.
- Cross-package request (recorded, not done here): **list restriction**. A
  multi-active session still lists every active company's rows, so a user who is
  admin in A and ordinary in B sees B's rows in a combined list and is denied only
  on the record. `TenantRoleResolver::companyIdsHolding()` is the intended input
  for a per-resource `whereIn`; adopt it in **SAAS-12/13** per resource, not by
  changing `CompanyScope`.
- Cross-package request (review RF-2, 2026-10-28): **policy adoption**. Nothing
  consults `hasPermissionInCompany()` yet, so the storage is correct while the
  enforcement still runs through `hasPermissionTo()` (which unions both stores).
  Policies and services that know a record's company must call the company-scoped
  method; that rewrite is **SAAS-05** (per plugin) and **SAAS-13** (screens), and
  the same user is admin in A and ordinary in B only once it lands.
- Cost of a check (review RF-3): measured, not assumed —
  `PermissionCheckQueryBudgetTest` caps a granted and a denied check at 4 queries
  and asserts a repeated check does not grow. No caching was added; re-measure
  there before adding any.

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

#### SAAS-02 handoff — 2026-10-28

- Owner / database: Copilot / **no database used** — suite to be run next.
  Reserve `aureuserp_testing_saas_02`, or reuse the SAAS-01 database (both are
  `SecurityFeature`/`SupportFeature` work).
- Status: **review**, no test executed. `php -l` clean; `pint --format agent`
  passed.
- **Explicit context** — `CompanyContext::runFor(?int $companyId, Closure)` sets
  the company, runs the callback and restores the previous context in a
  `finally`. This is the contract for queued jobs and console commands: the
  company must be declared, because `CompanyScope` is skipped outside tests in
  console execution and there is no session to fall back on. `forceActive()` is
  the same thing without a restore step, for a request lifetime.
- A forced context deliberately **loses the default-company fallback**:
  `currentId()` returns the forced company or null, never "their default",
  otherwise a context that resolved to nothing would quietly act on the wrong
  tenant.
- **Fail-closed write** — `BelongsToCompany::creating` now throws
  `MissingCompanyContextException` when an acting user is present and no company
  resolves. A null `company_id` is read by `CompanyScope` as *shared*, so the row
  would appear in every company; this is a cross-tenant leak, not a default.
  Nobody acting (seeders, installers, console bootstrapping) keeps the old
  behaviour, because those genuinely create rows outside a request.
- **API selector** — `ResolveApiCompany`, aliased as `api.company` in
  `SupportServiceProvider`, applied to every plugin's API group before
  `plugin.entitlement` so entitlement is judged against the selected company.
  Reads `X-Company-Id` (or a `company_id` field), checks membership, 403 on a
  forged id, allows any company for a super admin. No header = the documented
  compatibility fallback (default company), so existing integrations are
  unaffected until they opt in.
- Cross-package edit, recorded as required: `plugin-manager/src/PackageServiceProvider.php`
  (the API middleware group now reads `throttle:api, api.abilities, api.company,
  plugin.entitlement:<plugin>`). It is the only place every plugin's API routes
  are loaded, so the selector could not be applied without it.
- Files changed:
  - `plugins/webkul/support/src/Services/CompanyContext.php`
  - `plugins/webkul/support/src/Traits/BelongsToCompany.php`
  - `plugins/webkul/support/src/Exceptions/MissingCompanyContextException.php` (new)
  - `plugins/webkul/support/src/Http/Middleware/ResolveApiCompany.php` (new)
  - `plugins/webkul/support/src/SupportServiceProvider.php` (alias)
  - `plugins/webkul/plugin-manager/src/PackageServiceProvider.php` (group)
  - `plugins/webkul/support/tests/Feature/Tenancy/CompanyExecutionContextTest.php` (new, 8 tests)
- **Highest risk in this package:** the fail-closed write. Any existing test or
  seeder that authenticates a user with **no** resolvable company and then
  creates a tenant model with `autoAssignsCompany() === true` now throws.
  `SecurityHelper::authenticateWithPermissions()` sets a default company and
  membership, so the common pattern is safe; that is a reading, not a
  measurement. If a suite fails with `MissingCompanyContextException`, the
  fixture is missing a company - do not relax the guard.
- Deliberately not done here: converting existing jobs/commands to `runFor()`.
  The card requires a recorded ownership request for jobs outside Support; the
  helper and its tests are the deliverable, adoption belongs with each owner.
  (There is no application `Jobs` namespace yet - Chatter's notification is the
  only `ShouldQueue` class - so adoption is a greenfield step, not a migration.)
- Tests to run, one at a time: `--testsuite=SupportFeature --filter=CompanyExecutionContextTest`,
  then the whole `SupportFeature`, then `SecurityFeature` and one plugin suite
  (`SaleFeature`) as the fail-closed probe.

#### SAAS-03 — plugin defaults and audited mutations

**Owns:** Plugin Manager services/models/provider/form actions, Plugin Manager
migrations and tests. Other plugin directories are read-only.

**Deliver:** configured defaults; historical grants preserved; one
transactional mutation service; dependency closure/revocation rules;
authorization per D-SAAS-06; before/after audit; cache flush.

**Accept:** existing companies unchanged; new company receives exact defaults;
dependency, forged-write, audit and idempotency tests pass.

#### SAAS-03 handoff — 2026-10-28

- Owner / database: Copilot / **no database used** — code written on request, the
  suite is to be run next. Reserve `aureuserp_testing_saas_03`.
- Status: **review**, no test executed. `php -l` clean on all files;
  `pint --format agent` passed.
- What changed, and why it was two writers before: the Plugins page action called
  `$record->companies()->sync(...)` with no authorization re-check and no
  dependency awareness, while `CompanyContributions::syncGrants()` had its own
  delete/insert with an authorization check the other lacked. Both now delegate
  to `PluginEntitlementService`, which owns the transaction, dependency closure,
  revocation guard, audit record and `PluginAccess::flush()`.
- Configured defaults: new key `config('plugin-manager.default_company_plugins')`.
  **`null` keeps today's behaviour** (every installed plugin), so an installation
  that never sets it is unaffected; an explicit list of plugin names narrows the
  set for *new* companies only. Provisioning stays purely additive - a narrowed
  default never revokes an existing grant.
- Dependency closure: granting a plugin also grants its declared dependencies
  (`Plugin::getDependenciesFromConfig()`, the live source - the
  `plugin_dependencies` table exists but nothing writes to it). Only installed
  dependencies are added.
- Revocation guard: revoking a plugin that a still-granted plugin depends on is
  **refused** with the dependents named, rather than silently auto-revoking a set
  nobody ticked. Revoke the dependent first.
- Authorization per D-SAAS-06: **Super Admin only** for entitlements. This is a
  deliberate narrowing - `CompanyContributions::administersInstallation()` used to
  admit Multi-Company Admins, and the Companies action had no check at all. The
  service re-checks because the super-admin `Gate::before` skips policies. To
  restore MCA access, change that one predicate and the service guard together.
- Grants for plugins **not installed here** are left untouched by a replace, as
  before: they belong to a plugin this installation does not carry, and dropping
  them would lose an entitlement that returns as soon as the plugin is installed.
  They are therefore outside the compared/audited set.
- Audit: `plugin.entitlement.updated` on the Company (subject), with actor,
  company, and `before`/`after` `plugin_ids`, written only when the set actually
  changes.
- Files changed:
  - `plugins/webkul/plugin-manager/config/plugin-manager.php` (new)
  - `plugins/webkul/plugin-manager/src/Services/PluginEntitlementService.php` (new)
  - `plugins/webkul/plugin-manager/src/Services/CompanyPluginProvisioner.php` (delegates)
  - `plugins/webkul/plugin-manager/src/CompanyContributions.php` (delegates; super-admin only)
  - `plugins/webkul/plugin-manager/src/PluginManagerServiceProvider.php` (`->hasConfigFile`)
  - `plugins/webkul/plugin-manager/src/Filament/Resources/PluginResource.php` (Companies action delegates; hidden for non-super-admins)
  - `plugins/webkul/plugin-manager/tests/Feature/Entitlements/PluginEntitlementServiceTest.php` (new)
  - `plugins/webkul/plugin-manager/tests/Feature/Entitlements/DefaultPluginGrantCharacterizationTest.php` (third test rewritten for the config seam)
- Tests to run, one at a time, `-e DB_DATABASE=aureuserp_testing_saas_03`:
  1. `--filter=PluginEntitlementServiceTest` — 7 tests.
  2. `--filter=DefaultPluginGrantCharacterizationTest`.
  3. `--filter=PluginAccessTest` and `--filter=CompanyModulesTest` (both exercise
     the paths that now delegate).
  4. `--testsuite=PluginManagerFeature`, then `SupportFeature` (the company form
     section is contributed from here).
- Risks:
  - The dependency tests mock `dependencyNamesFor()`; a real two-plugin install
    pair is not exercised because installing plugins costs minutes per plugin in
    this suite. If a real dependency regresses, it will show in SAAS-05 slices.
  - `PluginResource`'s Companies action now writes through the service, which
    validates; a revocation blocked by a dependency surfaces as a validation
    error in the action, not a silent no-op.
  - Multi-Company Admin loses the modules section — **confirmed correct on
    2026-10-28** (review RF-4, D-SAAS-06). No change; do not "restore" it in
    SAAS-13 without a new decision.

#### SAAS-04 — generic plugin enforcement contract

**Owns:** Plugin Manager middleware/contracts/concerns/exceptions, provider
integration and contract tests. Other plugin directories are read-only.

**Deliver:** stable plugin key; navigation, route, current-company,
record-company and service/job guards; explicit core/non-entitleable manifest;
fail-closed no-company semantics.

**Accept:** a fixture proves hidden navigation, direct route denial, record
denial and service/job denial; re-enabling restores access. Do not bulk-edit
real plugins in this package.

#### SAAS-04 handoff — 2026-10-28

- Owner / database: Copilot / **no database used** — the suite is to be run next.
  Reserve `aureuserp_testing_saas_04` (or reuse the SAAS-03 database, since this
  is the same plugin and the same process).
- Status: **review**, no test executed. `php -l` clean; `pint --format agent`
  passed.
- What closed the hole: every plugin's `routes/api.php` is loaded by
  `PackageServiceProvider` inside
  `['throttle:api', 'api.abilities', 'plugin.entitlement:<plugin>']`, so a plugin
  cannot forget the guard. `PluginEnforcementCharacterizationTest` was inverted
  from `assertOk()` to `assertForbidden()` — same request, now denied.
- Middleware ordering trap, handled explicitly: the group runs *before* the route
  file's own `auth:sanctum`, so `$request->user()` is null there. The middleware
  resolves `sanctum` then `web` itself and calls `Auth::setUser()`, because
  `CompanyContext` reads `auth()->user()` and the default guard is not always
  `web` on an API request. Without that, an entitled request would read as "no
  company" and be denied.
- Contract surface (`Webkul\PluginManager\Support\PluginEntitlement`):
  `enabledForCurrent()` for navigation, `enabledForCompany()` for a record/policy,
  `assertForCompany()` for services and jobs, `currentCompanyId()` for the
  "route carries no record" case. Fail-closed: a null company throws.
- Exempt manifest: `config('plugin-manager.entitlements.exempt')` lists the nine
  `->isCore()` plugins (support, security, plugin-manager, partners, chatter,
  fields, table-views, analytics, full-calendar). Listed explicitly rather than
  derived so the boundary is readable; forgetting one fails closed.
- Super admin bypass: `config('plugin-manager.entitlements.super_admin_bypass')`,
  default true — the installation administrator may hold no membership at all,
  and Super Admin is also the recovery path. Tenants are still enforced.
- **Required companion fix (InstallCommand):** installing a plugin now grants it
  to every existing company (`PluginEntitlementService::grantToEveryCompany()`),
  called right after the plugin row is marked installed. Without it, a plugin
  installed after the tenants existed would have been unreachable — previously a
  missing menu entry, but with routes enforced a **403 for everyone**. This
  matches what the 2026-10-22 migration preserved: grants are an opt-out from a
  global default, not an opt-in.
- No page/resource trait was added on purpose. A trait that defines `canAccess()`
  collides with `HasPageShield` (a class method beats a trait method, and two
  traits defining the same method are a fatal error without `insteadof`) — that
  is the trap this repository already documents. SAAS-05 slices call
  `PluginEntitlement::enabledForCurrent('<plugin>')` from their own
  `canAccess()`/`canViewAny()` explicitly.
- Web routes are **not** auto-guarded: a URL carries no company, and a blanket
  middleware cannot express "this record belongs to another company". SAAS-05
  slices add `plugin.entitlement:<plugin>` to their web group where the page has
  no per-record decision, and a policy check via `assertForCompany()` where it
  does.
- Files changed:
  - `plugins/webkul/plugin-manager/config/plugin-manager.php` (exempt list, bypass flag)
  - `plugins/webkul/plugin-manager/src/Exceptions/PluginNotEntitledException.php` (new)
  - `plugins/webkul/plugin-manager/src/Support/PluginEntitlement.php` (new)
  - `plugins/webkul/plugin-manager/src/Http/Middleware/EnsurePluginIsEntitled.php` (new)
  - `plugins/webkul/plugin-manager/src/PluginManagerServiceProvider.php` (alias)
  - `plugins/webkul/plugin-manager/src/PackageServiceProvider.php` (api group)
  - `plugins/webkul/plugin-manager/src/Console/Commands/InstallCommand.php` (grant on install)
  - `plugins/webkul/plugin-manager/src/Services/PluginEntitlementService.php` (`grantToEveryCompany`)
  - `plugins/webkul/plugin-manager/tests/Feature/Entitlements/PluginEntitlementGuardTest.php` (new)
  - `plugins/webkul/plugin-manager/tests/Feature/Entitlements/PluginEnforcementCharacterizationTest.php` (inverted)
  - `plugins/webkul/plugin-manager/tests/Feature/Entitlements/PluginEntitlementServiceTest.php` (grant-on-install case)
- Tests to run, one at a time: `--filter=PluginEntitlementGuardTest`, then
  `--filter=PluginEnforcementCharacterizationTest`, then `--filter=PluginEntitlementServiceTest`,
  then the whole `PluginManagerFeature`.
- **Widest risk in the whole session:** the guard now applies to every plugin's
  API routes. Other suites that build a company *before* installing the plugin
  under test would now get a 403 instead of a 200. `TestBootstrapHelper::ensurePluginInstalled()`
  runs the real install command, which now grants, so the common
  "install in `beforeEach`, create company in the test" order is safe - but that
  claim is untested until the run. If a suite fails with 403 on a plugin API, the
  fixture needs the plugin installed before its company is created, not a
  relaxation of the guard.

#### SAAS-05A/B/C/D — plugin rollout slices

Each slice applies SAAS-04 without inventing another guard:

- **05A:** `accounts`, `accounting`, `invoices`, `payments`.
- **05B:** `products`, `sales`, `purchases`; `contacts` and `partners` are core
  plugins exempt from entitlement enforcement.
- **05C:** `inventories`, `manufacturing`, `maintenance`, `logistics`,
  `barcode`; reconcile `LogisticsAccess` to one authoritative entitlement.
- **05D:** `employees`, `recruitments`, `projects`, `timesheets`, `time-off`,
  `table-views`, `fields`, `analytics`, `full-calendar`, and `website` only
  when SAAS-04 marks them entitleable.

**Scope reconciliation and production evidence (2026-11-04).** Production's
five companies each held all 18 installed plugin grants, so enforcement removes
nothing until an administrator deliberately unticks a module. The card above
lists `contacts` and `partners` in 05B, while the authoritative SAAS-04 manifest
exempts both. Therefore 05B must not gate either core plugin; this rollout
covers `products`, `sales` and `purchases` only.

**Owns:** only listed plugin directories and slice tests. No slice edits Plugin
Manager, Support, Security, `phpunit.xml`, or another slice.

**Accept per plugin:** assert installed and granted; revoke; navigation hides;
direct page/API and one write service deny; data remains; restore access. Run
and report each touched plugin suite separately.

#### SAAS-05B handoff — 2026-11-04

- Owner / database: Codex / `aureuserp_testing_saas_05b` (local Sail Postgres).
- Status: **review**. No production or managed database used.
- Implemented: Products, Sales and Purchases now enforce the SAAS-04 entitlement
  at navigation/resource policy, API and write-service boundaries. Super Admin
  bypass remains intact. Tests revoke Company A's grant, verify denial, verify
  Company B remains enabled, then restore A's grant. Purchases also checks the
  customer portal against the customer's own company. Partners remains exempt
  under the SAAS-04 manifest and was not gated. Contacts is also exempt under
  SAAS-04; no contacts code was changed.
- Why / how / benefit: use the shared `PluginEntitlement` contract so revocation
  blocks direct access and writes, not only menu visibility. Policies use the
  record's `company_id` when a record is available. The Products category
  provisioner is deliberately independent of entitlement: company root
  categories are required infrastructure, and the company-created observer can
  run before default grants are synchronized.
- Protected decisions recorded in `AGENTS.md`: retain the record-company
  entitlement check; resolve customer-portal entitlement from the authenticated
  Partner's company because `CompanyContext` is for internal users; keep
  `contacts` and `partners` exempt per the SAAS-04 manifest.
- Files changed: implementation and regression test files are in
  `plugins/webkul/products/`, `plugins/webkul/sales/` and
  `plugins/webkul/purchases/`; the implementation is recorded in shared git
  history. This handoff and the SAAS-05B status row were updated here.
- Tests run (serially, same isolated database):
  - Focused `PluginEntitlementTest` — Products: **1 passed, 16 assertions**;
    Sales: **1 passed, 16 assertions**; Purchases: **1 passed, 25 assertions**.
  - Previous `ProductFeature`: **272 passed, 3 failed (619 assertions)**,
    2,584.58 s. After fixing root category provisioning, rerun:
    **275 passed (636 assertions), 0 failed**, 2,934.68 s.
  - `SaleFeature`: **147 passed (456 assertions)**, 3,105.92 s.
  - `PurchaseFeature`: **190 passed (529 assertions)**, 3,545.19 s.
- Reviewed but not executed: complete Contacts/Partners suites, broader
  plugin-manager and cross-slice suites, browser interaction, and production
  rollout checks.
- Failures investigated and resolved: category infrastructure provisioning
  must run when a company is created even before its default Products grant is
  synchronized. Removing the entitlement gate from
  `CompanyCategoryProvisioner` restored company root categories and backfill
  behavior; the full ProductFeature rerun passed.
- Security / tenant / concurrency review: entitlement checks are company
  specific; tests prove A revocation does not affect B, direct/API/service
  denial, data retention, and regrant. Existing Sales and Purchases business
  workflow tests passed. Suites were run serially to avoid concurrent use of the
  same test database.
- Risks and rollback: retain root category provisioning independently of module
  grants. No schema change was made. Remaining 05B package suite coverage is
  noted above; the existing focused entitlement and Sale/Purchase suites passed.
- Cross-package requests: none. Contacts and Partners stay exempt under SAAS-04.

#### SAAS-05A handoff — 2026-11-04

- Owner / database: Codex / `aureuserp_testing_saas_05a` (local Sail Postgres).
- Status: **review; tests pending**. The entitlement implementation is in
  shared commit `98f40d9e6`; no production database was used.
- Test attempt: Accounts `PluginEntitlementTest` was started twice. Both runs
  spent several minutes in `accounts:install` and repeated full
  `shield:generate --all` scans; the last isolated container was stopped after
  confirming the repeated work. No test count is claimed.
- Reviewed but not executed: Accounting and Invoices focused tests, full
  AccountFeature and AccountingFeature suites, browser access checks.
- Cross-package issue: none identified. Rerun focused tests and package suites
  after the plugin-install test bootstrap cost is addressed or accepted.

#### SAAS-05C handoff — 2026-11-04

- Owner / database: Codex / `aureuserp_testing_saas_05c` (local Sail Postgres).
- Status: **review; tests pending**. The entitlement implementation is in
  shared commit `98f40d9e6`; no production database was used.
- Reviewed implementation: inventory move confirmation and manufacturing order
  workflows assert company entitlement before mutation; Barcode authenticated
  routes use the shared route middleware; LogisticsAccess requires both its
  existing company activation setting and SAAS-04 entitlement.
- Tests: no 05C test suite ran this turn because the Accounts install bootstrap
  repeatedly triggered full Shield permission generation. No counts claimed.
- Cross-package request: none. Run the focused tests and InventoryFeature,
  ManufacturingFeature, and LogisticsFeature once setup completes within the
  available test window.

#### SAAS-05D handoff — 2026-11-04

- Owner / database: Codex / `aureuserp_testing_saas_05d` (local Sail Postgres).
- Status: **review; tests pending**. The entitlement implementation is in
  shared commit `98f40d9e6`; no production database was used.
- Scope: `analytics`, `fields`, `full-calendar`, and `table-views` are core and
  exempt per SAAS-04; `website` remains an entitlement plugin. The committed
  portal middleware checks the authenticated Partner's own company.
- Tests: no 05D suite ran this turn because the shared install test bootstrap
  repeatedly triggered full Shield permission generation. No counts claimed.
- Reviewed but not executed: focused tests and EmployeeFeature, ProjectFeature,
  recruitment, timesheet, and time-off package coverage.

#### SAAS-05 pre-deploy entitlement audit — 2026-11-05

Read-only review against the protected SAAS-04 decisions. No production code was
changed during this review.

| Plugin | Navigation / page | Resource policies | Write services / jobs | Routes |
|---|---|---|---|---|
| Accounts | Resource navigation follows policy | All 18 policies use the account entitlement concern; record checks use `company_id` | `PaymentRegistrar` checks Accounts and Payments | API guarded centrally |
| Accounting | Overview and clusters gated | All 22 policies use the accounting concern | No separate write-service assertion found | No package routes |
| Invoices | Clusters gated | All 16 policies use the invoice concern | No separate write-service assertion found | No package routes |
| Payments | No separate UI | No policies | `PaymentRegistrar` checks both Accounts and Payments | No package routes |
| Products | Resource navigation follows policy | All 6 policies use `ProductsAccess` and the product company | Root-category provisioning intentionally remains ungated infrastructure | API guarded centrally |
| Sales | Clusters and settings pages gated | All 17 policies use the sales concern | `OrderWorkflow` asserts Sales; `Invoicer` methods do not assert at their service boundary | API guarded centrally |
| Purchases | Clusters and settings pages gated | All 12 policies use the purchases concern | `OrderWorkflow` asserts Purchases; `Biller::createBill()` has no service assertion | API guarded centrally; signed vendor-response web route is unguarded (finding below) |
| Inventories | Overview and clusters gated | All 24 policies use the inventory concern | `MoveConfirmer` asserts Inventories | API guarded centrally |
| Manufacturing | Clusters gated | All 11 policies use the manufacturing concern | `OrderWorkflow` asserts Manufacturing | No package routes |
| Maintenance | Clusters gated | All 5 policies use the maintenance concern | No separate write service found | No package routes |
| Logistics | Dashboard, clusters, pages and widgets use `LogisticsAccess` | Shared Logistics policies check entitlement and the company switch | State-changing logistics services call `ensureEnabled()` | Public POD web flow checks through its service |
| Barcode | No Filament navigation | No policies | No separate service boundary found | Authenticated web routes use `plugin.entitlement:barcode`; login remains public |
| Employees | Clusters gated | All 10 policies use the employee concern | No separate write service found | No package routes |
| Recruitments | Page and clusters gated | All 15 policies use the recruitment concern | No separate write service found | No package routes |
| Projects | Dashboard and clusters gated | All 8 policies use the project concern | No separate write service found | API guarded centrally |
| Timesheets | Resource navigation follows policy | Its policy uses the timesheet concern | No separate write service found | No package routes |
| Time Off | Pages and clusters gated | All 7 policies use the time-off concern | No separate write service found | No package routes |
| Website | Customer portal middleware exists but is not registered or referenced | No website entitlement policy found | No separate write service found | Customer-panel routes have no website entitlement middleware (blocker) |
| Analytics, Fields, Full Calendar, Table Views, Partners, Plugin Manager, Security, Support, Chatter | Exempt core plugins; no entitlement gate added | No entitlement policies added | No entitlement service gates added | Shared API middleware short-circuits configured exemptions |

Findings and open pre-deploy risks:

1. **Website entitlement is not enforced on the customer portal.**
   `EnsureCustomerPortalIsEntitled` has no reference outside its class, and
   `CustomerPanelProvider` does not include it in panel middleware. A customer
   whose Website grant is revoked can still reach the authenticated portal.
2. **A signed Purchases route mutates after revocation.**
   `plugins/webkul/purchases/routes/web.php` sends the signed vendor-response URL
   to `RespondQuotation`; its `mount()` updates the order directly without
   `PurchasesAccess` or middleware. Signature validation does not check the
   current module grant.
3. **Two write-service boundaries lack explicit entitlement assertions.**
   Sales `Invoicer::invoiceOrder()` / `createInvoice()` and Purchases
   `Biller::createBill()` mutate records without asserting their owning module.
   Their current Filament callers have gated resources. No separate route or
   queued-job caller was found in the read-only audit, so this is an incomplete
   SAAS-04 service-boundary guarantee rather than a confirmed externally
   reachable bypass; review and pin the intended boundary before claiming full
   coverage.
4. **Core exemption configuration omits Contacts.** `contacts` is a core plugin
   with no current API route or entitlement reference, so it is not gated today;
   it is nevertheless absent from `plugin-manager.entitlements.exempt`, unlike
   the documented core set. This is not a current access break, but a future API
   route would inherit a non-exempt guard; reconcile the manifest before adding
   Contacts entitlement checks or routes.

**Resolutions, 2026-11-05 (Claude, after the user asked for the gaps to be
fixed):**

- **Finding 2 (Purchases signed link): fixed.**
  - `RespondQuotation::mount()` now answers **404** before writing, unless
    `PurchasesAccess::enabledForCompany()` allows the order's own company.
    404 matches how a revoked invitation link answers.
  - Test: `PluginEntitlementTest` "refuses a signed vendor response once
    Purchases is revoked for the order’s company". It also checks that
    re-granting Purchases restores the link.
- **Finding 1 (Website portal): not wired. This needs a decision, so it is
  not a deploy blocker.**
  - A read-only production check found the `website` plugin **not installed**,
    and `partners_partners` has no password column, so production has no
    customer portal and no customer sign-in today.
  - Wiring `EnsureCustomerPortalIsEntitled` as written would refuse every
    customer whose contact has no company. Those are the shared contacts that
    §4k deliberately keeps, so it would lock them out of the portal.
  - Decide before installing Website: which company's grant governs a shared
    customer, or whether a shared customer may sign in at all. Then register
    the middleware in the customer panel's auth middleware, never globally,
    because the login page must stay reachable.
- **Finding 3 (Invoicer/Biller service boundaries)** and **finding 4
  (`contacts` not in the exempt list)** are unchanged and open, as described.
  *Superseded the same day, see below.*

**User decisions and fixes, 2026-11-05 (later the same day):**

- **Finding 1, decided: a shared customer may always sign in.**
  - `EnsureCustomerPortalIsEntitled` now lets a customer with no company
    through. A company's own customer still follows that company's Website
    grant.
  - The middleware is **still not registered**. The customer panel has no
    `Authenticate` middleware at all, so registering it means adding sign-in
    enforcement to the panel. That is a change in its own right, and it can be
    verified only with Website installed.
  - Do both together when Website is installed.
- **Finding 3: fixed.**
  - `Sale\Services\Invoicer::invoiceOrder()` and `createInvoice()` now call
    `SalesAccess::assertForCompany()`.
  - `Purchase\Services\Biller::createBill()` now calls
    `PurchasesAccess::assertForCompany()`.
  - The record's company decides, and the existing super-admin bypass is kept.
- **Finding 4: fixed.**
  - `contacts` is in `plugin-manager.entitlements.exempt`.
  - The company Modules list still shows it as a tickbox, as it already does
    for the other exempt plugins that are installed.
- **Tests:**
  - `PluginEntitlementGuardTest`: "treats contacts as always available…" and
    "lets a shared customer through the portal guard…".
  - Sales `PluginEntitlementTest`: "refuses to invoice an order once Sales is
    revoked…".
  - Purchases `PluginEntitlementTest`: "refuses to bill an order once
    Purchases is revoked…".
- **Verification run:** the three files, each on its own new database
  (`aureuserp_testing_d4_*`), all passed with none failed:
  - plugin-manager guard test: 9 passed, 20 assertions;
  - Purchases: 3 passed, 31 assertions;
  - Sales: 2 passed, 19 assertions.

  Pint passed.

  The full suites were then run, with logs in `storage/logs/deploy-check/d5/`.
  SaleFeature: 148 passed, 459 assertions. PurchaseFeature: 192 passed, 535
  assertions. Neither had a failure.

  PluginManagerFeature: 44 passed, 137 assertions, none failed. That is the
  earlier 42 plus the two new tests. Logs are in `d6/`.

No new entitlement check was found inside a plugin's `Plugin::register()`.
Newly added page checks use explicit entitlement checks; the inspected
`parent::canAccess()` overrides in Inventory, Sales, Purchases and Projects
pre-date this rollout. Barcode and Website have no feature tests; no tests were
invented, as requested.

#### Remaining suites — 2026-11-06 (other agent)

Ran the requested remaining suites serially in pairs, each against its own fresh
local Sail database. Logs are in `storage/logs/deploy-check/a7/`. Account,
Inventory, Logistics and Accounting suites/databases belonging to the other
agent were left untouched.

| Suite | Passed | Failed | Assertions | Seconds | Database |
|---|---:|---:|---:|---:|---|
| ProductFeature | 276 | 0 | 640 | 5424.42 | `aureuserp_testing_a7_product` |
| PartnerFeature | 92 | 0 | 205 | 1428.65 | `aureuserp_testing_a7_partner` |
| ProjectFeature | 87 | 0 | 208 | 1579.31 | `aureuserp_testing_a7_project` |
| ManufacturingFeature | 43 | 0 | 113 | 1515.53 | `aureuserp_testing_a7_manufacturing` |
| EmployeeFeature | 8 | 0 | 16 | 344.52 | `aureuserp_testing_a7_employee` |
| TimeOffFeature | 1 | 0 | 5 | 322.70 | `aureuserp_testing_a7_timeoff` |
| RecruitmentFeature | 1 | 0 | 5 | 443.65 | `aureuserp_testing_a7_recruitment` |
| InvoiceFeature | 1 | 0 | 5 | 645.95 | `aureuserp_testing_a7_invoice` |
| TimesheetFeature | 0 | 1 | 3 | 450.15 | `aureuserp_testing_a7_timesheet` |
| MaintenanceFeature | 1 | 0 | 5 | 351.68 | `aureuserp_testing_a7_maintenance` |
| ChatterFeature | 10 | 0 | 23 | 845.12 | `aureuserp_testing_a7_chatter` |
| PlatformBillingFeature | 19 | 0 | 46 | 811.86 | `aureuserp_testing_a7_platformbilling` |
| TableViewsFeature | 1 | 0 | 2 | 111.70 | `aureuserp_testing_a7_tableviews` |

**Timesheet failure, reproduced on an isolated retry:**
`Plugins\\webkul\\timesheets\\tests\\Feature\\PluginEntitlementTest` —
`Failed asserting that false is true.` at `PluginEntitlementTest.php:27`, where
the test expects `Gate::forUser($user)->allows('viewAny', Timesheet::class)` to
be true. The focused file rerun on fresh database
`aureuserp_testing_a7_timesheet_retry` failed with the same exception (1 failed,
3 assertions, 511.24 seconds).

*Cause, 2026-11-07 (Claude): a test defect, not an application one.*
- The test imported `Webkul\Project\Models\Timesheet`. Laravel resolves that
  class to Projects' policy, which checks the project permission.
- The Timesheets resource uses `Webkul\Timesheet\Models\Timesheet`. That class
  resolves to Timesheets' own policy, which carries the Timesheets
  entitlement check, so production was gated correctly all along.
- Fixed by importing the plugin's own model, with a why comment (commit
  5906f2269).
- Re-run on `develop`: TimesheetFeature passed, 1 test and 5 assertions, on
  database `aureuserp_testing_d8_timesheet`.

#### Full-suite verification — 2026-11-05 (`7bbf65741..aca1f6602`)

*Correction, 2026-11-05 (Claude): the claim below that the first batch and the
retries "did not reach Pest" is contradicted by the logs.*

- Decoded from UTF-16, `verify-AccountFeature.log`,
  `verify-InventoryFeature.log`, `verify-LogisticsFeature.log` and
  `verify-PurchaseFeature.log` show **47, 32, 7 and 9 tests passed, with
  none failed**, when the runs were stopped at about 03:23.
- They were slow, not stalled. Each suite's first test installs the ERP for
  several minutes.
- The `retry-*` logs exit 137 (killed) right after container creation. They
  also show PowerShell wrapping Docker's progress output as
  `NativeCommandError`, the PowerShell 5.1 `2>&1` trap AGENTS.md warns about.
- These partial counts are still not a suite result. The suites were re-run
  from Bash; see the block that follows this one.

#### Suite runs — 2026-11-05, Bash re-run (Claude)

Each suite ran on its own new database (`aureuserp_testing_d2_<suite>` and
`aureuserp_testing_d3_<suite>`). Logs are in `storage/logs/deploy-check/d2/`
and `d3/`.

| Suite | Result | Seconds |
|---|---|---|
| PurchaseFeature | 191 passed (533 assertions), includes the vendor-link test | 28,832 |
| SecurityFeature | 125 passed (423 assertions) | 1,283 |
| SupportFeature | 195 passed (950 assertions) | 1,892 |
| PluginManagerFeature | 42 passed (133 assertions) | 1,101 |

- **Full run abandoned.** The first attempt ran 22 suites four at a time. The
  host slept overnight, which paused Docker, so Purchase took 8 hours.
- **Stopped partway, not counted as results:** Account had 369 tests passing,
  Inventory 293 and Logistics 163, none failed, when they were stopped.
- **Not re-run this time:** the other suites. They were last green on
  7bbf65741, the 17-suite run, and are untouched by this change set except for
  Purchases.
- **Decision:** the user chose the targeted run (option 2).

All five package suites are registered in `phpunit.xml`, bringing its suite
count to 22. The four-suite first batch was interrupted before Pest
started: Sail Laravel bootstrap processes became stuck in uninterruptible host
filesystem I/O, with idle sessions on their isolated PostgreSQL databases. A
retry after clearing `storage/installed` showed the same pre-Pest stall. No
suite footer was produced, so these are **not test failures and have no valid
test/assertion counts**. The four incomplete containers were stopped; the
protected `aureuserp_testing_admin_pw` container was left running and untouched.

| Suite | Result | Tests / assertions | Database |
|---|---|---:|---|
| AccountFeature | Blocked before Pest startup; bootstrap stalled | N/A | `aureuserp_testing_verify_account` |
| AccountingFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_accounting` |
| InvoiceFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_invoice` |
| InventoryFeature | Blocked before Pest startup; bootstrap stalled | N/A | `aureuserp_testing_verify_inventory` |
| SaleFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_sale` |
| PurchaseFeature | Blocked before Pest startup; bootstrap stalled | N/A | `aureuserp_testing_verify_purchase` |
| ManufacturingFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_manufacturing` |
| ProjectFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_project` |
| RecruitmentFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_recruitment` |
| PartnerFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_partner` |
| ProductFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_product` |
| SupportFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_support` |
| TableViewsFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_tableviews` |
| TimeOffFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_timeoff` |
| TimesheetFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_timesheet` |
| MaintenanceFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_maintenance` |
| SecurityFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_security` |
| LogisticsFeature | Blocked before Pest startup; bootstrap stalled | N/A | `aureuserp_testing_verify_logistics` |
| EmployeeFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_employee` |
| PluginManagerFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_pluginmanager` |
| ChatterFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_chatter` |
| PlatformBillingFeature | Not started; first batch blocked | N/A | `aureuserp_testing_verify_platformbilling` |

The initial four commands used unregistered shorthand suite names and returned
“No tests found”; those attempts ran zero tests and are excluded. Correctly
named first attempts and the retries did not reach Pest. Logs are under
`storage/logs/deploy-check/` (ignored). All 22 dedicated databases were created
locally; no managed database was used. No contribution to the shared test
workflow library was needed: its existing guidance already covers isolated
databases, bounded batches, footer-based counts and bootstrap-marker handling.

The repeated `shield:generate --all` calls are caused by the plugin install
command itself: `InstallCommand::handle()` unconditionally calls
`regenerateAdminPanelPermissions()` after each plugin install, and that method
shells out to Shield. `TestBootstrapHelper::ensurePluginInstalled()` invokes
the plugin install command the first time each test process needs that plugin;
plugin dependencies can install more than one package in the same bootstrap.
The top-level ERP installer also generates permissions once. This cost is
therefore repeated at install boundaries by design, not by each assertion.
The existing timeout bounds each call but does not deduplicate it. No
implementation change was made during this verification-only task.

**Pre-deploy verdict: blocked.** Full-suite verification did not execute, and
the Website portal and Purchases signed-response entitlement gaps above must
be resolved or explicitly accepted before rollout. Tenant-visible effect of
the audited gating code alone is unchanged access for companies retaining their
grants; revoking Website currently fails to revoke authenticated portal access,
and a previously issued signed vendor response can still update a Purchases
order. The separate company “Users and logins” screen is restricted to
Super Admin/Multi-Company Admin and does not appear for ordinary tenant users.

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

#### SAAS-06 handoff — 2026-10-28

- Owner / database: Copilot / **no database used** — suite to be run next.
  Reuse `aureuserp_testing_saas_01` (same plugin, same run).
- Status: **review**, no test executed. `php -l` clean; `pint --format agent`
  passed.
- **Seats** — `companies.seat_limit`, `unsignedInteger` nullable. **5 is the
  column default for companies created from now on; existing companies are left
  unlimited** (`null`), per review RF-1: a limit is a commercial decision not yet
  made per tenant, and applying 5 retroactively would have stopped every tenant
  already at five people from inviting anyone on deploy. `null` means unlimited
  (an approved sold state, D-SAAS-04). Setting a limit is deliberate and audited
  (`SeatCapacityService::setLimit()`), and lowering one deactivates nobody.
- **Counting** (D-SAAS-03) — one seat per active membership per company
  (administrators included); a valid pending invitation reserves one; inactive
  users and accepted/revoked/expired invitations do not count.
- **Locking** — `SeatCapacityService::withLockedCompany()` takes
  `lockForUpdate()` on the company row and runs the decision inside it. Both
  `create()` and `accept()` go through it, so two administrators inviting the
  last seat serialize instead of both succeeding.
- **Invitation lifecycle** — `expires_at`, `accepted_at`, `revoked_at`,
  `revoked_by_id`, a `pending()` scope and `isPending()/isExpired()/isRevoked()/
  isAccepted()`. **Acceptance now marks the row instead of deleting it**: the row
  is the only record that someone was invited, and a seat count needs a state to
  count. `expires_at` is backfilled from `created_at + 7 days`, the window the
  signed URL already used.
- **Duplicate protection** — a second pending invitation for the same address and
  company is refused, checked under the same lock. Deliberately *not* a unique
  database index: re-inviting after a revoke must stay possible.
- Ripple effects that had to be updated, each because it assumed the old
  behaviour: `CompanyAdminContributions` (a surviving row is no longer the same as
  pending → uses the scope), `MultiCompanyAdminTest` (asserts the invitation is
  now `accepted` rather than deleted, and reads the tenant role through
  `effectiveRoleIds()` since SAAS-01 moved it), `InvitationUniquenessCharacterizationTest`
  (inverted to assert the refusal).
- New audit events: `security.company.seat_limit_updated` (before/after),
  `security.user.invitation_revoked`.
- Not done, by design: wiring capacity into the remaining entry points (company
  onboarding, user create/activate, membership attach, imports) is **SAAS-07** -
  `assertCanAccept()` and `assertCanAttachMembership()` are provided and tested
  here. Capacity messages are English via `__('<sentence>')`; translating them is
  a SAAS-07 item because the key set is not settled yet.
- **Not provable in this suite: the two-connection last-seat race.** The lock is
  implemented and reviewed, but a real race needs two connections; the plan asks
  for it and it stays open against SAAS-14/shadow mode. Everything else in the
  acceptance list is a test here.
- Files changed:
  - `plugins/webkul/support/database/migrations/2026_10_28_000004_add_seat_limit_to_companies_table.php` (new)
  - `plugins/webkul/support/src/SupportServiceProvider.php` (registration)
  - `plugins/webkul/security/database/migrations/2026_10_28_000005_add_lifecycle_to_user_invitations_table.php` (new)
  - `plugins/webkul/security/src/Services/SeatCapacityService.php` (new)
  - `plugins/webkul/security/src/Services/UserInvitationService.php`
  - `plugins/webkul/security/src/Models/Invitation.php`
  - `plugins/webkul/security/src/CompanyAdminContributions.php`
  - `plugins/webkul/security/src/SecurityServiceProvider.php` (registration)
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/SeatCapacityTest.php` (new, 12 tests)
  - `plugins/webkul/security/tests/Feature/SaaSAdministration/InvitationUniquenessCharacterizationTest.php`
  - `plugins/webkul/security/tests/Feature/Authorization/MultiCompanyAdminTest.php`
- Tests to run, one at a time: `--filter=SeatCapacityTest`, then
  `--filter=InvitationUniquenessCharacterizationTest`, then
  `--filter=MultiCompanyAdminTest`, `--filter=CompanyAdministratorInvitationTest`,
  `--filter=PermissionCheckQueryBudgetTest` (review RF-3), then the whole
  `SecurityFeature`.
- **Stale documentation to fix in SAAS-14:** `AGENTS.md` states that
  `user_invitations` has no `accepted_at` and that "a surviving row is a pending
  invitation". That is now false - the row survives acceptance and `pending()` is
  the predicate. Docs are SAAS-14's to change, so it is recorded here rather than
  edited from this package.
- Watch for, when the suite runs: `CompanyAdministratorInviter` is untouched, but
  it creates invitations through this service, so a company whose seat limit is
  5 and which already has five members will now report `INVITE_FAILED` for its
  own onboarding invitation. That is the intended rule (a seat is a seat) and it
  is why the default of 5 matters - but it is the behaviour most likely to
  surprise, and SAAS-07 owns surfacing it as a capacity message rather than a
  generic failure.

#### SAAS-07 — onboarding and all seat-enforcement paths

**Owns:** onboarding coordinator; Security company/user Filament pages;
invitation acceptance; import/API/services identified by SAAS-00; focused tests.

**Deliver:** UI/API/programmatic company onboarding; capacity service called by
create, invite, accept, activation, membership attach and imports; after-commit
email; resend/revoke; one consistent capacity error.

**Accept:** one test per inventory path; mail failure remains recoverable;
company remains durable; no path exceeds capacity; existing administrator
invitation and user-isolation suites pass.

#### SAAS-07 handoff — 2026-10-28 (partial)

- Owner / database: Copilot / **no database used**. Status: **partial** — the
  capacity checks are wired at the entry points that already exist; the rest of
  the card is listed below and is not started.
- Why this part first: SAAS-06 shipped the seat counting, and without wiring it
  into the existing paths the limit was decorative — while the *only* path that
  did consume a seat (invitations) would have reported a full company as the
  generic `INVITE_FAILED`. Both are now real.
- Done here:
  - `CompanyAdministratorInviter` returns a new **`SEATS_EXHAUSTED`** outcome,
    checked before anything is created, with the message in all five locales
    (`security::filament/resources/company.pages.create.administrator.skipped.seats-exhausted`).
    The create page already renders `skipped.<reason>`, so the difference between
    "raise the seat limit" and "that address already has an account" now reaches
    the operator.
  - `CreateUser` checks `assertCanAttachMembership()` for every company selected
    on the form, so "add a user" cannot walk past the limit invitations respect.
  - `EditUser` checks only **newly added** companies: re-saving a form for a
    company the person already belongs to must not be blocked by a full company
    (`SeatCapacityService::assertCanAttachUser()` states the same rule for
    services and imports).
  - `SeatCapacityService::canReserve()` — the non-throwing predicate the inviter
    needs, since a saved company must not be lost to an exception.
- Still open in this package (unchanged, do not assume done):
  - one onboarding coordinator used by UI, API and programmatic company creation
    (`CreateCompany` still calls the inviter directly). **Deliberately not built
    yet:** the inviter already performs the sequence the card describes (capacity
    → invitation → mail, failure as a reported reason), so a coordinator today
    would be an empty layer. It earns its place when a second caller exists -
    the API/programmatic company-creation path - and should be written then, not
    guessed now;
  - after-commit email dispatch. Also deliberately not changed: the inviter's
    `MAIL_FAILED` outcome currently returns the accept link to the page, which is
    what makes a failed send recoverable; moving the send after commit would make
    that outcome unknowable at the time it is reported. Whoever builds it must
    keep the link reachable by another route (resend, below);
  - capacity for imports and for the remaining API/service entry points listed in
    SAAS-00's inventory;
  - the capacity message is English inside the service and now localised at the
    create-page boundary only.
- **Resend added** (`UserInvitationService::renew()` +
  `CompanyAdministratorInviter::resend()`): a pending invitation can be sent
  again with a renewed window, keeping the row and its audit trail, instead of
  revoke-and-re-invite. A non-pending invitation is refused. Both outcomes are
  reported, never thrown, because the caller holds a company that must survive.
  **UI is not built** - the resend and revoke controls belong on the Users page
  (SAAS-13, which owns screens), so the capability exists for the API and for
  that work rather than sitting half-wired on a page nobody has tested.
- Tests: `InvitationLifecycleTest` (4 tests) covers renewal, refusal of an
  accepted/revoked row, a successful resend, and a refused resend.
- Tests added to `SeatCapacityTest`: a full company reports `SEATS_EXHAUSTED` and
  writes no invitation; an existing member costs no seat while an outsider in a
  full company is refused.- Watch for: `assertCanAttachMembership()` counts *active* memberships, so
  creating a user into a company that is exactly at its limit is refused — that
  is the intended rule, and it is why existing companies were left unlimited
  (review RF-1).

#### SAAS-07 handoff — 2026-11-07 (completion)

- Existing paths covered: company creation's automatic active tenant creator membership now calls `SeatCapacityService` under its company lock; invitation renewal and revocation recheck authorization and state inside the lock, preserving or releasing pending-seat reservations. The Users page exposes resend and revoke for pending invitations scoped to the actor's assigned companies. Service authorization repeats the check because Super Admin's `Gate::before` can bypass policies.
- Files: `plugins/webkul/support/src/Models/Company.php`; `plugins/webkul/security/src/Services/UserInvitationService.php`; `plugins/webkul/security/src/Filament/Resources/UserResource/Pages/ListUsers.php`; five `security/resources/lang/*/filament/resources/user/pages/list-user.php` translations; `security/tests/Feature/SaaSAdministration/InvitationLifecycleTest.php`; `security/tests/Feature/SaaSAdministration/CompanyCreationOnboardingCharacterizationTest.php`; this plan.
- Inventory: no production company-creation API, user/membership import, or other unguarded membership service was found. Existing create/edit user, invitation acceptance, and administrator invitation paths already call the shared seat service. No partners files changed.
- Verification on local Sail database `aureuserp_testing_saas07`: focused `InvitationLifecycleTest|CompanyCreationOnboardingCharacterizationTest` **8 passed, 43 assertions** (584.93s); full `SecurityFeature` **128 passed, 448 assertions** (4033.11s); full `SupportFeature` **200 passed, 958 assertions** (2905.14s). `vendor/bin/pint --dirty --format agent` passed. `PartnerFeature` was not run because no partners files changed.
- Remaining limits: a direct `Company::create()` outside an explicit transaction can insert the company before the created observer rejects an over-limit creator membership; it does not attach an unauthorized member, but may leave a company without that creator. A future company-creation API or second programmatic caller should use an atomic onboarding coordinator. Mail remains synchronous in `CompanyAdministratorInviter` so its `MAIL_FAILED` outcome can return a recovery link; changing dispatch to after commit requires a durable recovery-link route first. The service's English capacity error is localized at the company create-page boundary only. These items are not verified complete.
- Risk and rollback: tenant creator attachments can now fail on a full company; callers must handle the capacity validation error. Revert the scoped company observer, invitation service, Users page, and translation changes together to restore prior behavior. No schema or data migration was added. D-SAAS-03 and D-SAAS-04 remain the protected seat rules.

#### SAAS-07 handoff — 2026-11-09 (onboarding and mail)

- **Status / database:** implementation and verification are complete on local Sail database `aureuserp_testing_saas07b`. No production or managed database touched.
- **Why / mechanism:** an Eloquent `created` observer can refuse a creator seat after the company INSERT. First `Company::save()` now wraps its observers and partner creation in a transaction; the existing `SeatCapacityService` still owns the actual limit and pending-reservation rule. `CompanyOnboarding` coordinates company creation and its password-backed administrator account or invitation in one outer transaction, and the Security company create page routes through it. A failed administrator setup rolls the company back rather than leaving an apparently successful onboarding with no login. Existing company edit/password behavior remains in `CompanyAdministratorAccount`.
- **Callers:** the Filament Security company create page moved to the coordinator. A search of plugin `src` found one direct `Company::factory()->create()` in Support's `HasCompanyDefault` factory concern; it is a factory fallback, not an administrator-onboarding path, and first-save transaction protection applies. Installer/model factory/test callers retain direct creation; they do not request administrator onboarding. No production company-creation API was found.
- **Mail and recovery:** `CompanyAdministratorInviter::invite()` and `resend()` register mail after the outermost commit. A send failure reports the exception and audits `security.user.invitation_mail_failed` without a password or signed link. The company's staff-only Users and logins list exposes a pending-invitation selector and copyable signed link. `pendingLink()` rechecks active Super Admin or assigned Multi-Company Admin status and the invitation's company and pending state, so a forged ID cannot cross companies. The shared capacity exception now uses translations in all five locales wherever it reaches a service caller.
- **Files:** `support/src/Models/Company.php`; `security/src/Services/{CompanyOnboarding,CompanyAdministratorInviter,SeatCapacityService}.php`; Security company `Pages/CreateCompany.php` and `RelationManagers/UsersRelationManager.php`; five `security/resources/lang/*/filament/resources/company.php` files and five `.../company/relation-managers/users.php` files; Security onboarding, invitation-lifecycle and administrator-password feature tests; `AGENTS.md`; this plan.
- **Tests:** focused `InvitationLifecycleTest` **6 passed (31 assertions, 185.22s)**; focused `CompanyAdministratorInvitationTest` **51 passed (157 assertions, 573.89s)**; focused `CompanyOnboardingTest` **6 passed (20 assertions, 187.04s)**. `CompanyAdministratorPasswordTest` was isolated after the full suite identified two stale mail assertions: **55 passed, 2 failed (186 assertions, 967.92s)**; both assertions were corrected. Final full `SecurityFeature` on `aureuserp_testing_saas07b`: **134 passed (470 assertions, 1923.22s)**. Final full `SupportFeature` on `aureuserp_testing_saas07b`: **200 passed (958 assertions, 1587.73s)**. `vendor/bin/pint --dirty --format agent` and `git diff --check` passed. PartnerFeature was unnecessary because no partners files changed.
- **Test corrections:** `InvitationLifecycleTest` now expects no mail for a revoked invitation and expects mail after the successful Users-page resend commits. `CompanyAdministratorPasswordTest` now expects no invitation mail when setting the administrator password, and expects after-commit mail when an email-only invitation is created from the edit page.
- **Risk / rollback:** after-commit mail cannot return a synchronous `MAIL_FAILED` outcome; staff must use the copy-link action and audit entry. Existing invitations stay durable, and no password is emailed. Revert the coordinator, page, model transaction, inviter callback, recovery action, translations and tests together to restore the old synchronous flow. No schema migration was added.

#### SAAS-08 — platform-billing foundation

**Owns:** new `plugins/webkul/platform-billing/`, `bootstrap/providers.php`, its
`phpunit.xml` suite, and only its foundation migrations/models/enums/value
objects/tests. This package fixes the final plugin name and namespace.

**Deliver:** registered provider/plugin/test suite; integer-minor-unit Money;
table-name and FK/index conventions; policies denying tenant writes; migration
registration/structure tests. Do not alter tenant Accounts tables.

**Accept:** empty and production-shaped installs; Money rejects floats,
currency mismatch and overflow; migration guard and PlatformBillingFeature pass.

#### SAAS-08 handoff — 2026-10-28

- Owner / database: Copilot / **no database used**. Status: **review**, no test
  executed. `php -l` clean on all 11 files; `pint --format agent` passed.
- **The first thing to run is not a test**: adding the provider to
  `bootstrap/providers.php` without regenerating the autoloader makes every
  `/admin` request 500 with "class not found" — the exact outage mode the project
  memory records. Run, as root and without scripts:
  `docker run --rm -v "$(pwd -W):/var/www/html" -w /var/www/html --entrypoint composer sail-8.4/app dump-autoload --no-scripts`
- Plugin decided: **`platform-billing`**, namespace `Webkul\PlatformBilling\`,
  **`isCore()`**. Core because this is how the installation is paid for, not a
  module a company subscribes to: core plugins are never offered on the Plugins
  page and are exempt from the per-company entitlement gate, which is the correct
  posture for a plugin that administers tenants rather than serving one.
- `Money` — integer minor units with a currency carried on the value, immutable.
  Rejects: a third decimal at scale 2, non-decimal text, `1,50`-style commas,
  combining two currencies, and an amount or multiplication that does not fit in
  an `int` (compared as strings first, because a PHP `int` cast *saturates* rather
  than failing). Percentages round half-up once, in integer maths.
- Numbering (D-SAAS-05) — `platform_billing_document_numbers`, one row per
  document type per year, `unique(document_type, year)`, `lockForUpdate()` in the
  generator. `DEKA-INV-226-000001`; the compact year drops the third digit, so
  2026 → 226 and no two years collide (2030 → 230, 2003 → 203).
  Deliberately a separate table from the tenant `sequences`: platform numbering
  must be gap-free installation-wide, and a company resetting its own sequence
  must never disturb an invoice number DEKA has already issued.
- Policy — `DocumentNumberPolicy` denies every ability. Registered explicitly with
  `Gate::policy()` in `packageBooted()` because Laravel's namespace guessing only
  covers `App\Models`; the fork's convention is explicit registration (`support`,
  `fields`). Super Admin still works through the existing `Gate::before`, and
  Multi-Company Admin does not, because that hook only answers for bare ability
  checks.
- Deliberately **not** done, and not an oversight: plans, prices, subscriptions,
  invoices, payments and screens (SAAS-09 to SAAS-12), and no implementation of
  credit notes or tax beyond the config defaults. The foundation is the value
  object, the numbering and the policy.
- Files changed:
  - `plugins/webkul/platform-billing/composer.json` (new)
  - `plugins/webkul/platform-billing/config/platform-billing.php` (new)
  - `plugins/webkul/platform-billing/src/PlatformBillingServiceProvider.php` (new)
  - `plugins/webkul/platform-billing/src/Support/Money.php` (new)
  - `plugins/webkul/platform-billing/src/Enums/PlatformDocumentType.php` (new)
  - `plugins/webkul/platform-billing/src/Services/DocumentNumberGenerator.php` (new)
  - `plugins/webkul/platform-billing/src/Models/DocumentNumber.php` (new)
  - `plugins/webkul/platform-billing/src/Policies/DocumentNumberPolicy.php` (new)
  - `plugins/webkul/platform-billing/database/migrations/2026_10_28_000006_create_platform_billing_document_numbers_table.php` (new)
  - `plugins/webkul/platform-billing/tests/Feature/{MoneyTest,DocumentNumberTest,FoundationTest}.php` (new)
  - `bootstrap/providers.php`, `phpunit.xml` (registration and suite)
- Tests to run: `--testsuite=PlatformBillingFeature` (13 tests). They do not touch
  tenant tables, so this suite is safe to run first: it is the cheapest proof that
  the provider boots.
- Watch for: `Money::percentage()` converts the rate string through a float to
  reach integer basis points. That is deliberate (a 4-dp rate cannot drift
  meaningfully), but if a rate were ever longer than 4 decimals the value would be
  rejected by the regex first — do not add a rate with more precision without
  extending the rounding rule.

#### SAAS-09 — plans, prices and subscriptions

**Owns:** Platform Billing plan/price/subscription migrations, models, services
and tests; no screens or invoice scheduler.

**Deliver:** 1/3/6/12-month prices; included plugins; subscription status,
anchor/time zone/period/next invoice; transitions and audits; immutable terms;
approved synchronization to effective plugin and seat entitlements.

**Accept:** uniqueness/check constraints; month-end, leap-year and DST data
sets; no period drift; invalid transitions fail; plan edits preserve snapshots.

#### SAAS-09 handoff — 2026-11-07

- **Status / owner / database:** in progress; Codex; local Sail
  `aureuserp_testing_saas09` (created once and reused). No production or managed
  database was used.
- **Built:** additive plan, price and company subscription tables; explicit USD
  prices for 1/3/6/12 months; catalog included-plugin list; company foreign key
  and `BelongsToCompany` scope on subscriptions; draft records snapshot plan name,
  plugin list, currency, amount, interval, timezone and anchor. The period
  calculator derives each boundary from the original local anchor, including
  month ends, leap years and daylight saving changes. A Super Admin-only service
  creates and audits drafts without activating access or scheduling invoices.
- **Why / benefit:** D-SAAS-01 and D-SAAS-06 require explicit USD terms and
  platform-only changes. Snapshots keep catalog edits from silently changing a
  sold deal; the company scope prevents a tenant query from reading another
  company's subscription. Model `is_active` defaults mirror the database so a
  newly created price is usable before reload. Unique plan codes and
  `(plan_id, currency, interval_months)` prices, foreign keys and interval check
  constraints guard direct database writes.
- **Files:**
  `plugins/webkul/platform-billing/database/migrations/2026_11_07_000001_create_platform_billing_plans_prices_subscriptions_tables.php`;
  `src/Models/{Plan,Price,Subscription}.php`;
  `src/Services/{SubscriptionDrafts,SubscriptionPeriod}.php`;
  `src/PlatformBillingServiceProvider.php`;
  `tests/Feature/{SubscriptionTest,SubscriptionPeriodTest}.php` (paths under
  `plugins/webkul/platform-billing/`). This status row, handoff, D-SAAS-08
  decision row, D-SAAS-06 exception note and SAAS-05D correction are the plan
  edits. D-SAAS-08 is accepted and SAAS-09A is `todo`; no discount code was built.
- **Verification actually run:** `vendor/bin/pint --dirty --format agent`
  passed in Sail. Initial full `PlatformBillingFeature`: 28 passed, 1 failed,
  65 assertions; the new draft test exposed the unset model default. After the
  fix, `SubscriptionTest.php`: 5 passed, 14 assertions; final full
  `PlatformBillingFeature`: **30 passed, 74 assertions, 0 failed** (679.42 s).
  All Pest runs used `aureuserp_testing_saas09`; logs are in
  `storage/logs/deploy-check/saas09/`. SupportFeature was not run because no
  resource or structure snapshot input was changed.
- *Answered 2026-11-07 (user), see D-SAAS-09: plan changes take effect at
  the next period; plan modules are added and manual ones kept; a plan
  carries a seat limit; non-payment becomes read-only after the grace period;
  one active subscription per company; cancellation runs to the end of the
  period and can be reactivated; there is no pause state.*
- **Open questions before activation:** when does a plan or price change take
  effect for an existing tenant: immediately, at the next period, or only on an
  explicit new agreement? Should plan plugin grants replace manual grants or
  preserve exceptions and dependency grants? What seat limit, if any, is sold
  with each plan, and how does a plan change synchronize it? Which subscription
  states and transitions are permitted, including pause, cancellation and
  reactivation? These rules were not specified, so no transition or effective
  entitlement service was added. One active subscription per company is not yet
  constrained; multiple drafts are allowed.

#### SAAS-09 handoff — 2026-11-07 (activation)

- **Status:** implementation in review. D-SAAS-09 now controls the transitions; SAAS-09A was not started.
- **Files:** `plugins/webkul/platform-billing/database/migrations/2026_11_07_000002_add_subscription_activation_fields.php`; `src/PlatformBillingServiceProvider.php`; `src/Models/{Plan,Subscription}.php`; `src/Services/{SubscriptionDrafts,SubscriptionLifecycle}.php`; `tests/Feature/SubscriptionActivationTest.php`; this plan.
- **Implemented:** a PostgreSQL partial unique index allows one `active` subscription per company while retaining drafts; the migration checks for existing duplicates and has a rollback. Plan seat limits and scheduled prices are stored, and both drafts and scheduled changes snapshot their terms so later catalog edits cannot rewrite them. `SubscriptionLifecycle` alone authorizes and audits activation, next-period price changes, cancellation at period end, expiry, overdue recording and clearing, and Super Admin reactivation. Renewal advances exactly one period even when the scheduler is late. It unions plan modules with existing manual grants through `PluginEntitlementService`, which supplies dependency closure, and applies the sold seat limit through `SeatCapacityService::setLimit()`. No automatic module revocation or proration is performed. The service repeats its Super Admin check because the platform role skips policies.
- **Read-only boundary:** `SubscriptionLifecycle::isReadOnly($company)` returns true after the 10-day overdue grace period or after cancellation expires; it does not block writes yet. A single guard across Filament, APIs, services, and queued writes needs a separate rollout. Applying it to only some plugins would create inconsistent tenant behavior. Installation staff must be exempt at that future enforcement point.
- **Risks:** subscription renewal is currently explicit through `advance()`; SAAS-10's invoice scheduler must call it for each elapsed period and record overdue/payment state. Existing company-specific seat overrides are replaced when a newly sold plan takes effect, as D-SAAS-09 states. The read-only predicate alone does not enforce the prohibition on writes.
- **Rollback:** run the new migration's `down()` to drop the active-only index and new columns, then revert the lifecycle, model, provider, draft snapshot, and tests together. No production or managed database was touched.
- **Verification:** pending completion of `PlatformBillingFeature` on local Sail database `aureuserp_testing_saas09`; Pint and final diff review pending.
- **Dependency / risk:** D-SAAS-04's 10-day overdue grace and extensions need an
  overdue invoice and belong with SAAS-10 enforcement; this package does not
  make a tenant read-only. Catalog mutation has no approved service or screen
  yet; the draft service is the only write entry point added here. Draft terms
  are snapshots against catalog edits, but direct subscription updates are not
  yet frozen by a transition service. SAAS-09A can later attach redemptions to a
  company/subscription and snapshot discounts on invoices without changing this
  draft schema.
- **Rollback:** stop calling the draft service and leave existing draft rows
  intact. The migration's `down()` drops only the three new tables in child-to-
  parent order; use it only if they contain no data worth preserving. No tenant
  Accounts table or existing billing table was changed.

#### SAAS-09 handoff — 2026-11-09 (read-only rollout assessment)

- **Decision:** no write guard was added. The activation task explicitly permits
  enforcement only at a reliable shared boundary and forbids a partial rollout.
  The current application has no single boundary that covers tenant web/API
  requests, service calls, bulk writes, and queued jobs while retaining read
  interactions and exempting installation staff.
- **Evidence:** `Gate::before` is only consulted by authorization checks, so
  direct service and query-builder writes can bypass it. A model event in
  `BelongsToCompany` would cover ordinary Eloquent saves/deletes for its 108
  model users, but not bulk query-builder operations or raw database writes. A
  blanket non-GET middleware would also block normal Filament/Livewire
  interactions. Company-scoped work is additionally dispatched to jobs, where
  `CompanyScope` is skipped outside tests unless the job establishes company
  context. A repository search found 462 PHP files matching database/query
  write APIs; this is a broad inventory signal, not a count of unique tenant
  write paths.
- **Billing prerequisite:** `SubscriptionLifecycle::isReadOnly($company)` uses
  the subscription's overdue date plus ten days, or an expired/cancelled
  subscription. It has no invoice-linked self-service or Super Admin extension
  deadline. D-SAAS-04 allows both extensions, so enforcing the predicate before
  SAAS-10 models those decisions could lock a company out despite an approved
  extension.
- **Rollout remains open:** after SAAS-10 defines overdue invoices and extension
  records, inventory and enforce every tenant write boundary (web and Livewire
  actions, API controllers, direct service and bulk writes, and queued jobs).
  Keep reads available, recheck at execution time for queued work, exempt
  installation staff, and route payment/renewal/extension recovery actions
  through their approved billing rules. Add refusal-message tests for each
  boundary and cross-company tests before changing the status row to complete.
- **Files / verification:** this plan only. No application code or tests changed;
  no tests run for this assessment.

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

#### SAAS-09A handoff — 2026-11-09

- Owner / database: commit `4f6284396` / `aureuserp_testing_saas10`
- Status: review
- Implemented: discount codes and redemptions for D-SAAS-08. Percentage
  (basis points, so 1500 is 15.00%), fixed USD minor units, and free months.
  Duration is one invoice, N months, or forever. Eligibility is any company
  or a named list. A company redeems a code at most once. An optional total
  redemption limit and an optional redemption end date are enforced at
  redeem time; the end date does not stop a redemption that already exists.
  One active discount per company is a slot row, so codes do not stack.
  Super Admin creates codes for any company. A Multi-Company Admin creates
  them only for assigned companies. The company's own admin, a Super Admin,
  or a Multi-Company Admin acting for that company may redeem. Redemption
  rows are frozen by a database trigger. `calculateBeforeVat()` is the
  pre-tax amount SAAS-10 snapshots.
- Why / How / Benefit: the package had been committed and the board still
  said `todo`, with no handoff. The service, the unique keys, the slot, and
  `DiscountCodeTest` are the enforcement. Two defects found by the first
  suite run on this database were corrected in `4d7b611b9`, not as new
  product rules: `Illuminate\Support\CarbonImmutable` does not exist, so
  every N-month redemption crashed; and the global cap was reported before
  the company-specific "already redeemed" message.
- Protected decisions added or superseded: none. The gaps below are not
  enforced and were not silently closed.
- Files changed: `plugins/webkul/platform-billing/database/migrations/2026_11_09_000001_create_platform_billing_discounts_tables.php`,
  `src/Models/DiscountCode.php`, `src/Models/DiscountRedemption.php`,
  `src/Services/DiscountCodeService.php`,
  `tests/Feature/DiscountCodeTest.php`, and the provider registration.
  Commit `4f6284396` also changed project dashboard widgets and
  `CHANGELOG.md`; those are outside this package.
- Tests run with pass/assertion counts: `DiscountCodeTest` passed inside
  PlatformBillingFeature on `aureuserp_testing_saas10` (2026-11-09): 64
  passed, 4 failed, 212 assertions. The four failures are the read-only
  guard under review (`54d606a4b`), listed in the SAAS-10 handoff. The
  earlier run, before the Carbon and message-order fixes, was 49 passed,
  6 failed, 144 assertions.
- Reviewed but not executed: a two-connection race for the last redemption.
  The company row lock and the unique key exist; no second connection was
  opened.
- Failures investigated: the expired/exhausted/already-redeemed test wanted
  "already redeemed" and received "reached its redemption limit" because
  the cap was checked first. The stacking test died on the missing Carbon
  class. Both are fixed in `4d7b611b9`.
- Security/tenant/concurrency review: creation and redemption are
  authorized and audited. A company-list code is refused for a company
  that is not on the list. There is no database constraint that a
  company-list code has at least one company row; the service checks it.
- Risks and rollback: `down()` drops the discount tables and the
  immutability function. Do not run it once a redemption has been issued.
  `migrate:fresh` does not run `down()`, so the migration drops the
  function before creating it again.
- D-SAAS-08 points the code does not enforce, left for a decision:
  1. Free-month calendar. The decision names "free months" and leaves the
     calendar to the invoice run. SAAS-10 waives the next
     `discount_value` invoices after redemption. Confirm or replace that.
  2. Percentage scale. The code stores basis points (1500 = 15.00%,
     10000 = 100%). The decision does not say that.
  3. Discount-code terms can still be updated through the model. Only the
     redemption snapshot is immutable. `setActive` is Super Admin only.
  4. A company-list code is not required by the database to name at least
     one company.
  5. No two-connection test proves the last redemption cannot exceed the cap.
- Cross-package requests: SAAS-10 consumes `calculateBeforeVat()` and
  releases a one-invoice slot after the snapshot. Do not add a second
  calculator.
- Suggested next package: SAAS-10, now in review.

#### SAAS-10 — recurring invoices

**Owns:** Platform Billing invoice migrations/models/services/jobs/commands,
scheduler registration and tests; no payment/allocation files or screens.

**Deliver:** immutable invoice/lines/legal snapshots; unique period key;
locked generator; bounded due-job; retries/telemetry; dry-run; advance next date
only with successful invoice.

**Accept:** duplicate call/retry/two workers produce one invoice; failure
advances nothing; paused/cancelled skip; dates match SAAS-09.

#### SAAS-10 handoff — 2026-11-09

- Owner / database: `aureuserp_testing_saas10`
- Status: review
- Implemented: one posted platform invoice per subscription period.
  `platform_billing_invoices` has a unique `(subscription_id, period_start_at)`.
  The generator inserts inside a savepoint and treats a unique conflict as
  "already posted". It does not look first and then insert. The subscription
  row is locked for the post and the advance, in one transaction, so a
  failed advance rolls the invoice back and a retry finishes a partial run.
  A late run posts elapsed periods one at a time, capped at 24, and does
  not skip. Numbers come from `DocumentNumberGenerator`:
  `DEKA-INV-{compact year}-{six digits}`, one sequence per year, and a
  failed post does not consume the number because the counter moves inside
  the same transaction. The posted row snapshots issuer name, whether VAT
  is on, the VAT rate, subtotal, discount, tax, and total. A SAAS-09A
  discount is applied before VAT. Net 7 is dated from
  `subscription.next_invoice_at` (when the period fell due), due seven days
  later, and `grace_starts_at` is day 8. The sweep sets
  `subscription.overdue_at` to that timestamp through
  `SubscriptionLifecycle::recordOverdueFromScheduler()` and does not
  rewrite it later. `isReadOnly()` is unchanged: grace is ten days after
  `overdue_at`. A scheduled plan change is snapshotted at schedule time
  and applied on advance, with no proration. The command
  `platform-billing:invoice-subscriptions` queues one job per due
  subscription; the job has no user and reads with `withoutGlobalScopes()`
  plus the subscription's company. The schedule is daily at 02:15,
  `withoutOverlapping`.
- Why / How / Benefit: D-SAAS-04, D-SAAS-05, and D-SAAS-09. A check-then-insert
  would double-bill under overlapping schedulers. Dating Net 7 from the
  job clock would give a late backlog a fresh week on every missed month.
  `SequenceService` was not used: SAAS-08 kept platform numbers in
  `platform_billing_document_numbers` so a company resetting its own
  sequences cannot move a DEKA invoice number. The job does not log in as
  a Super Admin: `CompanyReadOnlyWriteGuard` treats the authenticated
  installation staff as exempt, and a login would make the billing run
  look like staff. A scheduled plan change still names an active Super
  Admin as the audit actor, because `PluginEntitlementService` refuses a
  null actor. That person is not logged in.
- Protected decisions added or superseded: see `AGENTS.md`, platform
  invoices, 2026-11-09. The plan's worker note says SAAS-14 owns
  `AGENTS.md`. This task's four-place rule required the entry. It does not
  replace the SAAS-14 ownership of the rest of that file.
- Files changed: commit `4d7b611b9` —
  `database/migrations/2026_11_09_000002_create_platform_billing_invoices_tables.php`,
  `src/Models/Invoice.php`, `src/Models/InvoiceLine.php`,
  `src/Services/RecurringInvoiceGenerator.php`,
  `src/Jobs/GenerateSubscriptionInvoice.php`,
  `src/Console/Commands/InvoiceSubscriptions.php`,
  `src/Services/SubscriptionLifecycle.php` (`advanceFromScheduler`,
  `recordOverdueFromScheduler`), `config/platform-billing.php`
  (`issuer.tax_enabled`), `src/Services/DiscountCodeService.php` (the two
  defect fixes), `src/PlatformBillingServiceProvider.php`,
  `tests/Feature/RecurringInvoiceTest.php`.
  Still uncommitted after that commit: the schedule hook follows the
  application instance. A process-wide flag registered the daily command
  on the first Pest application only, so a later test saw an empty
  scheduler. `schedule:list` shows `platform-billing:invoice-subscriptions`
  at 02:15.
- Tests run with pass/assertion counts: `RecurringInvoiceTest` 13 passed,
  63 assertions, on `aureuserp_testing_saas10`. PlatformBillingFeature on
  the same database: 64 passed, 4 failed, 212 assertions, 1082.05s.
  SecurityFeature on the same database, after that suite exited: 134
  passed, 470 assertions, 2214.60s, zero failures. SupportFeature was not
  run; no support file changed. `vendor/bin/pint --dirty --format agent`
  passed.
- Reviewed but not executed: two database connections racing the same
  period. The unique key and the row lock are what the single-connection
  retry test exercises. SupportFeature was not run; no support file changed.
- Failures investigated: the four PlatformBillingFeature failures come from
  `CompanyReadOnlyWriteGuard` (`54d606a4b`), which this package was told
  not to change. The guard calls `isReadOnly(now())`. Today is 2026-11-09,
  so historical January–May 2026 periods are already read-only when the
  test saves again:
  - `keeps current price until the next period and retains access after cancellation until period end`
  - `refuses an early period transition and permits Super Admin reactivation`
  - `makes overdue subscriptions read only only after the ten day grace period`
  - `refuses a read-only company Filament create action` — the assertion
    looks for `mountedActions.0.data.company_id`, and the guard reports
    `company_id`.
  The same guard refuses the invoice job once a cancelled subscription has
  reached period end, and once a company is past grace. The cancelled case
  is tested as `read-only-blocked`: no final invoice is posted and the
  subscription stays active. That is fail-closed. It is not a fix.
- Security/tenant/concurrency review: invoices and lines take the
  subscription's company, including when another company is selected.
  Tenant `viewAny` on `Invoice` is denied. Posted rows reject update and
  delete with "Posted platform invoices are immutable." The job does not
  authenticate.
- Risks and rollback: `down()` drops the invoice tables, their triggers,
  and `platform_billing_posted_invoices_are_immutable()`. It does not touch
  subscriptions, discounts, or tenant accounting. Do not run it once an
  invoice has been issued. After the first unpaid invoice, about eighteen
  days from `issued_at` (Net 7, day-8 grace start, then the existing ten
  days), the company is read-only and the job cannot post the next period
  or expire a cancellation, because the guard refuses those writes. There
  is no payment screen. A Super Admin can still call `clearOverdue`;
  there is no screen for it. D-SAAS-04 self-service and manual extensions
  are not modeled, so `isReadOnly()` cannot see them.
- Cross-package requests, needing a decision before the next package:
  1. Should platform billing's own subscription and invoice writes be
     exempt from `CompanyReadOnlyWriteGuard`, so a read-only company still
     receives the next invoice and a cancelled subscription can expire and
     be billed for the consumed period? Recommendation: yes, only those
     writes, and only after the guard review of `54d606a4b` finishes.
     This package did not change the guard.
  2. Free months: the next `discount_value` invoices after redemption are
     waived in full, and the code's duration still limits them.
     Recommendation: keep that until a calendar is specified.
  3. The scheduled plan change records the first active Super Admin as the
     audit actor and does not log them in. Recommendation: keep that until
     a named system actor exists. Do not `Auth::login()` inside the job.
  4. D-SAAS-04 extensions (one Company Admin extension per overdue invoice,
     plus a separate Super Admin extension) were assigned to this package
     by the 2026-11-09 read-only assessment and were not built. They change
     when `isReadOnly()` becomes true, which the guard calls.
     Recommendation: a follow-up after the guard review, not a silent
     change here.
- Suggested next package: do not start SAAS-11 until the questions below
  are answered.

#### Read-only guard review (`54d606a4b`) — 2026-11-09 (Claude)

- Why: the guard refused platform billing's own writes. The invoice job
  runs with no user, so a read-only company never received its next
  invoice and a cancelled subscription could not expire. In Filament the
  refusal was keyed `company_id` while the field is
  `mountedActions.0.data.company_id`, so the user saw nothing happen.
- How: `CompanyReadOnlyWriteGuard::EXEMPT_MODELS` (Subscription, Invoice,
  InvoiceLine; user decision D-SAAS-12, 2026-11-09). Every other tenant
  write stays refused. The refusal also sends a danger notification.
- Tests: `SubscriptionReadOnlyEnforcementTest` (new: "lets platform billing
  write its own subscription and invoice records for a read-only company";
  the Filament test now asserts the notification and that nothing was
  saved) plus `SubscriptionActivationTest`: 17 passed, 45 assertions, on
  `aureuserp_testing_claude`. The exemption fixes the three activation
  failures from the SAAS-10 handoff. The full PlatformBillingFeature suite
  was not re-run.
- Not changed, noted: writes through `saveQuietly()` or query-builder
  `update()`/`delete()` fire no model events and pass the guard. Each
  guarded save makes one company and one subscription query, so watch
  bulk imports. `DiscountRedemption` is still refused for a read-only
  company.
- Rollback: remove the constant and the notification line.

#### SAAS-11 — payments, allocations, credit and reversals

**Owns:** Platform Billing payment/allocation/adjustment/reversal migrations,
models/services/commands/tests; SAAS-10 invoice generation files are read-only.

**Deliver:** posted deposits/payments; signed allocations; derived balance and
unapplied credit; idempotency; deterministic locks; cross-company/currency
rejection; compensating reversals; actor/date/reference/reason audit fields.

**Accept:** partial/multi-invoice allocation, over-allocation rejection,
concurrent no-overspend, duplicate request safety, reversal net-zero, immutable
posted rows and zero-drift reconciliation all pass.

#### SAAS-11 note — 2026-11-09 (answered 2026-11-09; see D-SAAS-10)

The five questions below are answered by D-SAAS-10. They stay here so the
recommendation that was accepted is still visible.

The row has said `blocked` since `f95e5212c` (2026-10-26), the commit that
added the board. `git log -S SAAS-11` on this plan has no later commit.
D-SAAS-05 and D-SAAS-06 were accepted the same day, after that wording, and
nobody moved the row. The decisions are not the remaining blocker.

What is still undecided:

1. Must a reversal be approved by a second person? Blocking question 6
   still asks this. D-SAAS-06 says payments, credits, and reversals are
   Super Admin only, with a narrow credit-note exception for a selected
   Multi-Company Admin. It does not require four eyes. Recommendation: no
   four-eyes for the first version. One Super Admin posts a compensating
   reversal with actor, date, reference, and reason. The credit-note grant
   does not include payment reversals.
2. How does the money arrive? Phase 6 says manual payments. Neither
   D-SAAS-05 nor D-SAAS-06 names a card or bank provider. Recommendation:
   Super Admin enters the payment. No provider in SAAS-11.
3. What is the idempotency key? The accept line says a duplicate request
   must be safe and does not say where the key comes from. Recommendation:
   the Super Admin supplies it, and the database unique key is
   `(company_id, idempotency_key)`. A retry returns the existing posting.
4. A deposit and a payment are both named on the card. D-SAAS-05 says an
   overpayment stays customer credit until it is allocated or refunded.
   Recommendation: one posted money-in row. An unallocated payment is the
   deposit. A refund of unused credit is its own immutable row that
   references that credit. It does not delete the payment, and it does not
   send money to a bank.
5. SAAS-08 is `review`, and the board's depends-on does not include
   SAAS-10. Allocation needs the invoices SAAS-10 now posts, and SAAS-08
   is already in production batch 28. Recommendation: treat SAAS-08 as
   satisfied, add SAAS-10 as a dependency, and start SAAS-11 only after
   these five answers.

D-SAAS-04 extensions are not part of this package. They are listed on the
SAAS-10 handoff because they change the read-only predicate.

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

#### SAAS-15 scoping proposal — 2026-11-09 (questions answered 2026-11-09; see D-SAAS-11)

The questions at the bottom of this proposal are answered by D-SAAS-11.
They stay here. Impersonation is still out of scope.

The board note, kept as written: "When a new conpany is created. the
superadmin or company admin should have a drop dwon option whether the
account is test or commercial. If its a test he should be able to indicated
how many days or add more if days if required. after test, the account can
be switecd to commercial in that order."

Tenant impersonation stays on this row and is a separate slice. This
proposal is only the account type. It has to fit
`CompanyOnboarding::create()`, which inserts the company and its first
administrator in one transaction. A refusal rolls both back.

Proposed shape:

- Types are `test` and `commercial`.
- The person who may create the company chooses the type inside that
  transaction: a Super Admin, or a Multi-Company Admin within their
  assignment. A tenant Company Admin is not offered the choice.
  D-SAAS-06 keeps subscription terms with Super Admin, and at creation the
  tenant administrator does not exist yet.
- `commercial` is today's company. It does not by itself start a
  subscription or an invoice.
- `test` stores a day count. Adding days extends that one trial. It does
  not open a second one. A test company is not invoiced.
- Switching runs only from test to commercial. The switch chooses a plan
  and price, and billing follows D-SAAS-09 from the next period. Test days
  are not back-billed.
- Companies that already exist are backfilled as `commercial`.

Questions:

1. Who may choose the type? Recommendation: only installation staff who
   may create the company, inside `CompanyOnboarding`. Not the tenant
   Company Admin.
2. What happens when the test days end and nobody has switched it?
   Recommendation: the company becomes read-only, data stays, and
   installation staff can add days or switch it to commercial.
3. Does a test company get a subscription? Recommendation: no charge and
   no platform invoice until the commercial switch. The switch starts
   billing at the next period and does not bill the trial.
4. Can commercial be switched back to test? Recommendation: no.
5. What about companies that already exist? Recommendation: backfill
   `commercial`. Do not put them on a trial.
6. Where is the choice stored? Recommendation: `account_type` and
   `test_ends_at` on the company, written in the onboarding transaction.
   Adding days later is an audited update by Super Admin, or by a
   Multi-Company Admin for an assigned company.
7. Impersonation is already specified on this row. Recommendation: build
   the account type on its own, after the questions above are answered.
   Do not combine it with impersonation.

### Handoff template

Append this under the claimed card:

```markdown
#### SAAS-XX handoff — YYYY-MM-DD

- Owner / database:
- Status: review | blocked
- Implemented:
- Why (root cause or decision id) / How (mechanism) / Benefit:
- Protected decisions added or superseded (see `AGENTS.md`, "CHANGE RATIONALE
  AND PROTECTED DECISIONS"):
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

**Superseded/fixed outcomes (2026-11-03).** The five findings above are retained
as audit history. SAAS-01 fixed the CreateUser/EditUser Company Admin mismatch
and added the unique membership pair. SAAS-06 added invitation lifecycle and
duplicate-pending protection. SAAS-03 centralized and audited plugin grant
changes. SAAS-02 added the authorized API company selector and explicit tenant
execution context. Their focused checks and all 17 full suites are green on the
deployed code; package status remains for the user to promote.

These are in scope for the relevant phases; no fixes were made during this audit.

## Blocking product/security questions

1. Are tenant prices whole-plan prices, additive per-plugin prices, or a base plan plus plugin add-ons? Are multiple billing currencies required at launch?
2. Must a user's roles differ by company? If yes, should multi-company list views aggregate records with different permissions, or should privileged work require one selected current company?
3. Who counts toward the user limit: all active company memberships, Company Admin, invited users, DEKA staff assigned for support, API/service accounts, and users shared across several companies?
4. What happens when a company is already over a newly lowered limit, or when a subscription is overdue—block only new seats, suspend login, disable plugins, or allow a grace period?
5. What legal invoice rules apply: issuing entity, tax/VAT, numbering, currency, due terms, credit notes, and required immutable address/tax snapshots?
6. Is Multi-Company Admin authorized to change subscription, limits, plugin entitlements, payments, and reversals, or are those operations Super Admin only? Should reversals require four-eyes approval?

Until these are answered, Phase 0–2 security work can proceed, but schema/API commitments for prices, seat counting, and financial documents should not.
