<?php

use App\Http\Controllers\Api\v1\Auth\PasswordController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CustomerRegistration;
use App\Http\Controllers\API\v1\DeliveryBoyAssignController;
use App\Http\Controllers\Api\v1\DeliveryPartner_login;
use App\Http\Controllers\Api\v1\DeliveryPartnerController;
use App\Http\Controllers\Api\v1\DeliveryZoneController;
use App\Http\Controllers\Api\v1\NearestRestaurantController;
// Controllers
use App\Http\Controllers\API\v1\OrderController;
use App\Http\Controllers\API\v1\PaymentController;
use App\Http\Controllers\Api\v1\ReviewController;
use App\Http\Controllers\Api\v1\SearchRestaurantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --------------------------------------------------
// Default Sanctum Route
// --------------------------------------------------
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --------------------------------------------------
// API v1 Routes
// --------------------------------------------------
Route::prefix('v1')->group(function () {

    // ----------------------------------------------
    // Test Route
    // ----------------------------------------------
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'HungerHop API is working!',
            'version' => '1.0.0',
            'timestamp' => now(),
            'laravel_version' => app()->version(),
        ]);
    });

    // ----------------------------------------------
    // Public Restaurant & Menu Routes
    // ----------------------------------------------
    Route::prefix('restaurant')->middleware('auth:api')->group(function () {
        Route::get('/', [NearestRestaurantController::class, 'list']);
        Route::get('/details', [NearestRestaurantController::class, 'details']);
        Route::get('/menu', [NearestRestaurantController::class, 'menuWithCategories']);
        Route::post('/menu', [NearestRestaurantController::class, 'menuWithCategories']);

        // Restaurant Reviews
        Route::get('/reviews', [ReviewController::class, 'getReviews'])->withoutMiddleware('auth:api'); // ?restaurant_id=1
        Route::post('/reviews', [ReviewController::class, 'addReview']);
    });

    // Menu Item Reviews
    Route::prefix('menu-item')->middleware('auth:api')->group(function () {
        Route::get('/reviews', [ReviewController::class, 'getMenuItemReviews'])->withoutMiddleware('auth:api'); // ?item_id=1
        Route::post('/reviews', [ReviewController::class, 'addMenuItemReview']);
    });

    // Restaurant Search
    Route::post('/search/restaurants', [SearchRestaurantController::class, 'index']);

    // ----------------------------------------------
    // Customer Authentication Routes
    // ----------------------------------------------
    Route::prefix('customer')->group(function () {
        // Public customer registration
        Route::post('/register', [CustomerRegistration::class, 'register']);
        Route::post('/add-address', [CustomerRegistration::class, 'addAddress'])->middleware('auth:api');
    });

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Protected customer routes
        Route::middleware('auth:api')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });
    });

    // ----------------------------------------------
    // Orders (Customer)
    // ----------------------------------------------
    Route::prefix('order')->middleware('auth:api')->group(function () {
        Route::post('/add', [OrderController::class, 'createOrder']);
        Route::post('/details', [OrderController::class, 'getOrderDetails']);
    });

    // ----------------------------------------------
    // Payments
    // ----------------------------------------------
    Route::prefix('payment')->middleware('auth:api')->group(function () {
        Route::post('/intent', [PaymentController::class, 'createIntent']);
        Route::post('/confirm', [PaymentController::class, 'confirm']);
        Route::post('/confirm-with-method', [PaymentController::class, 'confirmWithMethod']);
        Route::get('/history', [PaymentController::class, 'history']);
    });

    // ----------------------------------------------
    // Delivery Partner Routes
    // ----------------------------------------------
    Route::prefix('delivery-partner')->group(function () {
        // Public routes (no authentication required)
        Route::post('/login', [DeliveryPartner_login::class, 'login']);
        Route::post('/register', [DeliveryPartner_login::class, 'register']); // Delivery partner registration

        // Protected routes
        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [DeliveryPartner_login::class, 'logout']);
            Route::get('/assignments', [DeliveryPartnerController::class, 'myAssignments']);
            Route::post('/assignments', [DeliveryPartnerController::class, 'assignmentDetails']);
        });
    });

    // ----------------------------------------------
    // Delivery Boy Assignment
    // ----------------------------------------------
    Route::prefix('delivery-boy')->middleware('auth:api')->group(function () {
        Route::post('/assign', [DeliveryBoyAssignController::class, 'assign']);
        Route::post('/accept', [DeliveryBoyAssignController::class, 'acceptAssignment']);
        Route::post('/reject', [DeliveryBoyAssignController::class, 'rejectAssignment']);
        Route::get('/find-nearest-partner', [DeliveryBoyAssignController::class, 'findNearestPartner']);
    });

    Route::post('auth/forgot-password', [PasswordController::class, 'forgot']);
    Route::post('auth/reset-password', [PasswordController::class, 'reset']);

    // ----------------------------------------------
    // Delivery Zones Management
    // ----------------------------------------------
    // Add after your existing delivery-boy routes
    Route::prefix('delivery-zones')->group(function () {
        // Remove auth middleware for testing
        Route::get('/', [DeliveryZoneController::class, 'index']);
        Route::post('/', [DeliveryZoneController::class, 'store']);
        Route::get('/{id}', [DeliveryZoneController::class, 'show']);
        Route::put('/{id}', [DeliveryZoneController::class, 'update']);
        Route::delete('/{id}', [DeliveryZoneController::class, 'destroy']);
        Route::post('/check-availability', [DeliveryZoneController::class, 'checkDeliveryAvailability']);
        Route::post('/get-delivery-fee', [DeliveryZoneController::class, 'getDeliveryFee']);
    });

});
