<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemService
{
    /**
     * Get paginated menu items with filters
     */
    public function getMenuItems(array $filters = []): Collection
    {
        $user = Auth::user();

        $query = MenuItem::with(['category'])
            ->where('tenant_id', $user->tenant_id);

        // Apply filters
        if (! empty($filters['menu_category_id'])) {
            $query->where('menu_category_id', $filters['menu_category_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ingredients', 'like', "%{$search}%")
                    ->orWhere('allergens', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_available'])) {
            $query->where('is_available', $filters['is_available']);
        }

        if (isset($filters['is_popular'])) {
            $query->where('is_popular', $filters['is_popular']);
        }

        if (isset($filters['is_vegetarian'])) {
            $query->where('is_vegetarian', $filters['is_vegetarian']);
        }

        if (isset($filters['is_vegan'])) {
            $query->where('is_vegan', $filters['is_vegan']);
        }

        if (isset($filters['is_gluten_free'])) {
            $query->where('is_gluten_free', $filters['is_gluten_free']);
        }

        return $query->orderBy('sort_order', 'asc')
            ->orderBy('item_name', 'asc')
            ->get();
    }

    /**
     * Create a new menu item
     */
    public function create(array $data): MenuItem
    {
        $user = Auth::user();

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            $data['image_url'] = $this->handleImageUpload($data['image']);
            unset($data['image']);
        }

        // Set tenant and restaurant information
        $data['tenant_id'] = $user->tenant_id;
        $data['restaurant_id'] = $user->restaurant_id ?? null;

        // Ensure menu_category_id is set and mapped to the correct field in DB
        if (isset($data['menu_category_id'])) {
            $data['menu_category_id'] = (int) $data['menu_category_id'];
        }

        // Set boolean fields properly (only those present in DB)
        $data['is_available'] = isset($data['is_available']) ? (bool) $data['is_available'] : true;
        $data['is_vegetarian'] = isset($data['is_vegetarian']) ? (bool) $data['is_vegetarian'] : false;
        $data['is_vegan'] = isset($data['is_vegan']) ? (bool) $data['is_vegan'] : false;
        $data['is_gluten_free'] = isset($data['is_gluten_free']) ? (bool) $data['is_gluten_free'] : false;
        $data['is_popular'] = isset($data['is_popular']) ? (bool) $data['is_popular'] : false;

        // Handle datetime fields - convert time to datetime
        if (isset($data['available_from']) && $data['available_from']) {
            // If it's just a time (HH:MM), convert to today's date with that time
            if (preg_match('/^\d{2}:\d{2}$/', $data['available_from'])) {
                $data['available_from'] = date('Y-m-d').' '.$data['available_from'].':00';
            }
        }

        if (isset($data['available_until']) && $data['available_until']) {
            // If it's just a time (HH:MM), convert to today's date with that time
            if (preg_match('/^\d{2}:\d{2}$/', $data['available_until'])) {
                $data['available_until'] = date('Y-m-d').' '.$data['available_until'].':00';
            }
        }

        // Handle tags field - remove it entirely if causing constraint issues
        if (isset($data['tags'])) {
            unset($data['tags']);
        }

        // Set default values (only those present in DB)
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['preparation_time'] = $data['preparation_time'] ?? 15;
        $data['total_sales'] = 0;
        $data['total_reviews'] = 0;
        $data['average_rating'] = 0.00;

        return MenuItem::create($data);
    }

    /**
     * Update an existing menu item
     */
    public function update(MenuItem $menuItem, array $data): MenuItem
    {
        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image if exists
            if ($menuItem->image_url) {
                $this->deleteImage($menuItem->image_url);
            }
            $data['image_url'] = $this->handleImageUpload($data['image']);
            unset($data['image']);
        }

        // Handle datetime fields - convert time to datetime
        if (isset($data['available_from']) && $data['available_from']) {
            // If it's just a time (HH:MM), convert to today's date with that time
            if (preg_match('/^\d{2}:\d{2}$/', $data['available_from'])) {
                $data['available_from'] = date('Y-m-d').' '.$data['available_from'].':00';
            }
        }

        if (isset($data['available_until']) && $data['available_until']) {
            // If it's just a time (HH:MM), convert to today's date with that time
            if (preg_match('/^\d{2}:\d{2}$/', $data['available_until'])) {
                $data['available_until'] = date('Y-m-d').' '.$data['available_until'].':00';
            }
        }

        // Handle tags field - remove it entirely if causing constraint issues
        if (isset($data['tags'])) {
            unset($data['tags']);
        }

        // Set boolean fields properly (only those present in DB)
        $data['is_available'] = isset($data['is_available']) ? (bool) $data['is_available'] : false;
        $data['is_vegetarian'] = isset($data['is_vegetarian']) ? (bool) $data['is_vegetarian'] : false;
        $data['is_vegan'] = isset($data['is_vegan']) ? (bool) $data['is_vegan'] : false;
        $data['is_gluten_free'] = isset($data['is_gluten_free']) ? (bool) $data['is_gluten_free'] : false;
        $data['is_popular'] = isset($data['is_popular']) ? (bool) $data['is_popular'] : false;

        $menuItem->update($data);

        return $menuItem->fresh();
    }

    /**
     * Delete a menu item
     */
    public function delete(MenuItem $menuItem): bool
    {
        // Delete associated image
        if ($menuItem->image_url) {
            $this->deleteImage($menuItem->image_url);
        }

        return $menuItem->delete();
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($image): string
    {
        $filename = time().'_'.Str::random(10).'.'.$image->getClientOriginalExtension();
        $path = $image->storeAs('menu_items', $filename, 'public');

        return asset('storage/'.$path);
    }

    /**
     * Delete image from storage
     */
    private function deleteImage(string $imageUrl): void
    {
        $path = str_replace(asset('storage/'), '', $imageUrl);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Toggle menu item availability
     */
    public function toggleAvailability(MenuItem $menuItem): MenuItem
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return $menuItem->fresh();
    }

    /**
     * Update inventory count
     */
    public function updateInventory(MenuItem $menuItem, int $count): MenuItem
    {
        $menuItem->update(['inventory_count' => $count]);

        return $menuItem->fresh();
    }

    /**
     * Mark item as popular/not popular
     */
    public function togglePopular(MenuItem $menuItem): MenuItem
    {
        $menuItem->update(['is_popular' => ! $menuItem->is_popular]);

        return $menuItem->fresh();
    }

    /**
     * Get menu items by category
     */
    public function getByCategory(int $categoryId): Collection
    {
        $user = Auth::user();

        return MenuItem::where('tenant_id', $user->tenant_id)
            ->where('menu_category_id', $categoryId)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->orderBy('item_name')
            ->get();
    }

    /**
     * Get popular menu items
     */
    public function getPopularItems(int $limit = 10): Collection
    {
        $user = Auth::user();

        return MenuItem::where('tenant_id', $user->tenant_id)
            ->where('is_popular', true)
            ->where('is_available', true)
            ->orderBy('total_sales', 'desc')
            ->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search menu items
     */
    public function search(string $query): Collection
    {
        $user = Auth::user();

        return MenuItem::where('tenant_id', $user->tenant_id)
            ->where('is_available', true)
            ->where(function ($q) use ($query) {
                $q->where('item_name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('ingredients', 'like', "%{$query}%")
                    ->orWhere('tags', 'like', "%{$query}%");
            })
            ->orderBy('item_name')
            ->get();
    }
}
