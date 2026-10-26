# Payroll Plugin — Step-by-Step Implementation Plan

**Status: planning only — nothing is implemented.**
The user decides when this is built. This document is the build guide.

Branch: **`feature/payroll`** (create from `develop` when ready).
Architecture reference: `plugins/webkul/logistics/` (the most recent plugin).

---

## Quick summary

Each company/tenant can:
- Have payroll toggled on/off by the super-admin
- Set their own deduction percentages (tax, pension, health, etc.)
- Define custom earnings and deductions (allowances, overtime rates, loan repayments)
- Run payroll for their employees and generate payslips
- Post payroll to their own accounting ledger

The super-admin controls which companies have payroll, and each company's
administrator configures their own rates and rules.

---

## Decision gates — answer before building

These must be answered before starting the corresponding step. Recommendations
are given; the user confirms.

| # | Question | Answer (decided 2026-10-26) |
|---|---|---|
| DP-01 | Which country first? | **Both Ghana and Zambia** — seed both jurisdictions in the first release. Engine is generic; rates are configuration data per jurisdiction. |
| DP-02 | Where do hours come from? | **Salaried only (no hours)** — first release handles monthly salaried employees only. No hourly pay, no attendance, no hours import. Hourly pay is a future extension. |
| DP-03 | Money precision? | **`decimal(18,4)`** — matches the `accounts` plugin. All money columns use the same format across the app. One documented rounding point per component. |
| DP-04 | Mid-period pay change? | **Refuse — require two runs.** The admin splits the period manually. No silent pro-rating, no surprises. |
| DP-05 | Payslip delivery? | **PDF + email** — generate stored PDF, then auto-email to the employee's email address after posting. |
| DP-06 | Net pay output? | **Payment records only.** Per-employee payment records in the system. Admin pays via their own banking. Bank file is a future extension. |
| DP-07 | Separate permissions? | **Yes — three separate permissions:** `run_payroll`, `approve_payroll`, `post_payroll`. Enforces segregation of duties. Super Admin holds all three initially. |
| DP-08 | Reuse `payments` plugin? | **No — own payment tracking.** Payroll manages its own payment records. Simpler, no risk of breaking existing payment flows. |

---

## Step 1 — Plugin skeleton and per-company toggle

**Goal:** A payroll plugin that exists, installs, uninstalls, and lets the
super-admin toggle it per company.

### 1.1 Create the plugin directory

```
plugins/webkul/payroll/
├── composer.json
├── config/
│   ├── payroll.php                     # plugin config
│   └── filament-shield.php             # Shield permissions
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── lang/
│       ├── en/
│       ├── ar/
│       ├── es/
│       ├── fr/
│       └── pt_BR/
├── src/
│   ├── Enums/
│   ├── Filament/
│   │   ├── Clusters/
│   │   ├── Pages/
│   │   ├── Resources/
│   │   └── Widgets/
│   ├── Models/
│   ├── Policies/
│   ├── Services/
│   ├── PayrollPlugin.php
│   └── PayrollServiceProvider.php
└── tests/
    └── Feature/
```

### 1.2 PayrollServiceProvider

Extends `PackageServiceProvider`. Pattern: `LogisticsServiceProvider`.

```php
static $name = 'payroll';
// ->hasMigrations([...every migration...])
// ->runsMigrations()
// ->hasSeeder(PayrollSeeder::class)
// ->hasSettings()
// ->hasDependencies(['employees', 'accounts'])
// ->hasInstallCommand()  with startWith guard
// ->hasUninstallCommand() with startWith guard
// ->hasConfigFile('payroll')
// ->hasTranslations()
// ->hasViews()
```

### 1.3 PayrollPlugin (Filament)

Pattern: `LogisticsPlugin`. Checks `Package::isPluginInstalled('payroll')` only
in `register()`. Per-company gating is in `canAccess()` on clusters/pages/resources.

### 1.4 Register in bootstrap/providers.php

Add `Webkul\Payroll\PayrollServiceProvider::class`.

### 1.5 Migration: `payroll_company_settings`

One row per company. Pattern: `logistics_company_settings`.

