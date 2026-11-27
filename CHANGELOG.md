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

# CHANGELOG — v1.18.1 — 2026-11-27

### Fixes

- Record payment on the invoice list, and a new customer payment, open again: choosing a customer no longer stops the page.
- What's New shows the latest release as soon as it is out.

# CHANGELOG — v1.18.0 — 2026-11-27

### Improvements

- Dashboard navigation loads tenant grants once per request to reduce permission queries.
- Opening an invoice no longer pauses on a long list of lines.
- The nine reports can be saved as a PDF of exactly what is on screen, including the filters and totals.
- Dashboards show the period, the dates and Filters on one row.
- The Accounting overview follows the same two dashboard switches as the other money dashboards.
- A quotation can be marked Won after it is accepted, or Lost after it is declined or has expired. Sales figures stay the same.
- Expenses: the currency sits inside the amount field. With more than five accounts, Paid from becomes a searchable list with the last used account first. A paperclip on the list opens the receipt.
- Invoice lines use shorter placeholders, Item or service and Description, so they fit on a laptop screen.

### Fixes

- Opening a page no longer fails when a menu section and one of its pages both have an icon.
- Record payment and Paid now start on the way you last used for this company.
- Record payment shows the balance left on the invoice, and says when the extra is kept as credit for the customer.
- Confirming an invoice, or recording a payment, asks when the customer has unused credit. Apply is the main answer and settles the invoice from that credit. Don't apply posts the invoice and leaves the credit.
- Record payment on the invoice list, and a new customer payment, pays the amounts you enter on that customer's open invoices with one payment. Pay in full puts an invoice back to its open balance. Saving a customer payment posts it.
- A customer payment with money left over has Refund unused amount, for a Company Admin and up. It pays that amount back through the way you choose, and the payment's history names who did it.
- A new customer invoice's main button is Create and confirm. Save as draft is beside it. Paid now still confirms once.
- A new customer invoice asks the currency with chips on the form, and skips the question when the customer already has a currency.
- A new customer invoice starts with Create sales order ticked for stocked goods. Deliver records the delivery on the invoice, and opens it on the order.
- A bill made from a purchase order is dated on the goods-receipt date. Confirm Order still asks "Are you sure?".
- After a new user chooses a password, they stay signed in and open the dashboard.
- A posted customer invoice can be credited in one step: the whole invoice or some lines, then take it off the invoice, pay it back, or keep it as credit.
- An overdue customer invoice has Send reminder. It emails that customer through the invoice email.

# CHANGELOG — v1.17.20 — 2026-11-26

### Features

- Invoices: schedule customer invoices to repeat weekly, monthly, quarterly or yearly. Each run makes a draft for review and alerts the responsible person. You can pause or stop a schedule, or create its next invoice now. Automatic confirmation and email are optional and off by default.

# CHANGELOG — v1.17.19 — 2026-11-26

### Features

- New Expenses screen beside Bills. Record a cost in one form: date, amount, expense account, the bank or cash account it was paid from, and optionally the vendor, tax, reference, note and a photo or PDF of the receipt. Saving posts it at once in that bank or cash account, so its balance is right straight away. Editing an expense corrects its entry, and deleting it takes the money back out of the books. Both ask for a reason, and both are refused once the period is locked. Changing only the note or the receipt needs no reason and also works in a locked period.
- Expenses: an "Amount includes tax" switch says whether the tax is taken out of the amount or added on top. It starts at the tax's own setting.
- Bank reconciliation: a bank line for money paid out can now be matched to the expense it paid ("Match expense"). DEKA suggests the expense paid from the same account, for the same amount, within a few days. Matching posts nothing again, so the money is counted once. "Match all suggested" includes expenses.

# CHANGELOG — v1.17.18 — 2026-11-26

### Features

- Invoices and bills: one box per line finds your products and services (with their price) or takes whatever you type as a line you price yourself. Each line can have a description that prints on the invoice.

