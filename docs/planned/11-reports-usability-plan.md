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

## R-3 Compare the payment flow with Zoho Books

Compare how Zoho Books records a payment after an invoice is created with
our flow (Pay window, "Paid now", payment methods planned for v1.13.0):
number of clicks, fields asked, defaults, partial payments, and payments
recorded from the customer or from a list. Record the findings in
`docs/ux-findings.md` and adopt what makes ours simpler.
