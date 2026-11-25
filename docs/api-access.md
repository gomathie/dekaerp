# Giving clients API access

DEKA ERP exposes a token-authenticated REST API under `/admin/api/v1/*`,
contributed by the sales, purchases, accounts, inventories, partners,
products, projects, security and support plugins. Reference docs live at
`/api/docs`, sign-in only - see "Reference docs" below.

**This is the only API clients should ever be given.** The database also sits
behind Supabase's Data API, which is deliberately closed - see
[supabase-database.md](supabase-database.md). Never reopen that as a
shortcut: it bypasses every permission, policy and company scope below.

## Onboarding a client

A token inherits the permissions of the user it belongs to, so access is
granted by creating a user, not by creating a token.

1. **Create a dedicated user** for the client - never reuse a human's
   account, and never an admin's.
2. **Give it a role** (Filament Shield) carrying only the resources that
   client needs.
3. **Restrict its companies** if the client should only see one tenant.
4. **Issue a token** by calling `login` as that user, scoping it to what the
   integration actually does.

```bash
curl -X POST https://cloud.dekaerp.com/admin/api/v1/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
        "email": "integration@acme.example",
        "password": "...",
        "token_name": "acme-warehouse-sync",
        "abilities": ["read"]
      }'
```

```bash
curl https://cloud.dekaerp.com/admin/api/v1/sales/orders \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

`Accept: application/json` matters: without it some framework-level errors
render as HTML rather than JSON.

## Token scopes

`abilities` accepts `read`, `write` and `*`, enforced by
`App\Http\Middleware\EnforceApiTokenAbilities`, which maps the HTTP method
onto an ability - safe methods need `read`, everything else needs `write`.
A `["read"]` token cannot POST, PATCH or DELETE anything.

**`abilities` defaults to `["*"]`**, so a caller that omits it gets a
full-access token, and every token issued before scoping existed keeps
working. Always pass `["read"]` for an integration that only reports.

Scoping is coarse by design: it is a blast-radius control, not a replacement
for the role. A `write` token can still write anything its *user* may write,
so the role is what limits which resources are reachable at all.

## Rate limiting

Every plugin API route runs under the `api` limiter
(`App\Providers\AppServiceProvider`), keyed on the token id so one client
cannot spend another's budget. The default is 120 requests/minute per token,
set with `API_RATE_LIMIT`.

The routes are grouped in `Webkul\PluginManager\PackageServiceProvider`
rather than in each plugin's route file - a plugin added later inherits the
throttle automatically, and no route file can forget it.

## Revoking access

**Settings -> API Tokens** issues and revokes them. Issuing asks which user the
token acts as, a label to recognise it by, and its scopes; the token is shown
once, because Sanctum stores only a hash. The table shows every live token,
which user it speaks for, when it was last used and when it expires.

The page is gated on `view_any_security_user` - whoever administers users
administers their tokens.

Revoking is also possible without the screen:

- the client's own `POST /admin/api/v1/logout` drops the token it presented;
- deleting the row in `personal_access_tokens` kills a specific token;
- disabling the user kills every token they hold, which is the reliable
  lever when a client relationship ends.

Tokens expire after `SANCTUM_TOKEN_EXPIRATION` minutes. **Production sets
1440 - 24 hours** (the framework default is 43200). A machine-to-machine
integration therefore has to re-authenticate daily, storing the client's
password to do it. That is a deliberate trade-off, but if long-lived
integrations are the goal, raise it for the API and accept the longer-lived
credential, or build refresh into the client.

## Reference docs

The reference is built from the Scribe endpoint files committed in
`.scribe/endpoints/*.yaml`, read by `Webkul\Support\Services\ApiReference`.

- **`/api/docs`** (Scalar, full reference, every module). **Sign-in only, always**
  (user decision, 2026-11-25): a guest is redirected to `/admin/login`, and so
  is a guest asking for `/api/docs.openapi` or `/api/docs.postman`. Served by
  `Webkul\Support\Http\Controllers\ApiDocsController`; Scribe's own routes are
  off (`scribe.laravel.add_routes = false`). `SCRIBE_DOCS_PRIVATE` no longer
  exists. Scalar is loaded from jsDelivr at a pinned version with a
  Subresource Integrity hash, with telemetry off and no request proxy.

The OpenAPI document and the Postman collection are generated from the
committed files on first request and kept under `storage/framework/cache/api-docs/`
(per container, rebuilt when the files change). Nothing runs at deploy, and no
database or network is needed. Before 2026-11-25 `/api/docs.openapi` read
`storage/app/private/scribe/openapi.yaml`, which only exists where someone ran
`scribe:generate`, so production answered 500 (Sentry DEKAERP-26).

### Regenerating after an API change

Run Scribe locally against a seeded development database (its response calls
send real GET requests to the app inside a transaction), then commit the
changed `.scribe/` files:

```bash
docker compose run --rm laravel.test php artisan scribe:generate
```

That one command is all: `/api/docs` picks up the new files by their content
hash. The files Scribe also writes
(`storage/app/private/scribe/*`, `resources/views/scribe/index.blade.php`) are
unused and ignored. Example responses come from that database, so generate
against seed data, never a copy of a customer's.
