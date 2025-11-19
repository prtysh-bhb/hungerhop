<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryZone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'restaurant_id', 'tenant_id', 'zone_name', 'zone_polygon',
        'delivery_fee', 'minimum_order_amount', 'estimated_delivery_time', 'is_active',
    ];

    protected $casts = [
        'zone_polygon' => 'array',
        'delivery_fee' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function isPointInZone($latitude, $longitude)
    {
        if (! $this->zone_polygon || count($this->zone_polygon) < 3) {
            return false;
        }

        $polygon = $this->zone_polygon;
        $x = (float) $longitude;
        $y = (float) $latitude;
        $inside = false;

        $j = count($polygon) - 1;
        for ($i = 0; $i < count($polygon); $i++) {
            $xi = (float) $polygon[$i]['lng'];
            $yi = (float) $polygon[$i]['lat'];
            $xj = (float) $polygon[$j]['lng'];
            $yj = (float) $polygon[$j]['lat'];

            if ((($yi > $y) !== ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi)) {
                $inside = ! $inside;
            }
            $j = $i;
        }

        return $inside;
    }

    public static function getDeliveryZoneForLocation($latitude, $longitude, $restaurantId, $tenantId = null)
    {
        $zones = self::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->get();

        foreach ($zones as $zone) {
            if ($zone->isPointInZone($latitude, $longitude)) {
                return $zone;
            }
        }

        return null;
    }

    public static function getDeliveryFeeForLocation($latitude, $longitude, $restaurantId, $tenantId = null)
    {
        $zone = self::getDeliveryZoneForLocation($latitude, $longitude, $restaurantId, $tenantId);

        return $zone ? $zone->delivery_fee : null;
    }

    public static function getEstimatedDeliveryTime($latitude, $longitude, $restaurantId, $tenantId = null)
    {
        $zone = self::getDeliveryZoneForLocation($latitude, $longitude, $restaurantId, $tenantId);

        return $zone ? $zone->estimated_delivery_time : null;
    }
}
