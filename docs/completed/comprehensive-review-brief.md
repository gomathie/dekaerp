# MASTER AGENT PROMPT
# DEKA ERP — Comprehensive SaaS Architecture, Commercial Management, and Company Onboarding Audit

## ROLE AND MISSION

You are acting as a multidisciplinary senior technical and product team consisting of:

- Principal SaaS Solutions Architect
- Senior Laravel and Filament Engineer
- Multi-Tenant Architecture Specialist
- SaaS Product Manager
- UX/UI Researcher
- SaaS Billing and Commercial Operations Specialist
- Security and Identity Management Engineer
- Software Quality Assurance and End-to-End Testing Engineer
- Investor Due Diligence Technical Reviewer

Your assignment is to conduct a **comprehensive investigation, comparison, audit, and redesign of DEKA ERP's SaaS administration and company onboarding experience**.

DEKA ERP is a Laravel-based multi-company SaaS ERP application using Filament for administration and PostgreSQL for data storage. you have a production login. you can also login to see how some things work.

The system is being prepared for commercial growth, with Ghana, Zambia, and the Democratic Republic of Congo among its initial markets.

Investors have raised concerns about the complexity of onboarding new companies.

Some investors have experience with SaaS telematics platforms where administrators and system integrators can create client accounts, assign contracts, configure services, and provide login access with minimal effort.

A key reference is Pilot Telematics:

https://docs.pilot-gps.africa/

Additional reference:

https://docs.pilot-gps.com/

We want to understand how these platforms achieve a convenient onboarding and commercial administration experience, then adapt the best ideas to DEKA ERP.

**The ultimate objective is to make company onboarding, tenant administration, subscriptions, permissions, and commercial operations simple, reliable, secure, and scalable.**

Do not assume that the current architecture is correct.

Do not assume that the current onboarding flow is optimal.

Do not immediately begin rewriting the application.

First investigate, benchmark, test, document the deficiencies, and propose improvements based on evidence.

---

# PHASE 1 — DISCOVER AND UNDERSTAND THE EXISTING SYSTEM

Before suggesting changes, perform a complete technical investigation of the existing DEKA ERP codebase.

## 1.1 Repository and Architecture Investigation

Inspect the application to determine:

1. Laravel, PHP, Filament, PostgreSQL, and dependency versions.
2. Existing multi-tenancy implementation.
3. Whether the application uses a shared database, separate tenant databases, or a hybrid architecture.
4. How companies or tenants are represented.
5. How tenant records are created.
6. How company administrators are created.
7. How users are linked to companies.
8. How roles and permissions are assigned.
9. How company modules are enabled or disabled.
10. How subscription plans are created and assigned.
11. How pricing, billing, balances, payments, renewals, and deposits are managed.
12. How multiple currencies are currently handled.
13. How account suspension and reactivation work.
14. How partners or resellers are represented.
15. How tenant-specific configurations are initialized.
16. How the system handles login, tenant identification, and tenant switching.
17. Whether provisioning is synchronous, queued, or partially manual.
18. Whether company creation triggers automatic setup of default records.
19. Whether authorization rules prevent cross-tenant access.
20. Whether the current SaaS administration layer can support commercial growth.

Inspect migrations, database models, relationships, policies, middleware, Filament resources, form components, services, jobs, queues, notifications, tests, and configuration files.

Identify existing components that can be reused.

Do not recommend a new dependency or tenancy package until the existing implementation and its limitations have been understood.

## 1.2 Map the Current Company Onboarding Flow

Trace the complete current workflow from the perspective of a Super Admin.

Start at login and continue until the newly created company's first administrator can successfully use DEKA ERP.

Document every step.

For every step, capture:

- Screen or page involved
- Fields required
- Buttons and actions
- Manual configuration
- Database records created
- Services or methods executed
- Permissions assigned
- Any errors or confusing behavior
- Whether the action could be automated
- Whether the step is necessary before first login

