<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_disputes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('raised_by');
            $table->enum('dispute_type', [
                'food_quality', 'wrong_order', 'late_delivery', 'damaged_food',
                'missing_items', 'billing_issue', 'delivery_issue',
            ]);
            $table->text('description');
            $table->json('evidence_images')->nullable();
            $table->enum('status', ['pending', 'investigating', 'resolved', 'rejected', 'escalated'])->default('pending');
            $table->text('resolution_notes')->nullable();
            $table->decimal('compensation_amount', 8, 2)->default(0);
            $table->enum('compensation_type', ['refund', 'wallet_credit', 'voucher', 'none'])->nullable();
            $table->decimal('wallet_deduction_restaurant', 8, 2)->default(0);
            $table->decimal('wallet_deduction_delivery', 8, 2)->default(0);
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('raised_by')->references('id')->on('customer_profiles')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['order_id', 'status'], 'idx_order_status');
            $table->index(['assigned_to', 'status'], 'idx_assigned_status');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_disputes');
    }
};
