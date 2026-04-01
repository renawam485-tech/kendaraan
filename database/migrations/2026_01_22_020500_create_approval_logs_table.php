<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_approval_logs_table.php

    public function up()
    {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Siapa yang melakukan aksi (Atasan/Admin)

            // Aksi yang dilakukan: 'approved', 'rejected', 'revised', 'cancelled'
            $table->string('action');

            $table->text('comment')->nullable(); // Alasan penolakan/catatan

            $table->timestamps(); // Mencatat kapan aksi dilakukan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};
