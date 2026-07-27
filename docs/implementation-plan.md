# QORA Backend — Implementation Plan
**Generated:** 2026-07-20 | **Status:** ✅ COMPLETE

---

## Implementation Status: ALL PHASES DONE

| Phase | Status | Notes |
|-------|--------|-------|
| Phase 0 – Quick Fixes | ✅ Done | Campaign edit/show/list, Staff show/destroy, Dish/Bev CRUD, Auth changePassword |
| Phase 1 – Missing Controller Methods | ✅ Done | All CRUD methods for dishes, beverages, staff, campaigns, orders (admin-scoped) |
| Phase 2 – Migration Edit | ✅ Done | `sections` table — added `status` column |
| Phase 3 – New Migrations (7) | ✅ Done | `tables`, `requests`, `qr_codes`, `pos_requests`, `shop_products`, `shop_orders`, `assets` |
| Phase 4 – New Models (7) | ✅ Done | `Table`, `CustomerRequest`, `QrCode`, `PosRequest`, `ShopProduct`, `ShopOrder`, `Asset` |
| Phase 5 – New Controllers (8) | ✅ Done | `TableController`, `RequestController`, `QrCodeController`, `PosRequestController`, `ShopProductController`, `ShopOrderController`, `AnalyticsController`, `AssetController` |
| Phase 6 – Routes Wiring | ✅ Done | 93 routes registered in `routes/api.php` |
| Phase 7 – Style Check | ✅ Done | Laravel Pint passes clean |

---

## Final Route Count: 93

### Admin Auth (3)
- `POST /admin/auth/login`
- `POST /admin/auth/register`
- `POST /admin/auth/check-email`

### Admin Public (4)
- `GET /admin/health`
- `POST /admin/auth/logout`
- `GET /admin/profile`
- `PUT /admin/profile`
- `PUT /admin/profile/password`

### Sections (6)
- `GET /admin/sections`
- `GET /admin/sections/{id}`
- `POST /admin/sections`
- `PUT /admin/sections/{id}`
- `DELETE /admin/sections/{id}`
- `PATCH /admin/sections/{id}/status`

### Tables (10)
- `GET /admin/sections/{sectionId}/tables`
- `GET /admin/sections/{sectionId}/tables/{tableId}`
- `POST /admin/sections/{sectionId}/tables`
- `PUT /admin/sections/{sectionId}/tables/{tableId}`
- `DELETE /admin/sections/{sectionId}/tables/{tableId}`
- `GET /admin/tables`
- `PATCH /admin/tables/{id}/status`

### Dish Categories (5)
- `GET /admin/food/categories`
- `GET /admin/food/categories/{id}`
- `POST /admin/food/categories`
- `PUT /admin/food/categories/{id}`
- `DELETE /admin/food/categories/{id}`

### Dishes (5)
- `GET /admin/food/dishes`
- `GET /admin/food/dishes/{id}`
- `POST /admin/food/dishes/{category_id}`
- `PUT /admin/food/dishes/{id}`
- `DELETE /admin/food/dishes/{id}`

### Beverage Categories (5)
- `GET /admin/drinks/categories`
- `GET /admin/drinks/categories/{id}`
- `POST /admin/drinks/categories`
- `PUT /admin/drinks/categories/{id}`
- `DELETE /admin/drinks/categories/{id}`

### Beverages (5)
- `GET /admin/drinks`
- `GET /admin/drinks/{id}`
- `POST /admin/drinks/{category_id}`
- `PUT /admin/drinks/{id}`
- `DELETE /admin/drinks/{id}`

### Campaigns (5)
- `GET /admin/campaigns`
- `GET /admin/campaigns/{id}`
- `POST /admin/campaigns`
- `PUT /admin/campaigns/{id}`
- `DELETE /admin/campaigns/{id}`

### Staff (8)
- `GET /admin/staff`
- `GET /admin/staff/{id}`
- `POST /admin/staff`
- `PUT /admin/staff/{id}`
- `DELETE /admin/staff/{id}`
- `PATCH /admin/staff/{id}/role`
- `GET /admin/staff/leaderboard`
- `POST /admin/staff/assign`
- `GET /admin/staff/assignments`

### Orders Admin (5)
- `GET /admin/orders`
- `GET /admin/orders/{id}`
- `PATCH /admin/orders/{id}/status`
- `PATCH /admin/orders/{id}/time`
- `GET /admin/orders/today`

### Customer Requests (6)
- `GET /admin/requests`
- `GET /admin/requests/{id}`
- `POST /admin/requests`
- `DELETE /admin/requests/{id}`
- `PATCH /admin/requests/{id}/status`

### QR Codes (5)
- `GET /admin/qr-codes`
- `POST /admin/qr-codes`
- `GET /admin/qr-codes/{id}`
- `DELETE /admin/qr-codes/{id}`
- `GET /admin/qr-codes/{id}/download`

### POS Requests (4)
- `GET /admin/pos-requests`
- `POST /admin/pos-requests`
- `DELETE /admin/pos-requests/{id}`
- `PATCH /admin/pos-requests/{id}/status`

### Serviq Shop (7)
- `GET /admin/serviq/products`
- `GET /admin/serviq/products/{id}`
- `POST /admin/serviq/products`
- `PUT /admin/serviq/products/{id}`
- `DELETE /admin/serviq/products/{id}`
- `POST /admin/serviq/checkout`
- `GET /admin/serviq/orders`
- `GET /admin/serviq/orders/{id}`

### Customer Analytics (3) — Public
- `GET /orders/analytics`
- `GET /orders/analytics/room`
- `GET /orders/history`

### Social Media Assets (3)
- `GET /admin/assets`
- `POST /admin/assets/upload`
- `DELETE /admin/assets/{id}`

### Public Orders (5)
- `GET /orders/{admin_id}`
- `POST /orders/{admin_id}`
- `GET /orders/{admin_id}/{id}`
- `PUT /orders/{admin_id}/{id}`
- `DELETE /orders/{admin_id}/{id}`

---

## Files Created / Modified Summary

### Controllers (6 modified, 8 new)
**Modified:** `SectionsController`, `AuthController`, `CampaignController`, `StaffController`, `DishController`, `BeverageCategoryController`, `DishCategoryController`, `BeverageController`, `OrderController`
**New:** `TableController`, `RequestController`, `QrCodeController`, `PosRequestController`, `ShopProductController`, `ShopOrderController`, `AnalyticsController`, `AssetController`

### Models (1 modified, 7 new)
**Modified:** `Section` (added `status`)
**New:** `Table`, `CustomerRequest`, `QrCode`, `PosRequest`, `ShopProduct`, `ShopOrder`, `Asset`

### Migrations (1 modified, 7 new)
**Modified:** `2026_05_06_103528_create_sections_table.php` (added `status` column), `2026_06_12_000000_create_orders_table.php` (status enum + order_time)
**New:** `2026_07_20_000001` through `2026_07_20_000007`

### Routes
`routes/api.php` — complete rewrite with all 93 routes

---

*All phases complete. Run `php artisan migrate` to create new tables, then test endpoints.*
