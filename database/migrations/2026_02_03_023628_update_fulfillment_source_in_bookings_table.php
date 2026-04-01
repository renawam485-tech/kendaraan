<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Menggunakan DB::statement karena mengubah ENUM via Schema Builder sering bermasalah
        DB::statement("ALTER TABLE bookings MODIFY COLUMN fulfillment_source ENUM('internal', 'external', 'dispatch') NULL");
    }

    public function down()
    {
        // Kembalikan ke state awal jika rollback (sesuaikan dengan enum lama Anda)
        DB::statement("ALTER TABLE bookings MODIFY COLUMN fulfillment_source ENUM('internal', 'external') NULL");
    }
};
