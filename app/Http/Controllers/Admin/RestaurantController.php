<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Restaurant\RestaurantData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Models\City;
use App\Models\Restaurant;
use App\Models\State;
use App\Models\Tenant;
use App\Models\User;
use App\Services\RestaurantService;
use Exception;
use Illuminate\Http\Request;
use Log;

class RestaurantController extends Controller
{
    public function __construct(
        protected RestaurantService $restaurantService
    ) {}

    public function index()
    {
        $restaurants = Restaurant::with(['locationAdmin', 'approvedByUser'])
            ->latest()
            ->paginate(15);

        return view('pages.admin.restaurants.index', compact('restaurants'));
    }

    public function create()
    {
        $user = auth()->user();
        $tenants = collect();
        $locationAdmins = collect();
        $states = collect();
        $cities = collect();

        // Role-based data loading
        switch ($user->role) {
            case 'super_admin':
                // Super admin can see all tenants and create new ones
                $tenants = Tenant::where('status', 'active')->get(['id', 'name']);
                $locationAdmins = User::where('role', 'location_admin')->get(['id', 'name', 'email']);
                break;

            case 'tenant_admin':
                // Tenant admin can only see their location admins
                $locationAdmins = User::where('role', 'location_admin')
                    ->where('tenant_id', $user->tenant_id)
                    ->get(['id', 'name', 'email']);
                break;

            default:
                // Other roles cannot access this page
                abort(403, 'Unauthorized to create restaurants');
        }

        $states = State::where('country_id', config('app.country_id', 97))->get(['id', 'name']);
        $cuisineTypes = [
            'indian' => 'Indian',
            'chinese' => 'Chinese',
            'italian' => 'Italian',
            'mexican' => 'Mexican',
            'thai' => 'Thai',
            'continental' => 'Continental',
            'fast_food' => 'Fast Food',
            'beverages' => 'Beverages',
            'desserts' => 'Desserts',
        ];

        return view('pages.admin.restaurants.create', compact(
            'tenants',
            'locationAdmins',
            'states',
            'cities',
            'cuisineTypes',
            'user'
        ));
    }

    public function store(StoreRestaurantRequest $request)
    {
        try {
            $restaurant = $this->restaurantService->create(
                RestaurantData::fromArray($request->validated())
            );

            return redirect()
                ->route('admin.restaurants.show', $restaurant->id)
                ->with('success', 'Restaurant created successfully and is pending approval.');

        } catch (Exception $e) {
            Log::error('Restaurant creation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $restaurant = Restaurant::with([
            'locationAdmin',
            'approvedByUser',
            'documents',
            'workingHours',
            'menuCategories',
        ])->findOrFail($id);

        return view('pages.admin.restaurants.show', compact('restaurant'));
    }

    public function approve(Request $request, $id)
    {
        try {
            $this->restaurantService->approve($id);

            return redirect()
                ->back()
                ->with('success', 'Restaurant approved successfully.');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $this->restaurantService->reject($id);

            return redirect()
                ->back()
                ->with('success', 'Restaurant rejected.');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    // AJAX endpoint for getting cities by state
    public function getCitiesByState(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->get(['id', 'name']);

        return response()->json($cities);
    }

    public function togglePause(Request $request, $id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);
            $user = auth()->user();

            // Check permissions
            if (! $this->canManageRestaurant($restaurant, $user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to manage this restaurant.',
                ], 403);
            }

            $restaurant->togglePause();

            return response()->json([
                'success' => true,
                'is_paused' => $restaurant->is_paused,
                'message' => $restaurant->is_paused
                    ? 'Restaurant paused - Not accepting new orders'
                    : 'Restaurant resumed - Now accepting orders',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating restaurant status: '.$e->getMessage(),
            ], 500);
        }
    }

    private function canManageRestaurant(Restaurant $restaurant, User $user): bool
    {
        return match ($user->role) {
            'tenant_admin' => $restaurant->tenant_id === $user->tenant_id,
            'location_admin' => $restaurant->location_admin_id === $user->id,
            'super_admin' => true,
            default => false
        };
    }
}
