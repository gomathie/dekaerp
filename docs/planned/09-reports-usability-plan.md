# Reports and payment flow usability

Status (2026-11-25): **nearly done.** In production: R-1 (decided), R-6,
R-7 (including "Unused amount", which shipped with v1.17.0: the "Not
built" note in R-7 below is superseded), R-8, R-9 (complete; sales lists
keep their Order Date column). Being built by agents on 2026-11-25 after the
user approved mockups and decisions: R-2 (Export to PDF on the nine reports
with Excel only; every report already opens on screen), R-3 (written Zoho
comparison), R-4 (balance cards, Match all suggested, remembered column
mapping; no bank presets until sample files arrive), R-5 (lines without a
product: no tax until chosen, counted in sales, no stock). They ship as a
hotfix (user: HiTrace/TRACE365 requests are hotfixes).
*Superseded:* "planned (user, 2026-11-19) ... Partly done: R-1 decided, R-6
shipped in v1.13.1, R-3 partly built ... Open: R-2, R-4, R-5." Raised while
TRACE365 used the system; TRACE365 items ship as hotfixes or simplifications.

## R-1 Plain report names

"Aged receivable" and "Aged payable" are accountants' terms. They list what
customers still owe the company, and what the company still owes
suppliers, grouped by how long it has been due (0-30, 31-60, 61-90, over 90
days).

*Decided 2026-11-19 (user): keep the names as they are.* They are the
standard terms (Zoho Books "AR/AP Aging", QuickBooks "A/R Aging", Xero
"Aged Receivables/Payables"), so accountants find what they expect. The
guides explain them in plain words. Nothing to build.

## R-2 View a report before exporting it

**Built on `feature/backlog-a` (2026-11-26).** The nine reports already on screen
(Sales analysis, Sales by product, Purchase analysis, Purchases by product,
Trip history, Shipment register, Shipment profitability, Delivery performance,
Multi-currency) gained Export to PDF. The PDF uses the filtered rows, the
visible columns and the totals the screen shows, and it keeps each model's
company scope. More than 2,000 rows is refused with a prompt to narrow the
filters. Excel is unchanged.

The user wants every report shown on screen first, with Excel and PDF as
choices from that screen, never an export as the first step. Inventory the
reports that export without showing (Partner reports, accounting reports,
Logistics reports), and give each an on-screen view with the same filters,
then Export to Excel and Export to PDF buttons that use exactly those
filters.

## R-4 Clean reconciliation for Cash, Bank and Mobile Money

*Shipped 2026-11-25 in hotfix v1.17.2:* items 1 (balance check per
account), 2 (Match all suggested), 3 (remembered column mapping) and 4
(Cash at bank wording and link). Cash journals show "No statement"; no cash
counting. The DEKA statement template (below) is built and waiting for its
test run.

User, 2026-11-20: "Mobile Money, bank and cash reconcile cleanly in Zoho; we
should have that too." We have the base already (accounting plan A3,
Accounting → Bank Reconciliation: CSV import per cash/bank journal, match a
line to a suggested payment, or book it to an account). Mobile Money is a
bank-type journal from v1.13.0, so it is covered. What is missing, by
priority:

1. **Balance check per account**: statement balance, book balance and the
   difference for each cash, bank and Mobile Money journal, so "clean" is
   visible at a glance.
2. **"Match all suggested"** for lines with exactly one suggestion, instead of
   one by one.
3. **Saved statement formats** per journal (column mapping remembered;
   presets for MTN MoMo and the common Ghana banks), so an import is one
   step. *Superseded 2026-11-25 (user, mockup approved):* no bank presets.
   DEKA publishes **its own statement template** (Excel and CSV, downloadable
   from the import dialog, pre-filled with the account and currency): header
   lines Account; Opening balance, Closing balance, Currency; Statement
   from/to; then Date, Description, Reference, Partner, Money in, Money out,
   Balance. Dates YYYY-MM-DD, amounts with a dot, in and out in separate
   columns. A file in this layout needs no mapping; its balances feed the
   balance cards; an import whose rows do not add up from opening to closing
   is refused with the difference; a different currency is refused. Other
   layouts keep the remembered column mapping.
