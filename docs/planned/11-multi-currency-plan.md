# Multi-currency invoicing: ask for the currency, confirm the rate, report it

Status: **planned** (user, 2026-11-24, raised for TRACE365). TRACE365 items
ship as hotfixes or simplifications; this plan is a feature, so it ships in a
monthly release unless the user decides otherwise.

Related: CR-6 company currencies (plan #1), DB-7 money rules (plan #2),
`Account\Support\DocumentCurrency` (v1.16.7), R-7/R-8 (plan #10).

**Out of scope, by decision:** TRACE365's base currency stays USD for now
(user, 2026-11-24: "leave as-is"). Nothing here moves their base currency,
re-rates old invoices or changes their journals.

## 1. What the user asked

1. A company sees only the currencies chosen for it. TRACE365 must see USD and
   GHS, not the other active currencies.
2. A company setting, "Ask money equivalent". When it is on, creating an
   invoice first asks which currency the sale was in (for TRACE365: GHS or
   USD). If the answer is not the base currency, the system shows the amount
   in the base currency at the day's exchange rate and asks the user to
   confirm it.
3. A report, "Multi-currency report" (the user's words: "double or multi
   currency report"), clean and easy to read, on web and phone.

## 2. What already exists (checked 2026-11-24)

- **Permitted currencies per company** (CR-6): table `company_currencies`,
  `Currency::permittedFor($companyId)`, a staff-only "Currencies" section on
  the company form, applied to the invoice, bill, quotation, purchase order,
  journal, payment and Logistics pickers, and to the API
  (`Support\Rules\PermittedCurrency`). **An empty list means every active
  currency** (decision 2026-11-13, so companies already invoicing in several
  currencies were not blocked).
- **TRACE365 in production** (read-only, 2026-11-24): no permitted list, so it
  sees all five active currencies (CDF, GHS, USD, ZAR, ZMW). Base USD.
- **One picker missed:** the purchase agreement currency uses `active()`
  only, not `permittedFor()`.
- **Which currency a document opens in:** `DocumentCurrency::for()` - the
  contact's default currency, else the journal's, else the company's.
- **Rates:** per company and date, synced daily by `currencies:sync-rates`.
  `Currency::canConvertTo()` refuses a conversion with no real rate (a
  missing rate is not 1).
- **The invoice's rate** is `accounts_account_moves.invoice_currency_rate`.
  `Move::computeInvoiceCurrencyRate()` recomputes it from the rate table at
  the invoice date on every compute, so **a rate typed by a user is
  overwritten today**. Lines take their company-currency amounts from it
  (`MoveLine`, `MoveCalculator`).
- **Showing the equivalent:** `Support\Support\CompanyCurrencyEquivalent`
  prints "≈ GH₵ X · 1 USD = 11.80 GHS" from the document's stored rate, on
  the invoice summary and in the lists.
- **Exchange differences at payment:** `ExchangeDifferenceRecorder` books the
  gain or loss when a payment's rate differs from the invoice's.

## 3. How others do it (research, 2026-11-24)

- **Zoho Books** converts every foreign-currency transaction into the base
  currency at that day's feed rate; the feed can be turned off and rates
  entered by hand each day; open foreign invoices are revalued separately
  under Accountant › Currency Adjustments, which shows the foreign balance,
  the original base balance and the revalued one before you confirm.
- **QuickBooks Online and Xero** (from general product knowledge, not
  re-checked against their documentation on 2026-11-24) show an "Exchange
  rate 1 USD = … GHS" field on the invoice itself once its currency differs
  from the home currency, filled from the day's rate and editable before
  saving. Verify before relying on it.
- **Odoo** (our upstream's model) takes the rate from the company's rate
  table at the invoice date, with no per-invoice override.

Common ground worth copying: the rate is visible on the document, it is
filled for you, it can be corrected before posting, and once posted it never
moves. What we add: an explicit question before the form, which suits clerks
who mostly invoice in the base currency and sometimes in dollars.

## 4. Design

### MC-1 A company sees only its currencies (small; hotfix candidate)

- Configure TRACE365's list as USD + GHS from the company form (Super Admin,
  "Currencies" section). This is data, done in the app so it is audited.
- Fix the purchase agreement picker to use `permittedFor()`; add it to the
  pickers test.
- A guard test that scans every `Select::make('currency_id')` and fails when
  one on a transaction form does not apply `permittedFor()` (configuration
  forms, such as the account and vendor-price currency, stay on an
  allow-list).
- The company form shows, under the Currencies section, which currencies the
  company sees right now ("No list: every active currency - CDF, GHS, USD,
  ZAR, ZMW"), so an empty list is never a surprise again.
- Decision for the user (D-MC-1): keep "empty list = every active currency",
  or make a new company start with its base currency only. Recommended: keep
  it, plus the visible line above; changing the default could block existing
  clients.

### MC-2 "Ask money equivalent" (company setting)

**Setting.** Accounting settings, per company, off by default:
"Ask which currency each sale is in". Stored in a per-company settings row,
not `CompanyAwareSettingsRepository` (it falls back to the default company's
values, which is wrong for an on/off switch; the Logistics
`logistics_company_settings` table is the pattern). Shown only when the
company has more than one permitted currency.

**Flow when it is on (new invoice, and new quotation):**

1. **The question, first.** Before the form, a small dialog: "What currency
   was this sale in?" with one large button per permitted currency, the base
   currency first and labelled "(company currency)". The contact's default
   currency is preselected when the customer is chosen first; otherwise the
   base currency is. On a phone it is a bottom sheet with full-width buttons
   of at least 48 px.
2. **Base currency chosen:** the form opens as today. Nothing else changes.
3. **Another currency chosen:** the dialog shows the day's rate, large and in
   words: "1 USD = 11.80 GHS · rate of 24 Nov 2026 (daily feed)". Two actions:
   **Use this rate** (primary) and **Enter a different rate** (opens a rate
   field: "1 USD = [ ] GHS"). A rate older than three days is labelled with
   its date and a warning. **No rate at all:** the user must type one, or
   choose the base currency; the system never assumes 1.
4. **The form** opens in the chosen currency with a slim rate bar under the
   header: "USD invoice · 1 USD = 11.80 GHS (confirmed by Ama, 24 Nov)" and a
   Change link that reopens step 3. Each total shows its base-currency
   equivalent under it (`CompanyCurrencyEquivalent`, already built).
5. **On Confirm (posting):** a short summary before the post: "Total USD
   1,200.00 = GH₵ 14,160.00 at 11.80. Post?" If the invoice date is not the
   rate's date, it says so and offers today's rate. After posting the rate
   never changes; payments book any difference through
   `ExchangeDifferenceRecorder`, as today.

**Data.** Two columns on `accounts_account_moves`: `currency_rate_source`
(`feed` | `manual`) and `currency_rate_confirmed_by`. While the source is
`manual`, `computeInvoiceCurrencyRate()` leaves `invoice_currency_rate`
alone; that is the one engine change, guarded by its own test (a recompute
must not overwrite a confirmed rate; a feed rate still follows the invoice
date). The confirmation is written to the chatter: who, which rate, from
where.

**What it does not do:** convert a USD sale into a GHS invoice. The invoice
stays in the currency the customer pays in; the base-currency value is shown
and posted at the confirmed rate. Decision for the user (D-MC-2): if TRACE365
wants instead to *issue* the invoice in GHS from USD prices, that is a
second mode ("convert to company currency"), the same dialog with the result
applied to the prices, planned only if asked.

**Where else:** quotations and sales orders use the same dialog (their rate
is `currency_rate`); bills and purchase orders get the setting later if
asked, with the vendor's currency preselected.

**Web and phone.** One Filament action modal; on a phone it is full screen
with the currency buttons stacked and the rate in 28 px type. No new
navigation: the dialog appears from "New invoice" and "New direct sale".

### MC-3 Multi-currency report

Accounting › Reporting › **Multi-currency**. Read-only, filtered by period,
currency (the company's permitted ones), and customers or vendors.

1. **One card per currency** (a row of cards, stacked on a phone):
   "USD · 14 invoices" with Invoiced, Received and Open in that currency,
   and under each, the base-currency value at the rates booked.
2. **By currency, side by side** (a table; phone: cards with labels):
   Currency · Invoiced · Average rate used · In GH₵ at booked rates ·
   Received · Open · Open in GH₵ at booked rates · Open in GH₵ at today's
   rate · Difference (what the open amount has gained or lost since).
3. **Exchange gains and losses booked** in the period (from the exchange
   difference entries), so the report agrees with the ledger.
4. **The documents** behind the figures: number, date, contact, currency,
   amount, rate, base amount, status; each row opens the document.

Rules: every figure states its currency; booked rates come from the
documents (`_signed` columns), "today's rate" only in the columns that say
so (DB-7 rules 1-2); a document with no usable rate is listed with "—" and
left out of the totals (DB-7 rule 5). Export to Excel through the same
filtered query (`getTableQueryForExport()`).

Design: the dashboards' look (DB-4/DB-9): white cards on the grey ground,
tabular figures, currency codes in a muted tag beside each amount, the
difference in red or green **with a sign** (never colour alone). A mockup on
the dashboards canvas before code, as for DB-4.

## 5. Order and size

| Step | What | Size |
| --- | --- | --- |
| MC-1 | TRACE365's list (data, today), purchase agreement picker, the picker guard test, the "sees right now" line | S |
| MC-2a | Setting, dialog, rate bar, confirm summary, manual-rate column and engine guard (invoices) | M |
| MC-2b | Same for quotations and sales orders | S |
| MC-3 | Mockup, then the report and its export | M |

## 5a. Built (develop, unreleased, 2026-11-24)

Decisions taken as recommended, under the user's 12-hour delegation of
2026-11-24 ("all decisions are yours"): D-MC-1 keep, D-MC-2 keep the invoice
in its currency, D-MC-3 three days, D-MC-4 anyone creating the invoice,
recorded under their name.

- **MC-1 done.** TRACE365's list is USD + GHS (production, through the
  company form); purchase agreement picker and the picker guard test
  (commit 85cfebf68); the company form's Currencies field now says what the
  company sees right now (`CompanyAdminContributions` helper text).
- **MC-2a built (invoices).** Setting: `accounts_company_settings`
  (`Account\Models\CompanySetting::forCompany()`), switched on the company
  edit page ("Currency question on new invoices",
  `CompanyCurrencyContributions`). Rules: `Account\Services\SaleCurrencyQuestion`
  (asked only when on and more than one permitted currency; base first; the
  feed rate with its date and a stale flag; "1 X = n Y" display with n of at
  least 1). Dialog: `CreateInvoice::askSaleCurrencyAction()`, opened with the
  page through `$defaultAction`. A rate equal to the feed's is not pinned; a
  corrected one is applied before the first save (`handleRecordCreation`), so
  every line is costed at it, with `currency_rate_source = manual` and
  `currency_rate_confirmed_by`, both deliberately not fillable. Engine:
  `Move::computeInvoiceCurrencyRate()` keeps a manual rate, and drops it when
  the currency changes without a new rate. A chatter note records the rate.
  *Rate bar, built the same day:* the line under the invoice's currency
  (`SaleCurrencyQuestion::keptRateText()`) names the rate kept for the sale,
  and who confirmed it once saved; it says so when a later currency change
  has dropped the rate. "Currency and rate" in the create page's header
  reopens the same dialog, so there is one set of rules. The wording of a
  rate is `SaleCurrencyQuestion::label()`, shared with the chatter note.
  *Not yet:* the posting summary, and the phone bottom-sheet styling (the
  dialog is Filament's modal, full width on a phone).
- **MC-2b (quotations, sales orders) not started.**
- **MC-3 built.** `Accounting\...\Reporting\Pages\MultiCurrency`, figures
  from `Accounting\Support\MultiCurrencyReport` (page and Excel export share
  them), permission `page_accounting_multi_currency`. Current company only:
  its base currency is the column everything converts into. Built without
  the mockup step; the user can ask for changes on the page itself.

## 6. Decisions for the user

- **D-MC-1** Empty permitted list: keep "every active currency" (recommended,
  with a visible line on the company form) or "base currency only".
- **D-MC-2** USD sale: keep the invoice in USD with its GHS equivalent
  (recommended) or issue it in GHS converted from USD.
- **D-MC-3** Rate older than how many days needs a warning: 3 (recommended).
- **D-MC-4** Who may type a different rate: anyone creating the invoice
  (recommended, it is audited) or only accountants.

## 7. Tests (to write with the code)

- Pickers: every transaction picker applies `permittedFor()` (guard scan).
- Engine: a confirmed manual rate survives a recompute and a line change; a
  feed rate follows the invoice date; posting keeps it.
- Flow: setting off, no dialog; on with one currency, no dialog; on, base
  chosen, form as today; on, USD chosen, rate stored with source and person;
  no rate, cannot continue without typing one.
- Report: known totals for two currencies at known rates, the difference at
  today's rate, a document with no rate excluded and shown with "—", export
  equals the table.