# CHANGELOG — v1.17.17 — 2026-11-26

### Improvements

- On a confirmed invoice or bill, "Pay" is now Record payment, shown as the main button until it is fully paid.
- Recording a payment, including "Paid now" on a new invoice, now needs the payment permission. Sales Managers have it.
- Invoices and bills: lines now say "Add item", "Item / service" and "Qty", for services as well as goods.

### Fixes

- The Zoho Books import page now accepts the export file (it was refused at upload).
- Sales: an order's invoice and delivery status now updates correctly when changed by a user who sees only their own records.

# CHANGELOG — v1.17.16 — 2026-11-26

### Features

- Move from Zoho Books: installation staff can now bring a company's whole Zoho Books history into DEKA: customers and vendors, products, quotations, invoices with their original numbers, payments, expenses and money put in by the owners. Balances match Zoho to the last pesewa, and the matching of accounts and taxes is shown for approval first.
- Zoho comparison: while you use Zoho and DEKA side by side, each check is kept on a page of its own (Settings › Zoho comparison), with the totals and every invoice, payment or expense that is in one system only.

### Improvements

- Invoice and quotation lines can now hold descriptions longer than 255 characters.

# CHANGELOG — v1.17.15 — 2026-11-26

### Improvements

- Invoices: "Create sales order" and "Customer has taken the goods" now appear only when the invoice sells goods. An invoice of services only no longer shows them.

# CHANGELOG — v1.17.14 — 2026-11-26

### Improvements

- Overview: Needs attention now shows deliveries that are ready but not yet validated, with a link to them.
- Bills: the list now says Vendor, shows amounts without a minus sign, has a Paid column, and fits the money on screen.
- Invoices: the email sent with an invoice is now in your language, signed with your name and your company, and no longer mentions a bank account when there isn't one.

### Fixes

- Sales: cancelling a delivery now updates its order's delivery status straight away.

# CHANGELOG — v1.17.13 — 2026-11-26

### Improvements

- "Create invoice" on a sales order now opens the new invoice, ready to check and confirm.
- Only people who may edit a quotation can confirm it.

### Fixes

- [Security] Document numbering (Settings › Sequences) can now be changed only by company administrators, for their own company, and by DEKA staff. Other users can no longer edit, reset or delete number sequences, and shared numbering used by several companies can be changed only by DEKA staff.
- Opening a page you do not have access to now always takes you to the dashboard with a notice, instead of sometimes showing an error page.

# CHANGELOG — v1.17.12 — 2026-11-26

### Fixes

- Invoices: deleting a draft invoice made from a sales order now puts the order back to "To invoice", and asks whether to cancel the order and its delivery too.

# CHANGELOG — v1.17.11 — 2026-11-26

### Features

- New "Convert to invoice" button on quotations: one click confirms the quotation, creates its invoice and opens it, so you can check it before confirming.

# CHANGELOG — v1.17.10 — 2026-11-26

### Fixes

- Invoice lines without a product now show as "No product" in Sales by product and on the Sales dashboard. Each counts once, including on invoices made from a sales order.
- Sales: orders whose past sales were recorded as already delivered no longer show as "waiting for delivery" on the Sales dashboard.

# CHANGELOG — v1.17.9 — 2026-11-25

### Fixes

- Records followed by a contact that was later deleted now open normally. The contact shows as "(deleted)" in the followers list and can be removed.
- Deleted contacts no longer receive emails or notifications about records they used to follow.

# CHANGELOG — v1.17.8 — 2026-11-25

### Features

- Invoices and bills can now have a line without a product: type a description, choose the account, the quantity and the price, and add a tax only if it applies.

# CHANGELOG — v1.17.7 — 2026-11-25

### Features

- Administrators now have a Developers tab under Help. It explains how to connect to the API and lists, with ready-to-copy examples, the API for the modules your company uses.

# CHANGELOG — v1.17.6 — 2026-11-25

