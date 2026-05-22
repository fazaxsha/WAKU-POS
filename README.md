# WAKU-POS PWEB

WAKU-POS is a modern, web-based Point of Sale (POS) and inventory management system built with **Laravel 13** and the **TALL Stack** (Tailwind CSS, Alpine.js, Laravel). It is designed to handle multiple roles, real-time stock movements, and comprehensive transaction tracking.

## 🚀 Features

- **Multi-Role Authentication**: Secure login and access control using `spatie/laravel-permission`. Includes predefined roles: *Owner*, *Admin*, and *Kasir* (Cashier).
- **Product & Inventory Management**: Manage categories, products, and suppliers. Real-time stock movement tracking (purchases, sales, opname, adjustments).
- **POS Transactions**: Fast and intuitive checkout process, invoice generation, and support for multiple payment methods (Cash, Transfer, QRIS).
- **Purchase Management**: Restock inventory from suppliers.
- **Stock Opname**: Audit actual stock vs system stock and automatically calculate differences.
- **Activity Logging**: Comprehensive auditing of user actions across the system using `spatie/laravel-activitylog`.
- **Reporting & Exports**: Generate receipts and reports in PDF (`barryvdh/laravel-dompdf`) and Excel (`maatwebsite/excel`).
- **QR Code Generation**: Easily generate QR codes for products and transactions.

## 🛠 Tech Stack

**Backend:**
- PHP ^8.3
- Laravel ^13.0
- PostgreSQL (Recommended) or MySQL
- Sentry (Error Tracking)

**Frontend:**
- TailwindCSS ^3.1 / ^4.0
- Alpine.js
- Vite

## ⚙️ Installation

Follow these steps to set up the project locally.

### Prerequisites

- PHP >= 8.3
- Composer
- Node.js & npm
- PostgreSQL or MySQL server

### Step-by-step Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/waku-pos-pweb.git
   cd waku-pos-pweb
   ```

2. **Run the automated setup script**
   The project comes with a convenient Composer script to handle the initial setup (installs dependencies, sets up the `.env` file, generates the app key, migrates the database, and builds frontend assets):
   ```bash
   composer setup
   ```
   
   *(Alternatively, you can do this manually)*:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   npm install && npm run build
   ```

3. **Configure the Database**
   Open the `.env` file and update your database credentials. For example, if using PostgreSQL:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Run Migrations and Seeders**
   This will create all the necessary tables, configure permissions, and generate default users.
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Start the Development Server**
   ```bash
   composer dev
   ```
   This command concurrently runs `php artisan serve`, starts the queue listener, and boots up the Vite dev server. You can now access the app at `http://localhost:8000`.

## 👥 Default Users (Seeders)

After running the database seeders, you can log in using the following default accounts (password for all accounts is **`password`**):

| Role | Name | Email |
|------|------|-------|
| Owner | Budi Santoso | `owner@toko.com` |
| Admin | Rina Wijaya | `admin@toko.com` |
| Kasir 1 | Doni Prasetyo | `kasir1@toko.com` |
| Kasir 2 | Sari Melati | `kasir2@toko.com` |

## 📦 Key Dependencies

- [Spatie Permission](https://spatie.be/docs/laravel-permission) - Roles and permissions management.
- [Spatie Activitylog](https://spatie.be/docs/laravel-activitylog) - Logging user activities.
- [Laravel Excel](https://docs.laravel-excel.com/) - Fast Excel imports and exports.
- [Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf) - PDF generation for invoices and reports.
- [Intervention Image](https://image.intervention.io/) - Image processing for product and user uploads.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
