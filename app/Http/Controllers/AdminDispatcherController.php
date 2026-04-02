<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\User;
use App\Enums\BookingStatus;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminDispatcherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Booking::where('status', BookingStatus::Approved)->with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', '%' . $search . '%')
                    ->orWhere('destination', 'like', '%' . $search . '%')
                    ->orWhere('purpose', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $tasks    = $query->get();
        $vehicles = Vehicle::where('asset_status', 'available')->get();
        $drivers  = User::where('role', 'driver')->get();

        return view('admin.dispatcher.index', compact('tasks', 'vehicles', 'drivers', 'search'));
    }

    // JALUR A: Assign Mobil Dinas
    public function assignInternal(Request $request, Booking $booking)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id'  => 'nullable|exists:users,id',
        ]);

        $conflict = Booking::where('vehicle_id', $request->vehicle_id)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', [BookingStatus::Approved, BookingStatus::Prepared, BookingStatus::Active])
            ->where(function ($q) use ($booking) {
                $q->where(function ($q) use ($booking) {
                    $q->whereBetween('start_time', [$booking->start_time, $booking->end_time])
                        ->orWhereBetween('end_time', [$booking->start_time, $booking->end_time])
                        ->orWhere(function ($q2) use ($booking) {
                            $q2->where('start_time', '<', $booking->start_time)
                                ->where('end_time', '>', $booking->end_time);
                        });
                });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['vehicle_id' => 'Mobil ini sudah dipesan pada jam tersebut!']);
        }

        $booking->update([
            'status'             => BookingStatus::Prepared,
            'fulfillment_source' => 'internal',
            'vehicle_id'         => $request->vehicle_id,
            'driver_id'          => $request->driver_id,
            'prepared_at'        => now(),
        ]);

        // ── Notifikasi: staff tahu kendaraan & pengemudi sudah disiapkan ──
        $vehicle    = Vehicle::find($request->vehicle_id);
        $driverName = $request->driver_id
            ? User::find($request->driver_id)?->name
            : null;

        NotificationService::unitAssigned($booking, $vehicle->name, $driverName);

        return back()->with('success', 'Unit berhasil disiapkan.');
    }

    // JALUR B: Assign Vendor Luar
    public function assignExternal(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'vendor_name'             => 'required|string',
            'external_vehicle_detail' => 'required|string',
            'driver_id'               => 'nullable|exists:users,id',
        ]);

        $booking->update([
            'status'                  => BookingStatus::Prepared,
            'fulfillment_source'      => 'external',
            'vendor_name'             => $validated['vendor_name'],
            'external_vehicle_detail' => $validated['external_vehicle_detail'],
            'driver_id'               => $request->driver_id,
            'prepared_at'             => now(),
        ]);

        // ── Notifikasi: staff tahu vendor sudah dikonfirmasi ──
        NotificationService::vendorAssigned($booking, $validated['vendor_name']);

        return back()->with('success', 'Unit berhasil disiapkan.');
    }

    // Aktifkan Perjalanan
    public function startTrip(Booking $booking)
    {
        if ($booking->status !== BookingStatus::Prepared) {
            return back()->withErrors(['status' => 'Perjalanan tidak dapat dimulai.']);
        }

        $booking->update([
            'status'     => BookingStatus::Active,
            'started_at' => now(),
        ]);

        // ── Notifikasi: staff tahu perjalanan resmi dimulai ──
        NotificationService::tripStarted($booking);

        return back()->with('success', 'Perjalanan telah diaktifkan dan status berubah menjadi "Sedang Jalan".');
    }

    // Finalisasi Batal Vendor
    public function confirmCancelVendor(Request $request, Booking $booking)
    {
        if ($booking->status !== BookingStatus::CancelReq) {
            abort(404);
        }

        $request->validate([
            'cancellation_fee' => 'numeric|min:0',
        ]);

        $booking->update([
            'status'           => BookingStatus::Cancelled,
            'cancellation_fee' => $request->cancellation_fee,
            'cancelled_at'     => now(),
        ]);

        // ── Notifikasi: staff tahu pembatalan vendor selesai diproses ──
        NotificationService::bookingCancelled($booking);

        return back()->with('success', 'Pembatalan vendor diproses. Denda tercatat.');
    }

    // Monitor Aktif
    public function activeTrips(Request $request)
    {
        $query = Booking::whereIn('status', [
            BookingStatus::Prepared,
            BookingStatus::Active,
        ]);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('fulfillment_source', $request->source);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $activeTrips = $query
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, start_time, NOW())) ASC')
            ->get();

        return view('admin.dispatcher.active', compact('activeTrips'));
    }

    // Selesaikan Perjalanan
    public function completeTrip(Request $request, Booking $booking)
    {
        $request->validate([
            'trip_notes' => 'nullable|string',
        ]);

        $booking->update([
            'status'       => BookingStatus::Completed,
            'trip_notes'   => $request->trip_notes,
            'completed_at' => now(),
        ]);

        // ── Notifikasi: staff tahu perjalanan selesai ──
        NotificationService::tripCompleted($booking);

        return back()->with('success', 'Perjalanan selesai. Unit telah kembali.');
    }

    public function activeCount(): \Illuminate\Http\JsonResponse
    {
        $count = \App\Models\Booking::whereIn('status', [
            \App\Enums\BookingStatus::Prepared,
            \App\Enums\BookingStatus::Active,
        ])->count();

        return response()->json(['count' => $count]);
    }

    // Riwayat
    public function tripHistory(Request $request)
    {
        $query = Booking::with(['user', 'vehicle', 'driver'])
            ->whereIn('status', [BookingStatus::Completed, BookingStatus::Cancelled]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('source')) {
            $query->where('fulfillment_source', $request->source);
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->where('status', BookingStatus::Completed);
            }
            if ($request->status === 'cancelled') {
                $query->where('status', BookingStatus::Cancelled);
            }
        }

        $archives = $query->latest('end_time')->paginate(15)->withQueryString();

        return view('admin.dispatcher.history', compact('archives'));
    }

    public function getCount()
    {
        $count = Booking::where('status', BookingStatus::Approved)->count();
        return response()->json(['count' => $count]);
    }
}