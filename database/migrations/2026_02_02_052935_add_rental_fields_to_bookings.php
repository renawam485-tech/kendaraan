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
    Schema::table('bookings', function (Blueprint $table) {
        // Cek dulu sebelum tambah kolom 'is_rental'
        if (!Schema::hasColumn('bookings', 'is_rental')) {
            $table->boolean('is_rental')->default(false)->after('with_driver');
        }

        // Cek dulu sebelum tambah kolom 'preferred_vehicle_type'
        if (!Schema::hasColumn('bookings', 'preferred_vehicle_type')) {
            $table->string('preferred_vehicle_type')->nullable()->after('is_rental');
        }

        // Cek dulu sebelum tambah kolom 'passenger_count'
        if (!Schema::hasColumn('bookings', 'passenger_count')) {
            $table->integer('passenger_count')->nullable()->after('preferred_vehicle_type');
        }
    });
}

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_rental', 'preferred_vehicle_type', 'passenger_count']);
        });
    }
};
