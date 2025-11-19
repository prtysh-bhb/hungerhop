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
        Schema::create('restaurant_working_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('tenant_id');
            $table->tinyInteger('day_of_week');
            $table->boolean('is_open')->default(true);
            $table->time('open_time')->default('09:00:00');
            $table->time('close_time')->default('22:00:00');
            $table->time('break_start_time')->nullable();
            $table->time('break_end_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->unique(['restaurant_id', 'day_of_week'], 'unique_restaurant_day');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_working_hours');
    }
};
