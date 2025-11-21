<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerProfileController;
use App\Http\Controllers\Admin\DocumentManagementController;
use App\Http\Controllers\Admin\DocumentVerificationController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\Restaurant\MenuVersionController;
use App\Http\Controllers\Admin\Restaurant\RestaurantMenuController;
use App\Http\Controllers\Admin\RestaurantAdminController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\RestaurantManagementController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\TenantSwitchController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\dashboards\LocationAdminDashboardController;
use App\Http\Controllers\dashboards\SuparAdminDashboard;
use App\Http\Controllers\dashboards\TenantAdminController;
use App\Http\Controllers\DeliveryPartnerController;
use App\Http\Controllers\Guest\DeliveryPartnerRegistrationController;
use App\Http\Controllers\Restaurant\MenuAnalyticsController;
use App\Http\Controllers\Restaurant\MenuBulkController;
use App\Http\Controllers\Restaurant\MenuCategoryController;
use App\Http\Controllers\Restaurant\MenuItemController;
use App\Http\Controllers\Restaurant\MenuVariationController;
use App\Http\Controllers\Restaurant\OrderController;
use App\Http\Controllers\SuperAdmin\MemberController;
use App\Http\Controllers\TenantAdmin\PaymentController;
use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        // Redirect based on user role
        switch ($user->role) {
            case 'super_admin':
            case 'tenant_admin':
                return redirect()->route('admin.dashboard');
            case 'restaurant_staff':
                return redirect()->route('restaurant.dashboard');
            case 'location_admin':
                return redirect()->route('location-admin.dashboard');
            case 'delivery_partner':
                return redirect()->route('restaurant.dashboard');
            case 'customer':
                return redirect()->route('customer.dashboard');
            default:
        }
    }

    // Guest users go to login page
    return redirect()->route('login');
})->name('home');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'submit'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'submit'])->name('password.update');
});

// Stripe Webhook (no authentication required)
Route::post('/webhook/stripe', [PaymentController::class, 'handleStripeWebhook'])
    ->name('webhook.stripe');

// routes/web.php (temporary test)
Route::get('/mail-test', function () {
    try {
        \Mail::raw('Testing Mailgun SMTP after reset', function ($m) {
            $m->to('nibeditas.brainerhub@gmail.com')->subject('SMTP Test');
        });

        return 'Sent';
    } catch (\Exception $e) {
        return 'Error: '.$e->getMessage();
    }
});

// Specialized registration routes
Route::post('/register/restaurant-staff', [RegisterController::class, 'registerRestaurantStaff'])->name('register.restaurant-staff');

// Delivery Partner Registration (Guest)

Route::get('/delivery-partner/register', [DeliveryPartnerRegistrationController::class, 'showForm'])->name('guest.delivery-partner.register-form');

// Route::post('/delivery-partner/document/upload', [DeliveryPartnerRegistrationController::class, 'uploadDocument'])->name('guest.delivery-partner.document.upload');
Route::post('/delivery-partner/register', [DeliveryPartnerRegistrationController::class, 'register'])->name('guest.delivery-partner.register');

Route::resource('/delivery/partners', DeliveryPartnerController::class);

Route::post('/restaurant/registration/store', [RestaurantAdminController::class, 'storeRegistration'])->name('public.restaurant.registration.store');

Route::get('/restaurant/login', function () {
    return view('layouts.partials.guest.restaurant_login');
})->name('restaurant.login');