```
payroll_company_settings
├── id
├── company_id (FK companies, unique)
├── is_enabled (boolean, default false, indexed)
├── enabled_at (timestamp, nullable)
├── disabled_at (timestamp, nullable)
├── enabled_by_id (FK users, nullable)
├── pay_frequency (enum: weekly, biweekly, monthly — default monthly)
├── pay_currency_id (FK currencies, nullable — defaults to company currency)
├── payslip_journal_id (FK accounts_journals, nullable)
├── salary_expense_account_id (FK accounts_accounts, nullable)
├── net_pay_liability_account_id (FK accounts_accounts, nullable)
├── statutory_liability_account_id (FK accounts_accounts, nullable)
├── payslip_sequence_code (string, default 'PAY')
├── require_approval_before_post (boolean, default true)
├── timestamps
```

### 1.6 Model: CompanySetting

Pattern: `Logistics\Models\CompanySetting`. Uses `BelongsToCompany`.
Static `forCompany(int $companyId)` method returns the row or an unsaved default.
`$attributes` array mirrors column defaults (the `users.is_active` trap).

### 1.7 Settings page

A Filament Settings page at **Payroll > Settings** for the company admin to configure
their journals, accounts, pay frequency, and sequence code. Only visible when
`is_enabled = true` for the current company.

### 1.8 Super-admin toggle

Super-admin can toggle `is_enabled` for any company from either:
- The Payroll settings page (if they are viewing a company's settings)
- The Company form's contributed "Payroll" section (via `CompanySchemaRegistry`,
  pattern: `CompanyAdminContributions`)

### 1.9 Tests for Step 1

- Plugin installs and uninstalls cleanly
- Settings page only visible when enabled
- Toggle on/off sets `enabled_at`/`disabled_at` and `enabled_by_id`
- Non-super-admin cannot toggle another company
- Company scoping: company A cannot see company B's settings
- Migration registration test

### 1.10 Checklist

- [ ] Plugin directory created
- [ ] `PayrollServiceProvider` with all `has*` calls
- [ ] `PayrollPlugin` for Filament
- [ ] Registered in `bootstrap/providers.php`
- [ ] Autoloader regenerated (root, no scripts)
- [ ] `payroll_company_settings` migration
- [ ] `CompanySetting` model with `$attributes` defaults
- [ ] Settings page
- [ ] Super-admin toggle
- [ ] `phpunit.xml` suite block added
- [ ] Translations (en, ar, es, fr, pt_BR)
- [ ] Tests pass

---

## Step 2 — Salary structures and pay components

**Goal:** Each company defines its own earnings and deductions — what they pay
and what they deduct, with their own percentages and amounts.

### 2.1 Migration: `payroll_salary_structures`

Company-owned salary structure templates (e.g. "Standard Employee", "Manager",
"Casual Worker").

```
payroll_salary_structures
├── id
├── company_id (FK)
├── name (string)
├── description (text, nullable)
├── is_active (boolean, default true)
├── sort_order (integer, default 0)
├── timestamps
```

### 2.2 Migration: `payroll_pay_components`

Named earnings and deductions. Each company defines their own set.

```
payroll_pay_components
├── id
├── company_id (FK)
├── code (string, unique per company — e.g. BASIC, HOUSING, PAYE, NAPSA)
├── name (string — "Basic Salary", "Housing Allowance", "PAYE Tax")
├── type (enum: earning, deduction, employer_contribution)
├── category (enum: basic, allowance, overtime, bonus, statutory_deduction,
│            voluntary_deduction, loan, employer_statutory, employer_benefit)
├── calculation_method (enum: fixed, percentage, formula)
├── default_rate (decimal 10,4, nullable — the percentage, e.g. 5.5 for 5.5%)
├── default_amount (decimal 18,4, nullable — flat amount)
├── base_component_id (FK self, nullable — "percentage of what?")
├── ceiling_amount (decimal 18,4, nullable — max deduction, e.g. NAPSA ceiling)
├── is_taxable (boolean, default true — included in taxable income?)
├── is_statutory (boolean, default false — government-mandated?)
├── is_active (boolean, default true)
├── sort_order (integer — controls payslip display order)
├── effective_from (date, nullable)
├── effective_to (date, nullable)
├── timestamps
```

**Key design**: `default_rate` is the company's configured percentage. When
`calculation_method = percentage`, the deduction is `default_rate / 100 * base`,
where `base` is determined by `base_component_id` (null = gross pay).

### 2.3 Migration: `payroll_structure_components`

Links components to structures with optional overrides.

```
payroll_structure_components
├── id
├── salary_structure_id (FK)
├── pay_component_id (FK)
├── override_rate (decimal, nullable — company-specific override for this structure)
├── override_amount (decimal, nullable)
├── is_active (boolean, default true)
├── sort_order (integer)
├── timestamps
```

