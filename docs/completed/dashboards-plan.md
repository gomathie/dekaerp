# Module dashboards — adoption plan

Status: **completed 2026-11-12; moved to docs/completed/.** Previously: **in progress** (2026-11-07, user decision: port the Invoice, Sales,
Purchase and Recruitment dashboard tabs). Point of Sale, Website and CRM are
out of scope: we do not have the first and the last, and our Website plugin is
portal-only.

## 1. Source

| Item | Detail |
|---|---|
| Upstream | `aureus/master` at `c31a0db21` (2026-11-06), inspected read-only. The port is frozen at that commit. |
| Main commits | `d54b999e8` (2026-10-28, "enhanced dashboards with themed widgets and animated charts") and `2669558aa` (2026-11-02, UI fixes). The pages themselves come from upstream's earlier `dashboard` branch, merged after this fork's sync point `8409fde62`. |
| Shared pieces (support) | `Filament/Concerns/BuildsDashboardStats`, `HasChartTheme` and `HasDashboardMetrics`; `Filament/Widgets/DashboardStatsWidget` and `DashboardChartWidget`; `resources/js/dashboard-charts.js` (registered as a Filament `Js` asset, with a published copy in `public/js/support/`); additions to `resources/css/filament/shared/theme.css`; the `compact_money()` helper. `DashboardDateRange` already exists here. |
| Invoice tab | `invoices/src/Filament/Pages/Invoices.php`, eight widgets under `invoices/src/Filament/Widgets` plus `Concerns/HasInvoiceDashboardFilters`, and `lang/*/filament/pages/dashboard.php` and `lang/*/filament/widgets/invoice-dashboard.php`. |

## 2. Rules every ported dashboard must meet

These are why it is a port rather than a copy.

1. **Company scoping.**
   - Every widget query goes through the scoped models, never `DB::` or
     `withoutGlobalScopes()`.
   - The filter options are scoped too: upstream's
     `Product::pluck('name', 'id')` and `User::pluck(...)` must list only
     records the current company can see. The salesperson list must list
     that company's members, not every user.
2. **Module switch (SAAS-05).**
   - The page's `canAccess()` requires the plugin's entitlement for the
     current company.
   - A `canAccess()` on a Shield page replaces `HasPageShield`'s, so it must
     check the page permission explicitly (AGENTS.md).
   - Widgets' `canView()` require the same entitlement.
3. **Permission name.**
   - Upstream hardcodes `page_invoice_invoices`. This fork generates
     `page_<plugin>_<page_snake>` through `PermissionManager`.
   - Use whatever name is actually generated, and pin it with a test against
     `FilamentShield::getPages()`, as Logistics' `ReportsTest` does.
4. **No query loops.** No per-state or per-row counting, which is the N+1
   Sentry 156271115 flagged on Projects. `HasDashboardMetrics::dailyBuckets()`
   loads every row in the period to bucket by day. Replace it with a grouped
   query (`date, count/sum ... group by date`).
5. **Assets.**
   - Register `dashboard-charts.js` through Filament assets, and publish it.
   - The CSS goes into the support theme. Rebuild `support.css` from the
     project root, then copy it to `public/css/support/` (AGENTS.md).
6. **Translations.** Upstream ships en, ar, es, fr and pt_BR. Use quoted
   numeric keys if any appear.

## 3. Packages

| Package | Content | Status |
|---|---|---|
| DB-1 | Shared support pieces (concerns, base widgets, JS asset, theme CSS, `compact_money()`), with the grouped `dailyBuckets` and a unit test for `statTrend()` and the period ranges. | done (verified 2026-11-07: SupportFeature 200 passed) |
| DB-2 | Invoice tab: the page, eight widgets, filters and translations, meeting every rule in §2. Tests: page permission generated; a revoked Invoices module hides the page; widgets count only the current company; query count bounded. | done (tests and browser check 2026-11-09) |
| DB-3 | Sales tab (`sales/src/Filament/Pages/SalesDashboard.php` and its widgets). | done (tests and browser check 2026-11-09) |
| DB-4 | Purchase tab (`purchases/src/Filament/Admin/Pages/Purchases.php` and its widgets). | done (tests and browser check 2026-11-09) |
| DB-5 | Recruitment tab (`recruitments/src/Filament/Pages/Recruitments.php`). | done (tests and browser check 2026-11-09) |
| DB-6 | Restyle the existing Project tab with the shared widgets (optional); run all affected suites; write the `CHANGELOG.md` entry. | done (tests and browser check 2026-11-09) |

