# Travel.com - Vacational Products Sales

Laravel application for selling vacation packages with user bookings, reviews, and admin management.

## Requirements

- Docker & Docker Compose
- Git

## Quick Start with Docker

```bash
# Clone the repository
git clone <repository-url>
cd Travel.com

# Copy environment file
cp .env.example .env

# Start Docker containers
docker-compose up -d

# Install dependencies (inside container)
docker exec -it travel_app composer install

# Generate app key
docker exec -it travel_app php artisan key:generate

# Create storage link
docker exec -it travel_app php artisan storage:link

# Run migrations and seeders
docker exec -it travel_app php artisan migrate:fresh --seed
```

## Access

- **App**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

## Default Users (After Seeding)

| Email               | Password | Role     |
| ------------------- | -------- | -------- |
| admin@travel.com    | password | Admin    |
| advanced@travel.com | password | Advanced |
| user@travel.com     | password | User     |

## Features

- **Public**: Browse vacations, search, filter by category/price/location
- **Verified Users**: Book vacations, write reviews (10-min edit window)
- **Advanced Users**: Create and manage their own vacations
- **Admin**: Full CRUD on vacations, users, categories; approve reviews

## Tech Stack

- Laravel 12
- PHP 8.2
- MySQL 8
- Tailwind CSS (CDN)
- Docker

## Project Structure

```
app/
├── Custom/          # Custom classes (SentComments)
├── Http/
│   ├── Controllers/ # All controllers
│   └── Middleware/  # Admin, Advanced middleware
├── Models/          # Eloquent models
database/
├── migrations/      # Database migrations
├── factories/       # Model factories
├── seeders/         # Database seeders
resources/views/
├── layouts/         # Base layout
├── main/            # Landing page
├── vacation/        # Vacation CRUD views
├── user/            # User management views
├── auth/            # Auth views (login, register, dashboard)
```

## License

MIT
