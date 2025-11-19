<?php

namespace App\Models;

use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItemReview extends Model
{
    use HasFactory, SoftDeletes, TracksActivity;

    protected $table = 'menu_item_reviews';

    protected $fillable = [
        'order_item_id',
        'customer_id',
        'item_id',
        'rating',
        'review_text',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the order item that this review belongs to
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /**
     * Get the customer who wrote this review
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id');
    }

    /**
     * Get the menu item that this review is for
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }

    // Scopes

    /**
     * Scope to filter by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to filter by minimum rating
     */
    public function scopeMinimumRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope to filter by customer
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter by menu item
     */
    public function scopeByMenuItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    /**
     * Scope to get reviews with images
     */
    public function scopeWithImages($query)
    {
        return $query->whereNotNull('images');
    }

    // Accessors

    /**
     * Get the rating as stars (for display)
     */
    public function getRatingStarsAttribute()
    {
        return str_repeat('★', $this->rating).str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Check if review has images
     */
    public function getHasImagesAttribute()
    {
        return ! empty($this->images);
    }

    // Methods

    /**
     * Get the average rating for a specific menu item
     */
    public static function getAverageRatingForItem($itemId)
    {
        return self::where('item_id', $itemId)->avg('rating');
    }

    /**
     * Get the total number of reviews for a specific menu item
     */
    public static function getTotalReviewsForItem($itemId)
    {
        return self::where('item_id', $itemId)->count();
    }

    /**
     * Get rating distribution for a specific menu item
     */
    public static function getRatingDistributionForItem($itemId)
    {
        return self::where('item_id', $itemId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();
    }
}
