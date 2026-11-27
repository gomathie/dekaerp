# Everyday flows in production: audit and improvement plan

> **Status (2026-11-25): decisions taken, work queued.** Each item ships as
> its own small hotfix (user rule for this project: mini fixes, shipped as
> soon as green). Audit made read-only in production on 2026-11-25.
>
> **Decisions (user, 2026-11-25):**
> - **D1** New invoice: **"Create and confirm"** is the main button; "Save as
>   draft" stays beside it.
> - **D2** Currency question **only when needed, as chips**: skipped when
>   the customer has a default currency; otherwise USD / GHS chips on the
>   form instead of a pop-up.
> - **D3** **"Deliver" button + sales order by default**, but only for
>   stocked goods (see services below).
> - **D4** Bill made from a purchase order is **dated on the goods-receipt
>   date**; the "Are you sure?" on Confirm Order **stays**. *(The audit
>   recommended today's date and removing the question; the user chose
>   otherwise.)*
> - **D5** Credit note **in one dialog** from the invoice (amount or lines,
>   reason; posts and applies or refunds).
> - **D6** **Manual "Send reminder"** on overdue invoices now; automatic
>   reminders later.
> - **D7** After choosing their password, a new user **stays signed in**.
> - **Four wrong things fixed first, as hotfixes:** delivery status
>   recalculated after the backfill; Needs attention also counts ready
>   deliveries (drafts already shipped in v1.17.4); vendor bills list
>   ("Vendor" heading, positive amounts, a Paid column, money columns
>   visible); invoice email (sender and company signature, user's
>   language, no "account N/A").
>
> **Services, not only goods (investor note, 2026-11-25; user decisions):**
> - Goods-only options ("Create sales order", "Customer has taken the
>   goods", "Deliver") appear **only when a line holds a stocked product**;
>   a service-only invoice shows none of them. D3's automatic order and
>   delivery apply to goods only.
> - **Follow Zoho's line** (checked live in HiTrace's Zoho, read-only,
>   nothing saved): **one box** per line that searches products *and*
>   services (showing their price), offers "+ Add new item" in place, and
>   accepts **any typed text** as a free line priced by hand. This replaces
>   the separate "Add a line without a product" button R-5 ships first. No
>   "what do you sell" company setting: the one box fits every market.
> - Wording for everyone: "Add Product" → "Add item"; column "Product" →
>   "Item / service"; "Quantity" → "Qty"; a description under each line,
>   printed on the invoice.
> - **Priority:** right after the four bug fixes and R-5, before D1–D7.
>
> **Paying an invoice, Zoho style (user, 2026-11-26):** Confirm, then a
> prominent **"Record payment"** button on the confirmed invoice (today's
> Pay, renamed). *Superseded:* "'Paid now' in the invoice Confirm dialog"
> (2026-11-25). It is dropped: one click more, but each step does one thing
> and no payment is recorded by accident. Record payment requires the
> invoices plugin's payment permission (`create_invoice_payment`); today Pay
> checks none. Company Admin and the business role keep it.
> "Convert to invoice" shipped in v1.17.11.
> The quotation view page's Confirm has no permission check of its own.
> **Add one if it is a small fix** (user, 2026-11-26): the same rule as
> Convert to invoice's first half, i.e. the user may edit the quotation.
> *Superseded the same day:* "leave it as it is ... Do not add one."
>
> **Zoho Books parity, from the first Zoho migration (user decisions,
> 2026-11-26; switchers from Zoho are the priority market):**
> - **Z1 Expenses screen**, shipped as a hotfix: amount, date, expense
>   account, paid from (a bank or cash journal), vendor, tax, note, receipt
>   file. It posts one balanced entry **in the chosen bank/cash journal**
>   (expense debited, money account credited), the same entry the importer
>   writes. Mockup first. Follow-ups (user, 2026-11-26): note-only or
>   receipt-only edits need no reason and no re-posting; an "Amount includes
>   tax" switch on the form, preset from the tax; then "Match to an expense"
>   in bank reconciliation, extending the existing suggestion rule.
>   *Built 2026-11-26 on `feature/expenses` (mockup approved), not yet
>   released:* Purchase/Invoices › Vendors › Expenses, posted through
>   `Account\Services\ExpenseRecorder` and the shared builder
>   `ExpenseEntry`; the three follow-ups are built on the same branch.
>   Details and verification in `docs/change-log.md`. Left for later as
>   the mockup said: billable to a customer, mileage, split receipts.
> - **Z2 Bank/cash journals take manual movements only through Expenses.**
>   Free-form manual entries there stay refused (`Move::getValidJournalTypes`
>   unchanged for everything else), so bank reconciliation is not weakened.
>   *Built 2026-11-26 with Z1:* `Move::isExpenseEntryInItsJournal()`, see
>   the protected decision in AGENTS.md.
> - **Z3 Receipts:** after the import is checked, a second pass downloads
>   the receipt and attachment files from Zoho (read-only) and attaches them
>   to the imported entries.
> - **Z4 Recurring invoices:** built on `feature/recurring-invoices` after Z1.
>   The Customer cluster in Invoices has a recurring-template list, form,
>   pause/resume/stop actions and issue history. The schedule generates one
>   standard draft invoice per period, not a parallel invoice type; it notifies
>   the responsible user and only confirms/emails when the template's opt-in is
>   on. “Create the next invoice now” creates the next scheduled period and
>   advances the schedule. Stopping a template leaves existing drafts alone.
>   Verification and limits are recorded in `docs/change-log.md`. Follow-ups
>   (user, 2026-11-26: ship now, then): the invoice screen's Zoho-style item
>   box on template lines, and "every N days/weeks/months". Each new
>   invoice is created as a **draft** and the user is notified to check and
>   send it; per template, "confirm and email automatically" can be switched
>   on (user, 2026-11-26). One money
>   feature in progress at a time; the scheduler must never invoice a period
>   twice (unique key per template and period, as platform invoices do).
> - **Z5 Small items:** map Zoho payment terms, units and item default taxes
>   after an import; a Won / Lost status on quotations; vendor bills and
>   vendor payments in the importer; Zoho salespeople linked to DEKA users.

