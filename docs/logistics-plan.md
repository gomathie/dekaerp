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
| WP-3 | Vehicles and drivers | WP-1 | WP-2, WP-8a | todo | |
| WP-4 | Trips and dispatch board | WP-2, WP-3 | WP-5, WP-6, WP-7 | todo | |
| WP-5 | Delivery and POD | WP-2 | WP-4, WP-6, WP-7 | todo | |
| WP-5b | Stop link for POD capture (optional, D15) | WP-5 | WP-6, WP-7, WP-8b | todo | |
| WP-6 | Waybill and delivery-note PDF | WP-2 | WP-4, WP-5, WP-7 | todo | |
| WP-7 | Charges and shipment invoicing | WP-2 | WP-4, WP-5, WP-6, WP-8b | todo | |
| WP-8a | Expense records and approval | WP-1 | WP-2, WP-3 | todo | |
| WP-8b | Expense and carrier bills | WP-8a, WP-2 | WP-7 | todo | |
| WP-9 | Sales quotation link (per D1) | WP-7 | WP-10 | todo | |
| WP-9b | Customer page integration (per D11) | WP-7 | WP-10 | todo (extension point only, else ask) | |
| WP-10 | Dashboard widgets | WP-4, WP-5 | WP-9, WP-11 | todo | |
| WP-11 | Reports | WP-4, WP-5, WP-7, WP-8b | WP-10 | todo | |
| WP-12 | Translations ar/es/fr/pt_BR | each finished package | anything | review (enums + foundation; rest waits for other packages) | Codex 2026-09-17 |
| WP-13 | Hardening and release | all | — (runs alone) | todo | |

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
