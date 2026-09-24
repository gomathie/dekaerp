# Logistics Plugin — Implementation Plan

Status: **Decisions D1–D15 accepted (2026-09-17, all recommendations, stop link included). Next: WP-1 Foundation.**
Prepared 2026-09-17 against `main @ 3e8cf0bf2`. Work happens on the branch
**`feature/logistics`**. No Logistics code exists yet.

This file is the working plan. It is written so **several agents can work at
once**: the work is split into packages (section 6), each with its own files,
dependencies and acceptance checks. Read sections 1–3 before claiming any
package.

---

## 0. How to use this plan (every agent, every session)

1. Read `docs/agent-reminders.md` (Standing instructions) and `AGENTS.md`.
   This fork is **in production with multi-tenancy in use**.
2. Read sections 1–3 of this file. Do not re-run discovery; it is recorded here.
3. **Claim a package** in the status board (section 5) before touching code:
   set `Status` to `in progress` and `Owner` to your session name and date.
   Only claim a package whose `Depends on` packages are all `done`.
4. **Only create or edit the files your package owns** (listed per package).
   If you need a change in a file you don't own, stop and write it under
   "Requests" in your package's handoff section instead.
5. Finish with the package's **acceptance checks**, then fill in its
   **Handoff** section and set `Status` to `review`. The user reviews and
   commits. Only the user sets `done`.

### Rules that apply to all packages

- **The user commits manually.** Never run `git commit`, `git push`,
  `git stash`, `git checkout <branch>` or `git reset`.
- **Never run git commands in parallel**, even read-only ones, alongside a
  checkout or merge.
- **Tests run in the Sail container, one run at a time, repo-wide.** The test
  database is shared, and parallel runs corrupt it. Before starting a run, check
  that no other run is active:
  ```bash
  docker ps --format '{{.Names}}' | grep -q 'laravel.test-run' && echo "BUSY - wait" || echo "free"
  docker compose up -d pgsql
  docker compose run --rm --no-deps laravel.test php artisan test --testsuite=LogisticsFeature --filter='<your tests>'
  ```
  If a run is interrupted, drop and recreate `aureuserp_testing` before the
  next run (see `docs/running-tests.md`). Each test takes about a minute.
- **Host PHP is too old.** Run `php -l`, artisan and Pest in the Sail image.
- **Company scoping is a security boundary.** Every operational model uses
  `Webkul\Support\Traits\BelongsToCompany`. Never query around `CompanyScope`
  without a written reason.
- **No existing table, model, migration or public API is changed**, except
  the files listed in section 3.3.
- **No new Composer or NPM packages.**
- **Business logic goes in `src/Services`, not in Filament resources.**
  Multi-record operations run in `DB::transaction()`.
- **Text:** every label goes through `__('logistics::…')`. Each package
  writes **English** only. Other languages are package WP-12.
- **Server-side authorisation:** every action checks a policy or
  permission. Never rely on only hiding a button.
- **Styling:** if you change any CSS source in the support plugin, rebuild
  `support.css` from the project root (see `docs/agent-reminders.md`).

---

## 1. Facts from discovery (don't re-derive)

| Topic | Fact |
| --- | --- |
| Stack | Laravel **13.21.1**, Filament **5.7.3**, Filament Shield 4.2.0, PHP ≥ 8.4.1 (vendor), Postgres (Supabase) |
| Plugin registration | `bootstrap/providers.php` (there is **no** `bootstrap/plugins.php`). `wikimedia/composer-merge-plugin` merges `plugins/*/*/composer.json` |
| Plugin template | `plugins/webkul/maintenance` (newest small plugin). **It has no tests**, so use `accounts/tests` as the testing template |
| Provider | `extends Webkul\PluginManager\PackageServiceProvider`, `configureCustomPackage()` with `->hasMigrations([...explicit list...])->runsMigrations()->hasSeeder()->hasSettings()->hasDependencies()->hasInstallCommand()->hasUninstallCommand()->icon()` |
| Filament plugin | `{Name}Plugin implements Filament\Contracts\Plugin`; `register()` returns early unless `Package::isPluginInstalled('logistics')`, then `discoverResources/Pages/Clusters/Widgets` for the `admin` panel |
| Uninstall | `UninstallCommand` **drops the plugin's tables** and deletes its migration rows, then runs `endWith` |
| Company scoping | `BelongsToCompany` → `CompanyScope` + auto `company_id` from `CompanyContext`. Invariant test pattern: `support/tests/Feature/Workflows/CompanyScopingInvariantsTest.php` + `CompanyScopeHelper` |
| Ownership | `Webkul\Security\Traits\HasOwnershipScope` (optional) |
| Permissions | Plugin `config/filament-shield.php` lists permission prefixes per resource (custom prefixes allowed, e.g. `reorder`). Policies call `$user->can('<prefix>_logistics_<model>')` |
| Audit | `Webkul\Chatter\Traits\HasChatter`, `HasLogActivity`; clean-up `ChatterCleanupService::purgeForModels()` |
| Sequences | `Webkul\Support\Services\SequenceService::next('logistics.shipment', $companyId, [...defaults])`; `purge()` on uninstall |
| Custom fields | `Webkul\Field\Traits\HasCustomFields` |
| Customers, carriers | `Webkul\Partner\Models\Partner` (`partners_partners`); `account_type` is individual, company or address |
| Employees | `Webkul\Employee\Models\Employee` (user, partner, company) |
| Products | `Webkul\Product` `ProductType::SERVICE` for charge types |
| Invoices | No generic API. Pattern: `plugins/webkul/sales/src/Services/Invoicer.php`: `Move::create([... move_type OUT_INVOICE ...])` → lines → `->taxes()->sync()` → `AccountFacade::computeAccountMove()`; link through a pivot (`sales_order_invoices`) |
| Vendor bills | Same, with `MoveType::IN_INVOICE` |
| PDFs | `barryvdh/laravel-dompdf`; invoice print view `customers.invoice.preview` (`accounts/resources/views/invoice`) |
| Files | Tenant S3 disk with `companies/{id}/` prefix; `SecureStorageController` checks the company |
| Settings | Spatie settings via `hasSettings([...])->runsSettings()` (see `accounts`, `inventories`) |
| Navigation | `Webkul\Support\Enums\NavigationGroup` enum (Maintenance has a case); clusters for sub-areas |
| Tests | Pest; helpers in `plugins/webkul/support/tests/Helpers` (`TestBootstrapHelper::ensurePluginInstalled`, `FilamentHelper::actingAs`, `CompanyHelper`); suites are listed by hand in `phpunit.xml` |
| Not present | No vehicle/fleet model (Maintenance `Equipment` is a generic asset), no expenses module, no driver/licence data |
| Name clash | Inventory already has `Delivery`, `Receipt`, `Operation` (warehouse pickings). Logistics uses **Shipment / Trip / Stop / Proof of delivery** |
| Install is global | The `plugins` table has no company column, and nothing enables a plugin per company. Installing Logistics installs it for **every** company |
| Inert until installed | `PackageServiceProvider` loads a plugin's migrations only when it is core or installed, and `{Name}Plugin::register()` returns early. Deploying the code before installing is safe |
| Roles | Global: `config/permission.php` has `teams => false` |
| Per-company settings | `CompanyAwareSettingsRepository` stores a settings group per company only if the group is listed in `COMPANY_SCOPED_GROUPS` (today only `accounts_accounts` and `accounts_taxes`). A row with `company_id` null holds the defaults |
| Sequence fallback | `SequenceService::next($code, $companyId)` looks for the company's row, then a shared one, and **creates a shared counter** if neither exists. Per-company numbering needs `SequenceService::ensure($code, $companyId, …)` first |
| New-company hook | `inventories/src/Observers/CompanyObserver.php` (`created()`, `ShouldHandleEventsAfterCommit`) → `CompanyLocationProvisioner` (checks `Schema::hasTable`, uses `firstOrCreate`). Accounts' observer checks `Package::isPluginInstalled()` first |
| Seeders | Maintenance seeders use fixed `id`s. `InstallCommand` calls `Package::syncPostgresSequences()` afterwards. Logistics seeders key rows by `code` instead |

---

## 2. Domain model

All tables are new, prefixed `logistics_`. **Company** = `BelongsToCompany`,
`company_id` not null. **Shared** = listed in the invariant test's `$shared`
array, `company_id` nullable.

| Table | Ownership | Key columns |
| --- | --- | --- |
| `logistics_company_settings` | Company (one row per company) | is_enabled, enabled_at, disabled_at, enabled_by_id, require_pod_for_delivery, require_pod_photo, expense_approval_required, overdue_grace_minutes, free_waiting_minutes, stop_link_ttl_hours, capacity_check, default_service_type_id?, invoice_journal_id?, bill_journal_id?, default_expense_account_id? |
| `logistics_service_types` | Shared, soft deletes | name, code (unique per company), transport_mode, **product_reference** (e.g. LOG-FREIGHT; the product is resolved in the record's company), is_active, sort |
| `logistics_vehicle_types` | Shared | name, code, capacity_kg?, capacity_m3?, is_active, sort |
| `logistics_package_types` | Shared | name, code, is_active, sort |
| `logistics_expense_categories` | Shared | name, code, requires_receipt, **is_subcontracting**, is_active, sort. **No expense account**: accounts belong to one company, so the account comes from `default_expense_account_id` in company settings |
| `logistics_vehicles` | Company, soft deletes, custom fields | registration_no (unique per company), vehicle_type_id, ownership, carrier_id?, capacity_kg, capacity_m3, equipment_id?, telematics_device_ref?, default_driver_id?, is_active |
| `logistics_drivers` | Company, soft deletes | employee_id? / partner_id?, license_number, license_class, license_expires_at, is_active |
| `logistics_shipments` | Company, chatter, log activity, custom fields, soft deletes | name (sequence), customer_id, customer_reference, sale_order_id?, service_type_id, transport_mode, priority, state, pickup_address_id, delivery_address_id, origin_label, destination_label, planned_pickup_at, actual_pickup_at, expected_delivery_at, actual_delivery_at, declared_value, currency_id, is_fragile, is_hazardous, instructions, dispatcher_id, carrier_id?, carrier_reference, waybill_no, is_opening (bool, D13), total_packages, total_weight_kg, total_volume_m3, total_charges, total_costs, creator_id. **There's no `carrier_cost` column: subcontractor costs are expense rows (category Subcontractor, payee = carrier). See D2.** |
| `logistics_shipment_lines` | Company | shipment_id, sort, description, package_type_id, quantity, weight_kg, length_cm, width_cm, height_cm, volume_m3, declared_value, handling_instructions |
| `logistics_trips` | Company, chatter, soft deletes | name (sequence), vehicle_id, driver_id, dispatcher_id, state, planned_start_at, actual_start_at, planned_end_at, actual_end_at, odometer_start, odometer_end, notes |
| `logistics_trip_shipments` | Pivot | trip_id, shipment_id, leg_sequence; unique(trip_id, shipment_id) |
| `logistics_stops` | Company | shipment_id, trip_id?, sequence, type (pickup/delivery/via), address_id, contact_name, contact_phone, instructions, planned_arrival_at, actual_arrival_at, planned_departure_at, actual_departure_at, state, latitude?, longitude? |
| `logistics_shipment_events` | Company, append-only | shipment_id, trip_id?, stop_id?, type, occurred_at, source (user/system/telematics), user_id?, latitude?, longitude?, location_label, notes, metadata json; index(shipment_id, occurred_at) |
| `logistics_delivery_proofs` | Company | shipment_id, stop_id?, recipient_name, received_at, reference, notes, photo_path?, signature_path?, captured_via (office/stop_link), captured_by_id?, latitude?, longitude?, accuracy_m? |
| `logistics_stop_links` | Company | stop_id, token_hash (unique), expires_at, used_at?, revoked_at?, created_by_id. One-time POD capture link (D15, WP-5b). Only the hash is stored |
| `logistics_shipment_charges` | Company | shipment_id, product_id, description, quantity, uom_id, price_unit, discount, subtotal, total, currency_id, is_billable, move_line_id? |
| `logistics_shipment_charge_taxes` | Pivot | charge_id, tax_id |
| `logistics_expenses` | Company, chatter, soft deletes | shipment_id?, trip_id?, vehicle_id?, category_id, date, amount, currency_id, paid_by (company/employee, D3), employee_id? (when paid_by = employee), payee_id? (vendor/carrier), vendor_reference?, description, receipt_path?, state (draft/submitted/approved/rejected/billed), approved_by_id?, bill_move_id?. Carrier or subcontractor costs are rows here with the Subcontractor category (D2) |
| `logistics_shipment_invoices` | Pivot | shipment_id, move_id; unique |

Settings live in **`logistics_company_settings`** (model `CompanySetting`,
`CompanySetting::forCompany($id)`), **not** a Spatie settings group. Built in
WP-1: `CompanyAwareSettingsRepository` falls back to the default company's
values for any company without its own row, so an `enabled` flag there would
switch Logistics on for every company as soon as the default company enabled
it. It also only resolves the *current* company, while the guards must check
the company of any record.

Enums WP-1 adds on top of WP-1a's: `ExpensePaidBy` (company, employee),
`ProofCaptureChannel` (office, stop_link) and `CapacityCheck` (warn, off).

**Shipment states:** Draft → Confirmed → AwaitingPickup → PickedUp →
InTransit → OutForDelivery → Delivered. Exceptions: OnHold (from Confirmed,
AwaitingPickup or InTransit; release goes back to Confirmed), FailedDelivery
(from OutForDelivery; retry goes to OutForDelivery, or on to Returned),
Cancelled (from Draft, Confirmed or AwaitingPickup). Final states: Delivered,
Returned, Cancelled. Only `ShipmentWorkflow::transition()` may change the
state. It validates the move, writes an event and a chatter entry, runs in a
transaction, and throws on an invalid transition.

**Trip states:** Planned → Assigned → Dispatched → InProgress → Completed,
plus Cancelled.

**Why events are not a chatter duplicate:** the dispatch board, reports and a
future telematics feed need typed, queryable data. Significant events are
also posted to chatter.

---

## 2a. Adoption: installing on a live system, and companies that don't use Logistics

Installing Logistics adds it for **all** companies (section 1), while each company
decides for itself whether to use it, possibly years after its other data exists.
This section is binding for WP-1, WP-2, WP-3, WP-9, WP-10, WP-11 and WP-13.

### Resource file layout

New Filament resources split their form, infolist and table into their own
classes, and the resource class only delegates:

```
XResource.php                    // thin: navigation, policy, getPages, delegation
XResource/Schemas/XForm.php      // public static function configure(Schema $schema): Schema
XResource/Schemas/XInfolist.php
XResource/Tables/XsTable.php     // plural
XResource/Pages/...
```

```php
public static function form(Schema $schema): Schema
{
    return VehicleForm::configure($schema);
}
```

This is not a style preference. Filament's own generator produces exactly these
FQCNs - see `MakeResourceCommand` in the installed package, which builds
`{namespace}\Schemas\{Model}Form`, `{namespace}\Schemas\{Model}Infolist` and
`{namespace}\Tables\{Plural}Table`. Upstream aureuserp moved every resource to
the same layout in v1.6.

Two reasons it is binding here:

1. Anything generated with `make:filament-resource` already lands in this
   shape, so writing resources any other way guarantees the plugin disagrees
   with its own generated code.
2. Upstream now keeps form and table code in those files. A resource that
   holds everything inline cannot take an upstream patch as a patch - every
   future fix has to be re-implemented by hand. Matching the layout keeps the
   backport cost near zero.

`ShipmentResource` (WP-2) is monolithic and is **not** to be retrofitted as
part of another package: converting it is its own change with its own test run.
New resources from WP-3 onward use the layout above.

### Per-company switch

- The switch is `logistics_company_settings.is_enabled`, one row per company,
  default off (D12). No support-plugin settings change is needed (see section 2).
- `Webkul\Logistics\Support\LogisticsAccess` provides `enabledFor(int $companyId)`,
  `enabledForCurrent()` (true if any of the user's active companies has it
  enabled) and `ensureEnabled(int $companyId)`, which throws
  `LogisticsNotEnabledException`.
- **UI:** clusters, pages, widgets and global search show only when the user
  has permission **and** `enabledForCurrent()` is true. The Logistics Settings
  page ignores the switch, so an admin can turn Logistics on.
- **Server side (not just hidden buttons):** every service that creates
  records or changes their state calls `ensureEnabled($record->company_id)`,
  and so do the policies' `create()` methods. API endpoints, if any are added
  later, do the same.

### Enabling a company = provisioning it (safe to run twice)

`CompanyProvisioner::provision(Company)` runs from the **"Enable Logistics"**
action on the settings page:
1. **Readiness check.** It reports problems but never fixes them: the company
   has a currency, a sales journal (for invoices), and a purchase journal (for
   bills, D2 and D3). Journals and the chart of accounts belong to Accounting,
   so Logistics never creates them.
2. **Numbering:** `SequenceService::ensure()` for `logistics.shipment`,
   `logistics.trip` and `logistics.waybill` with the company's id, so every
   company numbers from 1 and never shares a counter.
3. **Default service products (D14):** Freight, Pickup, Delivery, Handling,
   Waiting time and Storage, created for that company with `firstOrCreate` on
   reference `LOG-*`, skipped if present, and editable afterwards.
4. A chatter or audit entry on the company settings. Running it again changes nothing.

### Installing on a database that already has data

- Migrations **only create** `logistics_*` tables. No existing table is touched.
- Install seeds only the **shared** configuration rows (service, vehicle and
  package types, expense categories): `company_id` null, keyed by `code`, no
  fixed ids.
- **Nothing is created per company at install.** A company that never enables
  Logistics has zero Logistics rows.
- Roles get no permissions automatically (only super_admin bypasses). An admin
  grants them. Roles are global, so the company boundary remains
  `user_allowed_companies` plus the switch.

### Existing records when a company adopts Logistics (manual, per company, permission-checked; no automatic backfill)

- **Sales orders** confirmed before the company was enabled do **not**
  become shipments. The D1 listener acts only on confirmations **after**
  enabling. For already-sold work, the shipment form has a **"From sales
  order"** picker that pre-fills from the order (Logistics side; no Sales
  files change).
