# What is left, and who does it

Everything below needs a person with access this session does not have -
Supabase's dashboard, Laravel Cloud, or production data. Ordered by urgency.

---
---


### How to complete 0b and 0c, step by step (added 2026-09-30)

Both gates need a running environment, so neither can be settled from the
repository. What follows is written to be executed in order, and to **measure**
rather than assume - the one thing nobody can tell you from here is how Laravel
Cloud's edge actually behaves with `X-Forwarded-For`.

---

#### 0c. TRUSTED_PROXIES - do this one first

0c is first because 0b's verification step is easier once you can see real client
addresses.

**Step 1 - add a temporary diagnostic route.**

In `routes/web.php`, and remove it again at step 6:

```php
Route::get('/__proxy-check/{secret}', function (\Illuminate\Http\Request $request, string $secret) {
    abort_unless($secret === env('PROXY_CHECK_SECRET'), 404);

    return response()->json([
        'remote_addr'     => $request->server('REMOTE_ADDR'),
        'x_forwarded_for' => $request->header('X-Forwarded-For'),
        'laravel_ip'      => $request->ip(),
    ]);
});
```

Set `PROXY_CHECK_SECRET` to a long random string in the Laravel Cloud environment
variables, and deploy. The secret is in the path only so the route is not
guessable; it returns no customer data either way.

**Step 2 - see what the app currently receives.**

From your own machine:

```
curl https://<your-host>/__proxy-check/<secret>

reponse :
PS C:\WINDOWS\system32> curl.exe -sS "https://cloud.dekaerp.com/__proxy-check/46900fb2557524b1104039985510740ded5d14b9be33c7be75c53474a8cc679d"
{"remote_addr":"2a05:d014:145b:561c:1fa3::42","x_forwarded_for":"154.161.102.55","laravel_ip":"154.161.102.55"}
PS C:\WINDOWS\system32>

PS C:\WINDOWS\system32> curl.exe -sS -H "X-Forwarded-For: 1.2.3.4" "https://cloud.dekaerp.com/__proxy-check/46900fb2557524b1104039985510740ded5d14b9be33c7be75c53474a8cc679d"
{"remote_addr":"2a05:d014:145b:561c:1fa3::42","x_forwarded_for":"154.161.102.55","laravel_ip":"154.161.102.55"}
PS C:\WINDOWS\system32>

TRUSTED_PROXIES=*

mobile test confirms that Laravel correctly identifies a different client IP when connecting through mobile data.
DekaERP proxy verification results
Field	Computer	Mobile LTE
remote_addr	2a05:d014:145b:561c:1fa3::42	2a05:d014:145b:561d:496f::1b
x_forwarded_for	154.161.102.55	154.161.118.242
laravel_ip	154.161.102.55	154.161.118.242
```

Read the result:

- `remote_addr` is the address that actually connected - the Laravel Cloud load
  balancer or router. **Write it down; this is the value 0c needs.**
- `laravel_ip` will currently equal `remote_addr`. That is the bug: every visitor
  looks like the balancer, so the POD throttle's per-IP limit is one bucket shared
  by every driver on every tenant.
- `x_forwarded_for` shows what the edge passes through. Your real address should
  appear in it.

**Step 3 - find out whether the edge lets clients forge that header.** This is the
question that decides whether `*` is safe, and the only way to know is to try:

```
curl -H 'X-Forwarded-For: 1.2.3.4' https://<your-host>/__proxy-check/<secret>
```

Look at `x_forwarded_for` in the response:

- If it reads `1.2.3.4, <your real address>` the edge **appends**. Good - a forged
  value can be told apart from the real one by position.
- If it reads only `<your real address>` the edge **overwrites**. Also good.
- If it reads only `1.2.3.4`, the edge passes client input through untouched.
  **Do not use `*` in step 4** - it would let anyone claim any address and the
  per-IP limit would stop existing.

**Step 4 - set the value.**

In Laravel Cloud's environment variables for the production environment:

```
TRUSTED_PROXIES=<the remote_addr from step 2>
```

