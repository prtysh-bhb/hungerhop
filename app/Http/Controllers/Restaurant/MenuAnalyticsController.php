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

class MenuAnalyticsController extends Controller
{
    /**
     * Display menu analytics dashboard
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            abort(403, 'Access denied. User not properly configured.');
        }

        $dateRange = $this->getDateRange($request);
        $analytics = $this->getMenuAnalytics($user->tenant_id, $dateRange);

        return view('pages.restaurant_staff.menu_analytics', compact('analytics', 'dateRange'));
    }

    /**
     * Get popular items analytics
     */
    public function popularItems(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $limit = $request->get('limit', 10);
        $dateRange = $this->getDateRange($request);

        $popularItems = MenuItem::where('tenant_id', $user->tenant_id)
            ->where('is_available', true)
            ->orderBy('total_sales', 'desc')
            ->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get(['id', 'item_name', 'total_sales', 'average_rating', 'base_price']);

        return response()->json([
            'success' => true,
            'popular_items' => $popularItems,
        ]);
    }

    /**
     * Get category performance analytics
     */
    public function categoryPerformance(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $categoryStats = MenuCategory::where('tenant_id', $user->tenant_id)
            ->withCount(['menuItems' => function ($query) {
                $query->where('is_available', true);
            }])
            ->with(['menuItems' => function ($query) {
                $query->select('menu_category_id',
                    DB::raw('SUM(total_sales) as total_category_sales'),
                    DB::raw('AVG(average_rating) as avg_category_rating'),
                    DB::raw('COUNT(*) as item_count')
                )->groupBy('menu_category_id');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'category_performance' => $categoryStats,
        ]);
    }

    /**
     * Get revenue analytics by menu items
     */
    public function revenueAnalytics(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $dateRange = $this->getDateRange($request);

        $revenueData = MenuItem::where('tenant_id', $user->tenant_id)
            ->select('id', 'item_name', 'base_price', 'total_sales',
                DB::raw('(base_price * total_sales) as total_revenue')
            )
            ->where('total_sales', '>', 0)
            ->orderBy('total_revenue', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'revenue_data' => $revenueData,
        ]);
    }

    /**
     * Get inventory alerts
     */
    public function inventoryAlerts(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $lowStockThreshold = $request->get('threshold', 10);

        $lowStockItems = MenuItem::where('tenant_id', $user->tenant_id)
            ->where('track_inventory', true)
            ->where('inventory_count', '<=', $lowStockThreshold)
            ->where('is_available', true)
            ->orderBy('inventory_count', 'asc')
            ->get(['id', 'item_name', 'inventory_count', 'base_price']);

        $outOfStockItems = MenuItem::where('tenant_id', $user->tenant_id)
            ->where('track_inventory', true)
            ->where('inventory_count', 0)
            ->get(['id', 'item_name', 'base_price']);

        return response()->json([
            'success' => true,
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'low_stock_count' => $lowStockItems->count(),
            'out_of_stock_count' => $outOfStockItems->count(),
        ]);
    }

