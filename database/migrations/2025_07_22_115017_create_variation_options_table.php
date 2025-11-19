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
        Schema::create('variation_options', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('variation_id');
            $table->unsignedBigInteger('tenant_id');
            $table->string('option_name');
            $table->decimal('price_modifier', 8, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();

            $table->foreign('variation_id')->references('id')->on('menu_item_variations')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->index(['variation_id', 'sort_order'], 'idx_variation_sort');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_options');
    }
};
