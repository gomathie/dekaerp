# Completed plans

Plans whose work is built, tested and in production (user, 2026-11-12: one
folder for finished plans). They stay as the record of decisions and
verification; code comments and tests still point at them. Waiting work is in
[`../planned/`](../planned/README.md); living references (change log, handover
actions, UX findings, API access, running tests, Logistics) stay in `docs/`.

| Plan | Completed | Notes |
|---|---|---|
| [Unified product resource](unified-product-resource-plan.md) | 2026-11-12 | One product form on every module screen, contacts phase 3 and combination tests done. Suites green: Product 276, Sale 155, Purchase, Accounting 55, Partner 94, Account 137; InventoryFeature run pending at move time |
| [Attendance](attendance-plan.md) | 2026-11-12 | AT-0 to AT-7 in production; AT-5 kiosk stays a design note by decision. Suites: Attendance 32 + 2, Support 227, Employee 10 |
| [Module dashboards](dashboards-plan.md) | 2026-11-11 | Invoice, Sales, Purchase and Recruitment dashboards ported, then merged into the combined Overview |
| [Company Admin role and Users-list isolation](company-admin-role-plan.md) | 2026-11-10 | Company Admin role, company-scoped Users list (§4a), scoped-parent-read fixes (§4d–§4k). Pinned by `CompanyUserIsolationTest` |
| [SaaS administration and billing](saas-administration-billing-audit-plan.md) | 2026-11-12 (merged) | SAAS-00 to SAAS-15 built and tested. **Merged into** [2.1 comprehensive review plan](../planned/02.1-comprehensive-review-plan.md): browser smoke, seat checks on imports/API, alerts and reconciliation, SAAS-14 docs continue there as CR-10 |
| [Agent review 2026-11-11](agent-review-2026-11-11.md) | 2026-11-11 | One-off review; its findings were acted on |
| [Harness² assessment](harness2-integration.md) | 2026-10-26 | Assessment finished: framework not installed, evaluation scenarios adopted |
