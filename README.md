# 💊 PharmacyPOS

A complete, secure **Pharmacy Point-of-Sale & Inventory Management System** built with **Laravel 12**, plain **HTML/CSS/JS** (no front-end build step required), and **MySQL/MariaDB**.

It has **two sides**:

| Side | Who | What they can do |
|------|-----|------------------|
| 🧑‍💼 **Owner** | Pharmacy owner / manager | Full dashboard, manage products, categories, suppliers & staff, view all sales, run reports |
| 🧑‍🔧 **Attendant** | Counter staff | Fast Point-of-Sale checkout, view their own sales, print receipts |

---

## ✨ Features

- **Role-based two-sided system** — separate, secure workspaces for owners and attendants.
- **Fast Point of Sale** — live product search, category filtering, cart, discounts, tax, change calculation, and instant printable receipts.
- **Inventory management** — products, categories, suppliers, stock levels, reorder alerts and expiry tracking.
- **Atomic checkout** — stock is decremented inside a database transaction with row locking to prevent overselling.
- **Owner dashboard** — revenue stats, 7-day sales chart, top products, low-stock and expiry alerts.
- **Reports** — date-range sales reports by day, payment method and best-selling products (printable).
- **Staff management** — owners create/disable owner & attendant accounts with strong-password enforcement.
- **Security first** — see [Security](#-security) below.
- **SEO** — public landing page with full meta tags, Open Graph, Twitter cards, JSON-LD structured data, `sitemap.xml` and `robots.txt`.

---

## 🗄️ Database Design

> **Naming convention:** every table and every column uses a **single lowercase word with no underscores** (e.g. `saleitems`, `categoryid`, `createdat`, `remembertoken`) as required.

| Table | Purpose |
|-------|---------|
| `users` | Owners & attendants (`role` = `owner` / `attendant`) |
| `categories` | Product categories |
| `suppliers` | Product suppliers |
| `products` | Inventory items (price, cost, quantity, reorder, expiry) |
| `sales` | Sales/invoices header |
| `saleitems` | Line items per sale |

---

## 🚀 Getting Started

### Requirements
- PHP 8.2+
- Composer
- MySQL / MariaDB (this project was built against **XAMPP / MariaDB 10.4**)

### Installation

```bash
# 1. Clone
git clone https://github.com/defhopeee/PharmacyPOS.git
cd PharmacyPOS

# 2. Install dependencies
composer install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Create the database (named "pharmacypos") then configure DB_* in .env

# 5. Migrate & seed demo data
php artisan migrate:fresh --seed

# 6. Run
php artisan serve
```

Visit **http://localhost:8000**

---

## 🔑 Demo Accounts

All seeded users share the password: **`Password123!`**

| Role | Email |
|------|-------|
| Owner | `owner@pharmacypos.test` |
| Attendant | `attendant@pharmacypos.test` |
| Attendant | `brenda@pharmacypos.test` |
| Attendant | `caleb@pharmacypos.test` |

The seeder also creates **7 categories, 3 suppliers, 18 products** and **~40 sample sales**.

---

## 🔒 Security

PharmacyPOS is built to be safe by default:

- **Password hashing** — bcrypt (12 rounds), passwords never stored in plain text.
- **Strong password policy** — min 8 chars, mixed case, numbers and symbols on user creation.
- **CSRF protection** — on every form and AJAX request (Laravel default + meta token).
- **Role-based access control** — custom `role` middleware guards every owner/attendant route.
- **Brute-force protection** — login is rate-limited (5 attempts/min per email+IP).
- **Session security** — session regeneration on login, invalidation on logout.
- **SQL injection safe** — all queries use Eloquent ORM / parameter binding.
- **XSS safe** — Blade auto-escapes all output.
- **Hardened HTTP headers** — `Content-Security-Policy`, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` (see `app/Http/Middleware/SecurityHeaders.php`).
- **Mass-assignment protection** — explicit `$fillable` on every model.
- **Account deactivation** — disabled users are blocked from logging in.

---

## 🧱 Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Database:** MySQL / MariaDB
- **Frontend:** Blade templates, hand-written CSS, vanilla JavaScript (no build tooling)

---

## 📁 Project Structure

```
app/
 ├─ Http/Controllers/        # Auth, Dashboard, Pos, Sale, Owner/*
 ├─ Http/Middleware/         # RoleMiddleware, SecurityHeaders
 └─ Models/                  # User, Category, Supplier, Product, Sale, SaleItem
database/
 ├─ migrations/              # single-name, no-underscore schema
 └─ seeders/                 # demo data (password Password123!)
resources/views/
 ├─ layouts/app.blade.php    # app shell (role-aware sidebar)
 ├─ landing.blade.php        # SEO public page
 ├─ auth/ owner/ attendant/ pos/ sales/
public/
 ├─ css/app.css              # full stylesheet
 └─ robots.txt
routes/web.php
```

---

## 📜 License

Released under the MIT License.

Built with ❤️ using Laravel.
