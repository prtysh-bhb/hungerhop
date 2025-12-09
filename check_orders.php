<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Latest 5 Orders:\n";
echo str_repeat('-', 50)."\n";

$orders = \App\Models\Order::latest()->take(5)->get(['id', 'customer_id', 'total_amount', 'payment_status']);

foreach ($orders as $order) {
    echo "Order ID: {$order->id}\n";
    echo "Customer ID: {$order->customer_id}\n";
    echo "Amount: {$order->total_amount}\n";
    echo "Payment Status: {$order->payment_status}\n";
    echo str_repeat('-', 50)."\n";
}
