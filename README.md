# 🌱 Smart Waste Segregation & Collection System

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MongoDB](https://img.shields.io/badge/MongoDB-4EA94B?style=for-the-badge&logo=mongodb&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Leaflet](https://img.shields.io/badge/Leaflet-199900?style=for-the-badge&logo=Leaflet&logoColor=white)

A comprehensive, robust, and user-friendly web application for managing modern urban waste collection, built with **Laravel 11**, **MongoDB 7**, and **Tailwind CSS**. 

This platform connects **Citizens**, **Waste Collection Drivers**, and **System Administrators** in real-time, streamlining the process of waste segregation, pickup requests, and fleet routing.

---

## ✨ Key Features

### 👨‍👩‍👧‍👦 For Citizens
- **Pickup Requests**: Easily request waste pickups and categorize waste types (Organic, Recyclable, Hazardous).
- **Interactive Maps**: Pinpoint exact pickup locations using Leaflet.js interactive maps.
- **Real-Time Tracking**: Track the status of pickup requests (Pending, Assigned, Completed).

### 🚛 For Drivers
- **Smart Routing**: View optimized daily routes with clear lists of assigned pickup stops.
- **Status Updates**: Mark pickups as collected, document issues, and manage route progress effortlessly.
- **Live Notifications**: Get instant updates from admins when new stops are assigned.

### 👑 For Administrators
- **Comprehensive Dashboard**: View real-time analytics using Chart.js (Requests over time, waste type breakdown).
- **Fleet Management**: Manage driver assignments, view truck statuses, and monitor daily efficiency.
- **Data Export**: Export collection data and reports to CSV for external audits.

---

## 🛠️ Technology Stack

- **Backend**: Laravel 11.x
- **Database**: MongoDB 7.0 (with `mongodb/laravel-mongodb` ODM package)
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Mapping**: Leaflet.js for geospatial rendering
- **Charts**: Chart.js for data visualization
- **Roles & Permissions**: Spatie Laravel Permissions

---

## 🚀 Installation & Local Setup

### Prerequisites
- PHP 8.2+ with the `mongodb` extension enabled (`pecl install mongodb`)
- Composer
- MongoDB 7 Community Edition (running locally on port `27017`)
- Node.js & NPM

### Step-by-Step Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/mvc-project.git
   cd mvc-project/waste-system
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install and compile frontend assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup:**
   Copy the example environment file and configure it:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure your MongoDB is running locally on port 27017.*

5. **Storage Link:**
   ```bash
   php artisan storage:link
   ```

6. **Database Migration & Seeding:**
   Run migrations and seed the database with demo users, trucks, and pickup requests:
   ```bash
   php artisan migrate:fresh --seed --class=DemoDataSeeder
   ```

7. **Start the Application:**
   Run the Laravel development server:
   ```bash
   php artisan serve
   ```
   *The app will be available at `http://127.0.0.1:8000`.*

---

## 🔐 Testing Credentials

All test users have the default password: **`password`**

| Role | Email | Permissions |
|------|-------|-------------|
| **Admin** | `admin1@waste.local` | Full system access, routing, and analytics. |
| **Citizen** | `citizen@waste.local` | Create and track pickup requests. |
| **Driver** | `driver@waste.local` | View assigned routes and update collection status. |

*(The seeder generates an additional 9 random citizens and 4 random drivers for large-scale testing).*

---

## 📂 Project Structure

- `app/Models`: Contains MongoDB-enabled Eloquent models (`User`, `PickupRequest`, `Route`, etc.)
- `app/Http/Controllers`: Separated into `Admin`, `Citizen`, and `Driver` namespaces for clean role-based logic.
- `resources/views`: Organized by role with a responsive, modern Tailwind layout.
- `routes/web.php`: Role-based routing using Spatie middleware.

---

*Built with ❤️ for a cleaner, greener tomorrow.*
