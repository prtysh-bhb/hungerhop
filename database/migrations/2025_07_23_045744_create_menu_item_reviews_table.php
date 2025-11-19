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
        Schema::create('menu_item_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('item_id');
            $table->tinyInteger('rating')->check('rating >= 1 AND rating <= 5');
            $table->text('review_text')->nullable();
            $table->json('images')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customer_profiles')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('menu_items')->onDelete('cascade');

            $table->index(['item_id', 'rating'], 'idx_item_rating');
            $table->index(['customer_id', 'item_id'], 'idx_customer_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_reviews');
    }
};
