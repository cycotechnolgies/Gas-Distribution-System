# Gas Distribution System

A comprehensive web application for managing gas distribution operations, built with Laravel 12 and modern web technologies. This system handles customer management, delivery routes, inventory tracking, order processing, and supplier management.

## Project Overview

The Gas Distribution System is designed to streamline and optimize gas distribution operations. It provides features for:

- **Customer Management**: Track customer profiles, pricing tiers, and cylinder allocations
- **Inventory Management**: Monitor gas stock levels, manage refills, and track GRNs (Goods Received Notes)
- **Order Processing**: Create and manage customer orders with detailed item tracking
- **Route Management**: Plan and optimize delivery routes with route stops
- **Supplier Management**: Handle supplier information, purchase orders, and payment tracking
- **Vehicle & Driver Management**: Track delivery vehicles and assigned drivers
- **User Management**: Role-based access control and user administration

## Technology Stack

- **Framework**: Laravel 12
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Build Tool**: Vite
- **Database**: MySQL/MariaDB
- **PHP Version**: 8.2+
- **Package Manager**: Composer, NPM

## Authantications

- Role - admin
- email - admin@gas.com
- password - admin@1234

## Project Structure

```
Gas-Distribution-System/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Request handlers and application logic
│   │   └── Requests/           # Form request validation classes
│   ├── Models/                 # Eloquent ORM models
│   │   ├── Customer.php
│   │   ├── Order.php
│   │   ├── DeliveryRoute.php
│   │   ├── Driver.php
│   │   ├── GasType.php
│   │   ├── Supplier.php
│   │   ├── Stock.php
│   │   ├── Refill.php
│   │   └── ...
│   ├── Providers/              # Service providers
│   └── View/
│       └── Components/         # Reusable Blade components
├── bootstrap/
│   ├── app.php                 # Application bootstrap
│   └── cache/
├── config/
│   ├── app.php                 # Application configuration
│   ├── database.php            # Database connection settings
│   ├── auth.php                # Authentication configuration
│   ├── cache.php               # Cache configuration
│   ├── mail.php                # Mail configuration
│   └── ...
├── database/
│   ├── migrations/             # Database schema migrations
│   ├── factories/              # Model factories for testing
│   ├── seeders/                # Database seeding classes
├── public/
│   ├── index.php               # Application entry point
│   ├── build/                  # Compiled assets
│   └── images/                 # Public images
├── resources/
│   ├── css/                    # Tailwind CSS files
│   ├── js/                     # JavaScript files
│   └── views/                  # Blade template files
│       ├── layouts/            # Layout templates
│       ├── components/         # Reusable view components
│       ├── customers/          # Customer-related views
│       ├── orders/             # Order-related views
│       └── ...
├── routes/
│   ├── web.php                 # Web application routes
│   ├── auth.php                # Authentication routes
│   └── console.php             # Console commands
├── storage/
│   ├── app/                    # Application file storage
│   ├── logs/                   # Application logs
│   └── framework/              # Framework files
├── tests/
│   ├── Feature/                # Feature tests
│   ├── Unit/                   # Unit tests
│   └── TestCase.php            # Test base class
├── composer.json               # PHP dependencies
├── package.json                # Node.js dependencies
├── vite.config.js              # Vite build configuration
├── tailwind.config.js          # Tailwind CSS configuration
├── phpunit.xml                 # PHPUnit configuration
└── artisan                     # Laravel CLI tool
```

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL/MariaDB database
- XAMPP (or similar local development environment)

### Step-by-Step Installation