Measure the number of screens, required fields, clicks, manual interventions, and elapsed time.

If the workflow cannot be completed, identify precisely where it fails.

Produce an AS-IS onboarding flowchart.

Also identify whether company creation is currently mixed with subscription configuration, database provisioning, user creation, and feature activation in a way that unnecessarily complicates the experience.

---

# PHASE 2 — RESEARCH AND BENCHMARK SUCCESSFUL SAAS SYSTEMS

Conduct external research into leading SaaS systems with straightforward company and account onboarding.

## 2.1 Pilot Telematics — Primary Reference

Study:

https://docs.pilot-gps.africa/

https://docs.pilot-gps.com/

Investigate the documented workflows for:

1. Creating a client account
2. Creating a client contract
3. Creating a partner or reseller
4. Assigning a client to a partner
5. Creating the first customer user
6. Assigning account access and privileges
7. Associating services with contracts
8. Enabling platform modules
9. Assigning pricing or tariffs
10. Managing balances and subscription status
11. Managing existing customer accounts
12. Modifying customer contracts
13. Delegating onboarding to a partner
14. Providing support or administrative access to customer accounts

Distinguish between documented behavior, directly observed behavior, and assumptions.

If credentials for a permitted test environment are available, validate relevant workflows through actual interaction.

Do not attempt to bypass authentication or access real customer information without authorization.

Focus especially on what makes customer onboarding simple for a nontechnical administrator.

## 2.2 Benchmark Additional SaaS Platforms

Research relevant workflows from:

- Odoo
- Zoho
- ERPNext / Frappe
- Microsoft Dynamics 365
- Shopify
- Stripe Billing
- Other well-designed B2B multi-tenant SaaS platforms

Include selected SaaS platforms with strong partner or reseller onboarding models.

Research only workflows that are relevant to DEKA ERP.

Do not compare systems merely because they are popular.

For each platform, investigate:

- Company or organization creation
- First administrator provisioning
- User invitation
- Roles and permissions
- Subscription and plan assignment
- Module activation
- Multi-company access
- Partner or reseller administration
- Multi-currency configuration
- Account impersonation or support access, where available
- Subscription and billing administration
- Customer self-service
- Onboarding friction

Use official documentation and current credible technical references wherever possible.

Record links, evidence, and version information.

## 2.3 Compare and Contrast

Produce a structured comparison table containing:

| Evaluation area | Current DEKA | Pilot | Other SaaS examples | Recommended DEKA |
|---|---|---|---|---|
| Company creation | Investigate | Research | Research | Propose |
| First administrator | Investigate | Research | Research | Propose |
| User invitations | Investigate | Research | Research | Propose |
| Module setup | Investigate | Research | Research | Propose |
| Subscription assignment | Investigate | Research | Research | Propose |
| Partner onboarding | Investigate | Research | Research | Propose |
| Billing operations | Investigate | Research | Research | Propose |
| Currency setup | Investigate | Research | Research | Propose |
| Support impersonation | Investigate | Research | Research | Propose |
| Overall usability | Measure | Evaluate | Evaluate | Improve |

Identify the strongest approaches and explain why they are suitable for DEKA.

The objective is not to copy Pilot Telematics or another ERP.

Instead, create a simplified workflow appropriate to DEKA's architecture, business model, and intended customers.

---

# PHASE 3 — DESIGN THE IDEAL COMPANY ONBOARDING EXPERIENCE

## 3.1 Target Experience

The preferred onboarding sequence is:

**Create Company → Assign Subscription → Automatically Create Company Administrator → Activate Account → Start Using ERP**

The process must feel simple and predictable.

A Super Admin should not have to perform multiple unrelated administrative tasks merely to activate one customer.

## 3.2 Simplified Company Creation

Investigate whether the company can be created using only:

- Company name
- Administrator name
- Administrator email
- Country
- Subscription plan

