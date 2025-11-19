<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\TenantScoped;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int|null $location_admin_id
 * @property int|null $user_id
 * @property string $restaurant_name
 * @property string|null $contact_person_name
 * @property string $slug
 * @property string|null $description
 * @property string|null $cuisine_type
 * @property string $address
 * @property float $latitude
 * @property float $longitude
 * @property string $city
 * @property string $state
 * @property string $postal_code
 * @property string $phone
 * @property string $email
 * @property string|null $website_url
 * @property string|null $image_url
 * @property string|null $cover_image_url
 * @property int $delivery_radius_km
 * @property float $minimum_order_amount
 * @property float $base_delivery_fee
 * @property float $restaurant_commission_percentage
 * @property int $estimated_delivery_time
 * @property float $tax_percentage
 * @property bool $is_open
 * @property bool $accepts_orders
 * @property bool $is_paused
 * @property string $status
 * @property float|null $average_rating
 * @property int $total_reviews
 * @property int $total_orders
 * @property \Carbon\Carbon|null $approved_at
 * @property int|null $approved_by
 * @property array|null $business_hours
 * @property string|null $special_instructions
 * @property bool $is_featured
 * @property bool $setup_completed
 * @property int $onboarding_step
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read string|null $full_image_url
 * @property-read string|null $full_cover_image_url
 * @property-read string $status_label
 * @property-read Tenant $tenant
 * @property-read User|null $locationAdmin
 * @property-read User|null $user
 * @property-read User|null $approvedByUser
 * @property-read \Illuminate\Database\Eloquent\Collection|RestaurantDocument[] $documents
 * @property-read \Illuminate\Database\Eloquent\Collection|RestaurantWorkingHour[] $workingHours
 * @property-read \Illuminate\Database\Eloquent\Collection|Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|MenuCategory[] $menuCategories
 * @property-read \Illuminate\Database\Eloquent\Collection|MenuItem[] $menuItems
 * @property-read \Illuminate\Database\Eloquent\Collection|RestaurantBanner[] $banners
 * @property-read \Illuminate\Database\Eloquent\Collection|Review[] $reviews
 * @property-read City|null $cityRelation
 * @property-read State|null $stateRelation
 */
class Restaurant extends BaseTenantModel
{
    use TracksActivity;

    /**
     * Get all reviews for the restaurant (polymorphic relation).
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    use HasFactory, Searchable, SoftDeletes, TenantScoped;

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_SUSPENDED = 'suspended';

    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'tenant_id',
        'location_admin_id',
        'user_id',
        'restaurant_name',
        'contact_person_name',
        'slug',
        'description',
        'cuisine_type',
        'address',
        'latitude',
        'longitude',
        'city',
        'state',
        'postal_code',
        'phone',
        'email',
        'website_url',
        'image_url',
        'cover_image_url',
        'delivery_radius_km',
        'minimum_order_amount',
        'base_delivery_fee',
        'restaurant_commission_percentage',
        'estimated_delivery_time',
        'tax_percentage',
        'is_open',
        'accepts_orders',
        'is_paused',
        'status',
        'average_rating',
        'total_reviews',
        'total_orders',
        'approved_at',
        'approved_by',
        'business_hours',
        'special_instructions',
        'is_featured',
        'setup_completed',
        'onboarding_step',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'delivery_radius_km' => 'integer',
        'minimum_order_amount' => 'decimal:2',
        'base_delivery_fee' => 'decimal:2',
        'restaurant_commission_percentage' => 'decimal:2',
        'estimated_delivery_time' => 'integer',
        'tax_percentage' => 'decimal:2',
        'is_open' => 'boolean',
        'accepts_orders' => 'boolean',
        'is_paused' => 'boolean',
        'average_rating' => 'decimal:1',
        'total_reviews' => 'integer',
        'total_orders' => 'integer',
        'approved_at' => 'datetime',
        'is_featured' => 'boolean',
        'setup_completed' => 'boolean',
        'business_hours' => 'array',
    ];

    protected array $searchable = [
        'restaurant_name',
        'slug',
        'description',
        'cuisine_type',
        'address',
        'city',
        'state',
        'postal_code',
        'phone',
        'email',
    ];

    protected $appends = [
        'full_image_url',
        'full_cover_image_url',
        'status_label',
    ];

    // Boot method for auto-generating slug and setting defaults
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($restaurant) {
            if (empty($restaurant->slug)) {
                $restaurant->slug = \Str::slug($restaurant->restaurant_name);
            }

            if (empty($restaurant->status)) {
                $restaurant->status = self::STATUS_PENDING;
            }

            if (empty($restaurant->onboarding_step)) {
                $restaurant->onboarding_step = 1;
            }
        });
    }

    // ------------------------
    // Relationships
    // ------------------------

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function locationAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'location_admin_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RestaurantDocument::class);
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(RestaurantWorkingHour::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function banners(): HasMany
    {
        return $this->hasMany(RestaurantBanner::class);
    }

    /**
     * Check if this is the first restaurant for the tenant
     */
    public function isFirstRestaurantForTenant(): bool
    {
        return Restaurant::where('tenant_id', $this->tenant_id)->count() === 1;
    }

    // ------------------------
    // Scopes
    // ------------------------

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeAcceptingOrders($query)
    {
        return $query->where('accepts_orders', true);
    }

    public function scopeNotPaused($query)
    {
        return $query->where('is_paused', false);
    }

    public function scopeAcceptingNewOrders($query)
    {
        return $query->where('accepts_orders', true)
            ->where('is_paused', false);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
            ->where('is_open', true)
            ->where('accepts_orders', true)
            ->where('is_paused', false);
    }

    // ------------------------
    // Accessors
    // ------------------------

    public function getFullImageUrlAttribute(): ?string
    {
        return $this->image_url ? Storage::url($this->image_url) : null;
    }

    public function getFullCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_url ? Storage::url($this->cover_image_url) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_INACTIVE => 'Inactive',
            default => 'Unknown'
        };
    }

    // ------------------------
    // Helper Methods
    // ------------------------

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPaused(): bool
    {
        return $this->is_paused === true;
    }

    public function canAcceptNewOrders(): bool
    {
        return $this->isApproved()
            && $this->is_open
            && $this->accepts_orders
            && ! $this->is_paused;
    }

    public function togglePause(): bool
    {
        return $this->update([
            'is_paused' => ! $this->is_paused,
        ]);
    }

    public function approve(User $approvedBy): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $approvedBy->id,
        ]);
    }

    public function reject(User $rejectedBy): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    public function cityRelation(): BelongsTo
    {
        // 'city' field is actually city_id, but named 'city' in DB
        return $this->belongsTo(City::class, 'city', 'id');
    }

    public function stateRelation(): BelongsTo
    {
        // 'state' field is actually state_id, but named 'state' in DB
        return $this->belongsTo(State::class, 'state', 'id');
    }
}
