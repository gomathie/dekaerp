<p align="center">
  <a href="https://dekaerp.com">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/dekaerp/temp-media/main/aureus-logo-dark.png">
      <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/dekaerp/temp-media/main/aureus-logo-light.png">
      <img src="https://raw.githubusercontent.com/dekaerp/temp-media/main/aureus-logo-light.png" alt="DEKA ERP logo">
    </picture>
  </a>  
</p>

<h1 align="center">DEKA ERP</h1>

<p align="center">
  <strong>Open-Source Enterprise Resource Planning for Modern Businesses</strong>
</p>

<p align="center">
  Built with Laravel 13 • Powered by FilamentPHP 5 • PostgreSQL • Multi-Tenant
</p>

---

## 📋 Table of Contents

1. [Introduction](#-introduction)
2. [Key Features](#-key-features)
3. [Why Choose DEKA ERP?](#-why-choose-deka-erp)
4. [Architecture](#-architecture)
5. [Requirements](#-requirements)
6. [Quick Start](#-quick-start)
7. [Production Deployment](#-production-deployment)
8. [Multi-Tenancy](#-multi-tenancy)
9. [Plugin System](#-plugin-system)
10. [Plugin Installation & Management](#-plugin-installation--management)
11. [Customization](#-customization)
12. [Contributing](#-contributing)
13. [License](#-license)
14. [Security](#-security)
15. [Support & Community](#-support--community)

---

## 🚀 Introduction

DEKA ERP is a comprehensive, open-source Enterprise Resource Planning (ERP) solution designed for Small and Medium Enterprises (SMEs) and growing organizations. Built on **[Laravel 13](https://laravel.com)** and **[FilamentPHP 5](https://filamentphp.com)**, DEKA ERP offers an extensible, multi-tenant platform for managing every aspect of your business operations.

DEKA ERP is an independently maintained fork — it has its own Git repository and release cycle, and does not depend on upstream updates from the original Aureus ERP project.

Whether you're managing accounting, inventory, HR, CRM, or projects, DEKA ERP provides a modular approach that grows with your business.

---

## ✨ Key Features

### 🏗️ Modern Architecture

Built with Laravel 13, FilamentPHP 5, and Livewire 4 for maximum performance and developer experience. PostgreSQL-first with a database dialect abstraction layer for cross-platform compatibility.

### 🏢 Multi-Tenant

Built-in multi-company support with company-scoped data isolation, user-to-company access control, and a company switcher. Run multiple businesses from a single installation.

### 🧩 Modular Plugin System

28 plugins covering accounting, inventory, manufacturing, HR, CRM, and more. Install only the features you need.

### 🎨 Beautiful UI/UX

Responsive design with TailwindCSS 4, optimized for desktop and mobile. Real-time updates via Livewire 4.

### 🔐 Advanced Security

Role-based access control with Filament Shield, two-factor authentication with recovery codes, record-level ownership scopes, and Sanctum API authentication.

### 📊 Business Intelligence

Built-in analytics, reporting tools, and Excel export (Maatwebsite) for financial reports including Balance Sheet, Profit & Loss, Trial Balance, General Ledger, and Aged Receivable/Payable.

### 🌐 Multi-Language Support

Localized for English, Spanish, Arabic (with RTL support), Brazilian Portuguese, and French.

### ⚡ High Performance

Database dialect abstraction, optimized Eloquent queries, eager loading, and configurable caching strategies.

### 🔧 Developer-Friendly

Clean code, comprehensive REST API with Sanctum, Pest test suites, and a CLI-driven plugin system.

---

## 🎯 Why Choose DEKA ERP?

| Feature              | Benefit                                                   |
| -------------------- | --------------------------------------------------------- |
| **Open Source**      | Free to use, modify, and extend. No vendor lock-in        |
| **Modern Stack**     | Laravel 13, FilamentPHP 5, Livewire 4, TailwindCSS 4     |
| **Multi-Tenant**     | Built-in multi-company isolation for SaaS or multi-org    |
| **PostgreSQL-First** | Production-ready on PostgreSQL / Neon serverless Postgres  |
| **Scalable**         | From 5 tenants to enterprise-scale operations             |
| **Customizable**     | Extend with your own plugins using the modular architecture |
| **Production-Ready** | Docker setup for Coolify/Hetzner with Supervisor, Nginx    |

---

## 🏛️ Architecture

DEKA ERP uses a **plugin-first** architecture. The Laravel core (`app/`) is a thin shell — all business logic lives in 28 self-contained plugins under `plugins/webkul/`.

```
dekaerp/
├── app/                      # Thin Laravel shell (providers, models, console)
├── config/                   # Laravel + package configuration
├── database/                 # Core migrations & seeders
├── docker/production/        # Production Docker setup (Nginx, PHP-FPM, Supervisor)
├── plugins/webkul/           # 28 business plugins
│   ├── support/              # Core: database dialects, multi-tenancy, helpers
│   ├── security/             # Core: users, roles, permissions, ownership scopes
│   ├── accounts/             # Chart of accounts, journals, moves, tax
│   ├── accounting/           # Financial reporting (Balance Sheet, P&L, etc.)
│   ├── inventories/          # Warehouse management, stock moves, operations
│   ├── manufacturing/        # BOM, manufacturing orders, work centers
│   ├── sales/                # Sales pipeline, quotations, orders
│   ├── purchases/            # Procurement, RFQs, purchase orders
│   └── ...                   # 20 more plugins
├── resources/                # Views, JS, CSS
└── routes/                   # Web, API, console routes
```

### Tech Stack

| Component | Version | Purpose |
|-----------|---------|---------|
| PHP | 8.3+ | Runtime |
| Laravel | 13.x | Framework |
| FilamentPHP | 5.x | Admin panel |
| Livewire | 4.x | Real-time UI |
| TailwindCSS | 4.x | Styling |
| PostgreSQL | 14+ | Primary database |
| Pest | 4.x | Testing |
| Sanctum | 4.x | API authentication |
| Vite | — | Asset bundling |

### Database Abstraction

The app includes a `DatabaseDialect` abstraction layer (`MySqlDialect` / `PostgresDialect`) that handles cross-database SQL differences (JSON aggregation, date formatting, sequence syncing, case-insensitive equality). The `db_dialect()` helper resolves the active dialect at runtime.

---

## 📦 Requirements

### Server Requirements

- **PHP**: 8.3 or higher
- **Database**: PostgreSQL 14+ (recommended: [Neon](https://neon.tech) serverless Postgres)
- **Web Server**: Nginx 1.18+ (or Apache 2.4+)

### Development Tools

- **Composer**: 2.0+
- **Node.js**: 18.x or higher
- **NPM**: Latest stable version
- **Docker** (optional): For local development via Laravel Sail

### Supported Databases

| Database | Status | Notes |
|----------|--------|-------|
| PostgreSQL 14+ | ✅ **Primary** | Recommended for production |
| Neon (serverless PG) | ✅ **Supported** | Use pooled endpoint for app, direct for migrations |
| MySQL 8.0+ | ⚠️ Supported | Via `MySqlDialect`; not the primary target |
| SQLite | ⚠️ Dev only | For quick local testing |

---

## ⚡ Quick Start

### Option A: Laravel Sail (Recommended for Development)

```bash
git clone https://github.com/dekaerp/dekaerp.git
cd dekaerp
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan erp:install
```

Visit `http://localhost` and log in with your admin credentials.

### Option B: Manual Installation

```bash
git clone https://github.com/dekaerp/dekaerp.git
cd dekaerp
composer install
cp .env.example .env
php artisan key:generate
```

Configure your `.env` with your database credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dekaerp
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Then run the installer:

```bash
php artisan erp:install
npm install && npm run build
php artisan serve
```

**What `erp:install` does:**

- ✅ Runs database migrations
- ✅ Seeds initial data (currencies, countries, roles)
- ✅ Generates roles & permissions via Filament Shield
- ✅ Creates the admin account
- ✅ Installs all plugins

---

## 🚀 Production Deployment

DEKA ERP includes a production-ready Docker setup in `docker/production/` designed for **Hetzner + Coolify** (or any Docker-based host).

### Architecture

```
┌─────────────────────────────────────┐
│  Docker Container (Supervisor)      │
│  ┌──────────┐  ┌──────────────────┐ │
│  │  Nginx   │  │  PHP-FPM 8.4     │ │
│  └──────────┘  └──────────────────┘ │
│  ┌──────────────────┐ ┌───────────┐ │
│  │  Queue Worker     │ │ Scheduler │ │
│  └──────────────────┘ └───────────┘ │
└──────────────┬──────────────────────┘
               │
    ┌──────────┴──────────┐
    │ Neon PostgreSQL      │
    │ (pooled endpoint)    │
    └─────────────────────┘
```

### Key Environment Variables

| Variable | Production Value |
|----------|-----------------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-domain.com` |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | Your Neon pooled connection string |
| `DB_SSLMODE` | `require` |
| `QUEUE_CONNECTION` | `database` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` (or `redis`) |
| `FILESYSTEM_DISK` | `public` (or `s3`) |

### Neon PostgreSQL Setup

The Docker entrypoint natively supports Neon via `DB_URL`:

```env
DB_CONNECTION=pgsql
DB_URL=postgresql://user:password@ep-xxx-pooler.region.aws.neon.tech/dekaerp?sslmode=require
```

Use the **pooled** endpoint (`-pooler`) for the application and the **direct** endpoint for schema migrations:

```bash
DB_URL="postgresql://user:pass@ep-xxx.region.aws.neon.tech/dekaerp?sslmode=require" \
  php artisan migrate --force
```

### Health Check

The container exposes a `/health` endpoint for uptime monitoring:

```
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3
    CMD curl -fsS http://127.0.0.1/health || exit 1
```

---

## 🏢 Multi-Tenancy

DEKA ERP uses **single-database, company-scoped** multi-tenancy. All tenants share one database; data isolation is enforced via Eloquent global scopes.

### How It Works

| Component | Description |
|-----------|-------------|
| **Company model** | Each tenant is a `Company` record in the `companies` table |
| **`BelongsToCompany` trait** | Adds `company_id` to models and auto-assigns it on creation |
| **`CompanyScope`** | Global scope that filters queries by the user's active company IDs |
| **`CompanyContext`** | Session-based service that tracks which companies the user has selected |
| **Company switcher** | UI component for switching between allowed companies |
| **`ChecksCompanyConsistency`** | Validates that related FK records belong to the same company |
| **`OwnershipScope`** | Record-level visibility (creator / team / global) |

### Company Access Control

- Users are assigned to companies via the `user_allowed_companies` pivot table
- The `AllowedCompanyScope` ensures the Company model itself is filtered
- Super admins can bypass scoping via the `bypass_company_scope` gate
- Settings are company-aware via `CompanyAwareSettingsRepository`

---

## 🧩 Plugin System

DEKA ERP features a modular plugin system with 28 plugins. Choose only the modules you need.

### 📦 Core Plugins (Always Installed)

| Module       | Description                                       |
| ------------ | ------------------------------------------------- |
| Support      | Database dialects, multi-tenancy, helpers, calendars |
| Security     | Users, roles, permissions, ownership scopes       |
| Fields       | Custom fields framework                           |
| Chatter      | Internal messaging, activity logging, notifications |
| Table Views  | Saved table filters and column configurations     |
| Analytics    | Dashboard widgets and business intelligence       |
| Plugin Manager | Plugin install/uninstall lifecycle               |

### ⚡ Installable Plugins

#### 💼 Financial Management

| Module     | Description                        |
| ---------- | ---------------------------------- |
| Accounts   | Chart of accounts, journals, moves |
| Accounting | Financial reporting (P&L, Balance Sheet, Trial Balance, General Ledger) |
| Invoices   | Invoice generation and management  |
| Payments   | Payment processing and tracking    |

#### 📦 Operations

| Module        | Description                                                                           |
| ------------- | ------------------------------------------------------------------------------------- |
| Products      | Product catalog, variants, attributes, price lists                                    |
| Inventories   | Warehouse management, stock moves, operations, routes, putaway rules                  |
| Manufacturing | Bill of Materials, Manufacturing Orders, Work Orders, Work Centers                    |
| Sales         | Quotations, sales orders, order templates, teams                                      |
| Purchases     | RFQs, purchase orders, vendor management                                              |

#### 👥 Human Resources

| Module       | Description                   |
| ------------ | ----------------------------- |
| Employees    | Employee profiles and org chart |
| Recruitments | Applicant tracking and hiring |
| Time-off     | Leave types, allocations, accrual plans |
| Timesheets   | Employee work hour tracking   |

#### 🤝 Customer & Partner Management

| Module   | Description                                  |
| -------- | -------------------------------------------- |
| Contacts | Contact management for customers and vendors |
| Partners | Partner relationship management              |

#### 📊 Project & Content Management

| Module   | Description                     |
| -------- | ------------------------------- |
| Blogs    | Content management and blogging |
| Projects | Project planning, tasks, milestones |
| Website  | Customer-facing portal with customer access management |

#### 🔧 Utilities

| Module        | Description                     |
| ------------- | ------------------------------- |
| Barcode       | Native barcode scanning support |
| Full Calendar | Calendar view integration       |
| Maintenance   | Equipment maintenance requests  |

---

## 🔧 Plugin Installation & Management

### Installing a Plugin

```bash
php artisan <plugin-name>:install
```

**Example:** Install the Inventories plugin

```bash
php artisan inventories:install
```

During installation, the system automatically checks for dependencies. If dependencies are detected, you'll see:

```
This package products is already installed. What would you like to do? [Skip]:
  [0] Reseed
  [1] Skip
  [2] Show Seeders
```

**Options:**

- **Reseed**: Reinstall the plugin's seed data (overwrites existing data)
- **Skip**: Continue without modifying the already installed dependency
- **Show Seeders**: Display available data seeders for the plugin

### Uninstalling a Plugin

```bash
php artisan <plugin-name>:uninstall
```

⚠️ **Warning:** Uninstalling a plugin will remove its database tables and data. The system blocks uninstallation when dependent plugins exist. Always backup your data first.

### Plugin Dependencies

Some plugins require other plugins to function properly. The installation system:

- ✅ Automatically detects dependencies
- ✅ Prompts you to install required plugins
- ✅ Prevents conflicts and missing prerequisites
- ✅ Blocks uninstallation when dependents exist

---

## 🎨 Customization

DEKA ERP is designed to be highly customizable:

### Plugin Customization

- 🔹 Install only the plugins you need
- 🔹 Extend existing plugins with custom functionality
- 🔹 Create custom plugins using the modular architecture

### UI/UX Customization

- 🔹 Branding settings (logo, colors) via admin panel
- 🔹 Custom dashboards and reports
- 🔹 Custom forms and views with Filament

### Access Control

- 🔹 Define custom user roles and permissions
- 🔹 Record-level ownership scopes (Individual, Group, Global)
- 🔹 Company-level data isolation
- 🔹 Granular permissions via Filament Shield

### Business Logic

- 🔹 Extend models with custom business rules
- 🔹 Create custom workflows via event listeners
- 🔹 REST API integration via Sanctum

---

## 🤝 Contributing

We welcome contributions! Whether you're fixing bugs, adding features, or improving documentation.

### How to Contribute

1. **Fork the Repository**

    ```bash
    git clone https://github.com/dekaerp/dekaerp.git
    ```

2. **Create a Feature Branch**

    ```bash
    git checkout -b feature/your-feature-name
    ```

3. **Make Your Changes**
    - Follow existing code conventions (check sibling files)
    - Write Pest tests for new features
    - Run `vendor/bin/pint --dirty` to format code

4. **Run Tests**

    ```bash
    php artisan test --compact
    ```

5. **Submit a Pull Request**
    - Provide a clear description of the changes
    - Reference any related issues
    - Ensure all tests pass

### Development Guidelines

- Follow Laravel and Filament best practices
- Format code with Laravel Pint: `vendor/bin/pint`
- Write Pest tests for new functionality
- Use meaningful commit messages
- Never commit `.env` files or secrets

---

## 📄 License

DEKA ERP is open-source software licensed under the [MIT License](LICENSE).

- ✅ Free to use for commercial and personal projects
- ✅ Modify and distribute as you wish
- ✅ No licensing fees or restrictions

---

## 🔒 Security

Security is a top priority for DEKA ERP.

### Built-in Security Features

- 🔐 Two-factor authentication with recovery codes
- 🛡️ Role-based access control via Filament Shield
- 🔑 API authentication via Laravel Sanctum with token expiration
- 🏢 Multi-tenant data isolation via company scopes
- 👤 Record-level ownership scopes

### Reporting Security Vulnerabilities

**⚠️ Please DO NOT disclose security vulnerabilities publicly.**

If you discover a security vulnerability, please report it responsibly:

📧 **Email:** security@dekaerp.com

Include:
- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact assessment
- Suggested fix (if available)

---

## 💬 Support & Community

### 📚 Documentation

- 📖 **Developer Docs:** [devdocs.dekaerp.com](https://devdocs.dekaerp.com/)
- 📘 **User Guide:** [docs.dekaerp.com](https://docs.dekaerp.com/)

### 🤝 Get Support

- 🐛 **Issue Tracker:** [GitHub Issues](https://github.com/dekaerp/dekaerp/issues)
- 📧 **Email Support:** support@dekaerp.com

### 🔔 Stay Updated

- ⭐ **Star** this repository to show your support
- 👁️ **Watch** for new releases and updates
- 🍴 **Fork** to contribute to the project

---

<div align="center">

**DEKA ERP** — Open-Source ERP for Modern Businesses

[⬆ Back to Top](#-table-of-contents)

</div>
