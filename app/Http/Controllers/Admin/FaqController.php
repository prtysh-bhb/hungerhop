<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * List all FAQs (Admin + Tenant level)
     */
    public function index()
    {
        $faqs = Faq::whereNull('deleted_at')
            ->ordered()
            ->get();

        return view('admin.faq.index', compact('faqs'));
    }

    /**
     * Show create FAQ form
     */
    public function create()
    {
        return view('admin.faq.create');
    }

    /**
     * Store new FAQ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'target_role' => 'required|string|in:all,customer,restaurant,delivery_partner,admin',
            'order'    => 'nullable|integer|min:0',
            'is_active'=> 'boolean',
        ]);

        Faq::create([
            'tenant_id' => auth()->user()->tenant_id ?? null, 
            'question'  => $validated['question'],
            'answer'    => $validated['answer'] ?? null,
            'category'  => $validated['category'] ?? null,
            'target_role' => $validated['target_role'],
            'sort_order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
            'created_by'=> auth()->id(),
        ]);

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faq.edit', compact('faq'));
    }

    /**
     * Update FAQ
     */
    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'target_role' => 'required|string|in:all,customer,restaurant,delivery_partner,admin',
            'order'    => 'nullable|integer|min:0',
            'is_active'=> 'boolean',
        ]);

        $faq->update([
            'question'  => $validated['question'],
            'answer'    => $validated['answer'] ?? null,
            'category'  => $validated['category'] ?? $faq->category,
            'target_role' => $validated['target_role'],
            'sort_order' => $validated['order'] ?? $faq->sort_order,
            'is_active' => $validated['is_active'] ?? $faq->is_active,
        ]);

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ updated successfully.');
    }

    /**
     * Show reply page (your existing logic)
     */
    public function reply($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faq.reply', compact('faq'));
    }

    /**
     * Submit reply (answer only)
     */
    public function submitReply(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'answer' => 'required|string',
        ]);

        $faq->update([
            'answer' => $request->answer,
        ]);

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ answer updated successfully.');
    }

    /**
     * Delete FAQ (Soft delete)
     */
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    /**
     * Toggle active/inactive
     */
    public function toggleStatus($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->update([
            'is_active' => ! $faq->is_active,
        ]);

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ status updated.');
    }
}