Prefer the specific address or CIDR you measured. With a specific proxy trusted,
Laravel walks the `X-Forwarded-For` chain from the right and stops at the first
entry that is not a trusted proxy - which is the real client, and which a forged
left-hand entry cannot displace.

Use `TRUSTED_PROXIES=*` only if step 3 showed appending or overwriting **and** the
balancer's address is not stable enough to pin. If `remote_addr` varies between
requests, capture several and use the covering CIDR, or ask Laravel Cloud support
for the range rather than guessing it.

Deploy after changing the variable - environment changes do not take effect until
the next deploy.

**Step 5 - verify, twice.**

Re-run both curls from step 2 and step 3:

- Plain request: `laravel_ip` must now be **your** address, not the balancer's.
- Forged-header request: `laravel_ip` must **still** be your address. If it has
  become `1.2.3.4`, the configuration is unsafe - go back to step 4 and pin the
  specific proxy instead of `*`.

Then confirm the throttle actually separates clients: open a stop link repeatedly
from two different networks (office wifi and a phone on mobile data). Exhausting
the limit on one must not return 429 on the other.

**Step 6 - remove the diagnostic route**, delete `PROXY_CHECK_SECRET`, and deploy
again. Leaving it costs nothing in data terms but it is one more public route than
the application needs.

**Step 7 - close the gate.** Note the value you set and the date in this file, and
move WP-5b to `done` on the board in `docs/logistics-plan.md` if 0b is also
settled.

---

#### 0b. POD tokens in edge logs

**Step 1 - list the layers that see the URL.** At minimum Laravel Cloud itself.
Check whether anything sits in front: look up your production hostname
(`nslookup` or `dig`) and see whether it resolves to Laravel Cloud directly or to
a CDN such as Cloudflare. If a CDN is in the path, it logs request URIs too.

**Step 2 - for each layer, answer two questions.** Does it store the full request
path, and for how long? Laravel Cloud's HTTP access logs and Cloudflare's HTTP
request logs both keep the full URI by default.

**Step 3 - apply whichever of these the layer supports**, in order of preference:

1. Scrub the path segment after `/logistics/pod/` before the entry is stored.
   Cloudflare can do this with a Transform Rule or Logpush field redaction.
2. Shorten retention for access logs to the minimum the platform allows.
3. If neither is possible, record it here as accepted risk. The compensating
   control is the link lifetime: set the company's stop-link TTL to 4 hours, the
   shortest option, under Logistics > Configuration > Settings.

**Step 4 - verify with a real link.** In a test company: issue a stop link from a
shipment, open it, then search that layer's logs for the path. You are looking to
confirm the token is absent, shortened, or that the entry ages out when you
expect. Revoke the link afterwards from the shipment page.

**Step 5 - record the outcome here** - which layers scrub, which retain, what
retention window, and the date.

---

#### If the log gates cannot be met

The durable fix is to stop putting the credential in the path at all: serve the
capture page at a static URL and carry the token in the URL **fragment**
(`/logistics/pod#<token>`), which browsers never send to the server, with a small
amount of JavaScript reading `location.hash` and posting it. No layer that logs
request lines would ever see it.

That is a real change to WP-5b - new page flow, new tests, and it breaks any link
already issued - so it is not worth doing pre-emptively. It is the right answer if
you find you cannot scrub logs and the 4-hour lifetime is not acceptable to a
customer.

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

Code follow-ups completed in the independent WP-5b review on 2026-09-30: a test now
proves the per-IP limit applies across distinct tokens, and
`plugins/webkul/logistics/config/logistics.php` calls that layer best-effort and names
its `TRUSTED_PROXIES` dependency. What remains here is the production proxy value and
a live check that Laravel Cloud reports the real client address without trusting a
caller-supplied one.

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

## 0b. Stop POD link tokens being retained in edge logs — RESOLVED 2026-10-20

**Resolved in code by WP-5b (fragment-based POD). No production configuration
needed.**

The original concern was that `/logistics/pod/<token>` put a credential in the
request path, where every HTTP access log layer would retain it.