### Fixes

- Paying an invoice no longer moves its posted lines to a different income or expense account when the product's account was changed after the invoice was posted.

# CHANGELOG — v1.17.5 — 2026-11-25

### Fixes

- The API reference at /api/docs opens again. You now need to sign in to see it.

# CHANGELOG — v1.17.4 — 2026-11-25

### Fixes

- The Sales dashboard's number of sales and average sale now count invoiced sales, the same sales that make up Revenue. Orders not invoiced yet no longer lower the average.
- "Needs attention" on the Overview now shows customer invoices still in draft, with their total and a link to the draft invoices.

# CHANGELOG — v1.17.3 — 2026-11-25

### Fixes

- Dashboards show money in full below 100,000, for example $3,750.00 instead of "$3.8K", so figures match your invoices and bills. Only amounts of 100,000 and more are shortened ($125K, $1.2M).

# CHANGELOG — v1.17.2 — 2026-11-25

### Improvements

- Bank Reconciliation now shows a balance check for each cash, bank and Mobile Money account: the statement balance, the balance in the books, the difference, and whether the account is clean.
- "Match all suggested" matches, in one step, every bank line that has exactly one matching payment.
- Each bank account remembers the columns of its last statement import, so the next import is already filled in. You can also type the statement's closing balance.
- "Cash at bank" on the dashboard now says it counts reconciled money only, and opens Bank Reconciliation.
- The Projects card no longer shows "0% done" when there are no tasks.
- The guide says which way to start a sale: a quotation first, or an invoice straight away.

# CHANGELOG — v1.17.1 — 2026-11-25

### Improvements

- Posting an invoice or bill in another currency that is dated an earlier day: the summary now also says what it would come to at the latest exchange rate, and a "Post at the latest rate" button posts it at that rate instead. Plain Confirm still uses the rate of the document's date. The choice is recorded in the document's history under your name.
- On a phone, "What currency was this sale in?" opens as a panel from the bottom of the screen, with large full-width buttons and the rate in big type.
- The Accounting app's customer payments list shows this month's money received and number of payments above the list, the same figures as in the Invoices app.
- The purchase orders and payments lists show the date under each number, and the company a contact works for under their name, as the invoice list does. The separate date column is still available from the column picker. The sales orders and quotations lists show the customer's company under their name and keep the Order Date column.

# CHANGELOG — v1.17.0 — 2026-11-25

### Improvements

