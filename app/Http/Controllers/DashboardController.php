<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\User;
use App\Enums\BookingStatus;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $aktifStatuses  = ['pending', 'approved', 'prepared', 'active'];
        $doneStatuses   = ['completed'];
        $closedStatuses = ['cancelled', 'rejected'];

        // PAGINATION PENGAJUAN AKTIF — 10 per halaman
        $bookingsAktif = $user->bookings()
            ->with(['vehicle', 'approver'])
            ->whereIn('status', $aktifStatuses)
            ->latest()
            ->paginate(10, ['*'], 'aktif_page');

        // PAGINATION RIWAYAT
        $bookingsRiwayat = $user->bookings()
            ->with(['vehicle', 'approver'])
            ->whereIn('status', array_merge($doneStatuses, $closedStatuses))
            ->latest()
            ->paginate(5, ['*'], 'riwayat_page');

        // TOTAL SEMUA
        $totalAll = $user->bookings()->count();

        // STATISTIK
        $totalAktif = $user->bookings()
            ->whereIn('status', $aktifStatuses)
            ->count();

        $totalDone = $user->bookings()
            ->whereIn('status', $doneStatuses)
            ->count();

        $totalClosed = $user->bookings()
            ->whereIn('status', $closedStatuses)
            ->count();

        // ─── DATA KHUSUS ADMIN GA ────────────────────────────────────────────
        $urgentBookings   = null;
        $activeTripsTotal = 0;
        $activeInternal   = 0;
        $activeExternal   = 0;
        $returningToday   = 0;
        $lateCount        = 0;

        if ($user->hasRole('admin')) {

            // Pengajuan Urgent: masih Pending & jadwal berangkat ≤ 1 jam ke depan
            $urgentBookings = Booking::with(['user', 'approver'])
                ->where('status', BookingStatus::Pending)
                ->where('start_time', '>=', now())
                ->where('start_time', '<=', now()->addHour())
                ->orderBy('start_time')
                ->paginate(5, ['*'], 'urgent_page');

            $activeStatuses = [BookingStatus::Prepared, BookingStatus::Active];

            // Unit Berjalan (Disiapkan + Sedang Jalan)
            $activeTripsTotal = Booking::whereIn('status', $activeStatuses)->count();
            $activeInternal   = Booking::whereIn('status', $activeStatuses)
                ->where('fulfillment_source', 'internal')->count();
            $activeExternal   = Booking::whereIn('status', $activeStatuses)
                ->where('fulfillment_source', 'external')->count();

            // Kembali Hari Ini
            $returningToday = Booking::whereIn('status', $activeStatuses)
                ->whereDate('end_time', today())
                ->count();

            // Terlambat: end_time sudah terlewat, status masih berjalan
            $lateCount = Booking::whereIn('status', $activeStatuses)
                ->where('end_time', '<', now())
                ->count();
        }

        return view('dashboard', compact(
            'bookingsAktif',
            'bookingsRiwayat',
            'totalAll',
            'totalAktif',
            'totalDone',
            'totalClosed',
            'urgentBookings',
            'activeTripsTotal',
            'activeInternal',
            'activeExternal',
            'returningToday',
            'lateCount'
        ));
    }
}