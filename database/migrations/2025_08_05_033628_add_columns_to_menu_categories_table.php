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
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_template_id');
            $table->foreign('menu_template_id')->references('id')->on('menu_templates')->onDelete('cascade');
            $table->renameColumn('category_name', 'name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->dropForeign(['menu_template_id']);
            $table->dropColumn('menu_template_id');
            $table->renameColumn('name', 'category_name');
        });
    }
};
