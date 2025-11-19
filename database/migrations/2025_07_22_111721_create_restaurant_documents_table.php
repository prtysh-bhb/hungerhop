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
        Schema::create('restaurant_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('tenant_id');
            $table->enum('document_type', [
                'food_safety_certificate',
                'business_license',
                'pan_card',
                'gst_certificate',
                'owner_id_proof',
                'bank_details',
                'insurance_certificate',
                'fire_safety_certificate',
                'trade_license',
                'pollution_certificate',
            ]);
            $table->string('document_path', 500);
            $table->string('document_name');
            $table->string('original_filename', 500);
            $table->integer('file_size');
            $table->string('mime_type', 100);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('expires_at')->nullable(); // For documents with expiry
            $table->boolean('is_verified')->default(false);
            $table->json('metadata')->nullable(); // Store additional document metadata
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Indexes
            $table->index(['restaurant_id', 'document_type'], 'idx_restaurant_type');
            $table->index('status', 'idx_status');
            $table->index(['tenant_id'], 'idx_tenant_restaurant');
            $table->index(['expires_at'], 'idx_expires_at');

            // Unique constraint to prevent duplicate document types per restaurant
            $table->unique(['restaurant_id', 'document_type'], 'unique_restaurant_document_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_documents');
    }
};
