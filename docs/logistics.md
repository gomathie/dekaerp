# Logistics

The Logistics plugin manages shipments, fleet assignments, delivery, proof of
delivery, customer charges, operating expenses, and operational reports. Plugin
installation is global, but each company enables and configures Logistics
separately.

## Setup

Install the plugin from **Settings > Plugins**, or run:

```bash
php artisan logistics:install
```

The installer also requires the Products, Employees, and Accounts plugins. The
deployed code remains inert until the plugin is installed.

After installation, select a company and open **Logistics > Configuration >
Settings**. Review the readiness warnings, then enable Logistics for that
company. Enabling a company creates its Logistics sequences and standard service
products. It does not enable any other company.

For the complete workflow, the company needs:

- a currency;
- sale and purchase journals;
- the standard product unit and a product category;
- a default expense account for vendor-bill posting.

Configure service types, vehicle types, package types, expense categories, POD
requirements, stop-link lifetime and rate limit, capacity warnings, waiting
time, and accounting defaults before processing live work.

Production uploads use the configured `public` disk. With
`FILESYSTEM_PUBLIC_DRIVER=tenant-s3`, POD photos, signatures, and receipts are
stored in the private tenant bucket under the owning company's prefix and are
served through the authenticated secure-storage route. Report exports use the
application queue, so a queue worker must be running.

## Using Logistics

1. Add drivers and vehicles under **Logistics > Fleet**. Drivers can be imported
   from employees, and vehicles can be imported from Maintenance when that
   plugin is installed.
2. Create a shipment manually or from a sales order. Review its customer,
   route, cargo, dates, charges, and assigned dispatcher, then confirm it.
3. Create a trip, assign confirmed shipments, select an active vehicle and an
   eligible driver, and dispatch the trip. Capacity overruns warn rather than
   block when the company uses warning mode.
4. Record pickup, transit, out-for-delivery, delivery, failure, retry, or return
   from the shipment. Required POD data is read from the shipment company's own
   settings.
5. Capture POD in the admin panel or issue a one-time stop link to the driver.
   Stop links expire, can be revoked, are rate limited, and become unusable
   after one successful submission.
6. Print the waybill when needed. Its company-specific number is allocated on
   the first print and reused on later prints.
7. Add customer charges and create an invoice. Record operating expenses,
   submit and approve them, then create draft vendor bills for approved costs.
8. Use the dashboard, dispatch board, unbilled charges page, and Reporting
   pages for operational and financial review.

Disabling Logistics under the company settings hides the module and blocks new
work for that company without deleting its data. Re-enable it to restore access.

This release does not expose Logistics through the public REST API.

## Permissions

Permissions are assigned to global roles through Filament Shield. They do not
enable Logistics for a company; the role permission and the company's switch
must both allow the operation.

Resource permissions use the following subjects:

| Area | Permission subject | Additional abilities |
| --- | --- | --- |
| Shipments | `logistics_shipment` | `confirm`, `assign`, `mark_picked_up`, `mark_delivered`, `capture_pod`, `send_pod_link`, `cancel`, `create_opening`, `create_invoice`, `view_financials` |
| Trips | `logistics_trip` | `dispatch`, `complete` |
| Drivers | `logistics_driver` | Standard view/create/update/delete/restore abilities |
| Vehicles | `logistics_vehicle` | Standard view/create/update/delete/restore abilities |
| Expenses | `logistics_expense` | `approve`, `post_bill` |
| Service types | `logistics_service::type` | Standard abilities plus `reorder` |
| Vehicle types | `logistics_vehicle::type` | Standard abilities plus `reorder` |
| Package types | `logistics_package::type` | Standard abilities plus `reorder` |
| Expense categories | `logistics_expense::category` | Standard abilities plus `reorder` |

Standard resource prefixes include `view_any`, `view`, `create`, `update`,
`delete`, and the relevant bulk-delete, restore, and force-delete variants.
For example, shipment listing is `view_any_logistics_shipment` and delivery is
`mark_delivered_logistics_shipment`.

Standalone pages have these permissions:

- `page_logistics_dashboard`
- `page_logistics_dispatch_board`
- `page_logistics_unbilled_charges`
- `page_logistics_manage_company_settings`
- `page_logistics_shipment_register`
- `page_logistics_delivery_performance`
- `page_logistics_shipment_profitability`
- `page_logistics_vehicle_trip_history`
- `page_logistics_driver_trip_history`

The dispatch board also requires shipment-list permission. Unbilled charges and
shipment profitability also require `view_financials_logistics_shipment`.

## Uninstalling

Uninstalling is different from disabling a company. It removes every Logistics
table for every company, deletes Logistics sequence rows, and removes related
chatter data. Take a verified database backup first.

```bash
php artisan logistics:uninstall
```

The uninstall is blocked while any shipment exists, including archived rows.
After backing up and intentionally accepting complete data loss, an operator can
set `LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true` and run the uninstall again.
Plugin dependency checks may also block removal while another installed plugin
depends on Logistics.
