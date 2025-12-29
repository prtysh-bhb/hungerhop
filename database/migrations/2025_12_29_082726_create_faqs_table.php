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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()
                ->comment('NULL = global FAQ, else tenant specific');
            $table->string('question');
            $table->longText('answer');
            $table->enum('target_role', [
                'customer',
                'restaurant',
                'delivery_partner',
                'admin',
                'all',
            ])->default('all');
            $table->string('category')->nullable()
                ->comment('General, Payments, Orders, Delivery, etc');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'target_role']);
            $table->index(['category', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
