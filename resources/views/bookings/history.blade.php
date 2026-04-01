<x-app-layout>
    <div class="py-6 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 mx-4 sm:mx-0">
                <h2 class="text-2xl font-bold text-gray-800">Riwayat Pengajuan</h2>
                <p class="text-sm text-gray-500 mt-1">Daftar semua perjalanan Anda yang telah selesai, batal, atau ditolak.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-10">
                <div class="border-b border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full bg-gray-400"></div>
                        <h3 class="text-sm font-semibold text-gray-700">Daftar Riwayat</h3>
                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-medium">
                            {{ $bookingsRiwayat->count() }} data
                        </span>
                    </div>
                </div>

                {{-- MOBILE: Card list --}}
                <div class="sm:hidden divide-y divide-gray-100">
                    @forelse($bookingsRiwayat as $index => $booking)
                        <div class="px-4 py-4 space-y-3 opacity-90">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-[10px] text-gray-400">#{{ $index + 1 }}</span>
                                    <span class="text-xs font-mono font-semibold text-gray-600 truncate">{{ $booking->booking_code }}</span>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold flex-shrink-0 bg-{{ $booking->status->color() }}-100 text-{{ $booking->status->color() }}-700">
                                    {{ $booking->status->label() }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-snug">{{ $booking->destination }}</p>
                            
                            <button type="button" 
                                class="btn-show-detail w-full text-xs font-semibold text-blue-600 border border-blue-200 rounded-lg py-2 hover:bg-blue-50 transition"
                                data-code="{{ $booking->booking_code }}" data-destination="{{ $booking->destination }}"
                                data-purpose="{{ $booking->purpose }}" data-start="{{ $booking->start_time->format('d M Y H:i') }}"
                                data-end="{{ $booking->end_time->format('d M Y H:i') }}" data-passenger="{{ $booking->passenger_count }}"
                                data-driver="{{ $booking->with_driver ? 'Ya' : 'Tidak' }}" data-status-label="{{ $booking->status->label() }}"
                                data-status-color="{{ $booking->status->color() }}">
                                Lihat Detail
                            </button>
                        </div>
                    @empty
                        <div class="px-4 py-10 text-center">
                            <span class="text-xs text-gray-400 font-medium">Belum ada riwayat tertutup</span>
                        </div>
                    @endforelse
                </div>

                {{-- DESKTOP: Table --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-500 text-white">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase">No</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase">Kode</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase">Jadwal</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase">Tujuan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase">Status</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($bookingsRiwayat as $index => $booking)
                                <tr class="hover:bg-gray-50 transition-colors opacity-90">
                                    <td class="px-3 py-3 text-xs text-gray-500 text-center">{{ $index + 1 }}</td>
                                    <td class="px-3 py-3 text-center"><span class="text-xs font-mono text-gray-600">{{ $booking->booking_code }}</span></td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="text-sm text-gray-600">{{ $booking->start_time->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-700">{{ $booking->destination }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $booking->status->color() }}-100 text-{{ $booking->status->color() }}-700">
                                            {{ $booking->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <button type="button" 
                                            class="btn-show-detail text-blue-600 hover:text-blue-800 text-xs font-semibold bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition"
                                            data-code="{{ $booking->booking_code }}" data-destination="{{ $booking->destination }}"
                                            data-purpose="{{ $booking->purpose }}" data-start="{{ $booking->start_time->format('d M Y H:i') }}"
                                            data-end="{{ $booking->end_time->format('d M Y H:i') }}" data-passenger="{{ $booking->passenger_count }}"
                                            data-driver="{{ $booking->with_driver ? 'Ya' : 'Tidak' }}" data-status-label="{{ $booking->status->label() }}"
                                            data-status-color="{{ $booking->status->color() }}">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-10 text-center text-xs text-gray-400 font-medium">Belum ada riwayat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div id="booking-detail-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm transition-opacity duration-200 opacity-0 pointer-events-none px-4">
        <div id="booking-detail-box" class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-200 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Detail Riwayat</h3>
                <button type="button" id="close-detail-btn" class="text-gray-400 hover:text-gray-600 transition p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-y-4 gap-x-4 text-sm">
                    <div class="sm:col-span-1 text-gray-500 font-medium">Kode Booking</div>
                    <div class="sm:col-span-2 font-mono font-bold text-blue-600" id="mdl-code"></div>

                    <div class="sm:col-span-1 text-gray-500 font-medium">Tujuan</div>
                    <div class="sm:col-span-2 text-gray-800 font-medium" id="mdl-destination"></div>

                    <div class="sm:col-span-1 text-gray-500 font-medium">Keperluan</div>
                    <div class="sm:col-span-2 text-gray-800" id="mdl-purpose"></div>

                    <div class="sm:col-span-1 text-gray-500 font-medium">Waktu Mulai</div>
                    <div class="sm:col-span-2 text-gray-800" id="mdl-start"></div>

                    <div class="sm:col-span-1 text-gray-500 font-medium">Waktu Selesai</div>
                    <div class="sm:col-span-2 text-gray-800" id="mdl-end"></div>

                    <div class="sm:col-span-1 text-gray-500 font-medium">Penumpang</div>
                    <div class="sm:col-span-2 text-gray-800"><span id="mdl-passenger"></span> Orang</div>

                    <div class="sm:col-span-1 text-gray-500 font-medium">Pengemudi</div>
                    <div class="sm:col-span-2 text-gray-800" id="mdl-driver"></div>

                    <div class="sm:col-span-1 text-gray-500 font-medium flex items-center">Status</div>
                    <div class="sm:col-span-2" id="mdl-status"></div>
                </dl>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end shrink-0">
                <button type="button" id="close-detail-btn-bottom" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Tutup</button>
            </div>
        </div>
    </div>

    {{-- SCRIPT MODAL DETAIL --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const detailModal = document.getElementById('booking-detail-modal');
        const detailBox = document.getElementById('booking-detail-box');

        document.querySelectorAll('.btn-show-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = this.dataset;
                document.getElementById('mdl-code').textContent = data.code;
                document.getElementById('mdl-destination').textContent = data.destination;
                document.getElementById('mdl-purpose').textContent = data.purpose;
                document.getElementById('mdl-start').textContent = data.start;
                document.getElementById('mdl-end').textContent = data.end;
                document.getElementById('mdl-passenger').textContent = data.passenger;
                document.getElementById('mdl-driver').textContent = data.driver;
                
                document.getElementById('mdl-status').innerHTML = `<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-${data.statusColor}-100 text-${data.statusColor}-700">${data.statusLabel}</span>`;

                detailModal.classList.remove('opacity-0', 'pointer-events-none');
                detailBox.classList.remove('scale-95');
            });
        });

        function closeDetailModal() {
            detailModal.classList.add('opacity-0', 'pointer-events-none');
            detailBox.classList.add('scale-95');
        }

        document.getElementById('close-detail-btn').addEventListener('click', closeDetailModal);
        document.getElementById('close-detail-btn-bottom').addEventListener('click', closeDetailModal);
        detailModal.addEventListener('click', function(e) { if (e.target === detailModal) closeDetailModal(); });
    });
    </script>
</x-app-layout>