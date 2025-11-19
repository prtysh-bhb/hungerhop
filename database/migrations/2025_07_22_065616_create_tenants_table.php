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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_name');
            $table->string('contact_person');
            $table->string('email')->unique();
            $table->string('phone');
            $table->enum('subscription_plan', ['LITE', 'PLUS', 'PRO_MAX'])->default('LITE');
            $table->integer('total_restaurants')->default(0);
            $table->decimal('monthly_base_fee', 10, 2);
            $table->decimal('per_restaurant_fee', 10, 2);
            $table->integer('banner_limit')->default(0);
            $table->enum('status', ['pending', 'approved', 'suspended', 'rejected', 'subscription_expired'])->default('pending');
            $table->date('subscription_start_date')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->timestamps();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
