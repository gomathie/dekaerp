# Running the test suite

The host cannot run this project: PHP here is 8.3.2 while `vendor/` requires
`>= 8.4.1`, so every `php artisan` call dies at Composer's platform check
before Laravel boots. Tests run in the project's own Sail container instead.

## The commands

```bash
# 1. Start the test database (Sail's init script creates aureuserp_testing)
docker compose up -d pgsql

# 2. Run a suite - --no-deps because only pgsql is needed
docker compose run --rm --no-deps laravel.test php artisan test --testsuite=AccountFeature

# 3. Or a single file
docker compose run --rm --no-deps laravel.test php artisan test path/to/Test.php
```

`phpunit.xml` pins the connection to the `pgsql` service and database
`aureuserp_testing`, and blanks `DB_URL`/`DATABASE_URL` so `migrate:fresh`
cannot reach a managed database. Never override the host, credentials or the
blanked URLs.

**Give each work package its own database (superseded 2026-10-22).** This
section used to say one shared `aureuserp_testing` and serial runs only. That
is still true *per database*, but the enforced practice now is one database per
work package, created once and reused:

```bash
docker compose exec -T pgsql psql -U sail -d postgres \
  -c "CREATE DATABASE aureuserp_testing_wp<N> OWNER sail;"
docker compose run --rm --no-deps -e DB_DATABASE=aureuserp_testing_wp<N> \
  laravel.test php artisan test --testsuite=SUITE
```

`phpunit.xml` sets `DB_DATABASE` without `force="true"`, so a real environment
variable wins and no shared file has to change. Forgetting `-e DB_DATABASE=...`
silently lands you on `aureuserp_testing`, so treat that as a collision trap
rather than as the default. List the names in use with:

```bash
docker compose exec -T pgsql psql -U sail -d postgres -tAc \
  "SELECT datname FROM pg_database WHERE datname LIKE 'aureuserp%'"
```

**Two `migrate:fresh` runs on one database interleave**, producing errors that
look like broken code - "relation X does not exist" on a table the migration
just created, or "relation Y already exists". Waiting for the other container
to exit is not enough; a new run can start on top of yours mid-suite.

## Two things that will bite

**1. Root-owned files block overwrites.** The bind mount presents host files as
`root`-owned inside the container, while Sail's entrypoint drops to `sail`
(uid 1000) regardless of `docker compose run -u root`. A *new* file is fine -
the directories are world-writable - but an **existing** file cannot be
overwritten by `file_put_contents`. The one that matters is `storage/installed`,
the install marker the test bootstrap rewrites. If a run fails with:

```
file_put_contents(/var/www/html/storage/installed): Failed to open stream: Permission denied
```

delete it (`rm storage/installed`) and let the container recreate it as its
own user. It is gitignored and regenerated on every run. `chmod` from Git Bash
does **not** work here - NTFS ignores it.

The same applies to `vendor/pestphp/pest/.temp/test-results`; deleting it
clears the warning.

**2. An interrupted run poisons the database.** The bootstrap installs into
`aureuserp_testing`; if it dies partway, the next run hits
`relation "..." already exists` and every test fails for a reason that has
nothing to do with the code. Reset before re-running:

```bash
docker exec dekaerp-pgsql-1 psql -U sail -d postgres \
  -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname='aureuserp_testing' AND pid <> pg_backend_pid();"
docker exec dekaerp-pgsql-1 psql -U sail -d postgres -c "DROP DATABASE aureuserp_testing;"
docker exec dekaerp-pgsql-1 psql -U sail -d postgres -c "CREATE DATABASE aureuserp_testing OWNER sail;"
```

## Speed

Each test re-runs `migrate:fresh` and the installer, so a six-test file takes
around 6-7 minutes. Budget accordingly and run the suite you need rather than
everything, unless you have the time for a full pass.
