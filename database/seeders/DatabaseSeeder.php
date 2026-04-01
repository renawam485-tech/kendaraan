<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun ADMIN GA (Login pakai ini untuk tes Admin)
        User::create([
            'name' => 'Admin',
            'email' => 'admin@kantor.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin_ga',
            'department' => 'General Affair'
        ]);

        // 2. Akun ATASAN (Login pakai ini untuk tes Approve)
        User::create([
            'name' => 'Manager',
            'email' => 'manager@kantor.com',
            'password' => Hash::make('manager123'),
            'role' => 'approver',
            'department' => 'Marketing'
        ]);

        // 3. Akun STAFF (Login pakai ini untuk tes Request)
        User::create([
            'name' => 'Rendi',
            'email' => 'staff@kantor.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'department' => 'Marketing'
        ]);

        // 4. Akun DRIVER
        User::create([
            'name' => 'Mang Ujang',
            'email' => 'ujang@driver.com',
            'password' => Hash::make('password'),
            'role' => 'driver',
            'phone_number' => '08123456789'
        ]);

        // 5. Data Mobil Dummy
        Vehicle::create([
            'name' => 'Toyota Avanza',
            'license_plate' => 'B 1010 AA',
            'type' => 'MPV',
            'asset_status' => 'available'
        ]);

        Vehicle::create([
            'name' => 'Toyota Innova Reborn',
            'license_plate' => 'B 2020 BB',
            'type' => 'MPV',
            'asset_status' => 'available'
        ]);

        Vehicle::create([
            'name' => 'Daihatsu GranMax',
            'license_plate' => 'B 9999 BOX',
            'type' => 'Box',
            'asset_status' => 'maintenance' // Mobil rusak
        ]);
    }
}