<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CampaignController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BeverageCategoryController;
use App\Http\Controllers\BeverageController;
use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosRequestController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\ShopOrderController;
use App\Http\Controllers\ShopProductController;
use App\Http\Controllers\TableController;
use App\Services\JwtService;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    // Public
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:60,1');
    Route::post('/auth/check-email', [AuthController::class, 'checkEmail']);
    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'service' => 'qora-backend']);
    });

    Route::group(['middleware' => function ($request, $next) {
        $token = $request->cookie('token');
        if (! $token) {
            $token = $request->bearerToken();
        }
        if (! $token) {
            return response()->json(['message' => 'Unauthorized - No token'], 401);
        }
        $jwt = new JwtService;
        $admin = $jwt->validate($token);
        if (! $admin) {
            return response()->json(['message' => 'Unauthorized - Invalid token'], 401);
        }
        $request->setUserResolver(fn () => $admin);

        return $next($request);
    }], function () {
// Route::middleware('jwt')->group(function () {
        // Profile
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/profile/password', [AuthController::class, 'changePassword']);

        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // Sections
        Route::get('/sections', [SectionsController::class, 'index']);
        Route::get('/sections/{id}', [SectionsController::class, 'show']);
        Route::post('/sections', [SectionsController::class, 'store']);
        Route::put('/sections/{id}', [SectionsController::class, 'update']);
        Route::delete('/sections/{id}', [SectionsController::class, 'destroy']);
        Route::patch('/sections/{id}/status', [SectionsController::class, 'toggleStatus']);

        // Tables
        Route::get('/sections/{sectionId}/tables', [TableController::class, 'index']);
        Route::get('/sections/{sectionId}/tables/{tableId}', [TableController::class, 'show']);
        Route::post('/sections/{sectionId}/tables', [TableController::class, 'store']);
        Route::put('/sections/{sectionId}/tables/{tableId}', [TableController::class, 'update']);
        Route::delete('/sections/{sectionId}/tables/{tableId}', [TableController::class, 'destroy']);
        Route::get('/tables', [TableController::class, 'index']);
        Route::patch('/tables/{id}/status', [TableController::class, 'toggleStatus']);

        // Dish Categories
        Route::get('/food/categories', [DishCategoryController::class, 'index']);
        Route::get('/food/categories/{id}', [DishCategoryController::class, 'show']);
        Route::post('/food/categories', [DishCategoryController::class, 'store']);
        Route::put('/food/categories/{id}', [DishCategoryController::class, 'update']);
        Route::delete('/food/categories/{id}', [DishCategoryController::class, 'destroy']);

        // Dishes
        Route::get('/food/dishes', [DishController::class, 'index']);
        Route::get('/food/dishes/{id}', [DishController::class, 'show']);
        Route::post('/food/dishes/{category_id}', [DishController::class, 'store']);
        Route::put('/food/dishes/{id}', [DishController::class, 'update']);
        Route::delete('/food/dishes/{id}', [DishController::class, 'destroy']);

        // Beverage Categories
        Route::get('/drinks/categories', [BeverageCategoryController::class, 'index']);
        Route::get('/drinks/categories/{id}', [BeverageCategoryController::class, 'show']);
        Route::post('/drinks/categories', [BeverageCategoryController::class, 'store']);
        Route::put('/drinks/categories/{id}', [BeverageCategoryController::class, 'update']);
        Route::delete('/drinks/categories/{id}', [BeverageCategoryController::class, 'destroy']);

        // Beverages
        Route::get('/drinks', [BeverageController::class, 'index']);
        Route::get('/drinks/{id}', [BeverageController::class, 'show']);
        Route::post('/drinks/{category_id}', [BeverageController::class, 'store']);
        Route::put('/drinks/{id}', [BeverageController::class, 'update']);
        Route::delete('/drinks/{id}', [BeverageController::class, 'destroy']);

        // Campaigns
        Route::get('/campaigns', [CampaignController::class, 'index']);
        Route::get('/campaigns/{id}', [CampaignController::class, 'show']);
        Route::post('/campaigns', [CampaignController::class, 'create']);
        Route::put('/campaigns/{id}', [CampaignController::class, 'edit']);
        Route::delete('/campaigns/{id}', [CampaignController::class, 'destroy']);

        // Staff
        Route::get('/staff', [StaffController::class, 'index']);
        Route::get('/staff/{id}', [StaffController::class, 'show']);
        Route::post('/staff', [StaffController::class, 'store']);
        Route::put('/staff/{id}', [StaffController::class, 'update']);
        Route::delete('/staff/{id}', [StaffController::class, 'destroy']);
        Route::patch('/staff/{id}/role', [StaffController::class, 'changeRole']);
        Route::get('/staff/leaderboard', [StaffController::class, 'leaderboard']);
        Route::post('/staff/assign', [StaffController::class, 'assign']);
        Route::get('/staff/assignments', [StaffController::class, 'assignments']);

        // Orders (admin)
        Route::get('/orders', [OrderController::class, 'adminIndex']);
        Route::get('/orders/today', [OrderController::class, 'todayCount']);
        Route::get('/orders/{id}', [OrderController::class, 'adminShow']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::patch('/orders/{id}/time', [OrderController::class, 'updateOrderTime']);

        // Customer Requests
        Route::get('/requests', [RequestController::class, 'index']);
        Route::get('/requests/{id}', [RequestController::class, 'show']);
        Route::post('/requests', [RequestController::class, 'store']);
        Route::patch('/requests/{id}/status', [RequestController::class, 'updateStatus']);
        Route::delete('/requests/{id}', [RequestController::class, 'destroy']);

        // QR Codes
        Route::get('/qr-codes', [QrCodeController::class, 'index']);
        Route::post('/qr-codes', [QrCodeController::class, 'store']);
        Route::get('/qr-codes/{id}', [QrCodeController::class, 'show']);
        Route::delete('/qr-codes/{id}', [QrCodeController::class, 'destroy']);
        Route::get('/qr-codes/{id}/download', [QrCodeController::class, 'download']);

        // POS Requests
        Route::get('/pos-requests', [PosRequestController::class, 'index']);
        Route::post('/pos-requests', [PosRequestController::class, 'store']);
        Route::patch('/pos-requests/{id}/status', [PosRequestController::class, 'updateStatus']);
        Route::delete('/pos-requests/{id}', [PosRequestController::class, 'destroy']);

        // Serviq Shop - Products
        Route::get('/serviq/products', [ShopProductController::class, 'index']);
        Route::get('/serviq/products/{id}', [ShopProductController::class, 'show']);
        Route::post('/serviq/products', [ShopProductController::class, 'store']);
        Route::put('/serviq/products/{id}', [ShopProductController::class, 'update']);
        Route::delete('/serviq/products/{id}', [ShopProductController::class, 'destroy']);

        // Serviq Shop - Orders & Checkout
        Route::get('/serviq/orders', [ShopOrderController::class, 'index']);
        Route::get('/serviq/orders/{id}', [ShopOrderController::class, 'show']);
        Route::post('/serviq/checkout', [ShopOrderController::class, 'checkout']);

        // Customer Analytics (public, no auth)
        Route::prefix('orders')->group(function () {
            Route::get('/analytics/{tableId}', [AnalyticsController::class, 'tableAnalytics']);
            Route::get('/analytics/room', [AnalyticsController::class, 'roomAnalytics']);
            Route::get('/history', [AnalyticsController::class, 'orderHistory']);
        });

        // Social Media Assets
        Route::get('/assets', [AssetController::class, 'index']);
        Route::post('/assets/upload', [AssetController::class, 'store']);
        Route::delete('/assets/{id}', [AssetController::class, 'destroy']);
    });
});

// Public customer-facing order endpoints
Route::prefix('orders')->group(function () {
    Route::get('/{admin_id}', [OrderController::class, 'index']);
    Route::post('/{admin_id}', [OrderController::class, 'store']);
    Route::get('/{admin_id}/{id}', [OrderController::class, 'show']);
    Route::put('/{admin_id}/{id}', [OrderController::class, 'update']);
    Route::delete('/{admin_id}/{id}', [OrderController::class, 'destroy']);
});

// Public customer-facing requests endpoints
Route::prefix('requests')->group(function () {
    Route::post('/{admin_id}', [\App\Http\Controllers\RequestController::class, 'publicStore']);
});
