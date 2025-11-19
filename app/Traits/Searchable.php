<?php

namespace App\Traits;

trait Searchable
{
    public function scopeSearch($query, $term)
    {
        $columns = property_exists($this, 'searchable') ? $this->searchable : [];

        if (! empty($columns)) {
            $query->where(function ($q) use ($term, $columns) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%'.$term.'%');
                }
            });
        }

        return $query;
    }
}
