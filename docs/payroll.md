# Payroll plugin — build plan

**Status: planning only. Nothing is implemented, no decision is taken, and no
package is claimed.** The user decides **when** this is built; this document
exists so that decision is made against evidence rather than memory.

Read [`AGENTS.md`](../AGENTS.md) first — every trap listed under "Project
memory" applies here, several of them unusually sharply. Read
[`docs/saas-administration-billing-audit-plan.md`](saas-administration-billing-audit-plan.md)
for the multi-agent board conventions this plan borrows, and
[`docs/running-tests.md`](running-tests.md) for how to run a suite.

---

## 1. Verdict

There is **no payroll in this application, and none upstream**. Verified
2026-10-26:

| Question | Answer | Evidence |
|---|---|---|
| Payroll plugin? | No | 29 plugins under `plugins/webkul/`; no `payroll`, `hr` or `payslips`. Upstream `aureuserp/aureuserp` has the same list plus `blogs` and `point-of-sale`, both dropped by this fork. |
| Salary, grade or contract data? | No | No `basic_salary`, `gross_salary`, `net_salary`, `salary_structure`, `pay_grade`, `PAYE`, `NAPSA` or `payslip` anywhere in `plugins/`, case-insensitive, migrations included. The `employees_employees` table carries no compensation column (`plugins/webkul/employees/src/Models/Employee.php`). |
| Payslip or payroll documents? | No | No translations, no PDF template, no sequence code. |
| Somewhere to post it? | **Yes** | Tenant ledger, journals and moves exist, and the default chart of accounts already names `Employee Payroll Taxes` and `Employer Payroll Taxes` (`plugins/webkul/accounts/database/seeders/AccountSeeder.php:304`, `:318`). |
| Hours to pay from? | No | See §3 — `timesheets` is project time, not attendance. |

So payroll is a **new plugin**, not an extension. Nothing here should be
attached to `employees`, which owns people and organisation structure only.

---

## 2. What "payroll" means here

The minimum a Zambian employer needs, and therefore the reference scope:

1. **Employment terms per employee** — effective-dated pay, pay frequency,
   currency, statutory identifiers, bank details, tax residency.
2. **A pay run** — one period per company, over a defined population, that can
   be calculated, reviewed, posted and paid exactly once.
3. **Payslips** — per-employee document showing gross, each deduction, employer
   contributions, net, and year-to-date figures.
4. **Statutory computation** — progressive employee tax, employee and employer
   social contributions, skills levy, and whatever else applies, all from
   **effective-dated configuration**, never from code constants.
5. **Ledger postings** — one balanced journal per run into the tenant's own
   books: gross expense, employer contributions expense, net payable liability,
   each statutory liability.
6. **Payment** — net pay per employee, either a bank file or per-employee
   payment records, reconciled against the posting.
7. **Reporting** — per-period summary, per-employee history, statutory return
   figures, cost by department.

### Explicit non-goals (for the first build)

- Time and attendance / clock-in hardware. Hours are an **input**, not a
  feature of this plugin (see §3).
- Recruitment, onboarding, leave approval — already owned elsewhere.
- Employee self-service portal screens (the `customer` guard belongs to
  partners; an employee portal is its own initiative).
- Any computation for a country other than the first agreed one, until DP-01 is
  answered and the engine has been proven on one jurisdiction.

---

## 3. What already exists, and what payroll may reuse

