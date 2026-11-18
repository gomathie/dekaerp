# Dashboard improvement plan

> **Status:** Proposed 2026-11-18, not started. Decisions D-DB-1 to D-DB-4
> below are open.
> **Method:** the `dashboard-design` workflow in the shared library
> (`workflows/dashboard-design.md`), adapted 2026-11-18 from the MIT skill
> `abdiraimov-otabek/dashboard-design-skill`. Nothing was installed.
> **Scope:** improve the dashboards that exist. No redesign, no new chart
> library, no change to what a business metric means without the user.

## Why

The dashboards work, but nobody has checked them as a set. Three things
matter most to clients reading them:

1. **Numbers must be right and agree.** A tile, its chart and its table
   should give the same figure for the same period and company.
2. **Numbers must not mislead.** Today `HasDashboardMetrics::statTrend()`
   reports a move from 0 to 5 as "+100%", up and green (pinned by support
   `DashboardMetricsTest`, "reports a full increase when the previous period
   was empty"). On a small base that reads as a big win.
3. **Every state must look intended:** empty, no permission, module off,
   phone width. U13 and U19 in `docs/ux-findings.md` are open examples.

## What exists (inventory, 2026-11-18)

| Area | Pages and widgets |
| --- | --- |
| Overview (Invoices plugin) | `Overview` page; `OverviewBoard`, `OverviewModuleCards`, `OverviewHeadlineWidget`, `OverviewCashFlowChart` (both poll every 300 s, protected decision) |
| Invoices | `InvoiceHeadlineWidget`, `InvoiceStatsWidget`, `BillStatsWidget`, `InvoiceStatusChart`, `RevenueOverTimeWidget`, `CustomerRevenueChart`, `SalespersonPerformanceChart`, `TopInvoicesWidget`, `TopBillsWidget` |
| Accounting | `AccountingHeadlineWidget`, `JournalChartsWidget` |
| Sales | `SalesDashboard`; headline plus four top-N tables |
| Purchases | `Purchases` dashboard; headline plus four top-N tables |
| Inventory | `InventoryHeadlineWidget`, `OperationTypeOverviewWidget` (dashboard work in #4 belongs to the Inventory developer) |
| Projects, Recruitment, Logistics | Their own dashboards, now also Overview sections; Logistics has four stats widgets |
| Time Off, Maintenance | Calendar and leave widgets |
| Shared (support) | `HasDashboardMetrics` (period ranges, `statTrend`, grouped `dailyBuckets`), `GuardsDashboardWidget` (module and permission), `DashboardCardRegistry`, `DashboardFilters`, `DashboardDateRange` |

Protected decisions that bind this plan: the Overview "All" board is two
components, not stacked widgets (Sentry DEKAERP-16); refresh at most every
5 minutes (DEKAERP-11); widget visibility comes from each module's
`canViewAny()` plus `GuardsDashboardWidget`.

## Work packages

Each package is one change with its own tests, a change-log entry and a
UX-findings entry. Stop after any package.

### DB-0 Audit and a known-totals test harness

- Fill one row per widget: user, question, query, filters, states, gaps.
  Output: an audit table appended to this plan.
- A shared Pest helper that seeds a fixed dataset for two companies (the
  second created with `CompanyHelper::asNobody()`) with known totals, so
  every later package can assert tile, chart and table against expected
  numbers. Start with Invoices (12 widgets, most read).

### DB-1 Honest trends, in one place

All in `HasDashboardMetrics`, so every module inherits it:

- Previous period zero or tiny (below a threshold, D-DB-1): show "New" or
  the absolute change, no percentage, neutral colour.
- Missing or unavailable data: `—`, never `0`.
- Lower-is-better metrics (overdue amount, cost, open bills): a flag that
  inverts the delta colour.
- Each tile states its baseline ("vs 1-31 Oct").

Changes the pinned `DashboardMetricsTest` expectation: needs D-DB-1.

### DB-2 Numbers agree

Per module, a test that the tile total equals the chart's series sum and the
table's rows, for the same filter. Fix any mismatch at its query, sharing
one query where two widgets compute the same figure. Invoices, then Sales,
Purchases, Accounting, Logistics.

### DB-3 States

- Empty charts and tables get one action (U13: "Create a quotation", "Record
  a bill"), gated by the same permission as the action.
- Module off or no permission: the widget is absent, never an empty frame.
- No zero flash while loading.

### DB-4 Hierarchy, dashboard by dashboard

Overview first, then Invoices, Sales, Purchases: filters and period visible
at the top, then tiles, the main chart, breakdowns, details. Remove tiles
that repeat a neighbour. Each dashboard is a mockup for the user before code
(as the Overview was).

### DB-5 Phone width and accessibility

U19 (Overview tabs cut at 390 px), colour never the only signal (icon or
sign beside red and green), tabular figures, keyboard focus on chart
controls, dark mode.

### DB-6 Performance sweep

Every series one grouped query (as `dailyBuckets` already does); eager
loading on top-N tables; confirm no widget polls faster than 300 s.
Measure before and after with the query log in tests.

## Verification for every package

- Pest: known totals, a foreign-company row that must not count, and the
  permission and module gates.
- Browser: the dashboard at 1440 px and 390 px, light and dark, empty
  account; tooltips, date picker, filters. If no browser tool is available,
  say so in the handoff.
- Screenshots are reviewed but never stand in for the tests.

## Decisions for the user

- **D-DB-1** Small-base rule: below what previous value does a tile show
  "New" or an absolute change instead of a percentage? Suggested: previous
  value 0, or fewer than 5 records.
- **D-DB-2** Which metrics are lower-is-better (colour inverted)? Suggested:
  overdue receivables, open bills, costs, late shipments.
- **D-DB-3** Order of dashboards for DB-4. Suggested: Overview, Invoices,
  Sales, Purchases, then the rest.
- **D-DB-4** Inventory dashboards: leave to the Inventory developer (#4), or
  include the shared-helper changes only? Suggested: shared helpers only.

## Out of scope

New metrics or reports, a new chart library, changing what a metric counts,
and the Inventory dashboard work owned by plan #4.
