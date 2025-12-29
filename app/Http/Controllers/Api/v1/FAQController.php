<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Get FAQs for customer app
     */
    public function index(Request $request)
    {
        $tenantId = $request->input('tenant_id');

        $faqs = Faq::query()
            ->where('is_active', true)
            ->whereIn('target_role', ['customer', 'all'])
            ->where(function ($q) use ($tenantId) {
                if ($tenantId) {
                    $q->whereNull('tenant_id')
                      ->orWhere('tenant_id', $tenantId);
                } else {
                    $q->whereNull('tenant_id');
                }
            })
            ->ordered()
            ->get([
                'id',
                'question',
                'answer',
                'category'
            ]);

        return response()->json([
            'success' => true,
            'data' => $faqs,
        ]);
    }

    /**
     * Customer submits a question (NO answer allowed)
     * POST /api/v1/faqs
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question'  => 'required|string|max:500',
            'category'  => 'nullable|string|max:100',
            'tenant_id' => 'nullable|integer',
        ]);

        $faq = Faq::create([
            'tenant_id'   => $validated['tenant_id'] ?? null,
            'question'    => $validated['question'],
            'answer'      => '', // empty until admin replies
            'category'    => $validated['category'] ?? 'General',
            'target_role' => 'customer',
            'is_active'   => false, // admin approval required
            'sort_order'  => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your question has been submitted. Our team will reply soon.',
            'data' => [
                'id' => $faq->id,
                'question' => $faq->question,
            ],
        ], 201);
    }

    /**
     * Show single FAQ (read-only)
     */
    public function show(Faq $faq)
    {
        // Only allow access if active and for customer/all
        if (!$faq->is_active || !in_array($faq->target_role, ['customer', 'all'])) {
            abort(404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'category' => $faq->category,
            ],
        ]);
    }
}