| Capability | Where | Reusable as |
|---|---|---|
| Employee master record | `plugins/webkul/employees/src/Models/Employee.php` — `BelongsToCompany`, table `employees_employees`, `user_id`, `department_id`, `job_id`, `partner_id`, `bank_account_id`, `country_id`, `private_country_id` | **Yes** — the employee population, and `bank_account_id` for net pay |
| Bank accounts | `Webkul\Partner\Models\BankAccount` (used by `Employee::$bank_account_id`) | **Yes** |
| Departments, job positions | `employees` plugin | **Yes** — cost reporting dimension |
| Leave and allocations | `time-off` — `Leave`, `LeaveType`, `LeaveAllocation`, `LeaveAccrualPlan` | **Read only** — unpaid/paid leave affects pay, but leave is not payroll's to own |
| Calendars and attendance resources | `support` — `2026_04_02_000001_create_calendars_table`, `…add_resource_columns_in_calendar_attendances_table` | Possibly, if attendance is ever captured there. Today **nothing writes attendance**. |
| Project time | `plugins/webkul/timesheets/src/Models/Timesheet.php` extends `Webkul\Project\Models\Timesheet` | **No** — this is hours on project tasks, not employee attendance. It cannot be a payroll input. |
| Chart of accounts, journals, moves | `accounts` plugin — `Webkul\Accounts\Enums\DisplayType`, `Move`, `MoveLine` | **Yes** — posting target |
| Payments | `payments` plugin, and `accounts` payment register | **Partially** — investigate before writing a second payment path |
| Sequences | `Webkul\Support\Services\SequenceService` — `ensure(string $code, ?int $companyId)` at `:35`, `next(...)` at `:17` | **Yes** — one payslip/run counter per company |
| Documents to PDF | `Webkul\Support\Traits\PDFHandler`, and the logistics waybill action as the working pattern | **Yes** |
| Custom fields and chatter per model | `Webkul\Field\Traits\HasCustomFields`, `Webkul\Chatter\Traits\HasChatter` | **Yes** |
| Multi-company scoping | `Webkul\Support\Traits\BelongsToCompany` + `CompanyScope` | **Mandatory** |
| Child rows inheriting the parent's company | `Webkul\Support\Traits\InheritsParentCompany` | **Mandatory** on every child of a run |
| Currency metadata | `currencies` table — `decimal_places`, `rounding`, `active` | **Yes** — ZMW is now active (2026-10-26) with 2 decimals |

### The one hard gap

**Nothing records hours worked.** There is no attendance table (`create
…attendance` appears nowhere), and the deleted `employees_calendars*`
migrations of commit `6f827a879` removed the old calendar tables rather than
extending them. Therefore PAY-02 below must decide, explicitly, where the hours
come from: manual input per run, an attendance module, or an import. Do not
assume `timesheets`.

---

## 4. Jurisdiction strategy

Payroll is country-specific in a way no other plugin here is. The engine must
therefore be **jurisdiction data + one generic calculator**, not one code path
per country.

### First jurisdiction: Zambia

Components to support, each as **effective-dated configuration rows**, not
constants:

- progressive employee income tax (ZRA PAYE), with the annual tax credit;
- employee and employer social security (NAPSA), with its contribution ceiling;
- employee and employer health insurance (NHIMA);
- skills development levy where applicable;
- any locally required levies, and the statutory return figures the employer
  must file.

**Do not take rates, bands or ceilings from this document, from memory, or from
an existing implementation.** They change with every national budget and a wrong
band silently over- or under-deducts real money. When the package is picked up,
the values must be transcribed from the current official ZRA / NAPSA / NHIMA
publications, dated, and cited in the seeder that carries them (DP-03).

### Second jurisdiction: nothing yet

The engine should be able to hold a second jurisdiction's rows without code
changes. Do not build a second one until the first is in production and
reconciled.

---

## 5. Domain model

Tables are prefixed `payroll_`, owned by the new plugin, every one
company-scoped unless marked **shared**.

