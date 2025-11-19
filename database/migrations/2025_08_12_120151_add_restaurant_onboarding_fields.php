<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Add these columns if they don't exist
            if (! Schema::hasColumn('restaurants', 'setup_completed')) {
                $table->boolean('setup_completed')->default(false)->after('is_featured');
            }

            if (! Schema::hasColumn('restaurants', 'onboarding_step')) {
                $table->integer('onboarding_step')->default(1)->after('setup_completed');
            }

            if (! Schema::hasColumn('restaurants', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_by');
            }

            if (! Schema::hasColumn('restaurants', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            }

            if (! Schema::hasColumn('restaurants', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'setup_completed',
                'onboarding_step',
                'rejected_at',
                'rejected_by',
                'rejection_reason',
            ]);
        });
    }
};
