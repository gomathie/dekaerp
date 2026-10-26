# Point of Sale Plugin — Extraction Plan

**Status: planning only — nothing extracted yet.**
The user gives the go-ahead before any step is executed.

Branch: **`feature/point-of-sale`** (create from `develop` when ready).
Source: `aureus/master` remote, directory `plugins/webkul/point-of-sale/`.

---

## Summary

The upstream AureusERP repository has a complete POS plugin (953 files, 36 tests,
33 migrations, 37 services). It was never intentionally dropped from DEKA ERP —
only `blogs` and `website` were. This plan extracts it, adapts it to DEKA ERP's
conventions, and verifies it.

---

## Pre-extraction checklist

Before starting, confirm:

- [ ] `develop` branch is clean (`git status` shows nothing)
- [ ] `aureus/master` remote is up to date (`git fetch aureus`)
- [ ] Docker pgsql is running
- [ ] No other test suite is active

---

## Step 1 — Extract the plugin files

**What:** Copy the POS plugin directory from upstream into DEKA ERP.

```bash
# Create the feature branch (user does this)
git checkout -b feature/point-of-sale develop

# Extract the plugin from upstream
git show aureus/master:plugins/webkul/point-of-sale/ > /dev/null  # verify it exists
git checkout aureus/master -- plugins/webkul/point-of-sale/
git reset HEAD plugins/webkul/point-of-sale/   # unstage so user commits manually
```

**Files created:** entire `plugins/webkul/point-of-sale/` directory (953 files).

**Verify:** directory exists, `src/PointOfSaleServiceProvider.php` present.

---

## Step 2 — Extract the POS panel provider

**What:** Copy the POS Filament panel provider from upstream's `app/Providers/`.

```bash
git show aureus/master:app/Providers/Filament/PosPanelProvider.php \
  > app/Providers/Filament/PosPanelProvider.php
```

**Why a separate panel?** The POS terminal is a full-screen cashier interface
at `/pos` — it has no sidebar, no top navigation in the traditional sense, and
its own login. It's not just admin pages; it's a dedicated app for the cashier.

**Verify:** `app/Providers/Filament/PosPanelProvider.php` exists and has the
`pos` panel ID.

---

## Step 3 — Adapt the panel provider to DEKA ERP

**What:** The upstream panel provider is close but needs DEKA ERP's middleware.

### 3.1 Add `RequirePasswordChange` middleware

Upstream's `PosPanelProvider` has `SetLocale` and `ApplyBrandSettings` but not
`RequirePasswordChange`. Add it to the middleware stack so a user with an
expired password cannot use the POS terminal.

### 3.2 Verify branding

The panel uses `asset('images/favicon.ico')` and `asset('images/logo.svg')` —
confirm these exist in DEKA ERP or update to our branding paths.

### 3.3 Check color scheme

Upstream uses `Color::Blue`. Match DEKA ERP's brand color if different.

---

## Step 4 — Register the providers

**What:** Add both providers to `bootstrap/providers.php`.

```php
// In the providers array, after the existing plugins:
App\Providers\Filament\PosPanelProvider::class,
Webkul\PointOfSale\PointOfSaleServiceProvider::class,
```

**Order matters:** The panel provider must be registered so Filament knows about
the `pos` panel. The service provider registers the plugin's resources into both
the `admin` and `pos` panels.

---

## Step 5 — Regenerate the autoloader

```bash
docker run --rm -v "$(pwd -W):/var/www/html" -w /var/www/html \
  --entrypoint composer sail-8.4/app dump-autoload --no-scripts
```

**Why `--no-scripts`:** `package:discover` hangs without a database connection.

---

## Step 6 — Build frontend assets

The POS plugin has its own CSS and JS (terminal UI, cash register interactions).

```bash
cd plugins/webkul/point-of-sale
npm install
npm run build
cd ../../..
```

This produces:
- `plugins/webkul/point-of-sale/resources/dist/point-of-sale.css`
- `plugins/webkul/point-of-sale/resources/dist/point-of-sale.js`

**Note:** The `resources/dist/` files are already committed in upstream. If we
extract them, the build step is optional initially but should be verified. The
built files reference Tailwind classes that must match the terminal views.

---

## Step 7 — Run migrations

```bash
docker compose up -d pgsql
docker compose run --rm --no-deps laravel.test php artisan migrate --no-interaction
```

This creates **31 new POS tables** and alters **2 existing tables**:

