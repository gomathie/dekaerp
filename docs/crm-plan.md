# CRM — Plan

Status: **decisions accepted (user, 2026-11-11); not started.** CRM-1 begins after Attendance and once upstream CRM is stable (CRM-D2). Asked for by the user together
with a "Contacts & CRM" sidebar heading. That heading shipped the same day:
`NavigationGroup::Contact` → section `contacts-crm`, and a CRM module joins
it there.

Read `AGENTS.md` first: multi-company rules, protected decisions, and the
upstream review rules.

---

## 1. Goal

Sales teams track prospects before they become orders: leads come in,
become opportunities, move through a pipeline, and are won (a quotation in
Sales) or lost (with a reason). Managers see the pipeline value and
forecast. Everything stays per company.

## 2. What exists

| Exists | Where | Use for CRM |
| --- | --- | --- |
| Contacts, per company | `partners` (`Partner`, stamped with the creating company; protected decision 2026-10-29) | The customer or prospect on a lead |
| Sales teams, quotations, orders | `sales` (`Team`, `Order`, quotation → order) | Won opportunity becomes a quotation; team owns the pipeline |
| Activities, chatter, followers | `chatter` | Calls, meetings, notes on a lead |
| UTM source / medium / campaign | `support` models | Where a lead came from |
| Per-company module switch | `company_plugin` + `PluginEntitlement` | CRM on or off per company |
| Mail | app SMTP, later per-company SMTP (D16 in `docs/logistics-plan.md`) | Lead email and inbound email |

## 3. Upstream AureusERP CRM (checked 2026-11-11, `aureuserp/aureuserp`, `plugins/webkul/crm`)

A port of Odoo CRM, under active change (latest commits 2026-11-03 to
2026-11-05). Uses `BelongsToCompany`, `HasOwnershipScope`, chatter, and
depends on Sales (`Team`, `Quotation`).

| Area | Upstream has | Notes for DEKA |
| --- | --- | --- |
| Core | Leads and opportunities (one model with a type), stages (per team), tags, lost reasons, priority, expected revenue, probability | Wanted in v1 |
| Teams | CRM teams and members (on top of Sales teams) | Wanted; check it reuses `sales` teams, not a parallel copy |
| Conversion | Lead → opportunity, mass conversion, duplicate detection and merge | Wanted; merge must not cross companies |
| Quotation link | **Adds `opportunity_id` to `sales_orders`** | Changes a Sales table: the plugin owns that migration and must drop the column on uninstall |
| Scoring | Lead scoring tables and service | Later |
| Inbound email | Mail servers, aliases, inbound emails → leads | Later; must use the D16 per-company mail design, never one shared inbox across tenants |
| Lead "reveal" | IP-based website visitor identification (rules, views, countries) | Not wanted for now: needs a paid data provider and has privacy implications |
| Partner levels / assignation | Resellers and lead assignment to partners | Not wanted for now |
| Recurring plans | Recurring revenue on opportunities | Later (SaaS-style clients only) |

## 4. Reference: Odoo CRM (from knowledge, not re-read this session)

Pipeline kanban by stage; lead vs opportunity; activities with due dates;
won and lost with reasons; expected revenue and probability; forecast by
expected closing month; team targets; quotation from opportunity; lead
generation from email; lead scoring (predictive); duplicate merge.

## 5. Options

| Option | Effort | Fit | Risk |
| --- | --- | --- | --- |
| **A. Adopt upstream CRM wholesale** | Low at first | Same codebase lineage | Large, still changing, includes features we do not want (reveal, partner levels), adds a column to `sales_orders`; every fork rule (per-company contacts, entitlement, mail) must be re-checked across ~30 migrations |
| **B. Build a lean CRM ourselves** | Medium | Exactly our rules | We diverge from upstream and cannot take their fixes |
| **C. Phased adoption of upstream's core (recommended)** | Medium | Upstream models and names for the core; we take only the pieces we want, through the fork's checks | Needs a careful first port, then small upstream ports per fix |

**Recommendation: C.** Take upstream's core (leads, opportunities, stages,
tags, lost reasons, teams, conversion, quotation link) once its CRM stops
changing week to week, as a reviewed port (never a merge — AGENTS.md
upstream rule). Leave scoring, inbound email, recurring plans for later
phases, and reveal and partner levels out.

## 6. Rules the port must keep (DEKA forks)

- A lead, opportunity, stage, tag and lost reason belongs to the creating
  company; tag names unique per company (as for contact tags).
- A lead's contact must be the same company's or a shared contact
  (`Partner::recipientsForCompany()` style refusal), including on merge and
  conversion.
- Module switch: `PluginEntitlement` for navigation, policies, API and
  services; pages use `ChecksPagePermission`; widgets `GuardsDashboardWidget`.
- People pickers (salesperson, team members) use `Support\Filament\CompanyMembers`.
- Mail from the application address now, through the D16 resolver later;
  never the acting user's address.
- Reading the parent in model hooks: `withoutGlobalScopes()` (AGENTS.md).
- The `opportunity_id` column on `sales_orders`: nullable, added and dropped
  by the CRM plugin only, and Sales must work with CRM uninstalled.
- A CRM section on the combined Overview dashboard through
  `OverviewRegistry` (pipeline value, won/lost this period).

## 7. Decisions needed (user)

| ID | Question | Answer (user, 2026-11-11: recommendations accepted) |
| --- | --- | --- |
| CRM-D1 | Adopt upstream's core (C), build lean (B), or adopt all (A)? | **Accepted:** C |
| CRM-D2 | When? | **Accepted:** After upstream's CRM is stable for about two weeks and after Attendance; review upstream again then |
| CRM-D3 | Lead and opportunity as one record with a type (Odoo/upstream) or separate? | **Accepted:** One record with a type, as upstream |
| CRM-D4 | Inbound email → leads in phase 1? | **Accepted:** No; phase 3, after per-company mail (D16) |
| CRM-D5 | Pipeline per sales team or one per company? | **Accepted:** Per team, as upstream, with a company default team |

## 8. Phases

| Phase | Scope |
| --- | --- |
| CRM-1 | Port core: leads/opportunities, stages, tags, lost reasons, teams, kanban pipeline, activities, won/lost, conversion and duplicate merge within a company, quotation link; per-company switch; tests for every fork rule in §6 |
| CRM-2 | Dashboard: pipeline value, forecast by month, won/lost, a CRM section on the Overview; reports with export |
| CRM-3 | Inbound email to leads (needs D16), lead scoring |
| CRM-4 | Recurring plans, if clients ask |
| CRM-5 | Translations ar/es/fr/pt_BR, hardening, release |