Other information should be inherited from the plan, default settings, or country configuration wherever practical.

The system should automatically handle:

1. Unique company identity
2. Tenant provisioning
3. Default company configuration
4. Company Administrator creation
5. User-company membership
6. Required role and permission assignment
7. Subscription creation
8. Module entitlement activation
9. Default user limit
10. Default operational currency
11. Default settings and business configuration
12. Administrator activation invitation
13. Initial account status

An optional advanced section may expose settings such as:

- Partner or reseller
- Custom billing cycle
- Custom subscription price
- Trial period
- Additional currencies
- Custom module entitlements
- Additional user licenses
- Company branding
- Tax configuration
- Optional onboarding notes

Avoid overwhelming administrators with fields that are not required immediately.

## 3.3 First User Must Automatically Become Company Admin

This is a mandatory requirement.

Whenever a company is created, the system must create or associate the first user and grant that user the highest company-level administrative role.

This person must be the **Company Admin**.

The Company Admin must be able to:

- Log into the newly created company
- Access every ERP module enabled for the company, subject to legitimate plan, compliance, and security restrictions
- Manage all company-level configuration
- Create additional users
- Invite employees
- Assign company-scoped roles
- Edit user permissions
- Deactivate or reactivate company users
- Manage departments
- Manage company-level settings
- View the company's subscription details
- Configure supported operational currencies
- Manage permitted company integrations

The Company Admin must not automatically receive platform-level Super Admin privileges.

No manual role assignment should be required after company creation.

If the administrator email already belongs to an existing user, reuse the identity safely and add the appropriate company membership rather than creating a duplicate login.

Determine the correct behavior for users who belong to multiple companies.

## 3.4 Company Admin User Management

Review the current user management interface.

Investigate whether adding employees is unnecessarily complicated.

The target workflow should be:

**Add User → Enter Name and Email → Assign Role → Send Invitation**

Evaluate default roles such as:

- Company Admin
- Finance Manager
- Accountant
- HR Manager
- Payroll Officer
- Sales Manager
- Inventory Manager
- Logistics Manager
- Standard Employee
- Custom Role

These are candidate roles, not a directive to replace existing permissions.

Determine how they map to actual modules and capabilities.

Permissions must be scoped to the current company.

A company's administrators must not be able to grant platform-level privileges or access to another company's records.

Test invitations, activation, role changes, deactivation, and license limits.

---

# PHASE 4 — SUPER ADMIN IMPERSONATION AND SUPPORT ACCESS

A mandatory requirement is that the DEKA Platform Super Admin should have a convenient way to enter a company's workspace and inspect the experience of an authorized user.

This feature must be implemented securely.

## 4.1 Functional Requirements

The Super Admin should be able to:

1. Open the Companies management page.
2. Select a company.
3. View the company's users.
4. Choose a user.
5. Select an action such as "View as User."
6. Enter the user's company-specific workspace.
7. Observe the interface, visible modules, navigation, and effective permissions as that user would experience them.
8. Exit the impersonation session and return to the original Super Admin session.

Provide a clearly visible impersonation indicator showing the current company, selected user, and an **End Session** control.

## 4.2 Security Requirements

This is a privileged support feature.

Investigate the safest design, including:

- Explicit Super Admin authorization
- Reauthentication or step-up authentication for sensitive sessions
- A required support reason or ticket reference
- Time-limited impersonation
- Secure one-time tokens where appropriate
- Audit trails recording the real administrator and impersonated user
- Session isolation
- Reliable restoration of the original session
- Prevention of impersonation privilege escalation
- Protection against cross-company access
- Restrictions on sensitive actions

Recommend whether impersonation should be read-only by default.

At minimum, prohibit credential changes, MFA changes, authorization changes, financial approvals, bank-detail changes, and other high-risk actions during impersonation unless a separately authorized support workflow explicitly permits them.

For sensitive actions, preserve the real actor's identity in audit records.

