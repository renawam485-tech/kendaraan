<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_maintenances_table.php

    public function up()
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');

            // Kapan masuk bengkel, kapan estimasi selesai
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Jenis: 'regular' (ganti oli), 'repair' (rusak), 'accident' (kecelakaan)
            $table->string('type');

            $table->text('description'); // Cth: Ganti kampas rem & oli
            $table->decimal('cost', 15, 2)->nullable(); // Biaya servis

            // Status pengerjaan
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])->default('scheduled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
