# Agent Reminders

Running log of questions asked, tasks requested, and work done by the AI
coding agent on this project. Newest entries at the top.

Note: this is a working log, separate from [`AGENTS.md`](../AGENTS.md) (Laravel
Boost guidelines the agent follows) and [`CHANGELOG.md`](../CHANGELOG.md)
(release notes). Code-change details go in [`docs/change-log.md`](change-log.md).

---

## 2026-08-28

**Requested:** "push change log to the interface and name it whats new" —
surface release notes inside the admin panel itself.

**Open questions:** Which content should the page show — the root
`CHANGELOG.md` (user-facing release notes) or the new `docs/change-log.md`
(internal dev log created yesterday)? Asked the user directly since dumping
internal engineering notes (code diffs, file paths, "not fixed yet" caveats)
onto a production admin screen would've been the wrong call to make silently.
**Answered:** root `CHANGELOG.md`.

**Done:**
- Added a "What's New" page to the admin panel
  (`Webkul\Support\Filament\Pages\WhatsNew`, under the existing Help nav
  group), which parses `CHANGELOG.md` into collapsible per-version sections
  (Features/Improvements/Fixes/Upgrade), latest version expanded by default.
  Cached against the file's mtime so edits show up without a manual cache
  clear.
- Caught that `.dockerignore` excludes all `*.md` files (only `LICENSE` was
  excepted) — the production image would never have shipped `CHANGELOG.md`
  and the new page would render empty. Added a `!CHANGELOG.md` exception.
- Added `en`/`ar`/`es`/`pt_BR` translations for the page chrome, matching the
  existing 4-locale convention (`Help.php`'s lang files). The changelog
  content itself is not translated — it comes from the English `CHANGELOG.md`
  as-is.
- Verified the markdown-parsing logic against the real `CHANGELOG.md`
  (10 releases, v1.0.0–v1.5.0) with a standalone PHP script, since `php
  artisan` can't boot locally — see note below.

**Environment note:** Local PHP is 8.3.2, but `vendor/` was installed
against a dependency requiring `>=8.4.1`, so *no* `artisan` command runs
locally right now (fails at the Composer platform check, before Laravel even
boots). Verification here was by lint (`php -l`), `pint --dirty`, and a
standalone script exercising the parser in isolation — not by loading the
page in a real panel. Worth fixing the PHP/vendor mismatch (or using the
`verify-in-container` skill) before the next round of app-level changes.

## 2026-08-27

**Requested:** Investigate a `500 Internal Server Error` on
`POST /livewire-70e0a63d/update` when printing/print-previewing an invoice
(`https://cloud.dekaerp.com/admin/invoices/customers/invoices/1`). Also
create this reminders log and a change log.

**Open questions:**
- None outstanding — root cause identified and fixed without needing
  production log access.

**Done:**
- Traced the invoice "Print" flow (Preview modal → Print) to
  `PreviewAction::setUp()` in
  `plugins/webkul/accounts/src/Filament/Resources/InvoiceResource/Actions/PreviewAction.php`.
- Found the PDF is written via `Storage::disk('public')->put(...)` (which in
  production resolves to the `tenant-s3` driver — see
  `config/filesystems.php`), but was then read back with
  `storage_path('app/public/'.$pdfPath)`, which only ever resolves a local
  path. On production this file never exists locally, so
  `response()->download()` throws and Livewire returns a 500. See
  [`docs/change-log.md`](change-log.md) for the fix.
- Noted a second occurrence of the same pattern (disk write, local-path read)
  in `plugins/webkul/chatter/src/Traits/HasChatter.php::addAttachments()`
  (line ~620) and a related disk-mismatch in `removeAttachment()` (uses the
  default `local` disk with a `'public/'` prefix instead of
  `Storage::disk('public')`). **Not fixed** — out of scope of the reported
  bug (chatter attachments, not invoice printing). Flagging for a follow-up
  task if the user wants it addressed.
