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
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->enum('otp_type', ['pickup', 'delivery']);
            $table->string('otp_code', 6);
            $table->string('phone_number', 20);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->integer('attempts_count')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['order_id', 'otp_type'], 'idx_order_type');
            $table->index(['is_verified', 'expires_at'], 'idx_verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
