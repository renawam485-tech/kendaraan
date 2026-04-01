<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_vehicle_checklists_table.php

    public function up()
    {
        Schema::create('vehicle_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');

            // Tipe cek: 'checkout' (saat ambil), 'checkin' (saat kembali)
            $table->enum('type', ['checkout', 'checkin']);

            // Kondisi Fisik (Boolean: 1 = Bagus, 0 = Rusak/Kurang)
            $table->boolean('lights')->default(true); // Lampu
            $table->boolean('tires')->default(true);  // Ban
            $table->boolean('wipers')->default(true); // Wiper
            $table->boolean('body')->default(true);   // Bodi (Baret/Penyok)
            $table->boolean('fuel_card')->default(true); // Kartu Tol/BBM ada?
            $table->boolean('stnk')->default(true);   // STNK ada?

            // Level BBM (bisa dalam bentuk persen 0-100 atau bar)
            $table->integer('fuel_level');

            $table->text('notes')->nullable(); // Catatan tambahan (misal: "Baret halus di pintu kiri")

            // Opsional: Simpan path foto bukti kondisi mobil
            $table->string('photo_path')->nullable();

            $table->foreignId('checked_by')->constrained('users'); // Siapa petugas yang mengecek
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_checklists');
    }
};
