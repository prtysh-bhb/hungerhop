<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class SuparAdminDashboard extends Controller
{
    // Dashboard for Super Admin

    public function index()
    {
        // Get recent reviews with customer and user data (first_name, last_name)
        $reviews = Review::with(['customer.user:id,first_name,last_name'])->get();

        return view('pages.super_admin.dashboard.index', [
            'totalOrders' => Order::count(),
            'completedOrders' => Order::where('status', 'delivered')->count(),
            'canceledOrders' => Order::where('status', 'canceled')->count(),
            'totalRevenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'reviews' => $reviews,
        ]);
    }

    /**
     * Get updated dashboard statistics for AJAX requests
     */
    public function getStats()
    {
        return response()->json([
            'totalOrders' => Order::count(),
            'completedOrders' => Order::where('status', 'delivered')->count(),
            'canceledOrders' => Order::where('status', 'cancelled')->count(),
            'totalRevenue' => number_format((float) Order::where('status', 'delivered')->sum('total_amount'), 2),
        ]);
    }

    /**
     * Get recent orders for real-time updates
     */
    public function getRecentOrders(Request $request)
    {
        $limit = $request->get('limit', 5);

        $orders = Order::with(['customer.user:id,first_name,last_name', 'restaurant:id,name'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer && $order->customer->user ?
                        $order->customer->user->first_name.' '.$order->customer->user->last_name :
                        'Guest Customer',
                    'restaurant_name' => $order->restaurant ? $order->restaurant->name : 'Unknown Restaurant',
                    'status' => $order->status,
                    'total_amount' => number_format((float) $order->total_amount, 2),
                    'created_at' => $order->created_at->diffForHumans(),
                ];
            });

        return response()->json($orders);
    }

    /**
     * Display all wallet transactions for super admin
     */
    public function walletTransactions(Request $request)
    {
        $query = WalletTransaction::with(['user', 'paymentDetail']);

        // Apply filters
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderByDesc('created_at')->paginate(2);

        // Calculate totals
        $totalCredits = WalletTransaction::where('type', 'in')
            ->where('status', 'completed')
            ->sum('amount');

        $totalDebits = WalletTransaction::where('type', 'out')
            ->where('status', 'completed')
            ->sum('amount');

        $pendingAmount = WalletTransaction::where('status', 'pending')
            ->sum('amount');

        $totalTransactions = WalletTransaction::count();

        return view('pages.super_admin.wallet-transactions.index', [
            'transactions' => $transactions,
            'totalCredits' => $totalCredits,
            'totalDebits' => $totalDebits,
            'pendingAmount' => $pendingAmount,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}
