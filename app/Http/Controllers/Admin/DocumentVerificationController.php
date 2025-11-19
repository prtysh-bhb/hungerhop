<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentVerificationController extends Controller
{
    /**
     * Display documents pending verification
     */
    public function index(Request $request)
    {
        $query = RestaurantDocument::with(['restaurant', 'reviewer']);

        // Default to pending documents
        $status = $request->get('status', 'pending');
        $query->where('status', $status);

        // Apply additional filters
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

        // Sort by oldest pending first for verification queue
        $query->orderBy('uploaded_at', 'asc');

        $documents = $query->paginate(15);

        // Get data for filters
        $restaurants = Restaurant::select('id', 'restaurant_name')->get();
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

        // Get verification statistics
        $stats = [
            'pending' => RestaurantDocument::where('status', 'pending')->count(),
            'approved_today' => RestaurantDocument::where('status', 'approved')
                ->whereDate('updated_at', today())
                ->count(),
            'rejected_today' => RestaurantDocument::where('status', 'rejected')
                ->whereDate('updated_at', today())
                ->count(),
            'expiring_soon' => RestaurantDocument::where('expires_at', '<=', now()->addDays(30))
                ->whereNotNull('expires_at')
                ->count(),
        ];

        return view('restaurant_admin.verification.index', compact(
            'documents',
            'restaurants',
            'documentTypes',
            'stats',
            'status'
        ));
    }

    /**
     * View document for verification
     */
    public function viewDocument($id)
    {
        $document = RestaurantDocument::with(['restaurant', 'reviewer'])->findOrFail($id);

        // Check if file exists
        $fileExists = Storage::disk('public')->exists($document->document_path);

        // Get file URL for preview
        $fileUrl = $fileExists ? Storage::url($document->document_path) : null;

        return view('restaurant_admin.verification.view', compact('document', 'fileExists', 'fileUrl'));
    }

    /**
     * Update document verification status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $document = RestaurantDocument::findOrFail($id);

        try {
            DB::transaction(function () use ($document, $validated) {
                $updateData = [
                    'status' => $validated['status'],
                    'reviewed_at' => now(),
                    'reviewed_by' => Auth::id(),
                    'admin_notes' => $validated['admin_notes'] ?? $document->admin_notes,
                ];

                if ($validated['status'] === 'approved') {
                    $updateData['is_verified'] = true;
                    $updateData['rejection_reason'] = null;
                } else {
                    $updateData['is_verified'] = false;
                    $updateData['rejection_reason'] = $validated['rejection_reason'];
                }

                $document->update($updateData);

                // Check if all required documents are approved for the restaurant
                $this->checkRestaurantDocumentCompletion($document->restaurant_id);
            });

            $message = $validated['status'] === 'approved'
                ? 'Document approved successfully!'
                : 'Document rejected successfully!';

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Failed to update document status: '.$e->getMessage()]);
        }
    }

    /**
     * Bulk update document status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:restaurant_documents,id',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $updated = 0;
        $restaurantIds = [];

        try {
            DB::transaction(function () use ($validated, &$updated, &$restaurantIds) {
                foreach ($validated['document_ids'] as $documentId) {
                    $document = RestaurantDocument::find($documentId);
                    if ($document) {
                        $updateData = [
                            'status' => $validated['status'],
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                            'admin_notes' => $validated['admin_notes'] ?? $document->admin_notes,
                        ];

                        if ($validated['status'] === 'approved') {
                            $updateData['is_verified'] = true;
                            $updateData['rejection_reason'] = null;
                        } else {
                            $updateData['is_verified'] = false;
                            $updateData['rejection_reason'] = $validated['rejection_reason'];
                        }

                        $document->update($updateData);
                        $restaurantIds[] = $document->restaurant_id;
                        $updated++;
                    }
                }
            });

            // Check document completion for affected restaurants
            foreach (array_unique($restaurantIds) as $restaurantId) {
                $this->checkRestaurantDocumentCompletion($restaurantId);
            }

            $message = $validated['status'] === 'approved'
                ? "Successfully approved {$updated} documents."
                : "Successfully rejected {$updated} documents.";

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Failed to bulk update documents: '.$e->getMessage()]);
        }
    }

    /**
     * Download document
     */
    public function downloadDocument($id)
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
     * Get verification queue data for AJAX
     */
    public function getVerificationQueue(Request $request)
    {
        $status = $request->get('status', 'pending');

        $documents = RestaurantDocument::with(['restaurant'])
            ->where('status', $status)
            ->orderBy('uploaded_at', 'asc')
            ->take(50)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'restaurant_name' => $doc->restaurant->restaurant_name,
                    'document_type' => $this->getDocumentTypeName($doc->document_type),
                    'uploaded_at' => $doc->uploaded_at->diffForHumans(),
                    'file_size' => $this->formatFileSize($doc->file_size),
                    'mime_type' => $doc->mime_type,
                ];
            });

        return response()->json($documents);
    }

    /**
     * Get document expiry report
     */
    public function getExpiryReport(Request $request)
    {
        $days = $request->get('days', 30);

        $expiring = RestaurantDocument::with(['restaurant'])
            ->where('status', 'approved')
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now())
            ->orderBy('expires_at', 'asc')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'restaurant_name' => $doc->restaurant->restaurant_name,
                    'document_type' => $this->getDocumentTypeName($doc->document_type),
                    'expires_at' => $doc->expires_at->format('M d, Y'),
                    'days_until_expiry' => $doc->expires_at->diffInDays(now()),
                ];
            });

        return response()->json($expiring);
    }

    /**
     * Check if restaurant has all required documents approved
     */
    private function checkRestaurantDocumentCompletion($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);
        if (! $restaurant) {
            return;
        }

        $requiredDocuments = [
            'food_safety_certificate',
            'business_license',
            'pan_card',
            'gst_certificate',
            'owner_id_proof',
        ];

        $approvedCount = RestaurantDocument::where('restaurant_id', $restaurantId)
            ->whereIn('document_type', $requiredDocuments)
            ->where('status', 'approved')
            ->count();

        // If all required documents are approved and restaurant is pending, auto-approve
        if ($approvedCount === count($requiredDocuments) && $restaurant->status === 'pending') {
            $restaurant->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);
        }
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

    /**
     * Format file size for display
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        } else {
            return $bytes.' bytes';
        }
    }
}