Evaluate available Laravel and Filament-compatible solutions before implementing a custom mechanism.

Relevant documentation:

https://v4.tenancyforlaravel.com/features/user-impersonation/

https://filamentphp.com/docs/5.x/users/tenancy

Use the documentation matching the installed application versions.

Test session switching, expiration, logout, browser behavior, access restrictions, and audit records.

Never impersonate real customers during testing without explicit authorization.

---

# PHASE 5 — COMPREHENSIVE SAAS COMMERCIAL ADMINISTRATION AUDIT

The DEKA Super Admin panel must be a functional commercial control center, not merely a company and user database.

Review how the SaaS business is administered.

## 5.1 Subscription Plans

Investigate how subscription plans are created and maintained.

The system should support configurable:

- Plan name
- Plan description
- Included ERP modules
- Included users
- User overage rules
- Monthly price
- Quarterly price
- Biannual price
- Annual price
- Country-specific availability
- Plan-specific currency
- Trial period
- Feature entitlements
- Activation and suspension policies

Determine whether subscriptions can be assigned to a company during company creation.

Identify unnecessary duplicate data entry.

## 5.2 Commercial Billing Operations

Test the complete lifecycle of a SaaS customer:

1. Create company.
2. Assign plan.
3. Activate company.
4. Issue the first invoice or record the relevant billing obligation.
5. Record payment.
6. Add a deposit or account credit.
7. Check account balance.
8. Renew subscription.
9. Upgrade plan.
10. Downgrade plan.
11. Enable an additional paid module.
12. Increase user limit.
13. Apply a discount or custom commercial agreement.
14. Handle overdue invoices.
15. Suspend a subscription when required.
16. Reactivate a suspended account.
17. Cancel or terminate a subscription.

Use the existing billing model where suitable.

Review whether the software correctly distinguishes:

- Customer account balance
- Invoice balance
- Credit/deposit balance
- Subscription status
- Payment status
- Company operational status

These are not necessarily the same thing.

Assess billing proration, effective dates, duplicate payments, invoice corrections, credit notes, tax handling, historical pricing, and failed payment recovery.

Check whether the administrator can perform normal commercial operations without using the database or developer tools.

## 5.3 Partner and Reseller Administration

Investigate whether DEKA should support a partner layer similar to a telematics integrator structure.

The suggested hierarchy is:

DEKA Platform → Partner/Reseller → Client Companies → Company Users

A partner should potentially be able to:

- Onboard authorized client companies
- Manage assigned customers
- Assign eligible subscription plans
- View relevant commercial information
- Manage customer activation
- Track renewals
- View delegated subscription status
- Request support
- Apply partner-specific pricing, where authorized

A partner must not automatically gain access to confidential company accounting, payroll, HR, or financial records.

Investigate the appropriate permission boundaries.

Determine how to support direct customers and reseller-managed customers through the same onboarding architecture.

---

# PHASE 6 — MULTI-CURRENCY ADMINISTRATION

This is an important requirement for DEKA's international rollout.

The DEKA Super Admin must be able to maintain a centralized currency catalog and control which currencies individual companies can use.

## 6.1 Platform Currency Management

The SaaS administrator should be able to:

1. Add or enable a currency.
2. Specify its ISO 4217 currency code.
3. Define its name and symbol.
4. Configure decimal precision and formatting.
5. Activate or deactivate a currency for future use.
6. Select available currencies for each company.
7. Configure default currency availability by country.
8. Manage exchange-rate sources and update policies.
9. View currency configuration and changes.

Start by investigating how currency information is currently stored and used.

Do not create duplicate currency structures if a suitable model exists.

## 6.2 Company-Level Currency Management

Each company should have:

- One base accounting currency
- One or more permitted transaction currencies
- Country-specific defaults where appropriate
- Exchange rates and conversion rules
- Controls over which users can manage currencies

For example:

**Company A — Ghana**
- Base accounting currency: GHS
- Additional permitted currencies: USD, EUR

**Company B — Zambia**
- Base accounting currency: ZMW
- Additional permitted currencies: USD, ZAR

**Company C — DR Congo**
- Base accounting currency: CDF
- Additional permitted currencies: USD, EUR

These are illustrative defaults, not mandatory restrictions.

The Super Admin should determine which currencies are available to the company. Authorized Company Admins should then be able to choose from those permitted currencies.

## 6.3 Accounting and Billing Separation

Investigate the distinction between:

1. Platform subscription billing currency
2. Company base accounting currency
3. Company transaction currencies
4. Reporting currency

Do not assume they must be identical.

For example, DEKA may charge a company in USD while that company conducts its accounting in GHS.

Verify that invoice calculations, payments, exchange rates, accounting postings, rounding, refunds, credit notes, and financial reports remain consistent.

Exchange rates used in posted transactions must preserve historical correctness.

A company should not be able to silently change its base accounting currency after posting transactions if doing so would invalidate accounting history.

Research appropriate accounting controls and recommend a safe policy.

---

# PHASE 7 — LIVE SYSTEM INSPECTION AND END-TO-END TESTING

Where the necessary tools, permissions, and access are available, inspect the running DEKA application.

Use browser automation, approved computer-use tools, Laravel tests, Playwright, or other appropriate tools.

Discover available development and testing skills before selecting an approach.

## 7.1 Access and Environment

First determine:

- Whether a development or staging environment exists
- Whether the agent has authorized browser access
- Whether valid test credentials are available
- Whether a disposable test company can be created
- Whether database fixtures and test email services exist
- Whether current authentication and MFA requirements can be satisfied legitimately

Do not bypass security controls.

Do not expose credentials or secrets in reports.

Prefer staging or a dedicated test environment.

For production, restrict testing to authorized, non-destructive actions unless explicit approval is provided.

Do not send live customer invitations, create charges, change existing accounts, or perform destructive operations without authorization.

## 7.2 End-to-End Onboarding Test

Create a disposable company using authorized test data.

Attempt to complete:

1. Super Admin login
2. Company creation
3. Subscription assignment
4. Tenant provisioning
5. Automatic Company Admin creation
6. Administrator invitation
7. Administrator activation
8. Company Admin login
9. Module access verification
10. Creation of a second user
11. Invitation and login of that second user
12. Role and permission verification
13. Subscription and currency configuration
14. Secure Super Admin support impersonation, where available
15. Return to the original Super Admin session

Record each result as PASS, FAIL, BLOCKED, or NOT IMPLEMENTED.

Never claim a test passed unless it was actually executed and its expected result verified.

## 7.3 Negative and Edge-Case Tests

Include:

- Duplicate company names
- Duplicate administrator emails
- Invalid administrator emails
- Failed invitation delivery
- Expired invitations
- Repeated provisioning requests
- Partial database failures
- Failed queue jobs
- Missing default roles
- Disabled modules
- Company user limit reached
- Expired subscription
- Suspended company
- Unauthorized user creation
- Unauthorized tenant switching
- Cross-tenant data requests
- Unauthorized impersonation
- Impersonation session expiration
- Currency removal after transactions exist
- Exchange-rate rounding discrepancies
- Plan changes during a billing cycle
- Failed, repeated, or reversed payments

Ensure no test can corrupt or expose another company's data.

## 7.4 Usability Evaluation

Perform the onboarding process from three perspectives:

**Persona A — DEKA Super Admin**

Responsible for onboarding companies, configuring subscriptions, managing billing, and resolving customer issues.

**Persona B — Company Administrator**

Responsible for activating the account, configuring company settings, and adding employees.

**Persona C — Partner or Reseller Administrator**

Responsible for creating and managing customers within assigned commercial permissions.

For every persona, measure:

- Screens visited
- Required inputs
- Number of clicks
- Time to complete the task
- Confusing decisions
- Errors encountered
- Repeated work
- Reliance on technical support
- Actions that should be automated

Identify specific usability defects and recommend fixes.

Where possible, capture screenshots and annotated evidence from authorized test environments.

---

# PHASE 8 — AUTOMATED PROVISIONING AND SYSTEM RELIABILITY

Assess how DEKA creates and activates a new company technically.

Design a reliable provisioning service that can orchestrate the required operations.

Candidate service:

`CreateCompanyService`

The service should coordinate:

- Company registration
- Tenant identity creation
- Tenant data initialization
- Subscription assignment
- Module entitlement assignment
- First administrator membership
- Default roles and permissions
- Currency defaults
- Invitations
- Status updates
- Provisioning audit events

Investigate whether a queue-based provisioning process is appropriate.

Ensure:

1. Idempotent processing
2. Duplicate prevention
3. Appropriate transaction boundaries
4. Safe retries
5. Failure recovery
6. Provisioning status visibility
7. Clear error messages
8. No partially activated customer accounts
9. Safe invitation delivery after successful provisioning
10. Complete auditability

Avoid leaving the platform with a company marked Active when its administrator cannot log in.

Recommend a clear lifecycle such as:

Draft → Provisioning → Invited → Active → Suspended → Closed

Adjust this lifecycle to fit the existing application and distinguish provisioning state from subscription state.

---

# PHASE 9 — INVESTOR-READINESS AND COMMERCIAL SCALABILITY REVIEW

Evaluate DEKA ERP as if an investor or enterprise customer were conducting technical and operational due diligence.

Investigate:

- Ease of acquiring and onboarding a new customer
- Commercial onboarding scalability
- SaaS administration usability
- Subscription revenue visibility
- Tenant isolation
- Role and permission management
- Platform security
- International currency support
- Billing reliability
- Partner/reseller readiness
- Customer lifecycle management
- Auditability
- Support operations
- Recoverability from failed onboarding
- Ability to onboard customers without developer involvement

Propose meaningful KPIs such as:

- Median time to provision a company
- Time from invitation to first successful login
- Administrator actions required
- First-attempt onboarding success rate
- Percentage of customers activated without support assistance
- Failed provisioning rate
- Time to add a new employee
- Support tickets per 100 newly onboarded companies
- Subscription billing exception rate

Treat any target numbers as proposals until baseline measurements exist.

For each serious deficiency, identify its impact on customer experience, technical risk, operational cost, or investor confidence.

Create an investor-readiness scorecard supported by evidence.

Do not invent revenue metrics, customer counts, uptime results, or test outcomes.

---

# PHASE 10 — DESIGN THE IMPROVED USER EXPERIENCE

Produce improved interface proposals for:

1. SaaS Super Admin Dashboard
2. Companies Management Page
3. Add New Company Form
4. Company Details Page
5. Subscription and Plan Assignment
6. Company Administrator Invitation
7. Company First-Login Experience
8. Company User Management
9. Partner / Reseller Management
10. Super Admin Support Impersonation
11. Platform Currency Management
12. Company Currency Configuration
13. SaaS Billing and Payments
14. Subscription Upgrade / Downgrade
15. Company Suspension and Reactivation

Favor clean Filament-compatible interfaces.

Use existing application design patterns and components when practical.

Keep the common workflow short.

Move rarely used settings into an optional Advanced section or a later configuration screen.

Avoid multi-step wizards unless the number of decisions genuinely justifies them.

Produce both AS-IS and TO-BE flow diagrams.

Explain which fields are required, optional, auto-generated, or inherited.

---

# PHASE 11 — IDENTIFY THE BEST SKILLS, TOOLS, AND TESTING METHODS

Before implementation, inspect the available development environment and installed agent skills.

Identify whether existing tools can assist with:

- Laravel architecture review
- Filament resource inspection
- Database schema analysis
- Browser automation
- End-to-end testing
- UI testing
- Security auditing
- Documentation generation
- Screenshot capture
- Dependency analysis
- Regression testing

Review relevant existing project instructions and skill files.

Do not install tools or packages merely because they are available.

Select tools based on compatibility, maintenance status, existing project conventions, and actual benefit.

Useful research references include:

- Laravel documentation
- Filament documentation
- PostgreSQL documentation
- Playwright documentation
- Laravel tenancy documentation
- OWASP authentication and access control guidance
- SaaS billing vendor documentation
- Pilot Telematics documentation

Inspect the repository's own tooling first.

If automated browser access is unavailable, report that limitation and provide executable test cases rather than claiming live testing occurred.

---

# PHASE 12 — REQUIRED DELIVERABLES

Produce the following outputs.

## Deliverable 1 — Executive Audit Report

Include:

- Current state
- Major findings
- Onboarding friction points
- Commercial administration weaknesses
- Security risks
- Business impact
- Recommended changes
- Investor-readiness assessment

## Deliverable 2 — SaaS Platform Benchmark Report

Compare DEKA against Pilot Telematics and selected SaaS examples.

Include cited sources and specific lessons.

## Deliverable 3 — Current vs Proposed Workflow

Show AS-IS and TO-BE onboarding diagrams.

Document screen counts, manual actions, dependencies, and measurable improvements.

## Deliverable 4 — Technical Architecture Report

Include:

- Current tenancy architecture
- Existing data relationships
- Proposed improvements
- Provisioning service design
- Administrator and company membership model
- Authentication and authorization model
- Impersonation architecture
- Multi-currency architecture
- Billing and entitlement relationships

## Deliverable 5 — UI/UX Specification

Describe the proposed screens, field behavior, validation rules, and state transitions.

## Deliverable 6 — QA Test Report

For every test provide:

- Test ID
- Objective
- Preconditions
- Steps executed
- Expected behavior
- Actual behavior
- Result
- Evidence
- Defect severity
- Recommended fix

Clearly separate executed tests from proposed test cases.

## Deliverable 7 — Prioritized Implementation Backlog

Classify improvements into:

**P0 — Critical**
- Simplified company creation
- Reliable tenant provisioning
- Automatic first Company Admin
- Administrator activation
- Functional company login
- Company user management
- Tenant isolation and critical authorization fixes

**P1 — High Priority**
- Super Admin impersonation
- Centralized company management
- Subscription lifecycle improvements
- Currency management
- Billing usability
- Audit logs and provisioning recovery

**P2 — Commercial Growth**
- Partner/reseller onboarding
- Advanced partner commercial controls
- Automated subscription workflows
- Additional dashboards and analytics
- Improved customer self-service

Reprioritize based on verified risk and business impact.

For every task, include affected files or components where known, implementation effort, dependencies, risks, and acceptance criteria.

## Deliverable 8 — Final Investor-Ready Demonstration Plan

Design a short demonstration that proves DEKA can:

1. Create a new company with minimal input.
2. Automatically establish the first Company Admin.
3. Allow the Company Admin to log in.
4. Allow the Company Admin to invite another user.
5. Assign the company's subscription.
6. Enable an additional ERP module.
7. Configure additional operational currencies.
8. Review billing and payment status.
9. Demonstrate authorized support impersonation.
10. Return securely to the SaaS Super Admin dashboard.

The demonstration must use actual implemented features, not mock behavior presented as production functionality.

---

# WORKING RULES

