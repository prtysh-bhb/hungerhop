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
        Schema::create('delivery_partners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('vehicle_type', ['bicycle', 'motorcycle', 'car']);
            $table->string('vehicle_number', 20);
            $table->string('license_number', 50);
            $table->string('profile_image_url', 500)->nullable();
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->boolean('is_available')->default(false);
            $table->boolean('is_online')->default(false);
            $table->integer('total_deliveries')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('commission_percentage', 5, 2)->default(20.00);
            $table->timestamp('last_location_update')->nullable();
            $table->enum('status', ['pending', 'approved', 'suspended', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['status', 'is_available'], 'idx_status_available');
            $table->index(['current_latitude', 'current_longitude'], 'idx_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_partners');
    }
};