4. **Dashboard wording**: "Cash at bank" counts only reconciled money; say so
   and link to reconciliation (also dashboard plan #4).

## R-5 Invoice lines without a product

**Done (2026-11-26).** Invoice and bill lines shipped in `2e256499d`
(v1.17.8); sales counting under "No product" in `712ae164e` (v1.17.10,
cherry-pick of `8caff93c5`).

Zoho accepts a free-text line. Ours requires a product; v1.13.0 adds "+"
on the line to create one quickly. A real free-text line needs a line
description field, totals without a product, and the line posted to the
journal's income account with `display_type` PRODUCT set explicitly (see
AGENTS.md, MoveLine note). Decision taken (user, 2026-11-20: yes); build
after v1.13.0 with its own tests.

## R-6 Paid invoices reconcile with sales by product

*Done in hotfix v1.13.1 (2026-11-20).*

**Request** (user, 2026-11-20): "invoices paid for should reconcile with
orders and sales based on the products."

**Cause:** every product figure read confirmed sales orders only, so a sale
invoiced directly (TRACE365's way of working) counted nowhere.

**Change:**
- Sales by product now lists confirmed order lines plus the product lines of
  posted invoices and credit notes that no order produced.
- Amounts are untaxed and in company currency. Credit notes count as
  negative.
- An invoice made from an order is left out, because its order line is
  already listed.
- The Sales dashboard follows the same rule (dashboard plan DB-8).
- The column formerly called "Order" is now "Order or invoice".

**Tests:** sales `SalesByProductTest` and `SalesDashboardTest`.

**Follow-up:** if a company wants invoiced quantities instead of ordered ones
for its orders too, that is a separate decision.

## R-3 Compare the payment flow with Zoho Books

Compare how Zoho Books records a payment after an invoice is created with
our flow (Pay window, "Paid now", payment methods planned for v1.13.0):
number of clicks, fields asked, defaults, partial payments, and payments
recorded from the customer or from a list. Record the findings in
`docs/ux-findings.md` and adopt what makes ours simpler.

*Compared 2026-11-25* (`docs/ux-findings.md`, "Recording payments: four
cases counted against Zoho Books", U95–U100). *Superseded 2026-11-27:*
the five proposals below are built on `feature/backlog-b`. The sentence
that called them "not built" is the 2026-11-25 reading.

*Decided 2026-11-25 (user, after a mockup, one question per idea):* build
U98 (start "How was it paid?" on the way this user last used in this
company; built 2026-11-27), U99 (live "Balance after this payment" / "kept as credit"; built 2026-11-27), U97
(on confirm, offer to apply the customer's unused credit; built 2026-11-27), then U95 (one
payment for several invoices, oldest first, amounts editable; built 2026-11-27) with U96
built into it (the Payments › New form lists the customer's open
invoices; built 2026-11-27), then U100 (refund unused credit; **Company Admin and up only**; built 2026-11-27,
recorded in the payment's history under their name). Each ships as its own
small hotfix.

## R-7 Payments received, by month and year (TRACE365, 2026-11-23)

TRACE365 compute their taxes from the payments they received in a month or a
year, as on Zoho's "All Received Payments" (date, payment number, reference,
customer, invoice, mode, amount, unused amount). Production check: nothing is
missing (10 paid invoices, 10 payments, each linked to its invoice); the list
just did not show it.

**In progress (hotfix):** the Payments list gains the invoice number(s) the
payment settled and its reference (memo); a Year and Month filter on the
payment date; a total under the amount column for whatever is filtered, in
company currency; the "Amount" column in the payment's own currency (it
showed a dollar amount with the company's cedi sign). The export carries the
invoice numbers and reference. Not built: Zoho's "unused amount" (credit left
on a payment), because it needs the reconciliation residual per payment; add
it if TRACE365 records payments ahead of invoices.

## R-8 Clean invoice and quotation documents (TRACE365, 2026-11-23)

TRACE365 compared our printed invoice with their Zoho invoice and quote and
found ours not good enough to send. Theirs: logo and company details with tax
ID, a large title, number and balance due; "Bill to" beside the dates and
terms; numbered lines with a grey description under each item, quantity, rate
and amount; subtotal, one line per tax (NHIL/GETFund 5%, VAT 15%), total and a
shaded balance due; notes with bank details; terms; page numbers.

Ours today: no logo or tax ID, product name only, no line amount, one "Tax"
line, a "Discount" line that sums discount percentages as money, totals on the
left, raw dates, no balance, notes or page numbers. Four near-identical
templates (invoice, bill, credit note, refund) plus the sales quotation.

**In progress (hotfix):** one shared document layout used by all of them,
with the reference's structure; the logo read from the document company's own
folder (tenant-s3 prefixes by the session company); the notes sanitized
(`str()->sanitizeHtml()`, the same view renders in the preview modal). "Print"
streams the PDF named after the document number instead of saving it to the
public disk as `invoice-<date>.pdf`, where two invoices printed the same day
overwrote each other. Purchase RFQ and order PDFs follow in the same layout.

## R-9 List pages like the references (TRACE365, 2026-11-23)

From the invoice-list references TRACE365 shared:

- **Status tabs with counts** ("All 157 · Draft 5 · Paid 120 · Due 17 ·
  Overdue 7"): our preset views are tabs already; add the count badge to each.
- **Paid and Due columns** on invoices and bills, paid in green and due in
  red, beside the total (from `amount_residual_signed`, company currency as
  DB-7).
- **Two-line cells:** number with "Created on <date>" under it; customer with
  their company under it. Saves two columns on phones too.
- **Summary cards above the list:** "Invoiced this month" and "Paid" with a
  trend pill, read from the same queries as the Invoices dashboard (DB-2), so
  the list and the dashboard agree.
- "Showing 15 of 157" with a page-size picker: Filament already has it; check
  it is visible on phones.

Applies to invoices, bills, sales orders, purchase orders and payments, in
that order. One list concern, not five copies.

**In progress (hotfix), invoices first:** number with the invoice date under
it (the date column stays, hidden by default); a green "Paid" column and the
"Amount due" in red, both in the invoice's own currency (the due column showed
the company-currency residual with the invoice's currency sign); counts on the
Draft, To pay and Overdue tabs, with To pay and Overdue now shown as tabs
(`ListInvoices::presetCount()` applies the tab's own condition, so the count
matches the list; bills inherit it). Still open: the summary cards, the
customer's company under the customer, and the other lists.

*Summary cards built 2026-11-24 (develop, unreleased):* the Invoices list
(invoices plugin, `Customers\...\ListInvoices::getHeaderWidgets()`) shows the
Invoices dashboard's own `InvoiceStatsWidget` for this month, headed "This
month": total invoiced (net of credit notes), count, paid, unpaid, each with
its trend pill. Same widget and queries as the dashboard, so they cannot
disagree; its guard (widget permission, module, money-figures switch) decides
who sees it. *Also built the same day:* the contact's company (their parent
contact) under the customer on the invoice list and the vendor on the bill
list, eager loaded (`partner.parent`). Still open: summary cards and
two-line cells on the other lists (bills, orders, payments).

*Cards built the same day:* vendor bills (`BillStatsWidget`), purchase
orders (`PurchaseStatsWidget`), sales orders (`SaleStatsOverview`) and
payments received (new invoices `PaymentStatsWidget`, on
`OverviewCashFlowChart::recordedPayments()`), each the dashboard's own
figures for this month. Every list's cards are hidden when the user's
money-figures switch is off. Still open: the Accounting plugin's own
payments page, and two-line cells on orders and payments.

*Both built 2026-11-25 (develop, unreleased; docs/change-log.md):*
Accounting's customer payments list shows the same two cards through
`Account\Filament\Concerns\ShowsPaymentStats` and
`Account\Support\RecordedPayments`, shared with the Invoices widget
(Accounting does not depend on Invoices). Purchase orders and payments
show the date under the number (the separate date column hidden by
default) and the contact's company under the contact. Sales quotations
and orders show the customer's company; their "Order Date" stays its own
visible column, as the 2026-11-22 decision pins. *Decided 2026-11-25
(user, after a preview):* sales keeps its Order Date column, to filter and
sort by date; the date is not repeated under the number. R-9 is complete.
