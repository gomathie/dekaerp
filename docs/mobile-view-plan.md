# Mobile view — findings and proposed work

Status: **proposal, 2026-11-10.** No layout code has been changed. Waiting for
approval before implementation.

Looked at production (`https://cloud.dekaerp.com/admin`) signed in as the
admin account named in the local env. The browser was set to a phone size of
**390 × 844**. Pages opened: the project dashboard and Sales → Quotations.
No records were created or edited.

## What already works

Leave these alone. They were checked and they hold.

- The page itself does not scroll sideways. On the dashboard,
  `documentElement.scrollWidth` equals `clientWidth` (both 390).
- Dashboard filters and stat cards stack in one column. Charts sit under
  them.
- The module menu opens as a drawer. On the phone it receives
  `is-mobile-open`, is 200px wide, and slides in from the left. The rule is
  already in `resources/views/vendor/filament-panels/livewire/topbar.blade.php`
  (`@media (max-width: 767px)`).
- The 2026-09-14 pass already hid extra columns on the 13 widest lists
  (`visibleFrom('sm'|'md'|'lg')`, everything visible again from 1024px) and
  fixed the Filament summary row so those hidden columns do not break the
  totals. Card hover-jump and the installable icon were part of that same
  pass. Do not repeat it.
- The public proof-of-delivery page was already built for 390px. It is not
  part of this proposal.

## What is wrong on a phone

### 1. The top bar is too crowded

This bar is on every admin page, so it is the first thing to fix.

Measured on the dashboard at 390px:

| Piece | Measurement |
| --- | --- |
| Top bar height | 64px |
| Right cluster (`.fi-topbar-end`) | content 341px inside a 326px box, so it overflows by 15px |
| Company control | starts about 48px from the left, after the menu button and logo |

What that looks like: the menu button, the logo, **My Company** (wrapped onto
two lines), the search field, the bell, and the user button are all in one
row. The bell and the user button are clipped. A longer company name than
"My Company" will be worse.

The company control is `support::company-switcher`, injected at
`PanelsRenderHook::GLOBAL_SEARCH_BEFORE` from
`plugins/webkul/support/src/SupportServiceProvider.php`. The name is an
inline-styled `<span>` in
`plugins/webkul/support/resources/views/company-switcher.blade.php`, so it
cannot shrink or truncate. The search field keeps its desktop width.

### 2. List tabs are clipped

On Sales → Quotations the saved-view tabs read **Default**, **My Quotations**,
then **Quotati…**. The rest (Quotations, Sales Orders, Archived) are cut off
by the card edge. There is no visible way to reach them. The table header
beside them shows Number, Customer, and Amount Total, and stops.

The document still does not scroll sideways. The clip is inside the table
card. Filament's own table body can scroll, which is acceptable for the
columns that remain after the 2026-09-14 `visibleFrom` rules. The tabs are
not a data table and should not be clipped.

The quotation list was empty ("No Quotations"), so row content was not
checked. The header and the tabs were enough to see the clip.

## Recommendations

Every recommendation from the phone pass is listed here. The first two are
the ones to build. The rest are recorded so they are not lost, including the
ones that should not be built.

### Build first

**R1. Top bar, under 768px.** Same as M1 below. Truncate the company name to
one line and let search shrink, so the bell and the user button are fully
on screen. Shrink the name and the search, not the icon buttons. A longer
name than "My Company" is the case this has to survive.

**R2. Saved-view tabs scroll.** Same as M2 below. The tab strip scrolls
sideways inside the card. A fade on the trailing edge shows that more tabs
exist. The table columns keep their own sideways scroll.

### Build in a second pass, after R1 and R2 are on screen

**R3. Dashboard stat cards, two per row.** Total Tasks, Total Hours Spent,
and Total Time Remaining each take a full row, so the charts start far down
the page. Under 768px, put two cards on a row. Leave the filter fields in
one column. They already fit.

**R4. Open a list that has rows, and one edit form.** Quotations was empty,
so a row with a long customer name was not seen. After R1, check invoices
or contacts, and one edit form, at 390px and at 320px. Forms already stack
from the 2026-09-14 pass. Change a form only if the page itself scrolls
sideways.

