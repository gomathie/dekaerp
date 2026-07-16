<h1 align="center">DekaERP</h1>

<p align="center">
  <strong>A modern, modular Enterprise Resource Planning system.</strong>
</p>

<p align="center">
  Built on <a href="https://laravel.com">Laravel</a> and <a href="https://filamentphp.com">FilamentPHP</a>.
</p>

---

## 📖 Overview

DekaERP is a comprehensive, open-source ERP solution for small and medium enterprises and large-scale organizations. It provides a modular platform for managing accounting, inventory, HR, CRM, sales, purchases, projects, and more — choose only the modules you need to keep the system lean.

## ✨ Features

- **Modular plugin system** — enable only the modules your business needs
- **Sales & purchases** — quotations, orders, vendors, and procurement
- **Inventory control** — stock levels, warehouses, and supply chain
- **Financial accounting** — invoicing, payments, and reporting
- **Human resources** — employees, attendance, time-off, and recruitment
- **Project management** — plan, track, and deliver on time
- **Multi-language support** and a responsive Filament admin UI

## ⚡ Quick Start

Get DekaERP running in a few steps:

### 1. Clone the repository

```bash
git clone https://github.com/gomathie/aureuserp1.git
cd aureuserp1
```

### 2. Install dependencies

```bash
composer install
npm install && npm run build
```

### 3. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Update the `DB_*` values in `.env` to match your database.

### 4. Run the installation

```bash
php artisan erp:install
```

This runs migrations, seeds core data, generates roles & permissions, and creates the admin account.

### 5. Start the server

```bash
php artisan serve
```

Visit `http://localhost:8000` and log in with your admin credentials.

> Using Docker? This project ships with Laravel Sail — run `./vendor/bin/sail up` instead.

## 🧩 Plugin System

DekaERP uses a modular plugin architecture (under `plugins/`). Each module is a self-contained package you can enable or disable to tailor the installation to your needs.

## 📄 License

DekaERP is open-source software released under the [MIT License](LICENSE).

## 🙏 Credits

DekaERP is built on top of [AureusERP](https://github.com/aureuserp/aureuserp), an open-source ERP framework by Webkul, and inherits its modular foundation. Also powered by [Laravel](https://laravel.com) and [FilamentPHP](https://filamentphp.com).
