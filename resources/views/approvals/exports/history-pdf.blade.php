<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Riwayat Persetujuan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #777;
        }

        .info {
            margin-bottom: 15px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #2563eb;
            color: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            font-size: 11px;
        }

        td {
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .status-approved {
            color: green;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .status-cancelled {
            color: gray;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
            color: #777;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h2>LAPORAN RIWAYAT PERSETUJUAN</h2>
        <p>Drivora System</p>
        <p>{{ now()->format('d M Y H:i') }}</p>
    </div>

    <!-- INFO FILTER -->
    <div class="info">
        <strong>Filter:</strong><br>
        Dari: {{ request('export_date_from') ?? '-' }} |
        Sampai: {{ request('export_date_to') ?? '-' }} |
        Keputusan: {{ request('export_decision') ?? 'Semua' }}
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Tujuan</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $i => $h)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $h->booking_code }}</td>
                <td>{{ $h->created_at->format('d M Y') }}</td>
                <td>{{ $h->user->name }}</td>
                <td>{{ $h->destination }}</td>
                @php
                    $status = $h->status->value;
                    $wasApproved = $h->approvalLogs->where('action', 'approve')->count() > 0;
                @endphp
                <td>
                    @if ($status == 'rejected')
                        <span class="status-rejected">Ditolak</span>

                    @elseif($status == 'cancelled')
                        <span class="status-cancelled">Dibatalkan</span>

                        @if ($wasApproved)
                            <div style="font-size:10px; color:green;">
                                ✓ (Sempat Disetujui)
                            </div>
                        @endif

                    @else
                        <span class="status-approved">Disetujui</span>
                    @endif
                </td>
                <td>
                    {{ optional($h->approvalLogs->last())->comment ?? '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        Dicetak otomatis oleh sistem
    </div>

</body>
</html>