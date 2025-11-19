<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MenuTemplate extends BaseTenantModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'template_name', 'description',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function categories()
    {
        return $this->hasMany(MenuCategory::class);
    }
}
