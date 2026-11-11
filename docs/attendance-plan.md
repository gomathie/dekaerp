# Attendance — Plan

Status: **plan only (2026-11-11). No code.** Requested by the user together
with a welcome banner on every module dashboard (the banner shows the
clock-in control once attendance exists).

Read `AGENTS.md` (project memory and protected decisions) first. Multi-company
rules apply throughout: an attendance row belongs to the **employee's**
company, never the session's.

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

| ID | Question | Recommendation |
| --- | --- | --- |
| AT-D1 | Separate plugin or part of Employees? | Separate `attendances` plugin, per-company switch; many companies will not use it |
| AT-D2 | Kiosk (shared tablet with PIN or badge) in the first version? | Later (AT-5). Banner on own device first |
| AT-D3 | Location capture default | Optional, off by default; companies may require it |
| AT-D4 | Who corrects? | Department manager for their team, HR / admin for all; every correction needs a reason |
| AT-D5 | Overtime | Computed for reports in v1; paid overtime only through payroll |
| AT-D6 | Auto check-out limit | 12 hours, per company setting |

## 6. Work packages

| Package | Scope | Depends on |
| --- | --- | --- |
| AT-0 | Decisions AT-D1..D6 | — |
| AT-1 | Plugin skeleton, tables, models, per-company switch, provisioner, policies, Shield config, translations (en first) | AT-0 |
| AT-2 | `AttendanceService` (clock in/out, idempotence, one-open rule, auto check-out job) + tests | AT-1 |
| AT-3 | Welcome banner widget in `support` + attendance contribution (status pill, clock button) on every module dashboard | AT-2 (banner greeting part can ship first without attendance) |
| AT-4 | Manager views: who is in now, daily list, corrections with reason and audit, export | AT-2 |
| AT-5 | Kiosk mode (PIN / badge) | AT-2, AT-D2 |
| AT-6 | Reports: lateness, absence, overtime against schedule; payroll hand-off contract | AT-2, payroll A0 gate |
| AT-7 | Translations ar/es/fr/pt_BR, hardening, release | all |

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