| Table | Purpose | Company scoping |
|---|---|---|
| `payroll_company_settings` | Per-company switch (payroll enabled), jurisdiction, pay frequency, rounding, journal mapping, payslip sequence code | `company_id`, unique |
| `payroll_jurisdictions` | Jurisdiction definition: code, name, tax-year definition | **shared** (reference data) |
| `payroll_tax_brackets` | Effective-dated progressive bands per jurisdiction | **shared** |
| `payroll_contribution_schemes` | NAPSA/NHIMA/levy definitions: employee %, employer %, ceiling, basis | **shared** |
| `payroll_scheme_rates` | Effective-dated rates per scheme | **shared** |
| `payroll_employment_terms` | Per employee: effective-from, frequency, base pay, currency, jurisdiction, statutory identifiers, cost centre | `company_id` |
| `payroll_pay_components` | Named earning/deduction definitions (allowance, loan repayment, benefit) | `company_id` |
| `payroll_employee_components` | Recurring component amounts per employee, effective-dated | `company_id` |
| `payroll_runs` | One row per company per period: status, dates, totals, posting link | `company_id`, unique on (company, period) |
| `payroll_run_lines` | Per employee per run: gross, each deduction, employer cost, net, YTD snapshot | inherits run's company |
| `payroll_run_line_details` | Per component per line: quantity, rate, amount, taxable flag | inherits line's company |
| `payroll_payslips` | Rendered document record: number, path, hash, generated_at | inherits run's company |
| `payroll_postings` | Link run → `accounts` move, plus reversal link | inherits run's company |

Rules that must hold in the schema, not just in services:

- `payroll_runs` **unique per (company_id, period_start, period_end)** — that
  unique index is the database-level guard against a double run.
- `payroll_run_lines` unique per (run_id, employee_id) — one payslip per person
  per period.
- Money columns with explicit precision; see DP-04. Never a PHP float in a
  computation or a stored total.
- `posted_at` / `state` on the run, and no update path from a posted run —
  corrections are reversals plus a new run.
- Soft deletes only where the domain allows them; a posted run is immutable.

---

## 6. Calculation engine

Keep it a pure function, testable without a database:

```
calculate(EmploymentTerms $terms, Period $period, array $inputs, JurisdictionRules $rules): PayrollResult
```

- **Inputs**: hours/days if hourly, overtime, one-off components, unpaid leave.
- **Output**: gross, ordered deductions, employer contributions, net, and the
  taxable base used at each step.
- **Deterministic**: same inputs and same rules version → identical output, so a
  re-run cannot silently change a figure.
- **Rules version recorded on the line**, so a historical payslip can be
  explained after the rates change.
- **Rounding**: round once, at a defined point per component, documented in
  code and covered by tests — not accumulated float error.
- **Pro-rating and mid-period changes**: a term effective mid-period must split
  the period or refuse to calculate. Decide which (DP-05) and pin it.
- **Back-pay**: a later run must be able to pay an earlier period's correction
  without rewriting the earlier posted run.

---

## 7. Ledger integration

Posting reuses the tenant's own accounting — payroll belongs in the tenant's
books, unlike platform billing, which the SaaS plan keeps separate.

Non-negotiables, all of them learned in this codebase:

1. **Set `display_type` explicitly** on every `MoveLine` — `DisplayType::PRODUCT`
   for a normal costed line. `MoveLine`'s saving hook calls `computeAccountId()`
   before `computeDisplayType()`, so an unset type routes the line to the
   journal's default account and `MoveCalculator::recomputeLine()` zeroes
   `price_subtotal`/`price_total`. **Both failures are silent.**
2. **`$move->lines` is every line**, including the balancing payment-term line
   and tax lines. Never `count()` or `first()` it as if they were only payroll's.
3. **One journal, balanced, per run** — gross expense + employer contributions
   expense = net liability + each statutory liability. Assert debit = credit in
   the test, not by inspection.
4. **Idempotent posting**: posting a run twice must not create a second move.
   Guard on the run's state and the `payroll_postings` unique key.
5. **Reversal is a compensating entry**, never a delete or an edit.
6. **Never read a parent through a relation inside a model hook** — a globally
   scoped parent returns `null` for an actor not entitled to see it, and
   `company_id` then lands as `null`, which `CompanyScope` treats as *shared*.
   Use `InheritsParentCompany`, or read the parent with `withoutGlobalScopes()`
   filtered by its key.

---

## 8. Plugin scaffolding checklist

Modelled on `plugins/webkul/logistics/src/LogisticsServiceProvider.php`, which
is the current reference implementation.