    /**
     * Get menu performance metrics
     */
    public function performanceMetrics(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $metrics = [
            'total_items' => MenuItem::where('tenant_id', $user->tenant_id)->count(),
            'active_items' => MenuItem::where('tenant_id', $user->tenant_id)
                ->where('is_available', true)
                ->count(),
            'popular_items' => MenuItem::where('tenant_id', $user->tenant_id)
                ->where('is_popular', true)
                ->count(),
            'total_categories' => MenuCategory::where('tenant_id', $user->tenant_id)->count(),
            'active_categories' => MenuCategory::where('tenant_id', $user->tenant_id)
                ->where('is_active', true)
                ->count(),
            'total_sales' => MenuItem::where('tenant_id', $user->tenant_id)
                ->sum('total_sales'),
            'average_rating' => MenuItem::where('tenant_id', $user->tenant_id)
                ->where('total_reviews', '>', 0)
                ->avg('average_rating'),
            'total_revenue' => MenuItem::where('tenant_id', $user->tenant_id)
                ->sum(DB::raw('base_price * total_sales')),
        ];

        return response()->json([
            'success' => true,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Get items that need attention (low ratings, no sales, etc.)
     */
    public function itemsNeedingAttention(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $lowRatingThreshold = $request->get('rating_threshold', 3.0);
        $noSalesThreshold = $request->get('days_threshold', 30);

        $lowRatingItems = MenuItem::where('tenant_id', $user->tenant_id)
            ->where('average_rating', '<', $lowRatingThreshold)
            ->where('total_reviews', '>=', 5) // Only consider items with enough reviews
            ->where('is_available', true)
            ->get(['id', 'item_name', 'average_rating', 'total_reviews']);

        $noSalesItems = MenuItem::where('tenant_id', $user->tenant_id)
            ->where('total_sales', 0)
            ->where('is_available', true)
            ->where('created_at', '<=', Carbon::now()->subDays($noSalesThreshold))
            ->get(['id', 'item_name', 'created_at', 'base_price']);

        $highPriceNoSalesItems = MenuItem::where('tenant_id', $user->tenant_id)
            ->where('total_sales', '<', 5)
            ->where('base_price', '>', 50) // High-priced items
            ->where('is_available', true)
            ->get(['id', 'item_name', 'base_price', 'total_sales']);

        return response()->json([
            'success' => true,
            'low_rating_items' => $lowRatingItems,
            'no_sales_items' => $noSalesItems,
            'high_price_no_sales' => $highPriceNoSalesItems,
            'total_issues' => $lowRatingItems->count() + $noSalesItems->count() + $highPriceNoSalesItems->count(),
        ]);
    }

    /**
     * Update menu item metrics (called when orders are placed)
     */
    public function updateItemMetrics(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:menu_items,id',
            'quantity_sold' => 'required|integer|min:1',
            'rating' => 'nullable|numeric|min:1|max:5',
        ]);

        $menuItem = MenuItem::where('id', $validated['item_id'])
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (! $menuItem) {
            return response()->json(['error' => 'Menu item not found.'], 404);
        }

        // Update sales count
        $menuItem->increment('total_sales', $validated['quantity_sold']);

        // Update inventory if tracked
        if ($menuItem->track_inventory) {
            $menuItem->decrement('inventory_count', $validated['quantity_sold']);
        }

        // Update rating if provided
        if (isset($validated['rating'])) {
            $currentTotalRating = $menuItem->average_rating * $menuItem->total_reviews;
            $newTotalReviews = $menuItem->total_reviews + 1;
            $newAverageRating = ($currentTotalRating + $validated['rating']) / $newTotalReviews;

            $menuItem->update([
                'total_reviews' => $newTotalReviews,
                'average_rating' => round($newAverageRating, 2),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu item metrics updated successfully!',
            'item' => $menuItem->fresh(),
        ]);
    }

    /**
     * Get date range for analytics
     */
    private function getDateRange(Request $request): array
    {
        $period = $request->get('period', '7days');

        switch ($period) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::today()->endOfDay();
                break;
            case '7days':
                $start = Carbon::now()->subDays(7);
                $end = Carbon::now();
                break;
            case '30days':
                $start = Carbon::now()->subDays(30);
                $end = Carbon::now();
                break;
            case '3months':
                $start = Carbon::now()->subMonths(3);
                $end = Carbon::now();
                break;
            case 'custom':
                $start = Carbon::parse($request->get('start_date', Carbon::now()->subDays(7)));
                $end = Carbon::parse($request->get('end_date', Carbon::now()));
                break;
            default:
                $start = Carbon::now()->subDays(7);
                $end = Carbon::now();
        }

        return ['start' => $start, 'end' => $end, 'period' => $period];
    }

    /**
     * Get comprehensive menu analytics
     */
    private function getMenuAnalytics(int $tenantId, array $dateRange): array
    {
        $baseQuery = MenuItem::where('tenant_id', $tenantId);

        return [
            'overview' => [
                'total_items' => $baseQuery->count(),
                'active_items' => $baseQuery->where('is_available', true)->count(),
                'total_sales' => $baseQuery->sum('total_sales'),
                'total_revenue' => $baseQuery->sum(DB::raw('base_price * total_sales')),
                'average_rating' => $baseQuery->where('total_reviews', '>', 0)->avg('average_rating'),
            ],
            'top_performers' => $baseQuery->where('total_sales', '>', 0)
                ->orderBy('total_sales', 'desc')
                ->limit(10)
                ->get(['item_name', 'total_sales', 'base_price', 'average_rating']),
            'category_stats' => MenuCategory::where('tenant_id', $tenantId)
                ->withCount('menuItems')
                ->get(['category_name', 'menu_items_count']),
            'inventory_alerts' => $baseQuery->where('track_inventory', true)
                ->where('inventory_count', '<=', 10)
                ->count(),
            'low_rating_items' => $baseQuery->where('average_rating', '<', 3.0)
                ->where('total_reviews', '>=', 5)
                ->count(),
        ];
    }
}