- **Drivers:** a bulk action, "Add drivers from employees".
- **Vehicles:** "Import from Maintenance equipment", when Maintenance is installed.
- **Jobs already in progress:** D13.

### Companies created after install

Nothing happens unless D12 = "on by default". In that case,
`Webkul\Logistics\Observers\CompanyObserver::created()`
(`ShouldHandleEventsAfterCommit`, guarded by
`Package::isPluginInstalled('logistics')` and `Schema::hasTable`) runs the
provisioner, following the Inventories pattern. Otherwise the company is
provisioned when an admin enables it.

### Disabling a company later

The switch goes off: Logistics is hidden and new records and state changes are
refused. **Data stays**, and turning it back on restores access. Invoices and
bills already created are Accounting records and don't change. Uninstall remains
global and is blocked while any company has shipments (D4).

### Production install checklist (WP-13 rehearses it on a copy)

1. Deploy the code. It stays inert while Logistics isn't installed.
2. Back up the Supabase database.
3. Install from the Plugins page. This runs migrations on the live database.
4. Run Supabase's security advisor. The new `public.logistics_*` tables must not
   be readable or writable by `anon` or `authenticated` (see
   `docs/supabase-database.md`). Revoke if needed.
5. Grant Logistics permissions to the right roles.
6. Enable Logistics per company, and fix whatever the readiness check reports.

---

## 3. Change surface

### 3.1 New plugin: `plugins/webkul/logistics/`

```
composer.json
config/filament-shield.php
database/migrations/            (all create-table migrations, written in WP-1)
database/settings/              (LogisticsSettings)
database/factories/
database/seeders/
resources/lang/{en,ar,es,fr,pt_BR}/
resources/views/
src/LogisticsServiceProvider.php
src/LogisticsPlugin.php
src/Enums/  src/Models/  src/Policies/  src/Services/  src/Contracts/
src/Settings/  src/Filament/  src/Notifications/
tests/Feature/  tests/Helpers/
```

### 3.2 Namespace

`Webkul\Logistics\` → `src/`; factories, seeders and tests follow the
Maintenance `composer.json`. Translation namespace: `logistics::`.
Permission suffix: `_logistics_<model>`.

### 3.3 Existing files that change (only these, and only in the package named)

| File | Package | Change |
| --- | --- | --- |
| `bootstrap/providers.php` | WP-1 | register `LogisticsServiceProvider` |
| `phpunit.xml` | WP-1 | add the `LogisticsFeature` suite |
| `plugins/webkul/support/src/Enums/NavigationGroup.php` | WP-1 | add a `Logistics` case |
| `lang/{en,ar,es,fr,pt_BR}/admin.php` | WP-1 | `navigation.logistics` label (the enum reads `admin.navigation.*`) |
| `resources/svg/logistics.svg`, `public/svg/logistics.svg` | WP-1 | new icon |
| ~~`UninstallCommand.php`~~ | — | **Not changed.** It already runs `startWith` before dropping tables, from both the console and the Plugins page, so the D4 guard lives in the plugin (`UninstallGuard`, override with `LOGISTICS_ALLOW_UNINSTALL_WITH_DATA`) |
| ~~`CompanyAwareSettingsRepository.php`~~ | — | **Not changed.** Settings moved to a plugin table (section 2) |
| `CHANGELOG.md`, `docs/change-log.md` | WP-13 | release notes |
| Partners resource | WP-9b | only if D11 = yes |

---

## 4. Decisions (the user answers these before WP-1)

| ID | Question | Recommendation | Answer |
| --- | --- | --- | --- |
| D1 | Quotations: Sales quotations, or a Logistics quotation engine? | Sales quotations. Odoo freight add-ons follow the same path (CRM → quotation → shipment). A confirmed order creates a draft shipment **only if it has a line whose product is a service type's `default_product_id`** (that's how Logistics recognises its own orders). Route and cargo are captured on the shipment. Rate cards come later. | **Accepted** (user, 2026-09-17) |
| D2 | Carrier payment: vendor bill or purchase order? | **Cost lines, not a single carrier-cost field.** Each subcontractor, customs agent or other vendor cost is an expense row (payee = vendor). Once approved, rows become **one draft vendor bill per vendor** (`IN_INVOICE`), and the shipment shows revenue vs cost (margin). This is how Odoo freight add-ons do it. Purchase orders stay optional and can come later, for companies that agree carrier rates in advance. | **Accepted** (user, 2026-09-17) |
| D3 | How do expenses reach accounting? | Draft vendor bills, with a **`paid_by`** field as in Odoo Expenses. **Employee** → bill to the employee's `partner_id`, so it sits in payables until reimbursed. **Company** (cash, card or credit) → bill to the payee vendor, and finance registers payment from the account used. Nothing posts automatically. | **Accepted** (user, 2026-09-17) |
| D4 | Uninstall with data present? | Block unless `--force` (small guard in the plugin manager) | **Accepted** (user, 2026-09-17) |
| D5 | Vehicles owned by Logistics, optional Maintenance equipment link? | Yes. Store weight and volume capacity, as Odoo's dispatch system does, and have `DispatchService` **warn** when a trip's cargo exceeds it (setting `capacity_check`). | **Accepted** (user, 2026-09-17) |
| D6 | Driver logins in V1? | No. A driver is a record pointing to an **employee or a contact**; Odoo's fleet driver is also a contact, optionally linked to an employee. Drivers use the stop link (D15) instead of an account. | **Accepted** (user, 2026-09-17) |
| D7 | Seed roles? | No, permissions only, plus documented role suggestions | **Accepted** (user, 2026-09-17) |
| D8 | POD rule default | **Per delivery stop** (multi-drop): recipient name and time are required, and a photo is recommended (configurable to required). Location is recorded when captured through a stop link, but treated as supporting evidence, not proof. Office entry is allowed (e.g. a photo of the signed paper waybill) and marked `captured_via = office`. | **Accepted** (user, 2026-09-17) |
| D9 | Languages | English per package, all five by WP-12 | **Accepted** (user, 2026-09-17) |
| D10 | Central record name | "Shipment" | **Accepted** (user, 2026-09-17) |
| D11 | Shipments tab on the customer page | Later (WP-9b), and only via an extension point or with approval | **Accepted** (user, 2026-09-17) |
| D12 | Per-company switch default: off or on? | **Off.** An admin enables each company explicitly, so companies that don't do logistics never see it | **Accepted** (user, 2026-09-17) |
| D13 | Jobs already in progress when a company adopts Logistics | Standard ERP cutover practice: move only the **remaining work**. Pick a cutover date. Jobs finishing within about 2 days are completed in the old process. Longer ones are entered as **opening shipments**: a permission-gated `create_opening` action creates them directly at their current state, with only the remaining stops, `is_opening = true` and a `system` event "opened at cutover". Charges already invoiced elsewhere are **not** re-entered. | **Accepted** (user, 2026-09-17) |
| D14 | Create default service products when a company is enabled? | Yes: six `LOG-*` service products per company (Freight, Pickup, Delivery, Handling, Waiting time, Storage), created only if missing and linked as the service types' `default_product_id`. This also makes D1's order detection work. If the company has no income account set up, the readiness check reports it instead of guessing. | **Accepted** (user, 2026-09-17) |
| D15 | POD capture without driver logins | A **one-time stop link**: a signed, expiring (`stop_link_ttl_hours`, default 24), single-stop, revocable URL the dispatcher sends by SMS or WhatsApp. It opens a small mobile page for recipient name, photo, signature and optional location. Delivery apps work this way, and it follows best practice to capture proof at the stop. It is optional (WP-5b); office entry still works. | **Accepted** (user, 2026-09-17) |

Research behind D1–D3, D5, D6, D8, D13 and D15 (2026-09-17):
[Odoo 18 dispatch management](https://www.odoo.com/documentation/18.0/applications/inventory_and_mrp/inventory/shipping_receiving/setup_configuration/dispatch.html),
[Odoo fleet driver = contact](https://www.odoo.com/forum/help-1/change-driver-id-from-respartner-in-hremployee-fleet-app-odoo13-163640),
[Odoo reimburse employees](https://www.odoo.com/documentation/19.0/applications/finance/expenses/reimburse.html),
[Odoo freight forwarding (cost lines, bill per vendor, margin)](https://apps.odoo.com/apps/modules/19.0/freight_forwarding),
[ePOD practice](https://www.upperinc.com/blog/how-to-collect-electronic-proof-of-delivery/),
[POD geotag as supporting evidence](https://gse.kz/en/blog/proof-of-delivery-mobile-app-signature-photo-geotag),
[accessorial charges](https://truckstop.com/blog/accessorial-charges/),
[ERP cutover of open transactions](https://xorosoft.com/erp-data-migration/),
[PILOT geofences](https://pilot-telematics.com/products/fleet-management/geofences/).
The first `TelematicsProvider` adapter would be PILOT (geofence entry and exit →
stop arrival and departure events).

---

## 5. Status board

`Status`: todo · in progress · review · done · blocked. Update your row when
you claim a package, finish it, or get blocked.

| Package | Title | Depends on | Can run alongside | Status | Owner |
| --- | --- | --- | --- | --- | --- |
| WP-0 | Decisions D1–D15 | — | — | done | user (all recommendations accepted 2026-09-17) |
| WP-1a | Starter kit: composer.json, enums, icon (easy) | — | WP-0 | review | Codex 2026-09-17 |
| WP-1 | Foundation | WP-0, WP-1a | — (runs alone) | review (verified: 38 plugin tests, AccountFeature 521, SupportFeature 115) | Claude 2026-09-18 |
| WP-2 | Shipments and workflow | WP-1 | WP-3, WP-8a | review (52/52 pass) | Claude 2026-09-18 |
| WP-3 | Vehicles and drivers | WP-1 | WP-2, WP-8a | review | Codex 2026-09-21 |
| WP-4 | Trips and dispatch board | WP-2, WP-3 | WP-5, WP-6, WP-7 | review (76/348 pass) | Claude 2026-09-21 |
| WP-5 | Delivery and POD | WP-2 | WP-4, WP-6, WP-7 | review (88/408 pass) | Codex + Claude 2026-09-22 |
| WP-5b | Stop link for POD capture (optional, D15) | WP-5 | WP-6, WP-7, WP-8b | review (161/629 pass; security review done, independent pass recommended) | Claude 2026-09-24, db `aureuserp_testing_wp5b` |
| WP-6 | Waybill and delivery-note PDF | WP-2 | WP-4, WP-5, WP-7 | review | Codex 2026-09-21 |
| WP-7 | Charges and shipment invoicing | WP-2 | WP-4, WP-5, WP-6, WP-8b | review (95/430 + AccountFeature 521) | Claude 2026-09-22 |
| WP-8a | Expense records and approval | WP-1 | WP-2, WP-3 | review (121/500 pass, whole suite green) | Copilot 2026-09-23, finished by Claude 2026-09-23 |
| WP-8b | Expense and carrier bills | WP-8a, WP-2 | WP-7 | todo | |
| WP-9 | Sales quotation link (per D1) | WP-7 | WP-10 | review | Claude 2026-09-23, db `aureuserp_testing_wp9` |
| WP-9b | Customer page integration (per D11) | WP-7 | WP-10 | todo (extension point only, else ask) | |
| WP-10 | Dashboard widgets | WP-4, WP-5 | WP-9, WP-11 | review (125/506 pass, whole suite green) | Claude 2026-09-24, db `aureuserp_testing_wp10` |
| WP-11 | Reports | WP-4, WP-5, WP-7, WP-8b | WP-10 | todo | |
| WP-12 | Translations ar/es/fr/pt_BR | each finished package | anything | review (enums + foundation; rest waits for other packages) | Codex 2026-09-17 |
| WP-13 | Hardening and release | all | — (runs alone) | todo | |

### Test database per package - claim yours before running

Every suite runs `migrate:fresh`, so two agents on one database corrupt each
other's schema. The errors look like broken code but are not: "relation X does
not exist" for a table the migration just created, or "relation Y already
exists". Four runs were lost to this on 2026-09-18 before the cause was found.

| Database | Owner | Notes |
|---|---|---|
| `aureuserp_testing` | **nobody - do not use** | The phpunit.xml default, i.e. what you get when you forget the `-e` flag. Treat it as the collision trap. |
| `aureuserp_testing_claude` | review/verification runs across packages | Not for package work |
| `aureuserp_testing_wp5` | WP-5 | |
| `aureuserp_testing_wp6` | WP-6 | Free once WP-6 is merged |
| `aureuserp_testing_wp8a` | WP-8a | Reserved 2026-09-23. Taken over by Claude 2026-09-23 when WP-8a's owner became unavailable; dropped and recreated first, because the previous owner's run had stalled on it. |
| `aureuserp_testing_wp9` | WP-9 | Reserved 2026-09-23 |
| `aureuserp_testing_wp10` | WP-10 | Reserved 2026-09-23 |
| `aureuserp_testing_wp5b` | WP-5b | Reserved 2026-09-24 |

WP-4 used `aureuserp_testing_claude` (review/verification database) because the
same agent was also verifying WP-3 and WP-6 across packages.

Claim a name before your first run, add a row here, and state it in your
handoff:

```bash
docker compose exec -T pgsql psql -U sail -d postgres -c "CREATE DATABASE aureuserp_testing_wp7 OWNER sail;"
docker compose run --rm --no-deps -e DB_DATABASE=aureuserp_testing_wp7 \
  laravel.test php artisan test --testsuite=LogisticsFeature
