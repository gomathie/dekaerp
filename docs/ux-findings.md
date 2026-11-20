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
| U54 | A recent-items link pointed at `/admin/sale/orders/quotations/1`, which answers 404 for this user (record in another company) | Drop recents the user can no longer open, instead of linking to a 404 | S | Proposed |