// Dashboard routes (protected by auth middleware)
Route::middleware(['auth', 'identify_tenant'])->group(function () {
    // Admin Dashboard - for super_admin and tenant_admin
    Route::get('/admin/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard.super');
        } else {
            return redirect()->route('admin.dashboard.tenant');
        }
    })->middleware('role:super_admin,tenant_admin')->name('admin.dashboard');

    // Super Admin specific dashboard
    Route::get('/admin/dashboard/super', [SuparAdminDashboard::class, 'index'])
        ->middleware('role:super_admin')->name('admin.dashboard.super');

    // AJAX endpoints for real-time dashboard updates
    Route::get('/admin/dashboard/stats', [SuparAdminDashboard::class, 'getStats'])
        ->middleware('role:super_admin')->name('admin.dashboard.stats');
    Route::get('/admin/dashboard/recent-orders', [SuparAdminDashboard::class, 'getRecentOrders'])
        ->middleware('role:super_admin')->name('admin.dashboard.recent-orders');

    // Tenant Admin specific dashboard
    Route::get('/admin/dashboard/tenant', [TenantAdminController::class, 'index'])
        ->middleware('role:tenant_admin')->name('admin.dashboard.tenant');

    // Tenant Admin dashboard stats for AJAX
    Route::get('/admin/dashboard/tenant/stats', [TenantAdminController::class, 'getStats'])
        ->middleware('role:tenant_admin')->name('admin.dashboard.tenant.stats');

    // Tenant Admin Payment Routes (no payment check middleware here)
    Route::middleware(['role:tenant_admin'])->prefix('admin/tenant')->name('admin.tenant.')->group(function () {
        Route::get('/payment/plans', [PaymentController::class, 'plans'])
            ->name('payment.plans');
        Route::get('/payment/checkout', [PaymentController::class, 'checkout'])
            ->name('payment.checkout');
        Route::post('/payment/create', [PaymentController::class, 'createPayment'])
            ->name('payment.create');
        Route::post('/payment/success', [PaymentController::class, 'paymentSuccess'])
            ->name('payment.success');
        Route::post('/payment/failure', [PaymentController::class, 'paymentFailure'])
            ->name('payment.failure');
        Route::get('/payment/history', [PaymentController::class, 'history'])
            ->name('payment.history');
        Route::post('/payment/update-plan', [PaymentController::class, 'updatePlan'])
            ->name('payment.update-plan');
        Route::get('/payment/invoice/{payment}', [PaymentController::class, 'downloadInvoice'])
            ->name('payment.invoice');
    });

    // Restaurant Dashboard - for restaurant_staff, location_admin, and delivery_partner
    Route::get('/restaurant/dashboard', function () {
        $user = auth()->user();
        switch ($user->role) {
            case 'restaurant_staff':
                return view('pages.restaurant_staff.dashboard.index');
            case 'location_admin':
                return redirect()->route('location-admin.dashboard');
            case 'delivery_partner':
                return view('pages.delivery_partner.dashboard.index');
            default:
        }
    })->middleware('role:restaurant_staff,location_admin,delivery_partner')->name('restaurant.dashboard');

    // Location Admin Dashboard - specific route for location admin
    Route::get('/location-admin/dashboard', [LocationAdminDashboardController::class, 'index'])
        ->middleware('role:location_admin')->name('location-admin.dashboard');

    // Customer Dashboard - for customer
    Route::get('/customer/dashboard', function () {
        return view('pages.customer.dashboard.index');
    })->middleware('role:customer')->name('customer.dashboard');

    Route::middleware(['auth'])->group(function () {
        Route::post('/admin/switch-tenant', [TenantSwitchController::class, 'switch'])
            ->name('admin.switch.tenant');
    });

    // ===== Order Management Routes =====
    // Show all orders for restaurants under the same tenant
    Route::middleware(['role:super_admin,tenant_admin,location_admin', 'check_tenant_payment'])->group(function () {
        Route::get('/restaurant/orders', [OrderController::class, 'ShowList'])
            ->name('restaurant.orders');

        // Show order details (existing route)
        Route::get('/restaurant/orders/{id}', [OrderController::class, 'ShowDetails'])->name('restaurant.order.details');

        // Update order status
        Route::patch('/restaurant/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('restaurant.orders.updateStatus');
    });

    // Admin Management Routes
    // Route::resource('admin/members', MemberController::class);
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/members', [MemberController::class, 'index'])->name('members');
        Route::get('/members/{id}', [MemberController::class, 'show_details'])->name('show_details');
    });
    // Customer Management Routes (Super Admin Only)
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
        Route::patch('/customers/{id}/status', [CustomerController::class, 'updateStatus'])->name('customers.status');
        Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        // Customer Profile Management Routes
        Route::get('/customers/{id}/profile', [CustomerProfileController::class, 'index'])->name('customers.profile');
        Route::put('/customers/{id}/profile', [CustomerProfileController::class, 'updateProfile'])->name('customers.profile.update');
        Route::post('/customers/{id}/addresses', [CustomerProfileController::class, 'updateAddress'])->name('customers.addresses.store');
        Route::delete('/customers/{id}/addresses/{addressId}', [CustomerProfileController::class, 'deleteAddress'])->name('customers.addresses.destroy');
        Route::patch('/customers/{id}/loyalty-points', [CustomerProfileController::class, 'updateLoyaltyPoints'])->name('customers.loyalty.update');
    });

    Route::get('/admin/analysis', function () {
        return view('pages.super_admin.analysis');
    })->name('admin.analysis');

    // Add this route for loading cities by state
    Route::get('/admin/get-cities/{state}', function ($stateId) {
        $cities = City::where('state_id', $stateId)->get(['id', 'name']);

        return response()->json($cities);
    });

    Route::post('/restaurant-admin/{id}/approve', [RestaurantAdminController::class, 'approve'])
        ->name('restaurant-admin.approve');

    // Add this route to handle restaurant registration
    Route::post('/restaurant-admin/registration/store', [RestaurantAdminController::class, 'storeRegistration'])
        ->name('restaurant-admin.registration.store')
        ->middleware(['auth', 'role:super_admin,tenant_admin']);

    // Restaurant Admin Routes
    // Tenant Management Routes (Super Admin and Tenant Admin)
    Route::middleware(['role:super_admin,tenant_admin', 'check_tenant_payment'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
        Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
        Route::post('/tenants/{tenant}/status', [TenantController::class, 'updateStatus'])->name('tenants.updateStatus');
        
        // Export routes
        Route::get('/tenants/export/excel', [TenantController::class, 'exportExcel'])->name('tenants.export.excel');
        Route::get('/tenants/export/pdf', [TenantController::class, 'exportPdf'])->name('tenants.export.pdf');
    });

    Route::get('/admin/customers-old', function () {
        return view('pages.customer.customer');
    })->name('admin.customers.old');

    // Analysis Route - Super Admin Only
    Route::middleware(['can:access-analysis'])->group(function () {
        Route::get('/admin/analysis', function () {
            return view('pages.super_admin.analysis');
        })->name('admin.analysis');
    });    // Restaurant Admin Routes
    Route::prefix('restaurant-admin')->name('restaurant-admin.')->middleware(['role:super_admin,tenant_admin,location_admin', 'check_tenant_payment'])->group(function () {
        // Dashboard - Only for super_admin and tenant_admin
        Route::get('/', [RestaurantAdminController::class, 'index'])->name('index')->middleware('role:super_admin,tenant_admin');

        // Registration Routes - Only for super_admin and tenant_admin
        Route::prefix('registration')->name('registration.')->middleware('role:super_admin,tenant_admin')->group(function () {
            Route::get('/create', [RestaurantAdminController::class, 'showRegistrationForm'])->name('create');
            Route::post('/store', [RestaurantAdminController::class, 'storeRegistration'])->name('store');
        });

        // Restaurant List and Management - All allowed roles
        Route::get('/list', [RestaurantAdminController::class, 'list'])->name('list');
        Route::get('/show/{id}', [RestaurantAdminController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [RestaurantAdminController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RestaurantAdminController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [RestaurantAdminController::class, 'destroy'])->name('destroy');

        // Status Update Route - All allowed roles (super_admin, tenant_admin, location_admin)
        Route::post('/{id}/update-status', [RestaurantAdminController::class, 'updateStatus'])->name('update-status');
        
        // Pause/Resume Routes - All allowed roles (super_admin, tenant_admin, location_admin)
        Route::post('/{id}/toggle-pause', [RestaurantAdminController::class, 'togglePause'])->name('toggle-pause');

        // Document Management Routes
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [DocumentManagementController::class, 'index'])->name('index');
            Route::get('/create', [DocumentManagementController::class, 'create'])->name('create');
            Route::post('/store', [DocumentManagementController::class, 'store'])->name('store');
            Route::get('/{id}/view', [DocumentManagementController::class, 'view'])->name('view');
            Route::get('/{id}/download', [DocumentManagementController::class, 'download'])->name('download');
            Route::get('/{id}/edit', [DocumentManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DocumentManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [DocumentManagementController::class, 'destroy'])->name('destroy');
            Route::get('/restaurant/{restaurant_id}', [DocumentManagementController::class, 'getByRestaurant'])->name('by-restaurant');
        });

        // Verification Routes
        Route::prefix('verification')->name('verification.')->group(function () {
            Route::get('/', [DocumentVerificationController::class, 'index'])->name('index');
            Route::get('/{id}/view', [DocumentVerificationController::class, 'viewDocument'])->name('view');
            Route::post('/{id}/update-status', [DocumentVerificationController::class, 'updateStatus'])->name('update-status');
            Route::post('/bulk-update-status', [DocumentVerificationController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
            Route::get('/{id}/download', [DocumentVerificationController::class, 'downloadDocument'])->name('download');
            Route::get('/queue/data', [DocumentVerificationController::class, 'getVerificationQueue'])->name('queue-data');
            Route::get('/expiry/report', [DocumentVerificationController::class, 'getExpiryReport'])->name('expiry-report');
        });

        // Management Routes
        Route::prefix('management')->name('management.')->group(function () {
            Route::get('/', [RestaurantManagementController::class, 'index'])->name('index');
            Route::get('/{id}', [RestaurantManagementController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [RestaurantManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [RestaurantManagementController::class, 'update'])->name('update');
            Route::post('/{id}/status', [RestaurantManagementController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/toggle', [RestaurantManagementController::class, 'toggle'])->name('toggle');
            Route::delete('/{id}', [RestaurantManagementController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update-status', [RestaurantManagementController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
            Route::get('/data/ajax', [RestaurantManagementController::class, 'getData'])->name('data');
        });
    });

    // ====== ENHANCED RESTAURANT MANAGEMENT ROUTES ======
    // Restaurant CRUD Routes (Updated and Enhanced)
    Route::prefix('admin')->name('admin.')->middleware(['role:super_admin,tenant_admin', 'check_tenant_payment'])->group(function () {

        // Restaurant Resource Routes
        Route::resource('restaurants', RestaurantController::class)->except(['destroy']);

        // Additional Restaurant Management Routes
        Route::prefix('restaurants')->name('restaurants.')->group(function () {
            // Approval/Rejection Routes
            Route::post('{id}/approve', [RestaurantController::class, 'approve'])->name('approve');
            Route::post('{id}/reject', [RestaurantController::class, 'reject'])->name('reject');
            Route::post('{id}/suspend', [RestaurantController::class, 'suspend'])->name('suspend');
            Route::post('{id}/activate', [RestaurantController::class, 'activate'])->name('activate');

            // Pause/Resume Routes
            Route::post('{id}/toggle-pause', [RestaurantController::class, 'togglePause'])->name('toggle-pause');

            // Status Management
            Route::patch('{id}/status', [RestaurantController::class, 'updateStatus'])->name('update-status');
            Route::post('bulk-status-update', [RestaurantController::class, 'bulkStatusUpdate'])->name('bulk-status-update');

            // Document Management for Restaurant
            Route::get('{id}/documents', [RestaurantController::class, 'showDocuments'])->name('documents');
            Route::post('{id}/documents/upload', [RestaurantController::class, 'uploadDocuments'])->name('documents.upload');
            Route::delete('documents/{documentId}', [RestaurantController::class, 'deleteDocument'])->name('documents.delete');

            // Working Hours Management
            Route::get('{id}/working-hours', [RestaurantController::class, 'showWorkingHours'])->name('working-hours');
            Route::post('{id}/working-hours', [RestaurantController::class, 'updateWorkingHours'])->name('working-hours.update');

            // Delivery Zones Management
            Route::get('{id}/delivery-zones', [RestaurantController::class, 'showDeliveryZones'])->name('delivery-zones');
            Route::post('{id}/delivery-zones', [RestaurantController::class, 'updateDeliveryZones'])->name('delivery-zones.update');

            // Export/Import Routes
            Route::get('export', [RestaurantController::class, 'export'])->name('export');
            Route::post('import', [RestaurantController::class, 'import'])->name('import');

            // Analytics Routes
            Route::get('analytics', [RestaurantController::class, 'analytics'])->name('analytics');
            Route::get('{id}/performance', [RestaurantController::class, 'performance'])->name('performance');
        });

        // AJAX Routes for Dynamic Data Loading
        Route::prefix('ajax')->name('ajax.')->group(function () {
            // Location Data Routes
            Route::get('cities-by-state', [RestaurantController::class, 'getCitiesByState'])->name('cities-by-state');
            Route::get('areas-by-city', [RestaurantController::class, 'getAreasByCity'])->name('areas-by-city');

            // Restaurant Data Routes
            Route::get('restaurants/search', [RestaurantController::class, 'searchRestaurants'])->name('restaurants.search');
            Route::get('restaurants/datatable', [RestaurantController::class, 'getRestaurantsDataTable'])->name('restaurants.datatable');
            Route::get('restaurants/{id}/quick-info', [RestaurantController::class, 'getQuickInfo'])->name('restaurants.quick-info');

            // Validation Routes
            Route::post('restaurants/validate-name', [RestaurantController::class, 'validateRestaurantName'])->name('restaurants.validate-name');
            Route::post('restaurants/validate-email', [RestaurantController::class, 'validateRestaurantEmail'])->name('restaurants.validate-email');
            Route::post('restaurants/check-location', [RestaurantController::class, 'checkLocationAvailability'])->name('restaurants.check-location');
        });
    });

    // Restaurant Management Routes - protected from super admin
    Route::middleware(['can:access-menu-management'])->group(function () {
        Route::prefix('restaurant/menu')->name('restaurant.menu.')->group(function () {
            Route::get('/list', [MenuItemController::class, 'index'])->name('list');
            Route::get('/add', [MenuItemController::class, 'create'])->name('add');
            Route::post('/store', [MenuItemController::class, 'store'])->name('store');
            Route::get('/show/{menuItem}', [MenuItemController::class, 'show'])->name('show');
            Route::get('/edit/{menuItem}', [MenuItemController::class, 'edit'])->name('edit');
            Route::put('/update/{menuItem}', [MenuItemController::class, 'update'])->name('update');
            Route::delete('/delete/{menuItem}', [MenuItemController::class, 'destroy'])->name('destroy');
            Route::get('/duplicate/{menuItem}', [MenuItemController::class, 'duplicate'])->name('duplicate');
            Route::patch('/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('toggle');

            // Menu Item Variations Routes
            Route::prefix('{menuItem}/variations')->name('variations.')->group(function () {
                Route::get('/', [MenuVariationController::class, 'index'])->name('index');
                Route::get('/create', [MenuVariationController::class, 'create'])->name('create');
                Route::post('/store', [MenuVariationController::class, 'store'])->name('store');
                Route::post('/bulk-store', [MenuVariationController::class, 'bulkStore'])->name('bulk-store');
                Route::get('/{variation}/edit', [MenuVariationController::class, 'edit'])->name('edit');
                Route::put('/{variation}', [MenuVariationController::class, 'update'])->name('update');
                Route::delete('/{variation}', [MenuVariationController::class, 'destroy'])->name('destroy');
                Route::patch('/{variation}/toggle', [MenuVariationController::class, 'toggleAvailability'])->name('toggle');
                Route::post('/sort-order', [MenuVariationController::class, 'updateSortOrder'])->name('sort-order');
                Route::get('/api', [MenuVariationController::class, 'getVariations'])->name('api');
            });
        });

        // Menu Categories Routes
        Route::prefix('restaurant/categories')->name('restaurant.categories.')->group(function () {
            Route::get('/', [MenuCategoryController::class, 'index'])->name('index');
            Route::get('/create', [MenuCategoryController::class, 'create'])->name('create');
            Route::post('/store', [MenuCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [MenuCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [MenuCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [MenuCategoryController::class, 'destroy'])->name('destroy');
            Route::patch('/{category}/toggle', [MenuCategoryController::class, 'toggleStatus'])->name('toggle');
            Route::post('/sort-order', [MenuCategoryController::class, 'updateSortOrder'])->name('sort-order');
            Route::get('/api', [MenuCategoryController::class, 'getCategories'])->name('api');
        });

        // Menu Analytics Routes
        Route::prefix('restaurant/analytics')->name('restaurant.analytics.')->group(function () {
            Route::get('/', [MenuAnalyticsController::class, 'index'])->name('index');
            Route::get('/popular-items', [MenuAnalyticsController::class, 'popularItems'])->name('popular-items');
            Route::get('/category-performance', [MenuAnalyticsController::class, 'categoryPerformance'])->name('category-performance');
            Route::get('/revenue', [MenuAnalyticsController::class, 'revenueAnalytics'])->name('revenue');
            Route::get('/inventory-alerts', [MenuAnalyticsController::class, 'inventoryAlerts'])->name('inventory-alerts');
            Route::get('/performance-metrics', [MenuAnalyticsController::class, 'performanceMetrics'])->name('performance-metrics');
            Route::get('/items-needing-attention', [MenuAnalyticsController::class, 'itemsNeedingAttention'])->name('items-needing-attention');
            Route::post('/update-metrics', [MenuAnalyticsController::class, 'updateItemMetrics'])->name('update-metrics');
        });

        // Menu Bulk Operations Routes
        Route::prefix('restaurant/menu/bulk')->name('restaurant.menu.bulk.')->group(function () {
            Route::get('/', [MenuBulkController::class, 'index'])->name('index');
            Route::post('/update-prices', [MenuBulkController::class, 'bulkUpdatePrices'])->name('update-prices');
            Route::post('/update-availability', [MenuBulkController::class, 'bulkUpdateAvailability'])->name('update-availability');
            Route::post('/update-categories', [MenuBulkController::class, 'bulkUpdateCategories'])->name('update-categories');
            Route::post('/update-dietary', [MenuBulkController::class, 'bulkUpdateDietary'])->name('update-dietary');
            Route::post('/update-inventory', [MenuBulkController::class, 'bulkUpdateInventory'])->name('update-inventory');
            Route::post('/import', [MenuBulkController::class, 'bulkImport'])->name('import');
            Route::get('/export', [MenuBulkController::class, 'export'])->name('export');
            Route::delete('/delete', [MenuBulkController::class, 'bulkDelete'])->name('delete');
        });

        // Legacy route for menu categories (keeping for backward compatibility)
        Route::get('/restaurant/menu/categories', [MenuCategoryController::class, 'index'])->name('restaurant.menu.categories');
    });

    // Admin Management Routes
    // Route handled by MemberController resource, closure removed to avoid conflict
});

Route::middleware(['auth'])->group(function () {
    Route::post('/admin/switch-tenant', [TenantSwitchController::class, 'switch'])
        ->name('admin.switch.tenant');
});

// ====== LOCATION HELPER ROUTES (Enhanced) ======
Route::prefix('admin')->name('admin.')->group(function () {
    // Original location routes (kept for backward compatibility)
    Route::get('/get-states/{country}', [LocationController::class, 'getStates'])->name('get-states');
    Route::get('/get-cities/{state}', [LocationController::class, 'getCities'])->name('get-cities');

    // Enhanced location routes
    Route::prefix('location')->name('location.')->group(function () {
        Route::get('countries', [LocationController::class, 'getCountries'])->name('countries');
        Route::get('states/{country}', [LocationController::class, 'getStatesByCountry'])->name('states');
        Route::get('cities/{state}', [LocationController::class, 'getCitiesByState'])->name('cities');
        Route::get('areas/{city}', [LocationController::class, 'getAreasByCity'])->name('areas');

        // Geocoding routes
        Route::post('geocode', [LocationController::class, 'geocodeAddress'])->name('geocode');
        Route::post('reverse-geocode', [LocationController::class, 'reverseGeocode'])->name('reverse-geocode');
    });
});

// ====== RESTAURANT MENU ROUTES (Enhanced) ======
Route::prefix('admin/restaurant/{restaurant}')->name('admin.restaurant.')->middleware(['auth', 'role:super_admin,tenant_admin', 'check_tenant_payment'])->group(function () {
    // Menu Management
    Route::get('menu', [RestaurantMenuController::class, 'index'])->name('menu.index');
    Route::post('menu/inherit', [RestaurantMenuController::class, 'inheritFromTenant'])->name('menu.inherit');
    Route::get('menu/categories', [RestaurantMenuController::class, 'categories'])->name('menu.categories');
    Route::post('menu/categories', [RestaurantMenuController::class, 'storeCategory'])->name('menu.categories.store');
    Route::get('menu/items', [RestaurantMenuController::class, 'items'])->name('menu.items');
    Route::post('menu/items', [RestaurantMenuController::class, 'storeItem'])->name('menu.items.store');

    // Menu Version Management
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::post('version', [MenuVersionController::class, 'store'])->name('version.save');
        Route::get('versions', [MenuVersionController::class, 'index'])->name('versions');
        Route::get('versions/{version}', [MenuVersionController::class, 'show'])->name('versions.show');
        Route::post('versions/{version}/rollback', [MenuVersionController::class, 'rollback'])->name('versions.rollback');
        Route::post('versions/{version}/publish', [MenuVersionController::class, 'publish'])->name('versions.publish');
    });
});

// ====== API ROUTES FOR RESTAURANT MANAGEMENT ======
// Route::prefix('api/v1')->name('api.')->middleware(['auth:sanctum'])->group(function () {
//     // Restaurant API Routes
//     // Route::apiResource('restaurants', 'Api\RestaurantController');

//     Route::prefix('restaurants')->name('restaurants.')->group(function () {
//         // Route::post('{id}/approve', 'Api\RestaurantController@approve')->name('approve');
//         // Route::post('{id}/reject', 'Api\RestaurantController@reject')->name('reject');
//         // Route::get('search', 'Api\RestaurantController@search')->name('search');
//         // Route::get('nearby', 'Api\RestaurantController@nearby')->name('nearby');
//         // Route::get('{id}/menu', 'Api\RestaurantController@getMenu')->name('menu');
//     });
// });

// Test routes
// Route::view('/test-fixes', 'test-fixes')->name('test-fixes');
// Route::view('/test-navigation', 'test-navigation')->name('test-navigation');

// Test route for right sidebar functionality
Route::get('/test-sidebar', function () {
    return view('partials.right-sidebar');
})->name('test-sidebar');
