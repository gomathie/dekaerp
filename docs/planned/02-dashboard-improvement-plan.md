# Dashboard improvement plan

> **Status:** Decided 2026-11-19 (all decisions D-DB-1 to D-DB-7 taken).
> **In progress since 2026-11-22** (user: "start dashboard"); the
> restructure it waited for merged 2026-11-20. Order of work: DB-7's
> journal-chart fix (D-DB-7), then DB-1, then DB-0 onward.
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
| Inventory | `InventoryHeadlineWidget`, `OperationTypeOverviewWidget` (dashboard work in #5 belongs to the Inventory developer) |
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

#### DB-0 audit, 2026-11-22 (read from the code; no data changed)

Invoices, every widget. Shared base: `HasInvoiceDashboardFilters` dates by
`invoice_date` and sums `amount_total_signed` (company currency, own rate);
invoices are posted `out_invoice` only, bills posted `in_invoice` only.

| Widget | Question it answers | Source | Gap found |
| --- | --- | --- | --- |
| `InvoiceStatsWidget` | How much did we invoice, how many, paid, unpaid? | posted out_invoice, `amount_total_signed`, by payment_state | Credit notes are not deducted, while DB-8 defines revenue as invoices less credit notes. "Unpaid" counts `not_paid` only, so partly paid invoices fall out of it. |
| `BillStatsWidget` | Same for bills | posted in_invoice, abs | Same two gaps (refunds, partial). Lower-is-better now applied (DB-1). |
| `InvoiceHeadlineWidget` | Paid, overdue, due | signed paid total; residual signed for overdue and due | OK on currency. |
| `InvoiceStatusChart` | Totals by payment state | `amount_total_signed` grouped by state | OK. |
| `RevenueOverTimeWidget` | Revenue per day | `dailyBuckets` of **paid** invoices | *Corrected 2026-11-22:* labelled "Paid customer invoices per day", so it is a cash view, not a disagreement; kept as is. |
| `CustomerRevenueChart`, `SalespersonPerformanceChart` | Who bills most | `amount_total_signed` | OK. |
| `TopInvoicesWidget`, `TopBillsWidget` | Largest documents | ordered by signed total, shows `amount_total` in the document currency | Display in the document currency is right (DB-7 rule 2); no gap. |
| `OverviewCashFlowChart` | Money in and out | payments, `amount_company_currency_signed`, by payment date | "This period" ends yesterday (noted under DB-8); polls 300 s, as required. |
| `OverviewHeadlineWidget`, `OverviewBoard`, `OverviewModuleCards` | Section summaries | each module's `panel()` / cards | Covered by each module's row. |

Sales, Purchases and Accounting, currency check only:

- Sales orders convert with `amount_total / currency_rate`, and invoices use
  `amount_total_signed`, so both are in company currency. `TopQuotationsTable`
  sorts by `amount_total` in each quotation's own currency, so a USD quotation
  can rank below a larger-looking cedi one (DB-7).
- Top products is untaxed (R-6) while the revenue tile is tax-included. Both
  are right, but they will never agree, so the labels must say which (DB-2).
- Purchases use `total_cc_amount` throughout. OK.
- Accounting: `JournalChartWidget` is fixed (DB-7). `AccountingHeadlineWidget`
  is covered by `AccountingOverviewTest`.

Known-totals harness: started with accounting `JournalChartCurrencyTest`.
The shared two-company fixture comes with DB-2.

### DB-1 Honest trends, in one place

All in `HasDashboardMetrics`, so every module inherits it:

- Previous period zero or tiny (below a threshold, D-DB-1): show "New" or
  the absolute change, no percentage, neutral colour.
- Missing or unavailable data: `—`, never `0`.
- Lower-is-better metrics (overdue amount, cost, open bills): a flag that
  inverts the delta colour.
- Each tile states its baseline ("vs 1-31 Oct").

Changes the pinned `DashboardMetricsTest` expectation: needs D-DB-1.

*Progress 2026-11-22 (develop):* built in `BuildsDashboardStats::statTrend()`
(one rule for every module). Empty previous period: "New", neutral. Fewer
than 5 previous records: the real change ("+3"), neutral; money tiles built
by `buildStat()` count the previous records for this. Lower is better
(D-DB-2) applied to the bill total, unpaid bills, purchase value and the
purchase headline spend. The pinned test was inverted in the same change.
Still open in DB-1: "—" for unavailable data, the baseline caption
("vs 1-31 Oct"), and the Sales top-customers column rename.

*Progress 2026-11-22 (develop, after v1.16.1):* baseline caption built:
every tile from `buildStat()` and the Purchases tiles read
"+12% vs 1 Oct – 31 Oct" (`HasDashboardMetrics::baselineLabel()`; test
`DashboardMetricsTest` "names the period a trend is compared with"). The
top-customers count column is now "Sales". **DB-1 is done** except "—" for
unavailable data, which needs DB-7's missing-rate rule to mean anything; it
moves there.

*Fixed 2026-11-22 (v1.16.4):* the previous period is one rule,
`Support\Filament\DashboardPeriod::previous()`. Sales and Purchases had
their own copies that rounded a year of 365.99 days up, so Sales read
"vs 31 Dec – 30 Dec". The caption names the year when the compared period
is not in the current one. Test: support `DashboardMetricsTest`.

### DB-2 Numbers agree

Per module, a test that the tile total equals the chart's series sum and the
table's rows, for the same filter. Fix any mismatch at its query, sharing
one query where two widgets compute the same figure. Invoices, then Sales,
Purchases, Accounting, Logistics.


*Progress 2026-11-22 (develop):* Invoices and Bills money totals are net of
credit notes and vendor refunds (`customerInvoicesNet()` /
`vendorBillsNet()`; counts stay invoices only), and "Unpaid" includes partly
paid (`unpaidOnly()`). Top quotations rank by company-currency value (DB-7).
Test: invoices `InvoiceDashboardTest` "deducts credit notes from the
invoiced total and counts partly paid as unpaid", sales `SalesDashboardTest`
"ranks top quotations by their value in the company currency". Still open:
the shared two-company fixture and tile = chart = table tests per module.
### DB-3 States

- Empty charts and tables get one action (U13: "Create a quotation", "Record
  a bill"), gated by the same permission as the action.
- Module off or no permission: the widget is absent, never an empty frame.
- No zero flash while loading.


*Progress 2026-11-22 (develop):* empty top lists (quotations, sales orders,
invoices, bills, RFQs) say "Nothing in this period yet" and offer the one
next step, shown only to someone whose resource `canCreate()` allows it
(`Support\Filament\Concerns\HasDashboardEmptyState`; test sales
`SalesDashboardTest` "offers the next step on an empty list only to users
who may take it"). Charts already had their empty message. Still open: no
zero flash while loading, and widget-absent checks per module.
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

*Done 2026-11-22 (v1.16.5):* Sentry DEKAERP-23, deferred here, was the
new-invoice page, not a dashboard: one journal lookup per "Preferred payment
method" option, because the picker listed every company's payment method
lines. Fixed at the cause (`PaymentMethodLine::forCompany()`, options loaded
with their journal); see `docs/change-log.md`.

### DB-7 Businesses that trade in USD and their local currency

Many clients (Ghana, Zambia, Congo) invoice, buy and hold bank balances in
USD and in their local currency at the same time. A dashboard must never add
those amounts together as if they were one money.

**What the application already stores** (checked 2026-11-18): each invoice,
bill, sales order and purchase order keeps its own currency and the rate used
at the time (`invoice_currency_rate`, `currency_rate`), plus amounts in the
company's base currency (`amount_total_signed`, `amount_residual_signed`,
`total_cc_amount`, journal `balance`). Exchange rates are kept per company
under Currencies. So the data to do this right is there; some widgets just do
not use it.

**Found 2026-11-18 (fix first, it is a wrong number today):**
Accounting `JournalChartWidget` sums `amount_residual` and `amount_total`,
which are in each document's own currency. For a company with USD and local
invoices, its "to pay", "late" and draft figures add dollars to local units.
Fix: sum the `_signed` company-currency columns, as the other money widgets
already do. A known-totals test with one USD and one local invoice pins it.
(Outside dashboards, Sales `SendSMSNotificationListener` sums a customer's
invoices the same way; recorded for its own fix.)

*Done 2026-11-22 (develop, D-DB-7):* `JournalChartWidget` now sums
`amount_total_signed` / `amount_residual_signed` for every card and its
chart; pinned by accounting `JournalChartCurrencyTest` (a USD invoice of 100
at 15.5 plus a local 50 gives 1,600, not 150). The SMS listener sum is still
open. Exchange rates now arrive daily (`currencies:sync-rates`), which rule 5
below relies on.

*Progress 2026-11-22 (develop):* rule 3 for money to collect and pay:
the Overview "To collect" / "To pay" tiles add the split by currency when
more than one is open ("2 overdue · $9.8K · GH₵61K",
`InvoiceHeadlineWidget::withCurrencySplit()`), and an open-invoice row in a
foreign currency shows its own amount and code. Test: invoices
`InvoiceDashboardTest` "splits money to collect by currency and shows a
foreign debt in its own currency". Still open: cash and bank per account
currency (rule 4), revenue split, missing-rate "—" (rule 5), "Show in USD".

*Checked 2026-11-22 (production, read-only count):* rule 4 has no widget
to apply to yet. No dashboard shows a cash or bank balance; the Overview
"Money in and out" chart sums payments in company currency
(`amount_company_currency_signed`). Rule 5: every Sales and Purchases
widget treats a missing or zero `currency_rate` as 1
(`CASE WHEN currency_rate > 0 ...`). Production has one foreign-currency
sales order without a real rate, and no such invoice or purchase order.
Rates now arrive daily, so rule 5 waits until that count grows or a client
reports it.

**The rules for every money figure:**

1. **Totals are in the company's base currency**, each document converted at
   **its own stored rate**, never at today's rate. The figure then matches
   the accounts and does not move when rates move.
2. **The currency is always shown** on every tile and axis (`ZMW 85,000`,
   not `85,000`).
3. **Where the real currency matters, show it by currency too.** For money
   someone must collect or pay (receivables, payables, overdue) and for cash
   and bank, a "by currency" line under the base total:
   `ZMW 312,400 total · USD 9,800 · ZMW 61,000`. A clerk chasing a debt
   needs to know it is 9,800 dollars, not its kwacha value.
4. **Cash and bank balances stay in each account's own currency**, with the
   base-currency total beside them, labelled.
5. **A missing rate is not a zero.** If a document has no usable rate, show
   `—` with a warning naming the currency to set up, and leave it out of
   the total rather than counting it at 1.
6. **Optional view in USD** (D-DB-6): a "Show in USD" switch converting the
   base totals at the latest rate, labelled "estimate at today's rate". Never
   the default and never used for exports or accounts.

**Tests:** a fixture company with base ZMW, one USD invoice at a known rate
and one ZMW invoice; assert the base total, the by-currency breakdown, that a
later rate change does not move the historical total, and that a missing rate
shows `—` and is excluded.

### DB-8 Sales that start as an invoice (user, 2026-11-19)

TRACE365 invoices directly from Invoices, with no quotation or sales order,
so Sales and the Sales revenue tile showed nothing while invoices were being
paid. Decided:

- **Both ways of working are first-class.** Quote first (Sales → Quotation →
  Order → Invoice) or invoice straight away (Invoices → New invoice); neither
  should look like "nothing happened".
- **Revenue counts invoices.** The dashboard's revenue figure is invoiced
  sales, whether or not an order came first (posted customer invoices less
  credit notes, company currency).
- **Sales shows invoiced work too.** A "Recent invoices" (or "Sales without an
  order") view inside Sales, so a direct invoice is visible there.
- **The guides say which door to use:** "If you agree a price first, start in
  Sales → Quotations; if you just bill, start in Invoices → New invoice."

*Progress 2026-11-20:*
- v1.13.0 shipped the invoiced Revenue tile.
- That tile's daily chart failed on PostgreSQL (Sentry DEKAERP-1X). It
  blanked the Sales dashboard and the Overview's sales section.
- Hotfix v1.13.1 (user: "build it as a hotfix") does two things:
  - fixes that chart;
  - adds invoices made without an order to top products, revenue by
    category, top customers, salespeople, the monthly chart and the Sales by
    product report.

  An invoice made from an order is still counted once, through its order.
  The rule lives in one place: `Webkul\Sale\Support\DirectInvoices`.
- *Done 2026-11-22 (v1.16.1):* invoices sit inside Sales as an "Invoices" tab, which is the "Recent invoices" view this asked for. *Superseded 2026-11-23 (user, hotfix v1.16.6):* the invoices are back under the Invoices sidebar entry, first, because that entry had come to open Bank Accounts. Still open: the guide wording.
- Still open: the top-customers count column, labelled "Orders", now also
  counts direct invoices. Rename it ("Sales") with DB-1.

Also found on TRACE365's Overview (2026-11-19), to fix in DB-2/DB-3: the
"Money in and out" chart's "This period" ends yesterday, so a payment made
today is missing from it; the Logistics tab is absent although Logistics is
granted; "Cash at bank GHS 0" after a payment, because the payment sits in
the outstanding account until reconciled, needs a plain explanation.

## Verification for every package

- Pest: known totals, a foreign-company row that must not count, and the
  permission and module gates.
- Browser: the dashboard at 1440 px and 390 px, light and dark, empty
  account; tooltips, date picker, filters. If no browser tool is available,
  say so in the handoff.
- Screenshots are reviewed but never stand in for the tests.

## Decisions taken (user, 2026-11-19)

- **D-DB-1** A trend shows "New" or the absolute change, neutral colour,
  when the previous period was 0 or had fewer than 5 records.
- **D-DB-2** Lower is better (rise shown red): overdue receivables, open
  bills and payables, costs and expenses, late shipments.
- **D-DB-3** Order for DB-4: Overview, Invoices, Sales, Purchases, then the
  rest.
- **D-DB-4** Inventory: shared-helper changes only; its layout stays with
  the Inventory developer (#5).
- **D-DB-5** By-currency breakdown under the base total for receivables and
  overdue, payables, cash and bank, **and revenue** (wider than suggested).
- **D-DB-6** Offer "Show in USD", off by default, labelled as an estimate at
  today's rate, never in exports.
- **D-DB-7** The `JournalChartWidget` mixed-currency sum is fixed **with the
  plan**, not as a hotfix. It is DB-7's first change.
- Queue position: 4, right after the resource restructure.

## Decisions as originally proposed

- **D-DB-1** Small-base rule: below what previous value does a tile show
  "New" or an absolute change instead of a percentage? Suggested: previous
  value 0, or fewer than 5 records.
- **D-DB-2** Which metrics are lower-is-better (colour inverted)? Suggested:
  overdue receivables, open bills, costs, late shipments.
- **D-DB-3** Order of dashboards for DB-4. Suggested: Overview, Invoices,
  Sales, Purchases, then the rest.
- **D-DB-4** Inventory dashboards: leave to the Inventory developer (#4), or
  include the shared-helper changes only? Suggested: shared helpers only.
- **D-DB-5** Which figures get the "by currency" breakdown? Suggested:
  receivables, payables, overdue, cash and bank. Revenue and costs stay as
  one base-currency total.
- **D-DB-6** Offer the optional "Show in USD" estimate view? Suggested: yes,
  off by default, labelled as an estimate, not in exports.
- **D-DB-7** Fix the `JournalChartWidget` currency sum now as a hotfix
  (it shows a wrong figure for mixed-currency companies), or with the plan?
  Suggested: hotfix, since a client sees a wrong number.

## Out of scope

New metrics or reports, a new chart library, changing what a metric counts,
and the Inventory dashboard work owned by plan #5.
