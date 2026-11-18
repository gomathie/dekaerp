# Planned work, in order of need

Plans decided or drafted but not finished (user, 2026-11-11). The number in
each file name is its place in the queue; renumber when priorities change.
When work on one begins, mark its status "in progress" here and in the plan.

| # | Plan | What | Status |
| --- | --- | --- | --- |
| 1 | [Comprehensive SaaS review](../completed/comprehensive-review-brief.md) → [plan](01-comprehensive-review-plan.md) | Onboarding, commercial administration, currencies, partners; investor demo | **Nearly done (2026-11-15).** Built and in production: CR-1..CR-7, CR-10, CR-11; CR-3 production acceptance run done 2026-11-14 (role-template failure fixed by hotfixes v1.11.1/v1.11.2). Open: CR-9 investor demo; CR-6 browser check for DRC/Zambia waits on exchange rates for EUR, ZAR, CDF (user); invitation e-mails to check with the user; CR-8 partners after the demo |
| 2 | [Rebrand Webkul → 365 Systems](02-rebrand-namespace-plan.md) | Ownership and brand | All decisions accepted 2026-11-13; licence fixed; visible brand (option A) done 2026-11-13; code rename (B) waits only on the restructure branch |
| 3 | [Resource restructure](03-restructure-backlog.md) | Move the remaining screens to the upstream file layout (Package F) | Large but mechanical; before Inventory and CRM, which edit these screens. Follows the unified product resource plan (completed) |
| 4 | [Inventory improvements](04-inventory-plan.md) | Gaps against Odoo and ERPNext: dashboard and reports, lot dates, stock valuation, landed costs, count sessions, material requests, batch picking | Decisions accepted 2026-11-11 |
| 5 | [Payroll](05-payroll.md) | Multi-country payroll (Ghana, Zambia; Congo pending) | Planning only; all decisions made (DP-13 to DP-18 settled 2026-11-11). Needed by the pilots but the largest plan |
| 6 | [CRM](06-crm-plan.md) | Phased port of upstream's core (leads, pipeline, teams, won/lost, quotation link) | Decisions accepted 2026-11-11. Waits for upstream CRM to be stable |
| 7 | [Landing page](07-landing-page-brief.md) | Public landing page | Not started; content decisions made 2026-11-11. Hosting and screenshots after the other tasks |
| 8 | [Point of Sale](08-pos-extraction-plan.md) | Point of Sale from upstream | Planning only; first-version scope decided 2026-11-11. Least needed (user, 2026-11-13) |
| 9 | [Dashboard improvements](09-dashboard-improvement-plan.md) | Honest, consistent, tenant-scoped dashboard numbers; USD plus local currency; states, hierarchy, phone width | Proposed 2026-11-18; decisions D-DB-1 to D-DB-7 open. One wrong mixed-currency figure found (D-DB-7). Placed last pending the user's priority |

Moved to completed 2026-11-15: Accounting (A4 shipped in v1.11.0) and
Reports (shipped in v1.10.0); the rest renumbered.

**Owned elsewhere (user, 2026-11-13):** Inventory (#4), Payroll (#5) and CRM
(#6) are built by another developer on a separate branch. Agents on this
branch do not start them; keep shared code (products, employees, partners,
accounting) compatible and note any change there in `docs/change-log.md`.

Order re-set 2026-11-13 (user: "POS to the end; arrange the rest by ease of
completion and need"). Previous order: review, restructure, payroll,
inventory, CRM, POS, accounting, reports, landing page, rebrand.

Also waiting, but kept where it is because code and tests reference it:
Logistics phase 2 (WP-14 to WP-24) in [`../logistics-plan.md`](../logistics-plan.md) §6a/§6b.

Also waiting: [Mobile view](mobile-view-plan.md) (proposal, 2026-11-10; parts
already built, see `docs/change-log.md`).

Finished plans are in [`../completed/`](../completed/README.md) (2026-11-12).
The SaaS administration plan's remaining work continues as CR-10 in
[#1](01-comprehensive-review-plan.md).
