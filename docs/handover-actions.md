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
