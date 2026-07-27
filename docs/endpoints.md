# QORA API Endpoints Documentation

> Generated from QORA-Main Figma design analysis.
> All `/admin` routes require JWT auth via `token` cookie or `Authorization: Bearer` header.
> Customer/Hotel endpoints are public.

---

## Table of Contents

1. [Authentication](#1-authentication)
2. [Admin Onboarding / Profile](#2-admin-onboarding--profile)
3. [Sections (Venue Layout)](#3-sections-venue-layout)
4. [Tables (within Sections)](#4-tables-within-sections)
5. [Dish Categories & Dishes](#5-dish-categories--dishes)
6. [Beverage Categories & Beverages](#6-beverage-categories--beverages)
7. [Orders](#7-orders)
8. [Customer Requests (Waiter/Cleanup)](#8-customer-requests-waitercleanup)
9. [Campaigns](#9-campaigns)
10. [Staff Management](#10-staff-management)
11. [QR Codes](#11-qr-codes)
12. [POS Requests](#12-pos-requests)
13. [Customer Analytics (Hotel/Restaurant)](#13-customer-analytics-hotelrestaurant)
14. [Serviq Shop](#14-serviq-shop)
15. [Social Media Assets](#15-social-media-assets)

---

## 1. Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/admin/auth/login` | None | Admin login → returns JWT |
| `POST` | `/admin/auth/register` | None | Admin signup (multi-step) |
| `POST` | `/admin/auth/logout` | JWT | Invalidate token |
| `GET` | `/admin/auth/profile` | JWT | Get current admin info |

### Auth Views Mapped
- `onboarding/Sign Up` → `POST /admin/auth/register`
- `onboarding/Log In` → `POST /admin/auth/login`

---

## 2. Admin Onboarding / Profile

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/profile` | JWT | Get full admin profile |
| `PUT` | `/admin/profile/business-info` | JWT | Update business name, type, logo |
| `PUT` | `/admin/profile/address` | JWT | Update address, country, state, city, phone |
| `PUT` | `/admin/profile/payment` | JWT | Update bank name, account number, holder |
| `PUT` | `/admin/profile/password` | JWT | Change password |

### Profile Views Mapped
- `onboarding/business-info` → `PUT /admin/profile/business-info`
- `onboarding/business-address` → `PUT /admin/profile/address`
- `onboarding/payments` → `PUT /admin/profile/payment`
- `Profile/Change account` → `GET /admin/profile`
- `Profile/Change Password` → `PUT /admin/profile/password`

---

## 3. Sections (Venue Layout)

Sections represent venue areas (e.g., "Main Hall", "VIP Lounge", "Terrace").

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/sections` | JWT | List all sections |
| `GET` | `/admin/sections/{id}` | JWT | Get section details |
| `POST` | `/admin/sections` | JWT | Create section |
| `PUT` | `/admin/sections/{id}` | JWT | Update section (name, type, icon) |
| `DELETE` | `/admin/sections/{id}` | JWT | Delete section |
| `PATCH` | `/admin/sections/{id}/status` | JWT | Toggle section active/inactive |

### Views Mapped
- `Section Management` → `GET /admin/sections`
- `Section Management (Empty)` → `GET /admin/sections` (empty result)
- `Section Management | New Section` → `POST /admin/sections`
- `Section Management | Table 1..10` → `GET /admin/sections/{id}` (section with tables)
- `Section Card` → `GET /admin/sections/{id}` (individual card view)

---

## 4. Tables (within Sections)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/sections/{id}/tables` | JWT | List tables in a section |
| `GET` | `/admin/sections/{sectionId}/tables/{tableId}` | JWT | Get table details |
| `POST` | `/admin/sections/{id}/tables` | JWT | Create table in section |
| `PUT` | `/admin/sections/{sectionId}/tables/{tableId}` | JWT | Update table (name, status, capacity) |
| `PATCH` | `/admin/sections/{sectionId}/tables/{tableId}/status` | JWT | Update table status (available/occupied/reserved) |
| `DELETE` | `/admin/sections/{sectionId}/tables/{tableId}` | JWT | Delete table |
| `GET` | `/admin/tables` | JWT | List all tables across sections |

### Table Views Mapped
- `Home | Table management` → `GET /admin/sections` (all sections with tables)
- `Home | Table management (Empty state)` → `GET /admin/sections` (empty)
- `Home | Table management | Table details` → `GET /admin/sections/{id}/tables`
- `Home | Table management | Table details | Table History` → order history of table
- `Home | Table management | Table details | Table Orders` → active orders for table
- `Home | Table management | Table details | Table Requests` → customer requests for table

---

## 5. Dish Categories & Dishes

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/food/categories` | JWT | List all dish categories |
| `GET` | `/admin/food/categories/{id}` | JWT | Get category details |
| `POST` | `/admin/food/categories` | JWT | Create dish category |
| `PUT` | `/admin/food/categories/{id}` | JWT | Update category |
| `DELETE` | `/admin/food/categories/{id}` | JWT | Delete category |
| `GET` | `/admin/food/dishes` | JWT | List all dishes (optionally filter by `category_id`) |
| `GET` | `/admin/food/dishes/{id}` | JWT | Get dish details |
| `POST` | `/admin/food/dishes` | JWT | Create dish |
| `PUT` | `/admin/food/dishes/{id}` | JWT | Update dish |
| `DELETE` | `/admin/food/dishes/{id}` | JWT | Delete dish |

### Query Params (Dishes)
- `?category_id={id}` — filter by category
- `?search={query}` — search by name/description

### Views Mapped
- `Home | Menu management` → `GET /admin/food/categories`
- `Home | Menu management (Empty state)` → `GET /admin/food/categories` (empty result)
- `Home | Menu management | Add Food` → `POST /admin/food/dishes`
- `Home | Menu management | Food` → `GET /admin/food/dishes?category_id={id}`
- `Elements/Foods Card` → `GET /admin/food/dishes/{id}` (card component)
- `Product card` → reusable display (dish or beverage card)

---

## 6. Beverage Categories & Beverages

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/drinks/categories` | JWT | List all beverage categories |
| `GET` | `/admin/drinks/categories/{id}` | JWT | Get category details |
| `POST` | `/admin/drinks/categories` | JWT | Create beverage category |
| `PUT` | `/admin/drinks/categories/{id}` | JWT | Update category |
| `DELETE` | `/admin/drinks/categories/{id}` | JWT | Delete category |
| `GET` | `/admin/drinks` | JWT | List all beverages (optionally filter by `category_id`) |
| `GET` | `/admin/drinks/{id}` | JWT | Get beverage details |
| `POST` | `/admin/drinks` | JWT | Create beverage |
| `PUT` | `/admin/drinks/{id}` | JWT | Update beverage |
| `DELETE` | `/admin/drinks/{id}` | JWT | Delete beverage |

### Views Mapped
- `Home | Menu management | Add Drink` → `POST /admin/drinks`
- `Home | Menu management | Drinks` → `GET /admin/drinks?category_id={id}`
- `Restaurant | Drinks` → `GET /admin/drinks` (customer-facing API, returns all public beverages)

---

## 7. Orders

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/orders` | JWT | List all orders (paginated) |
| `GET` | `/admin/orders?status=pending` | JWT | Filter by status |
| `GET` | `/admin/orders?date={YYYY-MM-DD}` | JWT | Filter by date |
| `GET` | `/admin/orders/{id}` | JWT | Get order details |
| `PATCH` | `/admin/orders/{id}/status` | JWT | Update order status |
| `PATCH` | `/admin/orders/{id}/time` | JWT | Input order time |
| `GET` | `/admin/orders/today` | JWT | Today's orders count |

### Query Params
- `?status={pending|confirmed|cooking|served|completed|cancelled}`
- `?date={YYYY-MM-DD}`
- `?table_id={id}` — orders for specific table
- `?staff_id={id}` — orders by specific staff

### Views Mapped
- `Home | Analytics | New Orders` → `GET /admin/orders?status=pending`
- `Home | Analytics | Total orders today` → `GET /admin/orders/today`
- `Section Management | Table 1 | History | Orders Made` → `GET /admin/orders?table_id={id}`
- `Section Management | Table 1 | History | Orders Made | Order Details` → `GET /admin/orders/{id}`
- `Home | Table management | Table details | Table Orders` → `GET /admin/orders?table_id={id}`
- `Home | Table management | Table details | Table Orders | Order Details` → `GET /admin/orders/{id}`
- `Home | Table management | Table details | Table Orders | Order Details | Assign` → `PATCH /admin/orders/{id}/status`
- `Dashboard | Order` → `GET /admin/orders/{id}` (order detail card)
- `Dashboard | Total orders today` → `GET /admin/orders/today`
- `Order button` / `Order framed` → `POST /admin/orders` (from customer side, creates order)

---

## 8. Customer Requests (Waiter/Cleanup)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/requests` | JWT | List all customer requests |
| `GET` | `/admin/requests?type=waiter` | JWT | Filter by type (waiter/cleanup/other) |
| `GET` | `/admin/requests?table_id={id}` | JWT | Requests for specific table |
| `POST` | `/admin/requests` | JWT | Create request (admin-initiated) |
| `PATCH` | `/admin/requests/{id}/status` | JWT | Mark request as handled |
| `DELETE` | `/admin/requests/{id}` | JWT | Delete/cancel request |

### Views Mapped
- `Home | Analytics | Service requests` → `GET /admin/requests?type=waiter`
- `Home | Analytics | Cleaning requests` → `GET /admin/requests?type=cleanup`
- `Request card` → `GET /admin/requests/{id}` (individual request display)
- `Section Management | Table 1 | History | Requests` → `GET /admin/requests?table_id={id}`
- `Home | Table management | Table details | Table Requests` → `GET /admin/requests?table_id={id}`
- `Home | Table management | Table details | Table Requests | Assign` → `PATCH /admin/requests/{id}/status`

---

## 9. Campaigns

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/campaigns` | JWT | List all campaigns |
| `GET` | `/admin/campaigns/{id}` | JWT | Get campaign details |
| `POST` | `/admin/campaigns` | JWT | Create campaign |
| `PUT` | `/admin/campaigns/{id}` | JWT | Update campaign |
| `DELETE` | `/admin/campaigns/{id}` | JWT | Delete campaign |

### Views Mapped
- `Campaigns` → `GET /admin/campaigns`
- `Campaigns (Empty)` → `GET /admin/campaigns` (empty state)
- `Campaigns | New Campaign` → `POST /admin/campaigns`
- `Campaigns | Edit Campaign` → `PUT /admin/campaigns/{id}`
- `Campaigns | Delete Campaign` → `DELETE /admin/campaigns/{id}`

---

## 10. Staff Management

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/staff` | JWT | List all staff |
| `GET` | `/admin/staff/{id}` | JWT | Get staff details |
| `POST` | `/admin/staff` | JWT | Add staff member |
| `PUT` | `/admin/staff/{id}` | JWT | Update staff info |
| `DELETE` | `/admin/staff/{id}` | JWT | Remove staff |
| `PATCH` | `/admin/staff/{id}/role` | JWT | Change staff role |
| `GET` | `/admin/staff/leaderboard` | JWT | Staff performance leaderboard |
| `POST` | `/admin/staff/assign` | JWT | Assign staff to table/order |
| `GET` | `/admin/staff/assignments` | JWT | List staff assignments |

### Views Mapped
- `Home | Staff management` → `GET /admin/staff`
- `Home | Staff management (Empty state)` → `GET /admin/staff` (empty)
- `Home | Staff management | Add Staff` → `POST /admin/staff`
- `Home | Staff management | Staff Details` → `GET /admin/staff/{id}`
- `Home | Staff management | Staff Details | Edit Staff` → `PUT /admin/staff/{id}`
- `Home | Staff management | Staff Details | Change Role` → `PATCH /admin/staff/{id}/role`
- `Home | Staff management | Leaderboard` → `GET /admin/staff/leaderboard`
- `Section Management | Table 1 | History | Staffs Assigned` → `GET /admin/staff/assignments?table_id={id}`
- `Section Management | Staffs Assigned` → `GET /admin/staff/assignments`
- `Staff card` → `GET /admin/staff/{id}` (card component)

---

## 11. QR Codes

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/qr-codes` | JWT | List all QR codes |
| `POST` | `/admin/qr-codes` | JWT | Generate QR code for table/section |
| `GET` | `/admin/qr-codes/{id}` | JWT | Get QR code details |
| `DELETE` | `/admin/qr-codes/{id}` | JWT | Delete QR code |
| `GET` | `/admin/qr-codes/{id}/download` | JWT | Download QR code image |

### Views Mapped
- `Admin (Restaurant) | QR codes` → `GET /admin/qr-codes`
- `QR Code` → `GET /admin/qr-codes/{id}/download`

---

## 12. POS Requests

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/pos-requests` | JWT | List POS payment requests |
| `POST` | `/admin/pos-requests` | JWT | Create POS request |
| `PATCH` | `/admin/pos-requests/{id}/status` | JWT | Update POS request status |
| `DELETE` | `/admin/pos-requests/{id}` | JWT | Cancel POS request |

### Views Mapped
- `Home | Analytics | POS requests` → `GET /admin/pos-requests`
- `Analytics pill` (POS filter) → `GET /admin/pos-requests?status=pending`

---

## 13. Customer Analytics (Hotel/Restaurant)

Customer-facing endpoints that power the analytics screens in the mobile app.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/orders/analytics?table_id={id}` | Public (admin_id) | Customer's session analytics |
| `GET` | `/orders/analytics/room?admin_id={id}` | Public (admin_id) | Hotel room service analytics |
| `GET` | `/orders/history?admin_id={id}` | Public (admin_id) | Order history for customer |

### Views Mapped
- `Customer (Restaurant) | Analytics` → `GET /orders/analytics?table_id={id}`
- `Customer (Hotel) | Analytics` → `GET /orders/analytics/room?admin_id={id}`
- `Home | Analytics (Customer Issues)` → `GET /admin/requests?type=issues`
- `Filter` → query param `?filter={date_range|popular|revenue}` on analytics endpoints

---

## 14. Serviq Shop

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/serviq/products` | JWT | List shop products |
| `GET` | `/admin/serviq/products/{id}` | JWT | Get product details |
| `POST` | `/admin/serviq/products` | JWT | Create product |
| `PUT` | `/admin/serviq/products/{id}` | JWT | Update product |
| `DELETE` | `/admin/serviq/products/{id}` | JWT | Delete product |
| `POST` | `/admin/serviq/checkout` | JWT | Initiate checkout |
| `GET` | `/admin/serviq/orders` | JWT | List shop orders |
| `GET` | `/admin/serviq/orders/{id}` | JWT | Get shop order details |

### Views Mapped
- `Home | Serviq shop` → `GET /admin/serviq/products`
- `Home | Serviq shop | Frame Details` → `GET /admin/serviq/products/{id}`
- `Home | Serviq shop | Frame Details | Checkout` → `POST /admin/serviq/checkout`
- `Home | Serviq shop | Frame Details | Checkout | Payment Methods` → `GET /admin/payments` (payment methods)
- `Card` / `Cart Item` → shop product display components

---

## 15. Social Media Assets

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/admin/assets/upload` | JWT | Upload logo/trash (image) |
| `DELETE` | `/admin/assets/{id}` | JWT | Delete asset |
| `GET` | `/admin/assets` | JWT | List all assets |

### Views Mapped
- `Social media assets | Logo filled tranparent` → `GET /admin/assets` (logo variants)
- `Social media assets | Flyers` → `POST /admin/assets/upload` (flyer upload)
- `Social media assets | ig Posts` → `POST /admin/assets/upload` (Instagram post generation)
- `Wrap` / `X` — asset editing UI components

---

## Appendix: How Views Relate to Each Other

```
┌─────────────────────────────────────────────────────────────────────┐
│  CUSTOMER FLOW (Mobile)                                             │
│                                                                     │
│  Home (Table 12) → Foods/Drinks → Cart → Payment                    │
│                    → Hail Waiter                                    │
│                    → Request Clean up                               │
│                    → Analytics                                      │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  ADMIN FLOW (Mobile + Desktop)                                      │
│                                                                     │
│  Onboarding (Sign Up → Business Info → Address → Payments)          │
│       ↓                                                             │
│  Dashboard                                                          │
│       ├── Analytics  → Charts, Revenue, Orders, Requests            │
│       ├── Table Management → Sections → Tables → History/Orders     │
│       ├── Menu Management → Categories → Food/Drink Items           │
│       ├── Staff Management → List → Details → Leaderboard           │
│       ├── Campaigns → Create/Edit/Delete                            │
│       └── Profile → Business Info, Address, Payment, Password       │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  DATA RELATIONSHIPS                                                 │
│                                                                     │
│  Admin (1) ──hasMany── Sections (many)                              │
│       │                    │──hasMany── Tables (many)                │
│       │                    └──hasMany── Orders (many)                │
│       │                    └──hasMany── Requests (many)             │
│       │                                                             │
│       ├──hasMany── DishCategories (many)                             │
│       │                    └──hasMany── Dishes (many)                │
│       ├──hasMany── BeverageCategories (many)                        │
│       │                    └──hasMany── Beverages (many)             │
│       ├──hasMany── Staff (many)                                      │
│       ├──hasMany── Campaigns (many)                                  │
│       ├──hasOne── AdminAddress                                       │
│       └──hasOne── AdminPayment                                       │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Auth Pattern

All `/admin` endpoints (except login/register) require:
```http
Cookie: token={jwt}
# OR
Authorization: Bearer {jwt}
```

Customer/public endpoints use `admin_id` URL param:
```http
GET /orders/analytics?admin_id={admin_uuid}
```

---

*Endpoints derived from QORA Main Figma file. Backend controllers should map 1:1 to these routes.*
