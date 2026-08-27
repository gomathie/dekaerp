# Agent Reminders

Running log of questions asked, tasks requested, and work done by the AI
coding agent on this project. Newest entries at the top.

Note: this is a working log, separate from [`AGENTS.md`](../AGENTS.md) (Laravel
Boost guidelines the agent follows) and [`CHANGELOG.md`](../CHANGELOG.md)
(release notes). Code-change details go in [`docs/change-log.md`](change-log.md).

---

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
