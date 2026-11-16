# What's New page: improvement plan

Written 2026-11-12. Status: **done 2026-11-12** (WN-1 to WN-6; see docs/change-log.md). Verified: WhatsNewPageTest, VersionMatchesChangelogTest, OverviewTest, InventoryHeadlineTest, 21 passed.

Reference: the shared library's `whats-new-changelog-page` skill and workflow
(`C:\Users\gomat\OneDrive\Documents\Workflow and skills\`), built from Pilot
Academy's page. DEKA's page predates it.

## 1. What DEKA has today

`Support\Filament\Pages\WhatsNew` (Help group, route `whats-new`) reads the
root `CHANGELOG.md` on each request, cached on the file's modification time,
and shows each release as a collapsible card, the newest open, with section
icons.

Gaps against the skill, and other findings:

| # | Finding | Severity |
|---|---|---|
| G1 | Items are rendered with `Str::inlineMarkdown()` and **no** `html_input => strip` or unsafe-link guard. The file is ours, but a raw `<script>` or `javascript:` link in an entry would run. | Security (low likelihood) |
| G2 | **No tests at all** for the page. | Quality |
| G3 | No search and no category filter; 12 releases and about 850 lines. | Usability |
| G4 | A bullet that wraps onto a second line loses its continuation (the parser reads one line per item). Prose under a heading is dropped. | Correctness |
| G5 | No PDF export (dompdf is already installed). | Feature |
| G6 | No top-bar shortcut; the page is only in the Help menu. | Discoverability |
| G7 | No "unread" signal: nothing tells a user that a new release has arrived. | Discoverability |
| G8 | Headings are inconsistent: emoji and plain forms mixed ("🧩 Features" / "Features"), and v1.7.0's title differs from the rest. | Content |
| G9 | Some entries are written for developers, not users (for example "tenant-prefixed private object storage", hash details). | Content |
| G10 | The top release (v1.7.0) lists work that is on develop but not yet in production (Attendance, the welcome banner). Production deploys only chosen files, so the notes users see can describe features they do not have, or omit ones they do. | Accuracy |
| G11 | No release dates and no `config('app.version')`; the version lives only in the changelog heading. | Content |

## 2. Approach

Upgrade the existing page in place, following the skill. Keep the class,
route (`whats-new`), Help-menu entry and `CHANGELOG.md` location, so links
keep working. Keep the current file format readable: the parser accepts both
the existing `# CHANGELOG — v1.x` headings and the skill's `## 1.x — Month
YYYY` form, so 850 lines need no rewrite.

## 3. Work packages

| ID | Package | Effort | Notes |
|---|---|---|---|
| WN-1 | Safety and tests | 0.5 day | G1, G2 |
| WN-2 | Parser fixes | 0.5 day | G4, G8 |
| WN-3 | Search and category filters | 1 day | G3 |
| WN-4 | PDF export | 0.5 day | G5 |
| WN-5 | Top-bar shortcut with "new" dot | 0.5 day | G6, G7 |
| WN-6 | Content pass and release discipline | 0.5 day + per release | G9, G10, G11 |

### WN-1 — Safety and tests

- Render with `Str::markdown(..., ['html_input' => 'strip', 'allow_unsafe_links' => false])`.
- Tests (`support/tests/Feature/WhatsNew/WhatsNewPageTest.php`): the file is
  found; releases newest first; sections mapped; raw HTML and `javascript:`
  links not rendered; a missing file gives no releases and no error; the page
  is reachable from the Help menu for panel users.

### WN-2 — Parser fixes

- A bullet's wrapped lines belong to it; prose under a heading renders as one
  block; HTML comments are stripped (room for authoring notes); code fences
  are not split.
- Map headings to fixed categories whatever their emoji: Features → New,
  Improvements → Improved, Fixes → Fixed, Upgrade → Upgrade notes; anything
  else keeps its own text. `[Security]` items get a small "Security" badge.

### WN-3 — Search and category filters

- One search box (with a real label) and category pills (New, Improved,
  Fixed, Security). Filtering rolls up: an item hides, then an empty section,
  then an empty release; one "Nothing matches" line.
- Releases stay collapsible; a search opens the releases that match.
- Built from the existing Filament page styles; works on phones.

### WN-4 — PDF export

- "Download PDF" for one release and for all, through a controller that
  reuses the page's parser (never a second read of the file); same access as
  the page; 404 for an unknown release; opens inline; font subsetting on;
  paper layout writes the category in words.

### WN-5 — Top-bar shortcut with "new" dot

- A megaphone icon in the top bar, next to search (icon only, with a label
  for screen readers).
- A small dot when the newest release is newer than the last one this user
  opened. Stored per user (one column or a user setting), so it follows them
  across devices. Opening the page clears it.

### WN-6 — Content pass and release discipline

- Rewrite v1.7.0 and v1.6.0 entries in plain user language; keep technical
  detail in `docs/change-log.md`, where it belongs.
- Add a date to each release heading from here on.
- **Production accuracy (G10):** the changelog entry for a feature is added
  in the same production push that ships it, not on develop. Until then it
  goes under an "Unreleased" heading that the page does not show.
- `config('app.version')` set from the changelog's top released version, and
  shown at the bottom of the sidebar linking to this page.
- One line in `AGENTS.md`: every user-visible change gets an entry, in user
  language, under "Unreleased", moved to the version when pushed to
  production.

Total: about 3.5 days.

## 4. Decisions (user, 2026-11-12)

| ID | Question | Decision |
|---|---|---|
| D-WN-1 | Show an "Unreleased" section to users? | **No.** Hidden until pushed to production; entries move under the version in that push |
| D-WN-2 | "New" dot per user or per browser? | **Per user**, so it follows them across devices |
| D-WN-3 | Rewrite the v1.7.0 and v1.6.0 entries in plain language? | **Yes.** Technical detail stays in docs/change-log.md |
| D-WN-4 | Who may download the PDF? | **Everyone who can open the page** |
| D-WN-5 | When does this run? | **After the comprehensive review plan's (#2) first block** (CR-10 defects, CR-1, CR-3, CR-4) |
