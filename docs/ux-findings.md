# UX findings

Running log of UI, look-and-feel and ease-of-use improvements noticed while
testing (user rule, 2026-11-12: record them during every test session, judged
by modern design standards). Each entry: flow, what was seen, proposal, effort
(S = under half a day, M = 1–2 days, L = more), status. Built items move to
`docs/change-log.md`; bigger ones become plan packages.

## Company onboarding (2.1 plan CR-1 / CR-4)

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U1 | New Company form: 8 sections, 22 fields, all open at once; only 3 required | One short card with the essentials (name, country, plan, admin name, admin email); everything else in a collapsed "More details" section | M | Proposed (CR-4) |
| U2 | No plan on the create form; subscription is a second trip to another screen | Plan select on the form (Pilot puts the price plan on account creation) | M | Proposed (CR-4) |
| U3 | No administrator name; the account is named from the e-mail | "Administrator name" field next to the e-mail | S | Proposed (CR-4) |
| U4 | Currency did not follow country | Country fills currency on create | S | **Done** 2026-11-12 (`6d59d0cf6`) |
| U5 | After saving, nothing says what happens next (invitation sent? login ready?) | Success screen: "Invitation sent to …", copy-link button, "Open company" | S | Proposed |

## Plugins page

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U6 | Install dialog stays open and silent for minutes; looks broken | Background install with "Installing…" toast and a bell notification when done | M | Built, waiting for a supervised production install |
| U7 | Icons were old filled artwork, black in dark mode | Outline set with readable strokes | S | **Done** 2026-11-12 |
| U8 | Installing a plugin does not say it must then be switched on per company (Attendance, Logistics) | After install: "Next: switch it on for your companies" with a link to its settings page | S | Proposed |

## Attendance

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U9 | Installed but invisible: no way to switch it on, not in the HR sidebar | Settings page with Switch on; own HR sidebar entry | M | **Done** 2026-11-12 |
| U10 | When off, the HR "Attendance" entry opens only settings; first-time users may not know why the list is missing | Empty state on the settings page: "Attendance is off for this company. Switch on to let employees clock in from the dashboard." with the button inline | S | Proposed |

## Dashboards

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U11 | Greeting used server time, repeated the company, old chip style | Modern greeting: local time, one line, outline pills | M | **Done** 2026-11-12 |
| U12 | Page title under the greeting as large as the greeting | Title reduced to 20px under the banner | S | **Done** 2026-11-12 |
| U13 | Charts show "No data for this period" but give no next step | Empty state with one action ("Create a quotation", "Record a bill") per chart | S | Proposed |
| U14 | At-a-glance cards are not clickable | Each card links to its list (orders, quotations, invoices, bills, receipts, deliveries), only for viewers who may open it | S | Done 2026-11-12 |
| U18 | Greeting background barely visible (two faint glows) | One soft diagonal gradient in the panel's primary colour, fading out before the clock | S | **Done** 2026-11-12 |
| U19 | Overview section tabs run past the edge at 390 px ("Invoices &…" cut mid-word), no sign the row scrolls | Edge fade only on the side with hidden tabs; selected tab scrolled into view | S | **Done** 2026-11-12 |
| U20 | A mistyped record address (/admin/invoices/overview) showed "Server Error" (Sentry DEKAERP-15) | Record addresses accept digits only, so it is a normal "Not found" | S | **Done** 2026-11-12 |

## Profile menu

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U15 | Version showed 1.5.0 | Version from config, linked to What's New | S | **Done** 2026-11-12 |

## What's New

Checked on production 2026-11-12 (desktop and 390 px): search, category pills, no-match message, page and per-release PDFs all work.

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U16 | Clearing the search or a filter leaves every release expanded (721 items) instead of returning to the latest one open | On clear, restore the starting state: only the latest release open | S | **Done** 2026-11-12 |
| U17 | "Search release notes" appears twice: as a visible label and as the placeholder | Keep the label for screen readers only (sr-only), placeholder stays. Cause: the label already had `sr-only`, but the compiled support.css lacks that utility; the page now defines it | S | **Done** 2026-11-12 |

