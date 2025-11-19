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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('location_admin_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Owner/Creator user
            $table->string('restaurant_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cuisine_type', 100)->nullable();
            $table->text('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('postal_code', 20);
            $table->string('phone', 20);
            $table->string('email');
            $table->string('website_url', 500)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->string('cover_image_url', 500)->nullable();
            $table->integer('delivery_radius_km')->default(10);
            $table->decimal('minimum_order_amount', 8, 2)->default(0);
            $table->decimal('base_delivery_fee', 6, 2)->default(0);
            $table->decimal('restaurant_commission_percentage', 5, 2)->default(80.00);
            $table->integer('estimated_delivery_time')->default(30);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->boolean('is_open')->default(true);
            $table->boolean('accepts_orders')->default(true);
            $table->boolean('is_paused')->default(false);
            $table->enum('status', ['pending', 'approved', 'suspended', 'rejected'])->default('pending');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_orders')->default(0);
            $table->json('business_hours')->nullable(); // Store business hours as JSON
            $table->text('special_instructions')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('location_admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['tenant_id', 'status'], 'idx_tenant_status');
            $table->index(['latitude', 'longitude', 'status'], 'idx_location_status');
            $table->index(['city', 'status'], 'idx_city_status');
            $table->index(['user_id'], 'idx_user_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
