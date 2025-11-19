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
        Schema::create('delivery_partner_documents', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('partner_id');
            $table->enum('document_type', ['id_proof', 'driving_license', 'rc', 'address_proof', 'bank_passbook']);
            $table->string('document_path', 500);
            $table->string('document_name', 255);
            $table->integer('file_size');
            $table->string('mime_type', 100);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('partner_id')->references('id')->on('delivery_partners')->onDelete('cascade');

            $table->index(['partner_id', 'document_type'], 'idx_partner_type');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_partner_documents');
    }
};