### New tables (all `pos_` prefixed)
| Table | Purpose |
|---|---|
| `pos_payment_methods` | Cash, card, mobile money, etc. |
| `pos_configs` | POS terminal/register configurations |
| `pos_config_payment_methods` | Which payment methods per register |
| `pos_config_price_lists` | Price list assignments per register |
| `pos_config_fiscal_positions` | Tax position assignments per register |
| `pos_sessions` | Cashier sessions (open, close, reconcile) |
| `pos_cash_movements` | Cash in/out during a session |
| `pos_categories` | POS product categories (separate from main catalog) |
| `pos_config_categories` | Category assignments per register |
| `pos_orders` | POS orders/transactions |
| `pos_order_lines` | Line items per order |
| `pos_order_line_taxes` | Tax breakdown per line |
| `pos_order_line_attribute_values` | Product variant info per line |
| `pos_payments` | Payments per order |
| `pos_bills` | Receipt/bill templates |
| `pos_config_bills` | Bill assignments per register |
| `pos_order_line_lots` | Lot/serial tracking per line |
| `pos_floors` | Restaurant floor plans |
| `pos_tables` | Tables within a floor |
| `pos_config_floors` | Floor assignments per register |
| `pos_printers` | Receipt printers |
| `pos_printer_categories` | Product categories per printer (kitchen routing) |
| `pos_config_printers` | Printer assignments per register |
| `pos_category_products` | Product-to-POS-category mapping |
| `pos_notes` | Predefined order notes/messages |

### Altered existing tables (additive only)
| Table | Change | Risk |
|---|---|---|
| `inventories_warehouses` | Adds nullable `pos_type_id` and `pos_return_type_id` FK columns | **Low** — nullable, no existing data affected |
| `products_products` | Adds nullable `available_in_pos` boolean, default false | **Low** — nullable with default, no existing data affected |

### Settings migrations
| Setting | Purpose |
|---|---|
| `point_of_sale_account_settings` | Default journals and accounts for POS |
| `point_of_sale_inventory_settings` | Warehouse and stock handling config |

---

## Step 8 — Install the plugin

```bash
docker compose run --rm --no-deps laravel.test \
  php artisan point-of-sale:install --no-interaction
```

This runs the seeders (payment methods, default bills, notes, service products)
and sets up the plugin in the `plugins` table.

---

## Step 9 — Add the test suite to phpunit.xml

Add the POS test suite block to `phpunit.xml`:

```xml
<testsuite name="PointOfSaleFeature">
    <directory>plugins/webkul/point-of-sale/tests/Feature</directory>
</testsuite>
```

---

## Step 10 — Run the upstream tests

```bash
docker compose run --rm --no-deps \
  -e DB_DATABASE=aureuserp_testing_pos \
  laravel.test php artisan test --testsuite=PointOfSaleFeature --compact
```

**Create the test database first:**
```bash
docker compose exec -T pgsql psql -U sail -d postgres \
  -c "CREATE DATABASE aureuserp_testing_pos OWNER sail;"
```

**Expected:** 36 tests pass. The upstream tests include:
- API tests (order sync, session control, register list, order actions, history)
- Filament resource tests (config, sessions, terminal, orders, products)
- Workflow tests (orders, refunds, discounts, cash rounding, lot tracking,
  invoicing, ship-later, COGS, session closing entries, preparation tickets,
  restaurant orders, company scoping, tax parity)

---

## Step 11 — DEKA ERP adaptations

These are the changes needed to make the POS plugin match DEKA ERP conventions.
Each is a focused change, not a rewrite.

### 11.1 Per-company plugin gating

The POS plugin needs the same per-company gating as Logistics:
- `PluginAccess::enabledForCurrent('point-of-sale')` in cluster/page `canAccess()`
- `PluginAccess::enabledFor('point-of-sale', $record->company_id)` per record
- Policy checks for per-company access

**Check first:** whether upstream already does this. If it follows the same
`isPluginInstalled` pattern as other plugins, it needs the DEKA five-layer
gating added.

### 11.2 Shield permissions

Verify `config/filament-shield.php` in the plugin has the correct resource
permission prefixes. DEKA ERP's `PermissionManager` generates page permissions
as `page_point-of-sale_<page_snake>` — verify the page classes match.

### 11.3 Company provisioner

When a company has POS enabled, it may need default payment methods, a default
register configuration, etc. Assess whether a `PosCompanyProvisioner` is needed
(pattern: `LogisticsCompanyProvisioner`).

### 11.4 POS panel middleware alignment

The upstream `PosPanelProvider` already has `SetLocale` and `ApplyBrandSettings`.
Add `RequirePasswordChange` from DEKA ERP.

### 11.5 Translation check

Verify the POS plugin has translations in all five locales (en, ar, es, fr, pt_BR).
Upstream may only have English. If so, translations for the other four locales
need to be added — this is a large job (the POS has extensive UI text for the
terminal, receipts, configuration, etc.).

### 11.6 Structure snapshot fixtures

After the plugin is installed, regenerate the structure snapshot fixtures:
```bash
docker compose run --rm --no-deps \
  -e DB_DATABASE=aureuserp_testing_pos \
  laravel.test php artisan test --testsuite=SupportFeature --compact
```

The `ResourceStructureSnapshotTest` will fail on the first run — delete the
fixture files and re-record them under a full SupportFeature run.

---

## Step 12 — DEKA ERP-specific tests

Add tests that DEKA ERP needs beyond upstream's:

- [ ] **Company isolation**: POS orders, sessions, configs scoped per company
- [ ] **Per-company plugin toggle**: POS hidden when not enabled for a company
- [ ] **Panel middleware**: `RequirePasswordChange` enforced on `/pos`
- [ ] **Permission names**: generated page permission names match what pages check
- [ ] **Migration registration**: all 33 migrations registered in `hasMigrations()`