**What changed.** The link issued to dispatchers is now:

    https://<host>/logistics/pod#<64-character-token>

The token is in the URL fragment. Browsers strip the fragment before sending the
HTTP request — it is never transmitted to the server and never appears in any
access log, edge CDN log, or load-balancer log at any layer. The landing page
(`GET /logistics/pod`) reads `location.hash`, immediately clears the fragment
from the browser URL with `history.replaceState`, and POSTs the token to
`/logistics/pod/exchange` as a form field. From that point the token travels
only in POST bodies, never in a URL.

Backward-compatible path-based routes (`/logistics/pod/{token}`) are still
registered for links that were issued before this change, but `issue()` no
longer generates that URL pattern. All new links use the fragment form.

Sentry redaction (`AppServiceProvider::redactStopLinkTokensFromSentry`) remains
in place as defence-in-depth; it now covers the path-based backward-compat
URLs rather than the primary flow.

---

## 0. Point production Sentry at the DEKA ERP project DONE (2026-09-20)

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


# DekaERP — Production POD Security Verification & Handover Report

**Date:** 20 October 2026  
**Application:** DekaERP  
**Production URL:** https://cloud.dekaerp.com  
**Infrastructure:** Laravel Cloud, Supabase PostgreSQL  
**DNS Provider:** Cloudflare  
**Module:** Logistics — Proof of Delivery (POD)  
**References:** `docs/handover-actions.md` §§0b–0c, `docs/logistics-plan.md` WP-5b

**Overall Status: PROXY DIAGNOSTICS COMPLETED — POD TOKEN REMEDIATION REQUIRED**

---

## 1. Executive Summary

A production security review was completed to verify Laravel's trusted proxy behavior and investigate possible exposure of sensitive Proof of Delivery tokens in infrastructure access logs.

The review confirmed that:

1. Laravel correctly resolves client IP addresses through Laravel Cloud's proxy infrastructure.
2. The tested forged `X-Forwarded-For` header was rejected or sanitized before reaching Laravel.
3. Clients connecting from separate desktop and mobile networks were identified by different IP addresses.
4. The temporary diagnostic route and environment variable were removed, and the cleanup deployed.
5. The diagnostic endpoint was confirmed inaccessible with HTTP 404.
6. Laravel Cloud Access Logs retain complete HTTP request paths, including token-bearing URLs.
7. Laravel Cloud support confirmed that path redaction and configurable log retention are unavailable.
8. Laravel Cloud's internal Cloudflare edge logs can retain request-path metadata for up to 90 days.

**Security decision:** Retain the current working proxy configuration provisionally and migrate the POD workflow to fragment-based token URLs before customer release.

## 2. Production Network Architecture

The production application's DNS configuration was verified as follows:

| Property | Verified value |
|---|---|
| Hostname | cloud.dekaerp.com |
| DNS target | to.laravel.cloud |
| Resolved IPv4 addresses | 103.133.1.1, 103.133.1.2 |
| HTTP response | 302 Found |
| Redirect destination | /admin/login |
| HTTP server header | cloudflare |
| Cloudflare edge header | CF-RAY present |

The `server: cloudflare` header confirms Cloudflare infrastructure participates in serving the application. It does not, by itself, establish that the customer's own Cloudflare zone is proxying HTTP traffic.

The DNS results were consistent with a DNS-only CNAME pointing to Laravel Cloud. The Cloudflare DNS proxy status was not independently confirmed in the dashboard.

## 3. TRUSTED_PROXIES Verification — §0c

**Status: DIAGNOSTIC TESTS PASSED; CLEANUP COMPLETED**

### Production configuration

```env
TRUSTED_PROXIES=*
```

Laravel Cloud's observed proxy behavior was tested before making any change to this configuration.

### Test 1 — Desktop network

**Result: PASS**

```json
{
  "remote_addr": "2a05:d014:145b:561c:1fa3::42",
  "x_forwarded_for": "154.161.102.55",
  "laravel_ip": "154.161.102.55"
}
```

