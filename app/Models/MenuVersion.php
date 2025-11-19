<?php

namespace App\Models;

class MenuVersion extends BaseTenantModel
{
    protected $fillable = ['restaurant_id', 'version_name', 'snapshot'];

    protected $casts = [
        'snapshot' => 'array',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
