# Comprehensive SaaS review: plan

Plan for `docs/completed/comprehensive-review-brief.md` (the audit brief). Written 2026-11-12.
Status: **decided 2026-11-12 (section 4); in progress: CR-1 baseline taken, CR-10 defects already fixed. Coverage re-checked against the brief 2026-11-12 (section 7).**

## 1. What the review found

The brief asks for an audit and redesign as if little existed. Most of it is
already built. The SaaS administration plan
(`docs/completed/saas-administration-billing-audit-plan.md`, packages SAAS-00 to
SAAS-15) delivered onboarding, the automatic Company Admin, invitations,
seats, plans, invoices, payments, extensions, read-only enforcement,
impersonation and the test/commercial account type. All of those are in
"review": built and tested, but not measured end to end in a browser.

So this plan does **not** restart the audit from Phase 1. It:

1. proves what exists, end to end, as the brief's acceptance list demands;
2. builds only the real gaps: per-company currencies, a provisioning status,
   default role templates, partners/resellers;
3. produces the brief's deliverables from evidence, not from a fresh survey.

## 2. Requirement map (brief phase → current state)

Evidence is the code and the SaaS plan's handoffs. "Built" means code and
tests exist; it does not yet mean a browser walk-through was recorded.

| Brief requirement | State | Evidence / gap |
|---|---|---|
| 1.1 Architecture facts (tenancy, roles, modules) | Built | Shared database, `BelongsToCompany` + `CompanyScope`, `company_plugin` grants, tenant roles inside memberships (AGENTS.md) |
| 1.2 AS-IS onboarding flow, measured | **Missing** | Never timed or click-counted. CR-1 |
| 2 Benchmark (Pilot, Odoo, Zoho, ERPNext, Stripe…) | **Missing** | CR-2 |
| 3.2 One-form company creation | Partial | Company form + Administrator section (`CompanyOnboarding`, atomic). Needs the brief's 5-field minimum and country defaults. CR-4 |
| 3.3 First user is Company Admin automatically | Built | `CompanyAdministratorInviter::administratorRoleIds()`: Company Admin + business role; existing email reused as membership |
| 3.4 Add user → role → invite | Built (partly) | Invitations with seats (SAAS-06/07). No role templates (Finance Manager, HR Manager…). CR-5 |
| 4 Super Admin "View as user" | Built | `TenantImpersonation` (SAAS-15): password re-entry, reason, 60 min, audit, session restore. User decided **nothing extra is blocked** (protected decision); the brief's "block sensitive actions" conflicts. Decision D-CR-3 |
| 5.1 Plans: modules, users, prices per period | Built | `PlanCatalog`, plan modules and seat limit applied on commercial switch |
| 5.2 Billing lifecycle (invoice, pay, credit, overdue, read-only, reactivate) | Built | SAAS-09..11, 10B. Upgrade/downgrade proration and credit notes: verify in CR-3 |
| 5.3 Partner / reseller layer | **Missing** | No reseller model. CR-8 (P2) |
| 6 Platform currency catalog | Partial | `currencies` table has `active`, `decimal_places`, `rounding`. No per-country defaults. CR-6 |
| 6.2 Company base + permitted currencies | **Missing** | Company has one `currency_id`. No permitted-currency list. CR-6 |
| 6.3 Billing vs accounting currency separation | Partial | Platform invoices snapshot their own currency; base-currency change after postings is not locked. CR-6 |
| 7 End-to-end and negative tests | Partial | Many feature tests; no recorded browser run of the 15-step journey. CR-3 |
| 8 Provisioning service, idempotent, lifecycle | Partial | Onboarding is one transaction (no half-created company). No visible status (Draft → Invited → Active…). CR-7 |
| 9 Investor scorecard and KPIs | **Missing** | CR-9 |
| 10 UI/UX specification | Partial | Screens exist (SAAS-12/13); no spec document. Folded into CR-1/CR-4 |
| 12 Deliverables 1–8 | **Missing as documents** | CR-9 assembles them |
| Existing companies preserved | Rule | Every package below ships a backfill migration and a test against existing rows |

## 3. Work packages

Order is by dependency and by what the investors will see first. Effort is
calendar days of agent work, including tests.

