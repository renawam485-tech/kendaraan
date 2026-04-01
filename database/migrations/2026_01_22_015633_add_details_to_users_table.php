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
        Schema::table('users', function (Blueprint $table) {
            // Kita tambahkan kolom baru SETELAH kolom email
            // Berikan nilai default 'staff' agar user lama tidak error
            $table->string('role')->default('staff')->after('email'); 
            
            // Nullable artinya boleh kosong (opsional)
            $table->string('department')->nullable()->after('role');
            $table->string('phone_number')->nullable()->after('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Jika di-rollback, hanya kolom ini yang dihapus
            $table->dropColumn(['role', 'department', 'phone_number']);
        });
    }
};