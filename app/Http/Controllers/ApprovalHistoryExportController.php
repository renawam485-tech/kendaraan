<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ApprovalHistoryExportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'export_date_from' => ['required', 'date'],
            'export_date_to'   => ['required', 'date', 'after_or_equal:export_date_from'],
            'export_decision'  => ['nullable', 'in:approved,rejected,cancelled'],
        ]);

        $dateFrom = $request->input('export_date_from');
        $dateTo   = $request->input('export_date_to');
        $decision = $request->input('export_decision');

        // ── 1. Ambil data ────────────────────────────────────────────────────
        $approverId = Auth::id();

        $query = Booking::with(['user', 'approvalLogs'])
            ->whereHas('approvalLogs', fn($q) => $q->where('approver_id', $approverId))
            ->whereBetween('created_at', [
                $dateFrom . ' 00:00:00',
                $dateTo   . ' 23:59:59',
            ])
            ->latest('created_at');

        if ($decision === 'approved') {
            $query->whereNotIn('status', ['rejected', 'cancelled']);
        } elseif ($decision === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($decision === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $bookings = $query->get();

        // ── 2. Setup spreadsheet ─────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Persetujuan');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // Kolom terakhir = N (14 kolom)
        $colLast  = 'N';
        $colRange = "A:{$colLast}";

        // ── 3. HEADER LAPORAN (baris 1–3) ────────────────────────────────────

        // Baris 1: Judul utama
        $sheet->mergeCells("A1:{$colLast}1");
        $sheet->setCellValue('A1', 'LAPORAN RIWAYAT PERSETUJUAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A5F']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        // Baris 2: Periode
        $labelPeriode = 'Periode  :  '
            . Carbon::parse($dateFrom)->locale('id')->translatedFormat('d F Y')
            . '  s/d  '
            . Carbon::parse($dateTo)->locale('id')->translatedFormat('d F Y');

        $sheet->mergeCells("A2:{$colLast}2");
        $sheet->setCellValue('A2', $labelPeriode);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'bold' => true, 'color' => ['rgb' => '1E3A5F']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'left'  => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A5F']],
                'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A5F']],
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Baris 3: Diekspor pada
        $labelFilter = 'Diekspor pada  :  ' . now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB'
            . '        Filter Keputusan  :  ' . ($decision ? ucfirst($decision) : 'Semua');

        $sheet->mergeCells("A3:{$colLast}3");
        $sheet->setCellValue('A3', $labelFilter);
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '374151']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'left'   => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A5F']],
                'right'  => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A5F']],
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A5F']],
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // Baris 4: kosong sebagai spacer
        $sheet->getRowDimension(4)->setRowHeight(6);

        // ── 4. HEADER TABEL (baris 5) ────────────────────────────────────────
        $HEADER_ROW = 5;

        $headers = [
            'A' => 'No',
            'B' => 'Kode Booking',
            'C' => 'Tgl Request',
            'D' => 'Nama Pemohon',
            'E' => 'Departemen',
            'F' => 'Email',
            'G' => 'Tujuan',
            'H' => 'Keperluan',
            'I' => 'Waktu Mulai',
            'J' => 'Waktu Selesai',
            'K' => 'Keputusan',
            'L' => 'Status Booking',
            'M' => 'Catatan / Alasan',
            'N' => 'Tgl Diproses',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . $HEADER_ROW, $label);
        }

        $sheet->getStyle("A{$HEADER_ROW}:{$colLast}{$HEADER_ROW}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => [
                'outline'     => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1D4ED8']],
                'allBorders'  => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => '3B82F6']],
            ],
        ]);
        $sheet->getRowDimension($HEADER_ROW)->setRowHeight(28);

        // ── 5. DATA BARIS ────────────────────────────────────────────────────
        $dataStartRow = $HEADER_ROW + 1;
        $row          = $dataStartRow;

        foreach ($bookings as $i => $booking) {
            $lastLog     = $booking->approvalLogs->last();
            $wasApproved = $booking->approvalLogs->where('action', 'approve')->count() > 0;
            $status      = $booking->status->value ?? $booking->status;

            // Tentukan label & warna keputusan
            if ($status === 'rejected') {
                $decisionLabel = 'Ditolak';
                $decisionBg    = 'FEE2E2';
                $decisionFont  = 'B91C1C';
            } elseif ($status === 'cancelled') {
                $decisionLabel = $wasApproved ? 'Dibatalkan (Sempat Disetujui)' : 'Dibatalkan';
                $decisionBg    = 'F3F4F6';
                $decisionFont  = '6B7280';
            } else {
                $decisionLabel = 'Disetujui';
                $decisionBg    = 'DCFCE7';
                $decisionFont  = '15803D';
            }

            $statusLabel = method_exists($booking->status, 'label')
                ? $booking->status->label()
                : $status;

            // Isi sel — urutan kolom sama dengan detail modal
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $booking->booking_code);
            $sheet->setCellValue("C{$row}", $booking->created_at->format('d/m/Y'));
            $sheet->setCellValue("D{$row}", $booking->user->name);
            $sheet->setCellValue("E{$row}", $booking->user->department ?? '-');
            $sheet->setCellValue("F{$row}", $booking->user->email);
            $sheet->setCellValue("G{$row}", $booking->destination);
            $sheet->setCellValue("H{$row}", $booking->purpose);
            $sheet->setCellValue("I{$row}", $booking->start_time->format('d/m/Y H:i'));
            $sheet->setCellValue("J{$row}", $booking->end_time->format('d/m/Y H:i'));
            $sheet->setCellValue("K{$row}", $decisionLabel);
            $sheet->setCellValue("L{$row}", $statusLabel);
            $sheet->setCellValue("M{$row}", $lastLog->comment ?? '-');
            $sheet->setCellValue("N{$row}", $lastLog ? $lastLog->created_at->format('d/m/Y H:i') : '-');

            // Zebra shading baris
            $rowBg = ($i % 2 === 0) ? 'FFFFFF' : 'F0F7FF';

            $sheet->getStyle("A{$row}:{$colLast}{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'font'      => ['size' => 9, 'color' => ['rgb' => '1F2937']],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']],
                    'left'       => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '93C5FD']],
                    'right'      => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '93C5FD']],
                ],
            ]);

            // Warna khusus kolom Keputusan
            $sheet->getStyle("K{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $decisionBg]],
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $decisionFont]],
            ]);

            // Center alignment untuk kolom tertentu
            foreach (['A', 'B', 'C', 'I', 'J', 'K', 'L', 'N'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        $dataEndRow = $row - 1;

        // ── 6. BARIS TOTAL ───────────────────────────────────────────────────
        if ($bookings->isNotEmpty()) {
            // Hitung ringkasan per keputusan
            $totalDisetujui  = $bookings->filter(fn($b) => !in_array($b->status->value ?? $b->status, ['rejected', 'cancelled']))->count();
            $totalDitolak    = $bookings->where('status.value', 'rejected')->count()
                             + $bookings->filter(fn($b) => ($b->status->value ?? $b->status) === 'rejected')->count();
            // Hitung ulang agar akurat
            $cDisetujui  = 0; $cDitolak = 0; $cDibatalkan = 0;
            foreach ($bookings as $b) {
                $s = $b->status->value ?? $b->status;
                if ($s === 'rejected')        $cDitolak++;
                elseif ($s === 'cancelled')   $cDibatalkan++;
                else                          $cDisetujui++;
            }

            // Baris spacer
            $sheet->getRowDimension($row)->setRowHeight(4);
            $row++;

            // Baris ringkasan
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", 'RINGKASAN');
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->setCellValue("D{$row}", 'Total Disetujui');
            $sheet->setCellValue("E{$row}", $cDisetujui);
            $sheet->setCellValue("F{$row}", 'Total Ditolak');
            $sheet->setCellValue("G{$row}", $cDitolak);
            $sheet->setCellValue("H{$row}", 'Total Dibatalkan');
            $sheet->setCellValue("I{$row}", $cDibatalkan);
            $sheet->setCellValue("J{$row}", 'TOTAL KESELURUHAN');
            $sheet->mergeCells("J{$row}:K{$row}");
            $sheet->setCellValue("L{$row}", $bookings->count());

            $sheet->getStyle("D{$row}:I{$row}")->applyFromArray([
                'font' => ['size' => 9, 'bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
            ]);
            // Warna nilai ringkasan
            $sheet->getStyle("E{$row}")->applyFromArray(['font' => ['color' => ['rgb' => '15803D'], 'bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']]]);
            $sheet->getStyle("G{$row}")->applyFromArray(['font' => ['color' => ['rgb' => 'B91C1C'], 'bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']]]);
            $sheet->getStyle("I{$row}")->applyFromArray(['font' => ['color' => ['rgb' => '6B7280'], 'bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']]]);
            $sheet->getStyle("J{$row}:L{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            // Center nilai
            foreach (['E', 'G', 'I', 'L'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getRowDimension($row)->setRowHeight(22);

            // Border outline tabel data
            $sheet->getStyle("A{$HEADER_ROW}:{$colLast}{$dataEndRow}")->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']],
                ],
            ]);
        }

        // ── 7. Lebar kolom (dipadatkan agar muat A4 landscape) ──────────────
        // Total ~185 unit → dengan FitToWidth=1 Excel scale otomatis ke A4
        $widths = [
            'A' =>  4,   // No
            'B' => 14,   // Kode Booking
            'C' => 11,   // Tgl Request
            'D' => 16,   // Nama Pemohon
            'E' => 13,   // Departemen
            'F' => 20,   // Email
            'G' => 16,   // Tujuan
            'H' => 18,   // Keperluan
            'I' => 13,   // Waktu Mulai
            'J' => 13,   // Waktu Selesai
            'K' => 16,   // Keputusan
            'L' => 14,   // Status Booking
            'M' => 20,   // Catatan
            'N' => 13,   // Tgl Diproses
        ];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Font ukuran 8 untuk data agar lebih kompak di A4
        $sheet->getStyle("A{$dataStartRow}:{$colLast}{$dataEndRow}")
            ->getFont()->setSize(8);

        // ── 8. Freeze & AutoFilter ───────────────────────────────────────────
        $sheet->freezePane("A{$dataStartRow}");
        if ($bookings->isNotEmpty()) {
            $sheet->setAutoFilter("A{$HEADER_ROW}:{$colLast}{$dataEndRow}");
        }

        // ── Print setup: A4 Landscape, margin sempit, fit to 1 halaman lebar ─
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $pageSetup->setFitToWidth(1);   // semua kolom muat dalam 1 halaman lebar
        $pageSetup->setFitToHeight(0);  // tinggi bebas (multi halaman boleh)
        $pageSetup->setHorizontalCentered(true);

        // Margin sempit (dalam inci): kiri/kanan 0.4", atas/bawah 0.5", header/footer 0.3"
        $margins = $sheet->getPageMargins();
        $margins->setLeft(0.4);
        $margins->setRight(0.4);
        $margins->setTop(0.5);
        $margins->setBottom(0.5);
        $margins->setHeader(0.3);
        $margins->setFooter(0.3);

        // Footer: nomor halaman
        $sheet->getHeaderFooter()
            ->setOddFooter('&C&"Arial,Regular"&8Halaman &P dari &N  |  ' . now()->format('d/m/Y H:i'));

        $sheet->setPrintGridlines(true); // garis tabel tampil saat print

        // ── 9. Stream ke browser ─────────────────────────────────────────────
        $filename = 'laporan-persetujuan_'
            . Carbon::parse($dateFrom)->format('Ymd')
            . '_sd_'
            . Carbon::parse($dateTo)->format('Ymd')
            . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}