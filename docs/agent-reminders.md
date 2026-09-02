# Agent Reminders

Running log of questions asked, tasks requested, and work done by the AI
coding agent on this project. Newest entries at the top.

Note: this is a working log, separate from [`AGENTS.md`](../AGENTS.md) (Laravel
Boost guidelines the agent follows) and [`CHANGELOG.md`](../CHANGELOG.md)
(release notes). Code-change details go in [`docs/change-log.md`](change-log.md).

---

## 2026-09-02

**Requested:** Sentry's onboarding doc for this project, pasted in full
(install, Excimer, `Integration::handles()`, DSN publish, log channel,
`zend.exception_ignore_args`, verify) — i.e. finish setting Sentry up.

**Already done before this session** (commit `076735636`, 2026-08-24):
package installed (`sentry/sentry-laravel` 4.27.0 / `sentry/sentry` 4.30.0),
`config/sentry.php` published, `Integration::handles()` wired into
`bootstrap/app.php`, `sentry_logs` channel defined in `config/logging.php`,
and `SENTRY_LARAVEL_DSN` / `SENTRY_TRACES_SAMPLE_RATE` /
`SENTRY_SEND_DEFAULT_PII` present in `.env.example`. Verified each rather
than re-running the wizard's steps on top of them.

**Done this session:** Excimer added to the production image (best-effort, so
a failed extension build cannot block a deploy), `SENTRY_PROFILES_SAMPLE_RATE`
and `SENTRY_ENABLE_LOGS` added to `.env.example`, `SENTRY_*` passthrough added
to the entrypoint, and `zend.exception_ignore_args` pinned to `On` with the
reasoning written into `php.ini`. Details in
[`docs/change-log.md`](change-log.md).

**Decision taken against the doc:** Sentry asks for
`zend.exception_ignore_args = Off` so stack traces carry function arguments.
Left `On`. Those arguments are customer records, invoice payloads and
credentials passed to auth calls — the same data this project deliberately
keeps out of Sentry via `SENTRY_SEND_DEFAULT_PII=false`. Reversible in one
line if the user decides the debugging value is worth it.

**DSN (asked for a second time, now configured).** Written into the local,
gitignored `.env` — which is exactly what `sentry:publish --dsn=...` does —
along with the rest of the wizard's block: traces and profiles at 1.0,
`SENTRY_ENABLE_LOGS=true`, `LOG_STACK=single,sentry_logs`. `APP_ENV=local`
tags these events as `local` in Sentry, so they stay separable from
production.

`.env.example` deliberately keeps the empty placeholder: `gomathie/dekaerp`
is a **public** repo (verified against the GitHub API), and the image builds
its `.env` from that template, so a DSN committed there is a DSN anyone can
POST events into and burn the quota with. Production therefore takes it from
the platform's environment variables, which the entrypoint change above
mirrors into the container's `.env`.

**Still blocked on the user:** production sends nothing until
`SENTRY_LARAVEL_DSN` is set on the host running the image.

**Verified (partially).** `php artisan sentry:test` still cannot run on this
machine — PHP 8.3.2 against a vendor tree requiring >= 8.4.1, and a full
Laravel boot here also needs extensions the bare PHP image lacks plus a
reachable DB. Instead the DSN path was exercised directly: a throwaway
`php:8.4-cli` container mounting the repo, running the SDK standalone (no
framework boot, so no DB connection and no plugin discovery). Result: event
accepted, `flush()` returned SUCCESS, event ID
`7cc09061dbd74029b85e379a8e3fc0d9`, tagged `environment: setup-verification`
/ `release: sentry-wiring-check`.

That proves the DSN, the project and the network path to
`o4511967103811584.ingest.de.sentry.io`. It does **not** prove the Laravel
wiring (`config/sentry.php` reading env, `Integration::handles()` catching
unhandled exceptions) — those are still only statically verified — nor
anything about the Laravel Cloud environment. `php artisan sentry:test` on
Cloud remains the real end-to-end check.

**Confirmed on production (2026-09-02).** The user ran `php artisan
sentry:test` on Laravel Cloud: "DSN discovered from Laravel config or `.env`
file!", test event ID `88a82e3ec4a14bc39a0c3005099210c1`. That closes the two
gaps the local check left open — the platform's environment variables do reach
Laravel's config, and the SDK initialises correctly inside a full framework
boot. Error monitoring is live.

Still open after this, none of it blocking error capture:
- `LOG_CHANNEL=nightwatch` points at a channel that no longer exists
  (`laravel/nightwatch` is not installed), so `Log::` output falls through to
  the emergency logger and the Cloud Logs tab gets nothing. User asked to
  leave it for now.
- `SENTRY_ENABLE_LOGS=true` is inert while `sentry_logs` is absent from the
  active channel path. `Log::channel('sentry')` works regardless.
- `SENTRY_PROFILES_SAMPLE_RATE=0.1` is likely inert on Cloud: Excimer is
  installed by the Dockerfile, which Laravel Cloud does not build from.
  Harmless - the SDK logs a warning and skips profiling.