| ID | Package | Priority | Effort | Depends on |
|---|---|---|---|---|
| CR-1 | AS-IS walk-through, measured | P0 | 0.5 | — |
| CR-2 | Benchmark (Pilot first) | P1 | 1 | — |
| CR-3 | End-to-end acceptance run | P0 | 1 | CR-1 |
| CR-4 | Five-field company creation | P0 | 1.5 | CR-1, D-CR-1 |
| CR-5 | Role templates for company users | P1 | 1.5 | D-CR-2 |
| CR-6 | Company currencies | P1 | 3 | D-CR-4 |
| CR-7 | Provisioning status and recovery | P1 | 1.5 | CR-4 |
| CR-8 | Partners / resellers | P2 | 5–7 | D-CR-5 |
| CR-9 | Deliverables and investor demo | P0 (demo) | 1.5 | CR-3…CR-7 |
| CR-10 | Finish the SaaS administration plan | P0 defects / P1 | 3 | CR-3 for the smoke |
| CR-11 | Company activity log (added 2026-11-13, user) | P1 | 2 | Reason on reset/cancel (accounting A2b) |

### CR-1 — AS-IS walk-through, measured (P0)

- Walk the current flow in a browser on a **local** installation as Super
  Admin: create company → administrator → plan → login as the new admin →
  invite a second user. Production only for read-only observation.
- Record screens, required fields, clicks, time, confusing steps, and every
  failure, with screenshots.
- Output: AS-IS flowchart and the baseline numbers the KPIs need.
- Acceptance: a table of steps with counts; nothing claimed that was not done.

**Baseline, 2026-11-12 (read-only, production Super Admin, nothing
submitted).** Admin → Companies → New company is one page with **8 sections,
22 fields, 3 required**:

| Section | Fields (required *) |
|---|---|
| Company Information | Company Name *, Registration Number, Company ID, Tax ID, Website |
| Address Information | Street 1, Street 2, City, Zip Code, Country, State |
| Additional Information | Default Currency *, Company Foundation Date |
| Administrator | Administrator email, Administrator password |
| Account type | Account type * (test / commercial) |
| Plugins | module checklist |
| Branding | logo upload, colour |
| Contact Information | phone, mobile, email |

Findings for CR-4:
- **No plan on this form.** A subscription is assigned afterwards on another
  screen, so "create company → assign plan" is two trips.
- **No administrator name.** Only email and optional password; the account is
  named from the email.
- **Currency is required but not inferred from Country**, which sits in a
  different section above it.
- **All 8 sections are open at once**; 19 of 22 fields are optional and could
  sit under one collapsed "More details" section.
