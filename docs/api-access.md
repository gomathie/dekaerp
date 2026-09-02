# Giving clients API access

DEKA ERP exposes a token-authenticated REST API under `/admin/api/v1/*`,
contributed by the sales, purchases, accounts, inventories, partners,
products, projects, security and support plugins. Generated reference docs
live at `/api/docs`.

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

There is no admin screen for this yet. To revoke:

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

## Reference docs visibility

`/api/docs` is public by default, which suits a product whose customers
integrate against it, but it does publish the full endpoint surface. Set
`SCRIBE_DOCS_PRIVATE=true` to require an authenticated session. Client
integrations are unaffected either way - they authenticate with a token
against `admin/api/*` and never load that page.
