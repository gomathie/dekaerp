# CRM plugin — adoption plan

Status: **draft, awaiting decisions** (2026-11-07). Nothing has been ported yet.
Owner of decisions: the user. Work happens on its own branch,
`feature/crm`, never mixed with SaaS or Logistics work.

## 1. What upstream has

AureusERP added a `crm` plugin (`plugins/webkul/crm`, namespace `Webkul\Crm`)
between 2026-10-19 and 2026-11-05, after this fork's last sync point
(`8409fde62`, 2026-08-04). Upstream `aureus/master` at `c31a0db21`
(2026-11-06) was inspected read-only.

| Area | Size |
|---|---|
| Files in total | 858 |
| Translations | 395 files: en, ar, es, fr and pt_BR. Ours also carry fr. |
| Filament resources and pages | 240 files in clusters: Leads, Sales (pipeline, opportunities, quotations, customers, activities), Reporting (forecast, reports), Configuration |
| Models | 27, including Lead, Stage, CRMTeam, TeamMember, LostReason, Tag, MailServer, MailAlias, InboundEmail, LeadMerge, LeadConversion (plus mass conversion), RevealRule, RevealView, RecurringPlan and PartnerLevel |
| Migrations | 37, plus 7 settings migrations |
| Services | 16, covering lead assignment, scoring, merging, conversion and the activity log |
| Console commands | 3: `crm:assign-leads` and `crm:fetch-mail` (both scheduled), and visits-to-leads |
| Tests | 54 feature tests |
| Declared dependencies | `contacts`, `sales` |

It is still being changed daily upstream: there are commits on 3, 4 and 5 November, and a migration was renamed on 28 October.

## 2. What conflicts with this fork

Each item below is a reason a straight copy would be wrong here.

1. **Company scoping is partial.**
   - Only 8 of 37 migrations carry `company_id`: leads, activity logs, lead
     merges, mail servers, lead conversions, mass conversions, quotation
     partners and reveal rules.
   - Stages, teams, tags, lost reasons, industries and recurring plans are
     global.
   - That breaks this fork's rules: tags are unique **per company** (§4k), and
     unrelated client businesses must never see each other's pipeline.
   - Every tenant-data table needs `company_id`, `BelongsToCompany`, and
     `InheritsParentCompany` on child rows.
2. **Module switch (SAAS-05).** Upstream has no per-company entitlement. CRM
   needs the full five layers: cluster/page `canAccess`, resource
   `canViewAny`, policies, service `assertForCompany`, and the API route
   middleware. It also needs to be added to `company_plugin` on install. It
   must never be gated in `Plugin::register()`.
3. **Website visitor tracking does not exist here.**
   - `CRMServiceProvider` listens to `Webkul\Website\Events\VisitorTracked`.
     That event and the CMS were removed from our Website plugin on 2026-10-22.
   - Visits-to-leads, lead "reveal" (rules and views) and the visitor relation
     tables therefore have nothing to run on. They should be left out, not
     ported dormant.
4. **Inbound email.**
   - `crm:fetch-mail` and the mail servers need the PHP `imap` extension.
     Laravel Cloud may not have it.
   - Mail-server passwords are stored in the database, so they must be
     encrypted casts. The repo is public: never seed real credentials.
5. **Outgoing mail and SMS settings.**
   - Upstream adds its own outgoing-mail settings.
   - This fork's protected decision is that mail leaves as
     `config('mail.from.address')`, never as a person or a per-tenant
     address (Sentry 158147606, DMARC). CRM must use `EmailService` and must
     not add a second sender path.
   - SMS has no provider here.
6. **It changes Sales.**
   - `add_opportunity_id_to_sales_orders_table` alters a core table. It must
     be nullable, indexed, and safe to roll back.
   - `CRMServiceProvider` also overrides Sales list pages (`ListQuotations`,
     and the customer and partner relations). Those touch screens real
     customers use today.
7. **Missing shared pieces.** `Webkul\Support\Support\VersionedCss` and
   CRM's `VersionedJs` do not exist here. Upstream's newer dashboard widgets
   are also absent (see the dashboards note in the change log).
8. **Settings per company.** CRM settings via `CompanyAwareSettingsRepository`
   fall back to the default company. That is fine for preferences, but wrong
   for any on/off switch (AGENTS.md).
