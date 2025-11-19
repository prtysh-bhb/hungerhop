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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['customer', 'restaurant', 'delivery_partner']);
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->decimal('pending_balance', 10, 2)->default(0);
            $table->decimal('available_balance', 10, 2)->default(0);
            $table->decimal('total_credited', 10, 2)->default(0);
            $table->decimal('total_debited', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->decimal('daily_transfer_limit', 10, 2)->default(50000.00);
            $table->decimal('monthly_transfer_limit', 10, 2)->default(1000000.00);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'user_type'], 'unique_user_wallet');
            $table->index(['user_id', 'user_type'], 'idx_user_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
