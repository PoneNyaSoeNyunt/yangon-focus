# Yangon Focus

**Hostel Management Platform for Yangon**

Yangon Focus is a full-stack web application built to streamline hostel discovery, booking, and management across Yangon. The platform serves three distinct user roles — **Guests** (Hostel Seekers), **Owners** (Hostel Operators), and a **Super Admin** — with a strong emphasis on **service reliability** and **data integrity**.

---

## Tech Stack

| Layer | Technology | Key Responsibilities / Packages |
| ----- | ---------- | ------------------------------- |
| **Frontend** | React 19 (Vite 8) | Single Page Application (SPA), Tailwind CSS 4, React Router 7, React Query 5, Axios |
| **Backend** | Laravel 12 (PHP 8.2) | RESTful API, Sanctum (Token Auth), Service Layer Architecture, Form Request Validation |
| **Database** | MariaDB 10.4 (XAMPP) for local development; managed MySQL on Aiven Cloud for production | Relational Storage, Strict Foreign-Key Constraints, Cascade Rules, Snake_case Schema |
| **Cloud Storage** | Cloudinary | External cloud media management for hostel galleries, business licenses, and payment screenshots |
| **Hosting (API)** | Railway Cloud | Isolated backend containerization running on an Apache Web Server layout |
| **Hosting (UI)** | Vercel | Vercel optimized SPA hosting for the React frontend with dynamic SPA route rewrites |
| **Tooling** | Composer 2.8, Node 22, npm 10 | Dependency tracking, script automation, package management |

---

## Design Principles

- **Service Reliability** — Incremental brute-force lockout (3 min → 10 min → 30 min → 24 h), centralized `status_codes` table for consistent state management across all entities, and database-backed sessions/cache/queues for crash resilience.
- **Data Integrity** — Foreign-key constraints on every relationship, structured NRC verification with region/township lookups, and transactional writes for bookings and payments.

---

## Prerequisites

| Requirement    | Version      |
| -------------- | ------------ |
| XAMPP           | **8.2.12**   |
| PHP             | **8.2.12**   |
| Composer        | **2.8.x**    |
| Node.js         | **22.x**     |
| npm             | **10.x**     |
| MariaDB         | 10.4+ (bundled with XAMPP) |

---

## Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/PoneNyaSoeNyunt/yangon-focus.git
cd yangon-focus
```

### 2. Backend setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

> `key:generate` automatically writes a random `APP_KEY` into your `.env` file. No manual action needed — Laravel uses this key to encrypt sessions, cookies, and other sensitive data. Never share or commit this key.

### 3. Configure the database

Open `.env` and set the database credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yangon_focus
DB_USERNAME=root
DB_PASSWORD=
```

Then create the database and run migrations + seeders:

```bash
# In XAMPP, start Apache & MySQL, then via phpMyAdmin or CLI:
mysql -u root -e "CREATE DATABASE IF NOT EXISTS yangon_focus;"

php artisan migrate
php artisan db:seed
```

### 4. Frontend setup

```bash
cd frontend
npm install
```

### 5. Run the application

From the project root:

```bash
# Terminal 1 — Backend
php artisan serve

# Terminal 2 — Frontend
cd frontend
npm run dev
```

- **Backend API**: `http://localhost:8000/api/v1`
- **Frontend**: `http://localhost:5173`

If you are running the frontend on a different host or port, add the matching origin to your `.env` as `FRONTEND_URL`.

---

## Testing Credentials

After running `php artisan db:seed`, the following account is available:

| Role            | Phone Number     | Password      |
| --------------- | ---------------- | ------------- |
| **Super Admin** | `09333777999`    | `$Admin123`   |

> **Guest** and **Owner** accounts can be created through the registration wizard at `/register`.

---

## Core Features

### Authentication & Security
- **Phone-number-based login** — Myanmar phone numbers (`09XXXXXXXXX`) as the primary identifier.
- **Incremental brute-force lockout** — 5 failed attempts trigger escalating lockout penalties (3 min, 10 min, 30 min, 24 h), tracked in the `auth_rate_limits` table.
- **Sanctum token authentication** — Stateless API tokens for the React SPA.

