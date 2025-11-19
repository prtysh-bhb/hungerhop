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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('account_type', ['restaurant', 'delivery_partner']);
            $table->string('account_holder_name');
            $table->string('bank_name');
            $table->string('account_number', 50);
            $table->string('ifsc_code', 20);
            $table->string('branch_name')->nullable();
            $table->string('upi_id', 100)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_method', ['penny_drop', 'manual', 'api'])->nullable();
            $table->timestamp('verification_date')->nullable();
            $table->boolean('is_primary')->default(true);
            $table->decimal('min_transfer_amount', 8, 2)->default(100.00);
            $table->decimal('max_daily_transfer', 10, 2)->default(50000.00);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'account_type'], 'idx_user_type');
            $table->index(['is_verified', 'verification_date'], 'idx_verification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
