# Planned work, in order of need

Plans decided or drafted but not finished (user, 2026-11-11). The number in
each file name is its place in the queue; renumber when priorities change.
When work on one begins, mark its status "in progress" here and in the plan.

| # | Plan | What | Status |
| --- | --- | --- | --- |
| 1 | [Comprehensive SaaS review](../completed/comprehensive-review-brief.md) → [plan](01-comprehensive-review-plan.md) | Onboarding, commercial administration, currencies, partners; investor demo | **Nearly done (2026-11-15).** Built and in production: CR-1..CR-7, CR-10, CR-11; CR-3 production acceptance run done 2026-11-14 (role-template failure fixed by hotfixes v1.11.1/v1.11.2). Open: CR-9 investor demo; CR-6 browser check for DRC/Zambia: exchange rates now sync daily from a free public source (in production; written "develop, 2026-11-22"); EUR, ZAR, CDF still need activating; invitation e-mails to check with the user; CR-8 partners after the demo |
| 2 | [Dashboard improvements](02-dashboard-improvement-plan.md) | Honest, consistent, tenant-scoped dashboard numbers; USD plus local currency; states, hierarchy, phone width | All decisions taken 2026-11-19 (D-DB-1 to D-DB-7). **In progress (started 2026-11-22, user)**. DB-2/3/4/6/7/9 in production (v1.16.3 to v1.17.0). 2026-11-25: the remaining DB-1 items and per-module widget checks are being built (agent, branch `feature/plan9-r4`); merging the period bar and the Filters panel into one row is mocked up, awaiting the user |
| 3 | [Rebrand Webkul → 365 Systems](03-rebrand-namespace-plan.md) | Ownership and brand | All decisions accepted 2026-11-13; licence fixed; visible brand (option A) done 2026-11-13. Code rename (B) **unblocked 2026-11-20** (the restructure is merged); not started. |
| 4 | [Inventory improvements](04-inventory-plan.md) | Gaps against Odoo and ERPNext: dashboard and reports, lot dates, stock valuation, landed costs, count sessions, material requests, batch picking | Decisions accepted 2026-11-11 |
| 5 | [Payroll](05-payroll.md) | Multi-country payroll (Ghana, Zambia; Congo pending) | Planning only; all decisions made (DP-13 to DP-18 settled 2026-11-11). Needed by the pilots but the largest plan |
| 6 | [CRM](06-crm-plan.md) | Phased port of upstream's core (leads, pipeline, teams, won/lost, quotation link) | Decisions accepted 2026-11-11. Waits for upstream CRM to be stable |
| 7 | [Landing page](07-landing-page-brief.md) | Public landing page | Not started; content decisions made 2026-11-11. Hosting and screenshots after the other tasks |
| 8 | [Point of Sale](08-pos-extraction-plan.md) | Point of Sale from upstream | Planning only; first-version scope decided 2026-11-11. Least needed (user, 2026-11-13). Counter sales are partly covered in production (since 2026-11-22): "New direct sale" (walk-in customer, paid now, order and delivery in one save) and a printed receipt |
| 9 | [Reports and payment flow usability](09-reports-usability-plan.md) | View reports before export, Zoho Books payment-flow comparison (report names kept, 2026-11-19) | **Nearly done (2026-11-25).** In production: R-1 decided (names kept), R-6 (v1.13.1), R-7 payments received including "Unused amount" (v1.16.9 and v1.17.0), R-8 printed documents (v1.16.9 to v1.16.11), R-9 list pages complete (v1.16.9 to v1.17.1; sales lists keep their Order Date column, user 2026-11-25). **Being built 2026-11-25** (agents, mockups approved): R-2 Export to PDF on the nine reports that had Excel only, R-3 written Zoho comparison, R-4 reconciliation balance cards / Match all suggested / remembered column mapping, R-5 invoice lines without a product. Ships as a hotfix (HiTrace/TRACE365) |

Moved to completed 2026-11-25: Multi-currency invoicing (was #10, all of it
in production since v1.17.0/v1.17.1). No renumbering needed: it was last.

Moved to completed 2026-11-23: Russian and Swahili. Rebrand is #3,
inventory #4, payroll #5, CRM #6, landing page #7, point of sale #8,
reports usability #9, multi-currency #10.

Reordered 2026-11-22 (user): dashboards #2, languages #3, rebrand #4.

Moved to completed 2026-11-22: Resource restructure (merged 2026-11-20,
in production since v1.14.0). Languages moved up, right after
dashboards (user, 2026-11-22); reports usability became #10.

Moved to completed 2026-11-15: Accounting (A4 shipped in v1.11.0) and
Reports (shipped in v1.10.0); the rest renumbered. Renumbered again
2026-11-19: Dashboard improvements placed at #4, after the restructure
(user); Inventory to POS moved down one.

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
