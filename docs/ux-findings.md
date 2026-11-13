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
