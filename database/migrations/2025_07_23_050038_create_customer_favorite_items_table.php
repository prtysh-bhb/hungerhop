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
        Schema::create('customer_favorite_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamp('added_at')->useCurrent();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customer_profiles')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->unique(['customer_id', 'item_id'], 'unique_customer_item');
            $table->index(['customer_id', 'added_at'], 'idx_customer_favorites');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_favorite_items');
    }
};
