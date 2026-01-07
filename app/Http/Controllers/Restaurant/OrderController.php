<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use App\Models\DeliveryPartner;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Restaurant;
use app\Models\User;
use App\Scopes\TenantScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function ShowList(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $restaurantIds = Restaurant::where('tenant_id', $tenantId)->pluck('id');

        // Get all orders with relations
        $orders = Order::with(['customer.user', 'restaurant', 'deliveryAddress'])
            // ->where('status', '!=', 'draft')
            ->whereIn('restaurant_id', $restaurantIds)->get();

        return view('pages.restaurant_staff.order.index', compact('orders'));
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status' => 'required|string',
        ]);

        $newStatus = $request->input('status');
        $previousStatus = $order->status;
        $order->status = $newStatus;
        $order->save();

        // Insert into order_statuses table
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => $order->status,
        ]);

        // Auto-assign delivery partner when status changes to ready_for_pickup or ready_for_delivery
        $assignmentMessage = null;
        $shouldAssign = ($newStatus === 'ready_for_pickup' && $previousStatus !== 'ready_for_pickup') ||
                        ($newStatus === 'ready_for_delivery' && $previousStatus !== 'ready_for_delivery');

        if ($shouldAssign) {
            // Check if already assigned
            $existingAssignment = DeliveryAssignment::where('order_id', $order->id)
                ->whereIn('status', ['assigned', 'accepted', 'picked_up'])
                ->first();
            if (! $existingAssignment) {
                $assignmentResult = $this->assignDeliveryPartner($order);
                if ($assignmentResult['success']) {
                    $assignmentMessage = $assignmentResult['message'];

                    $order->status = 'assigned_to_delivery';
                    $order->save();
                    // Log the status change
                    OrderStatus::create([
                        'order_id' => $order->id,
                        'status' => 'assigned_to_delivery',
                    ]);
                } else {
                    $assignmentMessage = 'Warning: '.$assignmentResult['message'];
                }
            }
        }

        $successMessage = 'Order status updated successfully.';
        if ($assignmentMessage) {
            $successMessage .= ' '.$assignmentMessage;
        }

        return redirect()->route('restaurant.order.details', $order->id)
            ->with('success', $successMessage);
    }

    public function ShowDetails($id)
    {
        $order = Order::with([
            'restaurant',
            'customer.user',
            'deliveryAddress',
            'items.menuItem.category',
        ])->findOrFail($id);

        return view('pages.restaurant_staff.order.show', compact('order'));
    }

    /**
     * Assign nearest available delivery partner to the order
     */
    private function assignDeliveryPartner(Order $order): array
    {
        // Load order relationships
        $order->load(['deliveryAddress', 'restaurant']);

        if (! $order->deliveryAddress || ! $order->restaurant) {
            Log::warning("Order {$order->id}: Missing delivery address or restaurant for assignment");

            return [
                'success' => false,
                'message' => 'Missing delivery address or restaurant information',
                'delivery_partner' => null,
            ];
        }

        $customer_lat = (float) $order->deliveryAddress->latitude;
        $customer_lng = (float) $order->deliveryAddress->longitude;
        $restaurant_lat = (float) $order->restaurant->latitude;
        $restaurant_lng = (float) $order->restaurant->longitude;

        // Validate coordinates
        if (abs($customer_lat) > 90 || abs($customer_lng) > 180 ||
            abs($restaurant_lat) > 90 || abs($restaurant_lng) > 180) {
            Log::warning("Order {$order->id}: Invalid coordinates detected");

            return [
                'success' => false,
                'message' => 'Invalid coordinates detected',
                'delivery_partner' => null,
            ];
        }

        // Calculate distance between restaurant and customer
        $restaurantToCustomerDistance = $this->calculateDistance(
            $restaurant_lat, $restaurant_lng,
            $customer_lat, $customer_lng
        );

        Log::info("Order {$order->id}: Restaurant to Customer distance: {$restaurantToCustomerDistance} km");

        if ($restaurantToCustomerDistance > 15) {
            Log::warning("Order {$order->id}: Delivery destination too far ({$restaurantToCustomerDistance} km)");

            return [
                'success' => false,
                'message' => 'Delivery destination is too far. Maximum allowed distance is 15km.',
                'delivery_partner' => null,
            ];
        }

        // Check delivery zones - with fallback for distance-based fee
        $deliveryZone = DeliveryZone::getDeliveryZoneForLocation(
            $customer_lat,
            $customer_lng,
            $order->restaurant_id,
            $order->tenant_id
        );

        // Calculate delivery fee
        if ($deliveryZone) {
            $deliveryFee = $deliveryZone->delivery_fee;
            $estimatedDeliveryTime = $deliveryZone->estimated_delivery_time;
            Log::info("Order {$order->id}: Delivery Zone: {$deliveryZone->zone_name}, Fee: {$deliveryFee}");
        } else {
            // Fallback: Calculate delivery fee based on distance
            $deliveryFee = $this->calculateDeliveryFee($restaurantToCustomerDistance);
            $estimatedDeliveryTime = $this->calculateEstimatedDeliveryTime($restaurantToCustomerDistance);
            Log::info("Order {$order->id}: No delivery zone found. Using distance-based fee: {$deliveryFee}");
        }

        // Find nearest available delivery partner
        $partners = DeliveryPartner::where('is_available', true)
            ->where('is_online', true)
            ->where('status', 'approved')
            ->get();

        Log::info("Order {$order->id}: Found {$partners->count()} strictly available delivery partners");

        // If no partners found with strict criteria, try relaxed search
        if ($partners->isEmpty()) {
            $partners = DeliveryPartner::where('status', 'approved')->get();
        }

        $nearest = null;
        $minDistance = null;

        foreach ($partners as $partner) {
            // Check if partner has valid coordinates
            if ($partner->current_latitude === null || $partner->current_longitude === null) {
                Log::warning("Order {$order->id}: Partner {$partner->id} has no coordinates. Skipping.");

                continue;
            }

            if (abs($partner->current_latitude) > 90 || abs($partner->current_longitude) > 180) {
                Log::warning("Order {$order->id}: Partner {$partner->id} has invalid coordinates. Skipping.");

                continue;
            }

            $partner_lat = (float) $partner->current_latitude;
            $partner_lng = (float) $partner->current_longitude;
            $distance = $this->calculateDistance(
                $restaurant_lat, $restaurant_lng,
                $partner_lat, $partner_lng
            );
            if ($minDistance === null || $distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $partner;
            }
        }

        if (! $nearest) {

            return [
                'success' => false,
                'message' => 'No available delivery partner found. Order will be assigned when a partner becomes available.',
                'delivery_partner' => null,
            ];
        }

        try {
            // Create delivery assignment
            $assignment = DeliveryAssignment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id,
                'partner_id' => $nearest->id,
                'pickup_latitude' => $restaurant_lat,
                'pickup_longitude' => $restaurant_lng,
                'delivery_latitude' => $customer_lat,
                'delivery_longitude' => $customer_lng,
                'estimated_distance_km' => $restaurantToCustomerDistance,
                'estimated_duration_minutes' => $this->getEstimatedMinutes($estimatedDeliveryTime),
                'delivery_fee' => $deliveryFee,
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);

            // Update order with delivery fee
            $order->delivery_fee = $deliveryFee;
            // Recalculate total amount with delivery fee
            $order->total_amount = ($order->subtotal ?? 0) + ($order->tax_amount ?? 0) + $deliveryFee - ($order->discount_amount ?? 0);
            $order->save();

            // Load partner's user details
            $nearest->load('user');
            $partnerName = $nearest->user ? $nearest->user->first_name.' '.$nearest->user->last_name : 'Unknown';

            Log::info("Order {$order->id}: Assigned to delivery partner {$nearest->id} ({$partnerName})");

            return [
                'success' => true,
                'message' => "Delivery partner '{$partnerName}' has been assigned to this order.",
                'delivery_partner' => [
                    'id' => $nearest->id,
                    'name' => $partnerName,
                    'vehicle_type' => $nearest->vehicle_type,
                ],
            ];

        } catch (\Exception $e) {
            Log::error("Order {$order->id}: Failed to assign delivery partner - ".$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to assign delivery partner: '.$e->getMessage(),
                'delivery_partner' => null,
            ];
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }

    /**
     * Calculate delivery fee based on distance
     */
    private function calculateDeliveryFee($distance)
    {
        if ($distance <= 3) {
            return 20; // ₹20 for 0-3km
        } elseif ($distance <= 6) {
            return 40; // ₹40 for 3-6km
        } elseif ($distance <= 10) {
            return 60; // ₹60 for 6-10km
        } else {
            return 80; // ₹80 for 10-15km
        }
    }

    /**
     * Calculate estimated delivery time based on distance
     */
    private function calculateEstimatedDeliveryTime($distance)
    {
        $travelTimeMinutes = ($distance / 25) * 60; // 25 km/h average speed
        $totalMinutes = ceil($travelTimeMinutes + 10); // +10 mins for pickup

        if ($totalMinutes <= 20) {
            return '15-20 mins';
        } elseif ($totalMinutes <= 30) {
            return '25-30 mins';
        } elseif ($totalMinutes <= 45) {
            return '35-45 mins';
        } else {
            return '45-60 mins';
        }
    }

    /**
     * Convert estimated time string to minutes
     */
    private function getEstimatedMinutes($timeString)
    {
        // Extract max minutes from string like "25-30 mins"
        preg_match('/(\d+)\s*mins?$/i', $timeString, $matches);

        return isset($matches[1]) ? (int) $matches[1] : 30;
    }

    // Remove duplicate and misplaced method, and define getDeliveryPartnerForOrder only once outside of ShowDetails

    /**
     * Get the delivery partner user for a given order (or null if not assigned).
     */
    public static function getDeliveryPartnerForOrder($orderId)
    {
        $deliveryAssignment = DeliveryAssignment::where('order_id', $orderId)->first();
        if (! $deliveryAssignment) {
            return null;
        }
        $deliveryPartner = DeliveryPartner::find($deliveryAssignment->partner_id);
        if (! $deliveryPartner) {
            return null;
        }
        // Use withoutGlobalScope to bypass TenantScope - delivery partners have NULL tenant_id
        $user = User::withoutGlobalScope(TenantScope::class)
            ->find($deliveryPartner->user_id);
        if (! $user) {
            return null;
        }

        return [
            'user' => $user,
            'partner' => $deliveryPartner,
            'assignment' => $deliveryAssignment,
        ];
    }
}
