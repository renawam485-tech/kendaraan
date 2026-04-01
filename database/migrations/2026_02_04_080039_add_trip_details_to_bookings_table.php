<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Tambahkan kolom baru, cek dulu belum ada
            if (!Schema::hasColumn('bookings', 'prepared_at')) {
                $table->timestamp('prepared_at')->nullable()->after('end_time');
            }
            
            // Jika odo_start belum ada di migration sebelumnya
            if (!Schema::hasColumn('bookings', 'odo_start')) {
                $table->integer('odo_start')->nullable()->after('completed_at');
            }
            
            if (!Schema::hasColumn('bookings', 'odo_end')) {
                $table->integer('odo_end')->nullable()->after('odo_start');
            }
            
            if (!Schema::hasColumn('bookings', 'trip_notes')) {
                $table->text('trip_notes')->nullable()->after('odo_end');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['prepared_at', 'odo_start', 'odo_end', 'trip_notes']);
        });
    }
};