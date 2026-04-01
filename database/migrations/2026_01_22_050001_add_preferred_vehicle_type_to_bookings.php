<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Kolom untuk menampung request jenis mobil (Ex: "Innova Reborn", "HiAce")
            $table->string('preferred_vehicle_type')->nullable()->after('passenger_count');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('preferred_vehicle_type');
        });
    }
};
