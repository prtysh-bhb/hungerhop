<?php

namespace App\Http\Controllers;

use App\Enums\VehicleTypeEnums;
use App\Models\DeliveryPartner;
use Illuminate\Http\Request;

class DeliveryPartnerController extends Controller
{
    /**
     * Display a listing of all delivery partners.
     */
    public function index()
    {
        $partners = DeliveryPartner::with('user')->get();

        return view('pages.delivery_partner.index', compact('partners'));
    }

    public function destroy(DeliveryPartner $partner)
    {
        $partner->delete();
        if (! $partner) {
            return redirect()->back()->with('error', 'Delivery partner not found.');
        }
        // Optionally, you might want to delete the associated user as well
        $user = $partner->user;
        $partner->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->back()->with('success', 'Delivery partner deleted successfully.');
    }

    public function edit(DeliveryPartner $partner)
    {
        if (! $partner) {
            return redirect()->back()->with('error', 'Delivery partner not found.');
        }
        $vehicleTypes = VehicleTypeEnums::cases();

        return view('pages.delivery_partner.edit', compact('partner', 'vehicleTypes'));
    }

    public function update(DeliveryPartner $partner, Request $request)
    {
        $request->validate([
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:15',
                'regex:/^[a-zA-Z]+$/', // Only letters, no spaces
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:15',
                'regex:/^[a-zA-Z]+$/', // Only letters, no spaces
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9]{10,15}$/', // 10 to 15 digits only
                'regex:/^(?!0+$).*$/', // Not all zeros
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com)$/', // Must end with .com
            ],
            'vehicle_type' => ['nullable', 'string', 'in:'.implode(',', array_column(VehicleTypeEnums::cases(), 'value'))],
            'vehicle_number' => [
                'nullable',
                'string',
                'min:6',
                'max:15',
                'regex:/^[A-Z]{2}[\s-]?[0-9]{1,2}[\s-]?[A-Z]{1,2}[\s-]?[0-9]{1,4}$/', // Indian vehicle number format
            ],
            'license_number' => [
                'nullable',
                'string',
                'min:16',
                'max:20',
                'regex:/^[A-Z0-9]{16,20}$/', // 16 to 20 alphanumeric characters
            ],
            'is_available' => ['required', 'boolean'],
            'status' => ['required', 'string', 'in:active,inactive,pending_approval,suspended'],
        ], [
            'first_name.regex' => 'First name may only contain letters (no spaces or special characters).',
            'last_name.regex' => 'Last name may only contain letters (no spaces or special characters).',
            'phone.regex' => 'Phone number must be 10 to 15 digits and cannot be all zeros.',
            'email.regex' => 'Email must end with .com domain.',
            'vehicle_number.regex' => 'Please enter a valid Indian vehicle number (e.g., MH 12 AB 1234).',
            'license_number.regex' => 'License number must be 16 to 20 alphanumeric characters.',
            'status.in' => 'Please select a valid status.',
        ]);

        $user = $partner->user;
        if ($user) {
            $user->update($request->only('first_name', 'last_name', 'phone', 'email', 'status'));
        }

        // Map status between user table and delivery_partner table
        // User table: active, inactive, pending_approval, suspended
        // Delivery Partner table: pending, approved, suspended, rejected
        $partnerData = $request->only('vehicle_type', 'vehicle_number', 'license_number', 'is_available');

        $userStatus = $request->status;
        $statusMapping = [
            'active' => 'approved',
            'inactive' => 'rejected',
            'pending_approval' => 'pending',
            'suspended' => 'suspended',
        ];

        if (isset($statusMapping[$userStatus])) {
            $partnerData['status'] = $statusMapping[$userStatus];
        }

        $partner->update($partnerData);

        return redirect()->route('partners.index')->with('success', 'Delivery partner updated successfully.');
    }

    public function show(DeliveryPartner $partner)
    {
        if (! $partner) {
            return redirect()->back()->with('error', 'Delivery partner not found.');
        }
        $user = $partner->user;
        $documents = $partner->documents()->get();

        return view('pages.delivery_partner.show', compact('partner', 'user', 'documents'));
    }
}
