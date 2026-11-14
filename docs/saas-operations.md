# Running client companies (operator guide)

For DEKA staff who administer client companies: Super Admins and
Multi-Company Admins. Written 2026-11-13 (CR-10 item 4, the SaaS plan's
SAAS-14 documentation). The rules behind each step are in `AGENTS.md`
("Protected decisions") and `docs/completed/saas-administration-billing-audit-plan.md`.

## New client

1. **Settings → Companies → New.** Company name, account type (test or
   commercial), and the plan if commercial. Country is optional; when given it
   fills the currency.
2. **Administrator:** enter their email. By default they get an invitation
   and choose their own password. Enter a password only when email cannot
   reach them; it is never emailed, so hand it over yourself.
3. Check the **Setup status** column on the Companies list:
   - *Invited, not yet active* - use **Resend invitation** on the row if they
     lost the mail (it is valid 7 more days).
   - *Invitation expired* - invite again from the company form.
   - *Active* - an enabled administrator exists and can sign in.
4. If invitation mail never arrives, open the company → **Users and logins**:
   copy the pending link, or **Set password** for that user.

## Users, roles and seats

- New companies have **no seat limit**. A limit is set on purpose
  (company form, Super Admin only). Pending invitations count as seats.
- Ready-made roles (Finance Manager, Accountant, HR Manager, Payroll Officer,
  Sales Manager, Inventory Manager, Logistics Manager, Standard Employee) are
  created on deploy. Edit them under **Roles**; edits are kept. A Company Admin
  can give a user only a role whose permissions they hold themselves.
- **Modules:** the Plugins page → *Companies* action grants or removes a
  module per company. Removing a module that another granted module needs is
  refused.

## Support access (impersonation)

Super Admins can act as a client user; a Multi-Company Admin needs the
`tenant.impersonate` grant from a Super Admin. Every start asks for your own
password and a reason, ends after 60 minutes, and every write is logged under
your id. Never a Super Admin, Multi-Company Admin or yourself as the target.

## Billing

- Invoices are generated daily at 02:15 (`platform-billing:invoice-subscriptions`,
  scheduler). Payments are entered by hand by a Super Admin; the reference is
  the duplicate guard.
- Overdue: an unpaid invoice is overdue from day 8 (net 7); the company turns
  read-only 10 days after that. A Company
  Admin may add 10 days once per invoice; a Super Admin may add one more
  extension of any length.
- Test accounts turn read-only when `test_ends_at` passes; add days or switch to
  commercial from the company.

## Checks to run

| Command | When | What it tells you |
| --- | --- | --- |
| `php artisan platform-billing:health-check` | Runs itself daily at 06:00 and reports a failure to Sentry; run it by hand when something looks off | Exits with an error if balances do not reconcile or an invoice is more than a day late (scheduler or invoice job stopped). Read-only |
| `php artisan platform-billing:readiness-report` | Before turning billing on for a company | Seats, modules, subscription and read-only state per company. Read-only |
| `php artisan partners:company-report` | Before changing contact sharing | Which contacts more than one company uses |
| `php artisan erp:sync-permissions` | Every deploy (already in the deploy commands) | Creates missing permissions and role templates |

The health check is **not scheduled yet**. To be alerted, add it to the
scheduler with a failure notification, or run it from Laravel Cloud's cron.