1. **Audit before changing code.**
2. Do not rebuild working functionality unnecessarily.
3. Preserve existing customer data and subscriptions.
4. Do not disrupt production.
5. Do not run destructive migrations or reset existing databases.
6. Do not expose confidential customer data.
7. Do not treat documentation as proof that the running application works.
8. Test actual behavior wherever authorized access is available.
9. Label assumptions, unknowns, and untested functionality.
10. Favor reuse, simplicity, maintainability, and security.
11. Do not introduce unnecessary dependencies.
12. Preserve compatibility with the application's actual Laravel and Filament versions.
13. Maintain strict tenant data isolation.
14. Keep platform Super Admin and company-level permissions separate.
15. Make commercial operations accessible to authorized nontechnical administrators.
16. Ensure every proposed improvement can be tested.
17. Identify potential regressions before implementation.
18. Prepare a migration, rollback, and deployment strategy.
19. Explain architectural trade-offs rather than assuming one solution is always best.
20. Prioritize demonstrable customer value and commercial readiness over unnecessary technical complexity.

---

# EXPECTED EXECUTION ORDER

**Stage A — Research and Discovery**

Inspect the repository, current application, documentation, and external SaaS benchmarks.

**Stage B — Current Workflow Testing**

Test the existing onboarding and commercial administration workflows in an authorized environment.

**Stage C — Gap Analysis**

Identify friction, architectural limitations, missing functionality, and security issues.

**Stage D — Solution Design**

Produce the improved workflows, technical architecture, UX specifications, and acceptance criteria.

**Stage E — Implementation Plan**

Prepare a prioritized, technically grounded development backlog.

**Stage F — Controlled Implementation**

Only after the assessment is documented and the proposed changes are reviewed and authorized, begin implementing approved improvements in a development branch.

Use staging for verification before production deployment.

**Stage G — Regression Testing**

Run automated and manual tests for company onboarding, subscriptions, users, permissions, currencies, and impersonation.

**Stage H — Investor Demonstration**

Prepare a functioning end-to-end demonstration and evidence-based summary of the improvements.

---

# FINAL SUCCESS DEFINITION

The project is successful when a DEKA SaaS administrator can create a new company through a straightforward interface and the first company administrator can activate the account, log in, and manage company users without additional manual configuration.

The Super Admin must be able to manage subscriptions, module entitlements, company currencies, billing, and account status efficiently.

Authorized support personnel must be able to inspect company-user experiences through secure, auditable impersonation without compromising tenant isolation.

The commercial administration experience should be straightforward enough for sales, account-management, and support staff to operate independently.

**The overriding principle is simple: DEKA ERP may be complex internally, but the customer onboarding and SaaS management experience must be exceptionally easy to operate.**

Begin with a comprehensive investigation of the current codebase and existing onboarding workflow. Report what currently works, what is broken or unnecessarily complicated, and what changes will produce the greatest improvement.

Do not jump directly into coding.

Deliver an evidence-based assessment and an actionable improvement plan before implementing changes.

### Existing Companies — Data Preservation and Backward Compatibility

All improvements, new fields, and updated configurations must be applied to **existing companies as well as newly onboarded companies**.

The agent must ensure that:

- Existing companies automatically receive all necessary new fields, default values, roles, permissions, and configurations through safe database migrations.
- Existing company data, users, subscriptions, transactions, and settings remain unchanged and fully operational.
- No existing company loses access to its modules, users, or business records.
- New functionality remains backward-compatible with the current system.
- Database migrations are tested against existing company records before deployment.
- Existing companies are not required to repeat onboarding or manually configure newly introduced mandatory fields.

**Critical requirement:** All enhancements must be non-destructive, preserve tenant data isolation, and maintain uninterrupted operations for existing companies. Implement migration validation, regression testing, and a rollback strategy before production deployment.

acceptance critiria
[ ] Super Admin creates a company from one simple form
[ ] First Company Admin is created and invited automatically
[ ] Company Admin activates the account and logs in
[ ] Company Admin creates and manages a second user
[ ] Super Admin manages the subscription and module access
[ ] Super Admin assigns base and additional currencies
[ ] Super Admin reviews billing, payment and account status
[ ] Super Admin securely impersonates an authorized test user
[ ] Impersonation ends and original Super Admin session is restored
[ ] Cross-company access and privilege escalation tests pass