Each package runs its plugin's full suite, plus SupportFeature (the structure
snapshots record the new pages and widgets), on its own database
(`aureuserp_testing_dash<N>`).

## 4. Handoffs

### DB-1 handoff — 2026-11-07 (Claude)

**How it was produced.** A code-only agent designed DB-1, but it could not
write files. Its handoff described files that never reached the disk, any
commit or this plan. Claude built DB-1 along that design, then verified it.

**Files**
- **Verbatim from upstream:** `support/src/Filament/Concerns/BuildsDashboardStats.php`,
  `HasChartTheme.php`, `Widgets/DashboardStatsWidget.php`,
  `Widgets/DashboardChartWidget.php` and `resources/js/dashboard-charts.js`.
- **`support/src/Filament/Concerns/HasDashboardMetrics.php`:** ported, with
  two changes.
  - `dailyBuckets()` is one grouped query
    (`DATE(col)` with `COUNT(*)` or `SUM(col)`, limited to the period),
    built on `toBase()` so the global scopes stay in force. Upstream loaded
    every row of the period.
  - `previousPeriodRange()` floors the day count; upstream's fractional
    count is ambiguous.
- **`support/src/helpers.php`:** `compact_money()`, adapted. This fork has
  no `default_currency_code()` or `isRtl()`, so it defaults to
  `config('app.currency')`, sends Arabic to Latin digits, and falls back to
  `money()` without ext-intl.
- **`support/src/SupportServiceProvider.php`:** registers
  `Js::make('dashboard-charts', …)` in the `support` asset package.
  Upstream's `VersionedCss` wrapper is absent here and was not ported.
- **CSS:** a new `support/resources/css/dashboard.css` holds upstream's
  dashboard rules, with logical padding and radii for RTL. It is imported
  from `index.css`.
- **Built assets:** `support/resources/dist/support.css` was rebuilt from
  the project root, about +1.7 KB with no classes dropped. It is copied to
  `public/css/support/support.css`, and `public/js/support/dashboard-charts.js`
  is published alongside it.
- **Tests:** `support/tests/Feature/Dashboard/DashboardMetricsTest.php`, with 5
  tests:
  - the stat trend, in three cases;
  - the previous-period length;
  - the daily series in one query, within the active company.

**Verification**
- Pint passed.
- `DashboardMetricsTest`: 5 passed, 8 assertions, on
  `aureuserp_testing_dash1`.
- SupportFeature, full run: 200 passed, 958 assertions, on
  `aureuserp_testing_dash1`. DB-1 is confirmed.

**Not covered.** The `SUM` path of `dailyBuckets()` runs first in DB-2,
against invoices.

### DB-2 to DB-5 handoff — 2026-11-07 (Claude)

**Shared rules added to support, one place each:**
- **`Filament/Concerns/GuardsDashboardWidget`.** A widget is visible only
  when its module is on for the current company *and* the user holds the
  widget's Shield permission. A widget Shield cannot name is refused;
  Shield's own trait allows it. Each module adds a three-line trait that
  names its plugin.
- **`Filament/Concerns/ListsCompanyMembers`.** People filters list only
  members of the viewer's companies. Upstream's `User::pluck()` listed every
  tenant's staff.
- **`Filament/Concerns/ChecksPagePermission`.** This is the page half,
  written for the 13-page fix (see `docs/change-log.md` 2026-11-07,
  CRITICAL). Every new dashboard page returns
  `<module>::enabledForCurrent() && static::hasPagePermission()`.