**API notes for the two snippets in Sentry's verify section**, both valid on
`sentry/sentry` 4.30.0: `\Sentry\logger()` exists
(`vendor/sentry/sentry/src/functions.php:461`), and `Log::channel('sentry')`
works without any `LOG_STACK` change because the SDK auto-registers a
`sentry` channel when the app has not defined one
(`ServiceProvider.php:216`). Worth keeping the two channels distinct:
`sentry` sends log records as ordinary Sentry issues, `sentry_logs` feeds the
separate Logs product and is what needs `enable_logs` plus a place in the
stack.

**Flagged, not touched:** `.github/workflows/docker_publish.yml` is still
upstream's — it publishes `webkul/aureuserp`, takes an "AureusERP branch or
tag to build" input, and builds with `context: docker/production`, while this
fork's Dockerfile expects the repository root as context (`COPY . .`). That
workflow would not build a working image of this fork, so the Excimer layer
only reaches production via whatever builds from the repo root. Worth either
fixing or deleting, but it publishes under a name this fork may not own, so
it needs a decision rather than a quiet edit.

### OPEN SECURITY FINDING — Supabase Data API exposes the `public` schema

Supabase Advisor reported 225 issues, effectively one systemic finding
repeated across every table this ERP creates: "RLS Disabled in Public".

**Confirmed exploitable, not lint noise:**
- `select ... has_table_privilege('anon', ...)` returned `anon_can_select =
  true` for `password_reset_tokens`, `users`, `sessions`, `jobs`,
  `job_batches`, `failed_jobs`. Supabase's default privileges grant
  `anon`/`authenticated` on new tables in `public`, and Laravel migrations
  create tables there.
- The Data API is live: an unauthenticated GET to
  `https://sorkcuosrpicvvnwoxrr.supabase.co/rest/v1/` returns
  `{"message":"No API key found in request"}` — PostgREST is up and serving,
  it only wants an `apikey` header. The anon key is public by design.

Impact, after checking the full grant set: `anon` and `authenticated` each
hold SELECT, INSERT, UPDATE, DELETE, TRUNCATE, REFERENCES and TRIGGER on all
**206** tables in `public`. So the exposure is not only disclosure - password
reset tokens (account takeover, including admins), user emails and bcrypt
hashes, session rows - but full write access: TRUNCATE every table in the
ERP, INSERT an admin user, UPDATE invoices and payments. All over plain
HTTPS with a key that is public by design, bypassing the application
entirely.

This is Supabase's default-privileges behaviour for a project used as a plain
Postgres host by a non-Supabase app, not something this codebase did. Any
other Supabase project used the same way will have the same hole.

**Why RLS is the wrong lever here.** Laravel connects as
`postgres.<project-ref>`, which owns these tables, so it bypasses RLS -
enabling RLS would neither break nor protect the app's own path, and every
future migration would re-add unprotected tables. The exposure is the
`anon`/`authenticated` grants plus the API exposing `public`.

**Fixed by the user 2026-09-02, verified** (SQL supplied here; no DB access from this session):
1. `revoke all on all tables/sequences/functions in schema public from anon,
   authenticated`, plus the matching `alter default privileges ... revoke` so
   future migrations do not re-open it.
2. Settings -> API -> Exposed schemas: remove `public`, or disable the Data
   API outright. Nothing in this repo uses it (no `SUPABASE_*` keys, no client
   library) - but another project of theirs might, which is the one thing to
   check before revoking.
3. Re-run the grants query (expect zero rows) and check Supabase API logs for
   prior `/rest/v1/` traffic. Truncating `password_reset_tokens` is harmless
   and forecloses any harvested token.

**Verification (2026-09-02).** The `revoke` batch ran successfully, and the
default-privileges half was proven empirically rather than assumed: creating
a throwaway table in `public` and counting its `anon`/`authenticated` grants
returned **0**, so migrations from here on do not re-grant. The first attempt
at that test was inconclusive - `drop table` was the last statement and the
editor reported *its* "no rows", which is indistinguishable from an empty
SELECT. Re-run with `count(*)` as the final statement, it returned an
explicit 0. Worth remembering for any future check in that editor: end on the
SELECT, and count rather than rely on emptiness.

Still open at time of writing: removing `public` from Settings -> API ->
Exposed schemas (the second lock; the revoke alone closes the data path),
rotating the JWT secret so any leaked anon key dies, and checking Logs -> API
for prior `/rest/v1/` traffic - that last one is time-sensitive, since log
retention is days, not weeks.

**Blocked:** the `supabase` MCP server (`.mcp.json`, project
`sorkcuosrpicvvnwoxrr`) is configured but unauthorized, and this session
cannot run the OAuth flow, so the grants could not be inspected or fixed
directly. Authorizing it via `/mcp` in an interactive session would allow
verifying and applying the fix rather than handing over SQL.

**Not documented yet:** `docs/supabase-database.md` says nothing about RLS or
API exposure. Once the user decides, that file should carry the decision so it
is not re-litigated.

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
