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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('tenant_id');
            $table->enum('payment_method', ['wallet', 'card', 'upi', 'netbanking', 'cod']);
            $table->enum('payment_gateway', ['razorpay', 'stripe', 'paytm', 'phonepe', 'wallet']);
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_payment_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->text('gateway_response')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('initiated_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['order_id', 'status'], 'idx_order_status');
            $table->index('gateway_transaction_id', 'idx_gateway_txn');
            $table->index(['status', 'completed_at'], 'idx_status_date');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_');
    }
};
