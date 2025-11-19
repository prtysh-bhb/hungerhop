<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        // Only set audit fields if the model has those columns
        static::creating(function ($model) {
            if (Auth::check() && $model->isFillable('created_by')) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && $model->isFillable('updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && $model->usesSoftDeletes() && $model->isFillable('deleted_by')) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class));
    }
}
