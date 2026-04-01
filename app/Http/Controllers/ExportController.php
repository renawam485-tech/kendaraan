<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Booking;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function historyPdf(Request $request)
    {
        // Query data
        $histories = Booking::with(['user', 'approvalLogs'])
            ->when($request->export_date_from, function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->export_date_from);
            })
            ->when($request->export_date_to, function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->export_date_to);
            })
            ->when($request->export_decision, function ($query) use ($request) {
                $query->where('status', $request->export_decision);
            })
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('approvals.exports.history-pdf', [
            'histories' => $histories,
            'request'   => $request // 🔥 supaya bisa dipakai di blade (filter info)
        ]);

        // Setting PDF
        $pdf->setPaper('a4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        // Download
        return $pdf->stream('laporan-riwayat-' . now()->format('Y-m-d') . '.pdf');
    }
}