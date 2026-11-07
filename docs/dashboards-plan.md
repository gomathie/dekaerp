# Module dashboards — adoption plan

Status: **in progress** (2026-11-07, user decision: port the Invoice, Sales,
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
| DB-2 | Invoice tab: the page, eight widgets, filters and translations, meeting every rule in §2. Tests: page permission generated; a revoked Invoices module hides the page; widgets count only the current company; query count bounded. | review |
| DB-3 | Sales tab (`sales/src/Filament/Pages/SalesDashboard.php` and its widgets). | review |
| DB-4 | Purchase tab (`purchases/src/Filament/Admin/Pages/Purchases.php` and its widgets). | review |
| DB-5 | Recruitment tab (`recruitments/src/Filament/Pages/Recruitments.php`). | review |
| DB-6 | Restyle the existing Project tab with the shared widgets (optional); run all affected suites; write the `CHANGELOG.md` entry. | todo |

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
has not been done.