---

## Step 13 — Verify no regressions

Run the core suites that the POS plugin's existing-table alterations could affect:

```bash
# Products (the available_in_pos column)
docker compose run --rm --no-deps \
  -e DB_DATABASE=aureuserp_testing_pos \
  laravel.test php artisan test --testsuite=ProductFeature --compact

# Inventories (the warehouse FK columns)
docker compose run --rm --no-deps \
  -e DB_DATABASE=aureuserp_testing_pos \
  laravel.test php artisan test --testsuite=InventoryFeature --compact

# Support (structure snapshots)
docker compose run --rm --no-deps \
  -e DB_DATABASE=aureuserp_testing_pos \
  laravel.test php artisan test --testsuite=SupportFeature --compact
```

---

## Risk assessment

| Risk | Severity | Mitigation |
|---|---|---|
| POS models missing company scoping | **Low** — 8 key models already use `BelongsToCompany`; upstream has `CompanyScopingInvariantsTest` | Verify the remaining 13 models; add scoping where needed |
| Existing table alterations break products/inventory | **Low** — both are nullable additive columns | Run product and inventory test suites |
| POS panel missing DEKA middleware | **Medium** — `RequirePasswordChange` absent | Add in Step 11.4 |
| Missing translations (ar/es/fr/pt_BR) | **Medium** — POS has extensive terminal UI text | Audit translation files; add missing locales |
| Frontend assets outdated vs DEKA Tailwind config | **Low** — POS builds its own CSS/JS independently | Rebuild from source in Step 6 |
| Shield permission name mismatches | **Medium** — the two-places trap | Assert in Step 12 |
| `ProductSchemaRegistry` contribution order | **Low** — guarded by `isPluginInstalled` check | Verify no double-registration |
| Upstream POS code references features DEKA dropped | **Low** — POS depends only on products/inventories/invoices | Grep for `blogs` or `website` references |

---

## What the POS gives DEKA ERP

Once integrated:

1. **Full POS terminal** at `/pos` — touch-friendly cashier interface
2. **Multiple registers** per company — each with its own config, payment methods, products
3. **Session management** — open/close shifts, cash reconciliation
4. **Order processing** — products, taxes, discounts, multiple payment methods
5. **Inventory integration** — stock deducted on sale, ship-later for out-of-stock
6. **Accounting integration** — COGS posting, session closing entries, invoicing
7. **Restaurant mode** — floor plans, table management, kitchen preparation routing
8. **Receipt printing** — thermal printer support, configurable templates
9. **Refunds** — full and partial, tracked per order
10. **Offline support** — draft sync for intermittent connectivity
11. **Reports** — order analysis, sales details, session reports
12. **API** — REST endpoints for external POS hardware/integrations

---

## Models inventory

**21 models total.** 8 already use `BelongsToCompany`. The remaining 13 need
assessment — many are pivot/lookup models that may not need company scoping:

| Model | Has `BelongsToCompany` | Notes |
|---|---|---|
| Order | Yes | Core transactional model |
| OrderLine | Yes | Child of Order |
| Session | Yes | Cashier session |
| Config | Yes | Register configuration |
| CashMovement | Yes | Cash in/out |
| Payment | Yes | Order payment |
| PaymentMethod | Yes | Payment type definition |
| Printer | Yes | Receipt printer |
| Category | ? | POS product categories |
| Floor | ? | Restaurant floor plan |
| Table | ? | Restaurant table |
| Bill | ? | Receipt template |
| Note | ? | Predefined order notes |
| OrderLineLot | ? | Lot tracking (child of OrderLine) |
| Attribute | ? | Probably a proxy to products |
| Customer | ? | Probably a proxy to partners |
| Delivery | ? | Probably a proxy to inventory |
| Invoice | ? | Probably a proxy to accounts |
| Product | ? | Probably a proxy to products |
| Tax | ? | Probably a proxy to accounts |
| Warehouse | ? | Extended warehouse model |

Models marked `?` will be audited in Step 11.1. Proxy models (Customer,
Delivery, Invoice, Product, Tax, Warehouse) typically inherit scoping from
the model they extend.

---

## Estimated effort

| Step | Effort | Notes |
|---|---|---|
| Steps 1–6 (extraction, registration, build) | ~1 hour | Mechanical copy and registration |
| Steps 7–10 (migrate, install, test) | ~1 hour | Run and verify |
| Step 11 (DEKA adaptations) | ~3–5 hours | Per-company gating, middleware, permissions, translations |
| Steps 12–13 (DEKA tests, regression check) | ~2 hours | New tests + existing suite runs |
| **Total** | **~7–9 hours** | Spread across sessions as needed |

---

## References

- [`AGENTS.md`](../AGENTS.md) — binding conventions, multi-company traps.
- [`docs/logistics-plan.md`](logistics-plan.md) — reference for per-company plugin gating (five-layer shape).
- [`docs/running-tests.md`](running-tests.md) — how to run suites.
- Upstream source: `aureus/master` remote, `plugins/webkul/point-of-sale/`.
