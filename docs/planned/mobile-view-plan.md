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

## Deep audit — 2026-11-10, evening (Claude)

Requested because customers report they cannot use the app on a phone. The
first pass looked at a dashboard and an empty list. This one opened the
screens people work in, in production at a phone width (viewport 390 × 844,
375 usable), signed in as the Super Admin. Nothing was saved; one
unsaved quotation draft was discarded.

Pages: Contacts (list with rows), Create Quotation (including adding an
order line), Edit Employee, and the two menu buttons.

### Findings, most damaging first

**F1. iPhone zooms in on every field and stays zoomed.** On Edit Employee,
36 of 37 fields render at 14px (`.fi-input`). iOS Safari zooms the page
whenever a field under 16px gets focus and does not zoom back. After the
first tap the page is wider than the screen, buttons drift off the edge and
the user must pinch back after every field. The viewport meta is correct
(`width=device-width, initial-scale=1`); the font size is the cause. This
alone is enough for "customers cannot use it on mobile" on iPhones.

**F2. Navigation on a phone is two unlabelled 24px icons side by side.**
The top bar shows two buttons both named "Expand sidebar", at x=16 and x=32,
24 × 36px and 23 × 36px, touching each other:

- the first (≡) opens the **current module's pages** (for Sales: Orders,
  To Invoice, Products, Configurations, Settings);
- the second, a small grid that reads as part of the logo, opens the
  **list of modules**.

On a phone the top-bar page links are hidden, so these two icons are the only
way to move around, and nothing tells the user which does what. Both are
below the 44px minimum for a fingertip.

**F3. Header actions and the user button sit at the edge.** On Contacts the
user avatar was cut off on the right (also in the first pass: the right
cluster overflows by 15px). Combined with F1 zoom, the avatar and bell are
lost.

**F4. Tabs are clipped with no hint.** Contacts list tabs cut "Companies";
the quotation's lower tabs ("Order Line", "Optional Products", "Other…")
clip at 343px of 682px; Edit Employee tabs clip at 343 of 486px. The strips do
scroll, but nothing shows that more exist (same as M2).

**F5. 101 tap targets under 40px on Edit Employee.** Icon buttons in field
suffixes (the + beside selects, the mail icon), repeater delete icons,
pagination and the card action links (View, Edit, Delete) are small and close
together.

**F6. Status steps wrap badly.** On Create Quotation the state bar
(Quotation → Quotation Sent → Sales Order) breaks with "Sales Order" alone on
a second line under the arrow.

**F7. List cards waste space.** Contact cards reserve a large image area
even when there is no photo: one screen shows about one and a half contacts.

### What works and should stay

- The page itself never scrolls sideways (`scrollWidth = clientWidth` on all
  three pages).
- Forms stack to one column.
- **Order lines stack as cards** (`.fi-fo-table-repeater.fi-compact`):
  product, quantity, unit price, taxes, amount, delete. Line entry is usable
  on a phone once F1 is fixed.
- The module drawer itself (200px, slides in) and the cluster page selector at
  the top of each page.

### Options considered

1. **Targeted fixes in the existing UI (recommended).** CSS and two Blade
   templates already overridden in this repo. Fixes F1 to F7 in days, keeps
   desktop unchanged, nothing for customers to install.
2. **A bottom navigation bar on phones** (Modules · Pages · Search ·
   Account). App-like and thumb-reachable. Worth it later, but it is a new
   navigation surface to maintain beside two existing ones; F2 can be fixed
   with less.
3. **Switch the admin panel from top navigation to Filament's standard
   sidebar.** Filament's sidebar is its best-tested mobile path, but this
   changes desktop for every customer and fights the custom module sidebar.
   Not recommended now.
4. **A separate mobile app (native or a dedicated PWA screens set).**
   Months of work and a second front end. Only for specific field roles
   (drivers already have the proof-of-delivery page). Not recommended.

### Recommended fixes, in order

