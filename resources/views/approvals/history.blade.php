<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Laporan Riwayat Persetujuan
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- FILTER BAR --}}
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <form action="{{ route('approvals.history') }}" method="GET"
                        class="flex items-center gap-3 flex-wrap">

                        <div class="flex-1 max-w-md">
                            <label for="filterSearch" class="sr-only">Cari kode booking atau pemohon</label>
                            <input type="text" id="filterSearch" name="search" value="{{ request('search') }}"
                                placeholder="Cari kode booking atau pemohon..."
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <label for="filterDate" class="sr-only">Filter tanggal</label>
                        <input type="date" id="filterDate" name="date" value="{{ request('date') }}"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">

                        <label for="filterDecision" class="sr-only">Filter keputusan</label>
                        <select id="filterDecision" name="decision"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 pr-8 focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Keputusan</option>
                            <option value="approved" {{ request('decision') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('decision') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="cancelled" {{ request('decision') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>

                        <div class="flex gap-2 border-l border-gray-300 pl-3">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                                Cari
                            </button>
                            <a href="{{ route('approvals.history') }}"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                Reset
                            </a>
                        </div>

                        {{-- Tombol Export --}}
                        <button type="button" id="btnOpenExport"
                            class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            Export Laporan
                        </button>
                    </form>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Kode</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Tanggal Request</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Pemohon</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Tujuan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Keputusan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Catatan Log</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($histories as $index => $history)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-600 font-medium text-center">
                                        {{ $histories->firstItem() + $index }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <span class="text-xs font-mono font-semibold text-blue-600">
                                            {{ $history->booking_code }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-700">{{ $history->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $history->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $history->user->department }}</div>
                                        <div class="text-xs text-gray-400">{{ $history->user->email }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $history->destination }}</div>
                                        <div class="text-xs text-gray-500">Mulai: {{ $history->start_time->format('d M Y H:i') }}</div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        @php
                                            $status = $history->status->value;
                                            $wasApproved = $history->approvalLogs->where('action', 'approve')->count() > 0;
                                        @endphp
                                        @if ($status == 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-400 text-white">
                                                Ditolak Anda
                                            </span>
                                        @elseif($status == 'cancelled')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-400 text-white">
                                                Dibatalkan User
                                            </span>
                                            <div class="mt-1 text-[10px]">
                                                @if ($wasApproved)
                                                    <span class="text-green-600 font-medium">✓ (Sempat Disetujui)</span>
                                                @else
                                                    <span class="text-gray-400 italic">(Batal sebelum proses)</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-400 text-white">
                                                Disetujui
                                            </span>
                                            <div class="text-[10px] text-gray-500 mt-1 italic">
                                                {{ $history->status->label() }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-500 italic max-w-xs truncate">
                                        {{ $history->approvalLogs->last()->comment ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <button type="button"
                                            class="btn-history-detail bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md text-[10px] uppercase font-bold tracking-wider transition"
                                            data-code="{{ $history->booking_code }}"
                                            data-name="{{ $history->user->name }}"
                                            data-department="{{ $history->user->department ?? '-' }}"
                                            data-email="{{ $history->user->email }}"
                                            data-destination="{{ $history->destination }}"
                                            data-purpose="{{ $history->purpose }}"
                                            data-date="{{ $history->created_at->format('d M Y') }}"
                                            data-start="{{ $history->start_time->format('d M Y H:i') }}"
                                            data-end="{{ $history->end_time->format('d M Y H:i') }}"
                                            data-comment="{{ $history->approvalLogs->last()->comment ?? '-' }}">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Belum ada riwayat persetujuan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($histories->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $histories->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         MODAL EXPORT EXCEL
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div id="exportModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div id="exportModalContent" class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden">

            <div class="bg-emerald-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-white font-bold text-base">Export Laporan ke Excel dan PDF</h3>
                </div>
                <button id="closeExportModal" class="text-white/70 hover:text-white text-2xl leading-none font-bold">&times;</button>
            </div>

            <form action="{{ route('approvals.history.export') }}" method="GET" id="exportForm">
                <div class="px-6 py-5 space-y-4">

                    <p class="text-sm text-gray-500">
                        Tentukan rentang tanggal dan filter untuk menentukan data yang akan diekspor ke file <strong>.xlsx</strong> dan <strong>.pdf</strong>.
                    </p>

                    {{-- Shortcut preset --}}
                    <div>
                        <span class="block text-xs font-semibold text-gray-600 mb-1.5">Pilih Cepat Periode</span>
                        <div class="flex flex-wrap gap-2" id="presetButtons">
                            <button type="button" data-preset="this_month"
                                class="btn-preset text-xs bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-600 px-3 py-1.5 rounded-full font-medium transition">
                                Bulan Ini
                            </button>
                            <button type="button" data-preset="last_month"
                                class="btn-preset text-xs bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-600 px-3 py-1.5 rounded-full font-medium transition">
                                Bulan Lalu
                            </button>
                            <button type="button" data-preset="last_3_months"
                                class="btn-preset text-xs bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-600 px-3 py-1.5 rounded-full font-medium transition">
                                3 Bulan Terakhir
                            </button>
                            <button type="button" data-preset="this_year"
                                class="btn-preset text-xs bg-gray-100 hover:bg-emerald-100 hover:text-emerald-700 text-gray-600 px-3 py-1.5 rounded-full font-medium transition">
                                Tahun Ini
                            </button>
                        </div>
                        {{-- Label preview periode yang dipilih --}}
                        <p id="presetPreview" class="mt-2 text-xs text-emerald-700 font-medium hidden"></p>
                    </div>

                    {{-- Date range --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="exportDateFrom" class="block text-xs font-semibold text-gray-600 mb-1">
                                Dari Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="export_date_from" id="exportDateFrom" required
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label for="exportDateTo" class="block text-xs font-semibold text-gray-600 mb-1">
                                Sampai Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="export_date_to" id="exportDateTo" required
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                    <p id="exportDateError" class="text-xs text-red-500 hidden">
                        ⚠ Tanggal akhir tidak boleh sebelum tanggal awal.
                    </p>

                    {{-- Filter keputusan --}}
                    <div>
                        <label for="exportDecision" class="block text-xs font-semibold text-gray-600 mb-1">Filter Keputusan</label>
                        <select id="exportDecision" name="export_decision"
                            class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-emerald-500">
                            <option value="">Semua Keputusan</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>

                    {{-- Info kolom --}}
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-emerald-700">
                            File Excel memuat: No, Kode Booking, Tgl Request, Nama, Departemen, Email,
                            Tujuan, Keperluan, Waktu Mulai, Waktu Selesai, Keputusan, Status, Catatan, Tgl Diproses.
                        </p>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-red-700">
                            File PDF memuat: No, Kode Booking, Tgl Request, Nama,
                            Tujuan, Status, Catatan.
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    <button type="button" id="cancelExportModal"
                        class="bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>

                        <!-- PDF -->
                        <button type="button" id="btnExportPdf"
                            class="bg-red-500 text-white px-4 py-2 rounded-lg">
                            Export PDF
                        </button>
                        <!-- Excel -->
                        <button type="submit" id="btnDoExport"
                            class="bg-emerald-600 text-white px-4 py-2 rounded-lg">
                            Export Excel
                        </button>

                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         MODAL DETAIL RIWAYAT
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div id="historyDetailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div id="historyDetailModalContent" class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
            <button id="closeHistoryModal"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold">
                &times;
            </button>
            <h3 class="text-lg font-bold mb-4">Detail Riwayat Persetujuan</h3>
            <div class="space-y-2 text-sm text-gray-700">
                <p><span class="font-semibold">Kode Booking:</span>
                    <span id="hd-code" class="font-mono text-blue-600 font-bold"></span></p>
                <p><span class="font-semibold">Pemohon:</span> <span id="hd-name"></span></p>
                <p><span class="font-semibold">Departemen:</span> <span id="hd-department"></span></p>
                <p><span class="font-semibold">Email:</span> <span id="hd-email"></span></p>
                <p><span class="font-semibold">Tujuan:</span> <span id="hd-destination"></span></p>
                <p><span class="font-semibold">Keperluan:</span> <span id="hd-purpose"></span></p>
                <p><span class="font-semibold">Tanggal Request:</span> <span id="hd-date"></span></p>
                <p><span class="font-semibold">Waktu Mulai:</span> <span id="hd-start"></span></p>
                <p><span class="font-semibold">Waktu Selesai:</span> <span id="hd-end"></span></p>
                <div class="border-t pt-2 mt-2">
                    <p><span class="font-semibold">Catatan:</span>
                        <span id="hd-comment" class="italic text-gray-500"></span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- iframe tersembunyi untuk trigger download tanpa navigasi halaman --}}
    <iframe id="exportIframe" class="hidden" aria-hidden="true"></iframe>

    <script>
        // ════════════════════════════════════════════════════════════════════════
        // PENTING: Gunakan format lokal, BUKAN toISOString()
        // toISOString() konversi ke UTC sehingga di WIB (UTC+7) tanggal bisa
        // mundur 1 hari (misal: 31 Maret jadi 30 Maret di input date)
        // ════════════════════════════════════════════════════════════════════════
        function toYMD(d) {
            const y   = d.getFullYear();
            const m   = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        // Format tampilan label preview (misal: "01 Mar 2026 – 31 Mar 2026")
        function formatLabel(d) {
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        const today = new Date();

        const presets = {
            this_month: {
                label: 'Bulan Ini',
                range: () => {
                    const f = new Date(today.getFullYear(), today.getMonth(), 1);
                    const t = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    return [f, t];
                }
            },
            last_month: {
                label: 'Bulan Lalu',
                range: () => {
                    const f = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const t = new Date(today.getFullYear(), today.getMonth(), 0);
                    return [f, t];
                }
            },
            last_3_months: {
                label: '3 Bulan Terakhir',
                range: () => {
                    const f = new Date(today.getFullYear(), today.getMonth() - 2, 1);
                    const t = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    return [f, t];
                }
            },
            this_year: {
                label: 'Tahun Ini',
                range: () => {
                    const f = new Date(today.getFullYear(), 0, 1);
                    const t = new Date(today.getFullYear(), 11, 31);
                    return [f, t];
                }
            },
        };

        // ── Export Modal ─────────────────────────────────────────────────────────
        const exportModal        = document.getElementById('exportModal');
        const exportModalContent = document.getElementById('exportModalContent');
        const exportDateFrom     = document.getElementById('exportDateFrom');
        const exportDateTo       = document.getElementById('exportDateTo');
        const exportDateError    = document.getElementById('exportDateError');
        const presetPreview      = document.getElementById('presetPreview');
        const exportIframe       = document.getElementById('exportIframe');

        function setPresetActive(key) {
            const [f, t] = presets[key].range();
            exportDateFrom.value = toYMD(f);
            exportDateTo.value   = toYMD(t);
            exportDateError.classList.add('hidden');
            presetPreview.textContent = `📅 ${formatLabel(f)} – ${formatLabel(t)}`;
            presetPreview.classList.remove('hidden');

            document.querySelectorAll('.btn-preset').forEach(b => {
                b.classList.remove('bg-emerald-200', 'text-emerald-700', 'ring-1', 'ring-emerald-400');
                b.classList.add('bg-gray-100', 'text-gray-600');
            });
            const activeBtn = document.querySelector(`.btn-preset[data-preset="${key}"]`);
            activeBtn.classList.remove('bg-gray-100', 'text-gray-600');
            activeBtn.classList.add('bg-emerald-200', 'text-emerald-700', 'ring-1', 'ring-emerald-400');
        }

        function openExportModal() {
            setPresetActive('this_month');
            exportModal.classList.remove('hidden');
            exportModal.classList.add('flex');
        }

        function closeExportModal() {
            exportModal.classList.add('hidden');
            exportModal.classList.remove('flex');
        }

        document.getElementById('btnOpenExport').addEventListener('click', openExportModal);
        document.getElementById('closeExportModal').addEventListener('click', closeExportModal);
        document.getElementById('cancelExportModal').addEventListener('click', closeExportModal);
        exportModal.addEventListener('click', e => {
            if (!exportModalContent.contains(e.target)) closeExportModal();
        });

        document.getElementById('btnExportPdf').addEventListener('click', () => {

            const form = document.getElementById('exportForm');
            const params = new URLSearchParams(new FormData(form));

            const url = "{{ route('export.history.pdf') }}" + '?' + params.toString();

            window.open(url, '_blank');
        });

        document.querySelectorAll('.btn-preset').forEach(btn => {
            btn.addEventListener('click', () => setPresetActive(btn.dataset.preset));
        });

        // Reset highlight preset jika user ganti tanggal manual
        [exportDateFrom, exportDateTo].forEach(input => {
            input.addEventListener('change', () => {
                document.querySelectorAll('.btn-preset').forEach(b => {
                    b.classList.remove('bg-emerald-200', 'text-emerald-700', 'ring-1', 'ring-emerald-400');
                    b.classList.add('bg-gray-100', 'text-gray-600');
                });
                presetPreview.classList.add('hidden');
            });
        });

        // Submit: pakai iframe agar modal bisa ditutup otomatis setelah download
        document.getElementById('exportForm').addEventListener('submit', e => {
            e.preventDefault();

            if (exportDateFrom.value && exportDateTo.value && exportDateTo.value < exportDateFrom.value) {
                exportDateError.classList.remove('hidden');
                return;
            }
            exportDateError.classList.add('hidden');

            // Bangun URL dari form
            const form   = document.getElementById('exportForm');
            const params = new URLSearchParams(new FormData(form));
            const url    = form.action + '?' + params.toString();

            // Ubah tombol jadi loading
            const btn = document.getElementById('btnDoExport');
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg> Memproses...`;

            // Trigger download via iframe — halaman tidak navigasi, modal bisa ditutup
            exportIframe.src = url;

            // Tutup modal & reset tombol setelah 2.5 detik (file sudah mulai diunduh)
            setTimeout(() => {
                closeExportModal();
                btn.disabled = false;
                btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg> Download Excel`;
            }, 2500);
        });

        // ── Detail Modal ─────────────────────────────────────────────────────────
        const historyModal        = document.getElementById('historyDetailModal');
        const historyModalContent = document.getElementById('historyDetailModalContent');

        document.getElementById('closeHistoryModal').addEventListener('click', () => {
            historyModal.classList.add('hidden');
            historyModal.classList.remove('flex');
        });
        historyModal.addEventListener('click', e => {
            if (!historyModalContent.contains(e.target)) {
                historyModal.classList.add('hidden');
                historyModal.classList.remove('flex');
            }
        });

        document.querySelectorAll('.btn-history-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('hd-code').textContent        = btn.dataset.code;
                document.getElementById('hd-name').textContent        = btn.dataset.name;
                document.getElementById('hd-department').textContent  = btn.dataset.department;
                document.getElementById('hd-email').textContent       = btn.dataset.email;
                document.getElementById('hd-destination').textContent = btn.dataset.destination;
                document.getElementById('hd-purpose').textContent     = btn.dataset.purpose;
                document.getElementById('hd-date').textContent        = btn.dataset.date;
                document.getElementById('hd-start').textContent       = btn.dataset.start;
                document.getElementById('hd-end').textContent         = btn.dataset.end;
                document.getElementById('hd-comment').textContent     = btn.dataset.comment;
                historyModal.classList.remove('hidden');
                historyModal.classList.add('flex');
            });
        });
    </script>

</x-app-layout>