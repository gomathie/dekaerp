# Inventory — Improvement Plan (Odoo / ERPNext comparison)

Status: **decisions accepted (user, 2026-11-11); not started.** The user asked what Inventory
can adapt from Odoo or Frappe/ERPNext. Most customers come from another ERP
(see `docs/logistics-plan.md` §6a), so gaps they notice first come first.

Read `AGENTS.md` first. Inventory is shared, high-traffic code: every change
needs InventoryFeature, AccountFeature and the Sales/Purchases suites green.

---

## 1. What DEKA Inventory already has (keep)

Inventory is modelled on Odoo and is already broad:

| Area | DEKA has |
| --- | --- |
| Structure | Warehouses, locations (with types and sub-locations), storage categories and capacities, routes and rules, putaway rules, per-company location provisioning |
| Operations | Receipts, deliveries, internal transfers, dropship, operation types, 1/2/3-step reception and delivery, backorders, scrap |
| Traceability | Lots and serials (`ProductTracking`), packages, package types and levels, packagings, expiration time on products |
| Planning | Reorder points (`OrderPoint`), replenishment screen, procurement groups, push/pull rules, make-to-order |
| Counting | Counted quantity and difference on stock quantities (`counted_quantity`, `inventory_diff_quantity`) — a physical count exists |
| Reporting | Moves analysis and stock quantities (Reporting cluster) |
| API | v1 API for products, lots, quantities, operations |
| Barcode | Separate barcode app |

## 2. Gaps against Odoo Inventory and ERPNext Stock

From knowledge of Odoo 17–19 Inventory and ERPNext v15 Stock, not re-read
this session; verify specifics before building.

| # | Odoo / ERPNext has | DEKA today | Value to customers | Size |
| --- | --- | --- | --- | --- |
| I1 | **Stock valuation**: FIFO or average cost per product category; valuation layers; automatic journal entries on receipt and delivery (Odoo); moving average / FIFO with a Stock Ledger and stock-in-hand account (ERPNext) | No valuation layers, no inventory value, no stock journal entries | Highest: finance cannot see stock value or cost of goods sold; customers from both ERPs expect it | Large |
| I2 | **Landed costs**: freight, duty and clearing added to item cost (Odoo Landed Costs, ERPNext Landed Cost Voucher) | None | High for importers (Ghana, Zambia, Congo); links to Logistics expenses | Medium (needs I1) |
| I3 | **Inventory dashboard**: operation cards (to receive, to deliver, late, backorders) | No dashboard or widgets in Inventory | High; also a section on the combined Overview | Small |
| I4 | **Stock reports**: stock ageing, valuation, forecasted / projected quantity, stock balance per warehouse, slow movers | Only moves and quantities | High | Medium (ageing/forecast small; valuation needs I1) |
| I5 | **Physical count workflow**: count sheets per location, assign counters, apply all differences at once, scheduled cycle counts by ABC class (Odoo); Stock Reconciliation (ERPNext) | Counted quantity per line, no count session or schedule | Medium-high | Medium |
| I6 | **Batch / wave picking**: group deliveries into one picking round | None | Medium (warehouses with many small orders); overlaps Logistics WP-17 trip from several deliveries | Medium |
| I7 | **Lot dates**: best-before, removal and alert dates per lot, FEFO removal strategy, expiry alerts | Expiration time on product only | High for food, pharma, agro clients | Small-medium |
| I8 | **Material request** (ERPNext): internal request for stock that becomes a transfer or purchase | None (reorder points only) | Medium; common for clients from ERPNext | Medium |
| I9 | **Quality checks** on receipt (Odoo Quality, ERPNext Quality Inspection) | None | Medium; optional per company | Medium |
| I10 | **Pick list / delivery slip PDF** with lots and locations | Check existing prints | Medium | Small |
| I11 | **Serial/batch import** at receipt (paste or file) | Check | Medium | Small |
| I12 | **Consignment / customer-owned stock** (Odoo owner on quants) | None | Low | Medium |

## 3. Recommended order

1. **I3** dashboard (small, visible) and **I4** ageing and forecast reports.
2. **I7** lot dates with FEFO and expiry alerts.
3. **I1** stock valuation, then **I2** landed costs: the largest and most
   valuable; needs Accounting design decisions (accounts per category,
   real-time vs periodic) and the `display_type` / journal rules in
   AGENTS.md.
4. **I5** count sessions and cycle counts.
5. **I8** material requests, **I6** batch picking (coordinate with
   Logistics WP-17).
6. **I9** quality, **I10–I12** as clients ask.

## 4. Rules any package must keep

- Per-company: every new row stamps the record's company
  (`InheritsParentCompany` from its parent), read parents scope-free.
- Module switch (`PluginEntitlement` for `inventories`) on pages, widgets,
  policies, API and services.
- A dashboard section on the combined Overview goes through
  `OverviewRegistry`; mobile rules from 2026-11-11 apply (two stats per row,
  lists hidden on phones).
- Valuation entries follow the AGENTS.md `Move` rules (explicit
  `display_type`, never count balancing lines as yours).

## 5. Decisions needed (user)

| ID | Question | Answer (user, 2026-11-11: recommendations accepted) |
| --- | --- | --- |
| INV-D1 | Order above? | **Accepted:** Yes: I3/I4, I7, I1/I2, I5, I8/I6, then the rest |
| INV-D2 | Valuation method default | **Accepted:** Average cost (AVCO) per category, FIFO selectable; matches both Odoo and ERPNext defaults for most SMEs |
| INV-D3 | Valuation entries | **Accepted:** Real-time (automatic journal entries) per company setting, periodic as the alternative |
| INV-D4 | Quality checks | **Accepted:** Later, as an optional module |
| INV-D5 | Material requests in Inventory or Purchases? | **Accepted:** Inventory (internal request), converting to transfer or RFQ |
