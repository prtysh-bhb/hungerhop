<?php

namespace App\Traits;

trait TracksActivity
{
    protected static function bootTracksActivity()
    {
        // Track when model is created
        static::created(function ($model) {
            $model->logActivity('created', null, null, $model->getDisplayName().' was created');
        });

        // Track when model is updated
        static::updated(function ($model) {
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            foreach ($changes as $field => $newValue) {
                if ($field !== 'updated_at') { // Skip timestamp fields
                    $oldValue = $original[$field] ?? null;

                    // Create human-readable field names
                    $fieldName = $model->getHumanFieldName($field);

                    if ($oldValue !== $newValue) {
                        $description = $model->getDisplayName()." {$fieldName} changed from '{$oldValue}' to '{$newValue}'";
                        $model->logActivity('updated', $field, $oldValue, $description, $newValue);
                    }
                }
            }
        });

        // Track when model is deleted
        static::deleted(function ($model) {
            $model->logActivity('deleted', null, null, $model->getDisplayName().' was deleted');
        });
    }

    protected function logActivity($event, $field = null, $oldValue = null, $description = null, $newValue = null)
    {
        // Store in cache or session for the RightSidebar to pick up
        $activity = [
            'type' => class_basename($this),
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'event' => $event,
            'field_name' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'message' => $description,
            'user' => auth()->user()->first_name ?? 'System',
            'time' => now(),
            'border_class' => $this->getBorderClass(),
        ];

        // Store in cache with expiry
        $cacheKey = 'recent_activities';
        $activities = cache()->get($cacheKey, collect());

        $activities->prepend($activity);
        $activities = $activities->take(50); // Keep last 50 activities

        cache()->put($cacheKey, $activities, now()->addHours(24));
    }

    protected function getDisplayName()
    {
        // Try common name fields
        if (isset($this->name)) {
            return $this->name;
        }
        if (isset($this->title)) {
            return $this->title;
        }
        if (isset($this->first_name) && isset($this->last_name)) {
            return $this->first_name.' '.$this->last_name;
        }
        if (isset($this->first_name)) {
            return $this->first_name;
        }

        return class_basename($this).' #'.$this->id;
    }

    protected function getHumanFieldName($field)
    {
        $fieldMappings = [
            'name' => 'name',
            'status' => 'status',
            'email' => 'email',
            'phone' => 'phone',
            'address' => 'address',
            'description' => 'description',
            'price' => 'price',
            'is_active' => 'active status',
            'is_available' => 'availability',
            'category_id' => 'category',
            'restaurant_id' => 'restaurant',
            'user_id' => 'user',
            'first_name' => 'first name',
            'last_name' => 'last name',
        ];

        return $fieldMappings[$field] ?? str_replace('_', ' ', $field);
    }

    protected function getBorderClass()
    {
        $modelBorderClasses = [
            'MenuCategory' => 'border-primary',
            'MenuItem' => 'border-success',
            'Restaurant' => 'border-info',
            'Tenant' => 'border-warning',
            'User' => 'border-danger',
            'DeliveryPartner' => 'border-secondary',
            'Order' => 'border-primary',
        ];

        return $modelBorderClasses[class_basename($this)] ?? 'border-primary';
    }
}
