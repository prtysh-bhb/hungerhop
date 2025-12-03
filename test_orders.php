<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\DeliveryAssignment;
use App\Models\CustomerProfile;
use App\Models\User;

echo "=== Testing Order Data ===\n\n";

// Check the order with order_number 132
$order = Order::with(['customer.user', 'items'])->where('order_number', '132')->first();

if (!$order) {
    echo "Order with order_number '132' not found, checking latest order...\n";
    $order = Order::with(['customer.user', 'items'])->latest()->first();
}

if (!$order) {
    echo "No orders found!\n";
    exit;
}

echo "Order ID: " . $order->id . "\n";
echo "Order Number: " . $order->order_number . "\n";
echo "Customer ID: " . $order->customer_id . "\n";
echo "Tenant ID: " . $order->tenant_id . "\n";

echo "\n--- Customer Info ---\n";
if ($order->customer) {
    echo "Customer Profile exists\n";
    echo "Customer Profile ID: " . $order->customer->id . "\n";
    echo "Customer User ID: " . $order->customer->user_id . "\n";
    
    if ($order->customer->user) {
        echo "User exists\n";
        echo "User Name: " . $order->customer->user->first_name . " " . $order->customer->user->last_name . "\n";
    } else {
        echo "User is NULL - user_id: " . $order->customer->user_id . "\n";
    }
} else {
    echo "Customer Profile is NULL\n";
    
    // Check if customer profile exists with this ID
    $cp = CustomerProfile::find($order->customer_id);
    if ($cp) {
        echo "But CustomerProfile with ID " . $order->customer_id . " exists!\n";
        echo "CustomerProfile user_id: " . $cp->user_id . "\n";
    } else {
        echo "CustomerProfile with ID " . $order->customer_id . " does not exist!\n";
    }
}

echo "\n--- Order Status History ---\n";
$statuses = OrderStatus::where('order_id', $order->id)->get();
echo "Status count: " . $statuses->count() . "\n";
foreach ($statuses as $status) {
    echo "  - Status: " . $status->status . " at " . $status->created_at . "\n";
}

echo "\n--- Delivery Assignment ---\n";
$assignment = DeliveryAssignment::where('order_id', $order->id)->first();
if ($assignment) {
    echo "Assignment exists\n";
    echo "Partner ID: " . $assignment->partner_id . "\n";
    echo "Assignment Status: " . $assignment->status . "\n";
} else {
    echo "No delivery assignment for this order\n";
    echo "(Note: Only orders with status 'out_for_delivery' or 'delivered' have assignments)\n";
}

echo "\n--- Check All Order Statuses ---\n";
$orderCounts = Order::select('status', \DB::raw('count(*) as count'))->groupBy('status')->get();
foreach ($orderCounts as $row) {
    echo "  Status: " . $row->status . " - Count: " . $row->count . "\n";
}

echo "\n--- Delivery Assignments Count ---\n";
echo "Total assignments: " . DeliveryAssignment::count() . "\n";

echo "\n--- Check an Order with Delivery ---\n";
$deliveredOrder = Order::where('status', 'delivered')->first();
if ($deliveredOrder) {
    echo "Delivered Order ID: " . $deliveredOrder->id . "\n";
    echo "Delivered Order Tenant ID: " . $deliveredOrder->tenant_id . "\n";
    $da = DeliveryAssignment::where('order_id', $deliveredOrder->id)->first();
    if ($da) {
        echo "Delivery Assignment exists for delivered order\n";
        echo "Partner ID: " . $da->partner_id . "\n";
        
        $partner = \App\Models\DeliveryPartner::find($da->partner_id);
        if ($partner) {
            echo "Delivery Partner exists\n";
            if ($partner->user) {
                echo "Delivery Partner User: " . $partner->user->first_name . " " . $partner->user->last_name . "\n";
            } else {
                echo "Delivery Partner User is NULL\n";
            }
        } else {
            echo "Delivery Partner is NULL\n";
        }
    } else {
        echo "No delivery assignment found for delivered order!\n";
    }
} else {
    echo "No delivered orders found!\n";
}

echo "\n--- Check Logged In User Tenant ---\n";
// Check which user created the order
$customerProfile = CustomerProfile::find($order->customer_id);
if ($customerProfile) {
    $customerUser = User::find($customerProfile->user_id);
    echo "Customer User ID: " . $customerProfile->user_id . "\n";
    echo "Customer User Email: " . ($customerUser ? $customerUser->email : 'N/A') . "\n";
    echo "Customer User Tenant ID: " . ($customerUser ? $customerUser->tenant_id : 'N/A') . "\n";
}

// Check all tenant admins and their tenant_ids
echo "\n--- All Tenant Admins ---\n";
$tenantAdmins = User::where('tenant_admin', true)->get();
foreach ($tenantAdmins as $admin) {
    echo "  - {$admin->email} (tenant_id: {$admin->tenant_id})\n";
}

echo "\n--- Location Admins ---\n";
$locationAdmins = User::where('location_admin', true)->get();
foreach ($locationAdmins as $admin) {
    echo "  - {$admin->email} (tenant_id: {$admin->tenant_id})\n";
}

echo "\n=== ISSUE ANALYSIS ===\n";
echo "Order Tenant ID: " . $order->tenant_id . "\n";
echo "For Order Status History to show, logged-in user's tenant_id MUST match {$order->tenant_id}\n";
echo "Login with a user that has tenant_id = {$order->tenant_id}\n";

echo "\n=== Done ===\n";