- Still to measure (needs the user's go-ahead, D-CR-6): clicks and time to a
  working first login, through the throwaway test company.

### CR-2 — Benchmark (P1)

**Findings, 2026-11-12 (documentation only; nothing observed in a live
system).**

| Area | Pilot Telematics (docs 7.10) | Odoo | ERPNext / Frappe | DEKA today | Adopt |
|---|---|---|---|---|---|
| Create client | One call/form: tax ID + **price plan required**; returns the account's first admin login and password ([creating_an_account](https://docs.pilot-gps.com/creating_an_account.html)) | Settings → Companies → New: name, address ([companies](https://www.odoo.com/documentation/19.0/applications/general/companies.html)) | First-run wizard: language and **country, which sets currency and fiscal year**; later companies via Company → New ([setup](https://docs.frappe.io/erpnext/setting-up)) | 8 sections, 22 fields, 3 required; **no plan**, no admin name | **Plan chosen on the create form** (Pilot); **country sets currency** (ERPNext; built 2026-11-12, not pushed) |
| First admin | Created with the account, credentials returned | Invitation e-mail when the user is saved | User created in the wizard | Created with the company (invite or password), Company Admin + business role | Already equal or better |
| Users and rights | Roles and rights per account; owner-only rights | Allowed companies + default company per user; record rules | Role per user | Tenant roles per membership; no role templates | CR-5 role templates |
| Pricing | Price plans per account or **per partner**, applying to new objects only ([tariffs](https://docs.pilot-gps.com/setting_up_tariffs.html)) | Plan-level licensing | — | Plans, prices, invoices, payments, credit, read-only on overdue | Already strong |
| Partners | Partner: price plan, currency, login; sub-partners; one partner price list covering its accounts ([partners](https://docs.pilot-gps.com/how_to_add_a_partner.html)) | Parent/branch companies | Many companies per site | None | CR-8: partner with a default plan that covers its clients |
| Blocking | Block / unblock users and vehicles; disabled users list | Archive users | Disable users | Read-only company on overdue; user deactivation | Already equal |

**Strongest lessons:** (1) Pilot puts the **price plan on the creation form**:
the account is commercially complete in one step. DEKA's biggest onboarding
gap. (2) ERPNext lets **country drive currency and fiscal defaults**. (3)
Pilot's partner model is a price list plus a login, with sub-partners: a
small, clear target for CR-8. Labels: everything above is *documented*; none
of it was tried in a live Pilot, Odoo or ERPNext system.

- Pilot Telematics docs (both sites) for client, contract, partner, first
  user, privileges, tariffs, balance. Then only the relevant workflows of
  Odoo, Zoho, ERPNext, Stripe Billing.
- Each claim labelled documented, observed or assumed, with links.
- Output: the brief's comparison table, and a short "what to adopt" list.

### CR-3 — End-to-end acceptance run (P0)

- The brief's 15-step journey (section 7.2) and the negative list (7.3), run
  on a local installation with Mailpit for invitation mail.
- Where a feature test already covers a step, cite it; run the browser for the
  rest. Record PASS / FAIL / BLOCKED / NOT IMPLEMENTED per step.
- Every FAIL becomes a fix with a test, recorded the four-place way.

### CR-4 — Five-field company creation (P0)

- One form: company name, plan, administrator name, administrator email;
  country optional (D-CR-1). When a country is given, currency (CR-6 table,
  GHS / ZMW / CDF), language and timezone follow it; otherwise the platform
  defaults. Seat limit and modules from the plan.
- Optional "Advanced" section: trial instead of plan, custom seats, extra
  currencies, branding, notes.
- Reuses `CompanyOnboarding::create()`; no second creation path.
- Existing companies untouched (no new mandatory column).
- Acceptance: a Super Admin creates a working company from those five fields,
  and the administrator receives the invitation.

**Built 2026-11-12 (not yet seen in a browser).** Country fills the currency
(shipped in v1.7.1). The plan is chosen on the create form and starts in the
onboarding transaction (`CompanyOnboarded` event →
`StartPlanAtOnboarding`; Super Admin, commercial only). Administrator name
appears when a password is set; an invited administrator gives their own
name on acceptance, so the field is not required (decision taken while the
user was away: requiring it would duplicate the acceptance form). Address,
Branding and Contact fold on create. Language and timezone from country were
**not** built: companies have no language or timezone column today, so it
would be a new schema for a default nobody reads yet; revisit with CR-6.
Details and tests: `docs/change-log.md` 2026-11-12.

### CR-5 — Role templates for company users (P1)

- Seeded, editable templates: Finance Manager, Accountant, HR Manager,
  Payroll Officer, Sales Manager, Inventory Manager, Logistics Manager,
  Standard Employee. Each maps to Shield permissions of the modules it covers,
  and only to modules the company has.
- Tenant roles stay inside memberships (protected). A Company Admin can never
  grant a platform-only ability (existing guard).
- Acceptance: "Add user → name, email, role → invite" in one modal.

**Blocked on a decision (2026-11-13).** D-CR-2 says each company gets its
own editable copy, but roles are installation-wide: `roles` has no company
column and Spatie keeps `(name, guard_name)` unique, so eight copies per
company cannot even share a name. Options:
- **A. Shared templates (smallest).** Eight global roles, editable only by
  installation staff; a Company Admin assigns them inside a membership as
  today. Changes D-CR-2 (a company cannot adjust its own copy).
- **B. Per-company roles.** Add `roles.company_id` (null = global), make the
  unique key `(company_id, name, guard_name)`, scope the Roles screen and
  every role picker to the current company, and refuse a role from another
  company in `TenantRoleAssigner`. Matches D-CR-2; security-sensitive and
  touches Shield's role resource, so 2–3 days with tests.
Recommendation: A now, B only if a client actually asks to edit a role.

**Built 2026-11-13, option A (decided while the user was away; reverse by
asking for B).** `Security\Services\RoleTemplateProvisioner`: eight
installation-wide roles, created by `erp:sync-permissions` on deploy only when
missing, so Roles-screen edits survive. Access per plugin is full / standard
(no delete or restore) / view. Never a security, role, plugin, bypass or
impersonation permission. Assignment is unchanged: a Company Admin can give
only a role whose permissions they hold. The "one modal: name, email, role →
invite" acceptance is the existing invitation form; not rebuilt.

### CR-6 — Company currencies (P1)

- Platform: the existing `currencies` catalog (no second table); add a
  country → default currency map.
- Company: base accounting currency (existing `currency_id`) plus a
  `company_currency` list of permitted transaction currencies, chosen by the
  Super Admin; the Company Admin picks from that list.
- Base-currency change after posted entries (D-CR-4): allowed for a Super
  Admin only after a confirmation that names how many posted entries exist;
  audited with old and new currency, actor and reason. Posted entries keep
  their own currency and rate; reports state the currency they are in.
- Separation: platform billing currency stays on the subscription/invoice
  (already snapshotted).
- Backfill: each existing company gets its current base currency as its only
  permitted currency, so nothing changes until someone adds more.
  *Superseded 2026-11-13 (agent, user away): that backfill would block
  companies already invoicing in other currencies. No backfill; an empty
  list means every active currency.*

**Part 1 built 2026-11-13:** `company_currencies`, `Currency::permittedFor()`,
staff-only "Currencies" section on the company form, applied to invoice, bill,
quotation and purchase-order pickers. **D-CR-4 built the same day:**
`Account\Services\BaseCurrencyChange` plus a Super Admin "Base currency"
section on the company edit page (change log entry). Still to do: the
API/imports and the remaining pickers.
- Acceptance: Ghana GHS + USD/EUR, Zambia ZMW + USD/ZAR, DRC CDF + USD/EUR
  configured and used on an invoice; a base-currency change after posting
  asks for confirmation and is audited.

### CR-7 — Provisioning status and recovery (P1)

- A visible company lifecycle, separate from subscription state: Draft →
  Invited (admin has not logged in) → Active → Suspended → Closed, derived
  where possible (invitation accepted, read-only) rather than stored twice.
- Companies list filter "Invited, not yet active" and a "resend invitation"
  action (links already exist in Users and logins).
- Acceptance: no company shows Active while its administrator cannot log in.

**Built 2026-11-13 (not yet seen in a browser).** Derived status column,
filter and "Resend invitation" on the companies list, installation staff
only (`CompanyProvisioningStatus`). Draft was dropped (a company exists only
once created); Suspended is left to the billing screens so security does not
depend on platform-billing. Details: `docs/change-log.md` 2026-11-13.

### CR-8 — Partners / resellers (P2)

- DEKA → Partner → client companies. A partner user can create companies and
  assign plans within a partner price list; sees commercial status only, if the branch feature can work we may leave it that way for now. That way the multicompany admin can be termed as a reseller to reduce the work load at this moment.
  never the client's accounting, HR or payroll records.
- Largest package; needs its own design document first.

### CR-10 — Finish the SaaS administration plan (P0 defects, P1 rest)

Decided 2026-11-12 (D-CR-7). Every SAAS package is built but in "review".

1. **Defects first (P0): both already fixed (checked 2026-11-12).** The
   SaaS plan's "Known related defects" were written before the fixes:
   `EditUser::mutateFormDataBeforeSave()` now gives a promoted Company Admin
   `global` (pinned by `ResourcePermissionMismatchCharacterizationTest`), and
   security migration `2026_10_28_000001` adds the unique `(user_id,
   company_id)` on `user_allowed_companies` (refusing to run over duplicates).
   Nothing to build.
2. Seat checks on the remaining entry points (imports, API, services) listed in
   SAAS-00's inventory, through `SeatCapacityService`, no second rule.
3. Operational controls: alerts (scheduler not running, invoice generation
   failing, oldest due subscription) and a reconciliation report recomputing
   invoice balances and credit from immutable rows.

   *Checked 2026-11-13: nothing to build.* Every path that writes
   `user_allowed_companies` already goes through `SeatCapacityService`: user
   create/edit pages, invitation create and accept, company creator attach,
   `CompanyAdministratorAccount`. There is no user API and no import of
   users; employees do not create users. `erp:install` attaches only the
   platform administrator, who uses no seat (D-SAAS-03).
   *Item 3 built 2026-11-13:* `php artisan platform-billing:health-check`,
   read-only, exits non-zero on balance drift (through
   `PlatformPayments::reconcile()`) or an active subscription more than a day
   past `next_invoice_at` (scheduler or invoice job stalled). Not scheduled
   yet: wiring it to an alert is a deploy choice.
4. *Done 2026-11-13:* operator guide `docs/saas-operations.md`; the stale
   `accepted_at` note in `AGENTS.md` was already marked superseded.
   SAAS-14 documentation, including the stale `AGENTS.md` note it owns.
5. Browser smoke (done inside CR-3); mark each SAAS package "done" as it
   passes.

### CR-11 — Company activity log (P1)

*Added 2026-11-13 at the user's request: "every company may need an activity
section to find out who changes what, who deleted what." Order agreed: after
the accounting reset/cancel reason (accounting plan, A2b), before A7.*

*Built 2026-11-13 (awaiting push): `security/.../Settings/Pages/ActivityLog.php`
with filters, Excel export (`ActivityLogExporter`), five languages. Two
defects found and fixed at the source while auditing (step 1): deletes were
never recorded (`HasLogActivity` dropped events without field changes), and
every chatter message took the actor's session company instead of the
record's (`HasChatter::addMessage`). Reasons for reopening a document now
carry an event, so they appear too. Not done from step 1: the coverage list
of models without `HasLogActivity` and the backfill of old rows with a null
company; old rows with no company simply stay out of the log (safe default).
Tests: `security/tests/Feature/Authorization/ActivityLogTest.php`.*

**Already there (reuse, no second log).** `HasLogActivity` on 25 models
writes create / update / delete rows to `chatter_messages` (`company_id`,
`causer`, `event`, `properties` old → new). `SecurityAuditLogger` records
user, role, password and impersonation events. Today both are readable only
per record, so a deleted record's history cannot be found.

**Build.**
1. *Audit first (half a day).* List models without `HasLogActivity` that
   matter (manual journal entries, settings, lock date, roles) and add it to
   the important ones. Count `chatter_messages` activity rows with a null
   `company_id`; backfill from the owning record where it can be read, and
   report the rest.
2. *Page.* "Activity Log" in Settings: one table over activity messages and
   security audit rows. Columns: when, who, module, record (name kept for
   deleted records), action, changes (old → new), reason. Filters: user,
   module, action, date range. Click opens the record when it still exists.
   Export to Excel. Read-only: no edit or delete anywhere.
3. *Access.* Company Admin of that company plus a new page permission;
   always filtered to the active company, read scope-free by `company_id`
   (an actor's scope must not hide or widen it). Installation staff see all
   companies in "All companies" mode. Module switch does not apply (core).
4. *Tests.* Two-company isolation; a deleted record still listed; reason
   shown; export scope equals the filtered table
   (`getTableQueryForExport()`); generated page permission asserted.

**Not now.** Retention or archiving (keep everything until storage says
otherwise); a field-by-field diff for every model (only logged attributes).

### CR-9 — Deliverables and investor demo (P0 for the demo)

- Assemble the brief's eight deliverables from CR-1…CR-7 evidence.
- Demo script: the brief's ten steps, on real features only, with a scorecard
  of measured KPIs (time to provision, steps, first-login time).

## 4. Decisions (user, 2026-11-12)

| ID | Question | Decision |
|---|---|---|
| D-CR-1 | Is country required on the new-company form? | **Optional.** When given, it fills currency, language and timezone; when empty, the platform defaults are used. Four required fields: company name, plan, administrator name, administrator email |
| D-CR-2 | Ship role templates? | **Yes, editable per company:** each company gets its own copy of the eight roles and its admin can adjust them |
| D-CR-3 | Block sensitive actions while impersonating? | **No; keep the earlier decision:** nothing extra is blocked, every write is logged with the real administrator's id, and the banner shows the session |
| D-CR-4 | Base currency after posted entries? | **Allow with a warning.** A Super Admin may still change it after confirming a warning that names the posted entries; the change is audited (old and new currency, actor, reason). Posted entries keep their own currency and rate |
| D-CR-5 | Partners/resellers when? | **After the investor demo** (P2) |
| D-CR-6 | Where does CR-3 run? | **Local, plus one throwaway company in production** with account type "test", invitations only to an address the user provides; deleted or closed afterwards |
| D-CR-7 | Finish the SaaS administration plan inside this one? | **Yes, as CR-10** |

## 5. Order and estimate

CR-10 defects → CR-1 → CR-3 → CR-4 → CR-9 demo draft (about 5 days) gives the
investors a measured, working one-form onboarding. Then CR-6, CR-5, CR-7 and
the rest of CR-10 (about 8 days). CR-2 runs alongside. CR-8 after the demo.

Every package follows the project rules: one suite per database, the
four-place record for each guard, backfill for existing companies, and
production pushes only after its suites are green.

## 6. Acceptance (from the brief)

| Brief criterion | Covered by |
|---|---|
| Super Admin creates a company from one simple form | CR-4 |
| First Company Admin created and invited automatically | Built; proven in CR-3 |
| Company Admin activates and logs in | Built; proven in CR-3 |
| Company Admin creates and manages a second user | Built; CR-5 improves |
| Super Admin manages subscription and module access | Built; proven in CR-3 |
| Super Admin assigns base and additional currencies | CR-6 |
| Super Admin reviews billing, payment and account status | Built; proven in CR-3 |
| Super Admin impersonates an authorized test user | Built; proven in CR-3 |
| Impersonation ends and the session is restored | Built; proven in CR-3 |
| Cross-company access and privilege-escalation tests pass | Existing suites; CR-3 adds the brief's negatives |

## 7. Coverage check against the brief (2026-11-12)

The plan was re-read against every phase of the brief. Status: **not done**.
CR-1 has its baseline; CR-10's defects are already fixed. CR-1's timing and
CR-2 to CR-9 are still open, so the plan stays in `planned/`.

These brief items were missing or only implied, and now belong to a package:

| Brief item | Added to | What it adds |
|---|---|---|
| 5.2 Commercial lifecycle, 17 steps (create → plan → invoice → pay → credit → renew → upgrade → downgrade → extra module → more seats → discount → overdue → suspend → reactivate → cancel) | CR-3 | Run as a second script next to the 15-step journey. PASS / FAIL / NOT IMPLEMENTED per step. Upgrade and downgrade proration, invoice correction, credit notes and duplicate payments are checked explicitly |
| 5.2 Six separate balances and statuses (customer account balance, invoice balance, credit balance, subscription status, payment status, operational status) | CR-3, CR-9 | Show that each is shown separately on the Super Admin billing screens. A screen that merges two of them is a CR-3 finding |
| 7.4 Three personas, measured (Super Admin, Company Admin, Partner) | CR-1, CR-3 | CR-1 measures the Super Admin. CR-3 measures the Company Admin's first login and user invite. The Partner persona waits for CR-8 (D-CR-5). Usability defects go into `docs/ux-findings.md` |
| 8 Provisioning audit events | CR-7 | One audit row per step: company created, administrator invited, invitation accepted, plan applied. The lifecycle screen reads from these rows |
| 8 Safe retries | CR-7 | "Resend invitation" and "retry provisioning" are idempotent. A second click does not create a second membership or invitation |
| 4.2 High-risk actions while impersonating | D-CR-3 kept | Nothing is blocked (user decision). CR-3 shows that a password, role or bank-detail change made during impersonation is audited with the real administrator's id. If any is not, that is a defect |
| 11 Tools inventory | CR-9 | One paragraph in the deliverables naming the tools used: Pest, Playwright MCP, Sentry, Supabase read-only. Nothing is installed for this |
| Existing companies: migration tested against existing rows, rollback | Every package | Each migration gets a test that seeds pre-existing companies, plus a written rollback in its change-log entry. This is already in the project rules and is listed here so CR-6 and CR-7 do not miss it |

No new package was needed. The estimate rises by about 0.5 day, for CR-3's
second script.
