# Supabase in production

Local development runs against the `pgsql` service in `docker-compose.yml`.
Supabase is the production database. It is plain PostgreSQL, so the application
needs no changes — `DB_CONNECTION` stays `pgsql` and
`Webkul\Support\Database\Dialects\PostgresDialect` handles the dialect. None of
Supabase's other services (auth, realtime, storage, edge functions) are used;
this is a database and nothing more.

Project runs **PostgreSQL 17.6** in `eu-central-1`.

## Connecting: use the session pooler

Supabase offers three connection options. Only one works here.

| Option | Host / port | Verdict |
| --- | --- | --- |
| Direct | `db.<ref>.supabase.co:5432` | **Unusable** — IPv6 only |
| Transaction pooler | `...pooler.supabase.com:6543` | **Unsafe** — breaks migrations |
| **Session pooler** | `...pooler.supabase.com:5432` | **Use this** |

**The direct host has no A record.** Confirmed by lookup: it publishes only
AAAA. Any IPv4-only network — Docker's default bridge, most CI runners, a
Hetzner VPS without IPv6 enabled — cannot resolve it at all. Supabase moved
IPv4 direct connections to a paid add-on.

**The transaction pooler must not be used for schema changes.** It does not
hold session state between statements, which breaks migrations. It exists for
serverless functions; this application runs under php-fpm and is not that.

The **session pooler** behaves like an ordinary Postgres connection, works over
IPv4 and IPv6, and is safe for both application traffic and migrations — so one
connection string covers everything.

```dotenv
DB_URL=
DB_CONNECTION=pgsql
DB_HOST=aws-0-eu-central-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.<project-ref>
DB_PASSWORD=<database password>
DB_SSLMODE=require
```

Two details that cause avoidable failures:

- The pooler username is `postgres.<project-ref>`, **not** `postgres`. The
  direct connection uses the short form; the pooler does not.
- Use the **discrete variables above, not `DB_URL`**. Generated passwords
  routinely contain `@`, `/`, `?` and `#`, which must be percent-encoded inside
  a URL. Getting that wrong produces an authentication failure that looks
  exactly like a wrong password. The discrete fields need no encoding.

Note also that `config/database.php` reads `DB_URL` — not `DATABASE_URL`. A
`DATABASE_URL` line has no effect on this application.

### If the password is rejected

Supabase never displays the database password after project creation; every
connection string in the dashboard shows `[YOUR-PASSWORD]`. If it is not
recorded anywhere, reset it under **Settings → Database → Reset database
password**. There is no way to read the existing one.

## The Data API must stay closed

Not using Supabase's services is not the same as not being exposed by them.
The Data API (PostgREST) is enabled by default and serves every table in
`public` over HTTPS, and Supabase's default privileges grant `anon` and
`authenticated` full DML on tables created there. Laravel migrations create
tables there. Those two defaults together published this database.

**Found 2026-09-02.** Supabase Advisor reported 225 "RLS Disabled in Public"
findings. They were not noise:

| Check | Result |
| --- | --- |
| `has_table_privilege('anon', ..., 'SELECT')` | `true` on `users`, `sessions`, `password_reset_tokens` |
| Grants held by `anon` / `authenticated` | SELECT, INSERT, UPDATE, DELETE, TRUNCATE, REFERENCES, TRIGGER on all **206** tables |
| `GET https://<ref>.supabase.co/rest/v1/` unauthenticated | `401 {"message":"No API key found in request"}` — PostgREST live |

The anon key is public by design, so this was readable and **writable** by
anyone holding it: reset tokens for admin accounts, user emails and bcrypt
hashes, and `TRUNCATE` on every table in the ERP.

**Fixed by removing the grants and the exposure**, not by enabling RLS:

```sql
revoke all on all tables    in schema public from anon, authenticated;
revoke all on all sequences in schema public from anon, authenticated;
revoke all on all functions in schema public from anon, authenticated;
alter default privileges in schema public revoke all on tables    from anon, authenticated;
alter default privileges in schema public revoke all on sequences from anon, authenticated;
alter default privileges in schema public revoke all on functions from anon, authenticated;
```

plus removing `public` from **Settings -> API -> Exposed schemas**.

**RLS is the wrong lever here, despite what the advisor says.** The
application connects as `postgres.<project-ref>`, which owns these tables,
and owners bypass RLS. Enabling it on 206 tables would neither protect nor
break the application's own path, and every new migration would add more
unprotected tables. The grants and the API exposure are the actual control.

**The `alter default privileges` lines are the half that keeps it fixed.**
Without them the next migration creates tables carrying the same grants. They
apply only to objects created by the role that ran them, so verify rather
than assume:

```sql
-- current grants: expect zero rows
select grantee, privilege_type, count(*)
from information_schema.role_table_grants
where table_schema = 'public' and grantee in ('anon', 'authenticated')
group by 1, 2;

-- future tables: expect no anon/authenticated entries
select r.rolname as granted_by, d.defaclobjtype as obj_type, d.defaclacl
from pg_default_acl d
join pg_roles r on r.oid = d.defaclrole
join pg_namespace n on n.oid = d.defaclnamespace
where n.nspname = 'public';
```

Re-run both after any migration that adds tables, and treat a non-empty
result as a live incident rather than a lint warning. Any other Supabase
project used this way - as a plain Postgres host for a non-Supabase app -
has the identical hole until closed the same way.

## Rollback: no branching

Neon offered instant branches, which made "branch before every migration" a
free and complete rollback. **Supabase has no equivalent**, and this is the one
real capability lost in the move.

Replace it explicitly. Before any migration that reaches production:

```bash
pg_dump "postgresql://postgres.<ref>:<pw>@aws-0-eu-central-1.pooler.supabase.com:5432/postgres" \
  --no-owner --no-acl -Fc -f pre-migration-$(date +%Y%m%d-%H%M).dump
```

Verify it before relying on it — a dump that cannot be listed cannot be
restored:

```bash
pg_restore --list pre-migration-*.dump | head
```

Supabase's paid tiers include point-in-time recovery, which is the better
answer once this carries real data. On the free tier, the dump above is your
only rollback.

## Operational limits

- **Free tier pauses after roughly a week of inactivity**, and restoring is a
  manual action in the dashboard — not a transparent wake-up. Daily use never
  triggers it, but an extended shutdown can. A weekly scheduled query removes
  the risk, or a paid plan removes the behaviour.
- **Storage is capped around 500 MB on the free tier.** For five companies
  accumulating invoices, stock moves and chatter, this is the limit that will
  bite first — well before the pause does.
- **Session mode holds a server connection per client connection.** At twenty
  users under php-fpm this is comfortable, but it is the number to watch if
  queue workers are ever scaled out.
- There are no cold starts. Supabase runs an always-on instance; a warm
  connection from the application container measured **0.48s**.

## Keep the local database on the same major version

Supabase runs PostgreSQL 17. Pin `docker-compose.yml` to `postgres:17` so local
development, tests and production agree. Testing migrations against a different
major version undermines the point of testing them.

## Tests never touch production

`phpunit.xml` blanks `DB_URL` and `DATABASE_URL` and pins every connection field
to the local Docker `pgsql` service. This is deliberate and must stay that way:
`TestBootstrapHelper` calls `migrate:fresh --force`, which drops every table on
whatever connection is active.

Note the local `.env` currently points at production Supabase. That is
acceptable while there is no customer data, and must be changed before there
is — the commented local block sits directly above it in the file.
