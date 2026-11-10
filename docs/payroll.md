# Payroll — Multi-Company, Multi-Country Plan

**Status: planning only. Nothing is implemented.** The user decides when it is
built; this document is the build guide and the single payroll document.

| | |
|---|---|
| Revised | 2026-11-02 (Claude), for the user's *mandatory scope update: multi-company, multi-country payroll* |
| Supersedes | The 2026-10-26 "Payroll Plugin — Step-by-Step Implementation Plan" (Ghana-first seeding, company-owned statutory rates). Its decisions are kept below with a status; nothing is silently dropped. |
| Merged from | This file plus the scope note `REVIEW all THIS WITH SKILLs + i have.md` (kept verbatim in Appendix A). |
| Reviewed with | The `engineering:system-design` and `engineering:testing-strategy` skills, the shared library's `dekaerp-plugin-architecture` workflow, and the codebase inspection recorded in §2. |
| Branch | `feature/payroll`, from `develop`, when the user says go |
| Architecture reference | `plugins/webkul/logistics/`, the most recent plugin built end to end |

---

## Contents

1. [What changed and why](#1-what-changed-and-why)
2. [What the codebase already gives us](#2-what-the-codebase-already-gives-us)
3. [Review of the 2026-10-26 plan](#3-review-of-the-2026-10-26-plan)
4. [Requirements](#4-requirements)
5. [Architecture](#5-architecture)
6. [Decision gates](#6-decision-gates)
7. [Data model](#7-data-model)
8. [Interface](#8-interface)
9. [Implementation plan](#9-implementation-plan)
10. [Test strategy](#10-test-strategy)
11. [Country localizations and pilot validation](#11-country-localizations-and-pilot-validation)
12. [Codebase traps that will hit payroll](#12-codebase-traps-that-will-hit-payroll)
13. [Documentation to produce](#13-documentation-to-produce)
14. [Open questions](#14-open-questions)
15. [What to revisit as payroll grows](#15-what-to-revisit-as-payroll-grows)
16. [Adversarial review, 2026-11-02](#16-adversarial-review-2026-11-02)
17. [Odoo payroll check](#17-odoo-payroll-check)

- [Appendix A: The scope note, verbatim](#appendix-a-the-scope-note-verbatim)
- [Appendix B: Sources to research per country](#appendix-b-sources-to-research-per-country)
- [Appendix C: Standard company payroll items](#appendix-c-standard-company-payroll-items)

---

## 1. What changed and why

DEKA ERP is piloted in **Zambia, Ghana and Congo**, by unrelated client
companies. The first plan treated payroll as one company's configuration:
statutory rates were seeded into each company's own pay components, and a
company administrator could change any percentage, statutory ones included.
That breaks in three ways:

1. A statutory update (a new PAYE table) would have to be typed into every
   company by hand, and a company that missed it would underpay tax.
2. A company could set its pension or tax rate below the legal minimum,
   which the scope forbids.
3. A country's rules would be copied company by company, so nothing keeps
   Ghana's rules out of a Zambian payroll.

The new scope separates three things that the first plan mixed together:

```
 PLATFORM (DEKA staff)        COUNTRY (localization package)      COMPANY (tenant)
 ────────────────────────     ──────────────────────────────      ────────────────────────
 which countries exist        tax bands, social security,         employers, pay groups,
 package lifecycle and        pensions, levies, filing rules,     structures, allowances,
 publishing, with audit       sources, effective dates,           policies, accounting,
                              verified status                     approvals, payslips
            │                              │                                 │
            └──── publishes versions ──────┘                                 │
                                           └──── read-only, by date ─────────┤
                                                                             ▼
                                                 SHARED ENGINE: calculates one run for
                                                 one employer, one jurisdiction, one pay
                                                 group, one period and one currency
```

**One shared engine, separate country packages, company-owned policy.** The
rest of this document follows from that sentence.

---

## 2. What the codebase already gives us

Inspected 2026-11-02, so the design extends what exists instead of assuming.

| Concept the scope uses | What DEKA has today | Consequence for payroll |
|---|---|---|
| Tenant | A **`Company`**. `CompanyScope` filters by `company_id`; each client is one company (or a family of companies). | Payroll data is company-scoped like everything else. |
| Branch | A **child `Company`** (`companies.parent_id`, `Company::branches()`, `isBranch()`), with its own `company_id` scope. Production has **no** branches today (checked 2026-10-30). | A branch is its own scope. Whether it is also its own *legal employer* is a business fact, not a code fact: DP-11. |
| Legal company | The company row carries `registration_number`, `tax_id`, `country_id`, `currency_id`. | The legal identity exists. Payroll adds the **employer registrations** each jurisdiction requires (for example an SSNIT or NAPSA employer number) on a payroll employer record. It never infers them. |
| Module on/off per company | `company_plugin` grants plus `PluginAccess` / the SAAS-03/04 entitlement contract (`PluginEntitlement`, `plugin.entitlement:<plugin>` middleware). | "Enable payroll for this company" **is** a plugin grant. No second toggle (corrects the old Step 1.8). |
| Per-company permissions | Per-membership tenant roles (SAAS-01: `company_membership_roles`, `hasPermissionInCompany()`). Adoption in policies is SAAS-05/13 work, not yet done. | "Multi-company users need explicit access to each company's payroll" depends on this. Payroll policies use `hasPermissionInCompany()` from day one. |
| Employees | `employees_employees` has `company_id`, `department_id`, `job_id`, `work_location_id`, `bank_account_id`, `country_id`, `ssnid`, `identification_id`, `employee_type`. | Reuse them. Payroll adds effective-dated *employment terms*; it does not duplicate the employee. |
| Cost centres | The `analytics` plugin (analytic accounts on `accounts_account_move_lines`). | Cost-centre allocation maps to analytic accounts; there is no new cost-centre table. |
| Accounting | `accounts` moves and journals, `decimal(18,4)` money, `Currency::getConversionRate()`. | Posting follows `Logistics\Services\ExpensePoster` and the `MoveLine` rules in §12. |
| Money in code | `PlatformBilling\Support\Money` (integer minor units, exact percentage since 2026-10-30). `brick/math` is present only as a Laravel dependency. | The engine must not use floats: DP-03b. |
| Currencies | GHS, ZMW and USD are **active**. **CDF (DRC) and XAF (Republic of the Congo) are seeded but inactive.** Every currency picker filters on `active`. | Congo needs its currency activated the way ZMW was (2026-10-26 migration), after DP-09. |
| Sequences | `SequenceService::ensure($code, $companyId)` for per-company numbering. | Payslip and run numbers per company. |
| Readiness and provisioning | Provisioner pattern (`Logistics\Services\CompanyProvisioner`, `CompanyCategoryProvisioner`). | The onboarding wizard and the readiness checks reuse it. |

---

## 3. Review of the 2026-10-26 plan

Findings against the new scope. **Keep** means the content is still correct
and is carried into this document.

| # | Finding in the old plan | Severity | Resolution |
|---|---|---|---|
| F-01 | Statutory items (PAYE, SSNIT, NAPSA, NHIMA) were seeded into **company-owned** `payroll_pay_components` with rates the company could edit ("change the percentage for any percentage-based component"). | **Critical**: breaks scope §1 ("never silently override a mandatory obligation") and §6 (statutory updates). | Statutory rules live only in versioned country packages (§5.2). A company sees them read-only, and may only *add* where the law allows (a higher voluntary pension rate, within a cap). |
| F-02 | The Ghana seed table lists **NHIL 2.5 %** as an employee payroll deduction, and **Tier 2 5 % (employee)** as a deduction separate from SSNIT 5.5 %. To our understanding, NHIL is a levy on goods and services, not on salary, and Tier 2 is carved out of the total contribution rather than added to the employee's share. **Unverified.** | **Critical** if used: would over-deduct every Ghanaian employee. | The table is withdrawn. Ghana's rules are researched from GRA, SSNIT and NPRA sources and independently validated (§11). No rate in this document is authoritative. |
| F-03 | `payroll_runs` UNIQUE(`company_id`, `period_start`, `period_end`). | High: a company with two pay groups (monthly staff and weekly workers) or two employers could not run both. | Unique per **pay group, period and mode** (`live` only); see §7.4. |
| F-04 | `payroll_jurisdictions.currency_id`: one currency per country. | Medium: the DRC is widely reported to pay in both CDF and USD (to verify). | A localization lists its **permitted pay currencies**; the employer and pay group choose within that list (§5.5). |
| F-05 | `rules_version` was a free string on run lines. | High: historical reproducibility could not be proven. | Runs store the **id of an immutable published rule version** plus a snapshot of the company policy used (§5.6). |
| F-06 | No onboarding, no readiness, and no difference between a test and a live run. | High (scope §5, §7). | Setup wizard, readiness checks per country and per employer, and `test`/`live` run modes (§5.4, §8). |
| F-07 | No controlled rollout for statutory updates; approvals would survive a rate change. | High (scope §6). | Publishing a version re-flags affected unposted runs and invalidates their approval, with an audit record (§5.7). |
| F-08 | A separate `is_enabled` toggle duplicated the plugin grant. | Medium: two switches that can disagree. | The plugin grant is the switch; a `payroll_employers` record and its readiness state hold the rest. |
| F-09 | Super admins bypass `CompanyScope`, so they would see every tenant's salaries. | High (scope §1: "do not expose tenant salaries through ordinary platform configuration access"). | Salary data gets its own access rule that a scope bypass does not satisfy: DP-12, §5.8. |
| F-10 | DP-02 "salaried only" and DP-04 "refuse mid-period changes" fixed the data model to one industry's practice. | Medium (scope §2). | The data model carries a **pay basis** (monthly, daily, hourly) and **policies** (proration, overtime, unpaid leave) from the start. **Implementation** stays monthly-first, as decided: §6. |
| F-11 | Payslips were emailed automatically after posting. | Medium: a test run must never reach an employee. | Delivery only for `live` runs; test payslips are watermarked and never sent. |
| F-12 | Totals across runs summed `total_net` regardless of currency. | Medium (scope §8: no mixing currencies). | Every report groups by currency; a consolidated total requires an explicit conversion choice (§8.4). |
| Keep | Effective-dated rows, `decimal(18,4)` storage, the pure calculator, the state machine, idempotent posting with reversal by compensating entry, per-company payslip sequence, segregated permissions (run, approve, post), the shadow-mode rollout, the traps table. | | Carried forward below, adjusted. |

---

## 4. Requirements

The scope note (Appendix A) is the source. Each requirement has an id so the
design (§5–§8) and the tests (§10) can point at it.

### Functional

| Id | Requirement | Scope § |
|---|---|---|
| R-01 | Platform maintains supported countries and versioned statutory packages; payroll availability per company comes from the existing module system. | 1 |
| R-02 | Statutory updates are published with an effective date, sources and an audit history; older versions are preserved for historical runs. | 1 |
| R-03 | A rule version records its official sources, effective dates, verification date, and supported and unsupported scenarios; verified and incomplete rules are distinguished. | 1 |
| R-04 | Each company configures its own payroll country and currencies, pay frequencies, pay groups, calendar, pay dates, structures and grades, earnings, policies, loans, advances, permitted deductions, cost centres, accounting mappings, approvals, permissions, bank export and payslip presentation. | 1 |
| R-05 | Company settings cannot disable or undercut a mandatory legal obligation. | 1 |
| R-06 | Monthly, hourly, daily and variable pay are designable; they are implemented progressively. Many structures and pay groups per company, without custom code. | 2 |
| R-07 | Industry policy is kept separate from statutory country rules. | 2 |
| R-08 | Ghana, Zambia and Congo are separate localization packages keyed by ISO code. Congo stays inactive until the jurisdiction (CD or CG) is confirmed. | 3 |
| R-09 | One run = one legal employer + one jurisdiction + one pay group + one period + one currency. Jurisdiction is never inferred from user location, language or currency. | 4 |
| R-10 | When pay currency differs from accounting currency: the rate date, precision, rounding and journal conversion are explicit. | 4 |
| R-11 | Guided onboarding in ten steps; draft setup, test payroll and live payroll are distinct; missing configuration is explained in plain language. | 5 |
| R-12 | Incomplete statutory configuration blocks finalization for that country and employer only. | 5 |
| R-13 | One company's changes never affect another company's structures, calculations, approvals, mappings, payslips or reports. | 6 |
| R-14 | A statutory update identifies affected companies and draft runs, requires recalculation, and invalidates approvals. Finalized payroll is never modified. | 6 |
| R-15 | Readiness is tracked separately per country localization and per company. | 7 |

### Non-functional

| Id | Requirement |
|---|---|
| N-01 | **Exactness:** no floating point in calculation; one documented rounding point per component; components sum to totals to the minor unit. |
| N-02 | **Reproducibility:** a finalized run recalculated from its stored snapshot gives identical figures, whatever has changed since. |
| N-03 | **Confidentiality:** salaries and bank details are visible only to users granted payroll access *in that company*. Platform staff configuring packages do not see them by default; support access is explicit and audited. Payroll is sensitive personal data under data protection law (Ghana's Data Protection Act 2012, Zambia's Data Protection Act 2021 — to confirm), so a privacy impact assessment comes before the pilot. |
| N-04 | **Scale (estimate):** pilot companies of 10–500 employees; one run is ≤ 500 lines × ~25 components ≈ 12,500 detail rows. Calculation runs synchronously up to ~200 employees and in a queued job above that, so a request never times out. |
| N-05 | **Auditability:** every publish, readiness change, approval, invalidation, finalization and reversal is recorded (actor, time, before and after), through the existing `SecurityAuditLogger`. |

### Constraints

Existing stack and conventions (AGENTS.md); no new dependency without approval;
the user commits manually; tests on local Sail PostgreSQL only; country rules
come from official sources and a qualified local payroll practitioner, never
from this document or from model knowledge.

---

## 5. Architecture

### 5.1 Components

```
plugins/webkul/payroll/
└── src/
    ├── Engine/                       SHARED, country-agnostic, no database access
    │   ├── PayrollCalculator          inputs + policy snapshot + rule version -> result
    │   ├── Money / Rate               exact arithmetic (DP-03b)
    │   ├── CalculationContext          period, pay basis, worked units, YTD
    │   └── Result/ (PayrollResult, LineResult, ComponentResult, Warning)
    ├── Localization/                 COUNTRY packages, one folder per ISO code
    │   ├── Contracts/StatutoryLocalization.php
    │   ├── GH/V2027_1/ GhanaLocalization.php + rule data + golden cases
    │   │   (one folder per released class version, never edited; §5.6)
    │   ├── ZM/V2027_1/ ZambiaLocalization.php + rule data + golden cases
    │   └── (CD|CG)/ added only after DP-09; ships inactive
    ├── Models/                       platform (no company_id) and company data
    ├── Services/
    │   ├── RuleVersionPublisher        publish, verify, roll out (§5.7)
    │   ├── ReadinessService            country + employer readiness (§5.4)
    │   ├── PayRunService               create, calculate, approve, finalize
    │   ├── PayrollPoster               ledger posting and reversal (§9 B5)
    │   ├── PayslipService, PaymentService, BankExportService
    │   └── PayrollAccess               the confidentiality rule (§5.8)
    ├── Filament/ Platform cluster (DEKA staff), Payroll cluster (company)
    └── Policies/
```

**Why code plus data for a country.** Rates, bands, ceilings and dates change
every year: that is *data* in versioned rule rows, so publishing a new year is
not a deploy. *How* those numbers combine also differs by country (what a
contribution is a percentage of, how a ceiling applies, whether family relief
reduces tax): that is *code* in the country's `StatutoryLocalization` class,
reviewed and tested per country. A rule version names the localization code
version it needs, so new data never runs against old logic.

```php
interface StatutoryLocalization
{
    public function countryCode(): string;                 // 'GH', 'ZM' (ISO 3166-1 alpha-2)
    public function permittedPayCurrencies(RuleVersion $v): array;
    public function requiredEmployerRegistrations(RuleVersion $v): array;   // drives onboarding step 3
    public function requiredEmployeeFields(RuleVersion $v): array;          // drives readiness
    public function statutoryComponents(RuleVersion $v): array;             // read-only to companies
    public function constraints(RuleVersion $v): array;                     // what a company may and may not change
    public function apply(CalculationContext $c, RuleVersion $v, LineDraft $line): LineDraft;
    public function supportedScenarios(RuleVersion $v): array;              // and unsupported ones
}
```

### 5.2 The three tiers, made concrete

| Tier | Owns | Who edits | Storage |
|---|---|---|---|
| **Platform** | The list of localizations, their lifecycle (`draft` → `in_review` → `verified` → `active` → `retired`), publishing, the audit history | DEKA staff with `payroll_platform.*` permissions | `payroll_localizations`, `payroll_rule_versions` (no `company_id`) |
| **Country** | Statutory components, bands, rates, ceilings, bases, mandatory flags, permitted currencies, required registrations, filing outputs, sources, verified date, supported scenarios | Changed only by **publishing a new version**; a published version is immutable | `payroll_rule_versions`, `payroll_statutory_rules` (no `company_id`) |
| **Company** | Employers, pay groups, calendars, structures, grades, earnings, policies, loans, deductions, cost-centre and account mappings, approvals, bank export, payslip settings | The company's payroll administrators | Company-scoped tables (§7.3), stamped and isolated like every tenant record |

**R-05, enforced in one place.** `constraints()` returns rules such as "employee
pension ≥ statutory rate", "this component is mandatory", or "voluntary pension
≤ cap". The company configuration forms validate against them, and the engine
**re-checks at calculation time** and refuses a run that violates one, so a
company cannot get round the rule through an API call, an import or a forged
Livewire payload. The super-admin `Gate::before` skips policies, so the guard
lives in the service.

### 5.3 Entities and how a run is bound

```
Company (tenant) ──1:n── PayrollEmployer ──n:1── Localization (ISO country)
                              │                         │
                              │                         └── RuleVersion (effective-dated, immutable)
                              ├──1:n── PayGroup (frequency, calendar, currency, pay basis)
                              │            └──1:n── EmploymentTerms ──n:1── Employee
                              └──1:n── PayRun (employer + pay group + period + currency + mode
                                               + rule_version_id + policy snapshot)
```

- **A payroll employer** is the legal employer for one jurisdiction. It is owned
  by one company (the legal entity) and holds that entity's registrations in that
  country. A company that employs people in two countries has **two employers**
  (R-09). The jurisdiction is chosen explicitly at onboarding (step 2). It may be
  *suggested* from the company's country, but it is never inferred from a user,
  their language or a currency.
- **Branches choose (DP-11).** Each branch is configured one of two ways:
  - **Own employer:** the branch company owns its own employer record for its
    country, with its own registrations and runs. Nothing crosses company
    boundaries.
  - **Under the parent:** the parent's employer record **covers** the branch
    (`payroll_employer_companies`), so the branch's employees are paid in the
    parent's runs for that jurisdiction. A parent in Ghana with a branch in
    Zambia covered this way has a **Zambia** employer record of its own, with
    its Zambian registrations, so one run still has one jurisdiction.
  - A company's employees are covered by **exactly one employer per
    jurisdiction**, enforced in the database.
  - Covering a branch means reading its employees from the parent's run. That
    read is scope-free and keyed by the coverage row (the §4k rule), and needs
    payroll permission in **both** companies.
- **A pay group** fixes frequency, calendar, pay basis and currency for the
  employees in it. Many pay groups per employer (R-06).
- **A run** copies the employer, jurisdiction, pay group, period, currency, mode,
  the rule version that applied on the period's reference date (DP-14), and a
  snapshot of the company policy it used. Nothing outside the run is read again
  once it is finalized (N-02).

### 5.4 Readiness and modes

```
Localization:  draft ─► in_review ─► verified ─► active ─► retired
                                     (practitioner sign-off,   (selectable by employers)
                                      golden cases pass)

Employer:      setup ─► testing ─► live          (suspended at any point, reversible)
                 │        │          │
                 │        │          └─ live runs may be approved, finalized, posted and paid
                 │        └─ test runs only: calculated, watermarked, never posted, sent or paid
                 └─ configuration in progress; the readiness checklist lists what is missing
```

- `ReadinessService::check($employer)` runs the checklist (§8.2) and returns
  plain-language findings, each with a link to the screen that fixes it.
- `setup → testing` needs the mandatory configuration. `testing → live` needs a
  passed checklist, **a localization in `active`**, and a recorded sample-run
  review (onboarding step 9) by a user with `approve_payroll`.
- **Blocking is local (R-12).** A Ghana localization in `in_review` blocks live
  finalization for Ghanaian employers only; Zambian employers and other companies
  carry on.

### 5.5 Currency (R-10)

- The localization lists its **permitted pay currencies**. The employer picks its
  allowed set from that list, and each pay group picks one. A run has exactly one
  pay currency.
- If the pay currency is the company's accounting currency, posting is direct.
- If it differs, posting converts with `Currency::getConversionRate()` (as
  purchases does since UF-022):
  - **Rate date:** the run's `pay_date` (DP-13), stored on the run as
    `accounting_rate` and `accounting_rate_date` so the posting is reproducible.
  - **Precision:** the rate is stored to 6 decimals.
  - **Rounding:** each journal line is rounded to the accounting currency's
    `decimal_places`; the residual goes to the employer's rounding account,
    so the move always balances.
  - **No rate for that date:** finalization is refused with a plain message.
    The run never posts at a silent rate of 1.
- Whether the law permits paying in that currency is a localization fact: a
  currency outside the permitted list is refused.

### 5.6 Reproducibility (N-02)

- A rule version is **immutable once published**. A correction is a new version
  with a later `published_at`, linked to the one it supersedes.
- Each run stores `rule_version_id` and `policy_snapshot` (JSON: the structures,
  components, rates, overrides, policies and accounting mappings actually used,
  plus a hash).
- Run lines store every input (base pay, worked units, one-off items, YTD
  carried in) and every component result.
- `PayRunService::reproduce($run)` recalculates a finalized run from its snapshot
  and compares it to the stored lines. A difference is a defect and is logged.
- **The calculation code is pinned too, not just the data** (added 2026-11-02 after
  the adversarial review, §16). Immutable data alone does not make history
  reproducible: after any bug fix or refactor in `GhanaLocalization`, a finalized
  run recalculated with the new code would diverge. So:
  - each country's classes are **versioned side by side**, for example
    `Localization/GH/V2027_1/GhanaLocalization.php`;
  - every rule version pins exactly one class version, and every run records both;
  - **a released class version is never edited.** A fix is a new class version
    plus a new rule version, and the rollout (§5.7) decides which unfinalized
    runs move to it. A test compares each released class file with a hash
    manifest and fails on any edit;
  - a released class version is deleted only when no finalized run references
    it, which in practice means never during the statutory retention period.

### 5.7 Statutory rollout (R-14)

```
RuleVersionPublisher::publish($version)        (DEKA staff, audited)
  1. Validate: required sources, verified date, golden cases pass, no gap or
     overlap with sibling versions.
  2. Mark published (immutable).
  3. Find affected runs: unfinalized (draft, calculated, approved), same country,
     period reference date >= effective_from.
  4. Each affected run: state -> needs_recalculation; any approval revoked, with
     an audit record (who approved, revoked by which version); the company's
     payroll administrators are notified.
  5. Finalized runs (finalized, posted, paid) are never touched. A correction
     for a past period is a NEW adjustment run that references the original.
```

Publishing never recalculates silently. The affected company sees *why* its run
needs recalculating and does it deliberately.

### 5.8 Access and confidentiality (N-03, R-13)

- **Company isolation:** every company table uses `BelongsToCompany`; children
  use `InheritsParentCompany`; no payroll company row may have a null
  `company_id` (a null row is shared with every tenant).
- **Per-company permission:** payroll policies call
  `hasPermissionInCompany($permission, $record->company_id)`. Holding a payroll
  role in company A gives nothing in company B (scope §8).
- **Salary data is not configuration data (DP-12, decided 2026-11-02):**
  `PayrollAccess::canViewPayData($user, $companyId)` is required to read run
  lines, terms, payslips, payments and bank details. It is satisfied by exactly
  three cases:
  1. a **main super admin**: sees everything;
  2. a user holding **`view_payroll_pay_data`** for that company. The right is
     granted to DEKA support admins (all companies) and to Multi-Company Admins
     (only their assigned companies, checked with `hasPermissionInCompany()`);
  3. a company user with the company's own payroll permissions in that company.

  A scope bypass, or platform permissions to edit localizations, do **not**
  satisfy it on their own: someone who maintains Zambia's tax tables does not
  thereby see a client's salaries. **Every read of pay data by cases 1 and 2 is
  logged** (user, company, record, time) through `SecurityAuditLogger`, so the
  right stays accountable. The right is its own Shield permission, so it is
  never granted by accident with a broader role.
- **Segregation of duties (DP-07, kept):** `run_payroll`, `approve_payroll`,
  `post_payroll` (plus `finalize_payroll`); the approver of a run may not be
  the user who calculated it (configurable per employer, on by default).

### 5.9 Trade-offs

| Decision | Chosen | Alternative | Why, and the cost |
|---|---|---|---|
| Country rules | Versioned data + a reviewed class per country | Formula language that admins edit | Formulas differ in structure, not just numbers; a DSL is a large build and puts legal logic in an editable field. **Cost:** a deploy for structural law changes (rare); yearly rate changes are data only. |
| Statutory vs company | Statutory read-only to companies, with explicit constraints | Copy statutory rows into each company (old plan) | Copies drift and allow undercutting. **Cost:** a company cannot fix a wrong statutory rate itself; DEKA must publish, which is the point. |
| Reproducibility | Snapshot on the run | Recompute from current config | Configuration changes daily; snapshots make history immutable. **Cost:** storage (a few kB per run). |
| One jurisdiction per run | Yes (scope §4) | Mixed-country runs | Statutory logic cannot mix. **Cost:** two runs for a two-country employer family. |
| Salary confidentiality | Separate rule that ignores scope bypass | Rely on `CompanyScope` | Super admins bypass the scope by design. **Cost:** DEKA support needs a grant to help with a payslip. |
| Calculation | Synchronous ≤ ~200 lines, queued above | Always queued | Small pilots get an immediate result. **Cost:** two code paths, sharing one service. |

---

## 6. Decision gates

The 2026-10-26 decisions are kept with a status; new ones follow. A decision
marked *needs user* blocks only the step it names.

| # | Question | Decision / status |
|---|---|---|
| DP-01 | Which countries first? | *Superseded 2026-11-02:* not "seed both" but **separate localization packages**. Order: shared engine, then **Zambia first, Ghana second** (*amended 2026-11-02, user: the first pilot is Zambian*). Then both Congos (DP-09). |
| DP-02 | Where do hours come from? | **Kept for implementation:** v1 implements monthly salaried pay. *Amended:* the data model carries `pay_basis` (monthly, daily, hourly) and worked units from the start, so daily and hourly are added per verified pilot requirement without schema changes (R-06). |
| DP-03 | Money precision? | **Kept:** `decimal(18,4)` storage, matching `accounts`. |
| DP-03b | Arithmetic in the engine? | **Decided 2026-11-02 (user):** reuse the tested `PlatformBilling\Support\Money` (integer minor units, exact percentages), **promoted to `support`** so payroll and platform billing share one implementation. No new dependency. Never floats (N-01). |
| DP-04 | Mid-period pay change? | **Kept for v1 for rate changes only:** a raise or structure change inside a period is refused; the administrator splits the period. *Amended 2026-11-02 (§16):* **joiners, leavers and final pay are in v1**, prorated by calendar days, because almost any pilot has one in its first two cycles. Other proration bases (working days) are company **policy**, implemented when a pilot needs them. |
| DP-05 | Payslip delivery? | **Kept:** PDF + email, **for live runs only** (F-11). |
| DP-06 | Net pay output? | **Kept:** payment records first; bank export per company configuration in a later step (R-04). |
| DP-07 | Separate permissions? | **Kept and extended:** run, approve, post, finalize, per company (§5.8). |
| DP-08 | Reuse the `payments` plugin? | **Kept:** own payment records. |
| DP-09 | **Which Congo?** Democratic Republic of the Congo (`CD`, franc congolais) or Republic of the Congo (`CG`, CFA franc XAF), or both? | **Decided 2026-11-02 (user): both.** Two separate packages, `CD` and `CG`, each researched, verified and activated on its own (B10). Neither reuses the other's rules. Each package's currency (CDF, XAF) is activated by migration when that package starts, as ZMW was. |
| DP-10 | Is payroll a sold feature? | **Decided 2026-11-02 (user): free for pilots, paid later.** During the pilot, DEKA staff switch payroll on per company through the plugin grant. When pricing exists it moves behind the SAAS-03/04 entitlement contract and SAAS-09 subscriptions, with no change to how payroll checks availability (it always asks the grant). |
| DP-11 | Is a branch its own legal employer? | **Decided 2026-11-02 (user): the company chooses, per branch, and both must work, including branches in other countries.** A branch is either **its own employer** (its own registrations, its own runs) or **covered by its parent's employer** for the branch's jurisdiction. The model supports both from the start (§5.3, §7.2). Whether a parent may legally pay foreign-branch staff under its own registration there is a country fact the practitioner confirms; the localization can refuse it. |
| DP-12 | Can DEKA staff see a tenant's salaries? | **Decided 2026-11-02 (user):** the **main super admins see everything.** Seeing salary and bank data is otherwise a **right** (`view_payroll_pay_data`) that is granted to **DEKA support admins** and **Multi-Company Admins**; a Multi-Company Admin holds it only within their assigned companies. Ordinary platform configuration access (editing localizations) does not include it. Every view of pay data is logged (who, which company, when), so the right stays accountable. *Supersedes the recommendation of a client-granted support grant.* |
| DP-13 | Exchange-rate date when pay currency ≠ accounting currency? | **Recommended:** the run's pay date; stored on the run. Confirm with each pilot's accountant. |
| DP-14 | Which rule version applies to a run? | **Recommended:** the version effective on the period **end** date (or pay date where a country taxes on payment); the localization declares which. Never "today". |
| DP-16 | Do we check the design against Odoo payroll? | **Decided 2026-11-10 (user): yes.** A structured comparison with Odoo's payroll (§17), done in A0 and repeated before A3 and B1. Odoo is a reference for concepts, never a source of code. |
| DP-15 | Net pay below zero? | **Recommended:** refuse the line with a plain warning and let the administrator adjust (a deferred deduction is a policy option later). Never a silent floor at zero. |

---

## 7. Data model

Money columns are `decimal(18,4)`; rates are `decimal(12,6)`; every table has
timestamps. "Company" tables use `BelongsToCompany`, and their children use
`InheritsParentCompany`.

### 7.1 Platform

```
payroll_localizations                    (platform; no company_id)
├── id, country_code (char 2, unique: GH, ZM, ...)   ISO 3166-1 alpha-2
├── name
├── status  enum(draft, in_review, verified, active, retired)
├── localization_class (string)          the StatutoryLocalization implementation
├── notes, timestamps
└── status changes audited

payroll_rule_versions                    (platform; immutable once published)
├── id, localization_id
├── version_label (e.g. "GH 2027.1")
├── effective_from (date), effective_to (date, nullable)
├── reference_date_basis enum(period_end, pay_date)   DP-14
├── class_version (string, e.g. V2027_1)   the ONE released class version this data runs on (§5.6)
├── permitted_pay_currency_ids (json)
├── sources (json: [{authority, title, url, retrieved_at}])
├── verified_at, verified_by_id, practitioner_review (json: who, when, scope)
├── supported_scenarios (json), unsupported_scenarios (json)
├── status enum(draft, published, withdrawn)
├── published_at, published_by_id, supersedes_id (nullable)
├── checksum                              of the rule rows, set on publish
└── UNIQUE(localization_id, effective_from, published_at)

payroll_statutory_rules                  (platform; belongs to a rule version)
├── id, rule_version_id
├── code (PAYE, SSNIT_TIER1_EE, NAPSA_EE, NHIMA_ER, SDL_ER, ...)
├── kind enum(income_tax_band, contribution, levy, relief, threshold, other)
├── payer enum(employee, employer)
├── mandatory (boolean)
├── basis (string: gross, basic, taxable, custom)  interpreted by the class
├── band_floor, band_ceiling (nullable), rate, fixed_amount (nullable)
├── ceiling_amount (nullable), ceiling_period enum(period, month, year, nullable)
├── company_may_increase (boolean), company_max_rate (nullable)   R-05
├── account_role (string: e.g. statutory_payable.paye)   accounting mapping key
└── sort_order
```

### 7.2 Company setup

```
payroll_employers                        (company)
├── id, company_id
├── localization_id                      explicit; never inferred
├── legal_name (default: company name), registrations (json, per localization's required list)
├── allowed_pay_currency_ids (json, subset of the localization's)
├── readiness_state enum(setup, testing, live, suspended), readiness_checked_at
├── live_since, suspended_reason
├── approval_policy (json: approver ≠ calculator, number of approvers)
├── payslip_settings (json: logo, language, display options allowed by the localization)
├── bank_export_settings (json, later step)
├── rounding_account_id, accounting mappings by account_role (json or child table)
└── UNIQUE(company_id, localization_id)  one employer per owning company per country

payroll_employer_companies               (which companies' employees an employer covers; DP-11)
├── id, employer_id, company_id          the owning company itself, or one of its branches
├── localization_id (copied from the employer, for the constraint below)
├── covered_from, covered_to (nullable)
└── UNIQUE(company_id, localization_id)  a company's employees have exactly ONE employer
                                          per jurisdiction: either its own or its parent's

payroll_pay_groups                       (company, InheritsParentCompany from employer)
├── id, company_id, employer_id, name, code
├── frequency enum(monthly, semi_monthly, biweekly, weekly, daily)
├── pay_basis enum(monthly_salary, daily_rate, hourly_rate)       DP-02: monthly first
├── currency_id (in the employer's allowed set)
├── calendar (json: period anchor, pay-date rule, cut-off rule), is_active

payroll_salary_structures                (company)   name, grade, pay_group_id (nullable), is_active
payroll_pay_components                   (company; NON-statutory only)
├── code (unique per company), name
├── type enum(earning, deduction, employer_contribution)
├── category enum(basic, allowance, overtime, bonus, commission, voluntary_deduction, loan, benefit)
├── method enum(fixed, percentage, per_unit, formula_ref)
├── rate, amount, base_component_id, ceiling_amount
├── is_taxable_hint                      the localization decides the tax treatment
├── affects_statutory_basis (json)       which statutory bases it feeds (validated)
├── account_mapping_key, analytic_policy (inherit_employee, fixed_account)
└── effective_from, effective_to, is_active
payroll_structure_components             structure ↔ component, with override rate or amount
payroll_policies                         (company) kind enum(overtime, proration, unpaid_leave,
                                          attendance) + parameters (json), effective-dated
```

### 7.3 Employee terms and inputs

```
payroll_employment_terms                 (company)
├── employee_id, employer_id, pay_group_id, salary_structure_id, grade
├── effective_from, effective_to         no overlap per employee and employer (DP-04)
├── base_pay, rate_per_unit (daily or hourly; nullable)
├── statutory_identifiers (json, as the localization requires: TIN, SSNIT, NAPSA number...)
├── bank_account_id (reuses partners bank accounts), payment_method
├── cost_allocation (json: analytic account shares totalling 100 %)
payroll_employee_component_overrides    per term, effective-dated
payroll_employee_loans                  original, outstanding, instalment, start, repaid_at
payroll_period_inputs                   per run and employee: worked units, overtime units,
                                         unpaid leave days, one-off earnings and deductions
```

### 7.4 Runs and results

```
payroll_runs                             (company)
├── id, company_id, employer_id, pay_group_id
├── country_code (snapshot), currency_id (snapshot)
├── period_start, period_end, pay_date
├── mode enum(test, live)
├── state enum(draft, calculated, needs_recalculation, approved, finalized, posted, paid, cancelled)
├── rule_version_id, class_version (snapshot), policy_snapshot (json), policy_snapshot_hash
├── accounting_currency_id, accounting_rate, accounting_rate_date        R-10
├── totals: gross, employee_deductions, employer_contributions, net (in pay currency)
├── adjusts_run_id (nullable)            a correction for a finalized period
├── calculated_by_id/at, approved_by_id/at, finalized_by_id/at, posted_at
└── UNIQUE(pay_group_id, period_start, period_end, mode) WHERE mode = 'live'
     AND state <> 'cancelled' AND adjusts_run_id IS NULL      (partial unique index)

payroll_run_lines                        one per employee: inputs snapshot, gross, taxable,
                                          deductions, employer cost, net, warnings (json)
payroll_run_line_details                 one per component: source enum(statutory, company),
                                          rule code or component id, basis, rate, amount, sort
payroll_postings                         one per finalized live run (unique), move_id,
                                          reversal_move_id, reversed_at, reversed_by_id
payroll_payslips                         per line: number (per-company sequence), file, hash,
                                          is_test, sent_at
payroll_payments                         per line: amount, currency, bank snapshot, method,
                                          reference, state(pending, paid, failed), paid_at
payroll_audit                            or SecurityAuditLogger entries: publish, readiness,
                                          approve, revoke, finalize, reverse
```

**Immutability.** From `finalized` on, a run and its lines, details and
snapshot reject updates in the model (`saving` refuses) and in the service. A
reversal is a compensating move; a correction is a new adjustment run.

---

## 8. Interface

Filament conventions apply: split `Schemas`/`Tables`, Shield page permissions
`page_payroll_<page>` asserted against the generated set, and `canAccess()`
checking the permission explicitly (the `HasPageShield` trap).

### 8.1 Platform (DEKA staff): "Payroll Localizations" cluster

- **Localizations:** list of countries with status, the active version, the
  next scheduled version, and the number of employers on each.
- **Rule version editor:** draft rows, sources, supported and unsupported
  scenarios, golden-case results; **Submit for review → Verify → Publish**, each
  step recorded. Publishing shows the affected-runs preview (§5.7) before
  confirming.
- **Readiness overview:** employers per country and their state. It shows **no
  salary figures** (N-03).

### 8.2 Company: "Payroll" cluster

**Setup wizard** (`Filament\Schemas\Components\Wizard`, resumable, one step per
scope item):

| Step | What the user does | Blocks progress when missing |
|---|---|---|
| 1 | Payroll enabled for the company (the plugin grant; read-only if DEKA controls it) | Grant absent |
| 2 | Choose the legal employer details and the **payroll country**. The country is pre-selected from the region set on the company and can be changed (a company paying staff in two countries adds a second employer). Only `verified`/`active` localizations can be chosen; `in_review` shows as "not yet available" | No country chosen |

**Choosing a country is all a company does to get that country's rules (user,
2026-11-02).** Verification happens **once per country**, by DEKA, before the
country becomes selectable. It is not repeated per company. After that, any company that picks Zambia
gets Zambia's tax tables, contributions and payslip rules with no extra step.
The company supplies only what is its own: its registration numbers, pay
groups, employees and pay terms (steps 3–10). The pilot client's accountant
helps verify a country the first time; later companies in that country need
nothing of the kind.
| 3 | Employer registrations the country requires (from the localization) | Any required registration |
| 4 | Pay currencies (within the permitted list), pay groups, calendars, pay dates | No pay group |
| 5 | Salary structures, earnings, company policies (validated against the country's constraints) | Structure without a basic earning |
| 6 | Accounting mappings for every account role the country and components use; cost-centre defaults | Any unmapped role |
| 7 | Payroll roles and the approval rule | No user with `approve_payroll` other than the calculator (when segregation is on) |
| 8 | Employees: terms, statutory identifiers, bank or payment method, cost allocation | Employees missing required fields (listed by name) |
| 9 | Sample payroll: a `test` run, reviewed line by line, with a sign-off | No reviewed test run |
| 10 | Request **live**: runs the readiness check; succeeds only when everything passes and the country is `active` | Any failing check |

Findings read like "3 employees have no NAPSA number: Jane Phiri, ... →
open employment terms", never a code or a stack trace.

**Day to day:** pay runs (create, calculate, review lines, approve, finalize,
post, mark paid), employment terms (a tab on the employee), pay groups,
structures, components, policies, loans, readiness, settings. Statutory items
are shown read-only, labelled with their source and version.

### 8.3 Run screen states

- **Test** runs carry a "TEST — not for payment" banner and watermark, and have
  no finalize, post, email or payment actions.
- **Needs recalculation** shows the version that changed and what changed, with
  one action: recalculate.
- **Finalized** and later states are read-only. Actions are reverse (posted) and
  create adjustment run.

### 8.4 Reports

Payroll summary, employee history, department and cost-centre cost, statutory
returns per country, deduction register, loan balances. They all:

- show only the companies and employers the user has payroll access to;
- **group by currency** and show a consolidated total only when the user picks a
  conversion rate date explicitly (scope §8);
- export through `ExportAction` with filters applied (`getTableQueryForExport()`).

---

## 9. Implementation plan

Each phase ends with its own tests and a handoff in this document (Why, How,
Benefit, Trade-off, Verification actually run, Rollback), per AGENTS.md.

**Resequenced 2026-11-02 after the adversarial review (§16).** The first
version of this table built the general machinery (platform publishing,
rollout, wizard, reproduce) in P2–P6, before any real country's calculation had
been validated. Its first real country arrived only at P7, behind a fixture
country written by the same team, which can only prove that the engine agrees
with itself. If Ghana or Zambia then needed something the contract had not
anticipated, the rework would land in P2–P6 after months of build. The binding
constraint is **independent statutory verification and real pilot data**, and
no amount of machinery buys that faster. So the order is now: verify, build a
narrow vertical slice per country, reconcile it in shadow mode, and only then
generalize, shaped by the real Ghana and Zambia packages.

**Stage A: evidence first. No live payroll, no posting.**

| Phase | Content | Depends on | Gate to finish |
|---|---|---|---|
| **A0** | ~~Decisions DP-03b, DP-09 to DP-12~~ **decided 2026-11-02**. Privacy impact assessment. **Pilot facts per client** (collected during each client's onboarding, user decision): employers, branches and how each is employed, pay groups, frequencies, currencies, headcount, joiners and leavers, mid-month changes, casual or daily staff, the current payroll tool. **Practitioner = each pilot client's own payroll accountant** (user decision), named per client. **Zambia first**: shadow run within a month of 2026-11-02 (user). ZM research pack first, then GH (Appendix B). **Odoo payroll check, first pass (§17, DP-16).** | — | Zambia pilot's facts collected and its accountant named; ZM research pack done; §17 comparison table filled and reviewed |
| **A1** | Plugin skeleton; availability through the plugin grant (and entitlement if DP-10 says sold); **tenant isolation and the salary-access rule (§5.8) from day one**, since they are cheap early and costly to retrofit; `phpunit.xml` suite; autoloader | A0 (DP-10, DP-12) | Install, isolation and confidentiality tests |
| **A2** | Minimal company setup: employer (one per company per country), one pay group, employment terms with statutory identifiers, period inputs. **Joiners, leavers and final pay in v1** (calendar-day proration, DP-04 amended). | A1 | Effective-date, overlap and proration tests |
| **A3** | Engine plus the **Zambia calculator first** (the first shadow run is in Zambia), then Ghana, for monthly salaried staff. Rules live in **versioned data files in the repository**, deployed like code, each file citing its sources. Localization classes are versioned side by side (§5.6). Golden cases come from the practitioner's independent calculation. A fixture country is used only as a unit-test aid, never as a stand-in for a real one. | A2, DP-03b, A0 research | GH and ZM golden cases pass; practitioner review recorded |
| **A4** | **Test-mode runs** for a pilot employer: calculate, payslip preview (watermarked), and a **reconciliation export** comparing each employee, component by component, with the pilot's existing process. No posting, email, payment or live mode. | A3 | Two consecutive periods reconciled to the minor unit per pilot employer, discrepancies recorded and fixed |

**The one-month target (decided 2026-11-02).** The first shadow run, a Zambia
pilot client within a month, is realistic only for this slice:

- A1 (isolation and the salary-access rule);
- A2 (one employer, one monthly pay group, terms, joiners and leavers);
- A3 for **Zambia only**;
- A4 (test runs, watermarked payslip preview, reconciliation export).

Ghana follows the same path. Live payroll, posting, the wizard and reports come
after Stage A, not in that month. The multi-tenancy deploy for the new client
also has to finish within the same window, so the two must be scheduled
together.

**Gate to Stage B:** both countries reconciled for two periods (§11.2 steps
1–6). Anything Ghana or Zambia needed that the contract did not anticipate is
fixed **here**, while the engine is small.

**Stage B: generalize, shaped by the real GH and ZM packages.**

| Phase | Content | Depends on | Gate to finish |
|---|---|---|---|
| **B1** | Platform tier: move rule data from repository files to published, immutable DB versions (§7.1), with the publish workflow and audit | Gate A | Versioning, immutability and class-pinning tests |
| **B2** | Company tier in full: several pay groups and structures, components, policies, constraints validation (R-05) | Gate A | Constraint and isolation tests |
| **B3** | Live mode: state machine, approval with segregation, finalize, snapshots, `reproduce()`, readiness and local blocking | B1, B2 | Acceptance tests A-01–A-09 (§10.3) on GH and ZM data |
| **B4** | Statutory rollout (§5.7) | B1, B3 | Rollout tests |
| **B5** | Ledger posting and reversal, currency conversion | B3 | Balance, idempotency and conversion tests |
| **B6** | Payslips (live email), payments, then bank export per configuration | B3, B5 | Delivery and isolation tests |
| **B7** | Onboarding wizard and readiness screens | B2, B3 | Wizard tests; plain-language findings |
| **B8** | Reports with currency grouping | B3 | Report scope and currency tests |
| **B9** | Go live per employer (§11.2 step 7) | B3–B8 | Readiness check passes; employer moved to `live` |
| **B10** | **Congo package** (`CD` or `CG`) through the same Stage A steps, then activate its currency | DP-09 | Same gates as A3–A4 |
| **Later** | Daily and hourly pay, attendance, overtime by units, variable pay, per verified pilot need (R-06) | Pilot evidence | Their own golden cases |

### What carries over from the 2026-10-26 plan

- **Posting (B5):** one balanced move per finalized run, guarded by a unique
  `payroll_postings.run_id`. `DisplayType::PRODUCT` is set on every line. The
  balance is asserted in tests, not by inspection. Reversal is a compensating
  move. Statutory payables are mapped by `account_role`.
- **Payslips (A4 preview, B6 live):** a Blade view through `PDFHandler` (the
  waybill pattern), numbered by `SequenceService::ensure('payroll.payslip', $companyId)`.
  Email is queued (`ShouldQueue`); a failed send is logged, not fatal. An
  employee with no email is skipped with a warning.
- **Shadow mode and cutover (A4, B9):** see §11.2.

---

## 10. Test strategy

### 10.1 Pyramid

```
            /  Browser (few)  \          wizard happy path, run screen per state
           /  Feature (some)   \         isolation, permissions, readiness, rollout,
          /                     \        posting, reports, acceptance A-01..A-09
         /   Unit (many, fast)   \       engine, money, each localization's golden
        /_________________________\      cases, constraints, rule resolution
```

- **Unit (no database):** `PayrollCalculator`, `Money`/`Rate`, every
  `StatutoryLocalization`, the constraint checker, the rule-version resolver.
  These are the bulk of the tests and the ones that protect people's pay.
- **Feature (Sail PostgreSQL, one database per package):** `PayrollFeature`
  suite in `phpunit.xml`. Isolation, policies, readiness, rollout, posting,
  payslips, reports.
- **Browser:** the wizard's ten steps and a run in each state, only after the
  feature tests pass (UF-007 is still open for browser execution).

### 10.2 What to cover, by component

| Area | Type | Must cover |
|---|---|---|
| Money / Rate | Unit | Exact percentages (the `Money::percentage` defect of 2026-10-30 is the cautionary case), half-up rounding at one point, negative amounts, overflow refusal |
| Engine | Unit | Calculation order; components sum to totals to the minor unit; zero pay; net below zero refused (DP-15); ceilings; YTD; idempotence (same input, same output) |
| Engine, property-based | Unit | For random valid inputs: net = gross − employee deductions; no negative statutory amount; result independent of component insertion order |
| Each localization | Unit | **Golden cases:** synthetic employees with expected results computed **independently** (practitioner or official calculator), each case citing its source; boundary values at every band edge and ceiling |
| Constraints (R-05) | Unit + feature | A company cannot lower a mandatory rate, disable a mandatory item, or exceed a cap, through the form, the API, an import or a direct service call |
| Rule versions | Unit + feature | Effective-date resolution either side of a change; no gaps or overlaps; a published version rejects edits; `reproduce()` of a finalized run is identical after a newer version is published |
| Isolation (R-13) | Feature | Company A cannot see or affect B's structures, runs, payslips, mappings or reports, including as a Multi-Company Admin without payroll rights in B |
| Confidentiality (N-03, DP-12) | Feature | A main super admin reads pay data and the read is logged. A DEKA support admin with `view_payroll_pay_data` reads any company's pay data, logged. A Multi-Company Admin with the right reads only their assigned companies' pay data and is refused elsewhere. A user who edits localizations but lacks the right, or who has only a scope bypass, is refused. Platform screens show no salaries. |
| Branch coverage (DP-11) | Feature | A branch set as its own employer and a branch covered by its parent each pay correctly, including a foreign branch. A second employer for the same company and jurisdiction is refused by the database. A user without payroll rights in the branch cannot run the parent's payroll over the branch's employees. |
| Readiness (R-12, R-15) | Feature | An `in_review` country blocks live finalization for its employers only; other countries' employers finalize |
| Rollout (R-14) | Feature | Publishing flags only unfinalized runs in that country with a reference date on or after the effective date; approvals revoked and audited; finalized runs untouched |
| Posting (R-10) | Feature | Balanced move; idempotent; reversal; conversion at the stored rate with the residual on the rounding account; missing rate refused |
| Reports | Feature | Grouped by currency; no cross-currency total without an explicit conversion; filters applied to the export query |
| Test vs live | Feature | Test runs cannot finalize, post, email or pay; payslips watermarked |

### 10.3 Acceptance tests (scope §8)

Named after the behaviour, in `plugins/webkul/payroll/tests/Feature/Acceptance/`:

| Id | Test | Proves |
|---|---|---|
| A-01 | `two companies in the same country keep different salary policies` | R-04, R-13 |
| A-02 | `companies in different countries get their own country's statutory deductions` | R-08 |
| A-03 | `a country's rules never apply to another country's run` | R-08, R-09 |
| A-04 | `a run uses the rule version effective on its reference date` | R-02, DP-14 |
| A-05 | `a company administrator cannot undercut a mandatory statutory rule` | R-05 |
| A-06 | `a multi-company user needs payroll access in each company` | R-13, §5.8 |
| A-07 | `consolidated reports never add different currencies without a chosen conversion` | R-10, F-12 |
| A-08 | `an employer in a country with incomplete localization cannot finalize live payroll` | R-12 |
| A-09 | `a finalized run reproduces exactly after company settings and statutory rules change` | N-02, R-14 |

A-01 to A-09 run against the real GH and ZM packages in B3, once Stage A has
reconciled them. A fixture country may help unit tests, but no acceptance test
passes on it alone (§16).

### 10.4 Targets and gaps

- Engine and localization classes: every branch covered; golden cases for every
  supported scenario a localization declares. An unsupported scenario must be
  **refused with a message**, not calculated.
- Feature tests: every policy method, every state transition, every readiness
  check.
- **Gaps today:** none of the existing suites covers anything payroll needs. The
  closest reuse is the `CompanyScopingInvariantsTest` pattern: payroll gets its
  own, with an empty `$shared` list.

---

## 11. Country localizations and pilot validation

### 11.1 Readiness register

Updated in this table whenever a status changes. **Nothing here is production-ready.**

| Code | Country | Status | Pay currencies (to verify) | Practitioner | Shadow run starts | Blocker |
|---|---|---|---|---|---|---|
| ZM | Zambia (**first**) | `draft`: research not started | ZMW (active in DEKA since 2026-10-26) | The first Zambian client's accountant (not named here) | **Within a month of 2026-11-02**; exact date pending | Pilot facts at onboarding; ZM research pack |
| GH | Ghana (second) | `draft`: research not started | GHS (active in DEKA) | The first Ghanaian client's accountant (not named here) | **no date** | After Zambia's Stage A |
| CD | Democratic Republic of the Congo | **not created** (DP-09: both Congos) | CDF (inactive in DEKA), USD to confirm | Pilot client's accountant | **no date** | After GH and ZM; activate CDF |
| CG | Republic of the Congo | **not created** (DP-09: both Congos) | XAF (inactive in DEKA) | Pilot client's accountant | **no date** | After GH and ZM; activate XAF |

**The client's accountant as practitioner (user decision, 2026-11-02).** They
are independent of DEKA, which is what the scope requires. But they also run
the process DEKA is reconciled against, so an error in their current process
could be copied into the golden cases and still reconcile. Mitigation: every
golden case is also checked against the authority's own published tables or
calculator (for Zambia, ZRA's PAYE bands and NAPSA's published ceiling), and a
disagreement between the two is resolved before the version is `verified`.

Until a country has a named practitioner and a shadow-run date, Stage A for it
has **no end date**, and none should be promised to a client (§16, attack 3).

Employer readiness is tracked per employer in the application (`readiness_state`),
not in this document.

### 11.2 Per-country pilot procedure (scope §7)

For each localization, then for each pilot employer in that country:

1. **Document** the supported and unsupported scenarios in the rule version
   (for example: monthly salaried residents supported; expatriate relief, bonus
   tax treatment or hourly workers unsupported until verified).
2. **Golden cases:** synthetic employees covering every band, ceiling and
   relief, with expected results computed **independently** of DEKA and cited.
3. **Practitioner review:** a qualified local payroll practitioner reviews the
   rules, the golden cases and a sample payslip; their review is recorded on the
   rule version. Without it the version cannot reach `verified`.
4. **Parallel run (shadow mode):** at least one full period, ideally two, in
   `test` mode alongside the company's existing validated process.
5. **Reconcile** employee by employee: gross, each deduction, employer
   contributions, net, and the accounting entries, to the minor unit.
6. **Record discrepancies** with cause and fix; re-run until none remain. The
   reconciliation record is attached to the employer's readiness.
7. **Go live** for that employer only (`testing → live`). Other employers in the
   same country follow their own steps 4 to 7.

**Rollback:** set the employer to `suspended`, reverse the period's posting by
compensating entry, resume the previous process. Nothing is deleted, and no
finalized run is edited.

The shared engine working does **not** make any country production-ready. Each
row of §11.1 moves on its own evidence.

---

## 12. Codebase traps that will hit payroll

Kept from the first plan and extended. Every one has caused a real defect here.

| # | Trap | Rule |
|---|---|---|
| 1 | `company_id = null` is shared with every tenant | No payroll company row may be null; payroll's invariants test has an empty `$shared` list |
| 2 | Parent read through a relation in a model hook | `InheritsParentCompany`, or a `withoutGlobalScopes()` read by explicit key |
| 3 | `withoutGlobalScope(CompanyScope::class)` alone | Other scopes still hide rows; use `withoutGlobalScopes()` |
| 4 | `SequenceService::next()` without `ensure()` | A shared counter; call `ensure($code, $companyId)` first |
| 5 | Two `PermissionRegistrar` classes | Flush both after creating permissions |
| 6 | Super-admin `Gate::before` skips policies | Guard services (constraints, finalize, confidentiality), not only policies |
| 7 | `MoveLine` without `display_type` | Set `DisplayType::PRODUCT` explicitly |
| 8 | `$move->lines` includes balancing and tax lines | Filter to your own lines |
| 9 | `$table->enum(...)->change()` on PostgreSQL | Use `DB::statement` |
| 10 | Unregistered migration | Register in `hasMigrations()` |
| 11 | Page permission written in two places | Assert the generated name |
| 12 | **Float arithmetic in money** | Exact arithmetic only (DP-03b); the 2026-10-30 `Money::percentage` defect was off by 100× |
| 13 | **A factory default makes a test assert the opposite** | Set absent values explicitly (rates, ceilings, effective dates) |
| 14 | **A listener or job runs under whoever acted** | Rollout and queued calculation read by explicit keys, scope-free, and stamp the run's company |
| 15 | **An inactive currency is invisible to every picker** | Activate CDF or XAF with a migration when Congo is confirmed (the ZMW precedent) |
| 16 | **Cross-company reads return null silently** | Payroll reads of `Employee`, `Partner` (bank) and `Company` in services use explicit, scope-free keyed reads (§4k read audit, docs/company-admin-role-plan.md) |

---

## 13. Documentation to produce

- This document: phase handoffs and the readiness register.
- Per localization: a **country sheet** in the plugin (`src/Localization/<CC>/README.md`)
  with sources, scenarios, constraints and the review record.
- Company administrator guide: onboarding and the monthly cycle (into the
  existing user and admin guides behind `GuideAudience`).
- DEKA staff guide: publishing a rule version and handling a rollout.
- Privacy impact assessment (A0), and a records-of-processing entry for payroll.
- `docs/change-log.md` and `CHANGELOG.md` entries when phases ship.

---

## 14. Open questions

- ~~DP-09, DP-10, DP-11, DP-12, DP-03b~~ **all decided 2026-11-02** (§6).
- **Zambia pilot:** the exact start date of the first test run. Client and
  accountant names are not tracked in this plan (user, 2026-11-02): the company
  is added and its region selected in the normal way.
- Pilot client facts per country, collected at onboarding: number of
  employees, pay groups, frequencies, currencies, branches and how each is
  employed, joiners and leavers, casual or daily staff, current payroll tool.
- Branches abroad paid under the parent: does each country allow it? The
  practitioner confirms per country.
- Statutory returns: does DEKA produce the filing file each authority accepts,
  or export figures for manual entry in a portal? (per country, after research)
- Contractors and casual labour: payroll or purchasing? (affects scope §2)
- Bank export formats per pilot bank and mobile-money provider.

---

## 15. What to revisit as payroll grows

- More than one jurisdiction per employer (seconded staff, split payroll).
- Retroactive pay and back-dated rule corrections across many periods, beyond
  single adjustment runs.
- A formula editor for company-defined earnings, if the fixed methods prove
  insufficient.
- Queued calculation and pagination limits once an employer passes ~1,000
  employees.
- Direct e-filing integrations with tax and social-security authorities.

---

## 16. Adversarial review, 2026-11-02

Run with the `devil-advocate` skill: a fresh agent on a different model, given
only the claim and this file's path, attacked the plan; each attack was then
checked against this document before being answered.

**Claim attacked:** building payroll as specified here will let DEKA run correct,
tenant-isolated live payroll for its Ghana and Zambia pilots without reworking the
core data model when Congo or daily and hourly pay are added.

**Advocate's P(mistake): 65 %.** By the skill's calibration, that means the plan
as first written was probably wrong.

| Attack | Check run | Answer | What changed |
|---|---|---|---|
| **Kill shot:** the general machinery (P2–P6) was built before any real country was validated; the fixture country proves only that the engine agrees with itself; unanticipated GH or ZM needs would force rework after months of build | Phase table read: the first real country was P7, after P2–P6 | **Conceded** | §9 resequenced into Stage A (verify, one country slice at a time, shadow reconciliation) and Stage B (generalize, shaped by GH and ZM). Acceptance tests run on real packages only (§10.3). |
| 1. "Monthly salaried first" defers what every payroll needs in its first cycles: joiners, leavers, final pay | Searched for joiner, leaver, final pay or termination handling: none; DP-04 refused all mid-period changes | **Conceded** | DP-04 amended: calendar-day proration for joiners and leavers, plus final pay, are in v1 (A2). Casual and daily labour stays a pilot-facts question (A0). |
| 2. Reproducibility contradicts "one reviewed PHP class per country": data is immutable, code is not, so a bug fix changes how history recalculates | Searched for a class-versioning mechanism: only a `requires_class_version` string | **Conceded** | §5.6: class versions side by side, pinned per rule version and recorded on every run, never edited (hash-manifest test). |
| 3. The timeline depends on an unnamed practitioner and on shadow runs no pilot has agreed to | Not checkable from the repository | **Partly resolved 2026-11-02**: the user chose each client's own accountant as practitioner, and a Zambia shadow run within a month. Names are not needed (user). Still open: the exact date. | §11.1 updated; the one-month target is scoped to the Zambia slice only (§9). |

**Verdict: STOP the plan as first sequenced.** It is replaced by the Stage A /
Stage B sequence above. Attack 3 stays open until the user supplies the names
and dates.

---

## 17. Odoo payroll check

Added 2026-11-10 (user, DP-16). AureusERP follows Odoo's concepts in most
plugins, and many pilot clients' accountants know Odoo. Comparing the design
with Odoo's payroll finds concepts we have missed and makes deliberate
differences explicit, before code exists.

**Licensing guardrail.** Odoo's payroll (`hr_payroll` and its localizations)
is Odoo Enterprise, under the proprietary Odoo Enterprise licence. Study its
documentation and observed behaviour only: never copy its code, data files or
rule definitions. The OCA community `payroll` module is LGPL-3; read it for
ideas, do not paste it into this MIT-licensed repository. Country rules still
come only from official sources and the practitioner (Appendix B), never from
Odoo's localization data.

**When.** First pass in A0, before the skeleton. Repeat before A3 (engine and
calculators) and before B1 (platform publishing). Each pass records its date,
who did it, and the Odoo version it was checked against.

**What to compare.** The Odoo names below are from memory: verify each against
the current Odoo payroll documentation and record the version.

| Area | Odoo concept (to verify) | This plan | Question to answer |
|---|---|---|---|
| Pay structure | Structure types and salary structures | Pay groups and structures (§7.2) | Do we model the same separation of "kind of employee" and "how pay is computed"? |
| Rules | Salary rules with categories (basic, allowances, gross, deductions, net, employer contributions), computed by conditions and code | Statutory items in versioned data plus one reviewed PHP class per country; company items constrained (§5.2) | Do our component categories cover everything a payslip shows? Odoo lets users write rule code; we deliberately do not (tenant-editable code is a security and audit risk). Record this difference. |
| Dated parameters | Rule parameters with values effective from a date | Versioned, immutable statutory rule versions (§5.6, §5.7) | Is our effective dating at least as expressive, including mid-year changes? |
| Contract | Employee contract with wage and wage type (monthly or hourly) | Employment terms with `pay_basis` (§7.3, DP-02) | Any contract field a pilot will expect that we lack? |
| Time | Work entries and work entry types feeding worked days | Period inputs; daily and hourly pay later (R-06) | Which work-entry ideas do we need for leave and unpaid days in v1? |
| Inputs | Payslip "other inputs" | Period inputs (§7.3) | Same coverage for one-off bonuses and deductions? |
| Batches | Payslip batches | Runs bound to employer, pay group, period, currency and mode (§5.3) | Anything Odoo's batch does (e.g. regenerate, partial) that a run should? |
| Corrections | Refund or credit payslip | Reversal by a compensating move (§9) | Is our correction flow as usable for an accountant? |
| Accounting | Journal entry per payslip or per batch, accounts mapped on rules | One balanced move per finalized run, accounts by `account_role` (B5) | Do pilots need per-employee entries? |
| Cumulatives | Year-to-date totals on payslips | Snapshots and results (§7.4) | Can every payslip show YTD without recomputing history? |
| Payment | Payment reports and bank file exports | Payment records, bank export per configuration (DP-06) | Formats the pilots' banks actually accept. |
| Multi-company | Payslips per company | Employer per company per country, salary-access rule (§5.3, §5.8) | Do we exceed Odoo here? Record it as a selling point if so. |
| Localizations | Per-country payroll modules for some countries | Separate packages per country (ZM, GH, CD, CG) | Does Odoo ship a payroll localization for ZM, GH, CD or CG? If it does, use its documentation as a cross-check, not its data. |
| Reports | Payroll analysis and statutory declarations | Reports with currency grouping (B8) | Which statutory returns do pilots file, and in what format? |

**Output of each pass.** A table in this section with one row per area and a
verdict: *already covered*, *adopt* (add to the plan, with the phase),
*deliberately different* (with the reason), or *not relevant to the pilots*.
"Adopt" rows become plan changes in the same edit. The pass is reviewed by the
pilot's payroll accountant where it touches calculations.

**Status.** Not yet done. The first pass belongs to A0.

---

## Appendix A: The scope note, verbatim

Merged 2026-11-02 from `REVIEW all THIS WITH SKILLs + i have.md`. This is the
requirement source for §4; where this document and the note differ, the note
wins until the user decides otherwise.

> ## Mandatory scope update: Multi-company, multi-country payroll
>
> DEKA ERP is a multi-tenant SaaS currently being piloted in Zambia, Ghana, and Congo. Payroll must accommodate the individual companies using it, rather than applying one company's payroll settings to all tenants.
>
> Replace the Ghana-only implementation scope with a shared payroll engine and separate country localization packages.
>
> ### 1. Separate platform, country, and company settings
>
> **Platform level**
> - Maintain supported countries and versioned statutory localization packages.
> - Manage payroll feature availability through the existing tenant module system.
> - Publish tested statutory updates with effective dates and an audit history.
> - Do not expose tenant salaries or employee payment details through ordinary platform configuration access.
>
> **Country level**
> - Define statutory taxes, social security, pensions, mandatory contributions, reporting requirements, and applicable calculation rules.
> - Record official sources, effective dates, verification dates, and supported scenarios.
> - Preserve previous rule versions for historical payroll calculations.
> - Clearly distinguish verified rules from incomplete or unsupported functionality.
>
> **Tenant company level**
> Allow each company to configure its own:
> - Payroll country and permitted currencies.
> - Pay frequencies, pay groups, payroll calendar, and payment dates.
> - Salary structures and grades.
> - Allowances, bonuses, commissions, and other earnings.
> - Overtime, attendance, unpaid-leave, and proration policies.
> - Loans, salary advances, and permitted deductions.
> - Department and cost-centre allocations.
> - Accounting mappings.
> - Approval workflows and payroll permissions.
> - Bank export configuration.
> - Payslip branding, language, and permitted display options.
>
> Company policies must operate within applicable statutory constraints. Do not allow a company setting to silently disable or override a mandatory legal obligation.
>
> ### 2. Support different types of companies
>
> Do not hardcode one industry's employment practices.
>
> Design configurable structures for monthly salaried staff, hourly workers, daily-paid staff, and variable compensation. Implement these progressively according to verified pilot requirements.
>
> A company must be able to use multiple salary structures and pay groups without requiring custom code for every variation.
>
> Keep industry-specific policies separate from statutory country rules.
>
> ### 3. Implement country localizations independently
>
> Create separate localization packages for:
> - Ghana.
> - Zambia.
> - The applicable Congo jurisdiction once confirmed.
>
> "Congo" is ambiguous: confirm whether the pilot is in the Democratic Republic of the Congo, the Republic of the Congo, or both. Do not assume a jurisdiction, currency, tax authority, or statutory framework.
>
> Use distinct country identifiers, such as ISO country codes, rather than one generic "Congo" configuration.
>
> Continue work on the shared engine, Ghana, and Zambia while the Congo jurisdiction is being clarified. Do not activate an unverified Congo localization.
>
> Research each country using current official tax, pension, social-security, and other relevant authority sources. Do not reuse Ghana's rates, formulas, or filing assumptions for another country.
>
> ### 4. Handle country and currency explicitly
>
> Inspect whether DEKA distinguishes a tenant, a legal company, and a branch. Preserve that model rather than assuming they are interchangeable.
>
> Bind each payroll run to the correct legal employer, payroll jurisdiction, pay group, period, and currency.
>
> For the first release, support one statutory jurisdiction per payroll run. If one tenant contains employers in different countries, use separate employer configurations and payroll runs.
>
> Do not infer payroll jurisdiction from the user's location, interface language, or currency.
>
> If payroll currency differs from accounting currency, explicitly define exchange-rate dates, precision, rounding, and journal conversion behavior. Verify whether local rules permit the intended payment currency.
>
> ### 5. Add company payroll onboarding
>
> Provide a guided setup using existing Filament conventions:
>
> 1. Enable the payroll module for the company.
> 2. Select the legal employer and supported payroll country.
> 3. Enter required employer registration information.
> 4. Configure currency, pay calendar, and pay groups.
> 5. Configure salary structures and company policies.
> 6. Map payroll accounts and cost centres.
> 7. Assign payroll roles and approval rules.
> 8. Add employee payroll information and salary assignments.
> 9. Run a sample payroll and review validation results.
> 10. Mark the company ready for live payroll only after required checks pass.
>
> Explain missing configuration in plain language.
>
> Distinguish draft setup, test payroll, and live payroll. Incomplete statutory configuration must block finalization for the affected country without blocking other companies.
>
> ### 6. Preserve tenant independence
>
> One company's configuration changes must never change another company's:
> - Salary structures.
> - Employee calculations.
> - Approval workflow.
> - Accounting mappings.
> - Payslips or reports.
>
> Separate platform-maintained statutory rules from tenant-owned company policies.
>
> A shared statutory update must have a controlled, effective-dated rollout. Identify affected companies and draft runs, require recalculation where necessary, and invalidate affected approvals.
>
> Never modify finalized historical payroll.
>
> ### 7. Pilot rollout and validation
>
> Deliver the shared engine first, then validate each localization independently.
>
> For every pilot country:
> - Document supported and unsupported payroll scenarios.
> - Use synthetic test cases and independently validated expected results.
> - Obtain review from a qualified local payroll practitioner before live use.
> - Run parallel payroll against the company's existing validated process.
> - Reconcile gross pay, deductions, employer contributions, net pay, and accounting entries.
> - Record discrepancies and resolve them before enabling live payroll.
>
> Do not claim that all countries are production-ready because the shared engine works.
>
> Track readiness separately for each country localization and each tenant company.
>
> ### 8. Additional acceptance tests
>
> Verify that:
> - Two companies in the same country can use different salary policies without affecting each other.
> - Companies in different countries receive the correct statutory calculations.
> - Country-specific rules cannot leak into another country's payroll.
> - Effective-dated updates use the correct rule version.
> - Company administrators cannot bypass mandatory statutory constraints.
> - Multi-company users need explicit access to each company's payroll.
> - Consolidated reports preserve company boundaries and do not add different currencies without explicit conversion.
> - Countries with incomplete localization cannot finalize live payroll.
> - Historical payroll remains reproducible after company settings or statutory rules change.
>
> Update the architecture, implementation plan, data model, interface, documentation, and test strategy to reflect this scope before continuing implementation.

---

## Appendix B: Sources to research per country

**Starting points only.** The authority names come from general knowledge and
must be confirmed. Rates, bands and rules come from the authorities' current
publications, retrieved and cited in the rule version, then reviewed by a local
practitioner. Never from this list or from model knowledge.

| Country | Income tax | Social security / pension | Other levies and health | Data protection |
|---|---|---|---|---|
| Ghana (GH) | Ghana Revenue Authority (PAYE bands, reliefs) | SSNIT (Tier 1), NPRA (Tier 2 and 3 regulation) | Confirm whether any health levy applies to **salaries** (see F-02) | Data Protection Commission (Act 843) |
| Zambia (ZM) | Zambia Revenue Authority (PAYE bands) | NAPSA (contributions and ceiling) | NHIMA (health insurance); Skills Development Levy (employer) | Data Protection Act 2021 regulator |
| DRC (CD) | Direction Générale des Impôts (IPR) | CNSS (DRC) | INPP, ONEM (employer levies); confirm current names and rates | To research |
| Republic of the Congo (CG) | Direction Générale des Impôts et des Domaines (IRPP) | CNSS (Republic of the Congo) | To research | To research |

For each country also research: the permitted payment currencies, the
reference date for the tax period, the filing formats and deadlines, the
required employer registrations, and the employee identifiers.

---

## Appendix C: Standard company payroll items

Company-owned items the provisioner may offer as **templates** when an employer
is created. The company keeps, edits or deletes them. Statutory items are
**not** in this list: they come from the country package, read-only.

**Earnings:** basic salary; housing, transport, meal, acting and responsibility
allowances; overtime (regular and weekend or holiday, multipliers set by company
policy within any legal minimum); bonus; commission.

**Deductions:** staff welfare, union dues, cooperative, salary advance
repayment, staff loan repayment, voluntary pension top-up (within the country's
cap, if the country allows it).

**Employer benefits:** any non-statutory benefit the company provides.

What a company may customise: enable or disable *its own* items, set their
amounts, rates, bases and ceilings, add its own items, override per employee,
and set effective dates. It may **not** change, disable or lower a statutory
item, or remove a mandatory one (R-05).
