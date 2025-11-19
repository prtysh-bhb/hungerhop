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
        Schema::create('item_compatibility', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('compatible_item_id');
            $table->enum('compatibility_type', ['recommended', 'frequently_bought_together', 'substitute']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('compatible_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->unique(['item_id', 'compatible_item_id', 'compatibility_type'], 'unique_compatibility');
            $table->index(['item_id', 'compatibility_type'], 'idx_item_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_compatibility');
    }
};