9. **Layout.** New resources must use the split `Schemas/` + `Tables/` layout.
   Check upstream's layout before copying, rather than converting it later.
10. **Speed.** Upstream widgets count per state in a loop, the N+1 that
    Sentry 156271115 flagged on Projects. Check every CRM widget and report
    for the same thing.

## 3. Decisions needed before work starts

| ID | Question | Recommendation |
|---|---|---|
| D-CRM-1 | Which scope for the first release? | Leads, pipeline (opportunities, stages, lost reasons), activities, teams, and quotation from an opportunity. Leave out reveal and visits, inbound email, SMS, lead scoring and recurring revenue until later. |
| D-CRM-2 | Are stages, teams, tags and lost reasons per company? | Yes, matching tags and categories (§4k). |
| D-CRM-3 | Who is a lead's company: the creating user's, or a chosen one? | The creating company, required on the form, as for products. |
| D-CRM-4 | Inbound email at all? If so, which host? | Not in the first release. Later: decide host support for `imap`, or use a webhook-based provider instead. |
| D-CRM-5 | Is CRM entitled per company like other modules, and is it in the default set for new companies? | Entitled, and **not** in the default set; granted deliberately. |
| D-CRM-6 | Track upstream after the port? | Freeze at one upstream commit for the port. Review upstream CRM changes as findings in `upstream-fix-adoption-plan.md`, never merge wholesale. |

## 4. Work packages

Each package ends with its suite green on its own database
(`aureuserp_testing_crm<N>`), plus a handoff in §6. The four-place rule
applies to every guard.

| Package | Content | Depends on |
|---|---|---|
| CRM-0 | Freeze the upstream commit. Produce a file-by-file inventory: copy as is, adapt, or leave out, following D-CRM-1. List every class referenced outside the plugin and whether it exists here. | Decisions |
| CRM-1 | Plugin skeleton: provider, `CRMPlugin`, `bootstrap/providers.php`, a `phpunit.xml` `CrmFeature` suite, autoloader, Shield config, an install/uninstall guard, and an `isPluginInstalled` check only in `register()`. | CRM-0 |
| CRM-2 | Schema for the chosen scope, rewritten to carry `company_id` per D-CRM-2. Models use `BelongsToCompany` / `InheritsParentCompany`; factories use `HasCompanyDefault`. Includes the `opportunity_id` column on sales orders (nullable, with rollback). Adds `CompanyScopingInvariantsTest`. | CRM-1 |
| CRM-3 | Configuration screens: stages, teams and members, tags, lost reasons, and settings. All per company, with `unique(company_id, name)` where names are unique. | CRM-2 |
| CRM-4 | Leads and pipeline: lead and opportunity resources, kanban, won and lost, merge, convert to opportunity, and activities via chatter. Services repeat the business guards, because super admin skips policies. | CRM-3 |
| CRM-5 | Sales link: quotation from an opportunity, and opportunity shown on the order. Done without replacing Sales' own list pages, through a schema registry or contributions. Sales suite stays green. | CRM-4 |
| CRM-6 | Module switch: the five layers, `company_plugin` grant on first install, and a test per layer, following the SAAS-05 tests. | CRM-4 |
| CRM-7 | Reporting and dashboard: forecast, pipeline analysis and a CRM tab, each with page permissions asserted against `FilamentShield::getPages()`. No per-row query loops. | CRM-4 |
| CRM-8 | Release: run all suites; fr translations; guide entries; `CHANGELOG.md`; read-only pre-flight on production; and handover actions (install, then grant to chosen companies). | All |

Later, each only after its own decision: inbound email, lead assignment
scheduling, lead scoring, recurring revenue, SMS, and the reveal and visits
features.

## 5. Risks

- **Moving target.** Upstream may change models after the freeze. Accept
  that, and review later changes as findings.
- **Size.** About 860 files, so expect several sessions per package. Copying
  translations is cheap; tenancy and entitlement are the real cost.
- **Sales regressions.** CRM-5 touches a module in production use. Run the
  full SaleFeature suite at the end of that package.
- **Hosting.** `imap` and scheduled commands need Laravel Cloud's scheduler
  and extensions. Confirm both before any scheduled package.

## 6. Status board and handoffs

| Package | Status | Owner / database |
|---|---|---|
| CRM-0 … CRM-8 | todo (blocked on §3 decisions) | — |
