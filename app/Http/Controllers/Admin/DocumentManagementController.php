<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentManagementController extends Controller
{
    /**
     * Display all restaurant documents
     */
    public function index(Request $request)
    {
        $query = RestaurantDocument::with(['restaurant', 'reviewer']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('restaurant', function ($q) use ($search) {
                $q->where('restaurant_name', 'like', '%'.$search.'%');
            });
        }

        $documents = $query->paginate(15);

        // Get data for filters
        $restaurants = Restaurant::select('id', 'restaurant_name', 'email')->get();

        // Calculate statistics
        $stats = [
            'total' => RestaurantDocument::count(),
            'pending' => RestaurantDocument::where('status', 'pending')->count(),
            'approved' => RestaurantDocument::where('status', 'approved')->count(),
            'expiring' => RestaurantDocument::where('expires_at', '<=', Carbon::now()->addDays(30))
                ->whereNotNull('expires_at')
                ->count(),
        ];

        return view('restaurant_admin.documents.index', compact('documents', 'restaurants', 'stats'));
    }

    /**
     * Show document upload form
     */
    public function create(Request $request)
    {
        $restaurants = Restaurant::select('id', 'restaurant_name', 'email')->get();
        $selectedRestaurant = $request->get('restaurant_id');

        return view('restaurant_admin.documents.create', compact('restaurants', 'selectedRestaurant'));
    }

    /**
     * Store a new document
     */
    public function store(Request $request)
    {
        Log::info('Document upload started', $request->all());

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'document_type' => 'required|in:food_safety_certificate,business_license,pan_card,gst_certificate,owner_id_proof,bank_details,insurance_certificate,fire_safety_certificate,trade_license,pollution_certificate',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
            'document_number' => 'nullable|string|min:3|max:100',
            'issued_by' => 'nullable|string|min:3|max:255',
            'issued_date' => 'nullable|date|before_or_equal:today',
            'expires_at' => 'nullable|date|after:today|after:issued_date',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,approved,rejected',
            'is_verified' => 'nullable|boolean',
            'admin_notes' => 'nullable|string|max:1000',
        ], [
            // Custom error messages
            'restaurant_id.required' => 'Please select a restaurant.',
            'restaurant_id.exists' => 'Selected restaurant does not exist.',
            'document_type.required' => 'Please select a document type.',
            'document_type.in' => 'Selected document type is invalid.',
            'document_file.required' => 'Please select a document file to upload.',
            'document_file.file' => 'The uploaded file is invalid.',
            'document_file.mimes' => 'Document must be a PDF, JPG, PNG, DOC, or DOCX file.',
            'document_file.max' => 'Document file size cannot exceed 10MB.',
            'document_number.min' => 'Document number must be at least 3 characters.',
            'document_number.max' => 'Document number cannot exceed 100 characters.',
            'issued_by.min' => 'Issuing authority must be at least 3 characters.',
            'issued_by.max' => 'Issuing authority cannot exceed 255 characters.',
            'issued_date.date' => 'Please enter a valid issue date.',
            'issued_date.before_or_equal' => 'Issue date cannot be in the future.',
            'expires_at.date' => 'Please enter a valid expiry date.',
            'expires_at.after' => 'Expiry date must be after today.',
            'expires_at.after_date' => 'Expiry date must be after the issue date.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'status.in' => 'Selected status is invalid.',
            'admin_notes.max' => 'Admin notes cannot exceed 1000 characters.',
        ]);

        try {
            $restaurant = Restaurant::findOrFail($validated['restaurant_id']);

            // Check if document type already exists for this restaurant
            $existingDocument = RestaurantDocument::where('restaurant_id', $restaurant->id)
                ->where('document_type', $validated['document_type'])
                ->first();

            if ($existingDocument) {
                return redirect()->back()
                    ->withErrors(['document_type' => 'A document of this type already exists for this restaurant.'])
                    ->withInput();
            }

            $file = $request->file('document_file');
            $originalFilename = $file->getClientOriginalName();

            // Generate unique filename
            $filename = time().'_'.$restaurant->id.'_'.$validated['document_type'].'.'.$file->getClientOriginalExtension();

            // Store file
            $path = $file->storeAs('restaurant_documents', $filename, 'public');

            Log::info('File stored successfully', ['path' => $path]);

            // Create document record
            $document = RestaurantDocument::create([
                'restaurant_id' => $restaurant->id,
                'tenant_id' => $restaurant->tenant_id, // Use null if restaurant has no tenant
                'document_type' => $validated['document_type'],
                'document_path' => $path,
                'document_name' => $this->getDocumentTypeName($validated['document_type']),
                'original_filename' => $originalFilename,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'document_number' => $validated['document_number'] ?? null,
                'issued_by' => $validated['issued_by'] ?? null,
                'issued_date' => isset($validated['issued_date']) ? Carbon::parse($validated['issued_date']) : null,
                'status' => $validated['status'] ?? 'pending',
                'is_verified' => isset($validated['is_verified']) && $validated['is_verified'] ? true : false,
                'description' => $validated['description'] ?? null,
                'admin_notes' => $validated['admin_notes'] ?? null,
                'expires_at' => isset($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null,
                'uploaded_at' => now(),
                'metadata' => json_encode([
                    'uploaded_by' => Auth::id(),
                    'original_name' => $originalFilename,
                    'upload_ip' => $request->ip(),
                ]),
            ]);

            Log::info('Document created successfully', ['document_id' => $document->id]);

            return redirect()->route('restaurant-admin.documents.index')
                ->with('success', 'Document uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Document upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['document_file' => 'Failed to upload document: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * View document details
     */
    public function view($id)
    {
        $document = RestaurantDocument::with(['restaurant', 'reviewer'])->findOrFail($id);

        return view('restaurant_admin.documents.view', compact('document'));
    }

    /**
     * Download document
     */
    public function download($id)
    {
        $document = RestaurantDocument::findOrFail($id);

        if (! Storage::disk('public')->exists($document->document_path)) {
            abort(404, 'Document file not found');
        }

        $filePath = storage_path('app/public/'.$document->document_path);
        $fileName = $document->original_filename ?? $document->document_name;

        return response()->download($filePath, $fileName);
    }

    /**
     * Show edit document form
     */
    public function edit($id)
    {
        $document = RestaurantDocument::with('restaurant')->findOrFail($id);

        $documentTypes = [
            'food_safety_certificate' => 'Food Safety Certificate',
            'business_license' => 'Business License',
            'pan_card' => 'PAN Card',
            'gst_certificate' => 'GST Certificate',
            'owner_id_proof' => 'Owner ID Proof',
            'bank_details' => 'Bank Details',
            'insurance_certificate' => 'Insurance Certificate',
            'fire_safety_certificate' => 'Fire Safety Certificate',
            'trade_license' => 'Trade License',
            'pollution_certificate' => 'Pollution Certificate',
        ];

        return view('restaurant_admin.documents.edit', compact('document', 'documentTypes'));
    }

    /**
     * Update document
     */
    public function update(Request $request, $id)
    {
        $document = RestaurantDocument::findOrFail($id);

        $validated = $request->validate([
            'document_type' => 'required|in:food_safety_certificate,business_license,pan_card,gst_certificate,owner_id_proof,bank_details,insurance_certificate,fire_safety_certificate,trade_license,pollution_certificate',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'document_number' => 'nullable|string|min:3|max:100',
            'issued_by' => 'nullable|string|min:3|max:255',
            'issued_date' => 'nullable|date|before_or_equal:today',
            'expires_at' => 'nullable|date|after:today|after:issued_date',
            'description' => 'nullable|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ], [
            'document_type.required' => 'Please select a document type.',
            'document_type.in' => 'Selected document type is invalid.',
            'document_file.file' => 'The uploaded file is invalid.',
            'document_file.mimes' => 'Document must be a PDF, JPG, PNG, DOC, or DOCX file.',
            'document_file.max' => 'Document file size cannot exceed 10MB.',
            'document_number.min' => 'Document number must be at least 3 characters.',
            'document_number.max' => 'Document number cannot exceed 100 characters.',
            'issued_by.min' => 'Issuing authority must be at least 3 characters.',
            'issued_by.max' => 'Issuing authority cannot exceed 255 characters.',
            'issued_date.date' => 'Please enter a valid issue date.',
            'issued_date.before_or_equal' => 'Issue date cannot be in the future.',
            'expires_at.date' => 'Please enter a valid expiry date.',
            'expires_at.after' => 'Expiry date must be after today.',
            'expires_at.after_date' => 'Expiry date must be after the issue date.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'admin_notes.max' => 'Admin notes cannot exceed 1000 characters.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Selected status is invalid.',
            'rejection_reason.required_if' => 'Rejection reason is required when status is rejected.',
            'rejection_reason.max' => 'Rejection reason cannot exceed 1000 characters.',
        ]);

        try {
            // Update document metadata
            $updateData = [
                'document_type' => $validated['document_type'],
                'document_name' => $this->getDocumentTypeName($validated['document_type']),
                'document_number' => $validated['document_number'] ?? null,
                'issued_by' => $validated['issued_by'] ?? null,
                'issued_date' => isset($validated['issued_date']) ? Carbon::parse($validated['issued_date']) : null,
                'description' => $validated['description'] ?? null,
                'admin_notes' => $validated['admin_notes'],
                'expires_at' => isset($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null,
                'status' => $validated['status'],
                'rejection_reason' => $validated['rejection_reason'] ?? null,
            ];

            // Handle status change
            if ($validated['status'] !== $document->status) {
                $updateData['reviewed_at'] = now();
                $updateData['reviewed_by'] = Auth::id();

                if ($validated['status'] === 'approved') {
                    $updateData['is_verified'] = true;
                }
            }

            // Handle file replacement
            if ($request->hasFile('document_file')) {
                // Delete old file
                if (Storage::disk('public')->exists($document->document_path)) {
                    Storage::disk('public')->delete($document->document_path);
                }

                $file = $request->file('document_file');
                $filename = time().'_'.$document->restaurant_id.'_'.$validated['document_type'].'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('restaurant_documents', $filename, 'public');

                $updateData['document_path'] = $path;
                $updateData['original_filename'] = $file->getClientOriginalName();
                $updateData['file_size'] = $file->getSize();
                $updateData['mime_type'] = $file->getMimeType();
                $updateData['uploaded_at'] = now();
            }

            $document->update($updateData);

            return redirect()->route('restaurant-admin.documents.index')
                ->with('success', 'Document updated successfully!');

        } catch (\Exception $e) {
            Log::error('Document update failed', [
                'document_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['general' => 'Failed to update document: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete document
     */
    public function destroy($id)
    {
        $document = RestaurantDocument::findOrFail($id);

        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($document->document_path)) {
                Storage::disk('public')->delete($document->document_path);
            }

            $document->delete();

            return redirect()->route('restaurant-admin.documents.index')
                ->with('success', 'Document deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Document deletion failed', [
                'document_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['general' => 'Failed to delete document: '.$e->getMessage()]);
        }
    }

    /**
     * Get documents for a specific restaurant (AJAX)
     */
    public function getByRestaurant($restaurantId)
    {
        $documents = RestaurantDocument::where('restaurant_id', $restaurantId)
            ->with('reviewer')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'document_name' => $doc->document_name,
                    'status' => $doc->status,
                    'uploaded_at' => $doc->uploaded_at->format('M d, Y'),
                    'expires_at' => $doc->expires_at ? $doc->expires_at->format('M d, Y') : null,
                    'reviewer' => $doc->reviewer ? $doc->reviewer->first_name.' '.$doc->reviewer->last_name : null,
                ];
            });

        return response()->json($documents);
    }

    /**
     * Get document type display name
     */
    private function getDocumentTypeName($type)
    {
        $names = [
            'food_safety_certificate' => 'Food Safety Certificate',
            'business_license' => 'Business License',
            'pan_card' => 'PAN Card',
            'gst_certificate' => 'GST Certificate',
            'owner_id_proof' => 'Owner ID Proof',
            'bank_details' => 'Bank Details',
            'insurance_certificate' => 'Insurance Certificate',
            'fire_safety_certificate' => 'Fire Safety Certificate',
            'trade_license' => 'Trade License',
            'pollution_certificate' => 'Pollution Certificate',
        ];

        return $names[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
}