```

`phpunit.xml` sets `DB_DATABASE` without `force="true"`, so the `-e` override
wins and no shared file changes. Check who is running right now with:

```bash
docker compose exec -T pgsql psql -U sail -d postgres -c "SELECT datname, count(*) FROM pg_stat_activity WHERE datname LIKE 'aureuserp%' GROUP BY datname;"
```

```
WP-0 → WP-1 ─┬─ WP-2 ─┬─ WP-4 ──┬─ WP-10
             │        ├─ WP-5 ──┤
             │        ├─ WP-6   ├─ WP-11 ─┐
             │        └─ WP-7 ─┬┴─ WP-9   │
             ├─ WP-3 ─┘ (→WP-4)│          ├─ WP-13
             └─ WP-8a ─ WP-8b ─┘          │
                          WP-12 (rolling) ┘
```

---

## 6. Work packages

Each package lists what it **owns**. Files are relative to
`plugins/webkul/logistics/` unless they start with `/`. A package may read
any file but must only write the files it owns.

### WP-1a — Starter kit (easy; no decisions needed)

**Goal:** files that don't depend on D1–D15 and aren't loaded by the app yet,
so they can't affect production: the plugin `composer.json`, the status and
type enums with their English text, and the menu icon.

**Owns:** `composer.json`, `src/Enums/*`, `resources/lang/en/enums/*`,
`/resources/svg/logistics.svg`, `/public/svg/logistics.svg`.

**Must not:** create a provider, migrations or models, edit any existing
file, or run composer, artisan or Pest.

**Enums (exact cases).** `D10` is assumed to be "Shipment"; renaming later is
cheap.

| Enum | Backed values | Colour (`HasColor`) |
| --- | --- | --- |
| `ShipmentState` | draft, confirmed, awaiting_pickup, picked_up, in_transit, out_for_delivery, delivered, on_hold, failed_delivery, returned, cancelled | gray, info, info, primary, primary, warning, success, warning, danger, danger, gray |
| `TripState` | planned, assigned, dispatched, in_progress, completed, cancelled | gray, info, primary, warning, success, danger |
| `StopType` | pickup, delivery, via | — |
| `StopState` | pending, arrived, departed, skipped | gray, info, success, warning |
| `ShipmentEventType` | created, confirmed, driver_assigned, vehicle_assigned, trip_assigned, dispatched, pickup_started, picked_up, departed_origin, in_transit, arrived_destination, out_for_delivery, delivered, delivery_failed, delivery_retried, returned, put_on_hold, released, cancelled, pod_captured, invoice_created, position_update | — |
| `ShipmentEventSource` | user, system, telematics | — |
| `TransportMode` | road, air, sea, rail, multimodal | — |
| `ShipmentPriority` | low, normal, high, urgent | gray, info, warning, danger |
| `VehicleOwnership` | owned, leased, third_party | — |
| `ExpenseState` | draft, submitted, approved, rejected, billed | gray, info, success, danger, primary |

`ShipmentState`, `TripState` and `ExpenseState` also get
`public static function transitions(): array` (from-value → list of allowed
to-cases) and `public function canTransitionTo(self $to): bool`. The maps:

- ShipmentState: draft → confirmed, cancelled · confirmed → awaiting_pickup,
  on_hold, cancelled · awaiting_pickup → picked_up, on_hold, cancelled ·
  picked_up → in_transit · in_transit → out_for_delivery, on_hold ·
  out_for_delivery → delivered, failed_delivery · failed_delivery →
  out_for_delivery, returned · on_hold → confirmed · delivered, returned,
  cancelled → (none)
- TripState: planned → assigned, cancelled · assigned → dispatched,
  cancelled · dispatched → in_progress, cancelled · in_progress → completed ·
  completed, cancelled → (none)
- ExpenseState: draft → submitted · submitted → approved, rejected ·
  rejected → draft · approved → billed · billed → (none)

**Acceptance:** `php -l` is clean on every file. The verification script in the
WP-1a prompt passes. Every case has an English label. The icon renders at
64×64 and matches the other icons' frame.

---

### WP-1 — Foundation (runs alone; every other package depends on it)

> WP-1a has already created `composer.json`, `src/Enums/*`,
> `resources/lang/en/enums/*` and the icon. WP-1 reviews them and may extend
> them, but does not recreate them.

**Goal:** an installable, empty-but-complete plugin. The whole schema,
models, enums, policies and permissions exist, so later packages only add new
files in their own folders.

**Owns:** `composer.json`, `config/filament-shield.php`,
`database/migrations/*`, `database/settings/*`, `database/factories/*`,
`database/seeders/*`, `src/LogisticsServiceProvider.php`,
`src/LogisticsPlugin.php`, `src/Enums/*`, `src/Models/*`, `src/Policies/*`,
`src/Settings/*`, `src/Contracts/TelematicsProvider.php`,
`src/Support/LogisticsAccess.php`, `src/Exceptions/*`,
`src/Services/CompanyProvisioner.php`, `src/Observers/CompanyObserver.php`,
`tests/Feature/Adoption/*`,
`src/Filament/Clusters/Configurations/**`,
`src/Filament/Clusters/{Operations,Fleet,Finance,Reporting}.php` (the cluster
classes only), `resources/lang/en/{enums,models}/*`,
`resources/lang/en/filament/clusters/configurations/**`,
`tests/Helpers/LogisticsHelper.php`, `tests/Feature/Foundation/*`,
plus the existing files in 3.3 marked WP-1.

**Steps**
1. Confirm the decisions in section 4 are answered. If any is still pending, set WP-1 to blocked and stop.
2. Create `composer.json` modelled on Maintenance. Run `composer dump-autoload` in the Sail container and confirm `Webkul\Logistics\` resolves.
3. Write **every** migration from section 2, one per table, dated `2026_10_01_0000NN_`. Use foreign keys with `nullOnDelete()` for optional links and `cascadeOnDelete()` for child rows. Add indexes on `company_id`, `state`, date columns and reference numbers. Every `down()` drops only its own table.
4. Write all enums (with `HasLabel` and `HasColor` where the UI shows them) and the `ShipmentState::allowedTransitions()` map from section 2.
5. Write all models with their relations, casts, `BelongsToCompany` (Company tables), chatter and log-activity traits, and `HasCustomFields` on Shipment and Vehicle. Sequence names are assigned in `creating` hooks via `SequenceService::next()`. Recalculating totals belongs to WP-2 and WP-7, so leave those as stub methods.
6. Write the policies for every model, and the full `config/filament-shield.php` with **all** permissions from the plan, including custom prefixes: shipment `confirm, assign, mark_picked_up, mark_delivered, capture_pod, send_pod_link, cancel, create_opening, create_invoice, view_financials`; trip `dispatch, complete`; expense `approve, post_bill`. Later packages must not need to edit this file.
   - Put custom prefixes **inside `resources.manage`**, as Maintenance does with `reorder`. `PackageServiceProvider::mergeShieldConfig()` merges only `resources.manage` and the `exclude` lists; **`pages.manage` and `custom_permissions` from a plugin config are ignored**.
   - Page permissions are generated automatically as `page_logistics_<page>` (compare `page_maintenance_calendar`), so pages need no entry.
7. Write the provider: explicit migration list, settings, seeders (types, categories, sequences), dependencies (partners, employees, products and accounts; first check which are `isCore`), icon `logistics`, and an uninstall hook that purges chatter and sequences. Apply D4.
8. Write the plugin class (admin panel discovery) and register the provider in `/bootstrap/providers.php`.
9. Add the `Logistics` case to `NavigationGroup` with its label, and the SVG icon (copy the style of `/resources/svg/maintenance.svg`).
10. Build the Configurations cluster: service types, vehicle types, package types, expense categories, and the settings page.
11. Add the `LogisticsFeature` suite to `/phpunit.xml` and write `LogisticsHelper` (factories for a shipment with a customer, address and lines).
12. Tests in `tests/Feature/Foundation/`: install and uninstall (tables created and dropped, D4 guard), company-scoping invariants (copy the support test and list the Shared tables), policy allow and deny for one resource, and sequence numbering per company.
13. Build the adoption layer from section 2a: add the `logistics_company_settings` table and model; add `LogisticsAccess`, `LogisticsNotEnabledException` and `CompanyProvisioner` (readiness check, per-company sequences, D14 products); add the "Enable Logistics" and "Disable Logistics" actions on the settings page; gate every cluster, page and widget with `canAccess()`; guard every policy's `create()`; add `CompanyObserver` only if D12 = on. Seeders key rows by `code`, never by fixed `id`.
14. Tests in `tests/Feature/Adoption/`:
    - installing on a database that already has companies, partners, orders and invoices leaves those tables' row counts unchanged;
    - install creates no per-company rows;
    - enable company A only → company B users see no Logistics navigation, the policy's `create()` refuses, and `LogisticsAccess::ensureEnabled(B)` throws;
    - provisioning twice is idempotent;
    - A and B each number shipments from 1;
    - a company created after install has nothing until enabled (or is provisioned after commit if D12 = on);
    - disabling keeps data and refuses new records.

**Acceptance:** `php -l` passes on every file. `LogisticsFeature` passes.
Full `SupportFeature` and `AccountFeature` still pass. `translations:check
--plugin=logistics --locale=en` is clean. `php artisan logistics:install`
works on a fresh test database.

**Handoff:** list the model class names, enum cases, permission names and
any deviation from section 2.

---

### WP-2 — Shipments and workflow

**Depends on:** WP-1. **Can run alongside:** WP-3, WP-8a.

**Owns:** `src/Services/ShipmentWorkflow.php`,
`src/Services/ShipmentTotals.php`,
`src/Filament/Clusters/Operations/Resources/ShipmentResource/**` (except the
relation managers listed under WP-5, WP-7 and WP-8b),
`resources/lang/en/filament/clusters/operations/resources/shipment*`,
`tests/Feature/Shipments/*`.

**Steps**
1. `ShipmentWorkflow::transition(Shipment, ShipmentState, array $context)`: authorise, call `LogisticsAccess::ensureEnabled($shipment->company_id)`, validate against the map, write the event and chatter entry, all in a transaction. Add named helpers: `confirm()`, `hold()`, `release()`, `cancel()`. The create form also offers a **"From sales order"** picker (only when Sales is installed) that pre-fills customer, currency and lines from an existing confirmed order (section 2a).
2. `ShipmentTotals`: recalculate packages, weight and volume from the lines.
3. Shipment resource form tabs: General, Route & stops (repeater over stops, address = partner of type address), Cargo (repeater over lines), Assignment (dispatcher, carrier, carrier reference and cost). Table: responsive columns (`visibleFrom`, as other lists do), filters (state, customer, service type, dates), global search on name, customer reference and waybill number.
4. View page: header actions call the workflow (never set `state` directly), a timeline relation manager (read-only events), and chatter.
5. Tests: create, every valid transition, invalid transitions throw, permission denied without the custom permission, company A can't see company B's shipments (list, view, global search).

**Acceptance:** the WP-2 tests pass. The list page has no N+1 queries (assert
the query count in a test).

---

### WP-3 — Vehicles and drivers

**Depends on:** WP-1. **Can run alongside:** WP-2, WP-8a.

**Owns:** `src/Filament/Clusters/Fleet/Resources/{VehicleResource,DriverResource}.php`,
`src/Filament/Clusters/Fleet/Resources/{VehicleResource,DriverResource}/**`
(including `Schemas/` and `Tables/` - see "Resource file layout" above; this is
the first package to use it),
`src/Services/FleetImporter.php`,
`resources/lang/en/filament/clusters/fleet/**`, `tests/Feature/Fleet/*`.

**Steps**
1. Vehicle resource: ownership, type, capacity, carrier (for third-party ownership), optional Maintenance equipment select shown only when `Package::isPluginInstalled('maintenance')`, and the telematics device reference.
2. Driver resource: pick an employee **or** a carrier contact (partner). Licence fields, a badge when the licence has expired or expires within 30 days, and licence numbers hidden from users without update permission.
3. Adoption helpers (section 2a): a bulk action, **"Add drivers from employees"** (pick employees; skip those who already have a driver profile), and **"Import from Maintenance equipment"** (only when Maintenance is installed; creates vehicles linked through `equipment_id`, skipping ones already linked).
4. Tests: unique registration per company (the same number is allowed in another company), the expiry badge logic, company isolation, both imports skip duplicates when run twice.

---

### WP-4 — Trips and dispatch board

**Depends on:** WP-2, WP-3. **Can run alongside:** WP-5, WP-6, WP-7.

**Owns:** `src/Services/DispatchService.php`,
`src/Filament/Clusters/Operations/Resources/TripResource/**`,
`src/Filament/Clusters/Operations/Pages/DispatchBoard.php`,
`resources/views/filament/pages/dispatch-board.blade.php`, the matching English
text, `tests/Feature/Dispatch/*`.

**Steps**
1. `DispatchService`: `assign(Trip, Shipment[])` (attach, schedule the stops, move shipments to AwaitingPickup through `ShipmentWorkflow`), `dispatch(Trip)`, `start(Trip)`, `complete(Trip)`. Each is atomic, checks permissions, and refuses an inactive vehicle, an inactive driver or an expired licence. When `capacity_check = warn`, compare the total weight and volume on the trip with the vehicle's capacity and return a warning; don't block (D5).
2. Trip resource with a shipments relation manager (attach confirmed shipments only).
3. Dispatch board page: Filament tables/tabs for Unassigned, Awaiting pickup, Active trips, Due today, Overdue and Failed. Eager-load the relations, paginate, no drag and drop.
4. Tests: consolidation of 2 shipments on 1 trip, refusal cases, board query count, isolation.

---

### WP-5 — Delivery and proof of delivery

**Depends on:** WP-2. **Can run alongside:** WP-4, WP-6, WP-7.

**Owns:** `src/Services/DeliveryService.php`,
`src/Filament/Clusters/Operations/Resources/ShipmentResource/RelationManagers/DeliveryProofsRelationManager.php`,
`src/Filament/Clusters/Operations/Resources/ShipmentResource/Actions/{PickupAction,DeliverAction,FailDeliveryAction}.php`,
the matching English text, `tests/Feature/Delivery/*`.

**Steps**
1. `DeliveryService`: `markPickedUp()`, `markInTransit()`, `markOutForDelivery()`, `deliver(Shipment, PodData)`, `fail(Shipment, reason)`, `retry()`, `return()`. POD rules come from `LogisticsSettings` (D8). Stop actual times are updated alongside.
2. POD photo and signature are stored on the **public disk under the tenant prefix** (the same way other uploads are), never a new storage system. Check that `SecureStorageController` serves them to the same company only.
3. Actions are registered on the shipment view page through the resource's `getRelations`/header-action extension points. If that requires editing a WP-2 file, write it under "Requests" for the WP-2 owner instead.
4. Tests: can't deliver without the required POD, failed → retry → delivered, file access denied to another company.

---

### WP-5b — Stop link for POD capture (optional, D15)

**Depends on:** WP-5. **Can run alongside:** WP-6, WP-7, WP-8b.

**Owns:** `src/Services/StopLinkService.php`,
`src/Http/Controllers/StopLinkController.php`, `routes/web.php` (in this
plugin), `resources/views/stop-link/*`,
`src/Filament/Clusters/Operations/Resources/ShipmentResource/Actions/SendStopLinkAction.php`,
the matching English text, `tests/Feature/StopLinks/*`.

**Steps**
1. `StopLinkService::issue(Stop)` returns a URL with a random token. Only the **hash** is stored. It expires after `stop_link_ttl_hours`, can be used once, can be revoked, and requires `send_pod_link`. The action shows the link to copy or share. **No SMS or WhatsApp provider is built in** (prompt rule 34).
2. The public route (`/logistics/pod/{token}`) is rate-limited and needs no login. It resolves the stop **without** the user company scope, loads it **only** by token hash, and checks expiry, use and revocation. Nothing else is reachable from the page.
3. A mobile page (plain Blade, uses the site CSS, works at 390px) with recipient name, photo (camera input), signature (canvas, sent as PNG) and optional browser location. Submitting calls `DeliveryService::deliver()` with `captured_via = stop_link`. Files go to the tenant disk under the stop's company.
4. Tests: an expired, used or revoked token gets a 404/410 with no information leaked; a token can't be used for another stop; files land under the right company prefix; the rate limit applies; the link can't be issued without the permission.

**Security review is required** before the package leaves `review`: it is the only public entry point in the plugin.

---

### WP-6 — Waybill and delivery-note PDF

**Depends on:** WP-2. **Can run alongside:** WP-4, WP-5, WP-7.

**Owns:** `resources/views/pdf/*`,
`src/Filament/Clusters/Operations/Resources/ShipmentResource/Actions/PrintWaybillAction.php`,
the matching English text, `tests/Feature/Documents/*`.

**Steps:** follow the invoice print pattern (`accounts/.../InvoiceResource/Actions/PreviewAction.php`, dompdf, company letterhead): shipment number, customer, sender and recipient, route, cargo lines, driver and vehicle, dates, and a signature box. `waybill_no` comes from the `logistics.waybill` sequence when the waybill is first printed. **Test:** the PDF renders and uses the shipment's company details.

---

### WP-7 — Charges and shipment invoicing

**Depends on:** WP-2. **Can run alongside:** WP-4, WP-5, WP-6, WP-8b.

**Owns:** `src/Services/ShipmentInvoicer.php`,
`src/Filament/Clusters/Operations/Resources/ShipmentResource/RelationManagers/{ChargesRelationManager,InvoicesRelationManager}.php`,
`src/Filament/Clusters/Operations/Resources/ShipmentResource/Actions/CreateInvoiceAction.php`,
`src/Filament/Clusters/Finance/Pages/UnbilledCharges.php`, the matching English text,
`tests/Feature/Invoicing/*`.

**Steps**
1. Charge lines: a service product fills in the description, price and taxes; taxes use existing `Tax` records only.
2. `ShipmentInvoicer::createInvoice(Shipment)`: follow `sales/src/Services/Invoicer.php` exactly. Use the `OUT_INVOICE` move, the shipment's company and currency, the customer as partner, and `invoice_origin` = the shipment name. Create lines from billable, not-yet-invoiced charges, copying their taxes. Set `move_line_id` on each charge, attach the pivot, then `AccountFacade::computeAccountMove()`. Everything in one transaction. Refuse if nothing is billable.
3. Invoices relation manager (links to the existing invoice view) showing payment state.
3b. **Waiting-time (detention) suggestion.** When a stop's actual departure minus actual arrival is more than `free_waiting_minutes`, show a suggested "Waiting time" charge (the D14 product) for the extra time. It is added **only when a user confirms it**, never automatically.
4. Tests: totals equal `TaxComputer`'s result for inclusive and exclusive taxes, a waiting-time suggestion appears only past free time, a second invoice only picks up new charges, permission `create_invoice` is required, cross-company refusal. **Then run the whole `AccountFeature` suite.**

---

### WP-8a — Expense records and approval

**Depends on:** WP-1. **Can run alongside:** WP-2, WP-3.

**Owns:** `src/Services/ExpenseApproval.php`,
`src/Filament/Clusters/Finance/Resources/ExpenseResource/**`, the matching
English text, `tests/Feature/Expenses/Approval*`.

**Steps:** the expense resource (link to a shipment, trip or vehicle; the
receipt upload is required when the category says so), states draft →
submitted → approved or rejected, and the `approve` permission. **Tests:**
the approval flow, a submitter without `approve` can't approve their own
expense, isolation.

### WP-8b — Expense and carrier bills

**Depends on:** WP-8a, WP-2. **Can run alongside:** WP-7.

**Owns:** `src/Services/ExpensePoster.php`,
`src/Filament/Clusters/Finance/Resources/ExpenseResource/Actions/PostBillAction.php`,
`src/Filament/Clusters/Operations/Resources/ShipmentResource/RelationManagers/ExpensesRelationManager.php`,
the matching English text, `tests/Feature/Expenses/Posting*`.

**Steps:** per D2 and D3, `ExpensePoster::postForShipment(Shipment)` and
`postExpenses(Collection)` group **approved, unbilled** expenses by bill
partner. The bill partner is the employee's `partner_id` when
`paid_by = employee`, otherwise the payee. The poster creates **one draft
`IN_INVOICE` per partner** (in `bill_journal_id`), with one line per expense
on the category's expense account, sets `bill_move_id`, and sets the state to
billed, all in one transaction. It refuses when a partner is missing (for
example an employee without a contact). Never post the bill automatically. The
shipment view shows **revenue (charges) − costs (expenses) = margin**, visible
only with `view_financials`.
**Tests:** one bill per vendor, a reimbursement bill goes to the employee's
partner, the amount and account are right, no double posting, permission
`post_bill`, then the `AccountFeature` suite.

---

### WP-9 — Sales quotation link (only if D1 = Sales)

**Depends on:** WP-7. **Owns:** `src/Listeners/*`, `src/Services/ShipmentFromOrder.php`, `tests/Feature/Sales/*`.

**Steps:** create a draft shipment from a confirmed sales order that has
logistics service products, using an event or listener. The listener does
nothing unless `LogisticsAccess::enabledFor($order->company_id)` is true, and
it never backfills orders confirmed before the company was enabled.
**Do not edit Sales files.** If Sales fires no suitable event, stop and report the smallest hook
needed. Integrate only when Sales is installed. **Tests:** the conversion
copies customer, currency, lines and charges, and runs only once.

### WP-9b — Customer page integration (only if D11 = yes)

**Depends on:** WP-7. Look for a Partners extension point first. If none
exists, report instead of editing.

---

### WP-10 — Dashboard widgets

**Depends on:** WP-4, WP-5. **Owns:** `src/Filament/Pages/Dashboard.php`, `src/Filament/Widgets/*`, the matching English text, `tests/Feature/Dashboard/*`.

**Steps:** stat widgets (shipments today, awaiting pickup, in transit, out for
delivery, delivered today, overdue, failed, active trips, available vehicles
and drivers). Finance widgets appear only with `view_financials`. Use
aggregate queries (one grouped query per widget), not per-row loops.
**Tests:** figures match seeded data, company isolation, finance widgets are
hidden without permission.

### WP-11 — Reports

**Depends on:** WP-4, WP-5, WP-7, WP-8b. **Owns:** `src/Filament/Clusters/Reporting/**`, the matching English text, `tests/Feature/Reports/*`.

**Steps:** five reports as filtered Filament table pages with export,
following the Accounting reporting pages: Shipment register, Delivery
performance (on time / late / failed by period), Shipment profitability
(charges − expenses − carrier cost), Vehicle trip history, Driver trip
history. Filters: dates, customer, status, vehicle, driver.
**Tests:** profitability sums, isolation, export respects filters.

---

### WP-12 — Translations (rolling)

**Depends on:** each package being in `review` or `done`. **Owns:**
`resources/lang/{ar,es,fr,pt_BR}/**` of this plugin only.

**Steps:** mirror the English files exactly (same keys), use business
terminology, and keep placeholders. Run `php artisan translations:check
--plugin=logistics --details` in Sail until it is clean. Arabic must read
right-to-left correctly.

### WP-13 — Hardening and release (runs alone, last)

**Owns:** `docs/logistics.md` (setup, usage, permissions, uninstall behaviour),
`CHANGELOG.md` and `docs/change-log.md` entries, plus fixes for review findings
(coordinate with the package owner).

**Steps:** authorisation audit (every action and policy), company-isolation
audit (lists, views, search, widgets, reports, exports, files), N+1 review,
migration review (every `down()` works), phone-width check, **all ten existing
suites plus `LogisticsFeature`**, `translations:check`, and a rehearsal of the
production install checklist (section 2a) on a **copy of the production
database**: install → enable one company → use → disable → confirm the other
companies were never affected → uninstall guard. Check the Supabase advisor
for the new tables.

**Definition of done:** the prompt's list, plus every row in section 5 marked `done`.

---

## 7. Handoff log

Each package appends a block here when it moves to `review`.

```
### WP-x — <title> — <date> — <owner>

Files created:
Files modified:
Migrations / tables:
Reused components:
Tests added / results (command + pass count):
Existing suites run / results:
Deviations from the plan:
Risks:
Requests for other packages:
```

### WP-6 - Waybill and delivery-note PDF - 2026-09-21 - Codex

Files created:
- `plugins/webkul/logistics/src/Filament/Clusters/Operations/Resources/ShipmentResource/Actions/PrintWaybillAction.php`
- `plugins/webkul/logistics/resources/views/pdf/waybill.blade.php`
- `plugins/webkul/logistics/resources/lang/en/documents/waybill.php`
- `plugins/webkul/logistics/tests/Feature/Documents/WaybillTest.php`

Files modified:
- `docs/logistics-plan.md`
- `docs/change-log.md`

Migrations / tables:
- None. Uses the existing nullable `logistics_shipments.waybill_no` column and
  existing per-company `logistics.waybill` sequence.

Reused components:
- `Webkul\Support\Traits\PDFHandler` and Dompdf, matching the Accounts invoice
  print path.
- `Webkul\Logistics\Support\LogisticsSequences` for company-scoped numbering.
- Existing `ShipmentPolicy::view` / `view_logistics_shipment` permission.

Design notes for later packages:
- The action name is `printWaybill` because dots are parsed as nested Filament
  action paths.
- `visible()` checks `view`, and `download()` repeats authorization before any
  number is assigned or PDF content is rendered.
- First print locks the shipment row, explicitly ensures the shipment company's
  waybill sequence, consumes one number, and saves it. Repeat or concurrent
  prints reuse the stored number.
- Letterhead and address data come from `$shipment->company` and its partner;
  the session's current company is never used to render the document.
- The latest linked trip supplies the displayed driver and vehicle.

Tests added / results (command + pass count):
- `docker compose run --rm --no-deps -e DB_DATABASE=aureuserp_testing_wp6 laravel.test php artisan test --testsuite=LogisticsFeature --filter=WaybillTest`: **4 passed** (11 assertions).

Existing suites run / results:
- `docker compose run --rm --no-deps -e DB_DATABASE=aureuserp_testing_wp6 laravel.test php artisan test --testsuite=LogisticsFeature`: **60 passed** (293 assertions).
- `docker compose run --rm --no-deps laravel.test vendor/bin/pint --dirty --format agent`: passed.
- `docker compose run --rm --no-deps laravel.test vendor/bin/pint --format agent plugins/webkul/logistics/src/Filament/Clusters/Operations/Resources/ShipmentResource/Actions/PrintWaybillAction.php plugins/webkul/logistics/resources/lang/en/documents/waybill.php plugins/webkul/logistics/tests/Feature/Documents/WaybillTest.php`: passed.
- Dedicated local database: `aureuserp_testing_wp6`; no test run used the
  parallel agent's database.

Deviations from the plan:
- None. Page wiring is intentionally left to the WP-2 owner as required by the
  package boundary.

Risks:
- The action exists and is directly tested, but is not visible in the shipment
  UI until the WP-2-owned view page is wired as requested below.

Requests for other packages:
- WP-2 owner: in
  `ShipmentResource/Pages/ViewShipment.php`, add this import:
  `use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\PrintWaybillAction;`
- WP-2 owner: add this exact line to `getHeaderActions()`:
  `PrintWaybillAction::make(),`
- WP-12: translate `resources/lang/en/documents/waybill.php`.

### WP-3 - Vehicles and drivers - 2026-09-21 - Codex

Files created:
- `plugins/webkul/logistics/src/Filament/Clusters/Fleet/Resources/VehicleResource.php`
- `plugins/webkul/logistics/src/Filament/Clusters/Fleet/Resources/VehicleResource/Pages/{ListVehicles,CreateVehicle,ViewVehicle,EditVehicle}.php`
- `plugins/webkul/logistics/src/Filament/Clusters/Fleet/Resources/VehicleResource/Schemas/{VehicleForm,VehicleInfolist}.php`
- `plugins/webkul/logistics/src/Filament/Clusters/Fleet/Resources/VehicleResource/Tables/VehiclesTable.php`
- `plugins/webkul/logistics/src/Filament/Clusters/Fleet/Resources/DriverResource.php`
- `plugins/webkul/logistics/src/Filament/Clusters/Fleet/Resources/DriverResource/Pages/{ListDrivers,CreateDriver,ViewDriver,EditDriver}.php`
- `plugins/webkul/logistics/src/Filament/Clusters/Fleet/Resources/DriverResource/Schemas/{DriverForm,DriverInfolist}.php`
- `plugins/webkul/logistics/src/Filament/Clusters/Fleet/Resources/DriverResource/Tables/DriversTable.php`
- `plugins/webkul/logistics/src/Services/FleetImporter.php`
- `plugins/webkul/logistics/resources/lang/en/filament/clusters/fleet/resources/{vehicle,driver}.php`
- `plugins/webkul/logistics/tests/Feature/Fleet/{FleetResourceTest,FleetImporterTest}.php`

Files modified:
- `docs/logistics-plan.md`
- `docs/change-log.md`

Migrations / tables:
- None. WP-1 already created `logistics_vehicles` and `logistics_drivers`; WP-3 uses the existing `unique(company_id, registration_no)` index.

Model and permission names:
- Models: `Webkul\Logistics\Models\Vehicle`, `Webkul\Logistics\Models\Driver`.
- Resources: `Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource`, `Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource`.
- Permissions reused from WP-1 Shield config: `view_any/view/create/update/delete/delete_any/restore/restore_any/force_delete/force_delete_any_logistics_vehicle` and the same affixes for `logistics_driver`.

Reused components:
- `LogisticsAccess` for per-company enablement checks.
- Existing `VehiclePolicy` and `DriverPolicy`.
- Existing `Vehicle`, `Driver`, `VehicleType`, `VehicleOwnership`, `Employee`, `Partner`, and Maintenance `Equipment` models.
- Existing split resource layout used by upstream and the Partner resource.

Design notes for later packages:
- `FleetImporter` is safe to run twice. Driver import skips employees that already have a driver profile; Maintenance import skips equipment already linked through `equipment_id`.
- The importer calls `LogisticsAccess::ensureEnabled($source->company_id)` per source record, so super-admin Gate bypass cannot create fleet rows for disabled companies.
- Imported vehicles use `serial_no`, then `partner_ref`, then `EQ-{id}` as the registration number.
- Driver licence numbers are hidden in the driver table and infolist unless the user has `update_logistics_driver`.
- Driver licence expiry uses the existing model helpers: expired dates are danger, dates up to and including 30 days ahead are warning.

Tests added / results (command + pass count):
- Added `tests/Feature/Fleet/FleetResourceTest.php` and `tests/Feature/Fleet/FleetImporterTest.php`.
- `docker compose run --rm --no-deps laravel.test php artisan test --testsuite=LogisticsFeature --filter=Fleet`: **4 passed** (45 assertions).
- `docker compose run --rm --no-deps laravel.test php artisan test --testsuite=LogisticsFeature`: **56 passed** (282 assertions).
- `docker compose run --rm --no-deps laravel.test vendor/bin/pint --dirty --format agent`: passed.

Existing suites run / results:
- The full `LogisticsFeature` suite above includes the existing Adoption, Foundation and Shipment tests plus the new Fleet tests.

Deviations from the plan:
- The adoption helpers are implemented as list-page header actions with multi-select forms, backed by `FleetImporter`, rather than as record-selection bulk actions. There are no existing employee or equipment rows in the Fleet table to select; the actions still perform bulk creation and are idempotent.

Risks:
- Maintenance equipment has no guaranteed registration field. If `serial_no` and `partner_ref` are both blank, the importer falls back to `EQ-{equipment id}`.

Requests for other packages:
- WP-12: translate the new English files under `resources/lang/en/filament/clusters/fleet/resources/`.

### WP-1a - Starter kit - 2026-09-17 - Codex

Files created:
- `plugins/webkul/logistics/composer.json`
- `plugins/webkul/logistics/src/Enums/ShipmentState.php`
- `plugins/webkul/logistics/src/Enums/TripState.php`
- `plugins/webkul/logistics/src/Enums/StopType.php`
- `plugins/webkul/logistics/src/Enums/StopState.php`
- `plugins/webkul/logistics/src/Enums/ShipmentEventType.php`
- `plugins/webkul/logistics/src/Enums/ShipmentEventSource.php`
- `plugins/webkul/logistics/src/Enums/TransportMode.php`
- `plugins/webkul/logistics/src/Enums/ShipmentPriority.php`
- `plugins/webkul/logistics/src/Enums/VehicleOwnership.php`
- `plugins/webkul/logistics/src/Enums/ExpenseState.php`
- `plugins/webkul/logistics/resources/lang/en/enums/shipment-state.php`
- `plugins/webkul/logistics/resources/lang/en/enums/trip-state.php`
- `plugins/webkul/logistics/resources/lang/en/enums/stop-type.php`
- `plugins/webkul/logistics/resources/lang/en/enums/stop-state.php`
- `plugins/webkul/logistics/resources/lang/en/enums/shipment-event-type.php`
- `plugins/webkul/logistics/resources/lang/en/enums/shipment-event-source.php`
- `plugins/webkul/logistics/resources/lang/en/enums/transport-mode.php`
- `plugins/webkul/logistics/resources/lang/en/enums/shipment-priority.php`
- `plugins/webkul/logistics/resources/lang/en/enums/vehicle-ownership.php`
- `plugins/webkul/logistics/resources/lang/en/enums/expense-state.php`
- `resources/svg/logistics.svg`
- `public/svg/logistics.svg`

Files modified:
- `docs/logistics-plan.md`

Migrations / tables:
- None.

Reused components:
- Mirrored `plugins/webkul/maintenance/composer.json`.
- Mirrored enum label/options/color style from Maintenance and Sales enum patterns.
- Mirrored the maintenance icon frame and color constraints.

Tests added / results (command + pass count):
- None added.
- A) `docker run --rm -v "${PWD}:/app" -w /app --entrypoint sh sail-8.4/app -c 'for f in $(find plugins/webkul/logistics -name "*.php"); do php -l "$f" | grep -v "^No syntax errors" ; done; echo lint-done'`
  - Result: failed to execute because Docker Desktop Linux engine was unavailable: `failed to connect to the docker API at npipe:////./pipe/dockerDesktopLinuxEngine; check if the path is correct and if the daemon is running: open //./pipe/dockerDesktopLinuxEngine: The system cannot find the file specified.`
- B) Created `storage/app/wp1a-check.php` with the requested content, then ran `docker run --rm -v "${PWD}:/app" -w /app --entrypoint php sail-8.4/app storage/app/wp1a-check.php`
  - Result: failed to execute for the same Docker engine error above.
  - Cleanup: deleted `storage/app/wp1a-check.php`.
- C) File and SVG checks:
  - Enum files: `10`
  - English enum label files: `10`
  - SVG comparison: `same`
- D) `git status --short`
  - Output included two permission warnings for `C:\Users\gomat/.config/git/ignore`.
  - Untracked paths shown: `.claude/`, `docs/logistics-plan.md`, `plugins/webkul/logistics/`, `public/svg/logistics.svg`, `resources/svg/logistics.svg`.
  - Note: `.claude/` was already present before WP-1a and was not touched.

Existing suites run / results:
- None. The WP-1a prompt forbids Pest/phpunit/artisan runs.

Deviations from the plan:
- Required Docker PHP lint and enum script checks could not be completed because Docker was not running/reachable.

Risks:
- PHP syntax and enum runtime verification still need to be rerun once Docker Desktop is available.

Requests for other packages:
- None.

Unsure values or labels:
- None.

Reviewer verification (2026-09-17, Docker still unavailable, so host PHP 8.3 was used):
- `php -l` on all 11 PHP files: clean.
- Enum check (Filament contracts loaded directly, no vendor autoload): **OK**. All 10 enums have exactly the specified values; the ShipmentState, TripState and ExpenseState maps match; `canTransitionTo()` is right for every pair; colours match; every value has a non-empty English label, with no extra keys; keys use `logistics::enums/<kebab>`.
- `composer.json` matches the Maintenance pattern. The two SVG copies are identical. The icon was **not rendered**: it was reviewed from source only (frame, palette and truck shapes look right). Look at it on the Plugins page once WP-1 installs the plugin.
- Follow-up for WP-1a: reword `arrived_destination` → "Arrived at Destination" and `departed_origin` → "Departed from Origin".

### WP-12 (enums slice) - 2026-09-17 - Codex

> Reviewer verification (2026-09-17, host PHP): `php -l` is clean on all 50
> language files. The parity check is **OK**: 40 files, keys and order match
> English, no BOM, and Arabic values are all in Arabic script. The English fixes
> for `arrived_destination` and `departed_origin` are present. The only values
> identical to English are es/pt_BR "Normal" and es/fr/pt_BR "Multimodal", both
> correct. Terminology was spot-checked (shipment states and events in all four
> languages) and is consistent with the glossary. The enum check still passes
> after the English edit.

Files created:
- `plugins/webkul/logistics/resources/lang/ar/enums/expense-state.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/shipment-event-source.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/shipment-event-type.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/shipment-priority.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/shipment-state.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/stop-state.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/stop-type.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/transport-mode.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/trip-state.php`
- `plugins/webkul/logistics/resources/lang/ar/enums/vehicle-ownership.php`
- `plugins/webkul/logistics/resources/lang/es/enums/expense-state.php`
- `plugins/webkul/logistics/resources/lang/es/enums/shipment-event-source.php`
- `plugins/webkul/logistics/resources/lang/es/enums/shipment-event-type.php`
- `plugins/webkul/logistics/resources/lang/es/enums/shipment-priority.php`
- `plugins/webkul/logistics/resources/lang/es/enums/shipment-state.php`
- `plugins/webkul/logistics/resources/lang/es/enums/stop-state.php`
- `plugins/webkul/logistics/resources/lang/es/enums/stop-type.php`
- `plugins/webkul/logistics/resources/lang/es/enums/transport-mode.php`
- `plugins/webkul/logistics/resources/lang/es/enums/trip-state.php`
- `plugins/webkul/logistics/resources/lang/es/enums/vehicle-ownership.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/expense-state.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/shipment-event-source.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/shipment-event-type.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/shipment-priority.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/shipment-state.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/stop-state.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/stop-type.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/transport-mode.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/trip-state.php`
- `plugins/webkul/logistics/resources/lang/fr/enums/vehicle-ownership.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/expense-state.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/shipment-event-source.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/shipment-event-type.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/shipment-priority.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/shipment-state.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/stop-state.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/stop-type.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/transport-mode.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/trip-state.php`
- `plugins/webkul/logistics/resources/lang/pt_BR/enums/vehicle-ownership.php`

Files modified:
- `plugins/webkul/logistics/resources/lang/en/enums/shipment-event-type.php`
- `docs/logistics-plan.md`

Migrations / tables:
- None.

Reused components:
- Reused matching status terminology from the Inventories, Sales and Purchases locale enum files.
- Applied the WP-12 logistics glossary for transport-specific wording.

Tests added / results (command + pass count):
- None added.
- A) `Get-ChildItem plugins/webkul/logistics/resources/lang -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName } | Select-String -NotMatch "^No syntax errors"; "lint-done"`
  - Full output:
    ```text
    lint-done
    ```
- B) `php storage/app/wp12-enums-check.php`
  - Full output:
    ```text
    warning: plugins/webkul/logistics/resources/lang/es/enums/shipment-priority.php: 'normal' is identical to English ('Normal')
    warning: plugins/webkul/logistics/resources/lang/pt_BR/enums/shipment-priority.php: 'normal' is identical to English ('Normal')
    warning: plugins/webkul/logistics/resources/lang/es/enums/transport-mode.php: 'multimodal' is identical to English ('Multimodal')
    warning: plugins/webkul/logistics/resources/lang/fr/enums/transport-mode.php: 'multimodal' is identical to English ('Multimodal')
    warning: plugins/webkul/logistics/resources/lang/pt_BR/enums/transport-mode.php: 'multimodal' is identical to English ('Multimodal')
    OK (5 warnings)
    ```
  - Cleanup: deleted `storage/app/wp12-enums-check.php`.

Warnings kept as-is:
- Spanish and Brazilian Portuguese use `Normal` with the same spelling as English.
- Spanish, French and Brazilian Portuguese use `Multimodal`, the exact logistics glossary term in each locale.

Existing suites run / results:
- None. This slice explicitly forbids Artisan, Pest/phpunit, Composer, NPM and database commands.

Deviations from the plan:
- The repository-required `php vendor/bin/pint --dirty --format agent plugins/webkul/logistics/resources/lang` run reformatted the dirty WP-1a enum classes and all English enum label files despite the supplied path. Those out-of-scope formatting-only changes were restored by hand; only the new locale files retain formatter changes, plus the two requested English label values.

Risks:
- WP-12 remains open for translation files introduced by later work packages; this handoff covers only the current enum slice.

Requests for other packages:
- None.

Unsure terms:
- None.

### WP-12 (foundation slice) - 2026-09-17 - Codex

Files created:
- 100 files: each of the following 25 paths was created under `plugins/webkul/logistics/resources/lang/{ar,es,fr,pt_BR}/`:
  - `enums/capacity-check.php`
  - `enums/expense-paid-by.php`
  - `enums/proof-capture-channel.php`
  - `exceptions.php`
  - `filament/clusters/configurations.php`
  - `filament/clusters/finance.php`
  - `filament/clusters/fleet.php`
  - `filament/clusters/operations.php`
  - `filament/clusters/reporting.php`
  - `filament/clusters/configurations/pages/manage-company-settings.php`
  - `filament/clusters/configurations/resources/common.php`
  - `filament/clusters/configurations/resources/expense-category.php`
  - `filament/clusters/configurations/resources/package-type.php`
  - `filament/clusters/configurations/resources/service-type.php`
  - `filament/clusters/configurations/resources/vehicle-type.php`
  - `models/driver.php`
  - `models/expense-category.php`
  - `models/expense.php`
  - `models/package-type.php`
  - `models/service-type.php`
  - `models/shipment.php`
  - `models/trip.php`
  - `models/vehicle-type.php`
  - `models/vehicle.php`
  - `services/company-provisioner.php`

Files modified:
- `docs/logistics-plan.md`

Migrations / tables:
- None.

Reused components:
- Reused the WP-12 enum-slice terminology for shipment, trip, stop, delivery, pickup, transport and status labels.
- Reused Accounting and Inventories terminology for company, settings, journal, account, vendor bill, customer, status, code and name.
- Applied the supplied foundation glossary for clusters, configuration records, readiness and Logistics enable/disable actions.

Tests added / results (command + pass count):
- None added.
- A) `Get-ChildItem plugins/webkul/logistics/resources/lang -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName } | Select-String -NotMatch "^No syntax errors"; "lint-done"`
  - Full output:
    ```text
    lint-done
    ```
- B) `php storage/app/wp12-lang-check.php`
  - Full output:
    ```text
    English files: 35
    warning: plugins/webkul/logistics/resources/lang/es/enums/shipment-priority.php: 'normal' identical to English ('Normal')
    warning: plugins/webkul/logistics/resources/lang/pt_BR/enums/shipment-priority.php: 'normal' identical to English ('Normal')
    warning: plugins/webkul/logistics/resources/lang/es/enums/transport-mode.php: 'multimodal' identical to English ('Multimodal')
    warning: plugins/webkul/logistics/resources/lang/fr/enums/transport-mode.php: 'multimodal' identical to English ('Multimodal')
    warning: plugins/webkul/logistics/resources/lang/pt_BR/enums/transport-mode.php: 'multimodal' identical to English ('Multimodal')
    warning: plugins/webkul/logistics/resources/lang/pt_BR/filament/clusters/configurations/pages/manage-company-settings.php: 'sections.status' identical to English ('Status')
    warning: plugins/webkul/logistics/resources/lang/fr/filament/clusters/configurations/resources/common.php: 'fields.code' identical to English ('Code')
    warning: plugins/webkul/logistics/resources/lang/fr/filament/clusters/configurations/resources/common.php: 'columns.code' identical to English ('Code')
    warning: plugins/webkul/logistics/resources/lang/fr/filament/clusters/configurations.php: 'navigation.title' identical to English ('Configuration')
    warning: plugins/webkul/logistics/resources/lang/fr/filament/clusters/finance.php: 'navigation.title' identical to English ('Finance')
    warning: plugins/webkul/logistics/resources/lang/pt_BR/models/expense.php: 'log-attributes.state' identical to English ('Status')
    warning: plugins/webkul/logistics/resources/lang/pt_BR/models/shipment.php: 'log-attributes.state' identical to English ('Status')
    warning: plugins/webkul/logistics/resources/lang/pt_BR/models/trip.php: 'log-attributes.state' identical to English ('Status')
    OK (13 warnings)
    ```
  - Cleanup: deleted `storage/app/wp12-lang-check.php`.

Warnings kept as-is:
- Spanish and Brazilian Portuguese `Normal`, and Spanish, French and Brazilian Portuguese `Multimodal`, are the correct terms from the enum slice.
- Brazilian Portuguese uses `Status`, matching the existing application terminology.
- French uses `Code`, `Configuration` and `Finance`; these are the correct French terms and match the supplied glossary where applicable.

Existing suites run / results:
- None. This slice explicitly permits only `php -l` and the supplied standalone check script.

Deviations from the plan:
- The prompt expected `English files: 38`, but the source tree contains the 25 listed foundation files plus the 10 earlier enum files, for 35 total. No additional English source files exist to mirror. The supplied script reported `English files: 35` and exited successfully with `OK (13 warnings)`.

Risks:
- WP-1 remains in progress. Any English language files added after this handoff will need a later WP-12 slice.

Requests for other packages:
- Notify WP-12 if WP-1 adds English language files beyond the 25 translated here.

Unsure terms:
- `Dispatcher` had no supplied glossary entry. It was translated using transport-domain wording: `مسؤول الإرسال`, `Responsable de despacho`, `Agent d’exploitation` and `Responsável pelo despacho`; a native domain reviewer may standardize these if the product has a preferred role title.

> Reviewer note (Claude, 2026-09-17): the "English files: 38" expectation in the
> foundation-slice prompt was my counting error; 35 (25 + 10 enums) is correct.

### WP-1 - Foundation - 2026-09-17 - Claude

Files created (plugins/webkul/logistics/):
- `config/logistics.php`, `config/filament-shield.php`
- `database/migrations/2026_10_01_000001` … `000019` (19 `create_logistics_*_table`)
- `database/seeders/` (DatabaseSeeder + service, vehicle, package types, expense categories)
- `database/factories/` (12 factories)
- `src/LogisticsServiceProvider.php`, `src/LogisticsPlugin.php`
- `src/Enums/{ExpensePaidBy,ProofCaptureChannel,CapacityCheck}.php`
- `src/Exceptions/{LogisticsNotEnabledException,CompanyMismatchException,UninstallBlockedException}.php`
- `src/Models/` (16 models + `Concerns/InheritsParentCompany.php`)
- `src/Policies/` (LogisticsPolicy, ConfigurationPolicy, ShipmentChildPolicy + 15 model policies)
- `src/Services/CompanyProvisioner.php`
- `src/Support/{LogisticsAccess,LogisticsSequences,UninstallGuard}.php`
- `src/Filament/Clusters/{Operations,Fleet,Finance,Reporting,Configurations}.php`
- `src/Filament/Clusters/Configurations/Resources/` (4 resources + manage pages + `Concerns/ConfiguresCompanyScope.php`)
- `src/Filament/Clusters/Configurations/Pages/ManageCompanySettings.php`
- `resources/lang/en/` (exceptions, 3 enums, 9 models, 5 clusters, 5 configuration resources, settings page, provisioner), `resources/views/.gitkeep`
- `tests/Helpers/LogisticsHelper.php`, `tests/Feature/Foundation/{Install,CompanyScopingInvariants,Policy,RecordIntegrity,Screens}Test.php`, `tests/Feature/Adoption/AdoptionTest.php`

Files modified:
- `bootstrap/providers.php` (register the provider)
- `phpunit.xml` (`LogisticsFeature` suite)
- `plugins/webkul/support/src/Enums/NavigationGroup.php` (`Logistics` case + `icon-logistics`)
- `lang/{en,ar,es,fr,pt_BR}/admin.php` (`navigation.logistics`)
- `docs/logistics-plan.md`, `docs/change-log.md`, `docs/agent-reminders.md`, `AGENTS.md`

Migrations / tables: the 19 `logistics_*` tables in section 2. No existing table is altered.

Reused components: BelongsToCompany/CompanyScope/CompanyContext, SequenceService,
HasChatter/HasLogActivity, HasCustomFields, Shield + PermissionManager naming,
PackageServiceProvider install/uninstall hooks, Partner/Employee/Product/Journal/
Account/Tax/Move models, test helpers (TestBootstrapHelper, CompanyHelper,
FilamentHelper, CompanyScopeHelper).

Tests added / results:
- `php artisan test --testsuite=LogisticsFeature`: **30 passed (160 assertions)**
- `--filter=ScreensTest` (added after): **8 passed (21 assertions)**
- `php -l`: clean on all plugin PHP files and the changed core files.

Existing suites run / results:
- `AccountFeature`: **521 passed** (1,382 assertions), 0 failed.
- `SupportFeature` on a clean test database: **115 passed** (720 assertions), 0 failed.
- `SupportFeature` run straight after the Logistics suite: 112 passed, 3 failed
  (`UOMTest` delete/restore/force-delete). **Not caused by Logistics**: the app
  boots and reads the `plugins` table left by the previous suite, so the Products
  plugin looks installed and its `UOMObserver` is registered; `migrate:fresh`
  then drops `products_products`, which that observer queries. Dropping and
  recreating `aureuserp_testing` first makes the suite pass. Any suite that runs
  after a Products-installing suite hits this.
  **Suggested fix, outside this package:** `TestBootstrapHelper` should reload
  `Package::$plugins` after `migrate:fresh` (and only register plugin observers
  for plugins installed in the current database).

Deviations from the plan (all documented in sections 2, 2a and 3.3):
- The per-company switch and settings are a plugin table
  (`logistics_company_settings`), not a company-scoped Spatie group
  (CompanyAwareSettingsRepository falls back to the default company).
  `CompanyAwareSettingsRepository` is unchanged.
- The uninstall guard uses the existing `startWith` hook; `UninstallCommand`
  is unchanged. Override: `LOGISTICS_ALLOW_UNINSTALL_WITH_DATA`.
- `service_types.product_reference` replaces `default_product_id`. Expense
  categories have no account (`default_expense_account_id` is in company
  settings). `expense_categories.is_subcontracting` was added.
- New enum `CapacityCheck`. The menu label lives in `lang/*/admin.php`, not a
  support enum language file.
- No `CompanyObserver`, because D12 = off.
- Operational clusters exist but stay out of the menu until their packages add
  screens (Filament hides clusters with no accessible components).

Risks:
- The super-admin `Gate::before` bypass skips policies. WP-2+ services must call
  `LogisticsAccess::ensureEnabled()` themselves.
- Shield permission rows for later resources are generated when those resources
  exist (install or `shield:generate`), not now.
- Production: deploy, then install from the Plugins page, then run the Supabase
  advisor on the new `public.logistics_*` tables (section 2a checklist).

Requests for other packages:
- WP-12: new English files added after this handoff need translating (none
  beyond the 25 so far).
- WP-2 onward: use `LogisticsHelper` (install, company, enable, disable,
  shipment) in tests, and put resources under the FQCNs already listed in
  `config/filament-shield.php`.

### WP-2 - Shipments and workflow - 2026-09-18 - Claude

Status: `review`. `--testsuite=LogisticsFeature` = **52 passed** (237 assertions).

Files created: `Services/ShipmentWorkflow.php`, `Services/ShipmentTotals.php`,
`Exceptions/InvalidShipmentTransition.php`, the `ShipmentResource` with its
General / Route & stops / Cargo / Assignment tabs, pages (List, Create, Edit,
View, ManageTimeline), actions (Confirm, Hold, Release, Cancel), the English
language file, and `tests/Feature/Shipments/{ShipmentWorkflowTest,
ShipmentResourceTest}.php`.

Reused: `LogisticsAccess` for the per-company switch, `LogisticsSequences` for
numbering, chatter via `HasChatter`/`HasLogActivity`, `LogisticsHelper` in tests.

Design notes for later packages:

- `ShipmentWorkflow` is the only place `state` changes. It calls
  `LogisticsAccess::ensureEnabled()` and `Gate::authorize()` itself, because the
  super-admin `Gate::before` bypass skips policies. Dispatch, POD and telematics
  must go through it rather than assigning `state`.
- Filament actions carry **no** policy check of their own, and
  `Action::authorize()` takes ability names, not closures. Each action therefore
  repeats the check in `visible()` while the workflow authorises server-side.
- Action names must not contain dots — Filament reads a dot as a nested action
  path. Hence `confirmShipment`, not `confirm.shipment`.
- Global search drops any result whose URL cannot be built, which needs `view`
  as well as `view_any`. The search test grants both.
- A file that runs alone needs `URL::resolveMissingNamedRoutesUsing(fn () => '#')`
  because the panel boots before the plugin is installed in tests.
- The list-page query-count test asserts per-table counts, not a total:
  `HasCustomFields` loads per record by design.

Three bugs outside this package were found and fixed while getting the suite
green. All three broke far more than Logistics; see `docs/change-log.md`
(2026-09-18) for the reasoning:

1. `MultiCompanyAdminRoleProvisioner` called `modelKeys()` on a plain support
   collection, so its migration threw on every `migrate:fresh` — every suite in
   the repo failed.
2. `User::hasPermissionTo()` denies when `is_active` is falsy, but the attribute
   is filled by a **column default**, so a model created in memory carried null
   and lost every permission. 19 Logistics tests failed on this. Fixed with a
   model-level `$attributes` default matching the schema.
3. Permission name lookups read the fork's `Webkul\Security\PermissionRegistrar`
   while every flush site cleared Spatie's — two unrelated classes, two caches.
   A permission created after the cache warmed was invisible and `can()`
   returned false. This was the last failing test, which is the only one that
   authenticates twice. Flushes now clear both.

Requests for other packages:

- WP-12: the English file `filament/clusters/operations/resources/shipment.php`
  needs translating.
- WP-4/5/6/7: call `ShipmentWorkflow`, never assign `state`.
- Recommended, not done: make `Webkul\Security\PermissionRegistrar` extend
  Spatie's and alias the container binding, so one instance serves both names.
  Not done here because `AssignRoleCommand` and `CreateRoleCommand` type-hint
  Spatie's class, so it needs a full-suite run behind it.

### WP-4 - Trips and dispatch board - 2026-09-21 - Claude

Status: `review`. `--testsuite=LogisticsFeature` = **76 passed, 348 assertions**,
zero failures, on `aureuserp_testing_claude`. Pint clean (it reformatted 15
files for import order and operator alignment after that run; style only).

That 76 includes WP-5's in-progress tests, which landed in the tree in parallel.
WP-4's own contribution is the 10 tests in `tests/Feature/Dispatch/`.

Files created:

- `src/Services/DispatchService.php`
- `src/Exceptions/{InvalidTripTransition,TripNotDispatchable}.php`
- `src/Filament/Clusters/Operations/Resources/TripResource.php` and its
  `Schemas/{TripForm,TripInfolist}.php`, `Tables/TripsTable.php`,
  `Pages/{ListTrips,CreateTrip,EditTrip,ViewTrip}.php`,
  `RelationManagers/ShipmentsRelationManager.php`
- `src/Filament/Clusters/Operations/Pages/DispatchBoard.php` and
  `resources/views/filament/pages/dispatch-board.blade.php`
- `resources/lang/en/dispatch.php`,
  `resources/lang/en/filament/clusters/operations/resources/trip.php`,
  `resources/lang/en/filament/clusters/operations/pages/dispatch-board.php`,
  new keys in `resources/lang/en/exceptions.php`
- `tests/Feature/Dispatch/DispatchServiceTest.php`

Nothing in WP-1's files changed: `config/filament-shield.php` already declared
`TripResource` with `dispatch` and `complete`, and `TripPolicy` already
implemented both.

Design notes for later packages:

- **`DispatchService` is the only place a trip's state changes, and the only
  place shipments go onto a trip.** WP-10 and WP-11 must call it rather than
  assigning `state` or attaching the relation directly. A bare attach leaves the
  shipment `CONFIRMED` and the trip without stops - a silently half-assigned
  trip. The relation manager's attach action goes through the service for
  exactly this reason.
- **Refusals and warnings are different on purpose.** No vehicle, no driver, an
  archived vehicle, an archived driver or an expired licence all block
  (`assertDispatchable()` throws). Over capacity only warns and the dispatch
  proceeds (D5). Do not "fix" the capacity case into a refusal: blocking there
  strands real deliveries over an estimate.
- **Stops take the shipment's company, never the session's.** A dispatcher
  working across companies would otherwise stamp their own company on another
  company's stop. `DispatchServiceTest` asserts both halves: invisible through
  the scope, correct in the row.
- `DispatchBoard::canAccess()` requires **both** `page_logistics_dispatch_board`
  and `view_any_logistics_shipment`. The page permission alone would leak
  customer names and destinations to a user denied the shipment list. WP-10
  widgets over the same data should follow this, not the page permission alone.
- The board is paginated tables per tab, not drag and drop: a company with
  thousands of open shipments cannot render them all.
- The vehicle and driver selects only offer **active** records of the trip's own
  company, so the form and the service agree on what can be dispatched.
- `company_id` is locked after creation (`disabledOn('edit')`): the vehicle,
  driver and stops are all scoped to it.

Requests for other packages:

- WP-12: translate `resources/lang/en/dispatch.php`,
  `.../resources/trip.php`, `.../pages/dispatch-board.php`, and the new
  `exceptions.php` keys (`invalid-trip-transition`, `trip-*`).
- WP-5: `DeliveryService` should move stop actual times through the same stops
  WP-4 creates; one pickup and one delivery stop per shipment, ordered by
  `sequence`.

### WP-5 - Delivery and proof of delivery - 2026-09-22 - Codex, finished by Claude

Status: `review`. `--testsuite=LogisticsFeature` = **88 passed, 408 assertions**
on `aureuserp_testing_claude`. Pint clean.

Codex built the package and stopped without integrating it. Claude wired it in
and closed one test gap; the code below is Codex's unless noted.

Files created (Codex): `src/Services/DeliveryService.php` (with the `PodData`
readonly DTO in the same file), the Pickup/Deliver/FailDelivery actions,
`DeliveryProofsRelationManager`, `resources/lang/en/delivery.php`,
`tests/Feature/Delivery/DeliveryServiceTest.php`.

Finished by Claude:

- The three actions are registered on `ViewShipment` in workflow order, and
  `DeliveryProofsRelationManager` is first in `ShipmentResource::getRelations()`.
  Both are WP-2 files, which is why Codex was told to request the change rather
  than make it.
- `DeliveryProofsRelationManager::objectKey()` extracted and made public, so the
  test derives the storage key from the same code the UI links with. It
  previously hand-built `companies/{id}/{path}`, omitting the `root` prefix
  `fileUrl()` adds - the two could drift apart with every test still passing.

Design notes for later packages:

- `DeliveryService` is the only place delivery state changes, and every
  transition goes through `ShipmentWorkflow`. It re-reads the shipment under the
  company scope before writing, which is what stops a model held from before the
  active company changed being acted on. WP-7 adopted the same guard; **any
  service taking a model and acting on it needs it**, because
  `LogisticsPolicy::recordAbility()` grants on permission plus the per-company
  switch and never checks that the record is one the user can see.
- POD files go on the `public` disk, which resolves to `tenant-s3` in
  production. `withShipmentDisk()` re-resolves that disk under the shipment's
  company so the object lands in the right tenant prefix. Do not build paths
  with `storage_path()`.

**Known limitation, not covered by tests.** `phpunit.xml` does not set
`FILESYSTEM_PUBLIC_DRIVER`, so every test runs the `local` driver. The
`tenant-s3` branch of `fileUrl()` and the whole of `withShipmentDisk()`'s
tenant handling therefore never execute under test, and the `tenant-s3` driver
builds a real S3 driver so it cannot be faked cheaply. `objectKey()` is now
locked by a test; the driver behaviour around it is not. Closing this properly
means either a test that sets the driver with a fake S3, or accepting it as a
manual pre-deploy check. Recorded in `docs/upstream-fix-adoption-plan.md`
terms: this is a coverage gap, not a known defect.

Requests for other packages:

- WP-12: translate `resources/lang/en/delivery.php`.
- WP-5b (optional, D15): the stop link for POD capture is still `todo`.

### WP-7 - Charges and shipment invoicing - 2026-09-22 - Claude

Status: `review`. `--testsuite=LogisticsFeature` = **95 passed, 430
assertions** (88 when the service landed, plus the 7 UI tests below);
`--testsuite=AccountFeature` = **521 passed, 1382 assertions**, identical to
the WP-1 baseline, so nothing in accounting moved. Pint clean.

Files created: `src/Services/ShipmentInvoicer.php`,
`src/Exceptions/NothingToInvoice.php`, `ChargesRelationManager`,
`InvoicesRelationManager`, `CreateInvoiceAction`,
`src/Filament/Clusters/Finance/Pages/UnbilledCharges.php` and its view,
`resources/lang/en/invoicing.php`, new `exceptions.php` key,
`tests/Feature/Invoicing/ShipmentInvoicerTest.php` (8 tests).

Design notes for later packages:

- **Logistics does not own invoices.** `ShipmentInvoicer` creates the same
  `Account\Models\Move` that Sales creates and hands it to
  `AccountFacade::computeAccountMove()` for totals and taxes. Numbering,
  journals, payment state and posting all stay in Accounting. WP-8b must follow
  the same rule for vendor bills.
- Each invoiced charge keeps its `move_line_id`, which is the whole mechanism
  behind "a second invoice picks up only new charges"
  (`ShipmentCharge::scopeUninvoiced`).
- Waiting time is split into `waitingTimeSuggestions()`, which creates nothing,
  and `addWaitingTimeCharge()`, which a confirmed suggestion calls. D14 requires
  a person to confirm detention; a long wait is often the carrier's own fault,
  so auto-billing it would put invented charges on real customer invoices.
- `UnbilledCharges` requires **both** `page_logistics_unbilled_charges` and
  `view_financials_logistics_shipment`, as `DispatchBoard` requires both of its
  permissions. The page permission alone would expose charge amounts and
  customer names to a user denied shipment financials.

Two bugs found in the hand-off to accounting, both fixed here:

1. `Move` computes currency before journal in its saving hook, so a move created
   with neither dereferences a null journal. Sales never hits this because
   orders always carry a currency; shipments do not. The invoicer now supplies
   `$shipment->currency_id ?? $shipment->company?->currency_id` and lets
   accounting pick its own journal.
2. The scoped re-read originally ran after the charges were loaded, so
   invoicing another company's shipment reported "nothing to invoice" instead of
   refusing it. The guard now runs first.

Two ways to silently under-bill a customer, both closed in the UI and worth
knowing about:

- A charge with **no product** produces a move line with no account, which
  accounting treats as a non-product line and computes **untaxed**, with no
  error. `ChargesRelationManager` marks the product `required()`.
- A tax with **no repartition lines** contributes nothing, also silently. The
  tax select only offers taxes with `invoiceRepartitionLines` for that company.

UI coverage: `tests/Feature/Invoicing/InvoicingScreensTest.php` covers the
invoice action (visible with the permission, hidden without, creating a real
invoice through the page, and reporting "nothing to invoice" as a notification
rather than an error), plus `UnbilledCharges` (the permission pairing asserted
all three ways, the uninvoiced-only filter, and company isolation).

Writing those found a crash in `UnbilledCharges` that the service tests could
never have caught: the currency filter used `->relationship('currency', 'code')`,
but **`currencies` has no `code` column** - `Currency::code` is an accessor over
`name`. The filter pushed it into SQL and the page threw `Undefined column` on
every open. The `money()` column may still use `->code` because that evaluates
in PHP. The two usages look identical; only one is safe.

Still not covered: the two relation managers have no tests of their own.

Requests for other packages:

- WP-12: translate `resources/lang/en/invoicing.php` and the new
  `exceptions.php` key (`nothing-to-invoice`).
- WP-8b: reuse `ShipmentInvoicer`'s shape for vendor bills - same delegation to
  Accounting, same scoped re-read guard.

### WP-8a - Expense records and approval - 2026-09-23 - Copilot

Status: `in progress`. The package is claimed and the dedicated test database
is `aureuserp_testing_wp8a`.

Implemented `ExpenseApproval` with submit, approve and reject methods. Each
transition re-reads the expense through the company scope with a row lock before
loading or changing related data, calls `LogisticsAccess::ensureEnabled()`,
re-checks the policy ability, validates `ExpenseState` transitions, and records
the approver and timestamp for approvals. The new Finance resource uses the
required split `Schemas/`, `Tables/` and `Pages/` layout. Receipt uploads use
`Storage::disk('public')` through Filament, generated filenames, MIME
allowlisting and a 10 MB limit; category `requires_receipt` is evaluated per
selected category.

Tests added in `tests/Feature/Expenses/ApprovalTest.php` cover the approval and
rejection flows, missing approval permission, company isolation and conditional
receipt validation. Sail PHP syntax checks passed, and Pint passed for all nine
new PHP files. The focused Pest run was attempted on
`aureuserp_testing_wp8a`, but the existing install bootstrap stalled in
`shield:generate` before assertions; the database was reset and no test count
is claimed.

Requests: none.

### WP-9 - Sales quotation link - 2026-09-23 - Claude

Status: `review`. Test database `aureuserp_testing_wp9`.

Files created:
- `plugins/webkul/logistics/tests/Feature/Sales/ShipmentFromOrderTest.php`

Files modified:
- `plugins/webkul/logistics/src/Services/ShipmentFromOrder.php`
- `plugins/webkul/logistics/src/LogisticsServiceProvider.php`
- `plugins/webkul/logistics/src/Models/Shipment.php`
- `docs/logistics-plan.md`, `docs/change-log.md`

(`src/Services/ShipmentFromOrder.php` and `src/Listeners/CreateShipmentFromOrder.php`
were committed earlier in 09037a641; this block covers the whole package.)

Migrations / tables:
- None. `logistics_shipments.sale_order_id` already exists from WP-1, as a plain
  indexed column rather than a foreign key, precisely so Logistics installs on a
  deployment that has no Sales.

Reused components:
- `Webkul\Sale\Events\OrderConfirmed`, already dispatched by
  `OrderWorkflow::confirm()`. **No Sales file was edited.**
- `LogisticsAccess::enabledFor()` for the per-company switch.
- `InheritsParentCompany` on the charges, so each charge takes the shipment's
  company rather than the session's.

Tests added / results:
- 10 tests in `tests/Feature/Sales/ShipmentFromOrderTest.php`.
- Focused run (`--filter=ShipmentFromOrder`, db `aureuserp_testing_wp9`):
  **9 passed, 1 failed** before the model fix below. After the fix, all 10 pass
  as part of the full run.
- Pint on the four WP-9 files: passed.

Existing suites run / results:
- Full `LogisticsFeature` on a freshly recreated `aureuserp_testing_wp9`
  (an earlier run was interrupted, so the database was dropped and recreated
  first): **116 passed, 1 failed, 483 assertions, 2488 s**.
- The single failure is **WP-8a's**, not WP-9's:
  `tests/Feature/Expenses/ApprovalTest.php:97` fails with "Attempt to read
  property `form` on null" - the Livewire component never mounted. Cause:
  Filament's `Resource::canAccess()` returns `canViewAny()`
  (`vendor/filament/filament/src/Resources/Resource/Concerns/HasAuthorization.php:28`),
  so `CreateExpense` needs `view_any_logistics_expense` as well as
  `create_logistics_expense`. Reported to WP-8a's owner; not changed here.
- Everything WP-9 could plausibly have broken passed, including the whole
  `Shipments` group - the group most exposed to the `Shipment::$attributes`
  change below.

Decisions and deviations from the plan:
- **The listener registers unconditionally**, rather than being guarded by
  `Package::isPluginInstalled('sales')` or `class_exists()`. Install is global,
  so neither question is the one that matters; `isPluginInstalled()` queries the
  database at register time, which is what made `package:discover` hang for
  300 s; and every plugin's classes are autoloadable in this monorepo whether or
  not the plugin is installed. The decision lives once, in
  `ShipmentFromOrder::shouldConvert()`, where a test can reach it.
- **The "no backfill" rule is enforced by when the code runs, not by a date
  comparison.** The plan's wording is "orders *confirmed* before the company was
  enabled", and the listener only fires on confirmation, so the switch read at
  that moment is the whole rule. An earlier draft compared `date_order`, which
  wrongly blocked a quotation drafted before adoption and confirmed after it -
  exactly the open work a company adopting Logistics has in hand. Orders already
  confirmed before adoption are still converted deliberately, through the "From
  sales order" picker (WP-2).
- **`serviceLines()` reads the order's lines with global scopes removed**, filtered
  by `order_id`, the same way `existingShipment()` does. `OrderLine` is
  company-scoped, and an event fires under whoever acted; a queued or console
  confirmation under a different active company would otherwise have found no
  lines and silently skipped the shipment.

Bug found and fixed while testing (root cause, not the assertion):
- `logistics_shipments` fills `state`, `transport_mode`, `priority` and eight
  other columns by database default, and `Shipment` declared no `$attributes`.
  A freshly created shipment therefore carried `null` for all of them in memory
  until reloaded, so `ShipmentFromOrder::convert()` returned a shipment whose
  `state` was null rather than `DRAFT`, and `->state->value` on it would have
  been a fatal error. This is the same trap as `users.is_active` in project
  memory; the fix is the same, `protected $attributes` mirroring the migration.

Risks:
- The suite now installs the Sales plugin (`TestBootstrapHelper::ensurePluginInstalled('sales')`),
  which the first Sales test pays for: 496 s on the clean run, 507 s and 1212 s
  on earlier ones. That is roughly a fifth of the suite's 2488 s, on top of the
  515 s the first test already pays for the base install. Paid once per suite,
  but worth revisiting in WP-13 if suite time becomes a problem.
- `Shipment::$attributes` must be kept in step with the shipments migration. The
  docblock says so; a column added later with a default and not listed there
  reintroduces the same null-in-memory bug.
- The same gap exists on `Trip`, `Expense`, `Stop`, `ShipmentCharge`, `Vehicle`
  and `Driver`, none of which declare `$attributes`. Not changed here - it is
  outside WP-9 and each needs its own test run. Recommended for WP-13.

Requests for other packages:
- **WP-2 / WP-13:** `ShipmentResource::saleOrderOptions()` lists every order of
  the customer although its docblock says "Confirmed sales orders", and it does
  not exclude orders that already have a shipment. The listener cannot create a
  duplicate, because `existingShipment()` sees the `sale_order_id` the picker
  sets, but a user can still hand-create a second shipment for one order through
  the picker. Filter the options by confirmed state and by
  `whereDoesntHave`/`whereNotIn` on existing shipments.
- **WP-12:** nothing. WP-9 adds no user-facing strings.

### WP-8a - Expense records and approval - completion - 2026-09-23 - Claude

Status: `review`. Test database `aureuserp_testing_wp8a` (dropped and recreated;
the previous owner's run had stalled on it).

This block completes the `in progress` block above rather than replacing it.
That block's own summary of what it built still stands; what follows is the
verification it could not run, plus two defects that verification exposed.
WP-8a's owner became unavailable, so the user asked for it to be finished here.

Files modified:
- `plugins/webkul/logistics/src/Services/ExpenseApproval.php`
- `plugins/webkul/logistics/src/Filament/Clusters/Finance/Resources/ExpenseResource/Pages/ViewExpense.php`
- `plugins/webkul/logistics/tests/Feature/Expenses/ApprovalTest.php`
- `plugins/webkul/logistics/resources/lang/en/exceptions.php`
- `plugins/webkul/logistics/resources/lang/en/filament/clusters/finance/resources/expense.php`

Files created:
- `plugins/webkul/logistics/src/Exceptions/ReceiptRequired.php`

Defect 1 - the approval test could never have passed.
`ApprovalTest`'s form test granted only `create_logistics_expense`. Filament's
`Resource::canAccess()` returns `canViewAny()`
(`vendor/filament/filament/src/Resources/Resource/Concerns/HasAuthorization.php:28`),
so `CreateExpense` never mounted and every form assertion failed on a null
component - "Attempt to read property `form` on null" - rather than on the form.
Added `view_any_logistics_expense`, with a comment saying why.

Defect 2 - `requires_receipt` was enforced only by the form.
`ExpenseForm` marks the upload required when the category demands it, but the
form is not the boundary: an API write, an import or a direct service call
walked an unevidenced expense through to `approved`, which is where the company
agrees to pay it. `ExpenseApproval` now refuses the move to `submitted` **and**
to `approved`:
- Both points, because an expense can reach `submitted` without passing through
  `submit()` - a seeded record, an import, an API write.
- Rejection is deliberately not blocked; a missing receipt is often the reason
  for rejecting.
- The category is read `withoutGlobalScopes()` by key. Through the relation, a
  queued job or console command running outside the expense's company would see
  no category and read that as "no receipt needed" - the rule failing open.
- Refusal is a `ReceiptRequired` exception following the `NothingToInvoice`
  pattern, caught in `ViewExpense` and shown as a warning notification naming
  the category, not an error page.

Defect 3 - a test that asserted nothing.
The receipt test read `FileUpload::make('receipt_path')->getAcceptedFileTypes()`
on a freshly constructed component, which carries none of the resource's
configuration and returns null. It now asserts against the component the page
actually mounts, via `assertSchemaComponentExists`, covering the MIME
allowlist, the 10 MB cap and the `public` disk - all three claimed in the block
above and none previously tested.

Tests added / results:
- Four new tests: refusal on submit, refusal on approve for an expense that
  never passed through `submit()`, success once a receipt is attached, and
  rejection still going through without one.
- `ApprovalTest` now 9 tests. Pint passed on every changed file.

Existing suites run / results:
- Full `LogisticsFeature` on `aureuserp_testing_wp8a`:
  **121 passed, 0 failed, 500 assertions, 2161 s.** No failures anywhere in the
  plugin.

Risks:
- `Shipment::$attributes` aside (see WP-9), the `logistics_expenses` defaults
  have the same null-in-memory gap: `Expense` declares no `$attributes`.
  Not changed here. WP-13.

Requests for other packages:
- **WP-8b:** the "at least one of shipment, trip or vehicle" rule is form-only,
  like `requires_receipt` was. It is a data-shape rule rather than an evidence
  control, so it was left alone here, but WP-8b needs every expense to point at
  something billable before it can post a vendor bill. Enforce it where the bill
  is built, or in `ExpenseApproval` alongside the receipt check.

### WP-10 - Dashboard widgets - 2026-09-24 - Claude

Status: `review`. Test database `aureuserp_testing_wp10`.

Files created:
- `plugins/webkul/logistics/src/Filament/Pages/Dashboard.php`
- `plugins/webkul/logistics/resources/lang/en/filament/pages/dashboard.php`

Files modified:
- `plugins/webkul/logistics/tests/Feature/Dashboard/WidgetsTest.php`
- `docs/logistics-plan.md`, `docs/change-log.md`, `AGENTS.md`

(The three widgets and their seven tests were built earlier on this branch;
this block covers the whole package.)

Migrations / tables:
- None.

Reused components:
- `Webkul\Project\Filament\Pages\Dashboard` as the pattern: `BaseDashboard`,
  `HasPageShield`, `$routePath`, `NavigationGroup::Dashboard`, `getWidgets()`.
- `LogisticsAccess::enabledForCurrent()` for the per-company switch.
- Each widget's own `canView()`, so the page never decides which widgets a
  given user may see - the finance widget hides itself from anyone without
  `view_financials`, here as on the main dashboard.

Steps against the spec:
- Stat widgets: created today, awaiting pickup, in transit, out for delivery,
  delivered today, overdue, failed delivery (ShipmentStatsWidget); vehicles and
  drivers available, on the road, planned trips (FleetStatsWidget).
- Finance widget behind `view_financials_logistics_shipment`
  (UnbilledRevenueWidget), totalled per currency rather than summed across
  currencies, which would be a meaningless figure that looks authoritative.
- One grouped query per widget, asserted by a test that counts the statements
  a widget issues.
- The page itself, which was the missing part: the widgets are discovered by
  the plugin, so they also reach the panel's main dashboard, but a company that
  runs logistics wants them together in one place.

Tests added / results:
- Four new tests for the page: it opens for a permitted user of an enabled
  company, it is forbidden without `page_logistics_dashboard`, it is forbidden
  for a company that has not enabled Logistics, and it lists its three widgets.
- `tests/Feature/Dashboard/WidgetsTest.php` now 11 tests, all passing.
- Pint passed on every changed file.

Existing suites run / results:
- Full `LogisticsFeature` on `aureuserp_testing_wp10`:
  **125 passed, 0 failed, 506 assertions, 2625 s.** That is the 121 of WP-8a
  plus these four. Wall time was far longer because Docker was frozen across
  the machine's sleep; Pest's own 2625 s is the run.

Bug found and fixed during the package (mine, and a security hole):
- The first version of `Dashboard::canAccess()` returned
  `parent::canAccess() && LogisticsAccess::enabledForCurrent()`. A `canAccess()`
  written on the class **replaces** the one `HasPageShield` provides, because a
  class method beats a trait method, so `parent::` reached Filament's
  `Page::canAccess()`, which returns true. The page permission was never checked
  and the dashboard opened for any authenticated user of an enabled company.
  The test written for exactly that case caught it. Fixed by checking the
  permission explicitly, as `UnbilledCharges` already does. A grep over the
  whole repository confirms no other page combines `HasPageShield` with its own
  `canAccess()`, so this was the only instance. Recorded in `AGENTS.md`.

Risks:
- The widgets appear on the panel's main dashboard as well as on this page,
  because `LogisticsPlugin` discovers them. That is what the Projects plugin
  does too, and each widget guards itself with the switch and a permission, so
  a company without Logistics sees nothing. Change it only if the main
  dashboard becomes crowded.

Requests for other packages:
- **WP-11:** the `Reporting` cluster already exists and is excluded from Shield
  page permissions in `config/filament-shield.php`; reports belong there rather
  than on this dashboard.
- **WP-12:** translate `resources/lang/en/filament/pages/dashboard.php` and the
  three widget files in `resources/lang/en/filament/widgets/`.

### WP-5b - Stop link for POD capture - 2026-09-24 - Claude

Status: `review`. **The security review this package's spec requires has not
been done.** It is the only public entry point in the plugin, and the build
below should be what that review examines, not a substitute for it.
Test database `aureuserp_testing_wp5b`.

Files created:
- `plugins/webkul/logistics/src/Services/StopLinkService.php`
- `plugins/webkul/logistics/src/Http/Controllers/StopLinkController.php`
- `plugins/webkul/logistics/src/Exceptions/StopLinkUnavailable.php`
- `plugins/webkul/logistics/routes/web.php`
- `plugins/webkul/logistics/resources/views/stop-link/{layout,show,done,expired}.blade.php`
- `plugins/webkul/logistics/resources/lang/en/stop-link.php`
- `.../ShipmentResource/Actions/SendStopLinkAction.php`
- `plugins/webkul/logistics/tests/Feature/StopLinks/StopLinkTest.php`

Files modified:
- `plugins/webkul/logistics/src/LogisticsServiceProvider.php` (hasRoutes, rate limiter)
- `.../ShipmentResource/Pages/ViewShipment.php` (the action)
- `plugins/webkul/support/tests/Helpers/TestBootstrapHelper.php` (see below)

Migrations / tables:
- None. `logistics_stop_links` was created in WP-1.

How an unauthenticated request is authorised (the central decision):
- The token is the credential. `capture()` exchanges it for the identity of the
  dispatcher who issued the link, through `Auth::guard('web')->onceUsingId()`,
  which does not touch the session.
- **Nothing in `DeliveryService` is relaxed.** Its `ensureEnabled()`, both
  `Gate::authorize()` calls, the company scope and the POD validation run
  exactly as they do for a user in the panel.
- The alternative - a "skip the checks when there is no user" path through
  `DeliveryService` - was rejected: it would leave a second, weaker way into the
  same service for the next caller to find. This way also gives the right
  failure modes, since deactivating the dispatcher or removing their
  `capture_pod` permission kills their outstanding links.
- The guard is named rather than taken from `Auth::`'s default on purpose. Which
  guard is default depends on what ran earlier in the process, and the API's
  token guard has no `onceUsingId()` at all - that is a real
  `BadMethodCallException`, which the test run produced before the fix.

Other decisions:
- **Re-issuing revokes any earlier unused link for the stop.** Not in the spec.
  Without it a shipment accumulates live URLs, and one already sent or leaked
  stays valid.
- **One exception for all four refusal reasons** (unknown, expired, used,
  revoked), carrying no detail. Saying which one tells a caller whether a token
  ever existed, turning the endpoint into an oracle for guessing.
- **The link is consumed only on success**, inside the transaction that locks
  its row. A rejected photo leaves it usable, so a driver is not locked out at
  a customer's gate.
- **Middleware is declared in the route file.** `PackageServiceProvider` loads
  plugin web routes with a bare `loadRoutesFrom()`, which applies no group at
  all: without this the form would have had no session and therefore no CSRF
  protection, and no throttling on an unauthenticated upload endpoint.
- The rate limiter (20/min per IP) is registered in the plugin's provider, not
  `AppServiceProvider`, so it goes away with the plugin.
- The page is `noindex` and `no-referrer`, and its CSS is inline: it opens on a
  driver's phone and must not depend on the admin asset pipeline.

Bug found in shared test infrastructure:
- `TestBootstrapHelper::loadPluginRoutes()` only ever loaded `routes/api.php`.
  No plugin had web routes before, so every `route()` call in a test threw
  `RouteNotFoundException`. It now loads both files. This affects every suite,
  so `SupportFeature` was run as well as `LogisticsFeature`.

Tests added / results:
- 15 tests in `tests/Feature/StopLinks/StopLinkTest.php`: the token stored only
  as a hash, refusal without `send_pod_link`, refusal for another company's
  stop, re-issue revoking the earlier link, the page opening with no login,
  identical 404s for all four refusal reasons, nothing leaked on the refusal
  page, capture marking the link used, no session left behind, single use, the
  link surviving a rejected submission, files under the right company prefix,
  a canvas signature accepted as a real image, junk in the signature field
  ignored rather than blocking, and the throttle.
- **15 passed, 45 assertions.** Pint passed on every changed file.

Existing suites run / results:
- Full `LogisticsFeature` on `aureuserp_testing_wp5b`:
  **140 passed, 0 failed, 551 assertions** (125 from WP-10 plus these 15).

Risks for the security review to weigh:
- **The token travels in the URL path**, so it can reach web-server access logs,
  proxy logs and browser history. Mitigated by single use, the TTL, the
  `no-referrer` meta and revocation on re-issue, but not eliminated. Moving it
  to a POST body or a fragment would break the "send a link by WhatsApp" use
  case the decision is built on.
- The capture runs as the issuing dispatcher, so the delivery is attributed to
  them (`captured_by_id`). `captured_via = stop_link` records that it came from
  the field, but a reviewer should confirm that attribution is what the business
  wants on an audit trail.
- 20 requests a minute per IP may be tight where a fleet shares one mobile NAT
  address.

Requests for other packages:
- **WP-12:** translate `resources/lang/en/stop-link.php`.
- **WP-13:** the security review, before this leaves `review`.

### WP-5b - security review and follow-up decisions - 2026-09-24 - Claude

The review WP-5b's spec requires, plus the four decisions the user made on the
risks it raised. Done by the same agent that wrote the package, which the user
chose knowingly; an independent pass would still be worth having before release.

#### Decisions (user, 2026-09-24)

| Risk | Decision |
| --- | --- |
| POD attributed to the dispatcher, driver not identified | Record the driver **and** ask for the recipient's ID, both optional per company |
| Token travels in the URL | Accept, but let each company choose the lifetime |
| Throttle keyed on IP, shared by drivers behind carrier NAT | Key on the **token**, keep per-IP as a secondary abuse layer, make it configurable per tenant, answer with 429 + Retry-After |
| Who reviews | A fresh adversarial pass by the authoring agent |

#### What the decisions changed

- `logistics_company_settings` gains `capture_driver_on_pod`,
  `capture_recipient_id` and `stop_link_rate_limit`;
  `logistics_delivery_proofs` gains `driver_id` and `recipient_id_reference`
  (migration `..._000020`, added rather than folded into WP-1's, which have
  already run). Every option is off by default, so an existing company's proofs
  are unchanged.
- The driver is taken from the **stop's trip**, never from the request: whoever
  holds the link is not necessarily the driver, and a field they could fill in
  would be a claim rather than a record. A test posts a forged `driver_id` and
  asserts it is ignored.
- The recipient ID is required by `DeliveryService` when the company asks for
  one, not only by the page; and when the option is **off**, a crafted post
  carrying the field stores nothing. Both directions are tested. The column is
  free text named `recipient_id_reference`, deliberately not ID-specific: what
  counts as identification differs by country and this must not become a
  national-ID field by assumption. It is personal data; the setting says to
  collect it only where there is a reason to.
- The link lifetime is a select of 4/8/16/24/48 hours, replacing a 1-168
  free-text field. A credential's lifetime should be chosen from considered
  options, not typed.
- The throttle applies two limits at once: per token (primary, per-tenant
  configurable, default 12/min) and per IP (secondary, 120/min). The token is
  **hashed into the cache key** - keying on the plaintext would put a live
  credential into the cache store. Laravel's throttle supplies 429 and
  Retry-After, asserted by a test.

#### Findings from the adversarial pass, all fixed

1. **The signature temp file was never deleted.** `signatureFile()` decodes to
   `tempnam()` and storing copies rather than moves, so every capture with a
   signature left a file behind - unbounded growth driven by an unauthenticated
   endpoint. Now removed in a `finally`, so a rejected submission cleans up too.
2. **Revocation existed but was unreachable.** `revoke()` was never called;
   only issuing a replacement killed a link. That is wrong for the case that
   matters - a URL sent to the wrong number should be able to die rather than be
   replaced. Added `revokeForShipment()`, `hasLiveLink()` and a
   `RevokeStopLinkAction` shown only while there is something to cancel.
3. **A shipment leaving OUT_FOR_DELIVERY gave the driver a 500.** Link issued,
   shipment then held, cancelled or delivered by someone else, driver submits:
   `InvalidShipmentTransition` is a plain RuntimeException with no status, and
   the app's generic handler only answers JSON requests. An ordinary sequence
   produced a server error for the driver and a false Sentry alert. It is now
   reported as the same refusal as an expired link - which is also the right
   answer for an unauthenticated caller, since whether a shipment was cancelled
   is not theirs to learn. The link is not spent, so it works again if the
   shipment goes back out.
4. **`CompanySetting` had no `$attributes`.** `forCompany()` returns an unsaved
   instance for a company with no row, so every default-bearing column read as
   null: `require_pod_for_delivery` was falsy although the schema defaults it to
   true, quietly dropping a delivery control. Same trap as `users.is_active` and
   `logistics_shipments.state`.

#### Checked and found sound

Token entropy (64 chars of CSPRNG) and hash-only lookup; uniform refusals with
no oracle, asserted for all four reasons and for leaking neither shipment nor
company on the refusal page; CSRF genuinely enforced (the route declares the
`web` group and nothing exempts these paths); `StopLinkUnavailable::render()`
correctly taking precedence over the application's global 404 callback
(`method_exists($e, 'render')` is checked first, and that callback answers only
JSON requests); replay and concurrent submission (row lock inside the
transaction, consumed only on success); the cache key holding a hash rather than
a live token; signature polyglot and SVG upload (only a PNG data URL is accepted
and the result is re-validated by content); proof files landing under the
shipment's own company prefix.

#### Residual risks, accepted and recorded

- **The token is in the URL path**, so it reaches web-server and proxy logs and
  browser history, and anyone the message is forwarded to can complete the
  delivery. Single use, the chosen TTL, `no-referrer` and revoke-on-reissue
  reduce this; they do not remove it. Worth scrubbing these paths from log
  retention at the edge.
- **A forwarded link exposes** the shipment reference and the stop's contact
  name. The driver needs both, but it is PII reaching whoever holds the URL.
- **`rateLimitFor()` costs one indexed query per request**, including for tokens
  that do not exist - mild amplification, bounded by the per-IP limit.
- **The TTL select will not show a pre-existing out-of-range value** (the old
  field allowed 1-168). Unreachable in practice: Logistics is not released.