**Per tab:**
- **Invoice (DB-2).**
  - Files: `invoices/src/Filament/Pages/Invoices.php`; the eight widgets
    and `Concerns/HasInvoiceDashboardFilters`; `GuardsInvoiceDashboardWidget`.
  - Translations: `filament/pages/dashboard.php` and
    `filament/widgets/invoice-dashboard.php`, in en, ar, es, fr and pt_BR.
  - The revenue chart uses the grouped `dailyBuckets()` (SUM), where
    upstream loaded every paid invoice.
  - Permission: `page_invoice_invoices`.
- **Sales (DB-3).**
  - Files: `sales/src/Filament/Pages/SalesDashboard.php`; eight widgets plus
    `Concerns/HasSaleDashboardFilters`; `GuardsSaleDashboardWidget`; and
    translations in all five languages.
  - The widgets already aggregate in SQL, through Trend and grouped joins.
  - Permission: `page_sale_sales_dashboard`.
- **Purchases (DB-4).**
  - Files: `purchases/src/Filament/Admin/Pages/Purchases.php`; seven widgets
    plus `Concerns/HasPurchaseDashboardFilters`;
    `GuardsPurchaseDashboardWidget`; and translations in all five languages.
  - Upstream's `TopRfqsWidget` and `TopVendorsWidget` had **no check at
    all**; they are now guarded.
  - `PurchaseTrendWidget` uses one grouped query (day × state), where
    upstream loaded every order of the period.
  - Permission: `page_purchase_purchases`.
- **Recruitment (DB-5).**
  - The page already existed; the new `ApplicantsByStageChart` and the
    dashboard loading state were added.
  - All three widgets were taken from upstream and guarded.
    `ApplicantChartWidget` had no check before.
  - The local `getRouteName()` and entitlement code were kept.

**Test-helper fix.**
- `support/tests/Helpers/FilamentHelper::bootAdminPanel()` now calls
  `Once::flush()`.
- Shield memoizes `getPages()` and `getWidgets()` for the whole process. The
  install inside a file's first test filled that cache before the plugin's
  pages were registered, so every "permission name is generated" test failed
  in first position and passed elsewhere.
- Production is unaffected, because each request starts fresh.

**Verification (tests run):**
- Pint passed.
- Dashboard test files, each on its own database:

  | Tab | Database | Passed | Assertions |
  |---|---|---|---|
  | Invoice | `aureuserp_testing_dash2` | 6 | 14 |
  | Sales | `aureuserp_testing_dash3` | 5 | 11 |
  | Purchases | `aureuserp_testing_dash4` | 4 | 11 |
  | Recruitment | `aureuserp_testing_dash5` | 3 | 12 |

- The 13-page permission test: 13 passed, 39 assertions.
- Full suites: see below.

**Not done:** DB-6. That covers restyling the Project tab with the shared
widgets, a `CHANGELOG.md` entry, and a browser check of every tab, which
has not been done. *Superseded by the DB-6 handoff below.*

### DB-6 handoff — 2026-11-09 (Claude)

**Project tab.**
- The five widgets come from upstream: the stat cards on
  `DashboardStatsWidget`, the charts on `DashboardChartWidget`, and the hours
  shown as `H:MM`.
- Both charts keep grouped queries, because upstream's restyle still has the
  N+1:
  - **Tasks by state:** one count per state, which is Sentry 156271115.
  - **Tasks by stage:** one count per stage of `TaskStage::all()`, so the cost
    grew with every stage. It is now one grouped count plus one stage list.
- **Stage chart date bug.** Upstream passed a null start into
  `whereBetween()`. "Between NULL and …" matches no row, so every stage
  showed 0 whenever no start date was chosen. An empty start is now no lower
  bound.
- **Guard.** All five widgets use `GuardsProjectDashboardWidget` (module and
  permission). Shield's trait alone ignored the module switch.
- **Page.** `Pages/Dashboard.php` gained the loading state. Its
  `ChecksPagePermission` fix and route are unchanged.

