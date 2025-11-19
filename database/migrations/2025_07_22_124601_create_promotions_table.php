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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('promotion_code', 50)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed_amount']);
            $table->decimal('discount_value', 8, 2);
            $table->decimal('minimum_order_amount', 8, 2)->default(0);
            $table->decimal('maximum_discount_amount', 8, 2)->nullable();
            $table->integer('usage_limit_per_customer')->default(1);
            $table->integer('total_usage_limit')->nullable();
            $table->integer('current_usage_count')->default(0);
            $table->timestamp('valid_from');
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['promotion_code', 'is_active'], 'idx_code_active');
            $table->index(['tenant_id', 'restaurant_id'], 'idx_tenant_restaurant');
            $table->index(['valid_from', 'valid_until'], 'idx_validity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
