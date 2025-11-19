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
        Schema::create('delivery_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->decimal('pickup_latitude', 10, 8);
            $table->decimal('pickup_longitude', 11, 8);
            $table->decimal('delivery_latitude', 10, 8);
            $table->decimal('delivery_longitude', 11, 8);
            $table->decimal('estimated_distance_km', 6, 2)->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->decimal('delivery_fee', 6, 2);
            $table->decimal('tip_amount', 6, 2)->default(0);
            $table->enum('status', ['assigned', 'accepted', 'rejected', 'picked_up', 'delivered', 'cancelled'])->default('assigned');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('delivery_partners')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['order_id', 'status'], 'idx_order_status');
            $table->index(['partner_id', 'status'], 'idx_partner_status');
            $table->index(['assigned_at', 'status'], 'idx_assignment_date');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_assignments');
    }
};
