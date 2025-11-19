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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('customer_id');
            $table->enum('reviewable_type', ['restaurant', 'delivery_partner']);
            $table->unsignedBigInteger('reviewable_id');
            $table->tinyInteger('rating');
            $table->text('review_text')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->text('admin_response')->nullable();
            $table->timestamp('admin_responded_at')->nullable();
            $table->unsignedBigInteger('admin_responded_by')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customer_profiles')->onDelete('cascade');
            $table->foreign('admin_responded_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['reviewable_type', 'reviewable_id'], 'idx_reviewable');
            $table->index(['rating', 'is_featured'], 'idx_rating_featured');
            $table->index(['customer_id', 'created_at'], 'idx_customer_reviews');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
