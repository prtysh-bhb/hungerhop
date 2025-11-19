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
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->enum('subscription_plan', ['LITE', 'PLUS', 'PRO_MAX']);
            $table->integer('restaurant_count');
            $table->decimal('base_amount', 10, 2);
            $table->decimal('per_restaurant_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->enum('payment_method', ['card', 'upi', 'netbanking', 'wallet']);
            $table->enum('payment_gateway', ['razorpay', 'stripe', 'paytm', 'phonepe']);
            $table->string('gateway_transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('auto_retry_count')->default(0);
            $table->date('next_retry_date')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
            $table->index(['due_date', 'status'], 'idx_due_date');
            $table->index(['billing_period_start', 'billing_period_end'], 'idx_billing_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
