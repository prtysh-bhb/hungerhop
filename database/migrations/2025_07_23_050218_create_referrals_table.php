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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('referred_id');
            $table->string('referral_code', 20);
            $table->decimal('referrer_reward_amount', 8, 2)->default(0);
            $table->decimal('referred_reward_amount', 8, 2)->default(0);
            $table->enum('referrer_reward_status', ['pending', 'credited', 'expired'])->default('pending');
            $table->enum('referred_reward_status', ['pending', 'credited', 'expired'])->default('pending');
            $table->unsignedBigInteger('first_order_id')->nullable();
            $table->timestamp('first_order_completed_at')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('referrer_id')->references('id')->on('customer_profiles')->onDelete('cascade');
            $table->foreign('referred_id')->references('id')->on('customer_profiles')->onDelete('cascade');
            $table->foreign('first_order_id')->references('id')->on('orders')->onDelete('set null');

            $table->index(['referrer_id', 'referrer_reward_status'], 'idx_referrer_status');
            $table->index('referral_code', 'idx_referral_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
