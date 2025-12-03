<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates payment records for orders
     */
    public function run(): void
    {
        $paymentGateways = ['stripe'];
        $currencies = ['INR'];

        // Get orders that need payments
        $orders = Order::whereIn('payment_status', ['completed', 'pending'])
            ->whereNotIn('payment_method', ['cod'])
            ->get();

        $paymentCount = 0;

        foreach ($orders as $order) {
            $paymentStatus = $order->payment_status;
            $orderDate = Carbon::parse($order->created_at);

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'tenant_id' => $order->tenant_id,
                    'payment_method' => $order->payment_method,
                    'payment_gateway' => $paymentGateways[array_rand($paymentGateways)],
                    'gateway_transaction_id' => 'txn_' . uniqid() . '_' . $order->id,
                    'gateway_payment_id' => 'pay_' . uniqid(),
                    'amount' => $order->total_amount,
                    'currency' => $currencies[array_rand($currencies)],
                    'status' => $paymentStatus,
                    'gateway_response' => json_encode([
                        'status' => $paymentStatus,
                        'message' => $paymentStatus === 'completed' ? 'Payment successful' : 'Payment pending',
                    ]),
                    'failure_reason' => $paymentStatus === 'failed' ? 'Card declined' : null,
                    'initiated_at' => $orderDate,
                    'completed_at' => $paymentStatus === 'completed' ? $orderDate->copy()->addSeconds(rand(5, 30)) : null,
                ]
            );
            $paymentCount++;
        }

        $this->command->info("Created {$paymentCount} Payment Records");
    }
}
