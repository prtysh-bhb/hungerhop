<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update delivery_partner_documents table to support two-sided photo documents
     */
    public function up(): void
    {
        Schema::table('delivery_partner_documents', function (Blueprint $table) {
            $table->string('document_path', 500)->nullable()->change();
            // NEW two-sided support
            $table->string('document_path_front', 500)->nullable()->after('document_path');
            $table->string('document_path_back', 500)->nullable()->after('document_path_front');
            $table->enum('document_format', ['pdf', 'photo_single_side', 'photo_two_side'])->default('pdf')->after('document_path_back');
            $table->string('document_name_back', 255)->nullable()->after('document_name');
            $table->integer('file_size_back')->nullable()->after('file_size');
            $table->string('mime_type_back', 100)->nullable()->after('mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_partner_documents', function (Blueprint $table) {
            $table->dropColumn([
                'document_path_front',
                'document_path_back',
                'document_format',
                'document_name_back',
                'file_size_back',
                'mime_type_back',
            ]);
        });
    }
};
