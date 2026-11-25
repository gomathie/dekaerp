# CRM — Plan

Status: **decisions accepted (user, 2026-11-11); not started.** *Added
2026-11-25:* CRM stays inside DEKA as a plugin, with the performance and
isolation requirements in section 6a (P1–P5), found in a review of
upstream's CRM. CRM-1 begins after Attendance and once upstream CRM is stable (CRM-D2). Asked for by the user together
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

## 6a. Performance and isolation requirements (user, 2026-11-25)

**Decision (user, 2026-11-25):** CRM stays inside DEKA as a plugin, not a
separate app connected by API. The user's concern is that CRM must not slow
DEKA, so the port must follow one rule: **nothing slow runs while a user
waits.** Heavy work goes to the queue; every list and board is paged and
indexed. If one part (most likely bulk email or SMS campaigns) ever outgrows
that, only that part moves to its own worker or service.

Review of upstream `plugins/webkul/crm` at `aureuserp/master` (last CRM
commit 3b06067e2, 2026-11-05) found five problems. Each is a **required fix
in the port, with a test named after it**:

| # | Upstream today | Effect in DEKA | Required fix |
| --- | --- | --- | --- |
| P1 | `Lead::saved`/`deleted`/`restored` call `LeadScoring::scheduleRefresh()`, which runs `rebuildAndRescore()` in `DB::afterCommit` — inside the user's request — whenever a lead enters or leaves a won stage, or is deleted or restored | One card dragged to Won rebuilds scoring and rescores every open lead before the page answers; grows with every lead | Dispatch a queued, de-duplicated job (`ShouldQueue`, unique per company) instead; the request only records that a refresh is due. Test: moving a lead to Won dispatches the job and does not rescore in the request |
| P2 | `rescoreOpenLeads()` reads leads `withoutGlobalScopes()` with no company filter; the frequency tables are built the same way | One tenant's action rescores every client's leads, and one company's win history can shape another company's probabilities | Rebuild and rescore per company, filtered by `company_id` explicitly (scope-free read plus company filter, as `ShipmentFromOrder` does). Test: a won lead in company A changes no score in company B |
| P3 | The pipeline board (`ListLeads::getKanbanColumns()`) loads every lead with `->get()`, no limit | Fine at a few hundred leads, slow at thousands | Load a capped number of cards per stage (e.g. 50) with "Load more", and stage totals from one grouped query. Test: a stage with more cards than the cap renders the cap and a correct total |
| P4 | `foreignId()->constrained()` on `stage_id`, `user_id`, `company_id`, `partner_id`, `team_id` (and the conversion/merge tables); PostgreSQL creates no index for a foreign key | Board, lists and filters slow down as data grows | Add indexes in the migrations, including `(company_id, stage_id)` for the board. Test: a schema test asserts the indexes exist |
| P5 | `crm:fetch-mail` runs every minute and reads every active mail server of every company in one loop | Background, so not slow for users, but one shared loop over all clients' inboxes breaks the per-company mail rule (D16) | Fetch per company through the D16 per-company mail settings, each company's failure isolated and recorded without secrets; leads land in the mailbox owner's company only. Test: a mail server of company A never creates a lead in company B |

Also keep from upstream: chart widgets have polling off (`$pollingInterval =
null`); keep it so (never Filament's 5-second default). Upstream uses
`wezlo/filament-kanban`, which DEKA already requires. Visitor tracking
("reveal", `VisitorTracked` → `CreateLeadFromVisitor`) and partner levels
stay out (section 3).

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
