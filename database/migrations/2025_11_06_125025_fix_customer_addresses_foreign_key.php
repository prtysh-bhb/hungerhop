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
        Schema::table('customer_addresses', function (Blueprint $table) {
            // Drop the incorrect foreign key constraint
            $table->dropForeign(['customer_id']);

            // Add the correct foreign key constraint referencing users table
            $table->foreign('customer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            // Drop the correct foreign key
            $table->dropForeign(['customer_id']);

            // Restore the old (incorrect) foreign key for rollback
            $table->foreign('customer_id')
                ->references('id')
                ->on('customer_profiles')
                ->onDelete('cascade');
        });
    }
};
