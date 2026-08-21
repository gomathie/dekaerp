# Neon in production

Local development runs against the `pgsql` service in `docker-compose.yml`.
Neon is the production database. Neon speaks Postgres, so the application needs
no code changes — `DB_CONNECTION` stays `pgsql` and
`Webkul\Support\Database\Dialects\PostgresDialect` handles the dialect. What
follows is the deployment configuration and the sharp edges.

## Connection string

Neon hands you a URL. Pass it as `DB_URL`:

```dotenv
DB_CONNECTION=pgsql
DB_URL=postgresql://USER:PASSWORD@ep-xxxx-pooler.REGION.aws.neon.tech/dekaerp?sslmode=require
DB_SSLMODE=require
```

`DB_URL` overrides host, port, database, username and password wholesale — the
discrete `DB_*` fields are ignored entirely when it is set.

`DB_SSLMODE` is read by `config/database.php`. Neon refuses unencrypted
connections, and the framework default is `prefer`, which succeeds against a
local server and fails against Neon — so it must be `require`.

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

The same applies to the first-time install (`php artisan erp:install`).

## Deploying the production container

`docker/production` supports Neon directly. Pass the connection through the
environment; nothing else needs changing:

```bash
docker run -d \
  -e DB_CONNECTION=pgsql \
  -e DB_URL='postgresql://USER:PASSWORD@ep-xxxx-pooler.REGION.aws.neon.tech/dekaerp?sslmode=require' \
  -e APP_KEY='base64:...' \
  -e APP_URL='https://erp.example.com' \
  -p 80:80 dekaerp:latest
```

Three things make that work:

- The image installs `php-pgsql` alongside `php-mysql`. Without it the container
  cannot open a Postgres connection at all.
- `entrypoint.sh` recognises `DB_CONNECTION`, `DB_URL` and `DB_SSLMODE`, writes
  them into `.env`, and blanks the discrete `DB_*` keys so no stale value is
  left implying a connection that is not used.
- Setting `DB_URL`, or `DB_CONNECTION=pgsql`, or any non-local `DB_HOST` marks
  the database as managed, which keeps the bundled MySQL server switched off in
  Supervisor. The internal-MySQL path is unchanged for deployments still using
  it — `DB_CONNECTION` defaults to `mysql`.

The entrypoint waits up to 60 seconds for the database, retrying rather than
failing fast, which also covers a Neon compute scaling up from zero.

## Serverless changes the cost of the default drivers

`.env.example` ships with:

```dotenv
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Against a local Postgres these are free. Against Neon they are not:

- Every request performs session reads and writes plus cache lookups as
  round-trips to a remote database.
- Supervisor runs `queue:work --sleep=3`, which polls the jobs table every three
  seconds, and `schedule:work`, which wakes every minute. Together they mean the
  Neon compute **never scales to zero**, so it bills continuously.

For production, move these to Redis and keep only real data in Neon:

```dotenv
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

## Tests never touch Neon

`phpunit.xml` blanks `DB_URL` and pins every connection field to the local
Docker `pgsql` service. This is deliberate and should stay that way: the test
bootstrap (`TestBootstrapHelper`) calls `migrate:fresh --force`, which drops
every table on whatever connection is active. Without the blanking, a Neon
`DB_URL` present in the environment would be inherited by the test run and wipe
the production database.

If you ever want the suite to run against Neon, create a separate Neon **branch**
for it and point `DB_URL` at that branch inside `phpunit.xml` — never at the
branch holding real data.

## Other operational notes

- **Cold starts.** A scale-to-zero compute takes a few seconds to wake. The
  first request after idle can look like a timeout; raise the PDO connect
  timeout rather than assuming a fault.
- **Connection limits.** Use the pooled endpoint for anything opening many
  short-lived connections.
- **No auto-created test database.** The Sail Postgres image creates a `testing`
  database from an init script; Neon has no equivalent, so create any extra
  database or branch through the Neon console or API first.
- **Backups.** Neon's branching and point-in-time restore replace the
  `mysqldump` habits the MySQL setup encouraged. `docker/production/mysql-init.sql`
  applies only to the bundled MySQL path and is unused on Neon.