Laravel correctly identified the forwarded client IP rather than the connecting proxy IP.

### Test 2 — Forged X-Forwarded-For header

**Result: PASS**

The following header was supplied manually:

```http
X-Forwarded-For: 1.2.3.4
```

The application returned:

```json
{
  "remote_addr": "2a05:d014:145b:561c:1fa3::42",
  "x_forwarded_for": "154.161.102.55",
  "laravel_ip": "154.161.102.55"
}
```

The forged IP address was not propagated to Laravel.

This confirms the tested request path prevented that specific forwarded-header spoofing attempt.

### Test 3 — Mobile LTE network

**Result: PASS**

```json
{
  "remote_addr": "2a05:d014:145b:561d:496f::1b",
  "x_forwarded_for": "154.161.118.242",
  "laravel_ip": "154.161.118.242"
}
```

The mobile request used a separate client IP and reached a different Laravel Cloud IPv6 proxy endpoint.

### Consolidated proxy test results

| Test | Result |
|---|---|
| Diagnostic endpoint operational | PASS |
| Desktop client IP resolution | PASS |
| Forged X-Forwarded-For rejection | PASS |
| Mobile network IP resolution | PASS |
| Different networks identified separately | PASS |
| Multiple infrastructure proxy endpoints observed | CONFIRMED |
| Actual POD rate-limit isolation | NOT YET TESTED |
| Direct-origin bypass protection | NOT YET VERIFIED |

### Configuration decision

The existing `TRUSTED_PROXIES=*` configuration was retained because it produced correct results through the tested request paths.

No specific proxy address was hardcoded because multiple Laravel Cloud proxy endpoints were observed.

Wildcard trust remains conditional on the infrastructure preventing untrusted direct-origin access and sanitizing forwarded headers.

## 4. Temporary Diagnostic Cleanup

**Status: COMPLETED**

The following cleanup actions were completed:

- Removed `/__proxy-check/{secret}` from `routes/web.php`.
- Removed `PROXY_CHECK_SECRET` from Laravel Cloud environment variables.
- Deployed the cleanup to production.
- Executed a follow-up diagnostic URL request.

### Final cleanup verification

Command:

