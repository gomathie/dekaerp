# Attendance — Plan

Status: **in progress (2026-11-11, user: "build all of it from A to Z, step
by step").** Decisions AT-D1 to AT-D6 were answered by the user on
2026-11-11, each as recommended (§5). *Superseded the same day: "plan only,
no code".* Requested by the user together
with a welcome banner on every module dashboard (the banner shows the
clock-in control once attendance exists).

Read `AGENTS.md` (project memory and protected decisions) first. Multi-company
rules apply throughout: an attendance row belongs to the **employee's**
company, never the session's.

## Progress log

**2026-11-11 — first full `AttendanceFeature` run: 29 of 31; two real findings.**
On `aureuserp_testing_attendance` (639.64s, 74 assertions):

- **Clock-out in the same second as clock-in failed (product bug).**
  `afterCheckIn()` compared in memory with microseconds; the column stores
  whole seconds, so the saved row had check-out equal to check-in and the
  database check `attendances_check_out_after_check_in` refused it. The banner
  showed "failed" (the Livewire button reports and swallows the exception).
  Found by a direct-service probe (since deleted). Fixed: compare at whole
  seconds; pinned by "closes an attendance clocked out within the same second
  as the clock-in".
- **An install test asserted the opposite of a protected rule.** "derives
  attendance ownership from its employee" expected a contradicting
  `company_id` to be silently replaced. `InheritsParentCompany` refuses a
  mismatch by design (AGENTS.md). The test now pins both halves: no company
  given takes the employee's; a contradicting one throws
  `CompanyMismatchException`.

Pint was run over the plugin. **Re-run: `AttendanceFeature` 32 passed, 79 assertions, 690.83s.** New AT-4 test `ManagerViewsTest` (manager sees own and team rows only; an employee without a team sees only their own; no team and no permission cannot open the list): 2 passed, 9 assertions. Both on `aureuserp_testing_attendance`.

**2026-11-11 — the "maximum call stack" fatal is fixed; it was in Employees.**
Taken over from the previous agent. `pest --debug` showed the fatal in "refuses
a correction when the target company is outside the active context"; a
STDERR-marker probe put it at creating an employee for a company other than
the acting one; `pg_stat_activity` showed an endless `chatter_followers`
insert. Cause: `Employee`'s saved hook updated its contact through the
company-scoped `Partner::updateOrCreate(['id' => ...])`, missed it, created a
duplicate, re-saved and recursed. Fixed in `Employee::handlePartnerUpdation()`
(scope-free read; employees `EmployeeContactCompanyTest`), pushed to
production as `4cff41b6f`. `AttendanceServiceTest` then passed in full: with
the new employees test, **11 passed, 34 assertions, 544.87s** on
`aureuserp_testing_attendance`. The previous agent's probe test was deleted.
Remaining before release: the installation file in full (AT-1), the manager
screens (AT-4), Pint, the `SupportFeature` and `EmployeeFeature` suites.

**2026-11-11 — AT-3 to AT-7 built; verification running.** Everything below is
code-complete. Nothing is claimed as verified until a run is named here.

- **Built:** AT-3 banner (shared `DashboardBannerRegistry` in `support`, one
  `PAGE_START` hook scoped to every dashboard, attendance status pill and clock
  button through it), AT-4 manager screens (read-only resource narrowed to
  `visibleEmployeeIds()`, "Clocked in now" filter, audited correction,
  privacy-safe export), AT-5 kiosk (design note only, per AT-D2), AT-6 reports
  (Lateness, Overtime, Absence in a Reporting cluster, each behind its own
  Shield page permission and the company switch) plus the payroll hand-off
  contract, AT-7 five locales and the release notes.
- **Verification so far:** PHP lint clean on every new file;
  `translations:check --plugin=attendances` and `--plugin=support` each passed
  4 of 4 locales. Database `aureuserp_testing_attendance`.
- **Suite:** run one file at a time (the user asked for that, the machine also
  runs another agent's suite). Results are appended below as they arrive.
- **Not yet run:** Pint, `SupportFeature`, `EmployeeFeature`, the structure
  snapshot comparison.

**2026-11-11 — report tests, first run: five failures, all in the test itself.**
`AttendanceReportsTest` failed with a `TypeError` before any assertion:
`AttendanceHelper::enable()` returns the company setting, and the test passed
that to `AttendanceHelper::employee()`, which takes a company. The helper now
returns the `Company` it enabled (5 failed, 0 assertions, 546.68s). Three more
defects were found by reading while it ran, and are fixed with it:

- `CalendarAttendanceFactory` fills `display_type`, and the schedule reader
  skips any line that has one, so the test calendar had no working hours at
  all. The fixture now sets `display_type` to null explicitly.
- The report queries dropped `whereNotNull('check_out_at')`: someone who
  arrived late and has not left yet is late, and someone still clocked in has
  not been absent. Overtime still needs `worked_minutes`, so an open row simply
  reports none. Pinned by "counts someone still clocked in as late, and not as
  absent".
- `lateRows`/`overtimeRows`/`absenceRows` no longer accept a null employee
  list. `null` used to mean "every employee in every company" while the query
  deliberately reads scope-free - one caller away from a cross-tenant report.
  The pages always passed an explicit list; now the signature requires one.

**2026-11-11 — report tests, combined run: the computation passes, one page test was wrong.**
Combined run of both report files: **10 passed, 1 failed (22 assertions,
683.99s)** on `aureuserp_testing_attendance`.
`AttendanceReportsTest` passed all six, including the new open-row case.
`ReportsTest` passed four: the generated page permission names, the page
permission gate, the company switch and the own-rows-only query. The last one
failed on
`duplicate key value violates unique constraint "employees_employees_user_id_unique"`:
`EmployeeFactory` fills `user_id` with the first user, so two employees created
in one test collide. That test now passes `user_id => null` for both.

**2026-11-11 — report tests: all green.** `ReportsTest` 5 of 5 passed
(434.62s bootstrap, 589.72s total for the combined run with the banner file) on
`aureuserp_testing_attendance`. Together with the six `AttendanceReportsTest`
cases, every attendance report behaviour is now verified: the generated page
permission names, the page permission gate, the company switch, the own-rows
narrowing, manager visibility, and the lateness/overtime/absence arithmetic.

**2026-11-11 — banner tests: 4 of 5, the fifth a missing import.**
`DashboardBannerTest` passed the greeting, the keyed registry, the visibility
gate and the no-employee case. The clock-in/out case failed with
`Target class [Webkul\Attendance\Models\ScheduleReader] does not exist`:
`Attendance::displayTimezone()` had been changed mid-run to use
`ScheduleReader` before its `use` statement was added, so the unimported name
resolved inside the model's own namespace. The import is in; the file is
re-running with `AttendanceServiceTest`.

**2026-11-11 — clock times follow the employee's own time zone.**
`ScheduleReader::timezone()` now reads the employee's own `time_zone` column
before the calendar's, and `Attendance::displayTimezone()` delegates to it
instead of reading `employee->calendar->timezone` itself. Without that, a
person with their own zone and a shared calendar clocked in at the calendar's
zone, and the local week/day in the reports could be the wrong one. The two
implementations also disagreed, which is how one of them gets forgotten on the
next edit. Pinned by the existing "stores open and closed times in UTC and
records the worked minutes on check-out" case, which sets the employee's zone
to Africa/Lusaka and expects 10:00 local for an 08:00 UTC check-in.

**2026-11-11 — banner tests green; the service file still crashes PHP.**
`DashboardBannerTest` passed 5 of 5 (315.70s bootstrap) after the missing import
was added: greeting, keyed section, visibility gate, clock in and out, and the
no-employee case. `AttendanceServiceTest`, in the same run, died with the same
`Maximum call stack size ... reached during compilation` fatal at
`Illuminate/Database/ConcurrencyErrorDetector.php:20` that blocked it before.
Re-running the file with the guard disabled
(`php -d zend.max_allowed_stack_size=-1 vendor/pestphp/pest/bin/pest ...`) ended
in SIGSEGV (exit 139), so this is a genuine unbounded stack use somewhere in
that file's path, not a limit that is merely set too low. Nothing in it is
claimed as verified; the next attempt raises the limit instead of removing it.

**2026-11-11 — what a step-by-step probe of the service paths showed.** A
throwaway probe test (since deleted) replayed the service scenarios in one
process with a marker before each. It confirmed, by observation rather than by
the pinned assertions:

- the disabled switch refuses with `AttendanceNotEnabledException`;
- a supplied `employee_id` for another person refuses with
  `NotYourAttendanceException`;
- an empty forced company context refuses with
  `CorrectionRefusedException::NO_EMPLOYEE` - the AT-2 boundary;
- clock-in then clock-out complete, including the employee's own time zone.

Nothing in that run exhausted the stack, so the fatal in
`AttendanceServiceTest` is not one of those service paths as such. The probe
also showed that `CompanyContext::forceActive()` persists for the rest of the
process, so a test that forces a context changes the ones after it - which is
why the probe's later scenarios all refused with "the acting user has no active
company". That is a probe artefact, but it is worth knowing before writing more
service tests.

**2026-11-11 — banner tests: a stale switch reads as off.**
`DashboardBannerTest` enabled attendance with `CompanyProvisioner::enable()`
directly after `appliesToViewer()` had already cached the company's flag, so
the second assertion would have read the pre-enable value.
`AttendanceAccess` caches every company's flag for the request; the helper's
`AttendanceHelper::enable()` flushes it, and the tests now use that.

**2026-11-11 — continuation status.** The Attendance plugin's data layer and
the initial registration/test files were already present and tracked when this
continuation resumed. They have not yet been verified here.

- **Done in this continuation:** recorded the status and package board here;
  corrected the helper include path in both AttendanceFeature test files after
  the initial run failed before discovery.
- **Present, pending verification:** the three migrations, models, access and
  company provisioning classes, policy, clock-in/out and correction service,
  schedule reader, payroll adapter, auto-checkout command, provider and
  Filament plugin registration, Shield configuration, English strings,
  `AttendanceFeature` suite registration, test helper, and initial installation
  and service tests.
- **In progress:** AT-1; the migration and company provisioning checks pass,
  including the application-rebuild sequence, and the database's single-open
  index test passes. The employee-company ownership test remains blocked by
  the PHP stack-size fatal noted below. Shield permission generation and the
  complete installation test file remain unverified.
  AT-2's forced-context fallback is fixed and its focused regression test
  passes. Correction now checks the target company against the active context
  in both the policy and service; its focused behavior test was attempted but
  stopped at PHP's stack-size fatal in
  `Illuminate/Database/ConcurrencyErrorDetector.php:20`, before returning test
  counts. Other service behaviors remain to be verified. AT-3 banner research
  has started; implementation has not.
- **Not started:** AT-4 through AT-7. The tagged topbar override is owned by a
  concurrent agent and is out of scope.
- **Verification:** local PostgreSQL is running and the isolated database
  `aureuserp_testing_attendance` was created. In Sail,
  `docker compose run --rm --no-deps --user root --entrypoint composer
  laravel.test dump-autoload --no-scripts` completed successfully (exit 0).
  Composer emitted PSR-4 warnings for global test helper classes, including
  `AttendanceHelper`, and unrelated plugin helpers. The first
  `AttendanceFeature` attempt exited before test discovery: the tests require
  `tests/Helpers/AttendanceHelper.php` from the wrong relative path. No
  assertions ran. After correcting that path, an isolated install assertion
  passed (1 test, 4 assertions, 449.40s) on
  `aureuserp_testing_attendance`. `PluginInstallationTest.php` then stopped
  with 1 passed, 1 failed, 3 pending, 5 assertions in 412.04s; creating a
  company did not create its default-off `CompanySetting` row. The provider's
  process-static listener guard was the cause: application rebuilds replace
  Eloquent's dispatcher but preserve the static flag. The guard is now stored
  in the application container, so repeat boots in one application remain
  idempotent while a rebuilt application registers its own listener. The
  focused provisioning test passed after that change (1 test, 3 assertions,
  546.96s) using the clean `aureuserp_testing_attendance` database. Then the
  install-table and company-provisioning tests passed together (2 tests, 7
  assertions, 435.66s), and all three install/provisioning tests passed (3
  tests, 9 assertions, 437.60s), also on
  `aureuserp_testing_attendance`. A full `PluginInstallationTest.php` attempt
  exited with a PHP stack-size fatal at
  `Illuminate/Database/ConcurrencyErrorDetector.php:20`; it did not provide a
  complete suite result. The full service suite's debug run stopped at
  `does not fall back to the default company inside an empty forced context`:
  3 passed, 1 failed, 11 pending, 7 assertions, 545.85s. The exception was not
  thrown because `employeeForCurrentCompany()` fell back from `currentId()` to
  `default_company_id`, violating the forced-context boundary. The fallback is
  removed; the focused test passed (1 test, 2 assertions, 498.21s) using
  `aureuserp_testing_attendance`. Remaining service behaviors are still
  unverified. A subsequent full `AttendanceServiceTest.php` run also exited
  with the PHP stack-size fatal at `ConcurrencyErrorDetector.php:20`, before it
  returned test counts. The earlier non-debug full
  attempt also ended in PHP's `Maximum call stack size ... reached during
  compilation` fatal at `Illuminate/Database/ConcurrencyErrorDetector.php:20`;
  whether that was downstream of continued failures remains unverified. A
  separate direct `migrate:fresh` diagnostic failed on the existing ordering
  where the Employee migration references `calendars` before Support creates
  it; this bypassed the test helper's migration-path filtering and is not
  evidence against the attendance install path. Full Shield generation is
  intentionally not bypassed. After the later service-suite fatal, the
  single-open partial-index test passed in isolation (1 test, 1 assertion,
  368.60s) on `aureuserp_testing_attendance`. The isolated employee-company
  ownership test then exited with the same PHP stack-size fatal at
  `ConcurrencyErrorDetector.php:20`, before returning test counts. Translation
  checks have not run.

**AT-1 rationale — company listener registration (2026-11-11).** **Why:** a
process-static flag survives Laravel test application rebuilds, but rebuilding
the application replaces Eloquent's event dispatcher; later companies then
miss their settings row. **How:** `AttendanceServiceProvider` records listener
registration in the current application container; the existing
`provisions each new company with attendance disabled by default` test pins
the behavior. **Benefit:** settings are provisioned in each application
lifecycle without duplicate callbacks when a provider boots twice in one
application. **Trade-off / risk:** container state is intentionally not shared
between application instances, so each new instance registers one callback.
**Verification:** the application-rebuild sequence passed (2 tests, 7
assertions), the three install/provisioning tests passed (3 tests, 9
assertions), and the partial-index test passed in isolation (1 test, 1
assertion), all on `aureuserp_testing_attendance`. The complete installation
file still needs a successful run; a prior attempt ended in PHP's stack-size
fatal at Laravel's `ConcurrencyErrorDetector`. **Rollback:** revert the guard
to the previous static flag only if the application lifecycle is changed so
the Eloquent dispatcher outlives the provider; otherwise that reintroduces
the missing-listener regression.

**AT-2 rationale — empty forced company context (2026-11-11).** **Why:** a
forced context that resolves to no active company must not fall through to the
user's default company; that would clock them into a different tenant.
`CompanyContext::currentId()` already enforces this boundary. **How:**
`AttendanceService::employeeForCurrentCompany()` now refuses the empty context
with the existing no-employee refusal and always filters by the resolved
company. The behavior-focused test pins the refusal message. **Benefit:** no
cross-company clock-in/out when a forced company is invalid or disallowed.
**Trade-off / risk:** callers must provide a valid active company to use the
banner; absence now refuses rather than selecting a default. **Verification:**
the focused regression passed (1 test, 2 assertions, 498.21s,
`aureuserp_testing_attendance`). The complete service file remains
unverified; it exited with a PHP stack-size fatal at Laravel's
`ConcurrencyErrorDetector` before returning test counts.
**Rollback:** restoring the `default_company_id` fallback would violate the
existing `CompanyContext` forced-context decision and reintroduce the
cross-tenant fallback.

**AT-2 rationale — correction company boundary (2026-11-11).** **Why:** the
correction permission is global, but it must not authorize modifying an
attendance record outside the actor's active company context. The prior
service and policy checked the permission/module without that record-company
boundary. **How:** `AttendanceAccess::inActiveCompanyContext()` is the shared
check; `AttendancePolicy::correct()` and `AttendanceService::correct()` both
use it, and the existing behavior test now asserts both refusal surfaces and
the service refusal message. **Benefit:** direct service calls and Filament
authorization agree, including for super-admin calls that skip policies.
**Trade-off / risk:** staff working in several companies can correct only
records in companies currently selected; All companies mode remains supported
through `CompanyContext::activeIds()`. **Verification:** the focused test was
attempted on `aureuserp_testing_attendance` but PHP stopped with a stack-size
fatal at `Illuminate/Database/ConcurrencyErrorDetector.php:20`, before test
counts were returned; the behavior remains unverified. **Rollback:** removing
either call would reopen a path for a global correction permission to cross
the selected tenant boundary.

**AT-6 rationale — reports read only what the viewer may see (2026-11-11).**
**Why:** `AttendanceReports` reads attendance and employee rows with
`withoutGlobalScopes()`, because a report must read the same rows the schedule
reader does whoever is acting. That is only safe if the caller says which
employees it may see: an optional list defaulting to null meant "every employee
in every company", one careless caller away from a cross-tenant report.
**How:** `lateRows()`, `overtimeRows()` and `absenceRows()` require
`array $employeeIds`; the pages pass
`AttendanceService::visibleEmployeeIds()`, which returns an empty list for a
guest, so nothing shows rather than everything. The same change stopped
dropping open rows: lateness is known at check-in, so someone who arrived late
and has not left yet must appear, and someone still clocked in has not been
absent. Overtime already needs `worked_minutes`, so an open row reports none.
Dates are rendered in the employee's calendar timezone resolved by
`ScheduleReader`, not `Attendance::checkInLocal()`, which reads the employee
through a company-scoped relation and would silently fall back to the server
zone for an actor outside that company. **Benefit:** a manager's report cannot
leak another tenant and no longer misses the person still at work.
**Trade-off / risk:** a caller that genuinely wants every employee must build
the full id list itself. **Verification:** the report test file is being run on
`aureuserp_testing_attendance`; results are in the progress log above.
**Rollback:** restoring the null default or the `check_out_at` filter
reintroduces an unbounded cross-tenant read and the missing-late-arrival bug.

The reusable listener-lifecycle rationale was also added to `AGENTS.md`, and
the shared workflow library was updated with the same app-scoped registration
lesson. The latter is outside this repository.

- **Current file scope:** `docs/completed/attendance-plan.md`,
  `plugins/webkul/attendances/**`, `plugins/webkul/support/src/Filament/Contributions/**`,
  `plugins/webkul/support/src/SupportPlugin.php`,
  `plugins/webkul/support/resources/views/dashboard-banner.blade.php`,
  `plugins/webkul/support/resources/lang/*/dashboard-banner.php`, and
  `AGENTS.md` (project-memory trap). Nothing outside the plan's allowance was
  edited.

---

## 8. Handoffs

### AT-1 — plugin skeleton, data, switch

- **Why:** attendance is a separate plugin (AT-D1) so a company that does not
  track presence is not given a screen it does not use. Installing a plugin is
  global, so availability is the per-company `attendances_company_settings` row,
  default off, exactly like Logistics.
- **How:** provider `AttendanceServiceProvider` (name `attendances`) with the
  three migrations, `employees` dependency, install/uninstall commands and an
  uninstall guard; `AttendancePlugin` for panel discovery; `AttendanceAccess`
  (per-company switch layered on `PluginEntitlement`); `CompanyProvisioner`;
  models `Attendance`, `CompanySetting`, `Correction`; `AttendancePolicy`;
  `config/filament-shield.php` with the resource's abilities and the Reporting
  cluster excluded.
- **Benefit:** no company sees attendance until it switches it on, and no
  attendance row can be created for a company that has it off — the service
  re-checks because a super-admin `Gate::before` skips policies.
- **Trade-off / risk:** the `companies` trigger only provisions the row for
  companies created while the plugin is installed; an older company gets one
  the first time its settings are opened (`CompanyProvisioner::provision()`).
- **Verification (database `aureuserp_testing_attendance`):** tables exist after
  install (1 test, 4 assertions, 449.40 s); new company provisioned disabled
  (1 test, 3 assertions, 546.96 s); re-provisioning keeps it enabled and the
  app-rebuild sequence passes (2 tests, 7 assertions, 435.66 s; 3 tests, 9
  assertions, 437.60 s); partial unique index refuses a second open row (1 test,
  1 assertion, 368.60 s).
- **Rollback:** `php artisan attendances:uninstall` refuses while rows exist
  (`attendances.allow_uninstall_with_data`); removing the provider from
  `bootstrap/providers.php` leaves the tables inert.

### AT-2 — the service

- **Why:** every write must pass one place, because the banner, a manager
  correction and the nightly job all touch the same row, and the one-open rule
  has to hold under a double click.
- **How:** `AttendanceService` (clock in idempotent on the partial unique index,
  clock out stores worked minutes, correction requires a reason and writes
  `attendances_corrections`, auto check-out at the company limit flagged
  `needs_review`); `AutoCheckOutAttendances` scheduled every 15 minutes behind
  `Package::isPluginInstalled`.
- **Benefit:** one open row per person, a double click returns the same row, and
  a forgotten check-out is closed and flagged rather than paying infinite hours.
- **Trade-off / risk:** the scheduled command runs only where the scheduler runs;
  a missing cron leaves rows open until the next run.
- **Verification:** the two boundary defects found here are fixed and pinned
  (see the two AT-2 rationale blocks above). The complete
  `AttendanceServiceTest.php` still has to run without the PHP stack-size fatal
  noted in the progress log.
- **Rollback:** the command and the scheduling clause can be removed together;
  nothing else depends on them.

### AT-3 — the welcome banner

- **Why:** the user asked for a greeting banner on every module dashboard, with
  the clock control once attendance exists. `support` is the base plugin, so it
  may not reference `attendances`; the banner is therefore shared and the clock
  is contributed.
- **How:** `Webkul\Support\Filament\Contributions\DashboardBannerRegistry`
  (keyed sections, so a repeated `packageBooted()` replaces rather than
  duplicates, plus a location resolver);
  `DashboardBannerContributions::register()` registers one `PAGE_START` render
  hook scoped to the panel's `Filament\Pages\Dashboard` subclasses, so no
  dashboard file is edited and a dashboard added later is picked up;
  `support::dashboard-banner` renders greeting, role · company · date chips and
  "You are working at …". Attendance contributes through
  `AttendanceBannerContribution` (its own plugin) and a `ClockButton` Livewire
  component registered as `attendance-clock`.
- **Benefit:** every dashboard gains the banner with no edit to any module, and
  a company without attendance sees only the greeting.
- **Trade-off / risk:** the banner is inline CSS, not Tailwind utilities, so it
  renders without rebuilding `support.css`; changing it later should keep that
  property or the rebuild step becomes mandatory. The `PAGE_START` hook is
  registered once per application (container marker), because a process-static
  flag would survive a rebuilt test application while Filament's view manager
  does not.
- **Verification:** `Banner/DashboardBannerTest.php` (greeting, one section per
  key, gating by switch + employee record, clock in/out, hidden control).
- **Rollback:** remove `DashboardBannerContributions::register()` from
  `SupportPlugin::boot()`; the Attendance contribution then renders nowhere.

### AT-4 — manager screens

- **Why:** managers need to see who is in, correct a mistake with an audit
  trail, and export the list — without a second write path.
- **How:** read-only `AttendanceResource` (split `Tables/` layout, no create
  page) whose `getEloquentQuery()` narrows to
  `AttendanceService::visibleEmployeeIds()` (team managers see their team, an
  "own rows" user sees themselves, `view_any` sees the company);
  `AttendancesTable` with a "Clocked in now" toggle, needs-review toggle,
  employee and day filters; `CorrectAttendanceAction` calling
  `AttendanceService::correct()` with a reason the service refuses when blank;
  `AttendanceExporter` whose location/IP columns are off by default.
- **Benefit:** one audited write path, and the export matches the filtered query
  Filament runs — filters, search and sort included.
- **Trade-off / risk:** the reason is not `required()` on the field on purpose:
  Laravel treats whitespace as empty and the action would never run, hiding the
  service's own message.
- **Verification:** `Reports/ReportsTest.php` covers the row scoping for an own-rows
  user and a view-any user.
- **Rollback:** the resource is read-only; removing it leaves the underlying rows
  and the service untouched.

### AT-5 — kiosk (design note only, per AT-D2)

A shared tablet at the door, one badge or PIN per person, is deliberately not
built. When it is:

1. `attendances_kiosks` (company, name, `work_location_id?`, a hashed device
   token, active flag) and a kiosk page reachable without a panel login but with
   the device token, rate-limited per token *and* per IP like the Logistics stop
   link.
2. Identification by employee `barcode` (already on `employees_employees`) or a
   PIN held the same way passwords are — hashed, never stored in clear.
3. Every punch records `source_in`/`source_out = kiosk`, and the kiosk's work
   location becomes the attendance's location evidence, so "who was at which
   site" is answerable without GPS.
4. Kiosk mode is a per-company setting (`kiosk_enabled` already exists), off by
   default, and the device token is revocable.
5. It still goes through `AttendanceService`; a kiosk punch is a self-punch with
   a different source, never a correction.

Risks to settle before building it: a badge is transferable (so kiosk punches
need the same `needs_review` treatment as auto check-outs when they look
implausible), and a shared tablet is a shared session, so the kiosk must never
hold a panel login.

### AT-6 — reports and the payroll hand-off

- **Why:** lateness, absence and overtime have to be answerable without payroll
  being built; and payroll must not read a second source.
- **How:** `AttendanceReports` computes late/overtime rows and absence days at
  read time through `ScheduleReader` (the employee's own time zone, then their
  calendar's; after the company tolerance), and only for the employee ids the
  caller passes — the pages pass `AttendanceService::visibleEmployeeIds()`, so a
  report can never widen to another company. `Filament/Clusters/Reporting`
  holds three pages (`Lateness`, `Overtime`, `Absence`), each behind its own
  Shield page permission and the company switch, with a from/until range clamped
  to 92 days. Approved leave is not an absence, someone still clocked in counts
  as late but not absent, and overtime needs a closed row.
- **Payroll hand-off contract (no payroll tables are written here):** at period
  close, payroll asks `AttendancePayroll::unitsFor(Employee, from, to)` for
  `worked_minutes` and `overtime_minutes` per employee and applies the company's
  `payroll_policies` of kind `attendance` and `overtime`. Attendance never
  writes a payroll table, and payroll never recomputes presence from raw clock
  times. Numbers are frozen by payroll at that moment, so a later calendar
  change cannot rewrite a closed period.
- **Benefit:** one definition of lateness/overtime, shared by the screens and
  the future payroll run.
- **Trade-off / risk:** absence is a per-employee, per-day loop; the 92-day cap
  keeps an accidental wide range from becoming a slow request.
- **Verification:** `Reports/AttendanceReportsTest.php` (schedule timezone,
  tolerance, scheduled-day absence, non-scheduled day, no false overtime) plus
  the generated page-permission test in `Reports/ReportsTest.php`.
- **Rollback:** the cluster and pages can be removed without touching stored
  data; nothing is persisted.

### AT-7 — translations, hardening, release

- **Why:** the fork ships five locales, and untranslated keys show raw keys to
  fr/es/pt_BR/ar users.
- **How:** English plus ar/es/fr/pt_BR for `attendance.php`, the resource
  strings, the cluster label and `reports.php`, in the same key order as English
  so the parity checker compares structures rather than treating a missing key
  as a change. `support::dashboard-banner` is translated in the same five
  locales.
- **Verification:** `php artisan translations:check --plugin=attendances` (and
  `--plugin=support`) to be run with the suite; no numeric keys are used, so the
  quoted-numeric-key rule does not apply here.
- **Rollback:** translations are additive; removing a locale file falls back to
  English.

---

## 1. Goal

Staff record when they start and stop work, from the dashboard banner on any
device. Managers see who is in, late or absent, and correct mistakes with an
audit trail. Payroll later reads worked hours and overtime from the same
records, instead of a second source.

## 2. What already exists (do not rebuild)

| Exists | Where | Use for attendance |
| --- | --- | --- |
| Employee ↔ user link | `employees_employees.user_id` | Who is clocking in. A user without an employee record cannot clock in |
| Working schedule | `Employee\Models\Calendar` + `CalendarAttendance` (working hours per weekday), `employees_employees.calendar_id` | Expected hours, lateness, overtime |
| Work locations | `Employee\Models\WorkLocation`, `employees_employees.work_location_id` | "You are working at …" in the banner; optional geofence later |
| Leave | Time Off `Leave`, `CalendarLeave` | Show "On leave" instead of "Not clocked in"; do not count as absent |
| Timesheets | `timesheets` plugin | Separate: time spent **on projects**. Attendance is presence. Not merged |
| Payroll design | `docs/planned/03-payroll.md` (`payroll_period_inputs`, `payroll_policies` kind `attendance` / `overtime`) | Consumer of worked and overtime units |
| Company context | `CompanyContext`, `BelongsToCompany`, `InheritsParentCompany` | Scoping |

## 3. Reference (from knowledge of Odoo Attendances, not re-read this session)

Check in and check out from the app; a kiosk mode with badge or PIN;
optional geolocation and IP on each check; overtime computed against the
working schedule with a tolerance; automatic check-out after a maximum
length; manager corrections; reports by employee and period. Verify against
Odoo's documentation before copying any specific rule.

## 4. Proposed design

### Data (new plugin `attendances`, depends on `employees`)

| Table | Ownership | Key columns |
| --- | --- | --- |
| `attendances_attendances` | Company (the employee's, `InheritsParentCompany` from the employee) | employee_id, check_in_at, check_out_at?, worked_minutes (stored on check-out), source_in / source_out (`banner`, `kiosk`, `manager`, `system`), latitude/longitude/accuracy? (in and out, evidence only), ip_in/ip_out?, note, is_corrected, corrected_by_id? |
| `attendances_company_settings` | One row per company (per-company switch like Logistics, not Spatie settings) | is_enabled, late_tolerance_minutes, overtime_tolerance_minutes, auto_checkout_after_hours, capture_location (off / optional / required), kiosk_enabled |
| `attendances_corrections` | Company, append-only | attendance_id, before (json), after (json), reason, actor_id, created_at |

Constraints: at most **one open** attendance per employee (partial unique
index where `check_out_at is null`); `check_out_at > check_in_at`; times
stored in UTC, shown in the company or employee timezone.

### Rules (one service, `AttendanceService`, all writes go through it)

1. **Clock in:** refused if the employee already has an open attendance, if
   attendance is disabled for the employee's company, or if the user has no
   employee record in that company. A double click returns the existing
   open row (idempotent), never a second one.
2. **Clock out:** closes the open row and stores worked minutes.
3. **Only yourself** from the banner. Clocking someone else is a manager
   correction with a reason, audited.
4. **Auto check-out:** a scheduled job closes rows open longer than the
   company limit, marked `source_out = system`, flagged for review.
5. **Lateness / overtime** are computed from the employee's working
   schedule at read time (reports) and frozen only when payroll consumes a
   period.
6. **Location** is evidence, not proof (same stance as Logistics D8), and
   never required unless the company switches it to required.
7. **On leave** (approved Time Off for today) shows in the banner and
   blocks nothing; clocking in on a leave day is allowed and flagged.

### Banner (every module dashboard)

Greeting by time of day and the user's first name; chips for role · active
company and today's date; "You are working at <work location or company>";
status pill (Not clocked in / Clocked in since 08:12 / On leave) and the
Clock in / Clock out button. The attendance part appears only when the
attendance plugin is installed and enabled for that company and the user has
an employee record there. Built as a shared Filament widget in `support`,
with attendance contributing its part through a registry, so `support`
never depends on `attendances`.

### Permissions

`view_own` (implicit for the user), `view_team` (department manager),
`view_any`, `correct`, `export`; Shield page permission for the report pages.
Super-admin `Gate::before` skips policies, so the service repeats the
company and ownership checks.

### Payroll hand-off

At period close, payroll reads worked units and overtime units per employee
from attendance (`payroll_period_inputs`), applying the company's
`payroll_policies` of kind `attendance` and `overtime`. Attendance never
writes payroll tables.

## 5. Decisions needed (user)

| ID | Question | Answer (user, 2026-11-11) |
| --- | --- | --- |
| AT-D1 | Separate plugin or part of Employees? | **Accepted:** Separate `attendances` plugin, per-company switch; many companies will not use it |
| AT-D2 | Kiosk (shared tablet with PIN or badge) in the first version? | **Accepted:** Later (AT-5). Banner on own device first |
| AT-D3 | Location capture default | **Accepted:** Optional, off by default; companies may require it |
| AT-D4 | Who corrects? | **Accepted:** Department manager for their team, HR / admin for all; every correction needs a reason |
| AT-D5 | Overtime | **Accepted:** Computed for reports in v1; paid overtime only through payroll |
| AT-D6 | Auto check-out limit | **Accepted:** 12 hours, per company setting |

## 6. Work packages

| Package | Scope | Depends on | Status |
| --- | --- | --- | --- |
| AT-0 | Decisions AT-D1..D6 | — | done |
| AT-1 | Plugin skeleton, tables, models, per-company switch, provisioner, policies, Shield config, translations (en first) | AT-0 | built; install/provisioning checks pass |
| AT-2 | `AttendanceService` (clock in/out, idempotence, one-open rule, auto check-out job) + tests | AT-1 | built; **full file green** (10 tests, 2026-11-11) after the Employee contact-recursion fix |
| AT-3 | Welcome banner widget in `support` + attendance contribution (status pill, clock button) on every module dashboard | AT-2 (banner greeting part can ship first without attendance) | built; DashboardBannerTest 5/5 passed |
| AT-4 | Manager views: who is in now, daily list, corrections with reason and audit, export | AT-2 | built; ManagerViewsTest 2/2 passed (2026-11-12) |
| AT-5 | Kiosk mode (PIN / badge) | AT-2, AT-D2 | design note only (no code, per AT-D2) |
| AT-6 | Reports: lateness, absence, overtime against schedule; payroll hand-off contract | AT-2, payroll A0 gate | built; reports 11/11 passed (AttendanceReportsTest + ReportsTest) |
| AT-7 | Translations ar/es/fr/pt_BR, hardening, release | all | built (en/ar/es/fr/pt_BR), translations check 4/4; release notes added |

**Order:** the greeting banner (AT-3 without the attendance part) can ship
on its own straight away; attendance follows AT-1 → AT-2 → AT-3 → AT-4.

## 7. Risks

- **Wrong company:** an employee in a branch company clocking in while the
  session shows the parent. The row takes the employee's company
  (`InheritsParentCompany`), not the session's.
- **Time zones:** companies in Ghana, Zambia and Congo differ (UTC+0, +2,
  +1/+2). Store UTC, display per company; lateness uses the schedule's zone.
- **Privacy:** location and IP are personal data; capture only when the
  company enables it, show to managers only, and keep them out of exports
  unless asked.
- **Forgotten check-outs** inflate hours; the auto check-out plus review
  flag keeps payroll from paying them silently.