1. **Clone or Navigate to the Project**
   ```bash
   cd c:\xampp\htdocs\Gas-Distribution-System
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js Dependencies**
   ```bash
   npm install
   ```

4. **Create Environment Configuration**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gas_distribution
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Create Database**
   
   Create a new MySQL database named `gas_distribution`:
   ```bash
   mysql -u root -p -e "CREATE DATABASE gas_distribution;"
   ```

7. **Run Database Migrations**
   ```bash
   php artisan migrate
   ```

8. **Seed the Database (Optional)**
   ```bash
   php artisan db:seed
   ```

9. **Build Frontend Assets**
   ```bash
   npm run build
   ```
   
   Or for development with hot reload:
   ```bash
   npm run dev
   ```

10. **Start the Development Server**
    ```bash
    php artisan serve
    ```
    
    The application will be available at `http://127.0.0.1:8000`

## Configuration

### Key Environment Variables

- `APP_NAME`: Application name
- `APP_ENV`: Environment (local, production, testing)
- `APP_DEBUG`: Enable/disable debug mode
- `APP_URL`: Application URL
- `DB_*`: Database connection details
- `MAIL_*`: Mail configuration

### Database

All migrations are located in `database/migrations/`. Run `php artisan migrate` to execute all pending migrations.

### Front-end

- **CSS Framework**: Tailwind CSS configured in `tailwind.config.js`
- **Build Tool**: Vite configured in `vite.config.js`
- **JavaScript Framework**: Alpine.js for interactive components

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run with coverage
php artisan test --coverage
```

## Available Artisan Commands

```bash
# Database
php artisan migrate              # Run pending migrations
php artisan migrate:fresh        # Reset database and re-run migrations
php artisan db:seed              # Seed the database
php artisan tinker               # Interactive shell

# Cache
php artisan cache:clear          # Clear application cache
php artisan config:cache         # Cache configuration

# Queue
php artisan queue:work           # Start processing queued jobs

# Generate
php artisan make:migration        # Create a new migration
php artisan make:model           # Create a new model
php artisan make:controller      # Create a new controller
```

## Development Workflow

### Frontend Development

```bash
# Watch for CSS/JS changes and rebuild automatically
npm run dev
```

In another terminal, run the Laravel development server:

```bash
php artisan serve
```

### Database Changes

1. Create a new migration:
   ```bash
   php artisan make:migration create_table_name
   ```

2. Edit the migration file in `database/migrations/`

3. Run the migration:
   ```bash
   php artisan migrate
   ```

## Common Issues

### Database Connection Error
- Ensure MySQL is running
- Verify database credentials in `.env`
- Check database exists: `mysql -u root -e "SHOW DATABASES;"`

### npm Dependencies Not Found
- Run `npm install` again
- Delete `node_modules` and `package-lock.json`, then reinstall

### PHP Version Mismatch
- Verify PHP version: `php -v`
- Ensure PHP 8.2 or higher is installed

## Project Models

Key database models include:

- **Customer**: Gas customers with pricing tiers
- **Order/OrderItem**: Customer orders and line items
- **DeliveryRoute**: Delivery routes with multiple stops
- **Driver**: Delivery drivers assigned to routes
- **Supplier**: Gas suppliers
- **PurchaseOrder**: Orders placed with suppliers
- **Stock**: Current gas inventory levels
- **GasType**: Different gas products offered
- **Vehicle**: Delivery vehicles

## Screenshots

<img width="1364" height="610" alt="image" src="https://github.com/user-attachments/assets/608a1949-94ec-428e-ba72-17798f5313c5" />
<img width="1347" height="604" alt="image" src="https://github.com/user-attachments/assets/08ad903a-9c86-4bf9-a5ff-6e293331bc2b" />
<img width="1329" height="610" alt="image" src="https://github.com/user-attachments/assets/8b95844b-af14-4fb4-ac51-b76a8c2d18ed" />
<img width="1325" height="599" alt="image" src="https://github.com/user-attachments/assets/c5e51825-3ee0-4c2b-9d7b-dd44fdf3c99b" />

## Contributing

1. Create a feature branch
2. Make your changes
3. Write or update tests
4. Commit with descriptive messages
5. Push to the repository

## License

The Gas Distribution System is licensed under the MIT license. See LICENSE file for details.

## Support

For issues or questions, please contact me [cycotechnologies@gmail.com]
