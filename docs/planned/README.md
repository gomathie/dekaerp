# Planned work, in order of need

Plans decided or drafted but not finished (user, 2026-11-11). The number in
each file name is its place in the queue; renumber when priorities change.
When work on one begins, mark its status "in progress" here and in the plan.

| # | Plan | What | Status |
| --- | --- | --- | --- |
| 1 | [Comprehensive SaaS review](../completed/comprehensive-review-brief.md) → [plan](01-comprehensive-review-plan.md) | Onboarding, commercial administration, currencies, partners; investor demo | **Nearly done (2026-11-15).** Built and in production: CR-1..CR-7, CR-10, CR-11; CR-3 production acceptance run done 2026-11-14 (role-template failure fixed by hotfixes v1.11.1/v1.11.2). Open: CR-9 investor demo; CR-6 browser check for DRC/Zambia waits on exchange rates for EUR, ZAR, CDF (user); invitation e-mails to check with the user; CR-8 partners after the demo |
| 2 | [Rebrand Webkul → 365 Systems](02-rebrand-namespace-plan.md) | Ownership and brand | All decisions accepted 2026-11-13; licence fixed; visible brand (option A) done 2026-11-13. Code rename (B) **unblocked 2026-11-20** (the restructure is merged); not started |
| 3 | [Resource restructure](03-restructure-backlog.md) | Move the remaining screens to the upstream file layout (Package F) | **Done (status checked 2026-11-22):** converted 2026-11-17 by script, merged into develop 2026-11-20 (5e120a142), and in production since v1.14.0. Plan: `docs/upstream-structure-alignment-plan.md`. Ready to move to `completed/` |
| 4 | [Dashboard improvements](04-dashboard-improvement-plan.md) | Honest, consistent, tenant-scoped dashboard numbers; USD plus local currency; states, hierarchy, phone width | All decisions taken 2026-11-19 (D-DB-1 to D-DB-7). **Can start now** (it waited for the restructure, merged 2026-11-20); first change fixes the mixed-currency journal chart |
| 5 | [Inventory improvements](05-inventory-plan.md) | Gaps against Odoo and ERPNext: dashboard and reports, lot dates, stock valuation, landed costs, count sessions, material requests, batch picking | Decisions accepted 2026-11-11 |
| 6 | [Payroll](06-payroll.md) | Multi-country payroll (Ghana, Zambia; Congo pending) | Planning only; all decisions made (DP-13 to DP-18 settled 2026-11-11). Needed by the pilots but the largest plan |
| 7 | [CRM](07-crm-plan.md) | Phased port of upstream's core (leads, pipeline, teams, won/lost, quotation link) | Decisions accepted 2026-11-11. Waits for upstream CRM to be stable |
| 8 | [Landing page](08-landing-page-brief.md) | Public landing page | Not started; content decisions made 2026-11-11. Hosting and screenshots after the other tasks |
| 9 | [Point of Sale](09-pos-extraction-plan.md) | Point of Sale from upstream | Planning only; first-version scope decided 2026-11-11. Least needed (user, 2026-11-13). Counter sales are partly covered on develop since 2026-11-22: "New direct sale" (walk-in customer, paid now, order and delivery in one save) and a printed receipt |
| 10 | [Russian and Swahili](10-languages-ru-sw-plan.md) | Two more interface languages | Planned 2026-11-19 (user); two decisions to confirm |
| 11 | [Reports and payment flow usability](11-reports-usability-plan.md) | View reports before export, Zoho Books payment-flow comparison (report names kept, 2026-11-19) | **Partly done (2026-11-22):** R-1 decided (names kept), R-6 shipped in v1.13.1, R-3 partly built ("Paid now", payment methods named by how they were paid). Open: R-2 view before export, R-4 reconciliation balance check, R-5 invoice lines without a product. TRACE365 items ship as hotfixes |

Moved to completed 2026-11-15: Accounting (A4 shipped in v1.11.0) and
Reports (shipped in v1.10.0); the rest renumbered. Renumbered again
2026-11-19: Dashboard improvements placed at #4, after the restructure
(user); Inventory to POS moved down one.

**Owned elsewhere (user, 2026-11-13):** Inventory (#5), Payroll (#6) and CRM
(#7) are built by another developer on a separate branch. Agents on this
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