- Russian and Swahili are now languages you can choose, next to English, Arabic, Spanish, French and Portuguese (Brazil). These translations are machine-assisted. If a phrase sounds wrong, a native speaker's correction is welcome, and English stays one click away.
- Corrected wording on several screens, in every language: activity types are no longer titled "Departments", creating a lot says "Lot created", time-off allocations, accrual plans, mandatory days and public holidays confirm the right action, resume entries no longer say "Skill Level", and employment types, job positions and skill types say what was saved.
- Dashboards open on the year to date and compare it with the same days last year. A bar above each dashboard shows the period and switches it in one click: This month, Last 30 days, Year to date or Last year.
- The Sales, Purchases and Invoices tabs are laid out the same way: four key figures first, then the main chart, three breakdowns, and the lists two to a row. The separate rows of count tiles are gone; their numbers are in the key figures.
- Long periods are charted by week or by month instead of by day, so a year reads as twelve bars instead of hairline spikes.
- Sales has a "Quotations" breakdown: how many of the period's quotations became orders, are still open or were cancelled. Purchases shows "To receive": confirmed orders whose goods have not all arrived.
- When a period has nothing in it, the dashboard says when the latest record is, instead of only showing zeros.
- Sales dashboard: the "Monthly Sales" chart adds up to the Revenue figure above it. It counted confirmed orders that were not invoiced yet. It also draws the compared period's months beside this period's.
- Invoices dashboard: the customer revenue ring and the salesperson bars now deduct credit notes, so they add up to the "Total invoiced" figure beside them.
- Sales dashboard: the breakdowns by salesperson, category, customer and product say what they count (confirmed orders and invoices made without an order), and a customer's number of sales no longer counts a credit note.
- Overview: the date and clock in the welcome banner no longer change format a moment after the page opens, and "Updated" shows your own local time.
- Dashboard figures show their full comparison ("New vs 1 Jan 2025 – 31 Dec 2025") on a line of their own instead of cutting it off.
- Purchases dashboard: the supplier ring, top suppliers, top products and top orders count confirmed orders only, like the "Spend" figure. They also counted requests for quotation and cancelled orders.
- Accounting dashboard: on each journal card, the due-date chart deducts credit notes and vendor refunds, so it adds up to "To Pay". "Late", and the Overdue list it opens, include partly paid invoices past their due date.
- Logistics dashboard: "In transit" includes shipments that have been picked up, so it and "Out for delivery" add up to the Overview's "On the road".
- Dashboards refresh themselves at most every 5 minutes. The Projects, Recruitments, Logistics and Time Off figures refreshed every few seconds, which slowed the application for everyone with a dashboard open.
- The payment text message to a customer states one currency: it added an order's dollar and cedi invoices together, and counted a credit note as more invoiced.
- Dashboard figures show their change as a coloured pill with an arrow (▲ 12% green, ▼ 4% red), and the Sales and Purchases headline figures have an icon, like the other cards.
- The main chart on Sales, Purchases and Invoices has a switch to group it by day, week, month or year. "Automatic" keeps the grouping that suits the period. "Monthly Sales" is now "Sales over time".
- Invoices and the Overview: "Money in and out" shows the two as paired bars, with both totals and their change against the previous period above the chart.
- The revenue, supplier and customer rings list each slice's amount and share below the ring. Their centre total now follows the filters; it kept the first period's figure after a filter was changed.
- Sales dashboard: a new "Order activity" row shows how many of the period's orders are to deliver, delivered, to invoice and invoiced. Each opens the order list on that view; the order list has new "To Deliver", "Delivered" and "Invoiced" views.
- On the invoice and bill lists, a contact who works for a company has that company's name under their own.
- Confirming an invoice or bill in another currency now first shows its total in your company currency, at the document's own exchange rate, and the date that rate belongs to, before it is posted.
- The vendor bills, purchase orders, sales orders and payments received lists now show this month's figures at the top, the same as on their dashboards. These figures stay hidden for users who are not allowed to see money figures.
- The invoice list opens with this month's figures above it: total invoiced, number of invoices, paid and unpaid, each with its change against the same number of days just before. They are the same figures as the Invoices dashboard and show for people who can see that dashboard.
- Every dashboard list (orders, quotations, invoices, bills, requests for quotation, customers, vendors and products) has a "View all" button that opens the whole list. Vendors in the Purchases dashboard open when clicked.
- New company setting "Ask which currency each sale is in". When it is on and the company has more than one currency, a new invoice first asks for the currency. For a foreign currency it shows the day's exchange rate, which you accept or correct, and the invoice keeps the rate you confirmed. A corrected rate is noted in the invoice's history. The rate you kept is shown under the currency, and "Currency and rate" at the top of the page lets you change it. New quotations and sales orders ask the same question, and an invoice made from the order keeps its rate.
- The company form shows which currencies the company can use on documents right now.
- Purchase agreements offer only the company's own currencies, like the other documents.
- New report under Accounting › Reporting: "Multi-currency". Per currency, what was invoiced, received and is still open, in that currency and in the company currency at the rates booked, what the open amount is worth at today's rate, the exchange gains and losses booked, and the documents behind the figures. It can be exported to Excel.
- Payments received show an "Unused amount" column: the part of a payment not yet matched to an invoice or bill. It is in the export too.

# CHANGELOG — v1.16.13 — 2026-11-24