- [ ] `plugins/webkul/payroll/src/PayrollServiceProvider.php` extends
      `PackageServiceProvider`: `static $name = 'payroll'`, `hasViews`,
      `hasTranslations`, `hasConfigFile('payroll')`, `hasMigrations([...])`
      (every migration, or `MigrationRegistrationTest` fails), `runsMigrations`,
      `hasDependencies(['employees', 'accounts'])`, `hasSeeder`,
      `hasInstallCommand` / `hasUninstallCommand` with a `startWith` guard.
- [ ] `plugins/webkul/payroll/src/PayrollPlugin.php` implements Filament's
      `Plugin`, guarded by `Package::isPluginInstalled()`, discovering resources
      only for the `admin` panel.
- [ ] Registered in `bootstrap/providers.php` next to the other plugins.
- [ ] Autoloader regenerated **as root, without scripts** (scripts hang without
      a database):
      `docker run --rm -v "$(pwd -W):/var/www/html" -w /var/www/html --entrypoint composer sail-8.4/app dump-autoload --no-scripts`
- [ ] `config/filament-shield.php` in the plugin: `resources.manage` and the
      exclude lists only — `pages.manage` and `custom_permissions` there are
      ignored.
- [ ] Page permissions named `page_payroll_<page_snake>` **and** generated under
      that same name by `PermissionManager::managePermissions()`; assert the
      generated set in a test, because the two spellings are written in two
      places and a mismatch makes the page unopenable while every other test
      passes.
- [ ] A `phpunit.xml` `<testsuite name="PayrollFeature">` block — a new plugin
      with no suite means its tests never run.
- [ ] Translations in all five locales (`en`, `ar`, `es`, `fr`, `pt_BR`), same
      structure, quoted numeric keys, `php artisan translations:check
      --references` clean.
- [ ] Menu group + icons + `HasFilamentDefaults`, matching sibling plugins.
- [ ] New resources split into `Schemas/XForm.php`, `Schemas/XInfolist.php`,
      `Tables/XsTable.php` — the shape Filament's own generator produces and
      upstream moved to.
- [ ] Factories and seeders for every model, plus statutory seeders carrying
      cited, dated values.

---

## 9. Work packages

Each package owns only the files it lists, gets its own PostgreSQL database
`aureuserp_testing_payroll_<package>`, and leaves a handoff. Sequential where a
production file is shared.

| # | Package | Depends on | Owns | Deliver |
|---|---|---|---|---|
| PAY-00 | Baseline inventories and characterization | — | new tests only | Inventory every place employee pay data would have to come from and go to; characterize the current absence with a failing-first test per gap; confirm no other plugin assumes payroll exists. |
| PAY-01 | Jurisdiction and statutory configuration | PAY-00, DP-01, DP-03 | plugin skeleton, jurisdictions/brackets/schemes tables + seeders | Effective-dated tables, cited seed data for the first jurisdiction, a read API that resolves rules for a date. Tests for boundary dates across a rate change. |
| PAY-02 | Employment terms and the hours input decision | PAY-01, DP-02, DP-05 | terms table, screens, resolvers | Effective-dated terms per employee, per-company enable switch, and the decided source of hours (manual, attendance, import) with its validation. |
| PAY-03 | Calculation engine | PAY-02, DP-04 | engine + unit tests | Pure calculator, deterministic, rules-version stamped, rounding defined, pro-rating and mid-period change behaviour pinned by tests. |
| PAY-04 | Pay runs | PAY-03 | runs + lines tables, run screens, state machine | Create, calculate, review, lock. Unique period index; a second run for the same period is refused at the database level. |
| PAY-05 | Payslip documents | PAY-04, DP-06 | payslip model, template, PDF action, sequence | Numbered per company via `SequenceService::ensure()`; deterministic content; stored path; re-download without regeneration. |
| PAY-06 | Ledger posting and reversal | PAY-04, DP-07 | posting service, journal mapping | One balanced move per run, idempotent, reversible by compensating entry; mapping configurable per company; tested for balance and for double-post refusal. |
| PAY-07 | Net pay and payment hand-off | PAY-06, DP-08 | payment records or bank file | Pay the net per employee, reconcile against the posting, handle a changed bank account mid-run without rewriting history. |
| PAY-08 | Reporting | PAY-06 | report pages in a cluster | Period summary, employee history, cost by department, statutory figures for the return. Behind their own page permissions. |
| PAY-09 | Hardening and rollout | all | — | Shadow-mode comparison against the method it replaces, cutover runbook, rollback, retention. |

