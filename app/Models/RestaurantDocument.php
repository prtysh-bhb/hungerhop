<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\TenantScoped;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantDocument extends BaseTenantModel
{
    use HasFactory, Searchable, SoftDeletes, TenantScoped, TracksActivity;

    protected $fillable = [
        'restaurant_id',
        'tenant_id',
        'document_type',
        'document_path',
        'document_name',
        'original_filename',
        'file_size',
        'mime_type',
        'status',
        'rejection_reason',
        'admin_notes',
        'uploaded_at',
        'reviewed_at',
        'reviewed_by',
        'expires_at',
        'is_verified',
        'metadata',
    ];

    protected array $searchable = [
        'document_type',
        'document_name',
        'original_filename',
        'status',
        'mime_type',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
