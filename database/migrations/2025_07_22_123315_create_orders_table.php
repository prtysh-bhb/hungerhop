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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('delivery_address_id');
            $table->unsignedBigInteger('tenant_id');
            $table->enum('status', [
                'placed', 'accepted', 'preparing', 'ready_for_pickup',
                'assigned_to_delivery', 'picked_up', 'out_for_delivery',
                'delivered', 'cancelled', 'rejected',
            ])->default('placed');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->decimal('delivery_fee', 6, 2)->default(0);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('restaurant_amount', 10, 2);
            $table->decimal('delivery_amount', 6, 2);
            $table->decimal('platform_fee', 6, 2)->default(0);
            $table->enum('payment_method', ['wallet', 'card', 'upi', 'netbanking', 'cod']);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('special_instructions')->nullable();
            $table->string('pickup_otp', 6)->nullable();
            $table->string('delivery_otp', 6)->nullable();
            $table->timestamp('pickup_otp_verified_at')->nullable();
            $table->timestamp('delivery_otp_verified_at')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->enum('cancelled_by', ['customer', 'restaurant', 'admin'])->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('auto_accept_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customer_profiles')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('delivery_address_id')->references('id')->on('customer_addresses')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['customer_id', 'status'], 'idx_customer_status');
            $table->index(['restaurant_id', 'status'], 'idx_restaurant_status');
            $table->index('order_number', 'idx_order_number');
            $table->index(['created_at', 'status'], 'idx_created_status');
            $table->index(['pickup_otp', 'delivery_otp'], 'idx_otp_verification');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['restaurant_id']);
            $table->dropForeign(['delivery_address_id']);
            $table->dropForeign(['tenant_id']);
        });
        Schema::dropIfExists('orders');
    }
};
