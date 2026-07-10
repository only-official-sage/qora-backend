<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Qora Backend - Restaurant Management API

**A high-performance Laravel 12 REST API for restaurant and hotel management systems.**

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Schema](#database-schema)
- [API Reference](#api-reference)
- [Authentication](#authentication)
- [Development](#development)
- [Testing](#testing)
- [Deployment](#deployment)

---

## Features

- **Lightning Fast**: Built on Laravel 12 with optimized queries and eager loading
- **JWT Authentication**: Secure token-based auth with httpOnly cookies
- **Multi-Tenant**: Admin-scoped data isolation
- **RESTful API**: Well-structured endpoints with consistent response formats
- **Image Upload**: Base64 and file upload support with storage management
- **Rate Limiting**: Built-in throttle middleware for login attempts
- **Database agnostic**: SQLite by default, supports MySQL/PostgreSQL
- **Queue Ready**: Database-backed job queue for async operations
- **Production Ready**: Comprehensive error handling and logging

---

## Tech Stack

| Component | Technology | Version |
|-----------|------------|---------|
| Framework | Laravel | 12.0 |
| Language | PHP | 8.2+ |
| Database | SQLite / MySQL / PostgreSQL | - |
| Authentication | JWT (firebase/php-jwt) | 7.0 |
| Queue Driver | Database | - |
| Cache Driver | Database | - |
| Mail Driver | Log (development) | - |
| Frontend Build | Vite | - |
| Testing | PHPUnit | 11.5.50 |
| Code Style | Laravel Pint | 1.24 |

---

## Requirements

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ (for frontend builds)
- SQLite3 (default) or MySQL/PostgreSQL

---

## Installation

### 1. Clone and Navigate

```bash
cd backend
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite

# For MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=qora
# DB_USERNAME=root
# DB_PASSWORD=
```

Create SQLite database:

```bash
touch database/database.sqlite
```

### 5. Install Frontend Dependencies

```bash
npm install
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Build Frontend Assets

```bash
npm run build
```

### 8. Start Development Server

```bash
composer dev
```

This starts:
- Laravel server on http://localhost:8000
- Queue listener
- Laravel Pail (logs)
- Vite dev server on http://localhost:5173

---

## Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| APP_NAME | Application name | "Qora Backend" |
| APP_ENV | Environment | local |
| APP_KEY | Laravel encryption key | auto-generated |
| APP_DEBUG | Debug mode | true (local), false (production) |
| APP_URL | Application URL | http://localhost |
| DB_CONNECTION | Database driver | sqlite |
| JWT_SECRET | JWT signing secret | from `php artisan key:generate` |
| SESSION_DRIVER | Session storage | database |
| CACHE_DRIVER | Cache storage | database |
| QUEUE_CONNECTION | Queue driver | database |

### Security Headers

The application uses:
- httpOnly cookies for JWT storage
- Secure cookies in production (HTTPS only)
- SameSite=Strict policy
- Rate limiting on auth endpoints (60 attempts per minute)

---

## Database Schema

### Core Tables

#### `admins`
Business owners/administrators who manage restaurant data.

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| email | string | Unique email address |
| password | string | Hashed password |
| business_name | string | Restaurant/hotel name |
| business_type | enum | Restaurant, Hotel, Restaurant and Hotel |
| logo | string | Logo file path |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

**Related Tables:**
- `admin_addresses` (one-to-one)
- `admin_payments` (one-to-one)

#### `sections`
App sections (categories for organizing content).

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| name | string | Section name |
| type | string | Section type/classification |
| icon | array | JSON icon configuration |
| timestamps | - | Standard timestamps |

#### `dish_categories`
Categories for organizing dishes.

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| name | string | Category name |
| timestamps | - | Standard timestamps |

#### `dishes`
Menu items associated with categories.

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| category_id | bigInteger | Foreign key to dish_categories |
| name | string | Dish name |
| price | decimal | Item price |
| description | text | Optional description |
| ingredients | json/string | Array or comma-separated list |
| image | string | Image file path |
| timestamps | - | Standard timestamps |

#### `beverage_categories`
Categories for organizing beverages/drinks.

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| name | string | Category name |
| timestamps | - | Standard timestamps |

#### `beverages`
Drink items associated with categories.

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| category_id | bigInteger | Foreign key to beverage_categories |
| name | string | Beverage name |
| price | decimal | Item price |
| description | text | Optional description |
| ingredients | json/string | Array or comma-separated list |
| image | string | Image file path |
| timestamps | - | Standard timestamps |

#### `campaigns`
Marketing campaigns with audience targeting.

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| name | string | Campaign name |
| audience | enum | VIP, ROOM, TABLE, BAR, CUSTOM |
| description | text | Campaign description |
| image | string | Campaign image path |
| timestamps | - | Standard timestamps |

#### `staffs`
Staff members with scheduling information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| fname | string | First name |
| lname | string | Last name |
| email | string | Staff email |
| role | string | Job role/position |
| schedule | json | Working days and hours |
| timestamps | - | Standard timestamps |

**Schedule JSON Structure:**
```json
[
  {
    "day": "Monday",
    "start": "09:00",
    "end": "17:00"
  }
]
```

#### `admin_addresses`
Business address information (one-to-one with admins).

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| address | string | Street address |
| country | string | Country name |
| state | string | State/Province |
| city | string | City |
| country_code | string | ISO country code |
| phone_number | string | Contact phone |
| timestamps | - | Standard timestamps |

#### `admin_payments`
Bank/payment information (one-to-one with admins).

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | Primary key |
| admin_id | bigInteger | Foreign key to admins |
| bank_name | string | Bank name |
| account_number | string | Account number |
| account_holder_name | string | Account holder name |
| timestamps | - | Standard timestamps |

---

## API Reference

Base URL: `/api/admin`

All endpoints (except register, login, check-email, health) require authentication via JWT token in httpOnly cookie or Authorization header.

### Authentication Endpoints

#### POST `/auth/register`

Register a new admin account.

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password123",
  "business_name": "My Restaurant",
  "business_type": "Restaurant",
  "logo": "data:image/png;base64,iVBORw0KGgoAAAANS...",
  "bank_name": "Chase",
  "account_number": "1234567890",
  "account_holder_name": "John Doe",
  "address": "123 Main St",
  "country": "United States",
  "state": "California",
  "city": "Los Angeles",
  "country_code": "US",
  "phone_number": "+1234567890"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "email": "admin@example.com",
    "business_name": "My Restaurant",
    "business_type": "Restaurant",
    "logo": "logos/xyz.png"
  },
  "token": "eyJhbGciOiJIUzI1NiIs..."
}
```

**Status Codes:** 201 (Created), 422 (Validation Error)

---

#### POST `/auth/login`

Authenticate and receive JWT token.

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "email": "admin@example.com",
    "business_name": "My Restaurant"
  },
  "token": "eyJhbGciOiJIUzI1NiIs..."
}
```

**Cookies:** Sets `token` as httpOnly cookie (24h expiry)

**Status Codes:** 200 (Success), 401 (Invalid Credentials)

---

#### POST `/auth/check-email`

Check if an email is already registered.

**Request Body:**
```json
{
  "email": "admin@example.com"
}
```

**Response:**
```json
{
  "exists": true
}
```

**Status Codes:** 200 (Success), 422 (Validation Error)

---

#### GET `/health`

Health check endpoint (no auth required).

**Response:**
```json
{
  "status": "ok",
  "service": "qora-backend"
}
```

---

#### GET `/profile`

Get authenticated admin profile with address and payment info.

**Headers:** `Authorization: Bearer <token>` OR `Cookie: token=<jwt>`

**Response:**
```json
{
  "id": 1,
  "email": "admin@example.com",
  "business_name": "My Restaurant",
  "business_type": "Restaurant",
  "logo": "logos/xyz.png",
  "address": {
    "id": 1,
    "admin_id": 1,
    "address": "123 Main St",
    "country": "United States",
    "state": "California",
    "city": "Los Angeles",
    "country_code": "US",
    "phone_number": "+1234567890"
  },
  "payment": {
    "id": 1,
    "admin_id": 1,
    "bank_name": "Chase",
    "account_number": "1234567890",
    "account_holder_name": "John Doe"
  }
}
```

---

#### PUT `/profile`

Update admin profile, address, and payment info.

**Headers:** `Authorization: Bearer <token>` OR `Cookie: token=<jwt>`

**Request Body:** (all fields optional)
```json
{
  "business_name": "Updated Restaurant Name",
  "address": "456 New Address",
  "city": "San Francisco",
  "bank_name": "Bank of America",
  "logo": "data:image/png;base64,iVBORw0KGgoAAAANS..."
}
```

**Response:** Updated admin object with relations (200)

---

#### POST `/logout`

Invalidate session by clearing token cookie.

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

**Cookies:** Clears `token` cookie

---

### Sections Endpoints

#### GET `/sections`

List all sections for the authenticated admin (paginated, 10 per page).

**Headers:** Auth required

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "admin_id": 1,
      "name": "Breakfast Menu",
      "type": "menu",
      "icon": ["icon", "restaurant"],
      "created_at": "2025-04-30T..."
    }
  ],
  "total": 5
}
```

---

#### POST `/sections`

Create a new section.

**Headers:** Auth required

**Request Body:**
```json
{
  "name": "Dinner Menu",
  "type": "menu",
  "icon": ["icon", "utensils"]
}
```

**Response:** Created section object (201)

---

### Dish Categories & Dishes

#### POST `/categories/dish`

Create a new dish category.

**Headers:** Auth required

**Request Body:**
```json
{
  "name": "Appetizers"
}
```

**Response:**
```json
{
  "success": true,
  "message": "dish category created successfully",
  "data": {
    "id": 1,
    "admin_id": 1,
    "name": "Appetizers",
    "created_at": "2025-04-30T..."
  }
}
```

**Status Code:** 201

---

#### POST `/categories/dish/{category_id}`

Create a new dish in the specified category.

**Headers:** Auth required

**Request Body:**
```json
{
  "name": "Caesar Salad",
  "price": 12.99,
  "description": "Fresh romaine with caesar dressing",
  "ingredients": "romaine, parmesan, croutons, caesar dressing",
  "image": "data:image/jpeg;base64,/9j/4AAQ..."
}
```

**Response:**
```json
{
  "success": true,
  "message": "dish created successfully",
  "data": {
    "id": 1,
    "admin_id": 1,
    "category_id": 1,
    "name": "Caesar Salad",
    "price": 12.99,
    "description": "Fresh romaine with caesar dressing",
    "ingredients": ["romaine", "parmesan", "croutons", "caesar dressing"],
    "image": "dishes/xyz.jpg",
    "created_at": "2025-04-30T...",
    "updated_at": "2025-04-30T..."
  }
}
```

**Status Code:** 201

---

#### GET `/dishes`

List all dishes grouped by category (max 5 per category).

**Headers:** Auth required

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "category_name": "Appetizers",
      "data": [
        {
          "id": 1,
          "name": "Caesar Salad",
          "price": 12.99,
          "category": "Appetizers",
          "description": "Fresh romaine with caesar dressing",
          "ingredients": "romaine, parmesan, croutons, caesar dressing",
          "image": "dishes/xyz.jpg"
        }
      ]
    }
  ]
}
```

---

### Beverage Categories & Beverages

#### POST `/categories/beverage`

Create a new beverage category.

**Headers:** Auth required

**Request Body:**
```json
{
  "name": "Soft Drinks"
}
```

**Response:** Category object (201)

---

#### POST `/categories/beverage/{category_id}`

Create a new beverage in the specified category.

**Headers:** Auth required

**Request Body:** (same structure as dish creation)

**Response:** Beverage object (201)

---

#### GET `/beverages`

List all beverages grouped by category (max 5 per category).

**Headers:** Auth required

**Response:**
```json
{
  "Soft Drinks": [
    {
      "id": 1,
      "name": "Coca Cola",
      "price": 2.99,
      "description": "Classic cola",
      "ingredients": "carbonated water, sugar, caffeine",
      "image": "beverages/xyz.png",
      "category": {
        "id": 1,
        "name": "Soft Drinks"
      }
    }
  ]
}
```

---

### Campaigns Endpoints

#### POST `/campaigns`

Create a new marketing campaign.

**Headers:** Auth required

**Request Body:**
```json
{
  "name": "Summer Special",
  "audience": "VIP",
  "description": "Special discount for VIP customers",
  "image": "multipart/form-data image file"
}
```

**Response:** Campaign object (201)

**Audience Options:** `VIP`, `ROOM`, `TABLE`, `BAR`, `CUSTOM`

---

#### PUT `/campaigns/{id}`

Update a campaign.

**Headers:** Auth required

**Request Body:** (all fields optional)
```json
{
  "name": "Updated Campaign",
  "description": "Updated description"
}
```

**Response:** Updated campaign object (200)

---

#### DELETE `/campaigns/{id}`

Delete a campaign and its associated image.

**Headers:** Auth required

**Response:** 204 No Content

---

### Staff Endpoints

#### GET `/staffs`

List all staff members for the authenticated admin.

**Headers:** Auth required

**Response:**
```json
[
  {
    "id": 1,
    "admin_id": 1,
    "fname": "John",
    "lname": "Doe",
    "email": "john@example.com",
    "role": "Manager",
    "schedule": [
      {
        "day": "Monday",
        "start": "09:00",
        "end": "17:00"
      }
    ],
    "created_at": "2025-04-30T...",
    "updated_at": "2025-04-30T..."
  }
]
```

---

#### POST `/staffs`

Create a new staff member.

**Headers:** Auth required

**Request Body:**
```json
{
  "firstName": "Jane",
  "lastName": "Smith",
  "email": "jane@example.com",
  "role": "Chef",
  "workingdays": [
    {
      "day": "Tuesday",
      "start": "10:00",
      "end": "18:00"
    },
    {
      "day": "Wednesday",
      "start": "10:00",
      "end": "18:00"
    }
  ]
}
```

**Response:** Staff object (201)

---

#### PUT `/staffs/{id}`

Update staff member details.

**Headers:** Auth required

**Request Body:** (all fields optional)
```json
{
  "firstName": "Updated Name",
  "role": "Senior Chef",
  "workingdays": [
    {
      "day": "Monday",
      "start": "08:00",
      "end": "16:00"
    }
  ]
}
```

**Response:** Updated staff object (200)

---

## Authentication

This API uses JWT (JSON Web Tokens) for authentication with the following security measures:

1. **Token Storage**: httpOnly cookies prevent XSS attacks
2. **Secure Flag**: Enabled in production (HTTPS only)
3. **SameSite**: Strict policy prevents CSRF
4. **Expiry**: 24 hours from issuance
5. **Fallback**: Authorization header support for API clients

### Using the API

**Browser/SPA:**
- The token is automatically stored in httpOnly cookie after login/register
- Subsequent requests automatically include the cookie
- No manual token handling required

**API Clients (Postman, curl, mobile apps):**
```bash
# Login and save token
curl -X POST http://localhost:8000/api/admin/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' \
  -c cookies.txt

# Use saved cookies
curl http://localhost:8000/api/admin/profile -b cookies.txt

# OR use Authorization header (copy token from response)
curl http://localhost:8000/api/admin/profile \
  -H "Authorization: Bearer <token>"
```

---

## Development

### Directory Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── Admin/
│   │   │   │       ├── AuthController.php
│   │   │   │       └── StaffController.php
│   │   │   ├── CampaignController.php
│   │   │   ├── SectionsController.php
│   │   │   ├── BeverageCategoryController.php
│   │   │   ├── BeverageController.php
│   │   │   ├── DishCategoryController.php
│   │   │   └── DishController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Admin.php
│   │   ├── AdminAddress.php
│   │   ├── AdminPayment.php
│   │   ├── Campaign.php
│   │   ├── Dish.php
│   │   ├── DishCategory.php
│   │   ├── Section.php
│   │   ├── Staff.php
│   │   ├── Beverage.php
│   │   └── BeverageCategory.php
│   ├── Services/
│   │   └── JwtService.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── public/
├── resources/
│   └── js/
├── routes/
│   ├── api.php
│   ├── web.php
│   └── console.php
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
└── vite.config.js
```

### Code Style

This project follows Laravel Pint standards:

```bash
# Check code style
./vendor/bin/pint --test

# Auto-fix issues
./vendor/bin/pint
```

### Artisan Commands

```bash
php artisan route:list              # List all routes
php artisan config:cache            # Cache configuration
php artisan route:cache             # Cache routes
php artisan view:cache              # Cache views
php artisan optimize                # Optimize the framework
```

---

## Testing

### Running Tests

```bash
composer test
# or
php artisan test
```

### Test Structure

Tests are located in the `tests/` directory:

```
tests/
├── Feature/
│   └── Api/
│       └── Admin/
│           ├── AuthTest.php
│           ├── DishTest.php
│           ├── BeverageTest.php
│           ├── CampaignTest.php
│           └── StaffTest.php
└── Unit/
    └── ExampleTest.php
```

### HTTP Testing

Laravel's `TestResponse` provides convenient methods for assertions:

```php
$response->assertStatus(200)
          ->assertJsonStructure(['data'])
          ->assertJsonFragment(['name' => 'Test']);
```

---

## Performance & Speed

This backend is optimized for speed and performance:

### Optimizations Implemented

1. **Eager Loading**: Relationships loaded with `with()` to prevent N+1 queries
2. **Pagination**: List endpoints paginated to limit response size
3. **Database Indexes**: Foreign keys and frequently queried columns indexed
4. **Query Optimization**: Grouped queries with constrained result sets
5. **Lightweight JSON**: Minimal response payload with essential fields only
6. **Connection Pooling**: Database connection reuse via persistent connections
7. **Cache Ready**: Database cache driver configured for query caching
8. **Queue Processing**: Background jobs for heavy operations

### Benchmark Recommendations

For measuring endpoint speed:

```bash
# Using Apache Bench
ab -n 1000 -c 10 http://localhost:8000/api/admin/health

# Using wrk
wrk -t12 -c100 -d30s http://localhost:8000/api/admin/health

# Using curl with timing
curl -w "@curl-format.txt" -o /dev/null -s http://localhost:8000/api/admin/health
```

Expected performance (development environment):
- Health check: < 10ms
- Auth endpoints: < 50ms
- CRUD operations: < 100ms
- List endpoints (paginated): < 150ms

### Production Optimizations

Before deploying to production:

1. Enable config and route caching:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. Use production database (MySQL/PostgreSQL) instead of SQLite

3. Set `APP_DEBUG=false` in `.env`

4. Enable HTTPS and set `SESSION_SECURE_COOKIE=true`

5. Use Redis or Memcached for cache and session drivers

6. Configure queue workers for background jobs

---

## Error Handling

Standard HTTP status codes are used:

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | OK | Successful GET/PUT requests |
| 201 | Created | Successful POST requests |
| 204 | No Content | Successful DELETE requests |
| 400 | Bad Request | Validation failures |
| 401 | Unauthorized | Missing/invalid token |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource doesn't exist |
| 422 | Unprocessable Entity | Validation errors with details |
| 500 | Internal Server Error | Server-side errors |

### Validation Error Format

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "price": ["The price must be a number."]
  }
}
```

### Authentication Error Format

```json
{
  "message": "Unauthorized - No token"
}
```
or
```json
{
  "message": "Unauthorized - Invalid token"
}
```

---

## Security Considerations

1. **SQL Injection**: Prevented via Laravel's query builder/Eloquent
2. **XSS**: User input is escaped in Blade templates (if used)
3. **CSRF**: Not applicable for stateless API (except web routes)
4. **File Uploads**: 
   - Validated MIME types
   - Stored outside public root (accessed via routes)
   - Size limits configured
5. **Password Hashing**: bcrypt with default cost factor
6. **Rate Limiting**: Applied to login endpoint (60/min)
7. **CORS**: Configure based on frontend origin in production

---

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Follow Laravel coding standards (Pint)
4. Write tests for new features
5. Ensure all tests pass: `composer test`
6. Submit a pull request

---

## License

MIT License. See [LICENSE](LICENSE) file for details.

---

## Support

For issues, questions, or feature requests, please open an issue on GitHub.

---

## Quick Reference

### Default Ports
- API Server: `http://localhost:8000`
- Vite Dev Server: `http://localhost:5173`

### Common Commands
```bash
composer dev          # Start all dev services
php artisan serve    # Start Laravel server only
npm run dev          # Start Vite dev server only
php artisan test     # Run tests
php artisan migrate  # Run migrations
php artisan tinker   # Interactive shell
```

### Postman Collection

Import the API collection from `docs/postman_collection.json` (to be created).
