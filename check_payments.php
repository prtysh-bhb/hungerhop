<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Payments for Order 55:\n";
echo str_repeat('-', 80) . "\n";

$payments = \App\Models\Payment::where('order_id', 55)->get();

if ($payments->isEmpty()) {
    echo "No payments found for order 55\n";
} else {
    foreach ($payments as $payment) {
        echo "Payment ID: {$payment->id}\n";
        echo "Status: {$payment->status}\n";
        echo "Gateway: {$payment->payment_gateway}\n";
        echo "Transaction ID: {$payment->gateway_transaction_id}\n";
        echo "Amount: {$payment->amount}\n";
        echo "Gateway Response: " . ($payment->gateway_response ? 'EXISTS' : 'NULL') . "\n";
        echo str_repeat('-', 80) . "\n";
    }
}

echo "\n\nAll Payments (latest 10):\n";
echo str_repeat('=', 80) . "\n";

$allPayments = \App\Models\Payment::latest()->take(10)->get();
foreach ($allPayments as $payment) {
    echo "ID: {$payment->id} | Order: {$payment->order_id} | Status: {$payment->status} | Gateway TX: " . ($payment->gateway_transaction_id ?? 'NULL') . "\n";
}
