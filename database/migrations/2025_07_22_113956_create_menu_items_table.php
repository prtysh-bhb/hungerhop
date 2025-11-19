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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('menu_category_id');

            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 8, 2);
            $table->string('image_url', 500)->nullable();
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            $table->text('ingredients')->nullable();
            $table->string('allergens', 500)->nullable();
            $table->integer('preparation_time')->default(15); // in minutes
            $table->boolean('is_available')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->integer('total_sales')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('menu_category_id')->references('id')->on('menu_categories')->onDelete('cascade');

            $table->index(['tenant_id', 'restaurant_id'], 'idx_tenant_restaurant');
            $table->index(['menu_category_id', 'is_available'], 'idx_category_available');
            $table->index(['is_popular', 'average_rating'], 'idx_popular_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