**R5. Let a long customer name wrap inside the cell.** Quotations already
keeps Number, Customer, and Amount Total on the phone, and hides Creation
Date from the `md` breakpoint up. Keep those three. Do not hide more
columns. If a long name widens the page, wrap it inside the cell.

**R6. Truncate with logical CSS.** The company name needs
`max-inline-size`, `overflow`, and `text-overflow`, not a physical `width`,
so Arabic still clips on the correct side. The same class goes on the
switcher button and on the read-only name shown to someone who cannot
switch.

**R7. Check 320px as well as 390.** The top bar is already 15px over at
390. A narrower phone is the size that will still clip after a fix that
only just fits 390.

### Do not build unless a later look shows it

**R8. Leave the module drawer at 200px.** It opened, labels were visible,
and it is the control people use to change modules. Widen it only if a
module name is actually cut off. None was, on this pass.

**R9. Do not remove the second "Expand sidebar" control yet.** The
accessibility tree has two buttons with that name: Filament's page sidebar,
and the module drawer. The phone screenshot showed one menu icon. Hide the
Filament one on a phone only after a check that both are actually visible.

### Do not do

- Rebuild lists as cards.
- Put `visibleFrom` on the rest of the 221 tables. Thirteen lists were
  done on 2026-09-14, and desktop from 1024px up must stay complete.
- Remove search, shrink the bell, or shrink the user button to buy space.
- Change who may switch company, or the All-companies mode.
- Change `getNavigationGroup()`, the top menu, or `lang/*/admin.php`.
- Turn the panel into a chromeless app. The 2026-09-14 decision was
  `minimal-ui` because the panel opens PDFs and downloads.
- PILOT, or moving the proof-of-delivery token out of the URL path. Those
  are separate from layout.

## Proposed work

R1 and R2 are the proposed patch. Both are CSS and one Blade span. No new
page, no navigation change, no column set rewritten.

### M1 — Top bar, under 768px

In the company switcher, give the company name a class and, only under
768px:

- keep it on one line
- truncate with an ellipsis
- cap the width so the bell and the user button stay fully inside the bar

In the same breakpoint, let the global search shrink (a smaller field, or
the existing icon-only trigger Filament already uses when the field is
narrow). Do not remove search.

Desktop, from 768px up, stays as it is. The switcher still posts to
`company-context.set`. Do not change who may switch, or the All-companies
mode.

Files:

- `plugins/webkul/support/resources/views/company-switcher.blade.php`
- `resources/views/vendor/filament-panels/livewire/topbar.blade.php` (the
  existing mobile style block), or `resources/css/app.css` if the search
  rule does not belong in the top-bar template

### M2 — Saved-view tabs scroll

Under 768px, the tab strip inside a Filament table (`.fi-ta-tabs` or the
current Filament 5 equivalent) scrolls sideways on its own, with the tabs
kept on one line. Hidden tabs stay reachable by swiping the strip. The table
columns keep scrolling inside the table, as they do now.

One rule, in the same place as M1. Do not add `visibleFrom` to more columns
unless a later check shows the **page** (not the table card) scrolling
sideways.

## Out of scope for the first patch

R3 to R7 wait for a second pass. R8 and R9 stay unchanged unless a later
look shows the problem. The "Do not do" list above is not planned at all.

Production data is not part of this work. The layout ships only when the
branch is deployed.

## Verification, after approval

On a phone-sized browser (390 × 844), signed in:

1. Dashboard, Sales quotations, and one list that has rows (invoices or
   contacts). The top bar is one line. The company name truncates. The bell
   and the user button are fully visible.
2. The quotation tabs can all be reached by swiping the tab strip.
3. At 1280px the top bar and the tabs look as they do today.
4. A company the user may switch still switches. A user who may not switch
   still only sees the name.
5. `documentElement.scrollWidth` stays equal to `clientWidth` on those
   three pages.

No new Pest test is planned. This is rendered layout. Say so in the
change-log entry when the work lands.

## Approval

Reply with which recommendations to build:

- **R1 and R2** — the top bar and the scrolling tabs. This is the
  recommended first patch.
- **R1 only** — the top bar.
- **R1, R2, and R3** — that patch, plus two stat cards per row.
- **A different set** — name the R numbers.
- **None** — leave the phone layout as it is.
