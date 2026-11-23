# Reports and payment flow usability

Status: planned (user, 2026-11-19). *Superseded 2026-11-22: "Not started".*
Partly done: R-1 decided (names kept), R-6 shipped in v1.13.1, R-3 partly
built ("Paid now" and payment methods named by how they were paid, v1.13.0;
"New direct sale" on develop). Open: R-2, R-4, R-5. Raised while TRACE365
used the system; TRACE365 items ship as hotfixes or simplifications.

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

The user wants every report shown on screen first, with Excel and PDF as
choices from that screen, never an export as the first step. Inventory the
reports that export without showing (Partner reports, accounting reports,
Logistics reports), and give each an on-screen view with the same filters,
then Export to Excel and Export to PDF buttons that use exactly those
filters.

## R-4 Clean reconciliation for Cash, Bank and Mobile Money

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
   step.
4. **Dashboard wording**: "Cash at bank" counts only reconciled money; say so
   and link to reconciliation (also dashboard plan #4).

## R-5 Invoice lines without a product

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
