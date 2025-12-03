<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Restaurant;
use App\Models\CustomerProfile;
use App\Models\CustomerAddress;
use App\Models\MenuItem;
use App\Models\DeliveryAssignment;
use App\Models\DeliveryPartner;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates orders with items, status history, and delivery assignments
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Valid order statuses from migration
        $orderStatuses = ['placed', 'accepted', 'preparing', 'ready_for_pickup', 'assigned_to_delivery', 'picked_up', 'out_for_delivery', 'delivered', 'cancelled'];
        $paymentMethods = ['cod', 'card', 'upi', 'wallet'];
        $paymentStatuses = ['pending', 'completed', 'failed', 'refunded'];

        $customers = CustomerProfile::with('user', 'addresses')->get();
        $restaurants = Restaurant::with('menuItems', 'tenant')->where('status', 'approved')->get();
        $deliveryPartners = DeliveryPartner::where('status', 'approved')->get();

        if ($customers->isEmpty() || $restaurants->isEmpty()) {
            $this->command->warn('No customers or restaurants found. Run CustomerSeeder and RestaurantSeeder first.');
            return;
        }

        $orderCount = 0;
        $orderNumber = 100001;

        // Create orders for each customer
        foreach ($customers as $customer) {
            // Each customer gets 3-8 orders
            $numOrders = rand(3, 8);
            
            for ($i = 0; $i < $numOrders; $i++) {
                $restaurant = $restaurants->random();
                $menuItems = $restaurant->menuItems;

                if ($menuItems->isEmpty()) {
                    continue;
                }

                $address = $customer->addresses->first();
                if (!$address) {
                    continue;
                }

                // Random order date in last 30 days
                $orderDate = $now->copy()->subDays(rand(0, 30))->subHours(rand(0, 23));
                
                // Calculate order totals
                $subtotal = 0;
                $selectedItems = $menuItems->random(rand(1, min(4, $menuItems->count())));
                
                $itemsData = [];
                foreach ($selectedItems as $item) {
                    $quantity = rand(1, 3);
                    $itemTotal = $item->base_price * $quantity;
                    $subtotal += $itemTotal;
                    
                    $itemsData[] = [
                        'item' => $item,
                        'quantity' => $quantity,
                        'unit_price' => $item->base_price,
                        'total_price' => $itemTotal,
                    ];
                }

                $taxAmount = round($subtotal * ($restaurant->tax_percentage / 100), 2);
                $deliveryFee = $restaurant->base_delivery_fee;
                $discountAmount = rand(0, 1) ? rand(20, 50) : 0;
                $totalAmount = $subtotal + $taxAmount + $deliveryFee - $discountAmount;
                $restaurantAmount = round($subtotal * ($restaurant->restaurant_commission_percentage / 100), 2);
                $platformFee = $subtotal - $restaurantAmount;

                // Determine status based on order age
                $daysSinceOrder = $now->diffInDays($orderDate);
                if ($daysSinceOrder > 2) {
                    $status = rand(0, 10) > 1 ? 'delivered' : 'cancelled';
                } elseif ($daysSinceOrder > 1) {
                    $status = $orderStatuses[rand(4, 6)];
                } else {
                    $status = $orderStatuses[rand(0, 5)];
                }

                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $paymentStatus = $status === 'delivered' ? 'completed' : ($status === 'cancelled' ? 'refunded' : 'pending');
                if ($paymentMethod === 'cod' && $status !== 'delivered') {
                    $paymentStatus = 'pending';
                }

                // Create Order
                $order = Order::create([
                    'order_number' => 'ORD' . $orderNumber++,
                    'customer_id' => $customer->id,
                    'restaurant_id' => $restaurant->id,
                    'delivery_address_id' => $address->id,
                    'tenant_id' => $restaurant->tenant_id,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'delivery_fee' => $deliveryFee,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'restaurant_amount' => $restaurantAmount,
                    'delivery_amount' => $deliveryFee,
                    'platform_fee' => $platformFee,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'special_instructions' => rand(0, 3) === 0 ? 'Please pack separately' : null,
                    'pickup_otp' => str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
                    'delivery_otp' => str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
                    'estimated_delivery_time' => $orderDate->copy()->addMinutes(45),
                    'actual_delivery_time' => $status === 'delivered' ? $orderDate->copy()->addMinutes(rand(35, 60)) : null,
                    'cancellation_reason' => $status === 'cancelled' ? 'Customer requested cancellation' : null,
                    'cancelled_by' => $status === 'cancelled' ? 'customer' : null,
                    'cancelled_at' => $status === 'cancelled' ? $orderDate->copy()->addMinutes(5) : null,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Create Order Items
                foreach ($itemsData as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_id' => $itemData['item']->id,
                        'tenant_id' => $restaurant->tenant_id,
                        'item_name' => $itemData['item']->item_name,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'total_price' => $itemData['total_price'],
                        'special_instructions' => null,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }

                // Create Order Status History
                $statusIndex = array_search($status, $orderStatuses);
                $statusTime = $orderDate->copy();
                
                for ($j = 0; $j <= $statusIndex && $j < count($orderStatuses) - 1; $j++) {
                    if ($orderStatuses[$j] === 'cancelled') continue;
                    
                    OrderStatus::create([
                        'order_id' => $order->id,
                        'status' => $orderStatuses[$j],
                        'created_at' => $statusTime,
                        'updated_at' => $statusTime,
                    ]);
                    $statusTime = $statusTime->copy()->addMinutes(rand(5, 15));
                }

                // Create Delivery Assignment for orders that are out for delivery or delivered
                if (in_array($status, ['out_for_delivery', 'delivered']) && $deliveryPartners->isNotEmpty()) {
                    $partner = $deliveryPartners->random();
                    
                    // Valid delivery assignment statuses: 'assigned', 'accepted', 'rejected', 'picked_up', 'delivered', 'cancelled'
                    DeliveryAssignment::create([
                        'order_id' => $order->id,
                        'tenant_id' => $restaurant->tenant_id,
                        'partner_id' => $partner->id,
                        'pickup_latitude' => $restaurant->latitude,
                        'pickup_longitude' => $restaurant->longitude,
                        'delivery_latitude' => $address->latitude,
                        'delivery_longitude' => $address->longitude,
                        'estimated_distance_km' => rand(2, 10),
                        'estimated_duration_minutes' => rand(20, 45),
                        'delivery_fee' => $deliveryFee,
                        'tip_amount' => rand(0, 1) ? rand(20, 50) : 0,
                        'status' => $status === 'delivered' ? 'delivered' : 'accepted',
                        'assigned_at' => $orderDate->copy()->addMinutes(20),
                        'accepted_at' => $orderDate->copy()->addMinutes(22),
                        'picked_up_at' => $orderDate->copy()->addMinutes(35),
                        'delivered_at' => $status === 'delivered' ? $orderDate->copy()->addMinutes(rand(45, 60)) : null,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }

                $orderCount++;
            }
        }

        $this->command->info("✓ Created {$orderCount} Orders with Items and Delivery Assignments");
    }
}
