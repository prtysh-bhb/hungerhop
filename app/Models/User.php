<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\TenantScoped;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property int|null $tenant_id
 * @property int|null $restaurant_id
 * @property string $first_name
 * @property string $last_name
 * @property string $role
 * @property string $status
 * @property \Carbon\Carbon|null $phone_verified_at
 * @property \Carbon\Carbon|null $last_login_at
 * @property \Carbon\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read string $full_name
 * @property-read Tenant|null $tenant
 * @property-read Restaurant|null $restaurant
 * @property-read \Illuminate\Database\Eloquent\Collection|Restaurant[] $restaurants
 * @property-read \Illuminate\Database\Eloquent\Collection|Restaurant[] $managedRestaurants
 */
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Searchable, SoftDeletes, TenantScoped, TracksActivity;

    protected $fillable = [
        'email',
        'phone',
        'password',
        'tenant_id',
        'restaurant_id',
        'first_name',
        'last_name',
        'role',
        'status',
        'phone_verified_at',
        'last_login_at',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $appends = ['name'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected array $Searchable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'role',
        'status',
    ];

    // ========== JWT METHODS (ADD THESE) ==========
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'status' => $this->status,
            'tenant_id' => $this->tenant_id,
        ];
    }
    // ========== END JWT METHODS ==========

    // Keep all your existing relationships and methods below

    /**
     * Get the tenant that owns the user.
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    /**
     * Get the restaurant that the user belongs to.
     */
    public function restaurant()
    {
        return $this->belongsTo(\App\Models\Restaurant::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the customer profile associated with the user.
     */
    public function customerProfile()
    {
        return $this->hasOne(\App\Models\CustomerProfile::class);
    }

    /**
     * Get the addresses associated with the user (through customer profile).
     */
    public function addresses()
    {
        return $this->hasManyThrough(
            \App\Models\CustomerAddress::class,
            \App\Models\CustomerProfile::class,
            'user_id', // Foreign key on customer_profiles table
            'customer_id', // Foreign key on customer_addresses table
            'id', // Local key on users table
            'id' // Local key on customer_profiles table
        );
    }

    /**
     * Get the orders associated with the user (when user is a customer).
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'customer_id');
    }

    /**
     * Get the user's primary address.
     */
    public function primaryAddress()
    {
        return $this->hasOne(\App\Models\CustomerAddress::class, 'customer_id')
            ->where('is_default', true);
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Check if user is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is suspended.
     */
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if user is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Scope for customers only
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
