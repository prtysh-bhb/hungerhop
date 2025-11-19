<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index()
    {
        $customers = User::with(['customerProfile'])
            ->where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($customer) {
                // Get the default address
                $defaultAddress = $customer->addresses()->where('is_default', true)->first();

                // Get last order date
                $lastOrder = $customer->orders()->latest()->first();

                return [
                    'id' => $customer->id,
                    'customer_id' => 'CUST'.str_pad($customer->id, 6, '0', STR_PAD_LEFT),
                    'name' => trim($customer->first_name.' '.$customer->last_name),
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'status' => $customer->status,
                    'join_date' => $customer->created_at->format('d M Y'),
                    'location' => $defaultAddress ?
                        ($defaultAddress->address_line1.', '.$defaultAddress->city.', '.$defaultAddress->state) : 'No address',
                    'total_spent' => '$'.number_format($customer->orders_sum_total_amount ?? 0, 2),
                    'total_orders' => $customer->orders_count ?? 0,
                    'last_order_date' => $lastOrder ? $lastOrder->created_at->format('d M Y') : 'No orders',
                    'last_login' => $customer->last_login_at ? $customer->last_login_at->format('d M Y') : 'Never',
                    'loyalty_points' => $customer->customerProfile->loyalty_points ?? 0,
                    'profile_image_url' => $customer->customerProfile && $customer->customerProfile->profile_image_url
                        ? asset('storage/'.$customer->customerProfile->profile_image_url)
                        : asset('images/avatar/default-avatar.png'),
                ];
            });

        return view('pages.customer.customer', compact('customers'));
    }

    /**
     * Update customer status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,pending',
        ]);

        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Customer status updated successfully',
        ]);
    }

    /**
     * Show customer details
     */
    public function show($id)
    {
        $customer = User::with(['customerProfile', 'addresses'])
            ->where('role', 'customer')
            ->findOrFail($id);

        return view('pages.customer.show', compact('customer'));
    }

    /**
     * Show edit customer form
     */
    public function edit($id)
    {
        $customer = User::with(['customerProfile', 'addresses'])
            ->where('role', 'customer')
            ->findOrFail($id);

        return view('pages.customer.edit', compact('customer'));
    }

    /**
     * Update customer information
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[A-Za-z\s]+$/', // Only letters and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[A-Za-z\s]+$/', // Only letters and spaces
            ],
            'email' => [
                'required',
                'email',
                'min:7',
                'max:100',
                'unique:users,email,'.$id,
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9]{10,15}$/', // Only numbers, 10-15 digits
            ],
            'status' => 'required|in:active,suspended,pending',
        ], [
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name cannot exceed 50 characters.',
            'first_name.regex' => 'First name can only contain letters and spaces.',

            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name cannot exceed 50 characters.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.min' => 'Email must be at least 7 characters.',
            'email.max' => 'Email cannot exceed 100 characters.',
            'email.unique' => 'This email is already registered.',

            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number cannot exceed 15 digits.',
            'phone.regex' => 'Phone number must contain only numbers (10-15 digits).',

            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
        ]);

        $customer = User::where('role', 'customer')->findOrFail($id);

        $customer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.customers')->with('success', 'Customer updated successfully');
    }

    /**
     * Delete customer (soft delete)
     */
    public function destroy($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully');
    }
}
