<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ApprovalLog;
use App\Enums\BookingStatus;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Tambahkan facade DB

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $approvals = Booking::where('approver_id', Auth::id())
            ->where('status', BookingStatus::Pending)
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('booking_code', 'like', "%{$request->search}%")
                        ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
                });
            })
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->orderBy('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('approvals.index', compact('approvals'));
    }

    public function decide(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'action'  => 'required|in:approve,reject',
            'comment' => 'nullable|string',
        ]);

        $approver = Auth::user();
        $message  = '';

        try {
            // Gunakan DB Transaction agar jika ada error/bentrok, data tidak terproses setengah-setengah
            DB::transaction(function () use ($validated, $booking, $approver, &$message) {
                
                // 1. Kunci baris booking ini (Pencegahan jika 2 Atasan klik Approve di detik yang sama)
                $booking = Booking::lockForUpdate()->find($booking->id);

                if ($booking->status !== BookingStatus::Pending) {
                    throw new \Exception('Pengajuan ini sudah diproses sebelumnya.');
                }

                if ($validated['action'] === 'approve') {
                    
                    // 2. Cek ulang: Apakah unit ini baru saja dimenangkan oleh orang lain?
                    if ($booking->vehicle_id) {
                        $isAlreadyTaken = Booking::where('vehicle_id', $booking->vehicle_id)
                            ->where('id', '!=', $booking->id)
                            ->whereIn('status', [BookingStatus::Approved, BookingStatus::Prepared, BookingStatus::Active])
                            ->where(function ($q) use ($booking) {
                                $q->where('start_time', '<', $booking->end_time)
                                  ->where('end_time', '>', $booking->start_time);
                            })
                            ->exists();

                        if ($isAlreadyTaken) {
                            throw new \Exception('Gagal: Kendaraan ini baru saja disetujui untuk pemohon lain di waktu yang sama.');
                        }
                    }

                    // 3. Setujui pengajuan si Pemenang
                    $booking->update(['status' => BookingStatus::Approved]);
                    
                    // Notif ke User saja (Admin GA tidak perlu karena sudah ada badge navbar)
                    NotificationService::bookingApproved($booking, $approver);
                    $message = 'Pengajuan disetujui, diteruskan ke Admin GA.';

                    // 4. SELEKSI ALAM: Cari pengajuan lain (Si Kalah) yang masih Pending untuk unit & waktu yang sama
                    if ($booking->vehicle_id) {
                        $losers = Booking::where('vehicle_id', $booking->vehicle_id)
                            ->where('id', '!=', $booking->id)
                            ->where('status', BookingStatus::Pending)
                            ->where(function ($q) use ($booking) {
                                $q->where('start_time', '<', $booking->end_time)
                                  ->where('end_time', '>', $booking->start_time);
                            })
                            ->get();

                        // 5. Auto-Reject (Tolak Otomatis) si Kalah
                        foreach ($losers as $loser) {
                            $loser->update(['status' => BookingStatus::Rejected]);

                            ApprovalLog::create([
                                'booking_id' => $loser->id,
                                'user_id'    => $approver->id,
                                'action'     => 'reject',
                                'comment'    => 'Ditolak Otomatis: Unit telah disetujui untuk pengajuan (' . $booking->booking_code . ') di waktu yang sama.',
                            ]);

                            NotificationService::bookingRejected($loser, $approver, 'Mohon maaf, unit telah keduluan disetujui untuk pemohon lain di waktu yang sama.');
                        }
                    }

                } else {
                    // Jika aksi manual dari Atasan adalah "Tolak"
                    $booking->update(['status' => BookingStatus::Rejected]);
                    NotificationService::bookingRejected($booking, $approver, $validated['comment'] ?? null);
                    $message = 'Pengajuan ditolak.';
                }

                // Catat log untuk pengajuan utama
                ApprovalLog::create([
                    'booking_id' => $booking->id,
                    'user_id'    => $approver->id,
                    'action'     => $validated['action'],
                    'comment'    => $validated['comment'] ?? null,
                ]);
            });

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        $histories = Booking::where('approver_id', Auth::id())
            ->where('status', '!=', BookingStatus::Pending)
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('booking_code', 'like', "%{$request->search}%")
                        ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
                });
            })
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->decision, function ($q) use ($request) {
                if ($request->decision === 'approved') {
                    $q->whereNotIn('status', ['rejected', 'cancelled']);
                } else {
                    $q->where('status', $request->decision);
                }
            })
            ->with('approvalLogs')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('approvals.history', compact('histories'));
    }

    public function pendingCount(): \Illuminate\Http\JsonResponse
    {
        $count = Booking::where('approver_id', Auth::id())
            ->where('status', BookingStatus::Pending)
            ->count();

        return response()->json(['count' => $count]);
    }
}