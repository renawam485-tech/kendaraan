<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Pantau Unit Aktif
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- COMPACT FILTER BAR --}}
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <form action="{{ route('admin.active') }}" method="GET" class="flex items-center gap-3">

                        {{-- Status Pills --}}
                        <div class="flex items-center gap-1.5 pl-3">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}"
                                id="statusInput">

                            <button type="button" onclick="setStatus('all')"
                                class="status-pill {{ request('status', 'all') == 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-1.5 rounded-md text-sm font-medium transition"
                                data-status="all">
                                <span>Semua</span>
                            </button>

                            <div class="w-px h-8 bg-gray-300 mx-1"></div>

                            <button type="button" onclick="setStatus('prepared')"
                                class="status-pill {{ request('status') == 'prepared' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-1.5 rounded-md text-sm font-medium transition"
                                data-status="prepared">
                                <span>Disiapkan</span>
                            </button>

                            <div class="w-px h-8 bg-gray-300 mx-1"></div>

                            <button type="button" onclick="setStatus('active')"
                                class="status-pill {{ request('status') == 'active' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-1.5 rounded-md text-sm font-medium transition"
                                data-status="active">
                                <span>Sedang Jalan</span>
                            </button>

                            <div class="w-px h-8 bg-gray-300 mx-1"></div>
                        </div>

                        {{-- Search Input --}}
                        <div class="flex-1 max-w-md">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari peminjam, tujuan..."
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Source Filter --}}
                        <select name="source"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 pr-8 focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Unit</option>
                            <option value="internal" {{ request('source') == 'internal' ? 'selected' : '' }}>Mobil
                                Kampus</option>
                            <option value="external" {{ request('source') == 'external' ? 'selected' : '' }}>Sewa Luar
                            </option>
                        </select>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 border-l border-gray-300 pl-3">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                                Cari
                            </button>
                            <a href="{{ route('admin.active') }}"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- COMPACT TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th
                                    class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Kode</th>
                                <th
                                    class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Peminjam</th>
                                <th
                                    class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Jadwal</th>
                                <th
                                    class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Tujuan</th>
                                <th
                                    class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Unit</th>
                                <th
                                    class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($activeTrips as $trip)
                                <tr class="hover:bg-gray-50 transition-colors" x-data="{ showPurposeBubble: false }">
                                    <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-600 font-medium">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- Kode Booking --}}
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <span
                                            class="text-xs font-mono font-semibold text-blue-600">{{ $trip->booking_code }}</span>
                                    </td>

                                    {{-- Peminjam --}}
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $trip->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $trip->user->email }}</div>
                                    </td>

                                    {{-- Jadwal --}}
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="text-xs space-y-0.5">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3 h-3 text-green-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span
                                                    class="text-gray-700 font-medium">{{ $trip->start_time->format('d M, H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3 h-3 text-red-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span
                                                    class="text-gray-700 font-medium">{{ $trip->end_time->format('d M, H:i') }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Tujuan --}}
                                    <td class="px-3 py-3 max-w-xs">
                                        <div class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $trip->destination }}
                                        </div>

                                        @if ($trip->purpose)
                                            <div class="mt-1">
                                                <div class="purpose-preview text-xs text-gray-600 bg-gray-50 p-1.5 rounded border border-gray-200 truncate cursor-pointer hover:bg-gray-100 transition"
                                                    onclick="showPurposeBubble(event, `{{ e($trip->purpose) }}`)">
                                                    <span class="font-semibold text-gray-700">Keperluan:</span>
                                                    {{ Str::limit($trip->purpose, 40) }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Unit --}}
                                    <td class="px-3 py-3">
                                        @if ($trip->fulfillment_source == 'internal')
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $trip->vehicle->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500 font-mono">
                                                {{ $trip->vehicle->license_plate ?? '' }}</div>
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
                                                    class="text-sm font-semibold text-orange-600">{{ $trip->vendor_name ?? 'Vendor' }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $trip->external_vehicle_detail ?? '' }}</div>
                                        @endif
                                        <div class="text-xs text-gray-400 mt-1">
                                            Driver: <span
                                                class="text-gray-600 font-medium">{{ $trip->driver ? $trip->driver->name : 'Lepas Kunci' }}</span>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        @if ($trip->status === \App\Enums\BookingStatus::Prepared)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                                Disiapkan
                                            </span>
                                        @elseif($trip->status === \App\Enums\BookingStatus::Active)
                                            @if (now() > $trip->end_time)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <span
                                                        class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5 animate-pulse"></span>
                                                    Terlambat
                                                </span>
                                                <div class="text-[10px] text-red-600 mt-1 font-medium">
                                                    {{ now()->diffForHumans($trip->end_time, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                    Sedang Jalan
                                                </span>
                                            @endif
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-3 py-3 whitespace-nowrap text-right">
                                        @if ($trip->status === \App\Enums\BookingStatus::Prepared)
                                            <form method="POST" action="{{ route('admin.start.trip', $trip) }}"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 bg-green-600 text-white text-xs px-3 py-1.5 rounded-md font-medium hover:bg-green-700 transition shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Mulai
                                                </button>
                                            </form>
                                        @endif

                                        @if ($trip->status === \App\Enums\BookingStatus::Active)
                                            <button
                                                onclick="openCompleteModal('{{ route('admin.complete.trip', $trip) }}', '{{ $trip->vehicle->name ?? $trip->external_vehicle_detail }}')"
                                                class="inline-flex items-center gap-1.5 bg-blue-600 text-white text-xs px-3 py-1.5 rounded-md font-medium hover:bg-blue-700 transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Selesai
                                            </button>
                                        @endif
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
                                        <p class="mt-2 text-sm text-gray-500">Tidak ada kendaraan dalam pantauan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL COMPACT --}}
    <div id="completeModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Konfirmasi Unit Kembali</h3>
            </div>

            <form id="completeForm" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label for="trip_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Perjalanan
                    </label>
                    <textarea id="trip_notes" name="trip_notes"
                        class="w-full border-gray-300 rounded-md text-sm py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        rows="3"></textarea>
                </div>


                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-green-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-green-700 transition shadow-sm">
                        Simpan & Selesaikan
                    </button>
                    <button type="button" onclick="closeCompleteModal()"
                        class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Posisi bubble untuk layar kecil - muncul di atas */
        @media (max-width: 640px) {
            .purpose-bubble {
                left: 0;
                right: auto;
                top: auto;
                bottom: calc(100% + 0.5rem);
                min-width: 250px;
                max-width: 280px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animasi untuk mobile */
        @media (max-width: 640px) {
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(8px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }

        /* Pastikan tabel tidak overflow */
        .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- GLOBAL PURPOSE BUBBLE --}}
    <div id="globalPurposeBubble"
        class="hidden absolute bg-white border border-gray-200 rounded-lg shadow-xl p-3 z-[9999] max-w-sm max-h-72 overflow-y-auto text-xs">
    </div>
    <script>
        const purposeBubble = document.getElementById('globalPurposeBubble');

        function setStatus(status) {
            document.getElementById('statusInput').value = status;
            document.querySelectorAll('.status-pill').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            event.target.closest('form').submit();
        }

        function openCompleteModal(url, vehicleName) {
            document.getElementById('completeForm').action = url;
            document.getElementById('modalTitle').innerText = 'Unit Kembali: ' + vehicleName;
            document.getElementById('completeModal').classList.remove('hidden');
        }

        function closeCompleteModal() {
            document.getElementById('completeModal').classList.add('hidden');
        }

        function showPurposeBubble(event, content) {
            event.stopPropagation();

            const rect = event.currentTarget.getBoundingClientRect();

            purposeBubble.innerHTML = `
                <div class="font-semibold text-gray-700 mb-1">Keperluan Lengkap:</div>
                <div class="text-gray-600">${content}</div>
            `;

            // tambahkan offset scroll
            const top = rect.bottom + window.scrollY + 8;
            const left = rect.left + window.scrollX;

            purposeBubble.style.top = top + "px";
            purposeBubble.style.left = left + "px";

            purposeBubble.classList.remove('hidden');
        }


        // Close modal on outside click
        document.getElementById('completeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCompleteModal();
            }
        });

        document.addEventListener('click', function(e) {
            if (!purposeBubble.contains(e.target)) {
                purposeBubble.classList.add('hidden');
            }
        });
    </script>

</x-app-layout>
