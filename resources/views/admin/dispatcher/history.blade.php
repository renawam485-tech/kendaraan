<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Laporan Riwayat Perjalanan
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- COMPACT FILTER BAR --}}
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <form action="{{ route('admin.trip.history') }}" method="GET" class="flex items-center gap-3">

                        <div class="flex-1 max-w-md">
                            <label for="filterSearch" class="sr-only">Cari kode booking, peminjam, tujuan</label>
                            <input type="text" id="filterSearch" name="search" value="{{ request('search') }}"
                                placeholder="Cari kode booking, peminjam, tujuan..."
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <label for="filterSource" class="sr-only">Filter unit</label>
                        <select id="filterSource" name="source"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 pr-8 focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Unit</option>
                            <option value="internal" {{ request('source') == 'internal' ? 'selected' : '' }}>Mobil
                                Kampus</option>
                            <option value="external" {{ request('source') == 'external' ? 'selected' : '' }}>Sewa Luar
                            </option>
                        </select>

                        <label for="filterStatus" class="sr-only">Filter status</label>
                        <select id="filterStatus" name="status"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 pr-8 focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                Dibatalkan</option>
                        </select>

                        <div class="flex gap-2 border-l border-gray-300 pl-3">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                                Cari
                            </button>
                            <a href="{{ route('admin.trip.history') }}"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                Reset
                            </a>
                        </div>

                        <button type="button" id="btnOpenExport"
                            class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            Export Excel
                        </button>
                    </form>
                </div>

                {{-- COMPACT TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Kode
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Tanggal Jalan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Peminjam</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Tujuan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Unit
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Status Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($archives as $index => $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td
                                        class="px-3 py-3 whitespace-nowrap text-xs text-gray-600 font-medium text-center">
                                        {{ $archives->firstItem() + $index }}
                                    </td>

                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <span class="text-xs font-mono font-semibold text-blue-600">
                                            {{ $log->booking_code }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $log->start_time->format('d/m/Y') }}
                                        </div>
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $log->destination }}</div>
                                    </td>

                                    <td class="px-3 py-3">
                                        @if ($log->fulfillment_source == 'internal')
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $log->vehicle->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500 font-mono">
                                                {{ $log->vehicle->license_plate ?? '' }}</div>
                                        @else
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-orange-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                                    <path
                                                        d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                                </svg>
                                                <span
                                                    class="text-sm font-semibold text-orange-600">{{ $log->vendor_name ?? 'Vendor' }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $log->external_vehicle_detail ?? '' }}</div>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold 
                                            {{ $log->status == \App\Enums\BookingStatus::Completed ? 'bg-blue-400 text-white' : 'bg-red-400 text-white' }}">
                                            {{ $log->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Tidak ada riwayat perjalanan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($archives->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $archives->links() }}
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
                    <h3 class="text-white font-bold text-base">Export Laporan ke Excel</h3>
                </div>
                <button id="closeExportModal"
                    class="text-white/70 hover:text-white text-2xl leading-none font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.trip.history.export') }}" method="GET" id="exportForm">
                <div class="px-6 py-5 space-y-4">

                    <p class="text-sm text-gray-500">
                        Tentukan rentang tanggal dan filter untuk menentukan data yang akan diekspor ke file
                        <strong>.xlsx</strong>.
                    </p>

                    {{-- Shortcut preset --}}
                    <div>
                        <span class="block text-xs font-semibold text-gray-600 mb-1.5">Pilih Cepat Periode</span>
                        <div class="flex flex-wrap gap-2">
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
                        <p id="presetPreview" class="mt-2 text-xs text-emerald-700 font-medium hidden"></p>
                    </div>

                    {{-- Date range --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="exportDateFrom" class="block text-xs font-semibold text-gray-600 mb-1">
                                Dari Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="exportDateFrom" name="export_date_from" required
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label for="exportDateTo" class="block text-xs font-semibold text-gray-600 mb-1">
                                Sampai Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="exportDateTo" name="export_date_to" required
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                    <p id="exportDateError" class="text-xs text-red-500 hidden">
                        ⚠ Tanggal akhir tidak boleh sebelum tanggal awal.
                    </p>

                    {{-- Filter Unit --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="exportSource" class="block text-xs font-semibold text-gray-600 mb-1">Filter
                                Unit</label>
                            <select id="exportSource" name="export_source"
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-emerald-500">
                                <option value="">Semua Unit</option>
                                <option value="internal">Mobil Kampus</option>
                                <option value="external">Sewa Luar</option>
                            </select>
                        </div>
                        <div>
                            <label for="exportStatus" class="block text-xs font-semibold text-gray-600 mb-1">Filter
                                Status</label>
                            <select id="exportStatus" name="export_status"
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-emerald-500">
                                <option value="">Semua Status</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    {{-- Info kolom --}}
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-emerald-700">
                            File Excel memuat: Kode Booking, Tgl Berangkat, Peminjam, Departemen, Email,
                            Tujuan, Keperluan, Waktu Selesai, Unit/Vendor, Detail Kendaraan, Status Akhir.
                        </p>
                    </div>

                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    <button type="button" id="cancelExportModal"
                        class="bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnDoExport"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- iframe tersembunyi untuk trigger download tanpa navigasi halaman --}}
    <iframe id="exportIframe" class="hidden" aria-hidden="true"></iframe>

    <script>
        function toYMD(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        function formatLabel(d) {
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        const today = new Date();

        const presets = {
            this_month: {
                range: () => [
                    new Date(today.getFullYear(), today.getMonth(), 1),
                    new Date(today.getFullYear(), today.getMonth() + 1, 0),
                ]
            },
            last_month: {
                range: () => [
                    new Date(today.getFullYear(), today.getMonth() - 1, 1),
                    new Date(today.getFullYear(), today.getMonth(), 0),
                ]
            },
            last_3_months: {
                range: () => [
                    new Date(today.getFullYear(), today.getMonth() - 2, 1),
                    new Date(today.getFullYear(), today.getMonth() + 1, 0),
                ]
            },
            this_year: {
                range: () => [
                    new Date(today.getFullYear(), 0, 1),
                    new Date(today.getFullYear(), 11, 31),
                ]
            },
        };

        const exportModal = document.getElementById('exportModal');
        const exportModalContent = document.getElementById('exportModalContent');
        const exportDateFrom = document.getElementById('exportDateFrom');
        const exportDateTo = document.getElementById('exportDateTo');
        const exportDateError = document.getElementById('exportDateError');
        const presetPreview = document.getElementById('presetPreview');
        const exportIframe = document.getElementById('exportIframe');

        function setPresetActive(key) {
            const [f, t] = presets[key].range();
            exportDateFrom.value = toYMD(f);
            exportDateTo.value = toYMD(t);
            exportDateError.classList.add('hidden');
            presetPreview.textContent = `📅 ${formatLabel(f)} – ${formatLabel(t)}`;
            presetPreview.classList.remove('hidden');

            document.querySelectorAll('.btn-preset').forEach(b => {
                b.classList.remove('bg-emerald-200', 'text-emerald-700', 'ring-1', 'ring-emerald-400');
                b.classList.add('bg-gray-100', 'text-gray-600');
            });
            const btn = document.querySelector(`.btn-preset[data-preset="${key}"]`);
            btn.classList.remove('bg-gray-100', 'text-gray-600');
            btn.classList.add('bg-emerald-200', 'text-emerald-700', 'ring-1', 'ring-emerald-400');
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

        document.querySelectorAll('.btn-preset').forEach(btn => {
            btn.addEventListener('click', () => setPresetActive(btn.dataset.preset));
        });

        [exportDateFrom, exportDateTo].forEach(input => {
            input.addEventListener('change', () => {
                document.querySelectorAll('.btn-preset').forEach(b => {
                    b.classList.remove('bg-emerald-200', 'text-emerald-700', 'ring-1',
                        'ring-emerald-400');
                    b.classList.add('bg-gray-100', 'text-gray-600');
                });
                presetPreview.classList.add('hidden');
            });
        });

        document.getElementById('exportForm').addEventListener('submit', e => {
            e.preventDefault();

            if (exportDateFrom.value && exportDateTo.value && exportDateTo.value < exportDateFrom.value) {
                exportDateError.classList.remove('hidden');
                return;
            }
            exportDateError.classList.add('hidden');

            const form = document.getElementById('exportForm');
            const params = new URLSearchParams(new FormData(form));
            const url = form.action + '?' + params.toString();

            const btn = document.getElementById('btnDoExport');
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg> Memproses...`;

            exportIframe.src = url;

            setTimeout(() => {
                closeExportModal();
                btn.disabled = false;
                btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg> Download Excel`;
            }, 2500);
        });
    </script>

    <style>
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
    </style>

</x-app-layout>
