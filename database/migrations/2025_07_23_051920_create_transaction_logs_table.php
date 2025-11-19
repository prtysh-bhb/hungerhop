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
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['restaurant', 'delivery_partner']);
            $table->enum('transaction_type', ['order_credit', 'bank_transfer_debit']);
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('payout_id')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->string('description', 500);
            $table->decimal('wallet_balance_before', 10, 2);
            $table->decimal('wallet_balance_after', 10, 2);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('payout_id')->references('id')->on('automated_payout_requests')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['user_id', 'user_type', 'created_at'], 'idx_user_type_date');
            $table->index(['transaction_type', 'created_at'], 'idx_transaction_type');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};
