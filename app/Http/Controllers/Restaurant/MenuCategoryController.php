<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuCategoryController extends Controller
{
    /**
     * Display a listing of menu categories
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            abort(403, 'Access denied. User not properly configured.');
        }

        $query = MenuCategory::where('tenant_id', $user->tenant_id);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('pages.restaurant_staff.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create(): View
    {
        return view('pages.restaurant_staff.category.edit');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            abort(403, 'Access denied. User not properly configured.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'menu_template_id' => 'nullable|integer|exists:menu_templates,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['tenant_id'] = $user->tenant_id;
        $validated['restaurant_id'] = $user->restaurant_id ?? null;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('categories', $filename, 'public');
            $validated['image_url'] = asset('storage/'.$path);
        }

        MenuCategory::create($validated);

        return redirect()->route('restaurant.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing a category
     */
    public function edit(MenuCategory $category): View
    {
        $user = Auth::user();

        // Ensure user can only edit categories from their tenant
        if ($category->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        // Optionally fetch menu templates for dropdowns if needed

        $menuTemplates = \App\Models\MenuTemplate::all();

        return view('pages.restaurant_staff.category.edit', compact('category', 'menuTemplates'));
    }

    /**
     * Update a category
     */
    public function update(Request $request, MenuCategory $category): RedirectResponse
    {
        $user = Auth::user();

        // Ensure user can only update categories from their tenant
        if ($category->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? false;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image_url) {
                $oldPath = str_replace('/storage/', '', parse_url($category->image_url, PHP_URL_PATH));
                if (\Storage::disk('public')->exists($oldPath)) {
                    \Storage::disk('public')->delete($oldPath);
                }
            }

            $image = $request->file('image');
            $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('categories', $filename, 'public');
            $validated['image_url'] = asset('storage/'.$path);
        }

        $category->update($validated);

        return redirect()->route('restaurant.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove a category
     */
    public function destroy(MenuCategory $category): RedirectResponse
    {
        $user = Auth::user();
        // Ensure user can only delete categories from their tenant
        try {
            if ($category->tenant_id !== $user->tenant_id) {
                abort(403, 'Access denied.');
            }

            // Check if category has menu items
            if ($category->menuItems()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete category that contains menu items.');
            }

            // Delete image if exists
            if ($category->image_url) {
                $path = str_replace('/storage/', '', parse_url($category->image_url, PHP_URL_PATH));
                if (\Storage::disk('public')->exists($path)) {
                    \Storage::disk('public')->delete($path);
                }
            }

            $category->delete();

            // dd($category);
            return redirect()->route('restaurant.categories.index')
                ->with('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(MenuCategory $category): JsonResponse
    {
        $user = Auth::user();

        // Ensure user can only toggle categories from their tenant
        if ($category->tenant_id !== $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $category->update(['is_active' => ! $category->is_active]);

        return response()->json([
            'success' => true,
            'status' => $category->is_active,
            'message' => 'Category status updated successfully!',
        ]);
    }

    /**
     * Update category sort order
     */
    public function updateSortOrder(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:menu_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['categories'] as $categoryData) {
            $category = MenuCategory::find($categoryData['id']);

            // Ensure user can only update categories from their tenant
            if ($category && $category->tenant_id === $user->tenant_id) {
                $category->update(['sort_order' => $categoryData['sort_order']]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Category order updated successfully!',
        ]);
    }

    /**
     * Get categories for API/AJAX requests
     */
    public function getCategories(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $categories = MenuCategory::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'image_url']);

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }
}
