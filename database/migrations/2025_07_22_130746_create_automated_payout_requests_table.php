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
        Schema::create('automated_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_id');
            $table->enum('user_type', ['restaurant', 'delivery_partner']);
            $table->unsignedBigInteger('bank_account_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('wallet_balance_before', 10, 2);
            $table->decimal('wallet_balance_after', 10, 2);
            $table->enum('status', ['initiated', 'processing', 'completed', 'failed', 'retry_pending'])->default('initiated');
            $table->string('gateway_transfer_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->integer('max_retry_attempts')->default(3);
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index('requested_at', 'idx_requested_date');
            $table->index(['status', 'next_retry_at'], 'idx_retry_schedule');
            $table->index(['status', 'processed_at'], 'idx_processing_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automated_payout_requests');
    }
};
