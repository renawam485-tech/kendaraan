<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ── Stat card milik semua role ────────────────────────────────────────
        // Menggunakan query langsung (bukan clone builder dari relasi)
        // agar lebih eksplisit dan bebas dari kemungkinan state yang bocor.
        $totalAll = Booking::where('user_id', $user->id)->count();

        $totalAktif = Booking::where('user_id', $user->id)
            ->where('status', BookingStatus::Active)
            ->count();

        $totalDone = Booking::where('user_id', $user->id)
            ->where('status', BookingStatus::Completed)
            ->count();

        $totalClosed = Booking::where('user_id', $user->id)
            ->whereIn('status', [BookingStatus::Cancelled, BookingStatus::Rejected])
            ->count();

        // Tabel "Pengajuan Aktif" (Pending + Approved + Prepared + Active)
        // ditampilkan untuk semua role, hanya booking milik user ini.
        $bookingsAktif = Booking::where('user_id', $user->id)
            ->whereIn('status', [
                BookingStatus::Pending,
                BookingStatus::Approved,
                BookingStatus::Prepared,
                BookingStatus::Active,
            ])
            ->latest('start_time')
            ->paginate(10);

        // ── Variabel khusus admin — default kosong untuk non-admin ────────────
        // LengthAwarePaginator(items, total, perPage)
        // Harus diisi collect() bukan [] agar method isEmpty() bisa dipanggil
        // via __call magic method yang didelegasi ke Collection.
        $urgentBookings   = new LengthAwarePaginator(collect(), 0, 5);
        $activeTripsTotal = 0;
        $activeInternal   = 0;
        $activeExternal   = 0;
        $returningToday   = 0;
        $lateCount        = 0;

        // Menggunakan $user->role === 'admin' karena project ini memakai
        // kolom role biasa, bukan Spatie. Method hasRole() juga sudah
        // ditambahkan di User model sebagai alias dari pengecekan ini.
        if ($user->role === 'admin') {

            // Booking urgent (is_urgent=true) yang masih Pending
            $urgentBookings = Booking::urgentPending()
                ->with(['user', 'approver'])
                ->orderBy('start_time')
                ->paginate(5, ['*'], 'urgent_page');

            // Unit yang sedang disiapkan atau berjalan
            $activeTripsTotal = Booking::whereIn('status', [
                BookingStatus::Prepared,
                BookingStatus::Active,
            ])->count();

            $activeInternal = Booking::whereIn('status', [
                BookingStatus::Prepared,
                BookingStatus::Active,
            ])->where('fulfillment_source', 'internal')->count();

            $activeExternal = Booking::whereIn('status', [
                BookingStatus::Prepared,
                BookingStatus::Active,
            ])->where('fulfillment_source', 'external')->count();

            // Unit yang end_time-nya hari ini
            $returningToday = Booking::whereIn('status', [
                BookingStatus::Prepared,
                BookingStatus::Active,
            ])->whereDate('end_time', today())->count();

            // Unit Active yang sudah melewati end_time (terlambat)
            $lateCount = Booking::where('status', BookingStatus::Active)
                ->where('end_time', '<', now())
                ->count();
        }

        return view('dashboard', compact(
            'urgentBookings',
            'activeTripsTotal',
            'activeInternal',
            'activeExternal',
            'returningToday',
            'lateCount',
            'totalAll',
            'totalAktif',
            'totalDone',
            'totalClosed',
            'bookingsAktif',
        ));
    }
}