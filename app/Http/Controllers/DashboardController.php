<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $aktifStatuses  = ['pending', 'approved', 'prepared', 'active'];
        $doneStatuses   = ['completed'];
        $closedStatuses = ['cancelled','rejected'];

        // PAGINATION PENGAJUAN AKTIF
        $bookingsAktif = $user->bookings()
            ->with(['vehicle','approver'])
            ->whereIn('status', $aktifStatuses)
            ->latest()
            ->paginate(5, ['*'], 'aktif_page');

        // PAGINATION RIWAYAT
        $bookingsRiwayat = $user->bookings()
            ->with(['vehicle','approver'])
            ->whereIn('status', array_merge($doneStatuses, $closedStatuses))
            ->latest()
            ->paginate(5, ['*'], 'riwayat_page');

        // TOTAL SEMUA
        $totalAll = $user->bookings()->count();

        // HITUNG STATISTIK
        $totalAktif = $user->bookings()
            ->whereIn('status', $aktifStatuses)
            ->count();

        $totalDone = $user->bookings()
            ->whereIn('status', $doneStatuses)
            ->count();

        $totalClosed = $user->bookings()
            ->whereIn('status', $closedStatuses)
            ->count();

        return view('dashboard', compact(
            'bookingsAktif',
            'bookingsRiwayat',
            'totalAll',
            'totalAktif',
            'totalDone',
            'totalClosed'
        ));
    }
}