### 2.4 Filament resources

- **PayComponentResource** — CRUD for earnings/deductions. Company admin sets
  names, types, percentages, ceilings. Each company sees only their own.
  Split schema: `Schemas/PayComponentForm.php`, `Tables/PayComponentsTable.php`.
- **SalaryStructureResource** — group components into templates. Repeater for
  components with override rates.

### 2.5 Seeders

Default components seeded per jurisdiction when payroll is enabled for a company
(pattern: `LogisticsCompanyProvisioner`). For Ghana:

| Code | Name | Type | Category | Method | Default rate |
|---|---|---|---|---|---|
| BASIC | Basic Salary | earning | basic | fixed | — |
| HOUSING | Housing Allowance | earning | allowance | percentage | — |
| TRANSPORT | Transport Allowance | earning | allowance | fixed | — |
| OVERTIME | Overtime | earning | overtime | formula | — |
| PAYE | PAYE Income Tax | deduction | statutory_deduction | formula | progressive |
| SSNIT_EE | SSNIT (Employee) | deduction | statutory_deduction | percentage | 5.5 |
| SSNIT_ER | SSNIT (Employer) | employer_contribution | employer_statutory | percentage | 13.0 |
| TIER2_EE | Tier 2 (Employee) | deduction | statutory_deduction | percentage | 5.0 |
| TIER2_ER | Tier 2 (Employer) | employer_contribution | employer_statutory | percentage | — |
| TIER3 | Tier 3 (Voluntary) | deduction | voluntary_deduction | percentage | — |
| NHIL | NHIL | deduction | statutory_deduction | percentage | 2.5 |

**These rates are examples only.** Actual values must be transcribed from the
current official publications (GRA / SSNIT) at build time, dated, and cited in
the seeder.

### 2.6 Tests for Step 2

- Company A's components are invisible to company B
- Percentage calculation produces correct amount
- Ceiling stops deduction at the cap
- Effective dates: component not applied outside its date range
- Structure groups the right components
- CRUD permissions enforced

### 2.7 Checklist

- [ ] `payroll_salary_structures` migration
- [ ] `payroll_pay_components` migration
- [ ] `payroll_structure_components` migration
- [ ] Models with `BelongsToCompany`, `$attributes`, relationships
- [ ] `PayComponentResource` (split schema)
- [ ] `SalaryStructureResource` (split schema)
- [ ] Jurisdiction seeder (cited, dated)
- [ ] Provisioner seeds defaults when payroll is enabled for a company
- [ ] Translations
- [ ] Tests pass

---

## Step 3 — Employment terms (linking employees to payroll)

**Goal:** Each employee has payroll terms: their salary structure, base pay,
pay frequency, and any individual component overrides. Effective-dated so
raises and changes are tracked.

### 3.1 Migration: `payroll_employment_terms`

```
payroll_employment_terms
├── id
├── company_id (FK)
├── employee_id (FK employees_employees)
├── salary_structure_id (FK payroll_salary_structures)
├── effective_from (date)
├── effective_to (date, nullable — null = current)
├── base_pay (decimal 18,4 — the employee's base salary amount)
├── pay_frequency (enum: weekly, biweekly, monthly — inherits company default)
├── currency_id (FK currencies)
├── cost_centre (string, nullable — for reporting)
├── statutory_id_1 (string, nullable — e.g. SSNIT number)
├── statutory_id_2 (string, nullable — e.g. TIN)
├── bank_account_id (FK, nullable — for net pay)
├── notes (text, nullable)
├── created_by_id (FK users)
├── timestamps
```

### 3.2 Migration: `payroll_employee_component_overrides`

Per-employee overrides of component rates (e.g. an employee with a different
housing allowance percentage than the structure default).

```
payroll_employee_component_overrides
├── id
├── employment_term_id (FK payroll_employment_terms)
├── pay_component_id (FK payroll_pay_components)
├── override_rate (decimal, nullable)
├── override_amount (decimal, nullable)
├── effective_from (date)
├── effective_to (date, nullable)
├── timestamps
```

### 3.3 Migration: `payroll_employee_loans`

Recurring deductions with a balance (salary advances, loans).

```
payroll_employee_loans
├── id
├── company_id (FK)
├── employee_id (FK)
├── pay_component_id (FK — must be type=deduction, category=loan)
├── original_amount (decimal 18,4)
├── outstanding_balance (decimal 18,4)
├── monthly_deduction (decimal 18,4)
├── effective_from (date)
├── fully_repaid_at (date, nullable)
├── notes (text, nullable)
├── timestamps
```