PAY-00 through PAY-03 are the expensive, unglamorous half. Nothing after PAY-03
is safe to build before the engine is proven, and nothing at all is safe before
the decisions below are answered.

---

## 10. Test strategy

Reuse the existing suites' conventions (`Pest`, `DatabaseTransactions`, one
database per package). Invariants to pin, each as its own test:

- **Money never drifts**: sum of line components equals the line total; sum of
  lines equals the run total; run totals equal the posted move, to the cent.
- **Boundary dates**: a rate change effective mid-year, a period spanning it, a
  leap day, a month-end pay date, a 30/31-day month, a year boundary.
- **Ceilings**: contribution stops exactly at the ceiling, and a part-month at
  the ceiling does not over-deduct.
- **Zero and negative cases**: zero-pay employee, a deduction larger than net,
  a terminated employee mid-period.
- **Idempotency**: calculate twice → identical; post twice → one move; generate
  payslips twice → one document.
- **Concurrency**: two processes racing the same period — exactly one run, one
  posting, one payslip. Assert the precondition (no run exists) before racing.
- **Tenant isolation**: a user in company A cannot see, run, pay or post
  company B's payroll; a child row always carries its parent's company.
- **Permissions**: every page and action denied without its permission, and the
  generated page-permission name matches the one the page checks.
- **Immutability**: a posted run cannot be edited or deleted through any path,
  including Filament bulk actions and the API.
- **Documents**: payslip renders, contains the right figures, and the filename
  survives the Symfony `Content-Disposition` rules (the waybill bug).

---

## 11. Decision gates

Record the answer here before unblocking a dependent package.

| Decision | Question | Recommendation | Answer |
|---|---|---|---|
| DP-01 | Which jurisdiction first, and is it the only one for the first release? | Zambia only, one jurisdiction, engine generic | pending |
| DP-02 | Where do hours come from: manual entry per run, a new attendance capture, or an import? | Manual entry per run first; attendance is a separate feature | pending |
| DP-03 | Which statutory sources, and how are rate changes governed? | Effective-dated rows transcribed from current official publications, cited in the seeder, reviewed annually | pending |
| DP-04 | Money representation: `decimal(18,4)` like the tenant ledger, or integer minor units? | Integer minor units inside the calculator; decimal columns to match `accounts`, with one documented conversion point | pending |
| DP-05 | Mid-period pay change: split the period, or refuse and require two runs? | Refuse, and require a period split — no silent pro-rating | pending |
| DP-06 | Payslip delivery: stored PDF only, emailed, or both? | Stored PDF first; email later via the existing mail infrastructure | pending |
| DP-07 | Journal mapping: fixed per company, or configurable account per component? | Configurable per component, defaulted per company | pending |
| DP-08 | Net pay: bank file format, per-employee payment records, or both? | Per-employee payment records first; bank file once a bank's format is agreed | pending |
| DP-09 | Who may run payroll, approve it, and post it — one role or three? | Separate run, approve and post permissions; Super Admin holds all three initially | pending |
| DP-10 | Does payroll need to reuse `payments`, or own its net-pay path? | Investigate `payments` in PAY-00 and answer from evidence, not preference | pending |

---

## 12. Traps specific to this codebase

Each of these has already caused a real defect somewhere in this repository.
They are listed here because payroll will hit every one of them.

