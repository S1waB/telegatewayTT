# TeleGateway IoT Platform

TeleGateway is a robust IoT management platform built with Laravel 9. It provides an intuitive interface for operators to monitor devices and a powerful backend for administrators to manage the system.

## Key Features
- **Role-Based Access Control**: Separate views and permissions for Administrators and Operators using Spatie Permissions.
- **Device Management**: Track devices, monitor statuses (active, inactive, maintenance), and view telemetry data.
- **MQTT Integration**: Dispatch JSON payloads and commands to connected devices asynchronously using `php-mqtt/client`.
- **API First**: Full Sanctum-protected REST API for frontend integrations and IoT callbacks.
- **Modern UI**: Clean, responsive Bootstrap 5 dashboard with Chart.js analytics.

## Getting Started

1. **Clone the repository**
2. **Install dependencies**: `composer install` and `npm install && npm run build`
3. **Setup environment**: Copy `.env.example` to `.env` and configure your database and MQTT credentials.
4. **Run migrations & seeders**: 
   ```bash
   php artisan migrate:fresh --seed
   ```
5. **Start the server**: `php artisan serve`

### Demo Accounts
- **Admin**: `admin@telegateway.io` / `password`
- **Operator**: `operator@telegateway.io` / `password`

## Requirements
- PHP 8.0+
- MySQL
- Composer
- Node.js & NPM