### Fixes

- A sales order in another currency is now counted at its value in your company currency on the Sales dashboard and in the sales reports. Before, a 1,180 GHS order in a dollar company counted as $1,180. An order made from an invoice uses that invoice's exchange rate.

# CHANGELOG — v1.16.12 — 2026-11-24

### Fixes

- [Security] Removed a database administration tool that was included in the application files. It was not reachable from outside, but it does not belong in a deployed system.

# CHANGELOG — v1.16.11 — 2026-11-23

### Fixes

- Printed documents show "Page 1" in the footer instead of "Page 1 / 0".
- When a company's branding colour is very light, printed documents use a dark table header so the column names stay readable.

# CHANGELOG — v1.16.10 — 2026-11-23

### Fixes

- "Print" on an invoice or quotation preview downloads the PDF again. In v1.16.9 it showed an error instead.
- A document with an empty note no longer prints a "Notes" heading with nothing under it.

# CHANGELOG — v1.16.9 — 2026-11-23

### Improvements

- Invoices, bills, credit notes, refunds and quotations print in a new, cleaner layout: your logo and tax ID at the top, the customer beside the date, terms and due date, numbered lines with their description and amount, each tax on its own line (for example NHIL/GETFund and VAT), the amount paid and the balance due, your bank details and notes, and page numbers.
- Quotations can be downloaded as a PDF from their preview ("Print").
- Payments received: the list shows which invoice each payment settled and its reference, filters by year and month, and totals what is shown, so you can see what was received in a month or a year. The export includes the invoice numbers and references.
- Invoices list: the date shows under the invoice number, a "Paid" column shows what has been received, the amount still due is in red, and the Draft, To pay and Overdue tabs show how many invoices they hold.

### Fixes

- The invoices list showed the amount due of an invoice in another currency in your company currency, with the invoice's currency sign. The payments list did the opposite for the payment amount.
- Two invoices printed or e-mailed on the same day could share one PDF file, so a customer could receive another customer's invoice.
- The invoices list's "Overdue" tab includes invoices that are partly paid and past their due date.

# CHANGELOG — v1.16.8 — 2026-11-23

### Fixes

- Employees › Attendance reporting (Lateness, Overtime, Absence) opens again for managers and administrators. It showed a server error to anyone allowed to see everyone's attendance.
- On a phone, the cross that clears a picker and the small "Open" and "Configure" links are easier to tap.

# CHANGELOG — v1.16.7 — 2026-11-23

### Improvements

- Contacts have a "Default Currency" (Sales & Purchase tab). Invoices, bills and purchase orders for that contact open in it, so a customer or supplier you deal with in dollars no longer needs the currency changed by hand each time. Without one, bills and purchase orders open in the currency of your Vendor Bills journal, and invoices in that of your Customer Invoices journal, else your company currency.
- Invoices, bills and purchase orders in another currency show their value in your own currency beside the total, for example "≈ GH₵ 44,257.50 · 1 USD = 11.80 GHS", and in the lists under each amount. The rate is the document's own.
- Phone top bar: scrolling down hides the top row so the list gets more room, and scrolling up brings it back; the page tabs stay. The Menu button is an icon, the company name has more room, and the tabs fade only at the edge that has more.

### Fixes

- Phone list cards name each value ("Price", "On Hand", "Forecasted"…) and leave out empty ones. Product cards showed two unlabelled prices, a bare 0 and two quantities that looked the same.
- The company page stacks its cards on a phone. The name broke mid-word and the logo ran out of its card.
- While viewing as another user, the orange banner no longer covers the page tabs on a phone.

# CHANGELOG — v1.16.6 — 2026-11-23

### Fixes

- "Invoices" in the sidebar opens your invoices again, with Credit Notes, Payments, Products and Customers beside them. Since v1.16.1 it opened Bank Accounts, and customer invoices were a tab inside Sales. Bills stay under Purchase.

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