## Production acceptance run (CR-3, 2026-11-14)

Throwaway "test" company in production, Super Admin creating it, administrator
and a second user signing in with set passwords (no e-mail).

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U21 | Choosing Ghana as country left the currency on USD: the form presets USD, so "country fills currency" (U4) only works on an empty field | Fill currency from the country whenever the currency was not touched by hand | S | Not a bug: works, the server round trip took about 5 s |
| U22 | Country sits inside the collapsed "Address Information" section | Country next to the company name in the first card | S | Proposed (U1) |
| U23 | "Administrator name" and "must change password" appear only after a password is typed | Show the name field always; it is needed for invitations too | S | By design: invited people choose their own name |
| U24 | Module checkboxes show internal keys (`inventories`, `time-off`, `accounts`) | Show the plugin's display name and icon | S | **Done** 2026-11-14 |
| U25 | Plan list is empty with no hint why (no plans exist in production) | Empty state: "No plans yet - create one under Billing → Plans" with a link | S | **Done** 2026-11-14 |
| U26 | After first sign-in the administrator lands on Profile with no explanation that the password must be changed | Banner: "Choose your own password to continue" with the dialog already open | S | **Done** 2026-11-14 |
| U27 | Changing the password signs the user out without saying so | Notification "Password changed - sign in again with your new password" on the login page | S | **Done** 2026-11-14 |
| U28 | Companies list "Status" column shows a bare "Yes" | Badge "Active" / "Inactive" | S | Not a bug: check icon, "Yes" is its screen-reader text |
| U29 | Role "HR Manager" displays as "Hr Manager" | Show the stored name instead of a headline-cased one | S | **Done** 2026-11-14 |
| U30 | Activity log subject shows "User #6" instead of the person's name | Show the record's name with the id as a tooltip | S | **Done** 2026-11-14 |

## Invitation e-mail (seen in Gmail, 2026-11-16)

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U31 | "You have been invited to join DEKA Central To accept the invitation - click on the button below": missing full stop, a dash where a colon belongs | "You have been invited to join {company} on DEKA Central. To accept, click the button below and create your account." | S | Done 2026-11-16 |
| U32 | The e-mail never names the company or who invited you | Name the company and the inviting person; a stranger's invite with no names looks like phishing | S | Done 2026-11-16 |
| U33 | "If you did not expect to receive an invitation to this team" in a monospace font, and "team" is the wrong word | Same font as the body; "this company" | S | Done 2026-11-16 |
| U34 | No expiry date or plain link | Add "This link expires on {date}" and a copyable link under the button | S | Done 2026-11-16 |

## Recording a payment, compared with Zoho Books (2026-11-20, plan #11 R-3)