| # | Fix | Finding | Size |
|---|---|---|---|
| P1 | Under 768px, form fields at `font-size: 16px` (inputs, selects, textareas, Filament's select search). Desktop unchanged. | F1 | Small, CSS |
| P2 | Replace the two icons with one labelled control: a single "Menu" button that opens one drawer with two sections, **Modules** (the grouped list) and **This module** (the current module's pages). 44 × 44px. | F2 | Medium, top-bar Blade |
| P3 | Top bar under 768px: company name truncates on one line, search collapses to its icon, bell and avatar keep 40px. (R1/M1 above.) | F3 | Small |
| P4 | Every tab strip under 768px scrolls on one line with a fade on the trailing edge. (R2/M2 above, widened to form tabs.) | F4 | Small, CSS |
| P5 | Minimum 40px hit area for icon buttons, field suffix actions, pagination and card actions under 768px (padding, not bigger icons). | F5 | Small, CSS |
| P6 | Status bar under 768px scrolls on one line instead of wrapping. | F6 | Small, CSS |
| P7 | Card lists: hide the image block when a record has no image. | F7 | Small |
| P8 | Second pass: R3 (two stat cards per row) and a check at 320px. | — | Small |

P1 and P2 are the ones that make the app usable; P3 to P7 make it
comfortable. All are layout only: no data, permissions or navigation
groups change, and desktop from 768px up stays as it is.

**Verification for the build:** a real iPhone (Safari) and an Android
phone (Chrome), not only a resized desktop browser, because F1 happens only
on iOS. Check: focus each field on Edit Employee and Create Quotation, the
page does not zoom; one tap opens the menu and both sections are reachable;
`scrollWidth = clientWidth` on every page checked; desktop at 1280px
unchanged.

## Implementation — 2026-11-10 (Claude), user: "implement the mobile view"

Built: **P1 to P6.** P7 (card image block) and P8 are not built yet.

- **P1** fields at 16px under 768px.
- **P2** one labelled "Menu" button (44px) replaces the two icons; the
  module drawer lists **this module's pages** (from the active navigation
  group, child items included) above **Modules**. Filament's own sidebar
  toggles are hidden on phones only.
- **P3** company name truncates (`.company-switcher-name`, logical
  `max-inline-size`), search is an icon until tapped, bell and avatar keep
  their size.
- **P4** every `.fi-tabs` strip scrolls on one line with a trailing fade
  (mirrored for RTL).
- **P5** 40px minimum for icon buttons, table action links and pagination.
- **P6** `.state-container` status bar scrolls on one line.

Files: `resources/views/vendor/filament-panels/livewire/topbar.blade.php`
(button, drawer section, CSS), `plugins/webkul/support/resources/views/company-switcher.blade.php`
(name class), `lang/{en,fr,es,pt_BR,ar}/admin.php` (`mobile-menu`).

Verification:

- `support/tests/Feature/Filament/MobileNavigationTest.php`: 1 passed,
  7 assertions (the Menu button, the drawer's module pages, the Modules
  heading and the truncating name render on a real admin page).
- Preview at 390 × 844: the same CSS and markup injected into the production
  page in the browser (nothing on the server changed). Page width stays 375;
  the avatar's right edge is at 367 of 375 (was 379); fields compute 16px;
  the status bar is one line; the drawer shows Sales' six pages, then the
  modules.
- Not yet done: a real iPhone (Safari) and Android phone after deploy. F1
  zoom happens only on a real iPhone.

## Approval

*Superseded by the deep audit above:* the recommended first patch is now
**P1 + P2 + P3 + P4**. The original options below remain for reference.

Reply with which recommendations to build:

- **R1 and R2** — the top bar and the scrolling tabs. This is the
  recommended first patch.
- **R1 only** — the top bar.
- **R1, R2, and R3** — that patch, plus two stat cards per row.
- **A different set** — name the R numbers.
- **None** — leave the phone layout as it is.
