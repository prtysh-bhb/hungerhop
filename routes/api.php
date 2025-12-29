<?php

use App\Http\Controllers\Api\v1\Auth\DeliveryPartnerPasswordController;
use App\Http\Controllers\Api\v1\Auth\PasswordController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CustomerFavoriteController;
use App\Http\Controllers\Api\v1\CustomerRegistration;
use App\Http\Controllers\API\v1\DeliveryBoyAssignController;
use App\Http\Controllers\Api\v1\DeliveryPartner_login;
use App\Http\Controllers\Api\v1\DeliveryPartnerController;
use App\Http\Controllers\Api\v1\DeliveryPartnerLocationController;
use App\Http\Controllers\Api\v1\DeliveryPartnerWalletController;
use App\Http\Controllers\Api\v1\DeliveryZoneController;
// Controllers
use App\Http\Controllers\Api\v1\FAQController;
use App\Http\Controllers\Api\v1\NearestRestaurantController;
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
        Route::post('/menu/category', [NearestRestaurantController::class, 'menuByCategoryWithRestaurant']);
        Route::post('/by-category', [NearestRestaurantController::class, 'CategoryWiseResaurant']);

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
        Route::get('me', [CustomerRegistration::class, 'self'])->middleware('auth:api');

        // ----------------------------------------------
        // Address Management (Customer)
        // ----------------------------------------------
        Route::post('/add-address', [CustomerRegistration::class, 'addAddress'])->middleware('auth:api');
        Route::get('/addresses', [CustomerRegistration::class, 'addressesList'])->middleware('auth:api');
        Route::post('/edit', [CustomerRegistration::class, 'editProfile'])->middleware('auth:api');
        Route::get('/homepage', [CustomerRegistration::class, 'homepage'])->middleware('auth:api');
        // Route::put('/addresses/{id}', [CustomerRegistration::class, 'updateAddress'])->middleware('auth:api');
        Route::delete('/addresses/{id}', [CustomerRegistration::class, 'deleteAddress'])->middleware('auth:api');
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
        Route::post('/add', [OrderController::class, 'CreateOrder']);
        Route::post('/{id}/update', [OrderController::class, 'editOrder']);
        Route::get('/{id}/checkout', [OrderController::class, 'checkout']);
        Route::post('/details', [OrderController::class, 'getOrderDetails']);
        Route::get('/list', [OrderController::class, 'listOrders']);
        Route::post('/cancel', [OrderController::class, 'cancelOrder']);
    });

    // ----------------------------------------------
    // Customer Favorites
    // ----------------------------------------------
    Route::prefix('favorites')->middleware('auth:api')->group(function () {
        Route::post('/add', [CustomerFavoriteController::class, 'addFavorite']);
        Route::delete('/remove', [CustomerFavoriteController::class, 'removeFavorite']);
        Route::get('/', [CustomerFavoriteController::class, 'listFavorites']);
        Route::post('/toggle', [CustomerFavoriteController::class, 'toggleFavorite']);
        Route::post('/check', [CustomerFavoriteController::class, 'checkFavorite']);
        // Route::get('/type/{type}', [CustomerFavoriteController::class, 'getFavoritesByType']);
        Route::delete('/clear', [CustomerFavoriteController::class, 'clearAllFavorites']);
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
        Route::post('/update-profile', [DeliveryPartner_login::class, 'updateProfile'])->middleware('auth:api');

        // // Password Reset Routes (Public - no auth required)
        // Route::post('/forgot-password', [DeliveryPartnerPasswordController::class, 'forgotPassword']);
        // Route::post('/verify-otp', [DeliveryPartnerPasswordController::class, 'verifyOtp']);
        // Route::post('/reset-password', [DeliveryPartnerPasswordController::class, 'resetPassword']);

        // Protected routes
        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [DeliveryPartner_login::class, 'logout']);
            Route::post('/change-password', [DeliveryPartnerPasswordController::class, 'changePassword']);
            Route::get('/assignments', [DeliveryPartnerController::class, 'myAssignments']);
            Route::post('/assignments', [DeliveryPartnerController::class, 'assignmentDetails']);

            // Order filtering by status
            Route::get('/orders/new', [DeliveryPartnerController::class, 'newOrders']);
            Route::get('/orders/in-progress', [DeliveryPartnerController::class, 'inProgressOrders']);
            Route::get('/orders/past', [DeliveryPartnerController::class, 'pastOrders']);
            Route::get('/orders/summary', [DeliveryPartnerController::class, 'ordersSummary']);

            // Location tracking routes (should be called every 1 minute)
            Route::post('/location/update', [DeliveryPartnerLocationController::class, 'updateLocation']);
            Route::get('/location', [DeliveryPartnerLocationController::class, 'getLocation']);
            Route::post('/location/batch', [DeliveryPartnerLocationController::class, 'batchUpdateLocation']);
            Route::post('/toggle-online', [DeliveryPartnerLocationController::class, 'toggleOnlineStatus']);
            Route::post('/toggle-availability', [DeliveryPartnerLocationController::class, 'toggleAvailability']);

            // Wallet Management Routes
            Route::prefix('wallet')->group(function () {
                Route::post('/transaction', [DeliveryPartnerWalletController::class, 'walletTransaction']);
                Route::get('/details', [DeliveryPartnerWalletController::class, 'getWalletDetails']);
                Route::post('/payment-detail/add', [DeliveryPartnerWalletController::class, 'addPaymentDetail']);
                Route::delete('/payment-detail/delete', [DeliveryPartnerWalletController::class, 'deletePaymentDetail']);
            });
        });

        // Track delivery partner (for customers tracking their order)
        Route::post('/track', [DeliveryPartnerLocationController::class, 'trackPartner'])->middleware('auth:api');
    });

    // ----------------------------------------------
    // Delivery Boy Assignment
    // ----------------------------------------------
    Route::prefix('delivery-boy')->middleware('auth:api')->group(function () {
        Route::post('/assign', [DeliveryBoyAssignController::class, 'assign']);
        Route::post('/accept', [DeliveryBoyAssignController::class, 'acceptAssignment']);
        Route::post('/reject', [DeliveryBoyAssignController::class, 'rejectAssignment']);
        Route::post('/update-status', [DeliveryBoyAssignController::class, 'updateDeliveryStatus']);
        Route::post('/reassign', [DeliveryBoyAssignController::class, 'manualReassign']);
        Route::post('/rejections', [DeliveryBoyAssignController::class, 'getOrderRejections']);
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

    Route::get('/faqs', [FaqController::class, 'index']);
    
    Route::post('/faqs', [FaqController::class, 'store']); 
    Route::get('faqs/{faq}', [FaqController::class, 'show']);
});