Zoho Books: open the invoice → Record Payment → Amount received (full
amount), Payment date, Deposit to / Payment mode (configurable, "+ Configure
Payment Mode"), optional reference → Save. Several invoices can be paid at
once from the Payments Received list. Sources: Zoho Books help, "Record
payment for invoice" and "Multi payment".

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U35 | Our Pay window showed seven fields at once (journal, payment method, bank account, amount, currency, date, memo) though all are prefilled | One question first: "How was it paid?", amount, date; the rest under a collapsed "More options" | S | Built (v1.13.0) |
| U36 | Ways to pay were named "Cash Transactions" / "Bank Transactions"; no Mobile Money | "Cash", "Bank transfer", "Mobile Money" (own account), and "+" to add one (Wave, Card) | M | Built (v1.13.0) |
| U37 | A walk-in sale needed create, confirm, then Pay: three steps | "Paid now" on a new invoice confirms and pays in one save | M | Built (v1.13.0) |
| U38 | No way to pay several invoices of one customer at once (Zoho has it) | Bulk "Record payment" from the invoice list | M | Proposed |

Seen live in Zoho Books on 2026-11-20 (a client's account, read only,
nothing saved). Invoice → "Record Payment" (menu: Record Payment, Write Off)
→ one page: Customer (fixed), Payment # (auto), **Amount received** (full
balance), Bank charges, TDS deducted? (No), **Payment date** (today),
Payment received on, **Payment mode** (Bank Remittance, Bank Transfer, Cash,
Check, Credit Card, + Configure), **Deposit to** (any cash/bank/liability
account; the client keeps "Mobile Money" as an account there), Reference#,
Notes, Attachments → "Save as Paid" / "Save as Draft". New invoice offers
only "Save as Draft" / "Save and Send": no pay-on-create.

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U39 | Zoho asks two things, mode and deposit account; ours asks one ("How was it paid?" = the account) | Keep ours: one question is simpler, and a Mobile Money account is what this client already uses in Zoho | - | Decided |
| U40 | Zoho has a Reference# field visible; ours keeps the memo under More options | Show "Reference" (memo) next to the date when the way to pay is a bank or Mobile Money (transaction id) | S | Proposed |
| U41 | Zoho records bank charges with the payment | "Bank charges" under More options, posted to a fees account | M | Proposed |
| U42 | Zoho has no pay-on-create; ours ("Paid now") saves a step for walk-in sales | Keep; mention it in the guide | - | Decided |

## Creating an invoice, compared with Zoho Books (2026-11-20, same session)

Zoho: typing an unknown customer shows "No results found" and a **New
Customer** button; its form opens with the typed name already in Display
name, plus type, contact, email, phone, tax rate, currency, terms. Item
column: unknown text shows "Add New Item", a known item fills its rate and
"Recent transactions"; text that matches nothing can simply stay as a
**free-text line** with no product. Tax: one dropdown per line (single taxes
and tax groups, e.g. "TAX(VAT&LEVIES) 20%") and one switch per invoice,
**Tax inclusive / Tax exclusive**, with each tax shown in the totals.
Dashboard: receivables (current / overdue), payables, cash flow for the
year, income and expense, top expenses.

| # | Seen | Proposal | Effort | Status |
|---|---|---|---|---|
| U43 | Ours requires a saved product on every invoice line; no "+" there | "+" on the line's product to add one (name, price, tax), like the customer "+" | S | Proposed (v1.13.0 candidate) |
| U44 | Zoho accepts a free-text line (no product) | Allow a line with only a description and price, posted to the journal's income account with `display_type` PRODUCT set explicitly (see AGENTS.md MoveLine note) | M | Proposed, needs a decision |
| U45 | Zoho prefills the new customer's name from what was typed | Our customer "+" should prefill the typed search text | S | Proposed |
| U46 | Zoho switches the whole invoice between tax inclusive and exclusive | Ours decides per tax (price included or not); a per-invoice switch would be clearer for Ghana VAT/levies | M | Proposed |
| U47 | Zoho's dashboard leads with "Total receivables: current / overdue" | Matches our Needs attention; add the current/overdue split to the Invoices card | S | Proposed (dashboard plan #4) |

## Phone audit of production (390 x 844, test admin, 2026-11-20)

Read-only pass over every menu page (31 pages, plus new invoice / quotation /
RFQ). All answered 200 with no script errors except as listed. Script:
session scratchpad `pw/mobile-audit.js`.

