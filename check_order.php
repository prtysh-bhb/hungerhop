<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\CustomerProfile;
use App\Models\User;

$order = Order::find(29);
echo "Order ID: " . $order->id . "\n";
echo "Order customer_id: " . $order->customer_id . "\n\n";

$cp = CustomerProfile::find($order->customer_id);
echo "CustomerProfile ID: " . $cp->id . "\n";
echo "CustomerProfile user_id: " . $cp->user_id . "\n\n";

$user = User::find($cp->user_id);
echo "User exists: " . ($user ? 'YES' : 'NO') . "\n";
if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "User name: " . $user->first_name . " " . $user->last_name . "\n";
    echo "User email: " . $user->email . "\n";
}

echo "\n--- Testing with eager loading ---\n";
$order2 = Order::with('customer.user')->find(29);
echo "Order->customer: " . ($order2->customer ? 'exists' : 'NULL') . "\n";
if ($order2->customer) {
    echo "Order->customer->user: " . ($order2->customer->user ? 'exists' : 'NULL') . "\n";
    if ($order2->customer->user) {
        echo "Name: " . $order2->customer->user->first_name . " " . $order2->customer->user->last_name . "\n";
    }
}

echo "\n--- Check all users ---\n";
$allUsers = User::select('id', 'first_name', 'last_name', 'email')->get();
foreach ($allUsers as $u) {
    echo "  User #{$u->id}: {$u->first_name} {$u->last_name} ({$u->email})\n";
}

echo "\n--- Check all customer profiles ---\n";
$allProfiles = CustomerProfile::select('id', 'user_id')->get();
foreach ($allProfiles as $p) {
    echo "  Profile #{$p->id}: user_id={$p->user_id}\n";
}
