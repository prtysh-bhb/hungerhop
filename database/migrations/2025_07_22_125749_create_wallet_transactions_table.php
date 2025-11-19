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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id');
            $table->string('reference_number', 100)->unique();
            $table->enum('transaction_type', ['credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->enum('purpose', [
                'order_payment', 'order_credit_restaurant', 'order_credit_delivery', 'refund',
                'dispute_fine', 'subscription_payment', 'payout_request', 'wallet_topup',
                'tip_received', 'commission_deduction', 'pickup_release', 'delivery_release',
            ]);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('previous_balance', 10, 2);
            $table->decimal('current_balance', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->index(['wallet_id', 'transaction_type'], 'idx_wallet_type');
            $table->index('reference_number', 'idx_reference');
            $table->index(['purpose', 'status'], 'idx_purpose_status');
            $table->index('processed_at', 'idx_processed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
