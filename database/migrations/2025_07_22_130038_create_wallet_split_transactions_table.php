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
        Schema::create('wallet_split_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('restaurant_wallet_id');
            $table->unsignedBigInteger('delivery_wallet_id');
            $table->decimal('total_order_amount', 10, 2);
            $table->decimal('restaurant_amount', 10, 2);
            $table->decimal('delivery_amount', 6, 2);
            $table->decimal('platform_fee', 6, 2)->default(0);
            $table->boolean('restaurant_released')->default(false);
            $table->boolean('delivery_released')->default(false);
            $table->enum('restaurant_release_trigger', ['pickup_otp', 'manual', 'auto'])->nullable();
            $table->enum('delivery_release_trigger', ['delivery_otp', 'manual', 'auto'])->nullable();
            $table->timestamp('restaurant_released_at')->nullable();
            $table->timestamp('delivery_released_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('restaurant_wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('delivery_wallet_id')->references('id')->on('wallets')->onDelete('cascade');

            $table->index(['order_id', 'restaurant_released', 'delivery_released'], 'idx_order_release');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_split_transactions');
    }
};
