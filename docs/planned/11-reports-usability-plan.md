# Reports and payment flow usability

Status: planned (user, 2026-11-19). Not started. Raised while TRACE365 used
the system; TRACE365 items ship as hotfixes or simplifications.

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
