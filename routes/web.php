<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ApprovalHistoryExportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Admin\TripHistoryExportController;
use App\Http\Controllers\AdminDispatcherController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // ─── BOOKING ──────────────────────────────────────────────────────────────
    Route::get('/booking/create',             [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking',                   [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/{booking}/cancel',  [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.checkAvailability');
    Route::get('/booking/history',            [BookingController::class, 'history'])->name('booking.history');

    // ─── APPROVER ─────────────────────────────────────────────────────────────
    Route::middleware(['role:approver'])->group(function () {
        Route::get('/approvals',                [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/history',        [ApprovalController::class, 'history'])->name('approvals.history');
        Route::post('/approvals/{booking}',     [ApprovalController::class, 'decide'])->name('approvals.decide');
        Route::get('/approvals/history/export', [ApprovalHistoryExportController::class, 'export'])->name('approvals.history.export');
        Route::get('/export/history/pdf', [ExportController::class, 'historyPdf'])->name('export.history.pdf');

        /*
         * Badge navbar — jumlah booking berstatus 'pending' yang menunggu keputusan.
         * Dipanggil oleh pollTaskCounts() di navigation.blade.php tiap 30 detik.
         * Response: { "count": <int> }
         */
        Route::get('/approvals/pending-count', [ApprovalController::class, 'pendingCount'])->name('approvals.pending-count');
    });

    // ─── ADMIN GA ─────────────────────────────────────────────────────────────
    Route::middleware(['role:admin_ga'])->prefix('admin')->name('admin.')->group(function () {

        // Dispatcher / persiapan
        Route::get('/dispatch',                             [AdminDispatcherController::class, 'index'])->name('dispatch');
        Route::post('/dispatch/{booking}/internal',         [AdminDispatcherController::class, 'assignInternal'])->name('assign.internal');
        Route::post('/dispatch/{booking}/external',         [AdminDispatcherController::class, 'assignExternal'])->name('assign.external');
        Route::post('/dispatch/{booking}/start-trip',       [AdminDispatcherController::class, 'startTrip'])->name('start.trip');
        Route::post('/dispatch/{booking}/confirm-cancel',   [AdminDispatcherController::class, 'confirmCancelVendor'])->name('cancel.vendor');

        /*
         * Badge navbar — jumlah booking berstatus 'approved' yang belum di-dispatch.
         * Menggantikan route dispatch.count yang lama (getCount) agar nama konsisten
         * dengan yang dipanggil di navigation.blade.php.
         * Response: { "count": <int> }
         */
        Route::get('/pending-count', [AdminDispatcherController::class, 'getCount'])->name('pending-count');

        // Active trips
        Route::get('/active-trips',              [AdminDispatcherController::class, 'activeTrips'])->name('active');
        Route::post('/complete-trip/{booking}',  [AdminDispatcherController::class, 'completeTrip'])->name('complete.trip');
        Route::get('/active-count', [AdminDispatcherController::class, 'activeCount'])
            ->name('active-count');

        // History
        Route::get('/trip-history',              [AdminDispatcherController::class, 'tripHistory'])->name('trip.history');
        Route::get('/trip-history/export',       [TripHistoryExportController::class, 'export'])->name('trip.history.export');

        // Resources
        Route::resource('vehicles', VehicleController::class);
        Route::resource('users',    UserManagementController::class);
    });

    // ─── PROFILE ──────────────────────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── NOTIFIKASI ───────────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',               [NotificationController::class, 'index'])->name('index');
        Route::patch('mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::patch('{id}/read',     [NotificationController::class, 'markRead'])->name('mark-read');
        Route::delete('clear-read',   [NotificationController::class, 'clearRead'])->name('clear-read');
        Route::delete('{id}',         [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('unread-count',    [NotificationController::class, 'unreadCount'])->name('unread-count');
    });

    Route::get('/bantuan', [HelpController::class, 'index'])->name('help.index');
});

require __DIR__ . '/auth.php';
