# What is left, and who does it

Everything below needs a person with access this session does not have -
Supabase's dashboard, Laravel Cloud, or production data. Ordered by urgency.

---
---

## 0c. Set TRUSTED_PROXIES, or the POD throttle is one shared bucket (2026-09-30)

**Found by the second WP-5b security pass** (`docs/logistics-plan.md`, handoff log).

`bootstrap/app.php` calls `trustProxies()` only when `TRUSTED_PROXIES` is non-empty,
and `.env.example` ships it **empty**. With nothing trusted, `$request->ip()` returns
the address of whatever connected - on Laravel Cloud, the platform's proxy - for
every request.

The public POD capture route is throttled on two keys. The token key is fine. The
second one:

```php
Limit::perMinute(config('logistics.stop_link.per_ip_per_minute', 120))
    ->by('logistics-stop-link:ip:'.$request->ip()),
```

becomes **a single 120-per-minute bucket shared by every driver on every tenant**.

Why it matters:

- 120 unauthenticated requests a minute - one script, or one busy tenant - returns
  429 to every driver on the platform. POD capture is the step a driver cannot skip
  at the door.
- It contradicts the design. The token limiter is keyed on the token precisely so one
  tenant cannot spend another's budget, but the per-IP limiter sits alongside it and
  lets exactly that happen, at 120/min instead of the tenant's own limit.
- The config comment claims this limit exists "to blunt someone walking the token
  space from one address". When every address is the same address, it cannot tell that
  attacker from the drivers.

This is an availability problem, not a confidentiality one: the token is 64 random
characters and cannot be walked at any rate.

**What to do.** Set `TRUSTED_PROXIES` to the proxy range Laravel Cloud puts in front
of the app, so `$request->ip()` is the real client again.

**Do not reach for `TRUSTED_PROXIES=*` as a shortcut** unless you have confirmed that
the platform overwrites client-supplied `X-Forwarded-For`. If it does not, `*` makes
the header attacker-controlled: every request can present a fresh address and the
per-IP limit is bypassed entirely, which is worse than the shared bucket.

Two follow-ups for whoever holds the code, once the value is known: add a test for
the per-IP limiter (both throttle tests currently exercise only the token key), and
either key that limit on something sturdier or say plainly in
`plugins/webkul/logistics/config/logistics.php` that it is best-effort.

---

## 0a. Check who is left narrower than their colleagues (2026-09-29)

**Do this after deploying the company-wide visibility change**
(`2026_09_28_090000_default_users_to_company_wide_visibility`).

That migration set `users.resource_permission` to `global` for everyone who was
`individual`, so people in a company now see that company's invoices, bills,
orders, stock moves, projects and tasks. It deliberately left `group` rows alone:
nobody ever *chose* `individual` - it was a column default and a hard-coded value in
`UserInvitationService` - whereas `group` had to be selected in the form **and**
given a team, so it is somebody's real decision.

Run this and look at what comes back:

```sql
select id, email, resource_permission
from users
where resource_permission <> 'global'
order by resource_permission, email;
```

Every row is now a deliberate exception rather than an accident. For each one,
decide whether that narrower view is still wanted; if it is not, change it on the
user's record in the admin panel. An empty result is the expected outcome for most
installations.

Two related notes:

- **Tenant isolation is not affected.** `CompanyScope` is a separate scope and this
  change does not touch it. Nobody can see another company's data as a result.
- A `group` user with **no team** used to see everything, because an empty
  authorized-id list was read as "no restriction". That is fixed in code, but such a
  user now sees only their own rows - so if the query above returns `group` users,
  confirm each still has a team, or the fix will look like a regression to them:

```sql
select u.id, u.email
from users u
left join user_team ut on ut.user_id = u.id
where u.resource_permission = 'group' and ut.user_id is null;
```

Background: `docs/company-admin-role-plan.md` sections 4f-4h.

---

## 0b. Stop POD link tokens being retained in edge logs (2026-09-24)

**You must do this wherever requests reach the app before Laravel sees them -
the Laravel Cloud router, and any CDN or proxy in front of it.**

Logistics WP-5b captures proof of delivery through a one-time link whose token
is part of the URL path:

    https://<host>/logistics/pod/<64-character-token>

That token is a credential. Anyone holding it can complete that one delivery
until it is used, expires or is revoked. Because it is in the path rather than a
header or a body, every layer that logs request paths keeps a copy.

What the application already handles:

- The token is stored only as a SHA-256 hash, never in plaintext.
- Each link works once, expires after the company's chosen lifetime (4, 8, 16,
  24 or 48 hours), and can be revoked from the shipment page.
- The capture page sends `noindex` and `no-referrer`, so the URL reaches neither
  a search engine nor a Referer header.
- Sentry events have the token redacted before they leave
  (`AppServiceProvider::redactStopLinkTokensFromSentry`). Sentry records the
  request URL regardless of `send_default_pii`, which covers cookies, the client
  IP and the body but not the URL.