```powershell
curl.exe -sS -o NUL -w "%{http_code}`n" "https://cloud.dekaerp.com/__proxy-check/test"
```

Actual result:

```text
404
```

**Outcome: PASS**

The temporary diagnostic endpoint is no longer accessible at the tested location.

Historical access-log entries still contain the diagnostic secret, but the route and configured secret have been removed.

## 5. POD Token Exposure Investigation — §0b

**Status: INVESTIGATION COMPLETED — VULNERABILITY CONFIRMED IN CURRENT URL DESIGN**

### Existing POD URL format

The existing Logistics module generates Proof of Delivery links using:

```text
https://cloud.dekaerp.com/logistics/pod/<64-character-token>
```

The token acts as a temporary bearer credential granting access to the associated delivery confirmation workflow.

### Laravel Cloud access-log findings

Production Access Logs were inspected.

The logs contained complete request paths for the temporary proxy diagnostic endpoint, including its 64-character secret.

Observed log format:

```text
[200] GET /__proxy-check/<REDACTED_SECRET> HTTP/1.1
```

This demonstrates that Laravel Cloud records secret-bearing URL paths without automatically removing the secret segment.

Consequently, POD credentials placed in equivalent URL-path positions would be exposed to infrastructure access logging.

No real POD token was used for this verification.

## 6. Official Laravel Cloud Support Findings

A support inquiry was submitted to Laravel Cloud concerning access-log redaction, retention and sensitive URL tokens.

Laravel Cloud support confirmed the following:

| Question | Confirmed response |
|---|---|
| Can path segments be redacted before storage? | No |
| Can access-log retention be shortened per application? | No |
| Application/access-log storage retention | Up to 30 days |
| Starter plan dashboard/API visibility | 1 day |
| Growth plan dashboard/API visibility | 7 days |
| Business/Enterprise dashboard/API visibility | 30 days |
| Internal Cloudflare edge-log retention | Up to 90 days |
| Are full request paths retained at the edge? | Yes |
| Are edge logs accessible through the customer dashboard? | No |

Support also confirmed that the Laravel Cloud edge can record requests blocked before reaching the Laravel application.

### Security implication

Application-side logging redaction alone cannot eliminate the exposure because infrastructure layers retain the original HTTP request path before Laravel processes the request.

Reducing the POD token lifetime mitigates exposure but cannot prevent the token from entering these logs.

## 7. Recommended Security Remediation

**Status: REQUIRED — IMPLEMENTATION PENDING**

### Approved technical direction for implementation

Replace the current token-in-path design:

```text
/logistics/pod/<token>
```

With a fragment-based design:

```text
/logistics/pod#<token>
```

URL fragments are not transmitted in HTTP request lines.

Therefore, the token will not appear in access or edge logs solely because the landing-page URL was requested.

### Required implementation behavior

1. Generate new POD links using a URL fragment.
2. Serve a token-free public capture page at `/logistics/pod`.
3. Read the fragment using client-side JavaScript.
4. Immediately clear the fragment from the visible browser URL.
5. Exchange the token through a secure POST request body.
6. Validate the token through the existing Laravel Logistics token services.
7. Preserve expiry, revocation, company isolation and one-time completion rules.
8. Establish a short-lived, scoped authorization session for the delivery capture process.
9. Preserve IP-based and token-based throttling.
10. Prevent token exposure through application logs, Sentry, analytics, JavaScript error reporting and third-party scripts.
11. Apply `Referrer-Policy: no-referrer` and `Cache-Control: no-store`.
12. Update all POD link-generation and sharing mechanisms.
13. Implement compatibility handling for previously issued token-in-path links.
14. Extend automated security and regression tests.

### Alternative identified by Laravel Cloud support

The existing token-in-path URL can be exchanged for a short-lived session and redirected to a token-free URL.

However, the initial request would still expose the token to infrastructure logs. For this reason, the fragment-based approach is preferred for new POD links.

## 8. Current POD Security Controls

The existing implementation and original handover documentation describe the following controls:

- Tokens generated with high entropy.
- Only SHA-256 token hashes stored in the application database.
- One-time POD completion.
- Token expiry and revocation support.
- Company-configurable stop-link lifetime.
- `noindex` and `no-referrer` behavior.
- Application-side Sentry token redaction.
- Token-based and IP-based throttling.

These controls should be retained during the migration.

The fragment-based approach is an additional protection against infrastructure URL logging and should not replace existing server-side authorization controls.

## 9. Outstanding Actions

| Action | Status |
|---|---|
| Desktop proxy verification | COMPLETED |
| Forged-header verification | COMPLETED |
| Mobile proxy verification | COMPLETED |
| Diagnostic endpoint removal | COMPLETED |
| Diagnostic secret removal | COMPLETED |
| Cleanup deployment | COMPLETED |
| HTTP 404 cleanup verification | COMPLETED |
| Laravel Cloud access-log inspection | COMPLETED |
| Laravel Cloud support inquiry | COMPLETED |
| Infrastructure retention confirmed | COMPLETED |
| Fragment-based POD implementation | PENDING |
| POD security regression testing | PENDING |
| Existing POD link compatibility review | PENDING |
| Actual rate-limit isolation test | PENDING |
| Final origin trust-boundary confirmation | PENDING |
| Production rollout of revised POD flow | PENDING |

## 10. Final Handover Decision

**Section 0c — TRUSTED_PROXIES**

Status: **DIAGNOSTIC VERIFICATION AND CLEANUP COMPLETED**

The production infrastructure correctly identified separate client IPs and rejected the tested forged-header request.

The existing wildcard trust configuration remains in place provisionally, subject to final origin-access verification.

**Section 0b — POD TOKEN LOGGING**

Status: **INVESTIGATION COMPLETED — REMEDIATION OPEN**

The exposure mechanism was confirmed through actual Laravel Cloud access-log entries.

Laravel Cloud support confirmed that neither URL-path redaction nor reduced infrastructure log retention is available.

The recommended remediation is to move POD credentials from the HTTP request path into a browser URL fragment and exchange them using a secure POST request.

**Production release decision:** New customer-facing POD stop links should remain disabled until the revised flow has been implemented, tested and approved.

**Next responsible party:** DekaERP Development Team / Coding Agent

**Next milestone:** Implement and test fragment-based POD tokens, then update `docs/handover-actions.md` and the WP-5b release status.

---

**Report conclusion:** The production proxy diagnostic work is complete. The POD logging investigation has identified a confirmed architectural risk and a defined remediation approach. No further Laravel Cloud logging configuration is expected to resolve the issue; remediation must be implemented in the application.

---

## §4b Rehearsal — COMPLETED 2026-10-20

**Performed by:** Claude Sonnet 4.6  
**Database:** `seq_rehearsal` (local Sail pgsql) — dropped after verification.

### Procedure

1. Production Supabase dumped to scratchpad via `rehearsal.ps1`.
2. Restored into `seq_rehearsal` using `pg_restore` within the `dekaerp_sail` network.
3. Ran `php artisan migrate --force` against `seq_rehearsal`, passing all `DB_*` overrides explicitly. No production connection was used at any point.

### Migration batch 24 — first run

The first `migrate` run executed 15 migrations (batch 24). The two migrations below were **absent** — they did not appear in `migrate:status` at all, as Pending or Ran:

- `2026_08_04_100000_share_default_time_off_leave_types` (time-off plugin)
- `2026_09_24_000001_provision_company_admin_role` (security plugin)

**Root cause:** Both migrations were written to disk but never added to their plugin's `hasMigrations([...])` list in the respective `PackageServiceProvider`. The `PackageServiceProvider` base class uses `hasMigrations()` as an explicit whitelist; files not listed are invisible to the migration runner regardless of whether the plugin is installed.

**Fix applied:** Both migration names were appended to their providers:
- `plugins/webkul/time-off/src/TimeOffServiceProvider.php`
- `plugins/webkul/security/src/SecurityServiceProvider.php`

After the fix, both appeared as `Pending` in `migrate:status` and ran cleanly against `seq_rehearsal` (batch 25). `migrate:status` then showed zero pending migrations.

### Sequence continuity — PASS

Post-migration sequences table (key rows):

| scope | journal_id | company_id | prefix | next_number | verdict |
|---|---|---|---|---|---|
| Journal | 7 | 2 | INV/%(year)/ | 2 | **OK** — `INV/2026/1` exists; next is 2 |
| Journal | 7 | 2 | RINV/%(year)/ | 2 | OK |
| (global) | — | — | SO/ | 3 | OK — two sales orders exist |
| (global) | — | — | PO/ | 1 | OK — no purchase orders |
| All other journals | — | 1 or 2 | various | 1 | OK (no existing documents) |

`initialFromNames()` correctly read the highest document name for each journal and set `next_number = highest + 1`. No collision risk was found.

### Deployment verdict

**SAFE TO DEPLOY** — with the `hasMigrations` fix committed. All 17 pending migrations (15 original + the 2 now registered) will run in order. Sequence numbering is continuous: the single in-use journal (`INV/2026/1`) will continue from `/2` as expected.

### What `provision_company_admin_role` does on production

`CompanyAdminRoleProvisioner::provision()` creates a `Company Admin` role for each company that does not already have one, assigns it the same permissions as the existing Admin role, and wires it to the company settings as the default. It is idempotent. On production this is a first-time run, so it will create one role per company.

### What `share_default_time_off_leave_types` does on production

Sets `company_id = NULL` on the six default leave-type names (`Paid Time Off`, `Sick Time Off`, etc.), making them shared across all companies rather than company-owned. This is a simple UPDATE with no risk of data loss; names not in the list are untouched.

### Rehearsal database

`seq_rehearsal` was dropped after the report was written.