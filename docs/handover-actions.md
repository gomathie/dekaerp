# What is left, and who does it

Everything below needs a person with access this session does not have -
Supabase's dashboard, Laravel Cloud, or production data. Ordered by urgency.

---
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

## 2. Rotate the Nightwatch token

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

**4a. Multi-tax invoices - this one moves money.**

`TaxComputer` now batches taxes that share characteristics, where before each
was its own batch. `TaxGroupTest` proves a group tax with 10% + 5% children on
a 200 base still yields 30.00, but `sharesBatch()` also branches on
`price_include` and `include_base_amount`, and no test covers those.

If your invoices use tax-inclusive pricing, or a tax with "include base
amount", build one such invoice on staging and compare the tax total against
the same invoice before the change. If they do not, this does not apply.

**4b. Sequence numbering against real data.**

Numbering moves from `{prefix}/{database id}` to a sequence. Continuity relies
on `initialFromNames()` reading existing names and starting above the highest.
A fresh test database has no historical invoices, so this is untestable here.

Restore a copy of production, run `php artisan migrate`, then check:

```sql
select name from accounts_account_moves
where name is not null order by id desc limit 5;

select code, scope_type, scope_id, company_id, prefix, next_number, padding
from sequences order by id;
```

`next_number` must be **above** the highest existing number for that journal.
Then post one invoice on the restored copy and confirm the number continues
rather than colliding.

Expect the format to gain zero-padding: `INV/2026/42` becomes
`INV/2026/00043`. That was your decision; `padding` is editable per sequence
in Settings -> Sequences if you change your mind.

---

## 5. Optional, no urgency

- **Filament 5.7.6** - needs `composer update`, so it needs the container:
  `docker compose run --rm --no-deps laravel.test composer update filament/filament`.
- **Restructure backlog** - `docs/restructure-backlog.md`. 70 resources are
  mechanically safe to adopt, 51 hold local changes that must be carried over
  by hand. Pure merge-debt reduction; nothing breaks by leaving it.
- **Remaining suites** - most of `AccountFeature`, plus project, product and
  website. The harness works; see `docs/running-tests.md`.
