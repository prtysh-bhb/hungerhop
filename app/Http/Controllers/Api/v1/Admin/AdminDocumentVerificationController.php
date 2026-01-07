<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use App\Models\DeliveryPartnerDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminDocumentVerificationController extends Controller
{
    /**
     * Get all pending documents for verification
     */
    public function getPendingDocuments(Request $request)
    {
        $this->authorizeAdmin();

        $perPage = $request->get('per_page', 15);
        $status = $request->get('status', 'pending'); // pending, approved, rejected

        $documents = DeliveryPartnerDocument::with('partner')
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $formattedDocuments = $documents->map(function ($doc) {
            return $this->formatDocumentForAdmin($doc);
        });

        return response()->json([
            'success' => true,
            'message' => 'Documents retrieved successfully.',
            'data' => $formattedDocuments,
            'pagination' => [
                'total' => $documents->total(),
                'per_page' => $documents->perPage(),
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
            ],
        ], 200);
    }

    /**
     * Get document details for admin review
     */
    public function getDocumentForReview(Request $request, $documentId)
    {
        $this->authorizeAdmin();

        $document = DeliveryPartnerDocument::with('partner', 'reviewer')
            ->find($documentId);

        if (! $document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document details retrieved successfully.',
            'data' => $this->formatDocumentForAdmin($document),
        ], 200);
    }

    /**
     * Approve a document
     */
    public function approveDocument(Request $request, $documentId)
    {
        $this->authorizeAdmin();

        $document = DeliveryPartnerDocument::find($documentId);

        if (! $document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.',
            ], 404);
        }

        if ($document->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => "Document is already {$document->status}.",
            ], 422);
        }

        DB::beginTransaction();
        try {
            $document->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'rejection_reason' => null,
            ]);

            // Check if all documents for this partner are approved
            $partner = $document->partner;
            $allApproved = DeliveryPartnerDocument::where('partner_id', $partner->id)
                ->where('status', '!=', 'approved')
                ->count() === 0;

            // If all documents are approved, update partner status
            if ($allApproved && $partner->status === 'pending') {
                $partner->update(['status' => 'approved']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Document approved successfully.',
                'data' => [
                    'document_id' => (string) $document->id,
                    'status' => $document->status,
                    'approved_at' => $document->reviewed_at->toISOString(),
                    'partner_status' => $partner->status,
                    'all_documents_approved' => $allApproved,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve document: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reject a document
     */
    public function rejectDocument(Request $request, $documentId)
    {
        $this->authorizeAdmin();

        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|min:10|max:500',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $document = DeliveryPartnerDocument::find($documentId);

        if (! $document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.',
            ], 404);
        }

        if ($document->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => "Document is already {$document->status}.",
            ], 422);
        }

        DB::beginTransaction();
        try {
            $document->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // Update partner status to rejected
            $partner = $document->partner;
            $partner->update(['status' => 'rejected', 'rejection_reason' => $validated['rejection_reason']]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Document rejected successfully.',
                'data' => [
                    'document_id' => (string) $document->id,
                    'status' => $document->status,
                    'rejected_at' => $document->reviewed_at->toISOString(),
                    'rejection_reason' => $document->rejection_reason,
                    'partner_status' => $partner->status,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject document: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get documents by delivery partner
     */
    public function getPartnerDocuments(Request $request, $partnerId)
    {
        $this->authorizeAdmin();

        $partner = DeliveryPartner::find($partnerId);

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner not found.',
            ], 404);
        }

        $documents = DeliveryPartnerDocument::where('partner_id', $partnerId)
            ->orderByDesc('created_at')
            ->get();

        $formattedDocuments = $documents->map(function ($doc) {
            return $this->formatDocumentForAdmin($doc);
        })->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Documents retrieved successfully.',
            'data' => [
                'partner' => [
                    'id' => (string) $partner->id,
                    'name' => $partner->user->first_name.' '.$partner->user->last_name,
                    'email' => $partner->user->email,
                    'phone' => $partner->user->phone,
                    'status' => $partner->status,
                ],
                'documents' => $formattedDocuments,
                'summary' => [
                    'total' => count($formattedDocuments),
                    'pending' => collect($formattedDocuments)->where('status', 'pending')->count(),
                    'approved' => collect($formattedDocuments)->where('status', 'approved')->count(),
                    'rejected' => collect($formattedDocuments)->where('status', 'rejected')->count(),
                ],
            ],
        ], 200);
    }

    /**
     * Get document statistics for admin dashboard
     */
    public function getDocumentStats(Request $request)
    {
        $this->authorizeAdmin();

        $stats = [
            'total_documents' => DeliveryPartnerDocument::count(),
            'pending_documents' => DeliveryPartnerDocument::where('status', 'pending')->count(),
            'approved_documents' => DeliveryPartnerDocument::where('status', 'approved')->count(),
            'rejected_documents' => DeliveryPartnerDocument::where('status', 'rejected')->count(),
            'pending_partners' => DeliveryPartner::where('status', 'pending')->count(),
            'approved_partners' => DeliveryPartner::where('status', 'approved')->count(),
            'rejected_partners' => DeliveryPartner::where('status', 'rejected')->count(),
        ];

        // Documents per partner (recent)
        $pendingPerPartner = DeliveryPartnerDocument::where('status', 'pending')
            ->select('partner_id', DB::raw('count(*) as count'))
            ->groupBy('partner_id')
            ->with('partner')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully.',
            'data' => [
                'stats' => $stats,
                'recent_pending_partners' => $pendingPerPartner->map(function ($item) {
                    return [
                        'partner_id' => (string) $item->partner_id,
                        'partner_name' => $item->partner ? $item->partner->user->first_name.' '.$item->partner->user->last_name : 'N/A',
                        'pending_documents' => $item->count,
                    ];
                }),
            ],
        ], 200);
    }

    /**
     * Format document for admin view
     */
    private function formatDocumentForAdmin($document)
    {
        $data = [
            'id' => (string) $document->id,
            'partner_id' => (string) $document->partner_id,
            'partner_name' => $document->partner ? $document->partner->user->first_name.' '.$document->partner->user->last_name : 'N/A',
            'type' => $document->document_type,
            'format' => $document->document_format ?? 'pdf',
            'status' => $document->status,
            'uploaded_at' => $document->created_at ? $document->created_at->toISOString() : null,
            'reviewed_at' => $document->reviewed_at ? $document->reviewed_at->toISOString() : null,
        ];

        // Include document paths
        if ($document->document_format === 'photo_two_side') {
            $data['document_front'] = $document->document_path_front ? asset('storage/'.$document->document_path_front) : null;
            $data['document_back'] = $document->document_path_back ? asset('storage/'.$document->document_path_back) : null;
            $data['document_name_front'] = $document->document_name;
            $data['document_name_back'] = $document->document_name_back;
            $data['file_size_front'] = $document->file_size;
            $data['file_size_back'] = $document->file_size_back;
        } else {
            $data['document_path'] = $document->document_path ? asset('storage/'.$document->document_path) : null;
            $data['document_front'] = $document->document_path_front ? asset('storage/'.$document->document_path_front) : null;
            $data['document_name'] = $document->document_name;
            $data['file_size'] = $document->file_size;
        }

        if ($document->status === 'rejected') {
            $data['rejection_reason'] = $document->rejection_reason;
        }

        if ($document->reviewer) {
            $data['reviewed_by'] = [
                'id' => (string) $document->reviewer->id,
                'name' => $document->reviewer->first_name.' '.$document->reviewer->last_name,
            ];
        }

        return $data;
    }

    /**
     * Authorize admin access
     */
    private function authorizeAdmin()
    {
        $user = auth()->user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }
}