| # | Finding | Proposal | Size | State |
|---|---------|----------|------|-------|
| U48 | Guide page fired three 500s per visit: category icons in guide.json are raw `<svg>` markup, which `<x-filament::icon>` took as an image URL (`/admin/<svg ...`) | `GuideLibrary::index()` passes only icon names; test `GuideTest` "never hands raw svg markup" | S | Fixed (v1.13.0) |
| U49 | New purchase RFQ scrolled sideways (481 px on 390): each status step hides its radio with `position:absolute`, which escaped the scrolling bar | `.state-container` and `.state` get `position:relative` (topbar mobile CSS, P6); verified 390 px by injecting the rule on production | S | Fixed (v1.13.0) |
| U50 | Lists (invoices, shipments, roles) are wide tables on a phone: Total, Payment and Status sit off-screen behind a sideways scroll, with no hint | Stack rows as cards on phones (number, customer, amount, status badge), as the 13 widest tables already do (2026-09-14) | M | Fixed (v1.13.0): CSS cards under 640px on every list page |
| U51 | The module tab row ("Customers / Vendors / Configurations / Settings") is cut off at the right with no scroll cue | Fade edge or a "more" chevron on overflow | S | Already built (R2, 2026-11-10); recheck after release |
| U52 | Several icon-only buttons are under 28 px (column toggle, filter count, grouping) | Raise to 40 px touch target on phones, as pagination already is | S | Proposed |
| U53 | Company switcher label truncates to "My Compa..." in the top bar | Show initials or the short name; full name in the dropdown | S | Proposed |
| U54 | A recent-items link pointed at `/admin/sale/orders/quotations/1`, which answers 404 for this user (record in another company) | Drop recents the user can no longer open, instead of linking to a 404 | S | Fixed (develop, unreleased: recents kept per company) |
| U55 | Opening a module page the company is not entitled to (or a link that only answers POST) showed a bare 403 / 405 error page (user, 2026-11-20) | Signed-in GET under `/admin` lands on the dashboard with a notice; API, JSON and form posts keep their 403 | S | Fixed (hotfix v1.13.1) |
| U56 | Paid invoices made without a sales order showed nowhere in Sales: not per product, category, customer or month (user, 2026-11-20) | Sales dashboard and Sales by product count them once (plan #11 R-6, dashboard DB-8) | M | Fixed (hotfix v1.13.1) |
| U57 | A company with stocked products but no warehouse gets no warning: stock cannot be recorded, and deliveries have nowhere to come from (TRACE365, 2026-11-22) | Show a setup notice on Inventory and on the product page ("Create your warehouse"), with a one-click default warehouse | S | Proposed |
| U58 | Recording stock takes two steps (Add Quantity, then Apply per row) and asks for a "Scheduled At" date that means nothing for a simple stock update | One "Set stock" dialog: product, quantity, apply now (default on); hide Scheduled At unless cycle counts are used | M | Proposed |
| U59 | Applying one stock count deleted every other pending count, in every company (purgeEmpty) | Pending counts are kept until applied or cleared | S | Fixed (hotfix, 2026-11-22) |
| U60 | The warehouse form has two buttons labelled "Create" (the address "+" and the form submit) | Label the address action "New address" | S | Proposed |
| U61 | Company form Administrator section: loose email/password fields that both invite and reset, and names listed as plain text (user, 2026-11-22) | A: administrators table with status and row actions; B: "Add administrator" dialog (invite or set password); C: invitation status chips with Resend / Copy link. Kept in place | M | Built (develop, unreleased, 2026-11-22) |
| U62 | Barcode showed in a company's sidebar without the Barcode module (TRACE365) | Launcher page checks the module grant and its permission | S | Fixed (hotfix, 2026-11-22) |
| U63 | Sales → Orders opened on "My Orders": an admin checking TRACE365's backfilled orders saw "No Orders" though all 10 existed (user, 2026-11-22) | Open Orders and Quotations on all records; show the order date, not the day the record was entered | S | Fixed (develop, unreleased) |
| U64 | A counter sale needed three boxes nobody would find on the invoice form (Create sales order, Customer has taken the goods, Paid now) and a customer even for walk-ins | "New direct sale" button opening the invoice with all three set and Walk-in Customer; "Print receipt" afterwards | S | Built (develop, unreleased) |
| U65 | "Paid now" on an invoice with no lines showed an error page (Sentry DEKAERP-22) | Warning notification, nothing saved | S | Fixed (develop, unreleased) |
| U66 | Sales dashboard trends read "vs 31 Dec – 30 Dec": the comparison period was shifted a day, and named no year (production check, 2026-11-22) | One previous-period rule for every dashboard; name the year when it is not the current one ("vs 1 Jan 2025 – 31 Dec 2025") | S | Fixed (hotfix v1.16.4) |
| U67 | Overview Purchases: the spend tile said "No sales" under purchase spend | Its own wording: "No orders / 1 order / 3 orders" | S | Fixed (develop, unreleased) |
| U68 | Dashboards open on different periods: Sales on the whole calendar year (future months included), Invoices and Purchases on the last 30 days. Sales in November therefore compares eleven months with all of last year, which flatters a fall and understates a rise | One default period for every dashboard (last 30 days, or year to date compared with the same days last year); decide with the DB-4 mockups | S | Built (develop, unreleased, 2026-11-23: year to date, same days last year) |
| U69 | TRACE365's two purchase orders (1 Jul 2026, $3.8K) are listed under Purchase, but the Purchases dashboard opens at $0 and "0 confirmed orders" because it shows the last 30 days only. Nothing says why it is empty (production check, 2026-11-23) | Part of U68. Until then, when the period is empty but older orders exist, say "No orders in this period. Last order: 1 Jul 2026" with a link to widen the period | S | Built (develop, unreleased, 2026-11-23) |
| U70 | Overview Purchases spend tile reads "vs previous period" while the Purchases dashboard tiles name the dates ("vs 9 Feb 2025 – 31 Dec 2025") | Use the same dated caption on the Overview tiles | S | Proposed |
| U71 | The invoice and bill "Payment Method" picker reads "Manual Payment (Cash)", "Manual Payment (Bank transfer)": the technical method name comes first, while payments elsewhere show just "Cash" since v1.14.0 (production check, 2026-11-23) | Show the journal's name alone ("Cash", "Bank transfer", "Mobile Money"), as the payments list does | S | Proposed |
| U72 | The Overview banner's date and clock change format a moment after loading ("22 November · 22:29", then "November 23 · 01:29 AM"), and the board's "Updated 22:29" is server time beside the viewer's own clock (production check, 2026-11-23) | Browser formats in en-GB with a 24-hour clock, matching the server; "Updated" in the viewer's time | S | Fixed (develop, unreleased) |
| U73 | Customer Revenue ring and Salesperson bars added up more than the "Total invoiced" tile beside them whenever a credit note existed | Both net of credit notes, like the tile | S | Fixed (develop, unreleased) |
| U74 | Every quarter-width dashboard tile cut its caption off ("New vs 1 Jan 20…") since the caption named the compared period (Invoices tab, production, 2026-11-23) | Caption on its own line under the label, allowed to wrap | S | Fixed (develop, unreleased) |
| U75 | Invoices tab hierarchy: the key figures (Total invoiced, counts, paid, unpaid) sit at the bottom below four charts; the period is hidden in a collapsed "Filters" panel; a one-year "Revenue over time" is drawn per day, so ten payments are hairline spikes; Salesperson Performance sits alone in a half-empty row | DB-4 mockup: period bar at the top, key figures first, one main chart that groups by week or month for long periods, breakdowns in one row, lists last | M | Built (develop, unreleased, 2026-11-23) |
| U76 | Sales "Monthly Sales" added confirmed orders to direct invoices while the Revenue figure above it counts invoices, so an order not yet invoiced showed in the chart only, dated by the order | Chart reads the same invoiced revenue, with the compared period's months beside it | S | Fixed (develop, unreleased) |
| U77 | The sidebar "Invoices" entry opened Invoices → Configuration → Bank Accounts: since v1.16.1 the invoices themselves were a tab in Sales' top bar (user, production, 2026-11-23) | Invoices back in their own menu, first, so the entry opens the invoice list (user's choice) | S | Fixed (hotfix v1.16.6, checked on production) |
| U79 | Phone list cards showed values with no names: a product card had two prices, a bare 0 and two "Units" quantities (TRACE365, 2026-11-23) | Each value carries its column label; empty values hidden; the name leads | S | Fixed (hotfix v1.16.7) |
| U80 | Company page on a phone kept two cards side by side; "TRACE365" broke mid-word and the logo overflowed (TRACE365) | Cards stack below lg; logo scaled, not cropped | S | Fixed (hotfix v1.16.7) |
| U81 | While viewing as another user, the orange banner covered the Sales tabs on a phone once the page scrolled (TRACE365) | Top bar and sidebar stick below the banner's measured height | S | Fixed (hotfix v1.16.7) |
| U82 | Phone top bar took 117px before any content; Menu label and "My Compa..." crowded row 1; the tab strip cut a tab at the left with no hint (user, 2026-11-23) | Row 1 hides on scroll down and returns on scroll up; icon Menu; more room for the company; edge fades only where more tabs are; 44px tabs | S | Fixed (hotfix v1.16.7) |
| U83 | Invoices and bills always opened in the company currency, so dollar suppliers and customers needed the currency changed every time; dollar documents gave no cedi value (TRACE365) | Default currency per contact; company value under foreign totals | M | Fixed (hotfix v1.16.7) |
| U84 | Phone audit, 2026-11-23 (user: "check most views for the mobile interface"): 91 list and dashboard pages and 18 forms and record pages at 390px on production. No page scrolls sideways; lists are labelled cards, forms one column, invoice lines stack. Remaining: picker clear cross 16x16px; Overview "Open" links 19px, settings "Configure ..." links 22px and journal-name headings 20px tall | Larger hit area for the cross, 32px minimum for those links (phones only) | S | Fixed (hotfix v1.16.8) |
| U85 | Employees › Attendance reporting answered "Server Error" for anyone allowed to see every attendance (Sentry DEKAERP-24, found by the phone audit) | Reports resolve "everyone" to the company's employees | S | Fixed (hotfix v1.16.8) |
| U86 | Small calendar day headers (Maintenance calendar, Time Off) and status badges are under 32px, but none of them is a tap target | None needed | - | Checked, no change |
| U78 | Sales dashboard breakdowns (salesperson, category, customers, products) are titled "Revenue" but count confirmed orders and invoices made without an order, so they never match the invoiced Revenue figure; a customer's "Sales" column counted a credit note as a sale | Caption under each: "Confirmed orders and invoices made without an order"; credit notes not counted as sales | S | Fixed (develop, unreleased) |
| U87 | Ring charts kept the old total in the centre after the period changed: an update sends new data, not new chart options (found building DB-9, 2026-11-24) | Centre text and legend values travel with the data | S | Fixed (develop, unreleased) |
| U88 | The ring legends named a slice but not its amount or share, so reading one meant hovering every slice | Legend lists "amount · N%" under each name (DB-9 c) | S | Built (develop, unreleased) |
| U89 | The top-list tables ended at five rows with no way to the full list | "View all" in each table header, shown only to people who may open that list (DB-9 d) | S | Built (develop, unreleased) |
| U90 | The Sales dashboard said what was sold but not what still needed doing (deliver, invoice) | Order activity tiles, each opening the order list filtered to it (DB-9 d) | S | Built (develop, unreleased) |
| U91 | A sale in another currency took the day's rate silently; a seller who agreed a different rate with the customer had nowhere to put it (TRACE365) | Optional question on a new invoice: currency, then the rate shown as "1 USD = 11.8 GHS", correctable; who kept it goes on the invoice history (MC-2) | M | Built (develop, unreleased, off until a company switches it on) |
| U92 | The currency question does not show the confirmed rate again on the invoice form; "Change" means reopening the form | Rate line under the currency (kept rate, who confirmed it, or that a currency change dropped it) and "Currency and rate" in the page header; posting summary still to do | S | Built (develop, unreleased) |
| U94 | Dashboard customer, vendor and product lists ended at five rows with no way on; vendor rows did not open | "View all" on every dashboard list; vendor rows open the vendor | S | Built (develop, unreleased) |
| U93 | Payments received gave no sign of money paid in but not yet applied to an invoice | "Unused amount" column and export column | S | Built (develop, unreleased) |
