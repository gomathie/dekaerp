# Completed plans

Plans whose work is built, tested and in production (user, 2026-11-12: one
folder for finished plans). They stay as the record of decisions and
verification; code comments and tests still point at them. Waiting work is in
[`../planned/`](../planned/README.md); living references (change log, handover
actions, UX findings, API access, running tests, Logistics) stay in `docs/`.

| Plan | Completed | Notes |
|---|---|---|
| [Resource restructure backlog](restructure-backlog.md) | 2026-11-22 | Package F: every plugin converted to the split form/infolist/table layout on 2026-11-17 by script (`docs/upstream-structure-alignment-plan.md`), merged into develop 2026-11-20 (5e120a142), in production since v1.14.0. Was planned #3 |
| [Accounting](accounting-plan.md) | 2026-11-15 | A1–A7 in production; A4 country charts (Ghana, Zambia, SYSCOHADA for 17 OHADA states) in v1.11.0, standard VAT for every OHADA state in v1.11.1; rates are starting values each company maintains. Was planned #2 |
| [Reports](reports-plan.md) | 2026-11-15 | R1 Reports page, R2 cash flow, sales and purchases by product, in production since v1.10.0. R3 only if R1 is used. Was planned #4 |
| [What's New page](whats-new-plan.md) | 2026-11-12 | WN-1 to WN-6: safe rendering, parser fixes, search and filters, PDF, top-bar dot, plain-language notes, Unreleased rule. 21 tests passed |
| [Comprehensive SaaS review brief](comprehensive-review-brief.md) | 2026-11-12 | The audit brief itself. Reviewed against the code and checked phase by phase; its remaining work is the [#1 plan](../planned/01-comprehensive-review-plan.md), which stays in planned |
| [Unified product resource](unified-product-resource-plan.md) | 2026-11-12 | One product form on every module screen, contacts phase 3 and combination tests done. Suites green: Product 276, Sale 155, Purchase, Accounting 55, Partner 94, Account 137; InventoryFeature run pending at move time |
| [Attendance](attendance-plan.md) | 2026-11-12 | AT-0 to AT-7 in production; AT-5 kiosk stays a design note by decision. Suites: Attendance 32 + 2, Support 227, Employee 10 |
| [Module dashboards](dashboards-plan.md) | 2026-11-11 | Invoice, Sales, Purchase and Recruitment dashboards ported, then merged into the combined Overview |
| [Company Admin role and Users-list isolation](company-admin-role-plan.md) | 2026-11-10 | Company Admin role, company-scoped Users list (§4a), scoped-parent-read fixes (§4d–§4k). Pinned by `CompanyUserIsolationTest` |
| [SaaS administration and billing](saas-administration-billing-audit-plan.md) | 2026-11-12 (merged) | SAAS-00 to SAAS-15 built and tested. **Merged into** [#1 comprehensive review plan](../planned/01-comprehensive-review-plan.md): browser smoke, seat checks on imports/API, alerts and reconciliation, SAAS-14 docs continue there as CR-10 |
| [Agent review 2026-11-11](agent-review-2026-11-11.md) | 2026-11-11 | One-off review; its findings were acted on |
| [Harness² assessment](harness2-integration.md) | 2026-10-26 | Assessment finished: framework not installed, evaluation scenarios adopted |
