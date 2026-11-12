# Reports — Improvement Plan

Status: **plan written 2026-11-12; decisions made the same day; not started.** Built from the brief below
(added by the user 2026-11-12), cut to the gaps after the user's instruction
the same day: "if what we have is enough don't plan an overkill, just an
improvement and an easy workflow."

Read `AGENTS.md` first: page permissions (`page_<plugin>_<page>`, assert the
generated set), `ChecksPagePermission`, and the module switch on every page.

---

## 1. What exists (keep)

Reports already live in each module's own Reporting cluster, behind the
module switch and a Shield page permission, most with filters and export:

| Module | Reports (verified 2026-11-12) |
| --- | --- |
| Accounting | Balance Sheet, Profit and Loss, Trial Balance, General Ledger, Partner Ledger, Aged Receivable, Aged Payable |
| Logistics | Shipment Register, Shipment Profitability, Delivery Performance, Trip History, Driver and Vehicle Trip History |
| Attendance | Absence, Lateness, Overtime |
| Inventory | Moves analysis, Stock quantities |
| Time Off | By employee |
| Employees | Skills |
| Overview | Combined dashboard with sections per module (`OverviewRegistry`) |

So the brief's "plugin-aware, entitlement-aware, permission-aware" rule is
already how every report is guarded. What is missing is **one place to find
them**, and reports for Sales and Purchases.

## 2. Packages

**R1 — Reports page (the easy workflow).**
One "Reports" entry in the main navigation. It lists every report page the
user can open, grouped by module, each with a one-line description, plus a
search box. Plugins add their pages to a small registry (same shape as
`OverviewRegistry`: key, module, label, description, page class); the page
shows an entry only when that page's own `canAccess()` passes, so module
switch, Shield permission and company come for free and there is no second
permission rule. A disabled module's reports disappear by themselves. Opening
a report goes to its existing page; nothing is moved. Phone layout: one
column, search at the top.

**R2 — Sales and Purchases reports.**
Sales: by customer, by product, by salesperson, by month; open quotations.
Purchases: by vendor, by product, open purchase orders, received vs ordered.
Built like the Logistics report pages (filtered table, export through the
filtered query, never `getTable()->getQuery()`). Each states what it counts
(ordered, delivered or invoiced amounts) in its description.

**R3 — Favourites** (small): a star on each entry, stored per user and
company, shown first. Only if R1 is liked in use.

