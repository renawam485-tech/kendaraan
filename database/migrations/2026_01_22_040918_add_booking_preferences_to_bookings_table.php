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
            // Kolom untuk menampung jumlah penumpang (jika minta dicarikan)
            $table->integer('passenger_count')->nullable()->after('purpose');

            // Kita ubah vehicle_id jadi nullable (karena kalau minta dicarikan, ini kosong dulu)
            // Perlu install dbal: composer require doctrine/dbal (jika laravel lama), 
            // tapi di Laravel 10/11 biasanya langsung support change().
            // Jika error, lewati baris change() ini dan pastikan di migration awal sudah nullable.
            // Di kode sebelumnya saya sudah set ->nullable(), jadi aman.
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('passenger_count');
        });
    }
};
