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
