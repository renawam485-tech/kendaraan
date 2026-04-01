<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // Tampilkan daftar mobil
    public function index(Request $request)
    {
        $vehicles = Vehicle::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                                                  ->orWhere('license_plate', 'like', "%{$request->search}%"))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->asset_status, fn($q) => $q->where('asset_status', $request->asset_status))
            ->paginate(15)
            ->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    // Form tambah mobil
    public function create()
    {
        return view('admin.vehicles.create');
    }

    // Simpan mobil baru ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'license_plate' => 'required|string|unique:vehicles,license_plate',
            'type'          => 'required|string',
            'asset_status'  => 'required|in:available,maintenance,disposal',
            'notes'         => 'nullable|string',
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Mobil baru berhasil ditambahkan.');
    }

    // Form edit mobil
    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    // Update data mobil
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'license_plate' => 'required|string|unique:vehicles,license_plate,' . $vehicle->id,
            'type'          => 'required|string',
            'asset_status'  => 'required|in:available,maintenance,disposal',
            'notes'         => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Data mobil diperbarui.');
    }

    // Hapus mobil
    public function destroy(Vehicle $vehicle)
    {
        // Cek dulu apakah mobil pernah dipakai booking?
        if ($vehicle->bookings()->exists()) {
            return back()->with('error', 'Mobil tidak bisa dihapus karena memiliki riwayat peminjaman. Ubah statusnya menjadi Disposal saja.');
        }

        $vehicle->delete();
        return back()->with('success', 'Mobil dihapus.');
    }
}