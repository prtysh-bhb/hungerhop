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
            $table->enum('type', ['menu_item', 'restaurant']);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            /* =====================
             | Foreign Keys
             ===================== */
            $table->foreign('customer_id')
                ->references('id')
                ->on('customer_profiles')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade');

            $table->foreign('restaurant_id')
                ->references('id')
                ->on('restaurants')
                ->onDelete('cascade');

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');

            /* =====================
             | Indexes & Constraints
             ===================== */

            // Prevent duplicate favorites (type-aware)
            $table->unique(
                ['customer_id', 'type', 'item_id', 'restaurant_id'],
                'unique_customer_favorite'
            );

            $table->index(['customer_id', 'added_at'], 'idx_customer_favorites');
            $table->index(['tenant_id'], 'idx_tenant_favorite');
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