### 3.4 Filament integration

- **EmploymentTermResource** — manage an employee's pay terms. Accessed from
  the Employee record (relation manager or a "Payroll" tab).
- Only visible when payroll is enabled for the employee's company.

### 3.5 Tests for Step 3

- Effective date resolution: correct term selected for a given date
- Mid-period term change is refused (two active terms with overlapping dates)
- Employee override beats structure default
- Loan deduction reduces outstanding balance
- Company isolation

### 3.6 Checklist

- [ ] `payroll_employment_terms` migration
- [ ] `payroll_employee_component_overrides` migration
- [ ] `payroll_employee_loans` migration
- [ ] Models with relationships and `InheritsParentCompany` on children
- [ ] EmploymentTerm resource or relation manager
- [ ] Loan tracking model
- [ ] Translations
- [ ] Tests pass

---

## Step 4 — Tax brackets and statutory configuration

**Goal:** Effective-dated progressive tax tables and contribution rates, stored
as data, not code constants. The super-admin manages these as reference data;
companies consume them.

### 4.1 Migration: `payroll_jurisdictions`

```
payroll_jurisdictions (shared — no company_id)
├── id
├── code (string, unique — e.g. GH, ZM)
├── name (string — "Ghana", "Zambia")
├── currency_id (FK currencies)
├── tax_year_start_month (integer, default 1 — January)
├── is_active (boolean, default true)
├── timestamps
```

### 4.2 Migration: `payroll_tax_brackets`

Progressive income tax bands, effective-dated.

```
payroll_tax_brackets (shared)
├── id
├── jurisdiction_id (FK)
├── effective_from (date)
├── effective_to (date, nullable)
├── band_floor (decimal 18,4 — monthly income from)
├── band_ceiling (decimal 18,4, nullable — null = no cap)
├── rate (decimal 8,4 — e.g. 25.0000 for 25%)
├── sort_order (integer)
├── timestamps
```

### 4.3 Migration: `payroll_contribution_schemes`

Statutory contribution definitions (SSNIT, NHIMA, NAPSA, etc.), effective-dated.

```
payroll_contribution_schemes (shared)
├── id
├── jurisdiction_id (FK)
├── code (string — SSNIT_EE, SSNIT_ER)
├── name (string)
├── applies_to (enum: employee, employer, both)
├── effective_from (date)
├── effective_to (date, nullable)
├── rate (decimal 8,4)
├── ceiling_amount (decimal 18,4, nullable)
├── ceiling_period (enum: monthly, annual, nullable)
├── timestamps
```

### 4.4 Tax/contribution resolver service

```php
PayrollRulesResolver::forDate(Jurisdiction $jurisdiction, Carbon $date): JurisdictionRules
```

Returns the active brackets and schemes for a specific date. Pure read, cacheable.

### 4.5 Management screens

- **JurisdictionResource** — super-admin only (shared data)
- **TaxBracketResource** — effective-dated rows, inline repeater
- **ContributionSchemeResource** — effective-dated rows

### 4.6 Seeders

Cite the official source, date, and URL for every rate.

### 4.7 Tests for Step 4

- Bracket resolution for a given date: correct bands returned
- Rate change effective mid-year: two different results for dates on either side
- Ceiling enforcement: contribution stops at cap
- No bracket gaps or overlaps within a jurisdiction
- Super-admin can manage; company admin cannot

### 4.8 Checklist

- [ ] Jurisdictions, tax brackets, contribution schemes migrations
- [ ] Models (no `BelongsToCompany` — these are shared reference data)
- [ ] `PayrollRulesResolver` service
- [ ] Management resources (super-admin gated)
- [ ] Seeder with cited rates
- [ ] Translations
- [ ] Tests pass

---

## Step 5 — Calculation engine

**Goal:** A pure, testable calculator that takes an employee's terms, components,
hours, and jurisdiction rules, and produces a complete payslip breakdown.

### 5.1 The calculator service

```php
PayrollCalculator::calculate(
    EmploymentTerms $terms,
    Period $period,
    array $inputs,         // one-off items, unpaid leave days (no hours — salaried only per DP-02)
    JurisdictionRules $rules,
    Collection $components,
    Collection $overrides,
    ?EmployeeLoan $activeLoan,
): PayrollResult
```

