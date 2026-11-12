# Planned work, in order of need

Plans decided or drafted but not finished (user, 2026-11-11). The number in
each file name is its place in the queue; renumber when priorities change.
When work on one begins, mark its status "in progress" here and in the plan.

| # | Plan | What | Status |
| --- | --- | --- | --- |
| 1 | [Unified product resource](01-unified-product-resource-plan.md) | One complete product form on every module's product screen (and the same for contacts) | **High priority (user, 2026-11-11). Mostly built in June 2026**: shared registry, Inventory, Accounts and Manufacturing contribute, no module overrides the form. Left (about 2–3 days): verify every screen saves all fields, restore custom fields, contacts phase 3 (bank accounts, bills and purchases tabs, decided 2026-11-11), combination tests |
| 2.0 | [Comprehensive SaaS review](<02.0- Comprehensive-review.md>) → [plan](02.0-comprehensive-review-plan.md) | Onboarding, commercial administration, currencies, partners; investor demo | Plan written 2026-11-12: most of the brief is built (SaaS plan SAAS-00..15). Packages CR-1..CR-10: proof, four-field creation (country optional), editable role templates, company currencies, provisioning status, finishing the SaaS plan, partners after the demo, and the investor demo. Decisions D-CR-1..7 made 2026-11-12 |
| 2.1 | [Resource restructure](02.1-restructure-backlog.md) | Move the remaining screens to the upstream file layout (Package F) | Before every later plan (user, 2026-11-11): they all edit these screen files, so they should build on the final layout. Follows #1, which already moved the product screens |
| 3 | [Payroll](03-payroll.md) | Multi-country payroll (Ghana, Zambia; Congo pending) | Planning only; all decisions made (DP-13 to DP-18 settled 2026-11-11). Open items are pilot facts collected at onboarding. Moved to #3 by the user, 2026-11-11 |
| 4 | [Inventory improvements](04-inventory-plan.md) | Gaps against Odoo and ERPNext: dashboard and reports, lot dates, stock valuation, landed costs, count sessions, material requests, batch picking | Decisions accepted 2026-11-11 |
| 5 | [CRM](05-crm-plan.md) | Phased port of upstream's core (leads, pipeline, teams, won/lost, quotation link) | Decisions accepted 2026-11-11. Starts after Attendance, once upstream CRM is stable |
| 6 | [Point of Sale](06-pos-extraction-plan.md) | Point of Sale from upstream | Planning only; first-version scope decided 2026-11-11 (cash and mobile money, offline, printer and drawer, barcode) |
| 7 | [Landing page](07-landing-page-brief.md) | Public landing page | Not started; content decisions made 2026-11-11. Hosting and screenshots to be discussed after all other tasks |
| 8 | [What's New page](08-whats-new-plan.md) | Bring the in-app release notes up to the library's What's New skill: safe rendering, tests, search and filters, PDF, top-bar shortcut with a "new" dot, plain-language content tied to production releases | Plan written and decided 2026-11-12 (D-WN-1..5); about 3.5 days; starts after the 2.0 plan's first block |

Also waiting, but kept where it is because code and tests reference it:
Logistics phase 2 (WP-14 to WP-24) in [`../logistics-plan.md`](../logistics-plan.md) §6a/§6b.

In progress elsewhere: [`../attendance-plan.md`](../attendance-plan.md),
[`../dashboards-plan.md`](../dashboards-plan.md).
