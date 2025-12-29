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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('pay_type', ['bank', 'upi'])->default('bank');
            $table->string('pay_bank_name', 150)->nullable();
            $table->string('pay_bank_account_number', 50)->nullable();
            $table->string('pay_bank_ifsc', 20)->nullable();
            $table->string('pay_upi_id', 100)->nullable();
            $table->string('account_holder_name', 150)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index('user_id', 'idx_payment_details_user');
            $table->index(['user_id', 'pay_type'], 'idx_payment_details_user_type');
            // $table->index(['user_id', 'is_primary'], 'idx_payment_details_user_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
