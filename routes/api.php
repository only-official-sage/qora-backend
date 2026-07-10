<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CampaignController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\BeverageController;
use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\BeverageCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;

Route::prefix('admin')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:60,1');
    Route::post('/auth/check-email', [AuthController::class, 'checkEmail']);
    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'service' => 'qora-backend']);
    });

    Route::group(['middleware' => function ($request, $next) {
            $token = $request->cookie('token');
            if (!$token) {
                // Fallback to Authorization header for backward compatibility
                $token = $request->bearerToken();
            }
            if (!$token) {
                return response()->json(['message' => 'Unauthorized - No token'], 401);
            }
            $jwt = new \App\Services\JwtService();
            $admin = $jwt->validate($token);
            if (!$admin) {
                return response()->json(['message' => 'Unauthorized - Invalid token'], 401);
            }
            $request->setUserResolver(fn() => $admin);
            return $next($request);
        }
    ], function () {

        // Profile
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);

        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // Section
        Route::get('/sections', [SectionsController::class, 'index']);
        Route::post('/sections', [SectionsController::class, 'store']);

        // Beverage Categories
        Route::post('/categories/beverage', [BeverageCategoryController::class, 'store']);
        // Beverages
        Route::post('/categories/beverage/{category_id}', [BeverageController::class, 'store']);
        Route::get('/beverages', [BeverageController::class, 'index']);


        // Dish Categories
        Route::post('/categories/dish', [DishCategoryController::class, 'store']);
        // Dishes
        Route::post('/categories/dish/{category_id}', [DishController::class, 'store']);
        Route::get('/dishes', [DishController::class, 'index']);

        // Campaigns
        Route::post('/campaigns', [CampaignController::class, 'create']);
        Route::put('/campaigns/{id}', [CampaignController::class, 'edit']);
        Route::delete('/campaigns/{id}', [CampaignController::class, 'destroy']);

        // Staffs
        Route::post('/staffs', [StaffController::class, 'store']);
        Route::get('/staffs', [StaffController::class, 'index']);
        Route::put('/staffs/{id}', [StaffController::class, 'update']);
        Route::post('/staff/order/{id}');

        });
});
        


// Orders
Route::prefix('orders')->group(function() {
    Route::get('/{admin_id}', [OrderController::class, 'index']);
    Route::post('/{admin_id}', [OrderController::class, 'store']);
    Route::get('/{admin_id}/{id}', [OrderController::class, 'show']);
    Route::put('/{admin_id}/{id}', [OrderController::class, 'update']);
    Route::delete('/{admin_id}/{id}', [OrderController::class, 'destroy']);
});