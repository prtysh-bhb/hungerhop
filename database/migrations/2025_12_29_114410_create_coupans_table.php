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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            // Coupon identity
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();

            // Discount details
            $table->enum('discount_type', ['flat', 'percentage']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable();

            // Order constraints
            $table->decimal('min_order_value', 10, 2);

            // Usage limits
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_user')->default(1);

            // Validity
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();

            // Scope
            $table->enum('coupon_scope', ['global', 'restaurant'])
                ->default('global');

            // Status
            $table->boolean('is_active')->default(true);

            // Created by (future-proof)
            $table->enum('created_by', ['admin', 'restaurant'])
                ->default('admin');

            $table->timestamps();

            // Indexes (performance)
            $table->index('code');
            $table->index('is_active');
            $table->index('coupon_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
