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
        Schema::table('restaurant_documents', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['tenant_id']);

            // Make tenant_id nullable
            $table->unsignedBigInteger('tenant_id')->nullable()->change();

            // Re-add the foreign key constraint with nullable support
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_documents', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['tenant_id']);

            // Make tenant_id required again
            $table->unsignedBigInteger('tenant_id')->nullable(false)->change();

            // Re-add the original cascade foreign key
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }
};