### NRC Verification
- **Structured NRC input** — Region code, township (searchable dropdown from `nrc_townships` table), type (N/P/E/T), and 6-digit number.
- **Data-backed lookups** — All 330+ townships seeded per official Myanmar NRC data.

### Hostel Management (Owner)
- **Inventory hierarchy** — Hostels → Rooms → Beds, each bed tracked with `is_occupied`.
- **Multi-step listing wizard** — Basic Info → Rooms & Beds → License & Gallery.
- **Business license verification** — Admin-reviewed with approve/reject workflow.
- **Subscription gating** — Owners must maintain an active subscription to list properties.

### Bookings & Payments
- **Booking lifecycle** — Pending → Active → Completed / Cancelled, managed via centralized `status_codes`.
- **Payment methods** — Cash (manual entry) and digital screenshots (KBZPay / WaveMoney).
- **Screenshot upload** — Owners and guests can attach payment proof images.

### Admin Dashboard
- **User management** — View, suspend, and activate accounts.
- **License verification** — Approve or reject hostel business licenses.
- **Subscription management** — Monitor owner subscription payments.
- **Misconduct reports** — Categorized reporting system with resolution tracking.
- **Analytics** — Platform-wide statistics and overview.

### Guest Features
- **Hostel discovery** — Browse and search hostels across Yangon townships.
- **Booking management** — View current stays, booking history, and make payments.
- **Reviews** — Rate and review hostels after a stay.

---

## Project Structure

```
yangon-focus/
├── app/
│   ├── Http/Controllers/   # Skinny controllers
│   ├── Models/             # Eloquent models with relationships
│   └── Services/           # Business logic (Service Classes)
├── database/
│   ├── migrations/         # 36 migration files (FK-constrained)
│   └── seeders/            # Status codes, townships, NRC data, admin user
├── routes/
│   └── api.php             # All API routes prefixed with /v1
├── frontend/
│   ├── src/
│   │   ├── pages/          # Route-level React components
│   │   ├── components/     # Reusable UI components
│   │   ├── context/        # AuthContext (global auth state)
│   │   ├── services/       # API service modules (axios)
│   │   └── api/            # Axios client configuration
│   └── package.json
├── .env.example
├── composer.json
└── README.md
```

---

## Environment Variables

### 1. Backend API Environment Variables (`.env`)

Create a `.env` file in your root folder and configure these variables:

| Variable | Scope | Description | Recommended Default |
| -------- | ----- | ----------- | ------------------- |
| `DB_CONNECTION` | All | Database driver type | `mysql` |
| `DB_HOST` | All | Database connection host (local XAMPP host or Aiven Cloud connection URI for production) | `127.0.0.1` (Local) / Aiven Cloud Connection URI |
| `DB_PORT` | All | Database port allocation | `3306` |
| `DB_DATABASE` | All | MariaDB target database name | `yangon_focus` |
| `FILESYSTEM_DISK` | All | Target driver for handling uploaded files | `public` (Local) / `cloudinary` (Prod) |
| `CLOUDINARY_URL` | Cloud | Connection token generated from Cloudinary console | *Required for production asset uploads* |
| `CLOUDINARY_FOLDER` | Cloud | Top-level folder prefix to separate media scopes | `yangon-focus-local` / `yangon-focus-staging` |
| `FRONTEND_URL` | Security | Whitelisted client URL used by Laravel CORS rules | `http://localhost:5173` / Vercel App URL |
| `SANCTUM_STATEFUL_DOMAINS` | Security | Allowed domains for stateful authentication cookie tracking | `localhost:5173` / Vercel naked domain |

### 2. Frontend Environment Variables (`frontend/.env`)

Create an `.env` file inside your `frontend/` directory to allow your React application to discover your Laravel service layer:

| Variable | Scope | Description | Recommended Default |
| -------- | ----- | ----------- | ------------------- |
| `VITE_API_BASE_URL` | UI Engine | The global endpoint entry point for Axios API requests | `http://localhost:8000/api/v1` / Railway Live API URL |

---

## License

This project is developed for academic and portfolio purposes by **PoneNyaSoeNyunt**.