Date: 25 November 2026. Prepared after TRACE365's call ("from quotation to
invoice to confirm invoice the flow is too long").

**How this was checked.** Production was opened as the test Super Admin, with
the session switched to TRACE365. Pages, forms and dialogs were opened and
then left. Nothing was saved, confirmed, posted, paid, sent or validated.
Usage figures come from read-only counts in the production database, and the
code on `develop` was read to count clicks. Each step below is marked
**(seen)** when it was seen live, or **(code)** when it was read in the code
only.

**Already being fixed, so not proposed again:** quotation to invoice ("Convert
to invoice" opens the draft invoice, "Create invoice" on an order opens the
invoice, "Paid now" in the invoice Confirm dialog). Payment improvements
U95 to U100 are also decided: one payment for several invoices, offer unused
credit, remember the way to pay, show the balance after payment, and refund
unused credit. U97 (offer unused credit), U98 (remember the way), U99 (balance after payment), U95 with U96 (one payment for the amounts chosen on a customer's open invoices), and U100 (refund the unused amount, Company Admin and up) are built on `feature/backlog-b` (2026-11-27). Both are mentioned below where they touch a flow.

**How much TRACE365 uses each flow** (production counts, 25 Nov 2026):

| Flow | TRACE365 |
|---|---|
| Customer invoices | 15 (12 posted and paid, **3 still draft**: one from June, two from today) |
| Invoices paid on the same day they were made | 10 of 12. **None** used "Paid now": each was created, then confirmed, then paid in separate steps |
| Sales orders | 16 (14 confirmed, 11 of them made from invoices) |
| Deliveries | 17 (1 done, **4 ready but never validated**, 10 cancelled by the November backfill, 2 receipts done) |
| Purchase orders and vendor bills | 2 and 2, all paid |
| Credit notes | 0 |
| Currency of documents | 17 of 17 in USD. No contact has its own default currency |
| Users | 3 |

Across the other 6 companies there is almost no activity yet: 1 invoice and
1 sales order. TRACE365 is where the time goes.

---

## Broken or wrong: noticed during the audit

1. **The Sales "Order activity" tile says 14 orders are waiting for delivery
   and 0 have been delivered (seen).** The real figure is 4. Ten orders had
   their deliveries cancelled by the November backfill ("customer has taken
   the goods" for past sales), but each order kept its old delivery status of
   "pending". The rule the code uses today would say "nothing to deliver" for
   those ten. They were simply never recalculated after the backfill. Fix: a
   one-off recalculation of the delivery status of TRACE365's orders, and the
   backfill command should recalculate as it goes. Size: Small. Risk: low.
2. **"Needs attention: All clear. Nothing is waiting on you" (seen).** At the
   same time, 3 invoices are still in draft ($607.50, one of them from June)
   and 4 deliveries are ready to ship. The panel does not count either one.
   See fix 2 in the mini-fix list.
3. **Vendor bills list (seen).** The column is headed "Customer" instead of
   "Vendor". Totals show as negative (-$2,750.00), and on a 1440 px desktop
   screen the money columns are pushed off to the right, so you have to
   scroll sideways to see them. The bills list also has no "Paid" column,
   which the invoices list has.
4. **A bill made from a purchase order cannot be confirmed as it is (code).**
   "Create Bill" makes the bill without a bill date. Confirm then refuses
   with "Please provide a valid Bill/Refund Date". The person has to open
   Edit, pick the date, Save and only then Confirm.
5. **The invoice e-mail text is fixed and in English (code).** "Print &
   Send" fills in a message signed "Best regards, Administrator" whoever
   sends it. When the invoice has no bank account it also says "for account
   N/A". It is in English whatever the user's language.
6. **"New direct sale" appears on the Credit Notes list (seen).** There it
   opens an invoice, which is confusing on a credit-notes screen.
7. **A return is left half-done (seen).** "Return of TR365/OUT/00024" has
   been "Ready" since 23 Nov. Order SO/00015 therefore shows as partly
   delivered.
8. **Sentry, last 7 days (DEKAERP project).** Three "N+1 Query" warnings on
   the invoice page (DEKAERP-27, -28, -2A, all from today), one Redis
   connection timeout (DEKAERP-29), and `/api/docs.openapi` failing because
   the generated file is missing (DEKAERP-26). None stops a user, but the
   invoice page is the busiest page TRACE365 has.

---

## 1. Summary: the biggest time-wasters

Ranked by how often TRACE365 hits them multiplied by the clicks saved.

| # | Time-waster | Clicks today → after | How often |
|---|---|---|---|
| 1 | **Delivering after a sale is a separate trip** to Inventory, so it gets skipped: 4 deliveries are waiting. Dashboards are wrong because of it (items 1 and 2 above) | 4–6 clicks over 3 screens → **1 click** ("Deliver" on the invoice or order) | Every sale of goods (GPS trackers) |
| 2 | **Invoices are left in draft.** Confirm is a separate step after Create, and nothing reminds anyone of drafts | Create + Confirm = 2 steps → **1** ("Create and confirm"), plus drafts shown in Needs attention | Every invoice. 3 of 15 are stuck today |
| 3 | **The currency question appears on every new invoice and quotation**, though 17 of 17 documents were in USD | +1 to 2 clicks on every document → **0** for customers with a known currency | Every invoice and quotation |
| 4 | **Purchase to paid bill** takes about 24 clicks over 7 screens. "Create Bill" stays on the order, and the bill has no date, so Confirm fails | about 24 → **about 12** | Each supplier shipment (rare today, but every import of trackers) |
| 5 | **Chasing unpaid invoices has no tool**: no reminder, no customer statement, and the e-mail text is fixed | Today: write an e-mail outside DEKA → **2 clicks** "Send reminder" | Every overdue invoice (none overdue today, so lower priority for TRACE365) |
| 6 | **The invoice form shows fields almost nobody changes** (Journal, Due date beside Payment term, Sales person). 9 boxes are visible before the first line | 9 → **4 visible** (customer, date, paid now, lines), the rest under "More options" | Every invoice. Saves scanning, not clicks |
| 7 | **A credit note takes 5 screens:** Credit Note → typed reason → Submit → lands on a draft → Confirm → back to the invoice → Add credit | about 7 clicks → **2** | Rare today (0 so far), but each return of a faulty tracker |
| 8 | **Adding a user takes a 16-field form** with words like "Resource Permission" and "Allowed Companies" | 16 fields → **4** (name, e-mail, what they do, invite or password) | Each new staff member |

---

## 2. Flow by flow

### Flow 1: invoice to a walk-in or known customer

**Today (seen, TRACE365).**

*Walk-in, paid at once ("New direct sale"):*
1. Invoices → **New direct sale** (1 click). The form opens with Walk-in
   Customer, Paid now (Cash), Create sales order and "Customer has taken the
   goods" already set.
2. **"What currency was this sale in?"** dialog → **Continue** (1 click; 2 if
   GHS).
3. **Add Product** (1), pick the product (2), change the quantity if needed
   (type).
4. **Create** (1).

Total: **6 clicks, 1 screen.** This is good. It is already simpler than Zoho,
which has no pay-on-create (U42). Production shows it has not been used yet:
no TRACE365 payment was recorded at the moment of its invoice.

*Known customer, pays later or the same day:*
1. **New Invoice** (1) → currency dialog **Continue** (1).
2. Customer: open the list and choose (2). If the customer is new, use
   **"+"**, which asks for name, e-mail and phone (seen); it does not carry
   over the typed name (U45).
3. **Add Product** (1) → product (2). Product **"+"** asks name, type and price
   (code; U43 done).
4. **Create** (1). This lands on the invoice page, still a draft.
5. **Confirm** (1).
6. Then the same day: **Pay** → **Pay** (2).

Total: **about 12 clicks over 2 screens**, and step 5 is the one that gets
forgotten (3 drafts today).

**What slows it down**
- The currency dialog blocks the form on every new invoice, while every
  TRACE365 document so far has been USD. No contact has a default currency
  set, so the contact rule (v1.16.7) never helps.
- Create and Confirm are two separate buttons on two pages.
- Nine boxes sit above the lines (seen): Customer, Sales Person, Invoice
  Date, Paid now, Create sales order, Due Date, Payment Term, Journal,
  Currency. "Journal" has one choice ("Customer Invoices"). "Due Date" and
  "Payment Term" are two ways of saying the same thing.
- "Create sales order" is unticked on a normal new invoice. So TRACE365's
  usual sale (an invoice for trackers) only reaches Inventory when someone
  remembers to tick it.
- The lines start empty: "Add Product" is one extra click every time.

**How others do it**
- Zoho Books: one form with "Save as Draft" or "Save and Send"; no separate
  confirm step. <https://www.zoho.com/us/books/help/invoice/>
- Odoo: draft, then Confirm, then Send (the same two steps as ours).
  <https://www.odoo.com/documentation/18.0/applications/finance/accounting/customer_invoices.html>

**Proposed**
1. A **"Create and confirm"** button beside Create on new invoices (primary).
   "Create" keeps making a draft. This works with the "Paid now in the
   Confirm dialog" fix already in progress: the dialog shows on that one
   click.
2. **Currency question only when it matters:** skip it when the customer has
   a default currency, and show the currency as two chips at the top of the
   form (USD / GHS) instead of a blocking dialog. Needs a decision (D2).
3. **One empty line already open**, so the product box is ready (saves 1
   click).
4. **Journal, Payment Term/Due Date and Sales Person under "More options"**,
   closed by default, when the company has a single sales journal. Due date
   defaults to today, or comes from the customer's payment term.
5. **"Create sales order" ticked by default** for companies that use
   Inventory and sell stocked goods (a company setting). Needs a decision
   (D3).
6. The customer "+" carries over what was typed (U45, already proposed).

**After:** known customer, paid later: **8 clicks** (New, customer 2, product
2, Create and confirm, then Pay 2). Walk-in stays at 5–6.

**Size:** 1 Small, 2 Medium, 3 Small, 4 Small, 5 Small. **Risk:** low.
"Create and confirm" calls the existing confirm service, so the same checks
apply (lock date, lines required).

### Flow 2: record a payment received

**Today (seen and code).** From a posted invoice: **Pay → Pay** (2 clicks,
3 fields, all prefilled). From the Payments list: New Payment shows 8 fields
(seen: Payment Type, Customer, Amount, Currency, Date, Memo, Journal,
Payment Method), and the payment must then be applied invoice by invoice
(about 10 clicks over 3 screens).

**Already decided:** U95 to U100. Nothing new is proposed here except:
- The Payments list mixes customer and vendor payments under "Default". Open
  it on **Customer payments** when reached from Invoices, and on **Vendor
  payments** from Bills (Small).
- U40 (reference beside the date for bank and Mobile Money) is still open.
  It matters for matching in bank reconciliation, which shipped in v1.17.2.

### Flow 3: purchase: RFQ → purchase order → receive → bill → pay

**Today.**
1. Purchase → **New RFQ** (1). The form is lean (seen): Vendor, Vendor
   Reference, Order Deadline (filled), Expected Arrival, Currency (filled),
   Deliver To (filled). Vendor (2), **Add Product** (1), product (2), price
   (type).
2. **Create** (1). This goes to the edit page (code).
3. **Confirm Order** (1) and its "Are you sure?" dialog (1). The order stays
   on the RFQ screen (code).
4. **Receipts** tab (1) → open the receipt (1) → **Validate** (1)
   (seen: the receipt page has Validate at the top).
5. Back to the order (1–2) → **Create Bill** (1). It stays on the order with
   a "Bill created" message (code).
6. **Vendor Bills** tab (1) → open the bill (1). The bill has **no bill
   date** (code), so: **Edit** (1) → pick the date (2) → **Save** (1) →
   **Confirm** (1).
7. **Pay** (1) → **Pay** (1).

Total: **about 24 clicks over 7 screens.**

**What slows it down:** Create Bill does not open the bill. The bill
cannot be confirmed without a date. Receipt and bill are tabs to hunt for.
Confirm asks "Are you sure?" for something that can be undone.

**How others do it**
- Odoo: Create Bill opens the draft bill straight away. Set the bill date,
  Confirm, Register Payment. A "Receipt" smart button opens the receipt.
  <https://www.odoo.com/documentation/18.0/applications/inventory_and_mrp/purchase/manage_deals/control_bills.html>
- Zoho Books: "Convert to Bill" from the purchase order, which opens a
  prefilled bill. <https://www.zoho.com/us/books/help/purchase-order/>

**Proposed**
1. **Create Bill opens the bill**, the same change as "Create invoice" on a
   sales order (in progress).
2. **The bill is dated today** when made from an order. The person can still
   change it before confirming. (D4: today, or the receipt date.)
3. **"Receive" button on a confirmed order** that opens its receipt (or the
   only open receipt) directly, like Odoo's smart button.
4. **No "Are you sure?" on Confirm Order.** Cancel and "Back to draft" exist.
   (Optional; D4 asks.)
5. Bills list: "Vendor" heading, positive amounts, a "Paid" column, money
   columns visible without scrolling (broken item 3).

**After:** about **12 clicks over 4 screens** (RFQ 6 → Confirm 1 → Receive 2
→ Create Bill 1 → Confirm 1 → Pay 2, minus the edit loop).

**Size:** 1 Small, 2 Small, 3 Small, 4 Small, 5 Small. **Risk:** low.
Billing still goes through `Biller`, which keeps the module check.

### Flow 4: bill without a purchase order

**Today (seen).** Bills → **New Bill**: Vendor*, Bill Reference, Bill Date*,
Accounting Date, Payment Reference, Recipient Bank, Due Date*, Payment Term,
Journal*, Currency*, then lines. That is **10 boxes** before the lines.
Create → Confirm → Pay → Pay: about **10 clicks**.

**What slows it down:** Bill Date is empty and required. Accounting Date,
Payment Reference, Recipient Bank and Journal are rarely changed. There is
no "Paid now" for a bill paid on the spot (fuel, transport, small
purchases).

**How others do it:** Zoho has one bill form with "Save as Open" and records
payment from the bill. <https://www.zoho.com/us/books/help/purchase-order/>
links to the bill help.

**Proposed**
1. Bill Date defaults to today (Small).
2. Accounting Date, Payment Reference, Recipient Bank and Journal go under
   "More options" (Small).
3. **"Paid now"** on a new bill, reusing the invoice's QuickPayment
   ("How was it paid?"). Medium, because it reuses the same service and
   checks.
4. "Create and confirm", as in Flow 1.

**After:** about **6 clicks**. **Risk:** low.

### Flow 5: delivery of stock after a sale

**Today.**
- *Direct sale with "Customer has taken the goods"* (seen on the form): the
  delivery is validated on confirm. **0 extra clicks.** Good.
- *Invoice without that box, or a sale from a quotation* (seen): the order
  shows a "Deliveries 1" tab. Open the order (1) → **Deliveries** tab (1) →
  the delivery row (1, or its Actions menu → View) → **Validate** (1).
  Validate asks nothing when everything is reserved, and asks about a
  backorder for a partial delivery (code). From an invoice there is no link
  to its delivery at all: you go Sales → Orders → find the order first.
  Total: **4 to 6 clicks over 3 screens**, and the invoice page gives no
  sign that goods are still to go out.

Production: 4 deliveries "Ready", never validated (2 of them for today's
quotation-to-invoice sales), and 1 return left "Ready".

**How others do it:** Odoo puts a "Delivery" smart button on the sales
order; open it, set the quantity, Validate.
<https://www.odoo.com/documentation/18.0/applications/inventory_and_mrp/inventory/shipping_receiving/daily_operations/receipts_delivery_one_step.html>
Zoho (with inventory) has "Mark as Shipped/Delivered" on the package.

**Proposed**
1. **"Deliver" button on the invoice and on the sales order** while a ready
   delivery exists. It validates the reserved quantities in one click (same
   service as Validate). When stock is short it opens the delivery instead
   of forcing anything (no negative stock, as decided on 2026-11-22).
2. A **"To deliver" badge** beside the invoice status ("Paid · 1 to
   deliver").
3. Deliveries list: a **bulk "Validate"** for selected ready deliveries
   (each one checked the same way).
4. Fix the dashboard figure (broken item 1).

**After:** **1 click** from the invoice or order. **Size:** 1 Medium,
2 Small, 3 Small, 4 Small. **Risk:** medium. Validating stock is a real
movement, so the button must only do what Validate does today, with its own
test for "short stock leaves the delivery open". Needs a decision (D3,
together with "Create sales order" by default).

### Flow 6: add a customer or product while invoicing ("+")

**Today (seen).** Customer "+" asks Name*, E-mail, Phone → Create. Product
"+" on the line asks Name*, Type*, Price* (code). Both stay on the invoice.
This is good.

**What slows it down:** the customer "+" does not reuse the typed search
text (U45). The full contact form (seen: 36 fields, 10 sections, with
"Account Receivable*" and "Account Payable*" marked required) is what
people meet if they add a customer from Contacts. The full product form
(seen: 26 fields; Category*, Company*, Price*, Cost* required) is what they
meet from Products.

**How others do it:** Zoho's "New Customer" keeps the typed name and asks
for a short set: type, contact, e-mail, phone, currency, terms (U45 notes).

**Proposed**
1. U45 (prefill the typed name), Small, already proposed.
2. Add **"Currency"** to the customer "+" (USD/GHS). This feeds the
   currency rule and lets the currency question be skipped (Flow 1, item 2).
3. On the full contact form, open only General and Address. Fold the
   accounting tabs (receivable and payable accounts are already filled in).
   Small.

### Flow 7: credit note or refund for a customer

**Today.**
1. On the posted invoice, **Credit Note** (1). The dialog (seen) asks
   "Reason displayed on Reverse*", Journal* (filled), Date* (filled) →
   **Submit** (1).
2. It lands on the **draft credit note's edit page** (code), with all lines
   copied. For a partial credit, edit the lines and Save.
3. **Confirm** (1).
4. If the invoice was unpaid: back to the invoice (1–2) → Outstanding
   credits → **Add** (1). If it was paid and money goes back: on the credit
   note, **Pay** → **Pay** (2) to record the refund.

Total: **about 7 clicks over 3 screens**, plus the reason typed. The label
"Reason displayed on Reverse" is developer wording.

**How others do it**
- Odoo: Credit Note → Reason → "Reverse" (draft) or "Reverse and create
  invoice". For a refund, register the payment afterwards.
  <https://www.odoo.com/documentation/18.0/applications/finance/accounting/customer_invoices/credit_notes.html>
- Zoho: the credit note is created, then "Apply to invoices" or "Refund".
  <https://www.zoho.com/us/books/help/credit-note/>

**Proposed**
1. The dialog asks **what to credit**: "Whole invoice" (default) or "Some
   lines", and **what happens to the money**: "Take it off the invoice"
   (unpaid) or "Pay it back" / "Keep as credit" (paid). Submit then confirms
   and applies (or records the refund) in one go. "Some lines" opens the
   draft as today.
2. The label becomes "Reason (shown on the credit note)".

**After:** **2 clicks** for a whole-invoice credit. **Size:** Medium.
**Risk:** medium. It posts a document, so it needs the same lock-date
checks and its own tests. Needs a decision (D5).

### Flow 8: send an invoice or quotation by e-mail, or print it

**Today.** Invoice (posted only): **Print & Send** (1) → dialog prefilled
with recipients, subject, message and the PDF → **Send** (1). Printing:
**Preview** → **Print** (2) (code; the Print & Send dialog was not opened
live, because opening it saves a PDF file). Quotation: **Send by e-mail**
with the same shape (code).

**What slows it down:** the message is fixed English, signed "Administrator",
and may say "account N/A" (broken item 5). The recipients list loads every
contact the company has. A draft cannot be sent, so "send a proforma"
means confirming first.

**How others do it:** Zoho has "Save and Send" on the invoice form itself,
and "Save and Send Later". <https://www.zoho.com/us/books/help/invoice/>
Odoo has "Send" after Confirm, with a Download option.

**Proposed**
1. Message signed with the **sender's name and the company**, in the
   user's language, and no bank line when there is no bank (Small).
2. A **"Create, confirm and send"** choice in the "Create and confirm" menu
   (Small, after Flow 1 item 1).
3. Company-editable templates for the e-mail text (Medium, later).

### Flow 9: find an unpaid invoice and chase it

**Today (seen).** Invoices list → **To Pay** or **Overdue** tab (1). Aged
Receivable report under Accounting → Reporting, with Excel and PDF export.
There is **no reminder action** (code: no reminder or follow-up feature in
accounts, invoices or sales) and **no customer statement**. The only
automatic message is the SMS when a payment is received.

**How others do it**
- Zoho: automatic reminders before and after the due date (Settings →
  Reminders), plus "Send Now" on an overdue invoice.
  <https://www.zoho.com/us/books/help/settings/reminders.html>
- Odoo: follow-up levels, a "Requires follow-up" filter and "Process
  follow-ups". <https://www.odoo.com/documentation/18.0/applications/finance/accounting/payments/follow_up.html>

**Proposed**
1. **"Send reminder"** on an overdue invoice and as a bulk action on the
   Overdue tab. It uses the Print & Send path with a reminder message and the
   balance due (Small to Medium). The protected rule that a document is
   e-mailed only to its own company's contacts still applies.
2. **Customer statement** PDF (open invoices and payments for one customer)
   from the customer page (Medium).
3. Automatic reminders on a schedule (Large; later, D6).

TRACE365 has no overdue invoice today, so this ranks below the sales flows.

### Flow 10: month-end: what was sold, collected and spent

**Today (seen).** The Overview opens on Year to date with one-click periods
(This month, Last 30 days, Year to date, Last year). It has Sales, Purchases,
Invoices and bills, Accounting and Inventory cards. Lists show "This month"
cards (invoices, bills, payments). The Payments list filters by year and
month and totals what it shows (R-7). The Reports page lists 15 reports in 4
groups, each viewable before export.

**What slows it down:** the wrong "To deliver" and "Needs attention"
figures (broken items 1 and 2). There is no single "Month summary". The
owner opens Overview, then Payments, then Bills. "Cash at bank
(reconciled)" shows $0 until reconciliation is done.

**How others do it:** Zoho's dashboard leads with receivables, payables,
cash flow, and income and expense for the period (U47).

**Proposed**
1. Fix the two figures (broken items 1 and 2), Small.
2. A **"Month summary" PDF** from the Overview period bar: sold
   (invoiced), collected (payments received), spent (bills and payments
   out), owed to us, owed by us, using the same queries as the cards
   (Medium). This also serves R-7's tax use.
3. Dashboard plan #2 continues as planned. No new dashboard work proposed.

### Flow 11: first-time setup of a new company user

**Today (seen).** Settings → Users: **New User** (16 fields: Name*,
E-mail*, Username, Password*, Password Confirmation, "Require a new password
at next sign-in", Roles*, Resource Permission*, Teams, See dashboard, See
money figures, Avatar, Preferred Language, Status, Allowed Companies,
Default Company*) or **Invite User**. At first sign-in the person is sent to
Profile to choose their own password (U26), then signed out and back in
(U27). What they see after that depends on the role. The Super Admin's view
was checked; another user's view was not, because impersonation was not
allowed in this audit.

**What slows it down:** "Resource Permission" (Global / Individual / Group)
and "Allowed Companies" are system words. Roles are a long list, while role
templates exist (2026-11-14). Signing out after the password change is an
extra sign-in.

**How others do it:** Zoho: invite by e-mail and pick one role. Odoo:
name, e-mail, and an access level per app.

**Proposed**
1. For a Company Admin: an **"Add user"** dialog with Name, E-mail, **what
   they do** (role templates: Sales, Accountant, Storekeeper…), and "Send
   invitation" or "Set a password now". It is the same shape as "Add
   administrator" (U61) and goes through the same seat and role checks.
   Medium.
2. Keep the user signed in after they choose their own password (D7).
   Small.
3. A short **first-day checklist** on the dashboard for a new company:
   warehouse (U57), products, a customer, first invoice. Medium, later.

---

## 3. Mini fixes, in order (each one its own small hotfix)

1. **Recalculate the delivery status** of orders whose deliveries were
   cancelled by the backfill, and recalculate inside the backfill. This
   fixes "To deliver 14". (S)
2. **"Needs attention" counts draft invoices and ready deliveries**, each
   linking to its list ("3 invoices not confirmed", "4 deliveries ready").
   (S)
3. **Bill made from a purchase order is dated today**, so Confirm works
   first time. (S)
4. **"Create Bill" opens the bill**, the same change as the sales "Create
   invoice" fix in progress. (S)
5. **"Create and confirm" button** on new invoices (and new bills). (S)
6. **Skip the currency question when the customer has a default
   currency**, and add Currency to the customer "+". (S, after D2)
7. **Invoice and bill form: Journal, Payment Term/Due Date, Accounting Date,
   Recipient Bank and Payment Reference under "More options"**; Bill Date
   defaults to today; one empty line open. (S)
8. **Vendor bills list**: "Vendor" heading, positive amounts, a "Paid"
   column, money columns visible. (S)
9. **Invoice e-mail** signed by the sender and company, in the user's
   language, with no "account N/A". (S)
10. **"Receive" button on a confirmed purchase order** that opens its
    receipt. (S)
11. **"Deliver" button** on the invoice and the sales order, with a
    "to deliver" badge. (M, after D3)
12. **"Send reminder"** on overdue invoices (single and bulk). (S–M)
13. Remove **"New direct sale"** from the Credit Notes list; the Payments
    list opens on customer or vendor payments by where it is opened from;
    the credit-note label becomes "Reason (shown on the credit note)". (S)
14. **Credit note in one step** (whole invoice, apply or refund). (M, after
    D5)
15. **"Add user" dialog** for Company Admins. (M)
16. **"Paid now" on new bills.** (M)
17. **Month summary PDF**, then **customer statement**. (M each)

Items 1 to 4 are fixes to wrong behaviour and can ship as TRACE365
hotfixes. Items 5 to 10 are simplifications TRACE365 will feel on every
document.

---

## 4. Decisions needed from the owner

**D1. Should "Create and confirm" be the main button on a new invoice?**
Built 2026-11-27 on `feature/backlog-b`. The choice was A, with the words
"Save as draft". Customer invoices only. Paid now still confirms once.
- A: "Create and confirm" is the main (orange) button, and "Create as draft"
  is secondary. *(Recommended: TRACE365 confirms almost every invoice the
  same day, and drafts get forgotten.)*
- B: Keep "Create" as the main button and add "Create and confirm" beside
  it.
- C: No change; rely on the reminder in Needs attention.

**D2. The "What currency was this sale in?" question (on for TRACE365).**
- A: Ask only when the customer has no default currency, and put the choice
  on the form as two chips instead of a blocking dialog. *(Recommended.)*
- B: Keep the dialog, but skip it when the customer has a default currency.
- C: Turn it off for TRACE365 until they invoice in GHS (they can switch it
  back on in settings).

**D3. Deliveries for invoiced goods.**
- A: "Create sales order" ticked by default for companies with Inventory,
  plus a "Deliver" button on the invoice and order. *(Recommended: the
  goods then always reach stock and the dashboards.)*
- B: Only the "Deliver" button; the box stays unticked.
- C: Tick "Customer has taken the goods" by default too (no delivery step
  at all unless unticked).

**D4. Purchase flow.**
- Bill date when made from an order: A today *(recommended, simplest)*,
  or B the receipt date.
- "Are you sure?" on Confirm Order: A remove it *(recommended; the order
  can be cancelled)*, or B keep it.

**D5. Credit notes.**
- A: One dialog: whole invoice or some lines, then "take it off the
  invoice", "pay it back" or "keep as credit", posted in one go.
  *(Recommended.)*
- B: Keep the draft step, but add "Confirm and apply" on the draft.
- C: No change until TRACE365 issues its first credit note.

**D6. Chasing unpaid invoices.**
- A: Manual "Send reminder" now; automatic reminders later.
  *(Recommended.)*
- B: Build automatic reminders (before and after due date) straight away.
- C: Wait until a client has overdue invoices.

**D7. After a new user chooses their own password.**
- A: Keep them signed in and take them to the dashboard. *(Recommended.)*
- B: Keep today's behaviour (signed out, sign in again).

---

## What could not be checked

- The Pay dialog was not seen live on a TRACE365 invoice: every posted one is
  already paid. Its fields come from the code and from the 2026-11-20 review.
- The Print & Send dialog was not opened: opening it saves a PDF to storage.
  Its content is from the code.
- Validate, Confirm, Create Bill, the reversal itself and every save were
  not run. Their results (where they redirect, the bill date error) come
  from the code.
- What a non-admin TRACE365 user sees: impersonation was not allowed, so
  only the Super Admin view was checked.
- Phone layout was not re-checked; U84 (2026-11-23) covered it.
- Switching the session to TRACE365 wrote the normal "company context
  switched" audit entries for the test account; nothing else was written.

Screenshots: session scratchpad, `flowaudit/a1-*.png` to `a7-*.png`.
