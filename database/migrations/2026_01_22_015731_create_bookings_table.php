<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('destination');
            $table->text('purpose');
            $table->boolean('with_driver')->default(true);

            // Status Workflow
            $table->enum('status', [
                'pending', 'approved', 'rejected', 
                'prepared', 'active', 'completed', 
                'cancel_req', 'cancelled'
            ])->default('pending');

            // Fulfillment (Internal vs External)
            $table->enum('fulfillment_source', ['internal', 'external'])->nullable();

            // Opsi Internal
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');

            // Opsi External (Vendor)
            $table->string('vendor_name')->nullable();
            $table->string('external_vehicle_detail')->nullable();
            $table->string('external_driver_name')->nullable();
            $table->string('external_driver_phone')->nullable();
            $table->decimal('vendor_cost', 15, 2)->nullable();

            // Logs
            $table->integer('odo_start')->nullable();
            $table->integer('odo_end')->nullable();
            $table->text('trip_notes')->nullable();

            // Cancellation
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->decimal('cancellation_fee', 15, 2)->default(0); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};