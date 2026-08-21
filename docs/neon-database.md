# Running DEKA ERP on Neon

Neon is Postgres, so the application needs no code changes: `DB_CONNECTION`
stays `pgsql` and `Webkul\Support\Database\Dialects\PostgresDialect` handles the
dialect-specific SQL. What follows is the configuration and the sharp edges.

## Connecting

Neon hands you a connection string. Put it in `DB_URL` and comment out the
discrete fields:

```dotenv
DB_CONNECTION=pgsql
DB_URL=postgresql://USER:PASSWORD@ep-xxxx-pooler.REGION.aws.neon.tech/dekaerp?sslmode=require
DB_SSLMODE=require
# DB_HOST=
# DB_PORT=
# DB_DATABASE=
# DB_USERNAME=
# DB_PASSWORD=
```

`DB_URL` overrides host, port, database, username and password wholesale — the
discrete values are ignored entirely when it is set. Leaving stale ones in place
is harmless but misleading, so comment them out.

`DB_SSLMODE` is read by `config/database.php`; Neon rejects unencrypted
connections, so it must be `require`. (The framework default is `prefer`, which
would silently succeed against a local server and fail against Neon.)

## Pooled vs direct endpoints

Neon gives two hostnames for the same database:

| Endpoint | Host contains | Use for |
| --- | --- | --- |
| Pooled | `-pooler` | The application — web requests, queue workers |
| Direct | no `-pooler` | Migrations and any schema work |

The pooled endpoint runs PgBouncer in transaction mode, which does not carry
session state between statements. Run schema changes against the direct
endpoint:

```bash
DB_URL="postgresql://USER:PASSWORD@ep-xxxx.REGION.aws.neon.tech/dekaerp?sslmode=require" \
  php artisan migrate --force
```

The same applies to the first-time install:

```bash
DB_URL="postgresql://...@ep-xxxx.REGION.aws.neon.tech/dekaerp?sslmode=require" \
  php artisan erp:install
```

## Tests never touch Neon

`phpunit.xml` blanks `DB_URL` and pins every connection field to the local
Docker `pgsql` service. This is deliberate and should stay that way: the test
bootstrap (`TestBootstrapHelper`) calls `migrate:fresh --force`, which drops
every table on whatever connection is active. Without the blanking, a Neon
`DB_URL` sitting in `.env` would be inherited by the test run and wipe the
remote database.

If you do want the suite to run against Neon, create a separate Neon **branch**
for it and point `DB_URL` at that branch inside `phpunit.xml` — never at the
branch holding real data.

## Keeping the local stack

`docker-compose.yml` still defines `pgsql` (host port 5433) and `mysql`. Neither
is required once you are on Neon, but both are worth keeping:

- `pgsql` is what the test suite uses, and lets you work offline.
- `mysql` still holds the pre-upgrade data in the `sail-mysql` volume.

## Operational notes

- **Cold starts.** A scale-to-zero Neon compute takes a few seconds to wake. The
  first request after idle can look like a timeout; raise the PDO connect
  timeout rather than assuming a fault.
- **Connection limits.** Use the pooled endpoint for anything that opens many
  short-lived connections — queue workers and `php artisan serve` included.
- **No auto-created test database.** The Sail Postgres image creates a `testing`
  database from an init script. Neon has no equivalent, so create any additional
  database or branch through the Neon console or API first.