Later, when asked: stock valuation and ageing come with Inventory I1/I4
(Inventory plan); payroll reports come with Payroll (Payroll plan); platform (Super
Admin) revenue reports with the SaaS remainder (CR-10, plan #1).

Not planned (overkill now): custom report builder, scheduled delivery, pivot
tables, report definitions in the database, cached results.

## 3. Order and decisions

R1 → R2 → R3.

| ID | Question | Answer (user, 2026-11-12) |
| --- | --- | --- |
| REP-D1 | One "Reports" page linking to the existing reports, not a rebuilt report engine? | **Accepted:** link the existing reports |
| REP-D2 | Keep each module's own Reporting cluster too? | **Accepted:** keep both |
| REP-D3 | Which new reports first? | **Accepted:** Sales and Purchases (R2); more as clients ask |

---

## Original brief (user, 2026-11-12)

Kept as written. The plan above supersedes its phases and deliverables.

# DEKA ERP — MASTER AGENT PROMPT
# Comprehensive ERP Reporting, Analytics, Report Discovery, and Plugin-Aware Reporting Engine

## 1. ROLE AND OBJECTIVE

You are acting as a senior team consisting of:

- Enterprise ERP Solutions Architect
- Senior Laravel and Filament Developer
- ERP Business Analyst
- Chartered Accounting and Financial Reporting Specialist
- Business Intelligence and Data Analytics Architect
- Database and PostgreSQL Performance Engineer
- HR and Payroll Systems Analyst
- Inventory and Supply Chain Analyst
- SaaS Product Manager
- UI/UX Designer
- Software QA and Security Engineer

Your task is to conduct comprehensive research, investigate DEKA ERP's existing architecture, evaluate current reporting functionality, and design and implement a **centralized, enterprise-grade Reports & Analytics Center**.

DEKA ERP is a multi-company SaaS ERP built with Laravel, Filament, and PostgreSQL.

The application uses a modular architecture in which ERP functionality is controlled by plugins or modules activated for individual companies.

The new reporting system must automatically expose the reports associated with a company's enabled modules, subject to subscription entitlements, user roles, permissions, and available data.

The reporting experience must be as straightforward and comprehensive as that of established ERP systems, without unnecessary complexity.

**Critical principle: Every enabled ERP module should provide its relevant business reports automatically, while disabled or unauthorized modules must not expose reports or data.**

Do not start coding immediately.

Begin by investigating the existing DEKA ERP implementation and conducting external research.

---

# PHASE 1 — COMPLETE CODEBASE AND REPORTING AUDIT

Inspect the full repository.

Identify:

1. Every existing ERP plugin or module.
2. Every currently activated and inactive plugin.
3. Every existing report, dashboard, widget, chart, and export.
4. Database tables and models associated with each plugin.
5. Existing reporting services and calculation methods.
6. Existing PDF, Excel, CSV, and printing capabilities.
7. Existing Filament resources and pages.
8. Existing permissions and authorization policies.
9. Company or tenant relationships.
10. Subscription and module entitlement controls.
11. Currency conversion and accounting configuration.
12. Existing scheduled jobs, queues, and notifications.
13. Existing performance bottlenecks in reporting.
14. Existing reusable libraries and application services.

Do not assume that a report is missing because it is not visible in the navigation. Search the codebase for existing implementations.

Do not assume a plugin exists merely because the proposed report catalog mentions it.

Discover the actual installed and supported plugin list.

For each plugin, create a report inventory with:

- Plugin name
- Plugin identifier
- Installation status
- Activation status per company
- Existing reports
- Missing essential reports
- Database data sources
- Available metrics
- Existing permissions
- Implementation readiness
- Dependency on other plugins

Produce a comprehensive **Current Reports Audit** before making changes.

---

# PHASE 2 — RESEARCH LEADING ERP REPORTING SYSTEMS

Conduct detailed research into reporting functionality in:

1. Odoo
2. ERPNext and Frappe
3. Microsoft Dynamics 365 Business Central
4. SAP Business One
5. Oracle NetSuite
6. Zoho Books / Zoho ERP products
7. Other relevant enterprise ERP or BI systems

Study official documentation, feature specifications, and implementation examples.

Focus on:

- Report Explorer and centralized catalogs
- Financial statements
- Standard operational reports
- Report discovery and search
- Report filtering
- Drill-down reporting
- Tabular reports
- Charts and dashboards
- Pivot analysis
- Grouping and aggregations
- Report scheduling
- Saved report configurations
- Report templates
- Custom report builders
- Export functionality
- Report permissions
- Multi-company reporting
- Multi-currency reporting
- Performance and caching
- Report validation and reconciliation

Compare the platforms.

Explain which designs are appropriate for DEKA and why.

Useful references:

https://www.odoo.com/documentation/19.0/applications/general/reporting.html

https://www.odoo.com/documentation/19.0/applications/finance/accounting/reporting.html

https://docs.frappe.io/erpnext/making-custom-reports

https://docs.frappe.io/erpnext/role-permission-for-page-and-report

https://learn.microsoft.com/en-us/dynamics365/business-central/reports-use-reports

Consult the current Laravel, Filament, and PostgreSQL documentation corresponding to installed versions.

The goal is to create a simple user experience supported by robust business logic.

---

# PHASE 3 — DESIGN THE CENTRAL REPORTS CENTER

Create a new centralized navigation section called:

**Reports & Analytics**

This section must automatically list all reports available to the current company and user.

## 3.1 Navigation and Organization

Organize reports by module or business category.

For example:

Reports & Analytics
- Overview
- Favorites
- Recent Reports
- Accounting & Finance
- Sales & CRM
- Purchasing
- Inventory & Warehousing
- HR & Payroll
- Logistics & Fleet
- Projects
- Assets & Maintenance
- Other Activated Plugins
- Scheduled Reports
- Custom Reports

These are proposed categories, not proof that all modules already exist.

Discover the real module names and adapt the structure accordingly.

A category should appear only when it contains reports available to the current user.

If a plugin is disabled, its reports should disappear automatically unless a deliberate, secure historical-report policy applies.

## 3.2 Report Discovery

Include:

- Search by report name
- Search by description
- Filter by module
- Filter by report type
- Favorite reports
- Recently opened reports
- Most-used reports where appropriate
- Recommended reports
- Report descriptions
- Report availability indicators
- Consistent report naming

Every report should have a concise description explaining what it shows and when it is useful.

## 3.3 User Experience

A typical user should be able to:

1. Open Reports & Analytics.
2. Select an activated plugin category.
3. Choose a report.
4. Set the desired filters.
5. Generate and view the results.
6. Drill into relevant records.
7. Print, download, or save the report.
8. Return to the catalog easily.

A user must not need technical expertise or knowledge of database tables to generate reports.

---

# PHASE 4 — COMPREHENSIVE REPORT CATALOG

Perform detailed business research for every module found in DEKA.

Use the following catalog as an initial research baseline.

For each candidate report, verify:

1. Whether the corresponding plugin exists.
2. Whether the required records exist in the database.
3. Whether the required calculation is supported.
4. Whether the report already exists.
5. Whether additional fields or business logic are necessary.
6. Whether the report should be mandatory, recommended, or optional.
7. Whether it depends on another plugin.

Do not fabricate missing data or generate misleading reports.

## 4.1 Accounting and Financial Reports

Research and evaluate:

- Balance Sheet
- Profit and Loss / Income Statement
- Trial Balance
- General Ledger
- Chart of Accounts
- Journal Entries
- Cash Flow Statement
- Cash Book
- Bank Book
- Bank Reconciliation
- Account Transactions
- Accounts Receivable
- Accounts Payable
- Aged Receivables
- Aged Payables
- Customer Statements
- Supplier Statements
- Outstanding Invoices
- Outstanding Bills
- Customer Payment History
- Supplier Payment History
- Income by Account
- Expense by Category
- Revenue by Period
- Budget vs Actual
- Departmental Expenses
- Tax Summary
- Tax Liability
- VAT / Sales Tax Report
- Withholding Tax Report
- Financial Audit Trail
- Multi-Currency Transaction Report
- Foreign Currency Gains and Losses
- Financial Ratio Analysis

Ensure reports follow the actual accounting model.

Financial statements must reconcile with the general ledger.

Do not substitute invoice totals for recognized revenue or cash movements for accrual-based profit unless the report explicitly uses those definitions.

Verify accounting periods, journal status, debit-credit balance, posting dates, reversals, and currency rules.

## 4.2 Sales and CRM Reports

Research:

- Sales Summary
- Daily Sales
- Weekly Sales
- Monthly Sales
- Annual Sales
- Sales by Customer
- Sales by Product
- Sales by Product Category
- Sales by Salesperson
- Sales by Branch
- Sales by Country
- Sales Growth
- Revenue Trends
- Sales Target vs Actual
- Sales Profitability
- Customer Purchase History
- Top Customers
- Top-Selling Products
- Sales Returns
- Discount Analysis
- Outstanding Quotations
- Quotation Conversion
- Sales Order Status
- Pending Sales Orders
- Invoice Collection Performance
- Customer Retention
- Sales Pipeline
- Opportunities by Stage
- Lead Conversion

Check whether CRM and Sales are separate plugins.

Only show relevant reports if their dependencies are enabled and their underlying features are available.

## 4.3 Purchasing and Supplier Reports

Research:

- Purchase Summary
- Purchases by Supplier
- Purchases by Product
- Purchases by Period
- Supplier Performance
- Purchase Order Status
- Outstanding Purchase Orders
- Goods Received vs Ordered
- Supplier Payment Summary
- Supplier Balances
- Supplier Price Comparison
- Purchase Price Variance
- Purchase Returns
- Procurement Lead Time
- Purchase Commitments
- Procurement Spend Analysis
- Supplier Delivery Performance

Verify whether purchasing records distinguish orders, receipts, bills, and payments.

Avoid double-counting purchases.

## 4.4 Inventory and Warehouse Reports

Research:

- Current Stock Balance
- Stock Valuation
- Inventory Movement
- Stock Ledger
- Stock In
- Stock Out
- Stock Transfers
- Warehouse Stock
- Stock by Location
- Reorder Level
- Low Stock
- Out-of-Stock Products
- Inventory Aging
- Slow-Moving Stock
- Fast-Moving Stock
- Dead Stock
- Stock Adjustment
- Stock Variance
- Inventory Turnover
- Product Profitability
- Batch and Lot Tracking
- Serial Number Tracking
- Expiry Report
- Damaged Goods
- Inventory Shrinkage
- Stock Reservation
- Available-to-Promise Inventory

Research valuation methods such as FIFO, weighted average, and other methods actually supported by the application.

Valuation figures must reconcile with underlying stock movements and accounting records where integration exists.

## 4.5 Human Resources Reports

Research:

- Employee Directory
- Employee Headcount
- Employee Distribution by Department
- Employee Distribution by Branch
- Employee Status
- New Hires
- Employee Turnover
- Employee Attendance
- Absence Summary
- Late Arrivals
- Overtime Summary
- Leave Balances
- Leave Utilization
- Leave Requests
- Employee Contracts
- Contract Expiration
- Employee Performance
- Training Records
- Employee Benefits
- Employee Disciplinary Records, subject to strict permissions

Reports containing sensitive employee information must use appropriate access restrictions.

## 4.6 Payroll Reports

Research:

- Payroll Register
- Payroll Summary
- Employee Payslip
- Payroll by Department
- Payroll by Branch
- Payroll by Pay Period
- Salary Structure
- Gross Pay
- Net Pay
- Allowances
- Deductions
- Bonuses
- Overtime Payments
- Employer Contributions
- Employee Contributions
- Payroll Cost Analysis
- Payroll Variance
- Payroll Payment Schedule
- Unpaid Salaries
- Payroll Journal
- Payroll Audit Trail
- Statutory Deduction Summary
- Income Tax Withholding
- Social Security Contributions
- Year-to-Date Payroll Summary
- Employee Annual Earnings

The application must support country-specific payroll requirements through appropriate localization logic.

Investigate Ghana, Zambia, and DR Congo requirements separately.

Do not hardcode the same tax or statutory deduction assumptions for every country.

Never present statutory reports as legally compliant without validating current local regulations, supported calculations, and required formats.

## 4.7 Logistics and Transport Reports

Research:

- Shipment Summary
- Shipment Status
- Shipment by Customer
- Shipment by Destination
- Trip Summary
- Trip Cost
- Trip Profitability
- Driver Performance
- Vehicle Utilization
- Vehicle Assignment
- Vehicle Expenses
- Transport Charges
- Delivery Performance
- Delivery Delays
- Proof of Delivery
- Undelivered Shipments
- Route Performance
- Trip Events
- Delivery Exceptions
- Fuel Consumption, if data exists
- Vehicle Maintenance, if supported
- Fleet Operating Costs
- Cost per Shipment
- Cost per Trip
- Revenue per Vehicle
- Driver Trip History
- Logistics Invoice Summary

Inspect the actual DEKA logistics tables and services before implementing these.

Do not assume GPS tracking, fuel sensors, live telematics, or maintenance features exist unless the application genuinely supports them.

## 4.8 Fixed Assets and Maintenance Reports

Where relevant plugins exist, research:

- Fixed Asset Register
- Asset Acquisition
- Asset Disposal
- Depreciation Schedule
- Accumulated Depreciation
- Net Book Value
- Asset by Location
- Asset by Department
- Asset Maintenance
- Maintenance Cost
- Maintenance Due
- Warranty Expiration
- Asset Lifecycle
- Asset Utilization

Verify accounting integration and depreciation calculation policies.

## 4.9 Projects and Operations Reports

Where relevant:

- Project Status
- Project Profitability
- Project Budget vs Actual
- Project Expenses
- Project Revenue
- Task Completion
- Outstanding Tasks
- Employee Time Tracking
- Resource Utilization
- Project Milestones
- Project Delays
- Project Cost Breakdown
- Work-in-Progress

## 4.10 Other Installed Plugins

The agent must inspect every additional DEKA plugin, including custom modules.

For each plugin, research and design appropriate reports based on its actual features, database model, and business purpose.

Do not limit the reporting architecture to the modules listed above.

---

# PHASE 5 — PLUGIN-AWARE REPORT REGISTRATION

This is one of the most important architectural requirements.

Every ERP plugin should be able to declare its supported reports.

Implement or improve a reusable report-registration mechanism.

## 5.1 Report Definition

A report definition should contain appropriate metadata, including:

- Unique report identifier
- Report name
- Description
- Owning plugin
- Report category
- Report type
- Required permissions
- Required module entitlements
- Required dependencies
- Data source or report service
- Available filters
- Available columns
- Supported groupings
- Available calculations
- Supported exports
- Supported visualizations
- Availability status
- Version or compatibility requirements

Evaluate whether report definitions should be stored in code, a database registry, or a hybrid model.

## 5.2 Automatic Availability

When a company activates a plugin:

1. The associated eligible reports should become available.
2. The Reports Center should update automatically.
3. The reports should use the current company's data.
4. The appropriate company roles should receive access according to policy.
5. No manual report installation should be necessary.

When a plugin is deactivated:

1. Reports requiring that plugin should no longer be accessible.
2. Direct URLs and APIs must enforce the same restriction.
3. Existing business data must remain intact.
4. Existing saved report definitions must be preserved safely where appropriate.
5. Existing schedules must be disabled or handled according to an explicit policy.
6. Reactivation should restore eligible reports without duplicating definitions.

Do not physically delete historical records merely because a plugin is disabled.

## 5.3 Cross-Module Reports

Some reports need multiple modules.

For example:

Sales Profitability may require Sales, Inventory, and Accounting information.

Evaluate dependencies explicitly.

Provide fallback reports where meaningful, but do not calculate incomplete metrics or silently omit essential cost information.

---

# PHASE 6 — REPORT GENERATION AND VIEWING

Create a consistent reporting experience.

## 6.1 Standard Filters

Support relevant filters such as:

- Date range
- Today
- Yesterday
- This week
- This month
- Previous month
- This quarter
- This year
- Previous year
- Custom period
- Customer
- Supplier
- Employee
- Department
- Branch
- Warehouse
- Product
- Product category
- Transaction status
- Document type
- Currency
- Salesperson
- Project
- Vehicle
- Driver

Not every filter should be shown for every report.

Filters must be generated from the report definition and its data model.

## 6.2 Data Presentation

Depending on report type, support:

- Data tables
- Grouped tables
- Summary cards
- Line charts
- Bar charts
- Pie charts when appropriate
- Pivot tables
- Period comparisons
- Trend analysis
- Drill-down views
- Subtotals
- Grand totals

Use appropriate visualizations rather than forcing every report into the same format.

## 6.3 Report Accuracy

Ensure:

- Correct aggregation
- Correct date boundaries
- Correct timezone handling
- Accurate monetary calculations
- Decimal precision
- Clear treatment of tax
- Proper handling of refunds and reversals
- Correct transaction statuses
- No duplicate records from joins
- Correct historical exchange rates
- Consistent reporting periods
- Reconciliation with authoritative business records

Use explicit metric definitions.

A report should explain whether it measures ordered, delivered, invoiced, recognized, or paid amounts.

---

# PHASE 7 — EXPORT, PRINTING AND DOCUMENT OUTPUT

Research and implement suitable export capabilities.

Recommended formats:

- PDF
- Excel (.xlsx)
- CSV
- Print-friendly HTML

Evaluate whether other formats are genuinely necessary.

Reports should have professional layouts.

Include where appropriate:

- Company name
- Company logo
- Report title
- Reporting period
- Applied filters
- Generation date
- Currency
- Totals
- Page numbers
- Prepared-by information

Financial statements should use appropriate accounting layouts.

Payroll and employee reports require stricter confidentiality controls.

All exported data must respect tenant isolation and record-level authorization.

Never rely only on frontend access restrictions.

For large exports, use queued jobs and secure download links with appropriate expiration.

---

# PHASE 8 — SAVED REPORTS, CUSTOMIZATION AND REPORT BUILDER

Research how DEKA users can save and reuse report configurations.

## 8.1 Saved Reports

Users should be able to save permitted configurations such as:

- Selected report
- Date filters
- Columns
- Grouping
- Sorting
- Display preferences

Saved configurations must remain scoped to the correct company and user or authorized group.

## 8.2 Custom Report Builder

Investigate whether DEKA should provide a simple report builder.

Prefer a controlled, metadata-driven report builder over unrestricted SQL access.

Candidate capabilities:

1. Choose an approved data source.
2. Choose available columns.
3. Add filters.
4. Group records.
5. Select supported calculations.
6. Preview results.
7. Save the report.
8. Export or schedule it.
9. Share with authorized company roles.

Do not expose arbitrary SQL execution to normal tenant users.

Prevent cross-tenant data access, unbounded queries, and expensive joins.

Treat the report builder as an advanced capability, not a prerequisite for essential standard reports.

---

# PHASE 9 — SCHEDULED REPORTING

Investigate automated report delivery.

Authorized users should potentially be able to:

- Schedule daily reports
- Schedule weekly reports
- Schedule monthly reports
- Schedule reports on a specified date
- Select delivery recipients
- Select output format
- Manage report schedules
- Pause or cancel schedules

Examples:

- Monthly Profit and Loss to a Finance Manager
- Weekly Sales Summary to a Sales Manager
- Monthly Payroll Summary to an authorized Payroll Manager
- Weekly Inventory Reorder Report to a Warehouse Manager
- Daily Shipment Exceptions to a Logistics Manager

Implement scheduling with Laravel's existing scheduling and queue infrastructure where appropriate.

Do not send confidential reports to recipients without explicit authorization.

Revalidate user permissions, tenant status, and plugin entitlement when the scheduled report executes.

Ensure that a deactivated user or disabled plugin cannot continue receiving sensitive reports through an old schedule.

Include secure delivery, audit records, retry handling, and failure notifications.

---

# PHASE 10 — PERMISSIONS AND TENANT ISOLATION

Implement strict reporting authorization.

Report access must depend on:

1. Current tenant
2. Enabled plugin
3. Subscription entitlement
4. User role
5. Report-specific permission
6. Relevant record-level restrictions

Possible permissions include:

- View report
- Generate report
- Export report
- Print report
- Schedule report
- Create custom report
- Manage report definitions
- Share saved report
- View sensitive financial reports
- View payroll reports

A Company Admin should have broad reporting access within the company's permitted modules, except where additional security restrictions are required.

Standard employees should only see their authorized reports.

**A user must never access another company's reports, generated files, scheduled outputs, cached results, or underlying records.**

Test direct URLs, API endpoints, exports, background jobs, cached results, and downloaded files.

During Super Admin impersonation, enforce the approved impersonation restrictions.

Keep SaaS platform reporting separate from tenant operational reporting.

---

# PHASE 11 — SAAS SUPER ADMIN REPORTS

The DEKA SaaS Super Admin also requires platform-level business reports.

These reports are separate from the operational reports used by client companies.

Research and develop reports such as:

## Commercial Reports

- Total Companies
- Active Companies
- Trial Companies
- Suspended Companies
- New Companies by Month
- Company Onboarding Performance
- Subscription Summary
- Subscription Revenue
- Monthly Recurring Revenue
- Annual Recurring Revenue
- Revenue by Country
- Revenue by Subscription Plan
- Revenue by Currency
- Revenue by Partner
- Outstanding Subscription Invoices
- Customer Account Balances
- Deposits and Credits
- Subscription Renewals
- Upcoming Expirations
- Overdue Accounts
- Plan Upgrades
- Plan Downgrades
- Subscription Cancellations
- Customer Churn
- Average Revenue per Account

Use valid definitions for MRR, ARR, churn, and recurring revenue.

Separate actual collections from invoiced revenue and contracted recurring revenue.

## Platform Usage Reports

- Active Users by Company
- Companies by Enabled Plugin
- Plugin Adoption
- Module Usage
- Company Activity
- User License Utilization
- Onboarding Success Rate
- Failed Company Provisioning
- Invitation Activation
- Tenant Storage Usage, if measured
- Scheduled Report Activity
- Export Activity
- Support Impersonation Audit
- Security and Administrative Events

Only use measurable data already collected or implement an appropriate privacy-conscious event model.

Do not fabricate usage statistics.

## Partner Reports

Where the partner module exists:

- Companies by Partner
- Partner Revenue
- Partner Commissions
- Partner Renewals
- Partner Customer Activity
- Partner Subscription Performance

Restrict partner reporting to authorized commercial information.

---

# PHASE 12 — MULTI-CURRENCY REPORTING

DEKA supports companies operating in different countries and currencies.

Investigate the current currency architecture before implementing reports.

Every monetary report must clearly identify:

- Original transaction currency
- Base accounting currency
- Reporting currency, if different
- Exchange rate used
- Exchange-rate date or policy
- Conversion and rounding rules

For multi-currency reports, determine which values may be aggregated and how conversions should be performed.

Never sum amounts in unrelated currencies as though they were directly comparable.

Preserve historical conversion rates where required.

For SaaS Super Admin revenue reporting, define a consistent reporting currency and an appropriate conversion policy.

Test financial accuracy across Ghana, Zambia, and DR Congo configurations.

---

# PHASE 13 — REPORT PERFORMANCE AND SCALABILITY

Investigate performance requirements.

Reports may eventually process substantial financial, sales, employee, inventory, and logistics records.

Review:

- PostgreSQL indexes
- Tenant-scoped queries
- Query execution plans
- Aggregation performance
- Pagination
- Chunked exports
- Queued report generation
- Memory usage
- Timeouts
- Cache strategy
- Materialized summaries where justified
- Reporting snapshots
- Large-data handling
- Concurrent report generation

Ensure caching is tenant-aware and permission-safe.

Do not introduce stale financial reports without clearly defined refresh and consistency policies.

Avoid expensive queries that slow normal ERP operations.

Evaluate whether separate reporting workloads are necessary only after measuring actual bottlenecks.

---

# PHASE 14 — REPORT QUALITY ASSURANCE

Create automated tests for:

1. Report discovery
2. Plugin activation and deactivation
3. Role-based visibility
4. Tenant isolation
5. Correct financial calculations
6. Correct filters
7. Accurate totals
8. Export functionality
9. Currency conversion
10. Scheduled report generation
11. Report permissions
12. Saved report functionality
13. Missing-data handling
14. Large-data performance
15. Existing company compatibility

Create test fixtures with known figures.

For financial reports, reconcile results against the source ledger.

For inventory, reconcile stock balances and valuation.

For payroll, reconcile gross pay, deductions, and net pay.

For logistics, reconcile calculated trip and shipment metrics with source transactions.

Test disabled plugins and users with restricted roles.

Use real browser-based end-to-end tests where an authorized test environment is available.

Do not classify a report as working merely because it displays a page or downloads a file.

A report is working only when its calculations, filters, permissions, and outputs have been verified.

---

# PHASE 15 — PRESERVE EXISTING COMPANIES AND DATA

This requirement is mandatory.

All reporting improvements must work for both newly onboarded and existing companies.

Ensure:

- Existing companies receive newly introduced reporting configuration where applicable.
- Existing records remain intact.
- Existing reporting features continue to work.
- Existing roles and permissions are migrated safely.
- New report metadata is registered automatically.
- Existing subscriptions and plugin entitlements remain accurate.
- Reports do not require companies to repeat onboarding.
- Database migrations are backward-compatible.
- No historical business records are deleted or overwritten.
- Existing financial data is not recalculated destructively.
- Production operations remain unaffected.

If existing records lack information required for a new report, provide a clear missing-data or unsupported-state explanation.

Do not invent historical values or silently backfill unverifiable figures.

Use safe migration procedures, staging verification, backups, and regression testing.

---

# PHASE 16 — IMPLEMENTATION ARCHITECTURE

Investigate the best architecture for the Reports Center.

Consider reusable components such as:

- ReportRegistry
- ReportDefinition
- ReportDiscoveryService
- ReportPermissionService
- ReportFilterResolver
- ReportExecutionService
- ReportExportService
- ReportScheduleService
- ReportResultCache
- ModuleReportProvider

These are proposed architectural concepts, not instructions to create unnecessary classes.

Choose the simplest maintainable architecture that integrates cleanly with the existing Laravel and Filament application.

A plugin should be able to register reports through an approved interface.

The central Reports Center should discover those definitions and apply entitlements, tenant scope, permissions, and execution requirements.

Avoid hardcoding every report into one enormous controller or Filament page.

Separate presentation, business calculations, and data retrieval.

Reuse existing report services where they already provide correct functionality.

---

# PHASE 17 — REQUIRED DELIVERABLES

Produce the following:

## A. Existing Reporting Audit

Document every installed plugin, existing report, data source, and limitation.

## B. ERP Benchmark Report

Compare reporting practices across leading ERP platforms.

Include source links and evidence.

## C. Complete DEKA Report Catalog

For every discovered plugin, list:

- Report name
- Description
- Business purpose
- Data source
- Calculation logic
- Available filters
- User permissions
- Dependencies
- Suggested visualization
- Export formats
- Implementation priority

## D. Reporting Architecture Proposal

Describe the central report registry, module integration, authorization, execution, and export design.

## E. UI/UX Design

Prepare the Reports Center, report viewer, report filter interface, favorites, scheduled reports, and administrative configuration screens.

## F. Prioritized Development Backlog

Classify requirements:

**P0 — Essential**
- Central Reports Center
- Automatic plugin-aware discovery
- Essential reports for existing plugins
- Tenant isolation
- Permission enforcement
- Reliable report calculations
- Filters and essential exports

**P1 — Advanced**
- Dashboards
- Charts
- Pivot tables
- Drill-down
- Saved reports
- Scheduling
- Multi-currency report enhancements
- Platform SaaS reporting

**P2 — Extended BI**
- Custom report builder
- Advanced analytics
- Trend analysis
- Cross-module business intelligence
- Additional visualizations

Adjust priorities to verified business needs and dependencies.

## G. QA and Validation Report

Include actual test results, evidence, defects, and unresolved risks.

## H. Implementation and Rollout Plan

Include:

- Development sequence
- Database migration strategy
- Backward compatibility
- Testing strategy
- Performance benchmarks
- Rollback strategy
- Production deployment considerations

---

# PHASE 18 — EXECUTION INSTRUCTIONS

Follow this sequence:

1. Inspect the current repository.
2. Discover every existing module and report.
3. Conduct external ERP reporting research.
4. Compare industry practices.
5. Produce the report catalog.
6. Identify gaps and missing data requirements.
7. Design the architecture.
8. Design the user interface.
9. Prepare a prioritized implementation plan.
10. Present findings before making broad architectural changes.
11. Implement approved changes in a development environment.
12. Test reports with known data.
13. Validate permissions, currencies, and tenant isolation.
14. Confirm compatibility with existing companies.
15. Document all completed and pending functionality.

Use relevant installed development skills and testing tools where they improve the quality of the investigation and implementation.

Do not replace existing working features unnecessarily.

Do not introduce dependencies without verifying compatibility.

Do not make destructive database changes.

Do not claim reports are accurate without reconciliation tests.

---

# FINAL SUCCESS CRITERIA

The reporting implementation is successful when:

1. A company opens one Reports & Analytics section.
2. The system automatically identifies its activated plugins.
3. Only relevant and authorized reports appear.
4. Every visible report can be generated from valid company data.
5. Reports have appropriate filters and calculations.
6. Reports support suitable viewing, printing, and export functionality.
7. Company administrators can access and manage their permitted reports.
8. Employees see only reports authorized for their roles.
9. SaaS Super Admins have a separate commercial and platform reporting center.
10. Existing companies continue operating without data loss or interruption.
11. Report calculations are verified against underlying business records.
12. Tenant isolation is maintained across report views, exports, schedules, and caches.

**The overriding objective: DEKA ERP must provide the right business reports automatically, according to the plugins a company has activated, through one simple and professional Reports Center.**

Begin by auditing the existing DEKA ERP repository and identifying every installed plugin, its existing reports, and its missing reporting requirements. Do not begin by creating generic report screens without understanding the actual data and business logic.