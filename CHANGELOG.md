<!--
Authoring notes (not shown on the What's New page, which strips HTML comments):
- Write for the people who use DEKA, not for developers. Technical detail
  belongs in docs/change-log.md.
- New entries go under "## Unreleased" (hidden from users). Move them under the
  version heading in the same push that ships them to production, and date it.
- Headings: "# CHANGELOG — vX.Y.Z — YYYY-MM-DD", sections "### Features",
  "### Improvements", "### Fixes". Start a security fix with "[Security]".
-->

## Unreleased

### Improvements

- Dashboards open on the year to date and compare it with the same days last year. A bar above each dashboard shows the period and switches it in one click: This month, Last 30 days, Year to date or Last year.
- The Sales, Purchases and Invoices tabs are laid out the same way: four key figures first, then the main chart, three breakdowns, and the lists two to a row. The separate rows of count tiles are gone; their numbers are in the key figures.
- Long periods are charted by week or by month instead of by day, so a year reads as twelve bars instead of hairline spikes.
- Sales has a "Quotations" breakdown: how many of the period's quotations became orders, are still open or were cancelled. Purchases shows "To receive": confirmed orders whose goods have not all arrived.
- When a period has nothing in it, the dashboard says when the latest record is, instead of only showing zeros.
- Invoices dashboard: the customer revenue ring and the salesperson bars now deduct credit notes, so they add up to the "Total invoiced" figure beside them.
- Overview: the date and clock in the welcome banner no longer change format a moment after the page opens, and "Updated" shows your own local time.
- Dashboard figures show their full comparison ("New vs 1 Jan 2025 – 31 Dec 2025") on a line of their own instead of cutting it off.

# CHANGELOG — v1.16.5 — 2026-11-23

### Fixes

- [Security] The "Preferred payment method" on invoices and bills lists only your company's payment methods. It listed every company's, and the new invoice page loaded more slowly because of it.
- Overview, Purchases: the spend tile counts purchase orders ("3 orders") instead of saying "No sales".

# CHANGELOG — v1.16.4 — 2026-11-22

### Fixes

- Dashboard trends compare with the right period again. The Sales dashboard read "vs 31 Dec – 30 Dec" instead of last year, and a comparison with an earlier year now names it, for example "vs 1 Jan 2025 – 31 Dec 2025".

# CHANGELOG — v1.16.3 — 2026-11-22

### Improvements

- Dashboard: the invoiced and billed totals now deduct credit notes and vendor refunds, and "Unpaid" includes partly paid invoices.
- Sales dashboard: top quotations are ranked by their value in your company's currency, so a dollar quotation no longer sits below a smaller one in local currency.
- Empty dashboard lists say "Nothing in this period yet" and offer the next step, such as "Create a quotation" or "Record a bill", when you are allowed to take it.
- Overview: "To collect" and "To pay" show how much is owed in each currency when there is more than one, and an open invoice in another currency shows the amount in that currency.
- The Sales dashboard tiles now also say what they compare with.

# CHANGELOG — v1.16.2 — 2026-11-22

### Improvements

- Dashboard trends say what they compare with, for example "+12% vs 1 Oct – 31 Oct".
- In Sales, the top customers table counts "Sales" (orders and invoices made without an order), not only orders.

### Fixes

- Clicking Sales or Purchase in the menu opens Orders again. Since v1.16.1 it opened the new Invoices or Bills tab first.

# CHANGELOG — v1.16.1 — 2026-11-22

### Improvements

- Customer invoices are now under Sales, as an "Invoices" tab next to Orders, and vendor bills are under Purchase as a "Bills" tab. Companies without Sales or Purchases keep the Invoices menu as before.
- Every customer and contact page has an Invoices tab listing that customer's invoices and credit notes.
- Lists load faster: permission checks are no longer repeated for every row.

### Fixes

- The Bills tab on a contact listed every document of that contact, customer invoices included. It now shows only bills and vendor refunds.

# CHANGELOG — v1.16.0 — 2026-11-22

### Features

- "New direct sale" on Invoices and on Sales → Orders: a counter sale in one save. The invoice opens with Walk-in Customer, "Paid now", the sales order and "Customer has taken the goods" already set; change anything before saving.
- "Print receipt" on a confirmed invoice prints a till-style receipt with the items, total and amount paid.
- Exchange rates now update by themselves every day, for every active currency and every company. A rate you enter for a day is kept.

### Improvements

- The company page's Administrator section lists each administrator with Set password and Manage buttons, has an "Add administrator" button that sends an invitation or sets a password, and shows invitations still waiting with Resend and Copy link.
- Sales Orders and Quotations open on all records instead of only your own, and the list shows the order date.
- Dashboard trends are honest: a figure with nothing before it says "New" instead of "+100%", a small number shows the real change ("+3"), and for costs and unpaid bills a rise is shown in red.

### Fixes

- Creating an invoice with "Paid now" but no lines shows a message instead of an error page.
- The Accounting journal cards (To Validate, Late, To Pay and the chart) add amounts in your company's currency. A company invoicing in dollars and its local currency saw the two added together.

# CHANGELOG — v1.15.0 — 2026-11-22

### Features

- Invoices can create their sales order: tick "Create sales order" on a customer invoice and, when the invoice is confirmed, a confirmed sales order is made from it, so the sale appears in Orders, Sales reports and the dashboards exactly once.
- "Customer has taken the goods" on the same invoice records the delivery as done straight away (stock goes down); untick it to leave the delivery for the warehouse. Each company chooses the default under Sales settings, "Customers take the goods when invoiced".
- The invoice page shows its sales order and the delivery status, with a "Mark as delivered" button for goods delivered later.
- Cancelling or resetting such an invoice cancels its sales order and any delivery not done yet.

### Improvements

- Recent items in quick navigation are kept per company, so after switching company they no longer link to pages you cannot open.

### Fixes

- Applying one stock count no longer clears the other counts waiting to be applied.
- Barcode no longer appears in the menu of companies that do not have it.
- "Reset to Draft" on a paid payment now resets the payment as well as its journal entry.

# CHANGELOG — v1.14.2 — 2026-11-21

### Fixes

- After your session expires, pictures and files now send you to the sign-in page instead of an error.

# CHANGELOG — v1.14.1 — 2026-11-21

### Fixes

- The "Revenue by category" ring and other ring charts no longer show a stray 0 to 1 scale beside them.

# CHANGELOG — v1.14.0 — 2026-11-21

### Improvements

- Sales made by invoicing directly, without a sales order, now count as
  sales everywhere in Sales: the number of sales, the average sale, top
  sales, the Overview card and the Sales analysis report (as well as revenue,
  products, categories, customers and salespeople).
- Invoices show the salesperson next to the customer. It starts as you, or
  as the customer's own salesperson, and lists only your company's people.
  Invoices made without a sales order now count for their salesperson on the
  dashboards. Earlier invoices with no salesperson count for the person who
  created them.
- Payments show how they were paid (Cash, Bank, Mobile Money) instead of
  "Manual Payment", on the invoice, the journal items and the payments list.
- Behind the scenes, the forms and lists of most modules were reorganised to
  match the latest upstream layout. Screens look and work the same; future
  improvements can now be adopted faster.

### Fixes

- The "Revenue by category" ring no longer shows a stray scale beside it.

# CHANGELOG — v1.13.1 — 2026-11-20

### Fixes

- The sales figures on the Overview and the Sales dashboard load again. The
  daily revenue chart stopped them from opening.
- Opening a page that you or your company cannot use now takes you to the
  dashboard with a short message, instead of an error page.
- Invoices made without a sales order now count on the Sales dashboard: top
  products, revenue by category, top customers, salespeople and the monthly
  chart. An invoice made from an order is not counted twice.
- The "Sales by product" report now also lists products sold on invoices made
  without a sales order, so paid invoices show up per product.

# CHANGELOG — v1.13.0 — 2026-11-20

### Features

- Add a new customer straight from the invoice: press "+" next to the
  customer field and enter a name, and optionally an email and phone.
- Administrators can choose, per user, whether they see the dashboards
  ("See dashboard") and the money dashboards for sales, invoices,
  purchases and accounting ("See money figures"). Both are on for
  everyone until changed.
- Sign in with your email or a username. Administrators can give a user a
  username, and you can set your own on your Profile page.
- Recording a payment is quicker: the Pay window asks how it was paid, how
  much and when; everything else waits under "More options".
- Cash, Bank transfer and Mobile Money are ready to use as ways to pay, each
  with its own account, and you can add your own (for example Wave or Card)
  from the Pay window.
- "Paid now" on a new invoice confirms it and records the full payment in one
  step, for cash and walk-in sales.
- Add a new product or service straight from an invoice line with "+".
- Changing a quotation's currency now converts every line, as on invoices,
  and warns you when there is no exchange rate.

### Improvements

- Sales revenue now counts every invoice, including invoices made without
  a quotation or sales order.

- Emails from DEKA now carry the DEKA logo and name at the top and amber
  buttons, so you can recognise them at a glance in your inbox.
- Invitation emails name the company and the person who invited you, say
  when the invitation expires, and include a plain link in case the button
  does not work.
- On a phone, lists show each record as a card: the number or name on top,
  the details under it, and the actions at the end, with no sideways
  scrolling.

### Fixes

- The Guide no longer causes errors in the background when it opens.
- The new purchase quotation no longer scrolls sideways on a phone.

# CHANGELOG — v1.12.5 — 2026-11-19

### Fixes

- Creating a user no longer stops silently: the new user starts in your
  current company, and if something is still missing, a message names the
  fields to fill in.
- Choosing "Company Admin" for a user now adds the business role too, so
  the new administrator sees the company's screens straight away.
- The "Money in and out" chart on the Overview shows payments on the day
  they were made, so money received today appears today, even for older
  invoices.
- Your profile picture shows again after switching company.
- Invoice, bill, payment and purchase lists sort again when you click a
  column heading. They still open with the newest first.

# CHANGELOG — v1.12.4 — 2026-11-19

### Fixes

- Invoices in your company's own currency (for example GHS) can be paid
  again. The payment window now uses the invoice's currency, not the bank
  journal's.
- If a payment cannot be prepared, you now see a clear message instead of a
  technical error, and our team is notified automatically.
- New companies are ready to invoice and take payments straight away:
  accounting (chart of accounts, journals, taxes and payment methods) is set
  up automatically. Existing companies that had not set it up are set up
  too.

# CHANGELOG — v1.12.3 — 2026-11-19

### Fixes

- Invoices can be marked paid again in every company. Companies whose
  accounting was set up after installation had no payment method on their
  bank and cash journals, so the payment could not be recorded. They now
  get "Manual Payment" in and out automatically.

# CHANGELOG — v1.12.2 — 2026-11-19

### Fixes

- Switching an invoice to another currency now converts every line and
  updates the totals and currency symbol straight away.
- When no exchange rate exists between two currencies, the invoice now says
  so and leaves the prices as they are, instead of copying them at 1:1. Add
  the rate under Configuration → Currencies.
- The invoice lines now say that a unit price changed on the invoice applies
  to that invoice only; the product keeps its own price.

# CHANGELOG — v1.12.1 — 2026-11-19

### Fixes

- [Security] Accounting entries in a locked period can no longer be
  deleted. Closing a month now protects its posted entries from deletion as
  well as from changes.

# CHANGELOG — v1.12.0 — 2026-11-16

### Improvements

* The Overview now has Projects, Recruitment and Logistics tabs next to Accounting and Inventory; the separate dashboards are gone from the top bar (old links open the matching tab).
* The Overview "All" page matches the approved design: Sales shows its three figures side by side, and the module cards fill even rows.
* Tax groups now live under Taxes: open them with the "Tax groups" button on the Taxes list.
* On the new-company form, Country sits next to the company name.
* Scrollbars are hidden throughout; pages still scroll as before.
* What's New shows hotfixes inside their release, marked with the hotfix version, instead of as separate sections.
* Billing plans can be switched back on after being deactivated.

### Fixes

* Exchange rates below 1 can be entered, so currencies stronger than the US dollar (euro, pound) can be switched on.

# CHANGELOG — v1.11.3 — 2026-11-16

### Fixes

* [Security] A record can only be saved in a company you belong to. Before, a request could place a record in another company.

# CHANGELOG — v1.11.2 — 2026-11-14

### Fixes

* Company administrators now see all eight ready-made roles, including Finance Manager, HR Manager, Sales Manager, Inventory Manager and Logistics Manager. Permanently deleting records stays with installation staff.

# CHANGELOG — v1.11.1 — 2026-11-14

### Improvements

* Company administrators can now give their users the ready-made roles (Finance Manager, Accountant, HR Manager and the others), and see every module their company has. The administrators' role is now called "Business Administrator".
* The Activity log names the record that changed ("Partner: Ada Mensah") instead of a number.
* Module names on the company form are readable ("Time Off" instead of "time-off"), and the plan list explains when no plans exist yet.
* When an administrator set your password, a message now says to choose your own; after changing it you are told to sign in again.

### Fixes

* Role names show as written ("HR Manager", not "Hr Manager").
* Companies in every OHADA country (Cameroon, Senegal, Côte d'Ivoire and the others) now start with their country's standard VAT rate, not a generic 15 %. You can change it under Accounting → Configuration.

# CHANGELOG — v1.11.0 — 2026-11-14

### Features

* Accounting set up for your country: companies in Ghana and Zambia start with local tax and payroll accounts (for example SSNIT, Tier 2, NAPSA, PAYE) and their VAT rate; companies in Congo and the other OHADA countries start with the SYSCOHADA chart in French. Every other country keeps the standard chart. Everything stays editable under Accounting → Configuration.

### Fixes

* Taxes copied when a new company sets up its accounting now keep their posting accounts.

# CHANGELOG — v1.10.0 — 2026-11-14

### Features

* Accounting: Cash Flow report. Cash at the start and end of a period, and the money in and out in between, grouped as operating, investing and financing. Export to Excel.
* Sales by product and Purchases by product reports: quantities and amounts per product or category, in company currency, with Excel export. Purchases also shows how much was received.
* The Overview has an Accounting section and card: cash at bank, bank lines still to reconcile and draft journal entries, with a "Reconcile bank" shortcut.
* Company currencies (installation staff): choose which extra currencies a company may use on invoices, bills, quotations, purchase orders, payments, journals and Logistics shipments and expenses. Companies without a list keep every active currency. The API applies the same list.
* Super Admins can change a company's base currency even after it has posted entries, from "Base currency" on the company page. You confirm a warning showing how many posted entries exist and give a reason; the change is recorded. Earlier entries keep their own currency and rate.
* Ready-made roles: Finance Manager, Accountant, HR Manager, Payroll Officer, Sales Manager, Inventory Manager, Logistics Manager and Standard Employee. Edit them under Roles; your changes are kept.
* Companies list (installation staff): a Setup status for each company (Active, Invited, Invitation expired, No administrator, Closed), a filter for it, and "Resend invitation" for a company whose administrator has not signed up yet.

### Fixes

* The Inventory card on the Overview now shows only to people who may open the Inventory overview, and only in companies that use Inventory.

# CHANGELOG — v1.9.0 — 2026-11-13

### Features

* Accounting: Tax Summary report. For any period, each tax (for example VAT, NHIL and GETFund) with its taxable amount, the tax collected on sales and paid on purchases, and the net to pay or reclaim. Credit notes reduce it. Export to Excel.
* Reports: one page listing every report you can open, grouped by module, with a search box.
* Sales analysis and Purchase analysis reports: confirmed orders in company currency, grouped by customer or supplier, salesperson or buyer, or month, with totals and Excel export.
* Accounting: Bank Reconciliation. Import your bank statement (CSV), then match each line to the payment it confirms (the payment becomes Paid) or book it to an account, for example bank charges or interest.
* Activity log (Settings): see who created, changed or deleted what in your company, with the old and new values and the reason given. Filter by person, record type, action and date, and export to Excel. Deletions are now recorded too.

### Improvements

* The Overview has a new design. The greeting shows the date, how many things need you today as clickable chips, your local time, and a "New invoice" button with other shortcuts beside it.
* The Overview's All tab is now one board: Sales (revenue, orders and how many quotations became orders, each with its trend and a small chart), money in and out with an optional comparison to the previous period, Purchases with spend by supplier, Needs attention, and Invoices with paid, open and overdue amounts and the invoices to chase.
* Below it, "Across the company" shows the key figures of Inventory, Projects, Recruitment, Employees and Logistics. Only the modules your company uses appear.
* The Overview refreshes itself every 5 minutes.
* Attendance now appears inside the Employees menu.
* The Plugins page shows each module's own version number.

# CHANGELOG — v1.8.1 — 2026-11-13

### Improvements

* Reopening a confirmed invoice, bill, credit note, refund or payment (Reset to draft) now asks for a reason, for example "customer asked for a 10% discount". The reason is kept in the document history.

# CHANGELOG — v1.8.0 — 2026-11-13

### Features

* Accounting: lock a closed period. "Lock date" on Journal Entries stops anyone posting, changing, cancelling or deleting entries dated on or before it. Old invoices can still be paid. Only a Company Admin, or someone given "Unlock period", can move it.

### Improvements

* The app is now called DEKA Central, by 365 Systems.
* The dashboard greeting now tells you what needs you today (for example "3 invoices overdue · 2 bills due this week") and has quick buttons for a new invoice, bill, quotation or purchase request.
* The Overview's All tab is now a short summary: the headline figures with their trend against the previous period, one chart of money in and out, and a "Needs attention" list that links to each item. Each module's tab still has all its charts and lists.
* On a phone, the pages of the module you are in now show as a row of tabs under the top bar. The Menu lists only the modules, so switching module takes fewer taps.
* What's New starts at version 1.5.0.
* What's New has moved from the top bar into your profile menu, next to the version. A red dot on your picture means there is something new to read.
* On the Overview, Sales, Purchases and Invoices are now tabs above the filters. Pick one, or All.
* The dashboard greeting has a soft gradient in your company's colour behind it.
* On a phone, the Overview tabs fade at the edge when there are more to scroll to.
* In What's New, clearing the search or filters goes back to showing only the latest release open, and the search box no longer shows its label twice.

* Creating a company is simpler: choose the plan on the same form (it starts at once and sets the modules and seat limit), give the administrator a name when you set their password, and the optional address, branding and contact details start folded away.

### Fixes

* Changing a filter on a dashboard with many charts no longer fails.
* A mistyped record address now shows "Not found" instead of a server error.

# CHANGELOG — v1.7.1 — 2026-11-12

### Improvements

* The "At a glance" cards on the Overview open the list they count, such as open quotations or invoices to collect
* What's New has a search box, category filters, a PDF download and a megaphone in the top bar that shows a dot when there is a release you have not read

# CHANGELOG — v1.7.0 — 2026-11-12

### Features

* Logistics: plan and track shipments, vehicles, trips and deliveries, collect proof of delivery on a phone, print waybills, invoice customers and record trip expenses, with a dashboard and reports
* Drivers can capture proof of delivery from a one-time link, without an account
* A customer's page shows their recent shipments, and their open charges for people allowed to see shipment amounts
* Shipment, delivery performance, profitability, vehicle and driver reports, each exportable with its filters
* New dashboards for Invoices, Sales and Purchases, and a richer Recruitment dashboard, showing only your company's figures
* Attendance: clock in and out from the dashboard, see who is in, correct entries with a reason, and report lateness, absence and overtime
* A welcome banner on the dashboards greets you by name, with your role, company, date and work location

### Improvements

* Attendance and Logistics can each be switched on or off per company, without losing existing records
* A forgotten clock-out is closed automatically after 12 hours (you can change the limit) and flagged for review, so it does not inflate worked hours
* Attendance records location and network details only when the company turns that on
* Proof-of-delivery photos and signatures are stored privately and can only be opened by people in the same company
* A sales order for a delivery service can create a draft shipment, without fixing its route or date yet

### Fixes

* [Security] Settings pages, overviews and dashboards in Sales, Purchases, Accounting, Inventory, Projects, Recruitment and Time Off again require their own permission
* [Security] Dashboard widgets check both the module and their own permission
* The Project charts "Tasks by state" and "Tasks by stage" load faster, and "Tasks by stage" no longer shows zero for every stage when no start date is chosen
* [Security] Logistics actions, reports, exports, documents and delivery files are limited to your own company
* [Security] Delivery links work once, expire, can be revoked, and are protected against guessing

# CHANGELOG — v1.6.0

### Features

* Document numbering for invoices, sales and purchase orders, scraps and manufacturing orders: choose the prefix, length, step and when it restarts (Settings → Sequences). Numbers no longer skip or depend on internal record ids
* Taxes can be calculated from a formula based on unit price, quantity and subtotal, with a minimum or maximum
* API tokens (Settings → API Tokens): give an integration its own token, read-only or read/write, see when it was last used, and revoke it
* A What's New page under Help shows these release notes
* A "Late" view in Inventory lists every operation past its deadline or scheduled date
* Contact lists can be filtered to employees, customers or vendors
* Errors are reported automatically to the support team, so problems are found and fixed sooner
* Your company logo now shows on the login and password-reset pages

### Improvements

* Each API token has its own request limit, so one busy integration cannot slow down another
* A read-only API token cannot change data
* The API always answers errors in a format integrations can read
* Amounts use your company's currency by default
* You can show a password while typing it in the user form
* Sending a purchase order warns you when a vendor has no email address, instead of reporting success
* Fixed errors on forms with many nested rows
* A new company's journals no longer pick up another company's bank account, and copied records use the right company's currency
* Problems while creating product variants are now recorded instead of being hidden

### Fixes

* [Security] Text shown in record history and notifications can no longer run code entered elsewhere in the application
* [Security] The logo address can no longer be used to open other stored files
* An invoice's payment status now updates once it is paid
* The vendor's bank account now shows in the Pay window
* Saved list views no longer cause an error
* Editing an employee no longer fails with a database error
* Currency conversion is applied in requests for quotation, purchase order and quotation totals
* A company's currency can no longer be changed once it has journal entries
* Deleting a currency that is still in use now explains why it cannot be deleted
* Printing an invoice no longer fails when files are kept in cloud storage
* The applicant categories list no longer fails when sorted
* Saving a company with no currency no longer causes an error

# 🚀 CHANGELOG — v1.5.0

### 🧩 Features

* Added multi-company (multi-tenancy) support with company scoping, allowed-companies access control, cross-company transfer guards, and a company switcher
* Added Maintenance module
* Added Putaway Rules for inventory operations
* Added Time-off dashboard widget
* Added Inventory dashboard widgets
* Added quick "next transfer" navigation across chained inventory operations
* Added a system-wide command palette (Ctrl+K / Cmd+K) for keyboard-only search and navigation across records, pages, and actions
* Added native barcode scanning support (Barcode plugin)
* Added PostgreSQL database support
* Added Inventory Reporting cluster and UOM Category resource
* Added Filament database and email notifications for chatter activity (mentions, assignments, and record updates)
* Unified Settings pages across plugins
* Unified Product resource across plugins
* Implemented nested order resources (Deliveries, Invoices, Receipts, Bills under Orders)
* Added bulk lot/serial number generation
* Added consolidated Spanish (es) locale
* Added Brazilian Portuguese (pt-BR) locale
* Added Arabic localization for the Plugins resource
* Added branding settings and middleware for dynamic panel customization
* Added dashboard date-range filter widget
* Integrated custom fields functionality across Accounting, Products, Projects, Recruitment, Sales, Security, Time-off, Timesheets, and Website resources
* Blocked plugin uninstall when dependent plugins exist
* Added customer Portal Access management for partners (grant/revoke access, change or send portal password reset from the admin panel)

### ✨ Improvements

* Added `trustProxies` middleware for improved proxy handling
* Added Docker setup for the production environment
* Simplified Docker image publishing logic and enhanced tag handling
* Added Playwright tests for the Website plugin
* Added pull request template
* Updated README
* Removed redundant Arabic translation file
* General code refactor
* Added picking description to inventory operation moves
* Added reusable column-manager traits for repeaters
* Hardened `WarehouseFactory` to ensure unique warehouse names/codes
* Updated default company seeding and admin user setup on install
* Fixed failing test suite
* Enhanced availability check to include the WAITING state
* Dashboard date filters now default to the start of the year
* Removed a redundant redirect
* Upgraded Filament to v5.7.3
* Upgraded Filament to v5.7.1
* Added on-hand and forecasted quantity columns to the products table
* Added an excess-quantity tooltip to the return action form
* Added more test coverage
* Updated `IN_PROCESS` payment status color for consistency
* Added unit test coverage for the Accounting plugin
* Added unit test cases
* Refactored plugin navigation system
* Refactored settings retrieval into a centralized function
* Fixed duplicate DB queries in the plugin manager's `isPluginInstalled` check
* General performance optimization
* Removed stray comments
* Localized hardcoded UI strings in plugin resources and reports
* Refactored code and related fixes
* Refresh caches on plugin install/uninstall
* Refactored `ApplyBrandSettings` middleware
* Refactored branding asset URL handling
* Updated delivery and receipt status handling
* Added UOM suffix to the quantity field
* Removed unnecessary theme
* Added Help section
* Refactored quantity field handling, added insufficient-stock tooltip
* Refactored product repeater disabled condition
* Added view-page actions to match edit pages
* Added create-option form for customer selection in quotations
* Normalized path separators in `translations:check` for Windows
* Added component availability status for Manufacturing Orders and MO readiness
* Added Playwright tests for the Inventory plugin
* Fixed missing `timeout` command handling on macOS/Windows
* Added authorization policy for inventory move lines and page-level access control for the Barcode launch page
* Improved badge wrapping in the Partners table

### 🐛 Fixes

* #1459 [fixed] - Removed static title on Website homepage/view pages
* #1458 [fixed] - Fixed Website content and RichEditor rendering issue
* #1456 [fixed] - Fixed default Time-off leave types not being shared across companies
* #1455 [fixed] - Fixed Project milestone company filter
* #1454 [fixed] - Fixed chatter message sorting issue
* #1453 [fixed] - Fixed Employee reporting company filter
* #1452 [fixed] - Fixed bank account company scope issue
* #1451 [fixed] - Fixed duplicate records on the Project dashboard widgets
* #1448 [fixed] - Fixed chatter auto-scroll issue
* #1447 [fixed] - Issue #1445 resolved - Fixed print delivery slip issue
* #1444 [fixed] - Fixed Accounts plugin uninstallation issue
* #1443 [fixed] - [Security] Removed publicly accessible `adminer.php` database admin tool from the public directory
* #1245 [fixed] - Job Title and Title fields now hidden for company-type partners (shown only for individuals)
* #1231 [fixed] - Fixed broken Facebook social link URL in Website plugin
* #1229 [fixed] - Archived records appearing in the "All" tab for Manufacturing Operations, Work Centers, and Bills of Materials
* #1225 [fixed] - Fixed inventory operation cancellation on sales orders
* #1220 [fixed] - Linked purchase order receipts to their order operations
* #1219 [fixed] - Navigation group labels now update correctly on locale switch
* #1216 [fixed] - Removed duplicate Auth import in LeaveAccrualLevel model
* #1442 [fixed] - Fixed missing pt_BR translations for field and operation resources
* #1441 [fixed] - Fixed bill of materials resource issue
* #1393 [fixed] - Fixed manufacturing plugin uninstall cleanup and reinstall consistency
* #1387 [fixed] - Fixed Arabic RTL PDF rendering issues (DomPDF + DejaVu font, weight/currency handling)
* #1296 [fixed] - Fixed manufacturing order consumption check for soft-deleted products and UOMs
* #1440 [fixed] - Fixed missing translations across the app
* #1437 [fixed] - Fixed a translation issue
* #1435 [fixed] - Issue #839 resolved
* #1434 [fixed] - Fixed chatter message issues
* #1426 [fixed] - Fixed chained inventory operations issue
* #1424 [fixed] - Fixed duplicate notification issue
* #1421 [fixed] - Fixed work order's custom operation name issue
* #1417 [fixed] - Fixed "Destination to Scrap Location" Playwright test
* #1413 [fixed] - Fixed product table reorder saving in inverted order
* #1412 [fixed] - Fixed create-button grouping issue
* #1410 [fixed] - Corrected scrap view label to "Scrap Location"
* #1408 [fixed] - Re-fixed issue #1397
* #1407 [fixed] - Fixed refund redirect to 404 page
* #1403 [fixed] - Fixed miscellaneous issues
* #1402 [fixed] - Purged orphaned chatter records on plugin uninstall
* #1401 [fixed] - Issue #1400 resolved
* #1395 [fixed] - Fixed a location issue
* #1394 [fixed] - Fixed a backorder issue
* #1391 [fixed] - Issue #1348 resolved
* #1390 [fixed] - Issue #1353 resolved
* #1389 [fixed] - Issue #1329 resolved
* #1384 [fixed] - Issue #1330 resolved
* #1383 [fixed] - Fixed broken blog image resizing issue
* #1382 [fixed] - [Security] Prevented unauthorized viewing/editing of chatter messages (IDOR)
* #1378 [fixed] - Fixed chatter issues
* #1376 [fixed] - Issue #1374 resolved
* #1375 [fixed] - Issue #1373 resolved
* #1366 [fixed] - Fixed a receipt issue
* #1365 [fixed] - Fixed an issue
* #1362 [fixed] - Issue #1353 resolved
* #1361 [fixed] - Issue #1326 resolved
* #1360 [fixed] - Issue #1333 resolved
* #1359 [fixed] - Issue #1335 resolved
* #1358 [fixed] - Issue #1347 resolved
* #1356 [fixed] - Issue #1350 resolved
* #1355 [fixed] - Issues #1348 and #1346 resolved
* #1344 [fixed] - Fixed an operation update issue
* #1342 [fixed] - Fixed a chatter UI issue
* #1340 [fixed] - Hid "Pick from" field when inventory is not tracked
* #1337 [fixed] - Fixed package selection issues in quantity creation
* #1324 [fixed] - Fixed an issue
* #1323 [fixed] - Fixed a customer authentication issue
* #1321 [fixed] - Fixed chatter issues
* #1320 [fixed] - Fixed location validation issues
* #1319 [fixed] - Issue #1235 resolved
* #1314 [fixed] - Issue #1233 resolved
* #1311 [fixed] - Issue #1236 resolved
* #1300 [fixed] - Issue #1230 resolved
* #1299 [fixed] - Fixed a package issue
* #1298 [fixed] - Fixed a package location issue
* #1297 [fixed] - Fixed an invoice policy issue
* #1295 [fixed] - Issue #1294 resolved
* #1291 [fixed] - Fixed chatter issues
* #1290 [fixed] - Fixed quotation messaging issues
* #1288 [fixed] - Fixed inventory warning issue when changing warehouse
* #1283 [fixed] - Fixed purchase order issues
* #1282 [fixed] - Fixed an issue
* #1281 [fixed] - Fixed validation issues
* #1280 [fixed] - Fixed sale order issues
* #1279 [fixed] - Fixed non-tracking product issues
* #1276 [fixed] - Fixed nested resource issues
* #1268 [fixed] - Fixed an inventory rule issue
* #1266 [fixed] - Fixed issues
* #1265 [fixed] - Fixed dropship print issue
* #1263 [fixed] - Validated BOM component product
* #1261 [fixed] - Fixed issues
* #1260 [fixed] - Fixed backorder settings issue
* #1259 [fixed] - Fixed issues
* #1258 [fixed] - Fixed backorder issues
* #1257 [fixed] - Fixed a backorder issue
* #1256 [fixed] - Fixed issues
* #1255 [fixed] - Fixed sale order warehouse selection
* #1252 [fixed] - Fixed inventory document creation issue when confirming sale order
* #1186 [fixed] - Issue #1170 resolved
