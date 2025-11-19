<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MenuBulkController extends Controller
{
    /**
     * Display bulk operations page
     */
    public function index(): View
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            abort(403, 'Access denied. User not properly configured.');
        }

        $categories = MenuCategory::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.restaurant_staff.menu_bulk_operations', compact('categories'));
    }

    /**
     * Bulk update menu item prices
     */
    public function bulkUpdatePrices(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.price' => 'required|numeric|min:0|max:99999.99',
        ]);

        $updatedCount = 0;
        $operationId = uniqid('bulk_price_update_');

        DB::transaction(function () use ($validated, $user, &$updatedCount, $operationId) {
            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::where('id', $itemData['id'])
                    ->where('tenant_id', $user->tenant_id)
                    ->first();

                if ($menuItem) {
                    $menuItem->update([
                        'base_price' => $itemData['price'],
                        'bulk_operation_id' => $operationId,
                        'bulk_updated_at' => Carbon::now(),
                    ]);
                    $updatedCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully updated prices for {$updatedCount} menu items.",
            'operation_id' => $operationId,
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Bulk update menu item availability
     */
    public function bulkUpdateAvailability(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:menu_items,id',
            'is_available' => 'required|boolean',
        ]);

        $updatedCount = 0;
        $operationId = uniqid('bulk_availability_');

        DB::transaction(function () use ($validated, $user, &$updatedCount, $operationId) {
            foreach ($validated['item_ids'] as $itemId) {
                $menuItem = MenuItem::where('id', $itemId)
                    ->where('tenant_id', $user->tenant_id)
                    ->first();

                if ($menuItem) {
                    $menuItem->update([
                        'is_available' => $validated['is_available'],
                        'bulk_operation_id' => $operationId,
                        'bulk_updated_at' => Carbon::now(),
                    ]);
                    $updatedCount++;
                }
            }
        });

        $status = $validated['is_available'] ? 'available' : 'unavailable';

        return response()->json([
            'success' => true,
            'message' => "Successfully marked {$updatedCount} menu items as {$status}.",
            'operation_id' => $operationId,
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Bulk update menu item categories
     */
    public function bulkUpdateCategories(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:menu_items,id',
            'menu_category_id' => 'required|exists:menu_categories,id',
        ]);

        // Verify the category belongs to the user's tenant
        $category = MenuCategory::where('id', $validated['menu_category_id'])
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (! $category) {
            return response()->json(['error' => 'Invalid category.'], 400);
        }

        $updatedCount = 0;
        $operationId = uniqid('bulk_category_');

        DB::transaction(function () use ($validated, $user, &$updatedCount, $operationId) {
            foreach ($validated['item_ids'] as $itemId) {
                $menuItem = MenuItem::where('id', $itemId)
                    ->where('tenant_id', $user->tenant_id)
                    ->first();

                if ($menuItem) {
                    $menuItem->update([
                        'menu_category_id' => $validated['menu_category_id'],
                        'bulk_operation_id' => $operationId,
                        'bulk_updated_at' => Carbon::now(),
                    ]);
                    $updatedCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully moved {$updatedCount} menu items to {$category->category_name}.",
            'operation_id' => $operationId,
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Bulk delete menu items
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:menu_items,id',
            'confirm_delete' => 'required|boolean|accepted',
        ]);

        $deletedCount = 0;

        DB::transaction(function () use ($validated, $user, &$deletedCount) {
            foreach ($validated['item_ids'] as $itemId) {
                $menuItem = MenuItem::where('id', $itemId)
                    ->where('tenant_id', $user->tenant_id)
                    ->first();

                if ($menuItem) {
                    // Delete associated image
                    if ($menuItem->image_url) {
                        $path = str_replace(asset('storage/'), '', $menuItem->image_url);
                        if (\Storage::disk('public')->exists($path)) {
                            \Storage::disk('public')->delete($path);
                        }
                    }

                    $menuItem->delete();
                    $deletedCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} menu items.",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Bulk update dietary information
     */
    public function bulkUpdateDietary(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:menu_items,id',
            'dietary_updates' => 'required|array',
            'dietary_updates.is_vegetarian' => 'nullable|boolean',
            'dietary_updates.is_vegan' => 'nullable|boolean',
            'dietary_updates.is_gluten_free' => 'nullable|boolean',
        ]);

        $updatedCount = 0;
        $operationId = uniqid('bulk_dietary_');

        DB::transaction(function () use ($validated, $user, &$updatedCount, $operationId) {
            foreach ($validated['item_ids'] as $itemId) {
                $menuItem = MenuItem::where('id', $itemId)
                    ->where('tenant_id', $user->tenant_id)
                    ->first();

                if ($menuItem) {
                    $updateData = array_filter($validated['dietary_updates'], function ($value) {
                        return $value !== null;
                    });

                    $updateData['bulk_operation_id'] = $operationId;
                    $updateData['bulk_updated_at'] = Carbon::now();

                    $menuItem->update($updateData);
                    $updatedCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully updated dietary information for {$updatedCount} menu items.",
            'operation_id' => $operationId,
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Bulk update inventory settings
     */
    public function bulkUpdateInventory(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:menu_items,id',
            'track_inventory' => 'required|boolean',
            'inventory_count' => 'nullable|integer|min:0',
        ]);

        $updatedCount = 0;
        $operationId = uniqid('bulk_inventory_');

        DB::transaction(function () use ($validated, $user, &$updatedCount, $operationId) {
            foreach ($validated['item_ids'] as $itemId) {
                $menuItem = MenuItem::where('id', $itemId)
                    ->where('tenant_id', $user->tenant_id)
                    ->first();

                if ($menuItem) {
                    $updateData = [
                        'track_inventory' => $validated['track_inventory'],
                        'bulk_operation_id' => $operationId,
                        'bulk_updated_at' => Carbon::now(),
                    ];

                    if (isset($validated['inventory_count'])) {
                        $updateData['inventory_count'] = $validated['inventory_count'];
                    }

                    $menuItem->update($updateData);
                    $updatedCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully updated inventory settings for {$updatedCount} menu items.",
            'operation_id' => $operationId,
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Bulk import menu items from CSV
     */
    public function bulkImport(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'overwrite_existing' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getPathname()));
            $headers = array_shift($csvData); // Remove header row

            $importedCount = 0;
            $skippedCount = 0;
            $operationId = uniqid('bulk_import_');

            DB::transaction(function () use ($csvData, $headers, $user, &$importedCount, &$skippedCount, $operationId, $validated) {
                foreach ($csvData as $row) {
                    if (count($row) !== count($headers)) {
                        $skippedCount++;

                        continue;
                    }

                    $itemData = array_combine($headers, $row);

                    // Check if item already exists
                    $existingItem = MenuItem::where('tenant_id', $user->tenant_id)
                        ->where('item_name', $itemData['item_name'])
                        ->first();

                    if ($existingItem && ! ($validated['overwrite_existing'] ?? false)) {
                        $skippedCount++;

                        continue;
                    }

                    // Prepare item data
                    $menuItemData = [
                        'tenant_id' => $user->tenant_id,
                        'restaurant_id' => $user->restaurant_id ?? null,
                        'item_name' => $itemData['item_name'],
                        'description' => $itemData['description'] ?? '',
                        'base_price' => (float) ($itemData['base_price'] ?? 0),
                        'menu_category_id' => $this->findCategoryId($itemData['category'] ?? '', $user->tenant_id),
                        'is_available' => $this->parseBooleanValue($itemData['is_available'] ?? 'true'),
                        'is_vegetarian' => $this->parseBooleanValue($itemData['is_vegetarian'] ?? 'false'),
                        'is_vegan' => $this->parseBooleanValue($itemData['is_vegan'] ?? 'false'),
                        'is_gluten_free' => $this->parseBooleanValue($itemData['is_gluten_free'] ?? 'false'),
                        'ingredients' => $itemData['ingredients'] ?? '',
                        'allergens' => $itemData['allergens'] ?? '',
                        'preparation_time' => (int) ($itemData['preparation_time'] ?? 15),
                        'bulk_operation_id' => $operationId,
                        'bulk_updated_at' => Carbon::now(),
                    ];

                    if ($existingItem) {
                        $existingItem->update($menuItemData);
                    } else {
                        MenuItem::create($menuItemData);
                    }

                    $importedCount++;
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Import completed. {$importedCount} items imported, {$skippedCount} items skipped.",
                'operation_id' => $operationId,
                'imported_count' => $importedCount,
                'skipped_count' => $skippedCount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export menu items to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        $filters = $request->only(['menu_category_id', 'is_available', 'is_popular']);

        $query = MenuItem::where('tenant_id', $user->tenant_id)
            ->with('category');

        // Apply filters
        if (! empty($filters['menu_category_id'])) {
            $query->where('menu_category_id', $filters['menu_category_id']);
        }

        if (isset($filters['is_available'])) {
            $query->where('is_available', $filters['is_available']);
        }

        if (isset($filters['is_popular'])) {
            $query->where('is_popular', $filters['is_popular']);
        }

        $menuItems = $query->get();

        // Generate CSV content
        $filename = 'menu_items_'.date('Y_m_d_H_i_s').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($menuItems) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'item_name', 'category', 'description', 'base_price', 'display_price',
                'is_available', 'is_vegetarian', 'is_vegan', 'is_gluten_free',
                'ingredients', 'allergens', 'preparation_time', 'sku',
                'inventory_count', 'track_inventory', 'sort_order',
                'total_sales', 'average_rating', 'is_popular',
            ]);

            // CSV data
            foreach ($menuItems as $item) {
                fputcsv($file, [
                    $item->item_name,
                    $item->category->category_name ?? '',
                    $item->description,
                    $item->base_price,
                    $item->display_price,
                    $item->is_available ? 'true' : 'false',
                    $item->is_vegetarian ? 'true' : 'false',
                    $item->is_vegan ? 'true' : 'false',
                    $item->is_gluten_free ? 'true' : 'false',
                    $item->ingredients,
                    $item->allergens,
                    $item->preparation_time,
                    $item->sku,
                    $item->inventory_count,
                    $item->track_inventory ? 'true' : 'false',
                    $item->sort_order,
                    $item->total_sales,
                    $item->average_rating,
                    $item->is_popular ? 'true' : 'false',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Find category ID by name
     */
    private function findCategoryId(string $categoryName, int $tenantId): ?int
    {
        if (empty($categoryName)) {
            return null;
        }

        $category = MenuCategory::where('tenant_id', $tenantId)
            ->where('category_name', 'like', "%{$categoryName}%")
            ->first();

        return $category ? $category->id : null;
    }

    /**
     * Parse boolean values from CSV
     */
    private function parseBooleanValue(string $value): bool
    {
        return in_array(strtolower(trim($value)), ['true', '1', 'yes', 'y', 'on']);
    }
}
