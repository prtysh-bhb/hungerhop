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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('initiated_by');
            $table->unsignedBigInteger('tenant_id');
            $table->decimal('refund_amount', 10, 2);
            $table->enum('refund_reason', ['order_cancelled', 'order_rejected', 'dispute_resolution', 'partial_refund']);
            $table->enum('refund_method', ['original_source', 'wallet']);
            $table->string('gateway_refund_id')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('gateway_response')->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamp('initiated_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('initiated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['payment_id', 'status'], 'idx_payment_status');
            $table->index(['order_id', 'refund_reason'], 'idx_order_reason');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
