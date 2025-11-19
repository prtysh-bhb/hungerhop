<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::first(); // assumes you have at least 1 order

        if (! $order) {
            return;
        }

        Payment::create([
            'order_id' => $order->id,
            'tenant_id' => $order->tenant_id,
            'payment_method' => 'card',
            'payment_gateway' => 'stripe',
            'gateway_transaction_id' => 'test_txn_'.uniqid(),
            'amount' => 250.00,
            'currency' => 'INR',
            'status' => 'completed',
            'initiated_at' => now()->subMinutes(10),
            'completed_at' => now(),
            'gateway_response' => json_encode(['test' => 'success']),
        ]);
    }
}