What needs you:

1. In Laravel Cloud, check whether HTTP access logs retain full request paths,
   and for how long. If they do, either shorten retention for them or scrub the
   path segment after `/logistics/pod/`.
2. If a CDN or proxy sits in front - Cloudflare or similar - do the same there.
   Cloudflare's HTTP request logs keep the full URI by default.
3. If neither can scrub, record that here as accepted risk. The mitigation is
   then the link lifetime, which a company can set as low as 4 hours.

Worth doing before the first customer uses stop links. Not urgent before that:
no tokens exist yet, because Logistics is not released.

---

## 0. Point production Sentry at the DEKA ERP project (2026-09-20)

**You must do this in the Laravel Cloud dashboard.** Editing
`.env.laravel-cloud` locally changes nothing in production; that file is a
gitignored local copy, not the deployed environment.

Set, for the `production` environment:

```
SENTRY_LARAVEL_DSN=https://63cac95eb616de29acc760043c3d4288@o4511967103811584.ingest.de.sentry.io/4512018100322384
```

Verified, not assumed: `php artisan sentry:test` with this DSN produced event
`06dcfd82e8dd4b1caf3e51c2dc013e5f`, and the Sentry MCP then found it in
`365-s3/dekaerp` as `DEKAERP-1`. So this DSN provably reaches the DEKA ERP
project.

**Why it needs changing.** Production was configured with a different project
(id `4512018098815056`; the DEKA ERP project is `4512018100322384`). The
organisation holds only two projects, `dekaerp` and `osworksheet`, so
production has most likely been reporting into `osworksheet`. That matches what
the review found: `dekaerp` had **zero events in ninety days** for a live ERP
with real customers.

Not fully proven, and worth thirty seconds of your time: the MCP exposes issues
and events but not project keys, so compare the two ids under **Settings →
Client Keys (DSN)** in Sentry, or simply set this DSN and watch `dekaerp` start
receiving events.

Leave the other Sentry values in Laravel Cloud as they are - they are already
correct for production, and the local ones must **not** be copied over them:

| Key | Production | Local | Note |
|---|---|---|---|
| `SENTRY_TRACES_SAMPLE_RATE` | `0.1` | `1.0` | 1.0 would trace every request |
| `SENTRY_PROFILES_SAMPLE_RATE` | `0.1` | `1.0` | same |
| `SENTRY_SEND_DEFAULT_PII` | `false` | `false` | keep false |
| `SENTRY_ENABLE_LOGS` | `true` | `true` | keep - it is the only reason the plugin-install `logger()->error()` reaches Sentry at all |

`SENTRY_ENVIRONMENT` is optional. When empty the SDK falls back to
`$this->app->environment()`
(`vendor/sentry/sentry-laravel/src/Sentry/Laravel/ServiceProvider.php:301-303`),
which is already `production` there.

Afterwards: resolve `DEKAERP-1`, which is the test exception sent from here.

Still open even once the DSN is right - the intermittent plugin-install
failures will **not** appear as Sentry issues, because those catch blocks do not
report. See `docs/change-log.md` (2026-09-20) for the two `report($e)` lines
that would fix it.

---

## 1. Supabase - DONE (2026-09-03)

Closed out. For the record, what was done and what it means:

- Grants revoked from `anon`/`authenticated` on all 206 tables, and
  `alter default privileges` changed so future migrations cannot re-grant.
  Verified empirically: a throwaway table came back with zero grants.
- Data API shut. The repeating `schema "pg_pgrst_no_exposed_schemas" does not
  exist` errors in the Postgres log are the *expected* consequence of removing
  every exposed schema - PostgREST cannot build a schema cache and reports
  itself unready. Harmless here, since nothing uses that API, but it will keep
  logging. If a "disable Data API" toggle is available, prefer it over
  no-exposed-schemas to stop the noise.
- `password_reset_tokens` truncated, so any token harvested while the table
  was readable is dead.
- JWT secret rotated, invalidating every previously issued `anon` and
  `service_role` key. This did not touch the database connection - Laravel
  authenticates with `DB_USERNAME`/`DB_PASSWORD` over the Postgres protocol,
  a separate credential path - and the app was confirmed working afterwards.

**The forensic question stays formally open.** Free-tier log retention is one
day and the exposure was closed the day before, so whether anyone read those
tables cannot now be established. The two steps above make it moot rather than
answered: harvested tokens are void and old keys are dead. Worth remembering
the distinction if this is ever written up.

Also note the log filter that hid the answer: `Pathname = /rest/v1/` is an
exact match and never matches a real request such as
`/rest/v1/password_reset_tokens`. Use *contains*, or filter by the Postgrest
log type alone.

## 2. Rotate the Nightwatch token - DONE (2026-09-03)

