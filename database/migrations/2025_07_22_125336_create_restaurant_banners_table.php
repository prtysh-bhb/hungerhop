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
        Schema::create('restaurant_banners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('tenant_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url', 500);
            $table->enum('link_type', ['restaurant', 'menu_item', 'promotion', 'external']);
            $table->unsignedBigInteger('link_id')->nullable();
            $table->string('external_url', 500)->nullable();
            $table->enum('banner_position', ['home_slider', 'restaurant_page', 'category_page'])->default('home_slider');
            $table->integer('sort_order')->default(0);
            $table->integer('click_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('valid_from');
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['restaurant_id', 'banner_position'], 'idx_restaurant_position');
            $table->index(['tenant_id', 'is_active'], 'idx_tenant_active');
            $table->index(['is_active', 'valid_from', 'valid_until'], 'idx_active_validity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_banners');
    }
};
