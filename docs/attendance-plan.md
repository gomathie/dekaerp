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
  including the application-rebuild sequence. Shield permission generation
  and the complete installation test file remain unverified.
  AT-2's forced-context fallback is fixed and its focused regression test
  passes; other service behaviors remain to be verified.
- **Not started:** AT-3 through AT-7. The tagged topbar override is owned by a
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
  unverified. The earlier non-debug full
  attempt also ended in PHP's `Maximum call stack size ... reached during
  compilation` fatal at `Illuminate/Database/ConcurrencyErrorDetector.php:20`;
  whether that was downstream of continued failures remains unverified. A
  separate direct `migrate:fresh` diagnostic failed on the existing ordering
  where the Employee migration references `calendars` before Support creates
  it; this bypassed the test helper's migration-path filtering and is not
  evidence against the attendance install path. Full Shield generation is
  intentionally not bypassed. Translation checks have not run.

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
assertions) and the three install/provisioning tests passed (3 tests, 9
assertions), on `aureuserp_testing_attendance`. The complete installation file
still needs a successful run; a later attempt ended in PHP's stack-size fatal
at Laravel's `ConcurrencyErrorDetector`. **Rollback:** revert the guard to the
previous static flag only if the application lifecycle is changed so the
Eloquent dispatcher outlives the provider; otherwise that reintroduces the
missing-listener regression.

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
`aureuserp_testing_attendance`). Other service behavior tests remain to be run.
**Rollback:** restoring the `default_company_id` fallback would violate the
existing `CompanyContext` forced-context decision and reintroduce the
cross-tenant fallback.

- **Current file scope:** `docs/attendance-plan.md`,
  `plugins/webkul/attendances/src/AttendanceServiceProvider.php`,
  `plugins/webkul/attendances/src/Services/AttendanceService.php`, and the two
  attendance feature test files. Further files will be recorded as they change.

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
| Payroll design | `docs/payroll.md` (`payroll_period_inputs`, `payroll_policies` kind `attendance` / `overtime`) | Consumer of worked and overtime units |
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
| AT-1 | Plugin skeleton, tables, models, per-company switch, provisioner, policies, Shield config, translations (en first) | AT-0 | in progress |
| AT-2 | `AttendanceService` (clock in/out, idempotence, one-open rule, auto check-out job) + tests | AT-1 | in progress |
| AT-3 | Welcome banner widget in `support` + attendance contribution (status pill, clock button) on every module dashboard | AT-2 (banner greeting part can ship first without attendance) | pending |
| AT-4 | Manager views: who is in now, daily list, corrections with reason and audit, export | AT-2 | pending |
| AT-5 | Kiosk mode (PIN / badge) | AT-2, AT-D2 | design note only |
| AT-6 | Reports: lateness, absence, overtime against schedule; payroll hand-off contract | AT-2, payroll A0 gate | pending |
| AT-7 | Translations ar/es/fr/pt_BR, hardening, release | all | pending |

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
