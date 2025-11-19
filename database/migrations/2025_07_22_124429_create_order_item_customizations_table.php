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
        Schema::create('order_item_customizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedBigInteger('variation_id');
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('tenant_id');

            $table->string('variation_name', 255);
            $table->string('option_name', 255);
            $table->decimal('price_modifier', 8, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('menu_item_variations')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('variation_options')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['order_item_id', 'variation_id'], 'idx_order_item_variation');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_customizations');
    }
};
