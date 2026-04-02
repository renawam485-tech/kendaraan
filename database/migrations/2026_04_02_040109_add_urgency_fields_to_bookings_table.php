<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_urgent')
                  ->default(false)
                  ->after('is_rental');

            $table->timestamp('approval_deadline')
                  ->nullable()
                  ->after('is_urgent');

            $table->boolean('escalated_to_admin')
                  ->default(false)
                  ->after('approval_deadline');

            $table->text('escalated_reason')
                  ->nullable()
                  ->after('escalated_to_admin');
        });

        // Index untuk mempercepat query cron
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['status', 'approval_deadline', 'escalated_to_admin'], 'idx_escalation_check');
            $table->index('is_urgent', 'idx_is_urgent');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_escalation_check');
            $table->dropIndex('idx_is_urgent');
            $table->dropColumn([
                'is_urgent',
                'approval_deadline',
                'escalated_to_admin',
                'escalated_reason',
            ]);
        });
    }
};