# QORA Backend — API Reference for Frontend Developers

> **Base URL:** `http://localhost:8000/api`
> **Auth:** All `/admin` routes require JWT — send `Cookie: token={jwt}` or `Authorization: Bearer {jwt}`

---

## Table of Contents

1. [Authentication](#1-authentication)
2. [Profile](#2-profile)
3. [Sections](#3-sections)
4. [Tables](#4-tables)
5. [Dish Categories](#5-dish-categories)
6. [Dishes](#6-dishes)
7. [Beverage Categories](#7-beverage-categories)
8. [Beverages](#8-beverages)
9. [Campaigns](#9-campaigns)
10. [Staff](#10-staff)
11. [Orders (Admin)](#11-orders-admin)
12. [Customer Requests](#12-customer-requests)
13. [QR Codes](#13-qr-codes)
14. [POS Requests](#14-pos-requests)
15. [Serviq Shop — Products](#15-serviq-shop--products)
16. [Serviq Shop — Orders & Checkout](#16-serviq-shop--orders--checkout)
17. [Analytics (Public)](#17-analytics-public)
18. [Assets (Social Media)](#18-assets-social-media)

---

## Standard Response Structures

### Success (list)
```json
{
  "data": [ ... ],
  "meta": { "total": 10, "page": 1 }
}
```

### Success (single object)
```json
{
  "data": { ... }
}
```

### Created (201 / 200)
```json
{
  "data": { ... }
}
```

### Validation Error (422)
```json
{
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Not Found (404)
```json
{
  "message": "Not found"
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthorized"
}
```

---

## 1. Authentication

### POST /admin/auth/login
**Auth:** None

**Request Body:**
```json
{
  "email": "admin@example.com",          (required) string
  "password": "password123"              (required) string
}
```

**Response 200:**
```json
{
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "email": "admin@example.com",
    "business_name": "My Restaurant",
    "business_type": "Restaurant and Hotel",
    "logo": null,
    "created_at": "2026-07-20T10:00:00.000000Z",
    "updated_at": "2026-07-20T10:00:00.000000Z"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```
> Sets `token` as httpOnly cookie. Frontend should also store token from response body.

**Response 422:**
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

---

### POST /admin/auth/register
**Auth:** None

**Request Body:**
```json
{
  "email": "admin@example.com",              (required) string | unique:admins,email | email format
  "password": "password123",                 (required) string | min:6
  "business_name": "My Restaurant",          (required) string
  "business_type": "Restaurant and Hotel",   (required) string | in: Restaurant, Hotel, Restaurant and Hotel
  "bank_name": "First Bank",                 (required) string
  "account_number": "1234567890",            (required) string
  "account_holder_name": "John Doe",         (required) string
  "address": "123 Main St",                  (required) string
  "country": "Nigeria",                      (required) string
  "state": "Lagos",                          (required) string
  "city": "Ikeja",                           (optional) string
  "country_code": "+234",                    (required) string
  "phone_number": "08123456789"             (required) string
}
```

**Response 201:** Same as login — returns `{data: {admin}, token}` + httpOnly cookie.

---

### POST /admin/auth/check-email
**Auth:** None

**Request Body:**
```json
{
  "email": "admin@example.com"    (required) string | email
}
```

**Response 200:**
```json
{ "available": true }
```
```json
{ "available": false }
```

**Response 422:**
```json
{ "message": "Validation failed", "errors": { "email": ["The email field must be a valid email address."] } }
```

---

### POST /admin/auth/logout
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "message": "Logged out successfully" }
```

---

## 2. Profile

### GET /admin/profile
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "email": "admin@example.com",
    "business_name": "My Restaurant",
    "business_type": "Restaurant and Hotel",
    "logo": "data:image/png;base64,iVBORw0KG...",
    "address": {
      "id": "...",
      "admin_id": "...",
      "address": "123 Main St",
      "country": "Nigeria",
      "state": "Lagos",
      "city": "Ikeja",
      "country_code": "+234",
      "phone_number": "08123456789",
      "created_at": "...",
      "updated_at": "..."
    },
    "payment": {
      "id": "...",
      "admin_id": "...",
      "bank_name": "First Bank",
      "account_number": "1234567890",
      "account_holder_name": "John Doe",
      "created_at": "...",
      "updated_at": "..."
    }
  }
}
```

---

### PUT /admin/profile
**Auth:** Required

**Request Body (all fields optional):**
```json
{
  "business_name": "New Name",             (optional) string
  "business_type": "Restaurant",           (optional) string | in: Restaurant, Hotel, Restaurant and Hotel
  "logo": "data:image/png;base64,...",     (optional) string (base64 image)
  "address": "456 New St",                 (optional) string
  "country": "Ghana",                      (optional) string
  "state": "Accra",                        (optional) string
  "city": "Accra Central",                 (optional) string
  "country_code": "+233",                  (optional) string
  "phone_number": "0244000000",            (optional) string
  "bank_name": "GCB Bank",                 (optional) string
  "account_number": "9876543210",          (optional) string
  "account_holder_name": "Jane Doe"        (optional) string
}
```

**Response 200:**
```json
{ "data": { /* updated admin object with loaded address & payment */ } }
```

---

### PUT /admin/profile/password
**Auth:** Required

**Request Body:**
```json
{
  "current_password": "oldpass123",    (required) string
  "new_password": "newpass456"         (required) string | min:6
}
```

**Response 200:**
```json
{ "data": { /* admin object */ } }
```

**Response 422:**
```json
{ "message": "Validation failed", "errors": { "current_password": ["The current password is incorrect."] } }
```

---

## 3. Sections

### GET /admin/sections
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "xxx",
      "admin_id": "yyy",
      "name": "Main Hall",
      "type": "restaurant",
      "icon": {"emoji": "🍽️", "label": "Restaurant"},
      "status": "active",
      "tables": [
        {
          "id": "ttt",
          "section_id": "xxx",
          "name": "Table 1",
          "status": "available",
          "capacity": 4
        }
      ],
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```
> Sections are returned with their related tables loaded.

---

### GET /admin/sections/{id}
**Auth:** Required

**Response 200:** Single section object (same shape as list item above).

**Response 404:**
```json
{ "message": "Not found" }
```

---

### POST /admin/sections
**Auth:** Required

**Request Body:**
```json
{
  "name": "VIP Lounge",                 (required) string
  "type": "lounge",                     (required) string
  "icon": {"emoji": "⭐", "label": "VIP"}  (required) object
}
```

**Response 201:**
```json
{
  "data": {
    "id": "new-uuid",
    "admin_id": "current-admin-uuid",
    "name": "VIP Lounge",
    "type": "lounge",
    "icon": {"emoji": "⭐", "label": "VIP"},
    "status": "active",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PUT /admin/sections/{id}
**Auth:** Required

**Request Body (all optional — only send fields to update):**
```json
{
  "name": "Updated Name",          (optional) string
  "type": "bar",                   (optional) string
  "icon": {"emoji": "🍸"},         (optional) object
  "status": "inactive"             (optional) string | in: active, inactive
}
```

**Response 200:**
```json
{ "data": { /* updated section */ } }
```

---

### DELETE /admin/sections/{id}
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "message": "Section deleted successfully" }
```

---

### PATCH /admin/sections/{id}/status
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "data": { /* section with updated status */ } }
```
> Toggles between `active` and `inactive`.

---

## 4. Tables

### GET /admin/sections/{sectionId}/tables
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "ttt",
      "section_id": "xxx",
      "admin_id": "yyy",
      "name": "Table 1",
      "status": "available",
      "capacity": 4,
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

---

### GET /admin/sections/{sectionId}/tables/{tableId}
**Auth:** Required

**Response 200:** Single table object (same shape).

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/sections/{sectionId}/tables
**Auth:** Required

**Request Body:**
```json
{
  "name": "Table 11",           (required) string | max:255
  "capacity": 6,                (optional) integer | min:1
  "status": "available"         (optional) string | in: available, occupied, reserved
}
```

**Response 201:**
```json
{
  "data": {
    "id": "new-uuid",
    "section_id": "xxx",
    "admin_id": "yyy",
    "name": "Table 11",
    "status": "available",
    "capacity": 6,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PUT /admin/sections/{sectionId}/tables/{tableId}
**Auth:** Required

**Request Body (all optional):**
```json
{
  "name": "Table 11",            (optional) string | max:255
  "status": "occupied",          (optional) string | in: available, occupied, reserved
  "capacity": 8                  (optional) integer | min:1
}
```

**Response 200:** `{ "data": { /* updated table */ } }`

---

### DELETE /admin/sections/{sectionId}/tables/{tableId}
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "message": "Table deleted successfully" }
```

**Response 422:**
```json
{ "message": "Cannot delete table with existing orders", "errors": { "table_id": ["Table has active orders."] } }
```

---

### GET /admin/tables
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "ttt",
      "section_id": "xxx",
      "admin_id": "yyy",
      "name": "Table 1",
      "status": "available",
      "capacity": 4,
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```
> Returns all tables across all sections for the admin.

---

### POST /admin/tables/{id}/status
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "data": { /* table with updated status */ } }
```
> Toggles table status.

---

## 5. Dish Categories

### GET /admin/food/categories
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "cat-uuid",
      "admin_id": "admin-uuid",
      "name": "Main Course",
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 5 }
}
```

---

### GET /admin/food/categories/{id}
**Auth:** Required

**Response 200:** Single category object.

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/food/categories
**Auth:** Required

**Request Body:**
```json
{
  "name": "Appetizers"    (required) string | max:255
}
```

**Response 201:**
```json
{
  "data": {
    "id": "new-uuid",
    "admin_id": "admin-uuid",
    "name": "Appetizers",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PUT /admin/food/categories/{id}
**Auth:** Required

**Request Body:**
```json
{
  "name": "Starters"    (required) string | max:255
}
```

**Response 200:** `{ "data": { /* updated category */ } }`

---

### DELETE /admin/food/categories/{id}
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "message": "Category deleted successfully" }
```

**Response 422:**
```json
{ "message": "Cannot delete category with existing dishes", "errors": { "id": ["Category has dishes assigned."] } }
```

---

## 6. Dishes

### GET /admin/food/dishes
**Auth:** Required

**Query Parameters:**
- `category_id` (optional, string UUID) — filter by dish category
- `search` (optional, string) — search by name or description

**Response 200:**
```json
{
  "data": [
    {
      "id": "dish-uuid",
      "admin_id": "admin-uuid",
      "category_id": "cat-uuid",
      "name": "Jollof Rice",
      "price": 3500.00,
      "description": "Spicy Nigerian jollof with chicken",
      "ingredients": "Rice, Tomatoes, Onions, Chicken",
      "image": "data:image/jpeg;base64,...",
      "category": {
        "id": "cat-uuid",
        "name": "Main Course"
      },
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 12 }
}
```

---

### GET /admin/food/dishes/{id}
**Auth:** Required

**Response 200:** Single dish object (same shape as list item above).

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/food/dishes/{category_id}
**Auth:** Required

**Note:** `category_id` is in the URL path.

**Request Body:**
```json
{
  "name": "Jollof Rice",                    (required) string | max:255
  "price": 3500.00,                         (required) numeric | min:0
  "description": "Spicy Nigerian jollof",   (optional) string
  "ingredients": "Rice, Tomatoes",         (optional) string (comma-separated)
  "image": "data:image/jpeg;base64,..."     (optional) string (base64 image)
}
```

**Response 201:**
```json
{
  "data": {
    "id": "new-uuid",
    "admin_id": "admin-uuid",
    "category_id": "cat-uuid",
    "name": "Jollof Rice",
    "price": 3500.00,
    "description": "Spicy Nigerian jollof",
    "ingredients": "Rice, Tomatoes, Onions, Chicken",
    "image": "data:image/jpeg;base64,...",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PUT /admin/food/dishes/{id}
**Auth:** Required

**Note:** No `category_id` in URL for updates (dish already has a category).

**Request Body (all optional):**
```json
{
  "name": "Updated Name",                   (optional) string | max:255
  "price": 4000.00,                         (optional) numeric | min:0
  "description": "Updated desc",            (optional) string
  "ingredients": "New ingredients",        (optional) string
  "image": "data:image/jpeg;base64,..."     (optional) string
}
```

**Response 200:** `{ "data": { /* updated dish */ } }`

---

### DELETE /admin/food/dishes/{id}
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "message": "Dish deleted successfully" }
```

---

## 7. Beverage Categories

*(Identical structure to Dish Categories)*

### GET /admin/drinks/categories
**Auth:** Required

**Response 200:**
```json
{
  "data": [
    {
      "id": "cat-uuid",
      "admin_id": "admin-uuid",
      "name": "Cocktails",
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 3 }
}
```

---

### GET /admin/drinks/categories/{id}
**Auth:** Required

**Response 200:** Single beverage category object.

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/drinks/categories
**Auth:** Required

**Request Body:**
```json
{
  "name": "Cocktails"    (required) string | max:255
}
```

**Response 201:**
```json
{
  "data": {
    "id": "new-uuid",
    "admin_id": "admin-uuid",
    "name": "Cocktails",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PUT /admin/drinks/categories/{id}
**Auth:** Required

**Request Body:**
```json
{
  "name": "Mocktails"    (required) string | max:255
}
```

**Response 200:** `{ "data": { /* updated category */ } }`

---

### DELETE /admin/drinks/categories/{id}
**Auth:** Required

**Response 200:**
```json
{ "message": "Category deleted successfully" }
```

**Response 422:**
```json
{ "message": "Cannot delete category with existing beverages", "errors": { "id": ["Category has beverages assigned."] } }
```

---

## 8. Beverages

### GET /admin/drinks
**Auth:** Required

**Query Parameters:**
- `category_id` (optional, string UUID) — filter by beverage category

**Response 200:**
```json
{
  "data": [
    {
      "id": "bev-uuid",
      "admin_id": "admin-uuid",
      "category_id": "cat-uuid",
      "name": "Mojito",
      "price": 2500.00,
      "description": "Fresh mint mojito",
      "ingredients": "Mint, Lime, Rum, Soda",
      "image": "data:image/jpeg;base64,...",
      "category": {
        "id": "cat-uuid",
        "name": "Cocktails"
      },
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 8 }
}
```

---

### GET /admin/drinks/{id}
**Auth:** Required

**Response 200:** Single beverage object (same shape as list item).

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/drinks/{category_id}
**Auth:** Required

**Request Body:**
```json
{
  "name": "Mojito",                         (required) string | max:255
  "price": 2500.00,                          (required) numeric | min:0
  "description": "Fresh mint mojito",        (optional) string
  "ingredients": "Mint, Lime, Rum, Soda",   (optional) string (comma-separated)
  "image": "data:image/jpeg;base64,..."      (optional) string (base64 image)
}
```

**Response 201:**
```json
{
  "data": {
    "id": "new-uuid",
    "admin_id": "admin-uuid",
    "category_id": "cat-uuid",
    "name": "Mojito",
    "price": 2500.00,
    "description": "Fresh mint mojito",
    "ingredients": "Mint, Lime, Rum, Soda",
    "image": "data:image/jpeg;base64,...",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PUT /admin/drinks/{id}
**Auth:** Required

**Request Body (all optional):**
```json
{
  "name": "Virgin Mojito",                  (optional) string | max:255
  "price": 2000.00,                          (optional) numeric | min:0
  "description": "No alcohol version",      (optional) string
  "ingredients": "Mint, Lime, Soda",       (optional) string
  "image": "data:image/jpeg;base64,..."      (optional) string
}
```

**Response 200:** `{ "data": { /* updated beverage */ } }`

---

### DELETE /admin/drinks/{id}
**Auth:** Required

**Response 200:**
```json
{ "message": "Beverage deleted successfully" }
```

---

## 9. Campaigns

### GET /admin/campaigns
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "camp-uuid",
      "admin_id": "admin-uuid",
      "name": "Summer Special",
      "audience": "all",
      "description": "Summer deals for everyone",
      "image": "data:image/jpeg;base64,...",
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 3 }
}
```

---

### GET /admin/campaigns/{id}
**Auth:** Required

**Response 200:** Single campaign object.

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/campaigns
**Auth:** Required

**Request Body (multipart/form-data or JSON):**
```json
{
  "name": "Summer Special",                                      (required) string | max:255
  "audience": "all",                                             (required) string | in: all, restaurant, hotel, lounge
  "description": "Summer deals for everyone",                    (optional) string
  "image": "data:image/jpeg;base64,..."                          (optional) image file (max 2048KB, types: jpeg, png, jpg, gif)
}
```
> `image` can be sent as base64 string in JSON body OR as a file upload with key `image`.

**Response 201:**
```json
{
  "data": {
    "id": "camp-uuid",
    "admin_id": "admin-uuid",
    "name": "Summer Special",
    "audience": "all",
    "description": "Summer deals for everyone",
    "image": "campaigns/abc123.jpg",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PUT /admin/campaigns/{id}
**Auth:** Required

**Request Body (all optional):**
```json
{
  "name": "Updated Campaign Name",       (optional) string | max:255
  "audience": "restaurant",              (optional) string | in: all, restaurant, hotel, lounge
  "description": "New description",      (optional) string
  "image": "data:image/jpeg;base64,..."  (optional) image file
}
```

**Response 200:** `{ "data": { /* updated campaign */ } }`

---

### DELETE /admin/campaigns/{id}
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "message": "Campaign deleted successfully" }
```

---

## 10. Staff

### GET /admin/staff
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "staff-uuid",
      "admin_id": "admin-uuid",
      "fname": "John",
      "lname": "Doe",
      "email": "john@example.com",
      "role": "waiter",
      "schedule": [
        {
          "day": "Monday",
          "start": "09:00",
          "end": "17:00"
        }
      ],
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 5 }
}
```

---

### GET /admin/staff/{id}
**Auth:** Required

**Response 200:** Single staff object.

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/staff
**Auth:** Required

**Request Body:**
```json
{
  "firstName": "John",                                        (required) string | max:255
  "lastName": "Doe",                                          (required) string | max:255
  "email": "john@example.com",                               (required) string | email | unique:staffs,email
  "role": "waiter",                                           (required) string
  "workingdays": [                                            (required) array
    {
      "day": "Monday",         (required) string
      "start": "09:00",        (required) string (HH:MM format)
      "end": "17:00"           (required) string (HH:MM format)
    }
  ]
}
```

**Response 201:**
```json
{
  "success": true,
  "data": {
    "id": "new-uuid",
    "admin_id": "admin-uuid",
    "fname": "John",
    "lname": "Doe",
    "email": "john@example.com",
    "role": "waiter",
    "schedule": [{"day": "Monday", "start": "09:00", "end": "17:00"}],
    "created_at": "...",
    "updated_at": "..."
  },
  "message": "Staff member added successfully"
}
```

---

### PUT /admin/staff/{id}
**Auth:** Required

**Request Body (all optional):**
```json
{
  "firstName": "Jane",                                        (optional) string | max:255
  "lastName": "Smith",                                        (optional) string | max:255
  "email": "jane@example.com",                               (optional) string | email | unique:staffs,email (excludes current ID)
  "role": "manager",                                          (optional) string
  "workingdays": [                                            (optional) array
    {
      "day": "Tuesday",
      "start": "10:00",
      "end": "18:00"
    }
  ]
}
```

**Response 200:** `{ "success": true, "data": { /* updated staff */ }, "message": "Staff updated" }`

---

### DELETE /admin/staff/{id}
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "success": true, "message": "Staff member deleted" }
```

---

### PATCH /admin/staff/{id}/role
**Auth:** Required

**Request Body:**
```json
{
  "role": "manager"    (required) string
}
```

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": "staff-uuid",
    "role": "manager",
    "fname": "John",
    "lname": "Doe"
  },
  "message": "Role updated successfully"
}
```

---

### GET /admin/staff/leaderboard
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "staff-uuid",
      "fname": "John",
      "lname": "Doe",
      "role": "waiter",
      "completed_orders": 45,
      "revenue": 125000.00,
      "rank": 1
    }
  ]
}
```
> Returns staff ranked by completed orders / revenue. Exact fields depend on staff performance data.

---

### POST /admin/staff/assign
**Auth:** Required

**Request Body:**
```json
{
  "staff_id": "staff-uuid",    (required) string | exists:staffs,id
  "order_id": "order-uuid"     (required) string | exists:orders,id
}
```

**Response 201:**
```json
{
  "success": true,
  "data": {
    "id": "assignment-uuid",
    "staff_id": "staff-uuid",
    "order_id": "order-uuid",
    "created_at": "...",
    "updated_at": "..."
  },
  "message": "Staff assigned to order successfully"
}
```

---

### GET /admin/staff/assignments
**Auth:** Required

**Query Parameters:**
- `table_id` (optional, string UUID) — filter by table
- `staff_id` (optional, string UUID) — filter by staff

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "assgn-uuid",
      "staff_id": "staff-uuid",
      "order_id": "order-uuid",
      "staff": { "id": "staff-uuid", "fname": "John", "lname": "Doe", "role": "waiter" },
      "order": { "id": "order-uuid", "status": "confirmed", "total": 5000.00 },
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

---

## 11. Orders (Admin)

### GET /admin/orders
**Auth:** Required

**Query Parameters:**
- `status` (optional, string) — `pending`, `confirmed`, `cooking`, `served`, `completed`, `cancelled`
- `table_id` (optional, string UUID)
- `staff_id` (optional, string UUID)
- `date` (optional, string) — `YYYY-MM-DD`

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "order-uuid",
      "admin_id": "admin-uuid",
      "table_id": "table-uuid",
      "table_name": "Table 5",
      "staff_id": "staff-uuid",
      "staff_name": "John Doe",
      "order": [
        {
          "dish_id": "xxx",
          "name": "Jollof Rice",
          "price": 3500.00,
          "qty": 2
        }
      ],
      "status": "confirmed",
      "total": 7000.00,
      "order_time": "2026-07-20 14:30:00",
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 15 }
}
```

---

### GET /admin/orders/{id}
**Auth:** Required

**Response 200:** Single order object (same shape as list item above).

**Response 404:** `{ "message": "Not found" }`

---

### PATCH /admin/orders/{id}/status
**Auth:** Required

**Request Body:**
```json
{
  "status": "cooking"    (required) string | in: pending, confirmed, cooking, served, completed, cancelled
}
```

**Response 200:**
```json
{
  "data": {
    "id": "order-uuid",
    "status": "cooking",
    "order": [ /* ... */ ],
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PATCH /admin/orders/{id}/time
**Auth:** Required

**Request Body:**
```json
{
  "order_time": "2026-07-20 14:30:00"    (required) string | date_format: Y-m-d H:i:s
}
```

**Response 200:**
```json
{
  "data": {
    "id": "order-uuid",
    "order_time": "2026-07-20 14:30:00",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### GET /admin/orders/today
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": {
    "today_count": 12,
    "today_date": "2026-07-20"
  }
}
```

---

## 12. Customer Requests

### GET /admin/requests
**Auth:** Required

**Query Parameters:**
- `type` (optional, string) — `waiter`, `cleanup`, `other`
- `table_id` (optional, string UUID)

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "req-uuid",
      "admin_id": "admin-uuid",
      "table_id": "table-uuid",
      "type": "waiter",
      "status": "pending",
      "note": "Customer needs water",
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 4 }
}
```

---

### GET /admin/requests/{id}
**Auth:** Required

**Response 200:** Single request object.

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/requests
**Auth:** Required

**Request Body:**
```json
{
  "table_id": "table-uuid",    (optional) string | exists:tables,id (null for general requests)
  "type": "waiter",            (required) string | in: waiter, cleanup, other
  "note": "Customer needs water"  (optional) string
}
```

**Response 201:**
```json
{
  "data": {
    "id": "req-uuid",
    "admin_id": "admin-uuid",
    "table_id": "table-uuid",
    "type": "waiter",
    "status": "pending",
    "note": "Customer needs water",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PATCH /admin/requests/{id}/status
**Auth:** Required

**Request Body:**
```json
{
  "status": "handled"    (required) string | in: pending, handled, cancelled
}
```

**Response 200:**
```json
{
  "data": {
    "id": "req-uuid",
    "id": "req-uuid",
    "type": "waiter",
    "status": "handled",
    "note": "Customer needs water",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### DELETE /admin/requests/{id}
**Auth:** Required

**Response 200:**
```json
{ "message": "Request deleted successfully" }
```

---

## 13. QR Codes

### GET /admin/qr-codes
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "qr-uuid",
      "admin_id": "admin-uuid",
      "section_id": "section-uuid",
      "table_id": "table-uuid",
      "code": "QORA-A1B2C3",
      "path": "qr-codes/qr-A1B2C3.png",
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```
> `section_id` and `table_id` are mutually exclusive — one will be null.

---

### GET /admin/qr-codes/{id}
**Auth:** Required

**Response 200:** Single QR code object.

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/qr-codes
**Auth:** Required

**Request Body:**
```json
{
  "section_id": "section-uuid",    (optional) string | exists:sections,id (required if table_id not provided)
  "table_id": "table-uuid"         (optional) string | exists:tables,id (required if section_id not provided)
}
```
> At least one of `section_id` or `table_id` is required. Cannot create duplicate QR for same section.

**Response 201:**
```json
{
  "data": {
    "id": "qr-uuid",
    "admin_id": "admin-uuid",
    "section_id": "section-uuid",
    "table_id": null,
    "code": "QORA-XYZ123",
    "path": "qr-codes/qr-XYZ123.png",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### DELETE /admin/qr-codes/{id}
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "message": "QR code deleted successfully" }
```

---

### GET /admin/qr-codes/{id}/download
**Auth:** Required

**Response 200:**
> Returns the QR code image file (PNG) with appropriate `Content-Type: image/png` header.

**Response 404:** `{ "message": "QR code not found" }`

---

## 14. POS Requests

### GET /admin/pos-requests
**Auth:** Required

**Query Parameters:**
- `status` (optional, string) — `pending`, `approved`, `rejected`, `cancelled`

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "pos-uuid",
      "admin_id": "admin-uuid",
      "order_id": "order-uuid",
      "amount": 15000.00,
      "status": "pending",
      "note": "Customer wants POS payment",
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

---

### POST /admin/pos-requests
**Auth:** Required

**Request Body:**
```json
{
  "order_id": "order-uuid",     (required) string | exists:orders,id
  "amount": 15000.00,           (required) numeric | min:0
  "note": "Customer wants POS" (optional) string
}
```

**Response 201:**
```json
{
  "data": {
    "id": "pos-uuid",
    "admin_id": "admin-uuid",
    "order_id": "order-uuid",
    "amount": 15000.00,
    "status": "pending",
    "note": "Customer wants POS",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PATCH /admin/pos-requests/{id}/status
**Auth:** Required

**Request Body:**
```json
{
  "status": "approved"    (required) string | in: pending, approved, rejected, cancelled
}
```

**Response 200:**
```json
{
  "data": {
    "id": "pos-uuid",
    "status": "approved",
    "amount": 15000.00,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### DELETE /admin/pos-requests/{id}
**Auth:** Required

**Response 200:**
```json
{ "message": "POS request cancelled successfully" }
```

---

## 15. Serviq Shop — Products

### GET /admin/serviq/products
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "prod-uuid",
      "admin_id": "admin-uuid",
      "name": "Handwoven Basket",
      "description": "Beautiful artisan basket",
      "price": 8000.00,
      "image": "data:image/jpeg;base64,...",
      "stock": 25,
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 20 }
}
```

---

### GET /admin/serviq/products/{id}
**Auth:** Required

**Response 200:** Single product object.

**Response 404:** `{ "message": "Not found" }`

---

### POST /admin/serviq/products
**Auth:** Required

**Request Body:**
```json
{
  "name": "Handwoven Basket",                          (required) string | max:255
  "description": "Beautiful artisan basket",           (optional) string
  "price": 8000.00,                                    (required) numeric | min:0
  "image": "data:image/jpeg;base64,...",               (optional) string (base64 image)
  "stock": 25                                          (optional) integer | min:0
}
```

**Response 201:**
```json
{
  "data": {
    "id": "prod-uuid",
    "admin_id": "admin-uuid",
    "name": "Handwoven Basket",
    "description": "Beautiful artisan basket",
    "price": 8000.00,
    "image": "data:image/jpeg;base64,...",
    "stock": 25,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### PUT /admin/serviq/products/{id}
**Auth:** Required

**Request Body (all optional):**
```json
{
  "name": "Updated Product Name",      (optional) string | max:255
  "description": "Updated desc",       (optional) string
  "price": 7500.00,                    (optional) numeric | min:0
  "image": "data:image/jpeg;base64,...", (optional) string
  "stock": 20                          (optional) integer | min:0
}
```

**Response 200:** `{ "data": { /* updated product */ } }`

---

### DELETE /admin/serviq/products/{id}
**Auth:** Required

**Response 200:**
```json
{ "message": "Product deleted successfully" }
```

---

## 16. Serviq Shop — Orders & Checkout

### POST /admin/serviq/checkout
**Auth:** Required

**Request Body:**
```json
{
  "items": [                                      (required) array
    {
      "product_id": "prod-uuid",    (required) string | exists:shop_products,id
      "quantity": 2                 (required) integer | min:1
    }
  ],
  "payment_method": "card"                        (optional) string | in: cash, card, transfer
}
```
> On checkout: verifies stock, deducts quantities, computes total, creates order with `pending` status.

**Response 201:**
```json
{
  "data": {
    "id": "shop-order-uuid",
    "admin_id": "admin-uuid",
    "user_id": "user-uuid",
    "items": [
      { "product_id": "prod-uuid", "quantity": 2, "price": 8000.00, "subtotal": 16000.00 }
    ],
    "total": 16000.00,
    "status": "pending",
    "payment_method": "card",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

**Response 422 (insufficient stock):**
```json
{
  "message": "Validation failed",
  "errors": {
    "items.0.product_id": ["Insufficient stock. Only 1 left."]
  }
}
```

---

### GET /admin/serviq/orders
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "shop-order-uuid",
      "admin_id": "admin-uuid",
      "user_id": "user-uuid",
      "items": [ /* ... */ ],
      "total": 16000.00,
      "status": "pending",
      "payment_method": "card",
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": { "total": 5 }
}
```

---

### GET /admin/serviq/orders/{id}
**Auth:** Required

**Response 200:** Single shop order object.

**Response 404:** `{ "message": "Not found" }`

---

## 17. Analytics (Public)

> These endpoints have no auth. Customer-facing data via `admin_id` parameter.

### GET /orders/analytics
**Auth:** Public

**Query Parameters:**
- `table_id` (required for table analytics, string UUID)
- `admin_id` (optional, string UUID)

**Response 200:**
```json
{
  "data": {
    "table_id": "table-uuid",
    "table_name": "Table 5",
    "total_orders": 15,
    "total_spent": 45000.00,
    "orders": [
      {
        "id": "order-uuid",
        "status": "completed",
        "total": 5000.00,
        "created_at": "2026-07-20 14:30:00"
      }
    ]
  }
}
```

---

### GET /orders/analytics/room
**Auth:** Public

**Query Parameters:**
- `admin_id` (required, string UUID)

**Response 200:**
```json
{
  "data": {
    "admin_id": "admin-uuid",
    "business_name": "Grand Hotel",
    "total_room_orders": 42,
    "orders": [
      {
        "id": "order-uuid",
        "room": "Room 205",
        "table_name": null,
        "status": "served",
        "total": 12000.00,
        "created_at": "2026-07-20 12:00:00"
      }
    ]
  }
}
```

**Response 422:**
```json
{ "message": "Validation failed", "errors": { "admin_id": ["The admin_id field is required."] } }
```

---

### GET /orders/history
**Auth:** Public

**Query Parameters:**
- `admin_id` (optional, string UUID)
- `filter` (optional, string) — default: `all`. Options: `date_range`, `popular`, `revenue`
- `start_date` (optional, string) — `YYYY-MM-DD` (for `date_range` filter)
- `end_date` (optional, string) — `YYYY-MM-DD` (for `date_range` filter)

**Response 200:**
```json
{
  "data": [
    {
      "id": "order-uuid",
      "table_name": "Table 5",
      "staff_name": "John Doe",
      "items": [ "Jollof Rice x2", "Coke x1" ],
      "total": 7500.00,
      "status": "completed",
      "created_at": "2026-07-20 14:30:00"
    }
  ],
  "meta": {
    "total": 50,
    "per_page": 20,
    "current_page": 1,
    "last_page": 3
  }
}
```

---

## 18. Assets (Social Media)

### GET /admin/assets
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{
  "data": [
    {
      "id": "asset-uuid",
      "admin_id": "admin-uuid",
      "type": "logo",
      "path": "logos/store-logo.png",
      "mime_type": "image/png",
      "size": 245760,
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```
> `type` values: `logo`, `flyer`, `ig_post`

---

### POST /admin/assets/upload
**Auth:** Required

**Request Body:**
```json
{
  "type": "logo",                                        (required) string | in: logo, flyer, ig_post
  "image": "data:image/png;base64,iVBORw0KG..."         (required) string (base64-encoded image)
}
```
> Image is decoded and stored on the `logos/`, `flyers/`, or `ig_posts/` folder based on type. Supported: JPEG, PNG, GIF.

**Response 201:**
```json
{
  "data": {
    "id": "asset-uuid",
    "admin_id": "admin-uuid",
    "type": "logo",
    "path": "logos/store-logo.png",
    "mime_type": "image/png",
    "size": 245760,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

**Response 422:**
```json
{ "message": "Validation failed", "errors": { "type": ["The type must be logo, flyer, or ig_post."] } }
```

---

### DELETE /admin/assets/{id}
**Auth:** Required

**Request Body:** None

**Response 200:**
```json
{ "message": "Asset deleted successfully" }
```

---

## Quick Reference: Auth Header

All protected requests must include one of:
```http
Cookie: token=<jwt_token>
```
OR
```http
Authorization: Bearer <jwt_token>
```

> Cookie is primary; `Authorization` header is the fallback.

---

## Common Error Responses

| Code | Body |
|------|------|
| 401 | `{ "message": "Unauthorized" }` |
| 401 | `{ "message": "Unauthorized - Invalid token" }` |
| 401 | `{ "message": "Unauthorized - No token" }` |
| 404 | `{ "message": "Not found" }` |
| 422 | `{ "message": "Validation failed", "errors": { "field": ["msg"] } }` |
| 500 | `{ "message": "Server Error" }` |

---

*Generated: 2026-07-20 | Base: http://localhost:8000/api*
