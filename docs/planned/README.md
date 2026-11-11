# Planned work, in order of need

Plans decided or drafted but not finished (user, 2026-11-11). The number in
each file name is its place in the queue; renumber when priorities change.
When work on one begins, mark its status "in progress" here and in the plan.

| # | Plan | What | Status |
| --- | --- | --- | --- |
| 1 | [Unified product resource](01-unified-product-resource-plan.md) | One complete product form on every module's product screen (and the same for contacts) | **High priority (user, 2026-11-11). Mostly built in June 2026**: shared registry, Inventory, Accounts and Manufacturing contribute, no module overrides the form. Left (about 2–3 days): verify every screen saves all fields, restore custom fields, contacts phase 3 (bank accounts, bills and purchases tabs), combination tests |
| 2 | [Resource restructure](02-restructure-backlog.md) | Move the remaining screens to the upstream file layout (Package F) | Before every later plan (user, 2026-11-11): they all edit these screen files, so they should build on the final layout. Follows #1, which already moved the product screens |
| 3 | [Inventory improvements](03-inventory-plan.md) | Gaps against Odoo and ERPNext: dashboard and reports, lot dates, stock valuation, landed costs, count sessions, material requests, batch picking | Decisions accepted 2026-11-11 |
| 4 | [CRM](04-crm-plan.md) | Phased port of upstream's core (leads, pipeline, teams, won/lost, quotation link) | Decisions accepted 2026-11-11. Starts after Attendance, once upstream CRM is stable |
| 5 | [Payroll](05-payroll.md) | Multi-country payroll (Ghana, Zambia; Congo pending) | Planning only |
| 6 | [Point of Sale](06-pos-extraction-plan.md) | Point of Sale from upstream | Planning only |
| 7 | [Landing page](07-landing-page-brief.md) | Public landing page | Not started |

Also waiting, but kept where it is because code and tests reference it:
Logistics phase 2 (WP-14 to WP-24) in [`../logistics-plan.md`](../logistics-plan.md) §6a/§6b.

In progress elsewhere: [`../attendance-plan.md`](../attendance-plan.md),
[`../dashboards-plan.md`](../dashboards-plan.md).