| Trap | What it does | Rule |
|---|---|---|
| `CompanyScope` treats `company_id = null` as shared | A row with no company is visible to **every** tenant | Never allow a null company on a payroll row; test it |
| Reading a parent through a relation in a model hook | Returns `null` for a scoped actor, so `company_id` becomes null (shared), or the actor's company, or the stored value is wiped | `InheritsParentCompany`, or `withoutGlobalScopes()` filtered by key |
| `withoutGlobalScope(CompanyScope::class)` alone | Other global scopes (`OwnershipScope`) still hide the parent | Prefer `withoutGlobalScopes()` for a parent read |
| `SequenceService::next()` without `ensure()` | Creates an installation-wide counter shared by every company | `ensure($code, $companyId)` first, per company |
| Two `PermissionRegistrar` classes | Flushing one leaves the other's cache stale, so `can()` returns false for a permission that exists | Flush both after creating permissions |
| Super-admin `Gate::before` | Skips policies entirely, so services must repeat business guards | Guard the service, not only the policy |
| `Currency::code` is an accessor, not a column | Pushing it into SQL throws `Undefined column` and 500s the page | Use `->name` in queries; `->code` only in PHP |
| `MoveLine` without `display_type` | Silently mis-posts to the journal's default account and zeroes the subtotal | Set `DisplayType::PRODUCT` explicitly |
| `$move->lines` | Includes the balancing and tax lines | Filter to your own lines before asserting anything |
| `$table->enum(...)->change()` on PostgreSQL | Renders as invalid SQL; `migrate:fresh` dies and every test in the suite fails with 0 assertions | Use `DB::statement` for a default change |
| Unregistered migration | The file exists, nothing runs it, everything looks fine | Register in `hasMigrations([...])`; `MigrationRegistrationTest` enforces it |
| Plugin without a `phpunit.xml` suite | Tests exist and never run | Add the suite block |
| Page permission written in two places | Page is unopenable for everyone in production while tests pass | Assert the generated set |
| Two heavy suites at once on this Docker host | Even on separate databases, both slow to a crawl and can appear hung for tens of minutes — they share the `storage/` and `bootstrap/cache` bind mount | Run one suite at a time on this machine, regardless of database |

---

## 13. Rollout

1. **Shadow mode.** Calculate alongside the method currently in use for at least
   one full period, per company. Compare employee by employee, to the cent.
   Store the comparison; do not post it.
2. **Pilot.** One company, one real period: calculate, review, post, pay,
   reconcile the bank against the ledger.
3. **Cutover** per company, behind the per-company enable switch, with the
   previous method frozen rather than deleted.
4. **Rollback.** Disable the company switch, void the period by compensating
   entry, and resume the previous method. Nothing is deleted; no posted journal
   is edited.

---

## 14. Open questions

- Is payroll a **sold feature** (per-company entitlement) or always available?
  If sold, it needs the plugin-entitlement contract that does not exist yet
  (`ENT-01` in the SaaS plan), not just an `enabled` flag.
- Does the first customer need **payroll**, or only **employee cost reporting**
  from data entered elsewhere? The second is far smaller and may be the real
  requirement.
- Are employees paid in ZMW only, or in a currency different from the company's
  books? That decides whether FX enters payroll at all.
- Does anyone need **contractor** payments through payroll, or is that
  purchasing/expenses?
- Which statutory return format is mandatory, and does payroll produce it or
  export for a portal?

---

## 15. References

- [`AGENTS.md`](../AGENTS.md) — binding conventions and the project memory traps.
- [`docs/saas-administration-billing-audit-plan.md`](saas-administration-billing-audit-plan.md) — evidence-matrix and multi-agent board conventions reused here.
- [`docs/logistics-plan.md`](logistics-plan.md) — the most recent plugin built in this repository, end to end.
- [`docs/running-tests.md`](running-tests.md) — how to run a suite, one database and one run at a time.
- [`docs/upstream-fix-adoption-plan.md`](upstream-fix-adoption-plan.md) — check before assuming upstream has a component worth backporting.