**PayrollResult** is a value object:
- `grossPay` — sum of all earnings
- `taxableIncome` — gross minus exempt items
- `deductions[]` — each with component code, base, rate, amount
- `employerContributions[]` — same shape
- `netPay` — gross minus all deductions
- `ytdTotals` — year-to-date for each component
- `rulesVersion` — date/id of the tax brackets and rates used (for audit)

### 5.2 Calculation order

1. **Basic pay**: `base_pay` from employment terms (salaried only per DP-02 — no hourly calculation in v1)
2. **Earnings**: each earning component, in sort order, calculated on its base
3. **Gross**: sum of all earnings
4. **Pre-tax deductions**: voluntary deductions that reduce taxable income (e.g. Tier 3)
5. **Taxable income**: gross minus pre-tax deductions
6. **Income tax (PAYE)**: progressive brackets applied to taxable income
7. **Post-tax statutory deductions**: SSNIT/NAPSA employee, Tier 2, NHIL/NHIMA — each as percentage of gross (or of basic, per the scheme's basis)
8. **Employer contributions**: SSNIT/NAPSA employer, etc. — not deducted from employee
9. **Voluntary deductions**: loan repayments, other
10. **Net pay**: gross minus all employee deductions

### 5.3 Rounding rule

Round each component to the currency's `decimal_places` (2 for GHS/ZMW) at the
component level, not at the end. Document the rounding point. Test that
`sum(components) == total` to the cent.

### 5.4 Tests for Step 5

**These are the most important tests in the plugin.**

- Standard calculation produces expected gross, deductions, net
- Progressive tax: employee in 3rd bracket pays correct blended rate
- SSNIT ceiling: stops at cap, no over-deduction
- Zero-pay employee: no division by zero, net = 0
- Deduction larger than remaining pay: net floored at 0 (or error — decide in DP)
- Overtime: correct rate applied
- Loan deduction: reduces balance, stops when repaid
- Component override beats default
- Effective date: old rate vs new rate for same employee on different dates
- Idempotent: same inputs → identical output
- YTD accumulation correct across months

### 5.5 Checklist

- [ ] `PayrollCalculator` service (no DB dependency — pure function)
- [ ] `PayrollResult` value object
- [ ] `Period` value object
- [ ] Unit tests (not feature tests — no database)
- [ ] Tests pass

---

## Step 6 — Pay runs

**Goal:** A company admin creates a pay run for a period, calculates payroll for
all eligible employees, reviews, approves, and locks it.

### 6.1 Migration: `payroll_runs`

```
payroll_runs
├── id
├── company_id (FK)
├── name (string — e.g. "October 2026 Payroll")
├── period_start (date)
├── period_end (date)
├── pay_date (date — when employees are paid)
├── state (enum: draft, calculated, approved, posted, cancelled)
├── total_gross (decimal 18,4, default 0)
├── total_deductions (decimal 18,4, default 0)
├── total_employer_cost (decimal 18,4, default 0)
├── total_net (decimal 18,4, default 0)
├── employee_count (integer, default 0)
├── calculated_at (timestamp, nullable)
├── approved_at (timestamp, nullable)
├── approved_by_id (FK users, nullable)
├── posted_at (timestamp, nullable)
├── cancelled_at (timestamp, nullable)
├── notes (text, nullable)
├── created_by_id (FK users)
├── timestamps
├── UNIQUE(company_id, period_start, period_end) — DB-level double-run guard
```

### 6.2 Migration: `payroll_run_lines`

One per employee per run.

```
payroll_run_lines
├── id
├── run_id (FK)
├── company_id (FK — InheritsParentCompany from run)
├── employee_id (FK)
├── employment_term_id (FK — snapshot: which term was active)
├── gross_pay (decimal 18,4)
├── taxable_income (decimal 18,4)
├── total_deductions (decimal 18,4)
├── total_employer_cost (decimal 18,4)
├── net_pay (decimal 18,4)
├── rules_version (string — tax bracket date reference)
├── timestamps
├── UNIQUE(run_id, employee_id) — one payslip per person per run
```

### 6.3 Migration: `payroll_run_line_details`

One per component per line.

```
payroll_run_line_details
├── id
├── run_line_id (FK)
├── company_id (FK — InheritsParentCompany from line)
├── pay_component_id (FK)
├── component_type (enum: earning, deduction, employer_contribution)
├── base_amount (decimal 18,4 — what the % was applied to)
├── rate (decimal 10,4, nullable)
├── amount (decimal 18,4 — the actual money)
├── is_taxable (boolean)
├── sort_order (integer)
├── timestamps
```

### 6.4 State machine

```
draft → calculated → approved → posted
  ↓         ↓           ↓
cancelled cancelled  (cannot cancel after posting — use reversal)
```

- **draft**: created, employees not yet calculated
- **calculated**: engine has run for every eligible employee; totals filled
- **approved**: a user with `approve_payroll` permission has signed off
- **posted**: journal entry created (Step 7); run is now immutable
- **cancelled**: run voided before posting

### 6.5 PayRunResource

Split schema. Table shows: name, period, state, totals, employee count.
Actions:
- **Calculate** — runs engine for all eligible employees, fills lines
- **Approve** — requires `approve_payroll` permission
- **Post** — creates journal entry (Step 7)
- **Cancel** — only from draft/calculated/approved, not posted
- **View lines** — relation manager showing per-employee breakdown

### 6.6 Tests for Step 6

- Unique period constraint prevents double run
- Calculate fills correct lines and totals
- State transitions: only valid transitions allowed
- Posted run cannot be edited or deleted
- Cancelled run cannot be re-opened
- Company isolation
- Permission enforcement on each action

### 6.7 Checklist

- [ ] `payroll_runs` migration
- [ ] `payroll_run_lines` migration
- [ ] `payroll_run_line_details` migration
- [ ] Models with `InheritsParentCompany` on lines
- [ ] `PayRunService` orchestrating calculation for all employees
- [ ] `PayRunResource` with state-aware actions
- [ ] Relation manager for run lines
- [ ] Translations
- [ ] Tests pass

---

## Step 7 — Ledger posting and reversal

**Goal:** Post an approved run as a balanced journal entry in the tenant's own
books, and reverse a posted run by compensating entry.

### 7.1 Migration: `payroll_postings`

```
payroll_postings
├── id
├── run_id (FK, unique — idempotent)
├── company_id (FK — InheritsParentCompany)
├── move_id (FK accounts_moves — the journal entry)
├── reversal_move_id (FK accounts_moves, nullable)
├── reversed_at (timestamp, nullable)
├── reversed_by_id (FK users, nullable)
├── timestamps
```

### 7.2 Posting service

Pattern: `Logistics\Services\ExpensePoster` — the codebase's existing example.

Journal entry structure for a run:

| Line | Account | Debit | Credit |
|---|---|---|---|
| Salary expense | `salary_expense_account_id` from settings | total gross + employer contributions | — |
| Net pay liability | `net_pay_liability_account_id` | — | total net |
| PAYE payable | Statutory account (configurable) | — | total PAYE |
| SSNIT payable | Statutory account | — | total SSNIT (employee + employer) |
| Other deductions | Per component | — | each total |

**Debit = Credit** — assert in the test, not by inspection.

### 7.3 Rules (from project memory)

- Set `DisplayType::PRODUCT` on every `MoveLine`
- One move per run, guarded by `payroll_postings` unique key
- Reversal is a compensating entry, never a delete
- Read the run with `withoutGlobalScopes()` when posting

### 7.4 Tests for Step 7

- Posted move balances (debit = credit)
- Double-post refused (idempotent)
- Reversal creates compensating entry
- Correct accounts used from company settings
- Move lines use `DisplayType::PRODUCT`

### 7.5 Checklist

- [ ] `payroll_postings` migration
- [ ] `PayrollPoster` service
- [ ] Reversal action
- [ ] Translations
- [ ] Tests pass

---

## Step 8 — Payslip documents

**Goal:** Generate PDF payslips for each employee in a posted run.

### 8.1 Migration: `payroll_payslips`

```
payroll_payslips
├── id
├── run_line_id (FK, unique)
├── company_id (FK — InheritsParentCompany)
├── number (string — sequence via SequenceService)
├── file_path (string, nullable — stored PDF)
├── file_hash (string, nullable — integrity check)
├── generated_at (timestamp, nullable)
├── timestamps
```

### 8.2 Payslip template

Blade view rendered to PDF via `PDFHandler` (pattern: logistics waybill).
Shows:
- Company name and logo
- Employee name, department, position
- Pay period
- Earnings breakdown (each component, rate, amount)
- Deductions breakdown
- Employer contributions (informational)
- Net pay
- YTD totals
- Bank account (masked)

### 8.3 Sequence

`SequenceService::ensure('payroll.payslip', $companyId)` — per-company numbering.

### 8.4 Email delivery (DP-05: PDF + email)

After a run is posted and payslips are generated, an action emails each payslip
to the employee's email address. Uses the existing mail infrastructure
(`config/mail.php`). The email is a simple notification with the PDF attached.

- Queued via `ShouldQueue` so a 200-employee run doesn't block
- Failed sends are logged, not fatal — the payslip is still stored
- Employee with no email: skipped with a warning, not an error

### 8.5 Tests for Step 8

- Payslip generated with correct figures
- Sequence is per-company
- Re-download returns same document (no regeneration)
- Number format correct
- Email queued per employee after generation
- Missing email address: skipped, not error

### 8.6 Checklist

- [ ] `payroll_payslips` migration
- [ ] Payslip model
- [ ] Blade PDF template
- [ ] Generate action on PayRunResource
- [ ] Download action per line
- [ ] Email notification (queued)
- [ ] Translations
- [ ] Tests pass

---

## Step 9 — Net pay and payment records

**Goal:** Record per-employee payment of net salary, linked to the run.

### 9.1 Migration: `payroll_payments`

```
payroll_payments
├── id
├── run_line_id (FK)
├── company_id (FK — InheritsParentCompany)
├── employee_id (FK)
├── amount (decimal 18,4)
├── currency_id (FK)
├── bank_account_id (FK, nullable — snapshot at time of payment)
├── payment_method (enum: bank_transfer, cash, mobile_money, cheque)
├── payment_reference (string, nullable)
├── paid_at (timestamp, nullable)
├── state (enum: pending, paid, failed)
├── notes (text, nullable)
├── timestamps
```

### 9.2 Payment actions

- **Mark as paid** — bulk or individual
- **Export for bank** — CSV/text in the bank's format (future, behind DP-06)

### 9.3 Tests for Step 9

- Payment amount matches net pay
- Bank account snapshot preserved
- State transitions valid
- Company isolation

### 9.4 Checklist

- [ ] `payroll_payments` migration
- [ ] Payment model and resource
- [ ] Bulk "mark paid" action
- [ ] Translations
- [ ] Tests pass

---

## Step 10 — Reporting

**Goal:** Standard payroll reports behind Shield page permissions in a
Reporting cluster.

### 10.1 Reports (pattern: Logistics Reporting cluster)

| Report | Shows |
|---|---|
| **Payroll Summary** | Per-period totals: gross, deductions, employer cost, net. Filterable by date range. |
| **Employee Pay History** | Per-employee: every run they appeared in, with component breakdown. |
| **Department Cost** | Payroll cost grouped by department, for a period or range. |
| **Statutory Returns** | Per-period: total PAYE, SSNIT, NHIL, etc. — the figures needed for the return. |
| **Deduction Register** | All deductions by type and employee, for reconciliation. |
| **Loan Balances** | Outstanding loans by employee, with repayment history. |

### 10.2 Export

Each report exportable to CSV/Excel via Filament's `ExportAction`.

### 10.3 Tests for Step 10

- Each page behind its own page permission
- Permission name matches the generated name (the two-places trap)
- Filters applied in `getFilteredTableQuery()`, not base query
- Company isolation

### 10.4 Checklist

- [ ] Reporting cluster
- [ ] 6 report pages
- [ ] Shield permissions
- [ ] Export actions
- [ ] Permission-name assertion test
- [ ] Translations
- [ ] Tests pass

---

## Step 11 — Hardening and rollout

### 11.1 Shadow mode

Run payroll calculation alongside the company's existing method for one full
period. Compare employee by employee, to the cent. Store the comparison but
do not post it. Fix discrepancies before going live.

### 11.2 Pilot

One company, one real period: calculate → review → approve → post → pay →
reconcile bank against ledger.

### 11.3 Cutover

Per company, behind the `is_enabled` toggle. The previous method is frozen,
not deleted. No data loss.

### 11.4 Rollback

Disable `is_enabled`, reverse the period's posting by compensating entry, and
resume the previous method. Nothing is deleted; no posted journal is edited.

---

## Standard payroll setup items (per company)

When payroll is enabled for a company, the provisioner seeds these defaults.
The company admin customises them from the Payroll settings and resources.

### Earnings (configurable per company)

| Item | Default type | Notes |
|---|---|---|
| Basic Salary | Fixed amount | The employee's base pay |
| Housing Allowance | Percentage of basic or fixed | Common in Ghana/Zambia |
| Transport Allowance | Fixed amount | |
| Meal/Lunch Allowance | Fixed amount | |
| Overtime (regular) | Formula: hourly rate × 1.5 | Multiplier configurable |
| Overtime (weekend/holiday) | Formula: hourly rate × 2.0 | Multiplier configurable |
| Bonus | Fixed amount (one-off per run) | |
| Commission | Percentage or fixed | |
| Acting Allowance | Fixed or percentage | Temporary higher role |
| Responsibility Allowance | Fixed | |

### Deductions (configurable per company)

| Item | Default type | Notes |
|---|---|---|
| PAYE Income Tax | Progressive brackets | Rates from jurisdiction |
| Social Security (employee) | Percentage with ceiling | e.g. SSNIT 5.5%, NAPSA 5% |
| Social Security (employer) | Percentage with ceiling | e.g. SSNIT 13%, NAPSA 5% |
| Pension Tier 2 (employee) | Percentage | e.g. 5% |
| Health Insurance (employee) | Percentage | e.g. NHIMA 1%, NHIL 2.5% |
| Health Insurance (employer) | Percentage | |
| Skills Development Levy | Percentage | Where applicable |
| Staff Welfare | Fixed or percentage | Voluntary |
| Union Dues | Fixed or percentage | Voluntary |
| Salary Advance Repayment | Fixed amount with balance | Loan category |
| Staff Loan Repayment | Fixed amount with balance | Loan category |
| Cooperative Deduction | Fixed | Voluntary |

### Employer contributions (not deducted from employee)

| Item | Default type | Notes |
|---|---|---|
| Social Security (employer share) | Percentage with ceiling | |
| Health Insurance (employer share) | Percentage | |
| Workers' Compensation | Percentage | Where applicable |
| Training Levy | Percentage | Where applicable |

### What the company admin can customise

- **Toggle any component** on or off
- **Change the percentage** for any percentage-based component
- **Change the amount** for any fixed component
- **Set ceilings** (maximum deduction amount)
- **Add custom components** (e.g. a company-specific allowance or deduction)
- **Remove components** they don't use
- **Set effective dates** for rate changes (old rate applies to old periods)
- **Override per employee** (e.g. one employee gets a different housing rate)
- **Choose the base** for percentage components (of basic, of gross, of a specific component)

---

## Codebase traps that will hit payroll

Preserved from the analysis — every one of these has caused a real defect in
this repository. See `AGENTS.md` for full details.

| # | Trap | Rule |
|---|---|---|
| 1 | `company_id = null` is shared (visible to all tenants) | Never allow null on a payroll row; test it |
| 2 | Parent read through a relation in a model hook | Use `InheritsParentCompany` or `withoutGlobalScopes()` |
| 3 | `withoutGlobalScope(CompanyScope::class)` alone | Other scopes still hide the parent; use `withoutGlobalScopes()` |
| 4 | `SequenceService::next()` without `ensure()` | Creates a shared counter; call `ensure()` first |
| 5 | Two `PermissionRegistrar` classes | Flush both after creating permissions |
| 6 | Super-admin `Gate::before` skips policies | Guard the service, not only the policy |
| 7 | `MoveLine` without `display_type` | Set `DisplayType::PRODUCT` explicitly |
| 8 | `$move->lines` includes balancing/tax lines | Filter to your own lines |
| 9 | `$table->enum(...)->change()` on PostgreSQL | Use `DB::statement` |
| 10 | Unregistered migration | Register in `hasMigrations()`; test enforces it |
| 11 | Page permission written in two places | Assert the generated name matches |

---

## Open questions

- Is payroll a **sold feature** (per-company entitlement gated by billing) or
  always available once toggled? If sold, it needs the entitlement contract.
- Does the first customer need full payroll, or only **employee cost reporting**?
- Are employees paid in one currency only, or can a company pay in multiple?
- **Contractor** payments: through payroll or through purchasing/expenses?
- Statutory return format: does the plugin produce it, or export for a portal?
- Does any company need **multi-currency payroll** (employees in different
  countries)?

---

## References

- [`AGENTS.md`](../AGENTS.md) — binding conventions and the project memory traps.
- [`docs/logistics-plan.md`](logistics-plan.md) — the most recent plugin built end to end; the architecture reference.
- [`docs/running-tests.md`](running-tests.md) — how to run a suite.
- [`docs/saas-administration-billing-audit-plan.md`](saas-administration-billing-audit-plan.md) — multi-agent board conventions.
- [`docs/upstream-fix-adoption-plan.md`](upstream-fix-adoption-plan.md) — check before assuming upstream has a payroll component.