Deleted from Cloud, but it is still a live credential for a service you no
longer run, and it appeared in a chat transcript. Rotate or revoke it at
Nightwatch.

---

## 3. Verify logging actually works

Needs a redeploy first, since Cloud does not pick up env changes without one.

```php
Log::info('logging check');
```

| Where it appears | Meaning |
| --- | --- |
| Cloud Logs tab **and** Sentry Logs | Correct. Done. |
| Sentry only | `laravel-cloud-socket` is not resolving in your runtime. |
| Cloud Logs only | `SENTRY_ENABLE_LOGS` is still not parsing as a boolean. |
| Neither | `LOG_CHANNEL` is wrong again - check for stray trailing comments. |

Do not put `#` comments on the same line as a value in the Cloud env editor.
That is what silently disabled `SENTRY_ENABLE_LOGS` before.

---

## 4. Before deploying the backport

Two things no test here can reach.

**4a. Multi-tax invoices - now covered by tests, except one flag.**

`TaxInclusiveBatchTest` (added 2026-09-03, passing) covers the `price_include`
branch of `sharesBatch()`: an inclusive tax is carved out of the entered price,
two inclusive taxes share one base, and inclusive and exclusive taxes stay in
separate batches. The manual staging check for tax-inclusive pricing is no
longer needed.

**Still worth a look only if your invoices use `include_base_amount`**
(a tax that compounds onto the base of the next). The test helper has no
affordance for it, so it is untested. If you use it, build one such invoice on
staging and compare the tax total against the same invoice before the change.

**4b. Sequence numbering against real data - the remaining deploy gate.**

Numbering moves from `{prefix}/{database id}` to a sequence. Continuity relies
on `initialFromNames()` reading existing names and starting above the highest.
A fresh database has no such history, so this cannot be tested by the suite.

There is no staging environment, but this does not need one - only a throwaway
database, and the local Docker Postgres already running is enough. Rehearse
there, never against production.

**Step 1 - dump production.** Session pooler on 5432; the transaction pooler
on 6543 will not produce a clean dump.

```bash
docker run --rm -v "C:\Users\gomat\Downloads:/backup" postgres:17-alpine \
  pg_dump "postgresql://postgres.<ref>:<pw>@aws-0-eu-central-1.pooler.supabase.com:5432/postgres" \
  --no-owner --no-acl -Fc -f /backup/prod.dump
```

**Step 2 - restore into a throwaway local database.** Name it something that
could never be confused with the dev or test database.

```bash
docker exec dekaerp-pgsql-1 psql -U sail -d postgres \
  -c "CREATE DATABASE seq_rehearsal OWNER sail;"

docker run --rm --network dekaerp_sail -v "C:\Users\gomat\Downloads:/backup" \
  postgres:17-alpine pg_restore --no-owner --no-acl \
  -d "postgresql://sail:password@pgsql:5432/seq_rehearsal" /backup/prod.dump
```

**Step 3 - note the numbers the old scheme produced**, before migrating.

```sql
select journal_id, max(name) from accounts_account_moves
where name is not null group by journal_id;
```

**Step 4 - run the migration against the copy.** Every DB_* value is passed
explicitly so nothing can fall back to the production connection in `.env`.

```bash
docker compose run --rm --no-deps \
  -e DB_URL= -e DATABASE_URL= -e DB_CONNECTION=pgsql -e DB_HOST=pgsql \
  -e DB_PORT=5432 -e DB_DATABASE=seq_rehearsal -e DB_USERNAME=sail \
  -e DB_PASSWORD=password \
  laravel.test php artisan migrate --force
```

**Step 5 - check continuity.** This is the whole point of the exercise.

```sql
select code, scope_type, scope_id, company_id, prefix, next_number, padding
from sequences order by id;
```

For each journal, `next_number` must be **greater than** the highest number
already used by that journal in step 3. If it comes back 1 while invoices
exist, `initialFromNames()` did not read the existing names and deploying
would reissue numbers already on real documents. Stop and say so if that
happens.

**Step 6 - post one invoice on the copy** through the UI or tinker, and
confirm the number continues the run rather than colliding. Expect the
zero-padded form: `INV/2026/00043`.

**Step 7 - drop the rehearsal database.**

```bash
docker exec dekaerp-pgsql-1 psql -U sail -d postgres \
  -c "DROP DATABASE seq_rehearsal;"
```

Do not paste the production connection string into a chat - it carries the
database password.

## 5. Optional, no urgency

- **Filament 5.7.6** - needs `composer update`, so it needs the container:
  `docker compose run --rm --no-deps laravel.test composer update filament/filament`.
- **Restructure backlog** - `docs/restructure-backlog.md`. 70 resources are
  mechanically safe to adopt, 51 hold local changes that must be carried over
  by hand. Pure merge-debt reduction; nothing breaks by leaving it.
- **Remaining suites** - most of `AccountFeature`, plus project, product and
  website. The harness works; see `docs/running-tests.md`.
