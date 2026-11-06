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
| DB-1 | Shared support pieces (concerns, base widgets, JS asset, theme CSS, `compact_money()`), with the grouped `dailyBuckets` and a unit test for `statTrend()` and the period ranges. | todo |
| DB-2 | Invoice tab: the page, eight widgets, filters and translations, meeting every rule in §2. Tests: page permission generated; a revoked Invoices module hides the page; widgets count only the current company; query count bounded. | todo |
| DB-3 | Sales tab (`sales/src/Filament/Pages/SalesDashboard.php` and its widgets). | todo |
| DB-4 | Purchase tab (`purchases/src/Filament/Admin/Pages/Purchases.php` and its widgets). | todo |
| DB-5 | Recruitment tab (`recruitments/src/Filament/Pages/Recruitments.php`). | todo |
| DB-6 | Restyle the existing Project tab with the shared widgets (optional); run all affected suites; write the `CHANGELOG.md` entry. | todo |

Each package runs its plugin's full suite, plus SupportFeature (the structure
snapshots record the new pages and widgets), on its own database
(`aureuserp_testing_dash<N>`).

## 4. Handoffs

(none yet)