**Release notes.** `CHANGELOG.md` v1.7.0 lists the dashboards, the 13-page
security fix, the widget guard and the faster Project chart.

**Verification.**
- Pint passed.
- `projects/tests/Feature/Widgets`: 2 passed, 8 assertions, on
  `aureuserp_testing_dash6`. That covers the new `TaskByStageChartTest`
  (one grouped query, empty stage reported as 0) and `TaskByStateChartTest`.
- ProjectFeature, full run after the restyle: 89 passed, 216 assertions, on
  `aureuserp_testing_d12_project`.

**Full suites on `develop`, 2026-11-09.** Each ran on its own database
(`aureuserp_testing_d11_*`, plus `d12` for Project). The branch stayed put
throughout; the earlier `d10` batch mixed branches and is not counted.

| Suite | Passed | Assertions | Failed |
|---|---|---|---|
| Project (after restyle) | 89 | 216 | 0 |
| Plugin Manager | 57 | 176 | 0 |
| Support | 203 | 984 | 0 |
| Sale | 153 | 470 | 0 |
| Purchase | 196 | 546 | 0 |
| Invoice | 7 | 19 | 0 |
| Recruitment | 4 | 17 | 0 |

**Browser check — 2026-11-09 (Claude).**
- **Setup:** a fresh local install on `aureuserp_browser` with only the seed
  data, served from the Sail image with opcache on. Signed in as a local
  throwaway admin. Screenshots are in `storage/logs/browser-check/`.
- **Desktop (1440 px):** Invoice, Sales, Purchase, Recruitment and Project
  each render their filters, themed stat cards, charts, doughnut centre
  totals and top-record tables. The page widths are fine.
- **Phone (390 px):** Invoice was checked. The filters and cards stack, and
  nothing runs past the screen edge (measured: page `scrollWidth` equals
  `clientWidth`, no card or chart wider than the viewport).
- **Arabic:** the layout is mirrored, and the card icon and text sit
  correctly with the logical padding from DB-1.
- **Fixed:** the Sales tab's Arabic menu label read "لوحة" ("board"). It is
  now "المبيعات", matching the other tabs.
- **Not fixed (upstream, cosmetic):**
  - The date-range box on the Invoice and Purchase filter rows is too
    narrow at desktop width, so the dates are cut off. *Fixed 2026-11-09:*
    `DashboardDateRange::make()` takes an optional `columnSpan`. Invoice and
    Purchase lay their filters out in two rows of four columns at xl: the
    date range takes two columns, then six filters. A first attempt kept
    every filter on one row (eight columns); the date fitted, but
    "All Salespersons" broke mid-word, so it was replaced. The final layout
    was seen in the browser on both tabs, with nothing truncated or broken.
    Invoice 6 passed and Purchase 4 passed after the change.
  - The line charts' per-point entrance delay is capped so a whole line
    draws in about 1 s. It was a fixed 22 ms per point, so a year of daily
    points took about 8 s on every refresh. The change is in
    `support/resources/js/dashboard-charts.js` (`spreadFor`) and its
    published copy in `public/js/support/`. The browser was confirmed to
    load the new script; the seed data's lines are flat, so the timing was
    checked by arithmetic, not by eye.
  - **Cache note:** Filament serves the script as `?v=5.7.6.0`, Filament's
    own version, so a browser that cached the old script keeps it until its
    cache expires. The old script still works, it only animates slowly.
    Upstream's `VersionedCss` exists for this reason and was not ported. The
    asset version needs bumping before a script change that matters.
  - The line charts' entrance animation briefly draws past the card edge
    while redrawing after a refresh; it settles inside the card. *Mitigated
    2026-11-09: the animation now lasts about 1 s instead of up to 8 s (see
    below). The brief overflow itself was not reproduced on purpose.* Both are
    in upstream's CSS and `dashboard-charts.js`.
- **Environment only:** Livewire refresh requests were cancelled because
  the local server is slow (status null), which caused the console errors.
  These are not code faults.

DB-2 to DB-6 are complete; the two cosmetic upstream items are optional.
