<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Http\Controllers\Controller;

class TripHistoryExportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'export_date_from' => ['required', 'date'],
            'export_date_to'   => ['required', 'date', 'after_or_equal:export_date_from'],
            'export_source'    => ['nullable', 'in:internal,external'],
            'export_status'    => ['nullable', 'in:completed,cancelled'],
        ]);

        $dateFrom = $request->input('export_date_from');
        $dateTo   = $request->input('export_date_to');
        $source   = $request->input('export_source');
        $status   = $request->input('export_status');

        // ── 1. Ambil data ────────────────────────────────────────────────────
        $query = Booking::with(['user', 'vehicle'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->whereBetween('start_time', [
                $dateFrom . ' 00:00:00',
                $dateTo   . ' 23:59:59',
            ])
            ->latest('start_time');

        if ($source) {
            $query->where('fulfillment_source', $source);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $archives = $query->get();

        // ── 2. Setup spreadsheet ─────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Perjalanan');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $colLast = 'L'; // 12 kolom

        // ── 3. Header laporan (baris 1–3) ────────────────────────────────────
        $sheet->mergeCells("A1:{$colLast}1");
        $sheet->setCellValue('A1', 'LAPORAN RIWAYAT PERJALANAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A5F']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

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

        $sourceLabel = match($source) {
            'internal' => 'Mobil Kampus',
            'external' => 'Sewa Luar',
            default    => 'Semua',
        };
        $statusLabel = match($status) {
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default     => 'Semua',
        };

        $labelInfo = 'Diekspor pada  :  ' . now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB'
            . '        Unit  :  ' . $sourceLabel
            . '        Status  :  ' . $statusLabel;

        $sheet->mergeCells("A3:{$colLast}3");
        $sheet->setCellValue('A3', $labelInfo);
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

        // Baris 4: spacer
        $sheet->getRowDimension(4)->setRowHeight(6);

        // ── 4. Header tabel (baris 5) ────────────────────────────────────────
        $HEADER_ROW = 5;

        $headers = [
            'A' => 'No',
            'B' => 'Kode Booking',
            'C' => 'Tgl Berangkat',
            'D' => 'Nama Peminjam',
            'E' => 'Departemen',
            'F' => 'Email',
            'G' => 'Tujuan',
            'H' => 'Keperluan',
            'I' => 'Waktu Selesai',
            'J' => 'Unit / Vendor',
            'K' => 'Detail Kendaraan',
            'L' => 'Status Akhir',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . $HEADER_ROW, $label);
        }

        $sheet->getStyle("A{$HEADER_ROW}:{$colLast}{$HEADER_ROW}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => [
                'outline'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1D4ED8']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => '3B82F6']],
            ],
        ]);
        $sheet->getRowDimension($HEADER_ROW)->setRowHeight(28);

        // ── 5. Data baris ────────────────────────────────────────────────────
        $dataStartRow = $HEADER_ROW + 1;
        $row          = $dataStartRow;

        foreach ($archives as $i => $log) {
            $statusVal = $log->status->value ?? $log->status;

            if ($statusVal === 'completed') {
                $statusLabel2 = 'Selesai';
                $statusBg     = 'DBEAFE';
                $statusFont   = '1D4ED8';
            } else {
                $statusLabel2 = 'Dibatalkan';
                $statusBg     = 'FEE2E2';
                $statusFont   = 'B91C1C';
            }

            // Unit / Vendor
            if ($log->fulfillment_source === 'internal') {
                $unitLabel  = $log->vehicle->name ?? '-';
                $unitDetail = $log->vehicle->license_plate ?? '-';
            } else {
                $unitLabel  = $log->vendor_name ?? 'Vendor';
                $unitDetail = $log->external_vehicle_detail ?? '-';
            }

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $log->booking_code);
            $sheet->setCellValue("C{$row}", $log->start_time->format('d/m/Y'));
            $sheet->setCellValue("D{$row}", $log->user->name);
            $sheet->setCellValue("E{$row}", $log->user->department ?? '-');
            $sheet->setCellValue("F{$row}", $log->user->email);
            $sheet->setCellValue("G{$row}", $log->destination);
            $sheet->setCellValue("H{$row}", $log->purpose ?? '-');
            $sheet->setCellValue("I{$row}", $log->end_time ? $log->end_time->format('d/m/Y H:i') : '-');
            $sheet->setCellValue("J{$row}", $unitLabel);
            $sheet->setCellValue("K{$row}", $unitDetail);
            $sheet->setCellValue("L{$row}", $statusLabel2);

            // Zebra shading
            $rowBg = ($i % 2 === 0) ? 'FFFFFF' : 'F0F7FF';

            $sheet->getStyle("A{$row}:{$colLast}{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'font'      => ['size' => 8, 'color' => ['rgb' => '1F2937']],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => 'BFDBFE']],
                    'left'       => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '93C5FD']],
                    'right'      => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '93C5FD']],
                ],
            ]);

            // Warna kolom Status
            $sheet->getStyle("L{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusBg]],
                'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => $statusFont]],
            ]);

            // Warna label unit (internal=biru, external=oranye)
            $unitColor = ($log->fulfillment_source === 'internal') ? '1D4ED8' : 'C2410C';
            $sheet->getStyle("J{$row}")->getFont()->setColor(
                new \PhpOffice\PhpSpreadsheet\Style\Color($unitColor)
            );
            $sheet->getStyle("J{$row}")->getFont()->setBold(true);

            // Center alignment
            foreach (['A', 'B', 'C', 'I', 'K', 'L'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $dataEndRow = $row - 1;

        // ── 6. Baris ringkasan ───────────────────────────────────────────────
        if ($archives->isNotEmpty()) {
            $cSelesai    = $archives->filter(fn($b) => ($b->status->value ?? $b->status) === 'completed')->count();
            $cDibatalkan = $archives->filter(fn($b) => ($b->status->value ?? $b->status) === 'cancelled')->count();
            $cInternal   = $archives->where('fulfillment_source', 'internal')->count();
            $cExternal   = $archives->where('fulfillment_source', 'external')->count();

            // Spacer
            $sheet->getRowDimension($row)->setRowHeight(4);
            $row++;

            // Baris ringkasan
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("A{$row}", 'RINGKASAN');
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $summaryData = [
                'C' => 'Selesai',       'D' => $cSelesai,
                'E' => 'Dibatalkan',    'F' => $cDibatalkan,
                'G' => 'Mobil Kampus',  'H' => $cInternal,
                'I' => 'Sewa Luar',     'J' => $cExternal,
                'K' => 'TOTAL',         'L' => $archives->count(),
            ];
            foreach ($summaryData as $col => $val) {
                $sheet->setCellValue("{$col}{$row}", $val);
            }

            // Style label ringkasan
            foreach (['C', 'E', 'G', 'I', 'K'] as $col) {
                $sheet->getStyle("{$col}{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
                ]);
            }

            // Warna nilai ringkasan
            $sheet->getStyle("D{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '1D4ED8']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']]]);
            $sheet->getStyle("F{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'B91C1C']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']]]);
            $sheet->getStyle("H{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '1D4ED8']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']]]);
            $sheet->getStyle("J{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'C2410C']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF7ED']]]);
            $sheet->getStyle("K{$row}:L{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            foreach (['D', 'F', 'H', 'J', 'L'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getRowDimension($row)->setRowHeight(22);

            // Border outline tabel data
            $sheet->getStyle("A{$HEADER_ROW}:{$colLast}{$dataEndRow}")->applyFromArray([
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']]],
            ]);
        }

        // ── 7. Lebar kolom (dipadatkan untuk A4 landscape) ──────────────────
        $widths = [
            'A' =>  4,   // No
            'B' => 16,   // Kode Booking
            'C' => 13,   // Tgl Berangkat
            'D' => 22,   // Nama Peminjam
            'E' => 16,   // Departemen
            'F' => 26,   // Email
            'G' => 22,   // Tujuan
            'H' => 24,   // Keperluan
            'I' => 16,   // Waktu Selesai
            'J' => 20,   // Unit / Vendor
            'K' => 18,   // Detail Kendaraan
            'L' => 14,   // Status Akhir
        ];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ── 8. Freeze, AutoFilter, Print setup ──────────────────────────────
        $sheet->freezePane("A{$dataStartRow}");
        if ($archives->isNotEmpty()) {
            $sheet->setAutoFilter("A{$HEADER_ROW}:{$colLast}{$dataEndRow}");
        }

        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(0);
        $pageSetup->setHorizontalCentered(true);

        $margins = $sheet->getPageMargins();
        $margins->setLeft(0.4);
        $margins->setRight(0.4);
        $margins->setTop(0.5);
        $margins->setBottom(0.5);
        $margins->setHeader(0.3);
        $margins->setFooter(0.3);

        $sheet->getHeaderFooter()
            ->setOddFooter('&C&"Arial,Regular"&8Halaman &P dari &N  |  ' . now()->format('d/m/Y H:i'));

        $sheet->setPrintGridlines(true);

        // ── 9. Stream ke browser ─────────────────────────────────────────────
        $filename = 'laporan-perjalanan_'
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
