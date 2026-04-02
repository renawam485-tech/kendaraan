<x-app-layout>
    <div class="py-6 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- GREETING CARD --}}
            <div
                class="bg-blue-600 rounded-xl shadow-md px-6 py-5 flex items-center justify-between mx-4 sm:mx-0 transition-all duration-300 hover:shadow-blue-200 hover:shadow-xl group cursor-default">
                <div>
                    <p class="text-blue-100 text-sm transition-all group-hover:text-white">Selamat datang kembali,</p>
                    <h2 class="text-white text-xl font-bold mt-0.5">{{ auth()->user()->name }}</h2>
                    <p class="text-blue-200 text-xs mt-1 transition-all group-hover:text-blue-50">
                        {{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════
                 ADMIN GA SECTION
            ════════════════════════════════════════════════════════════════ --}}
            @if (auth()->user()->hasRole('admin'))

                {{-- LABEL SEKSI --}}
                <div class="flex items-center gap-3 px-4 sm:px-0">
                    <span class="h-px flex-1 bg-gray-200"></span>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Ringkasan Admin</span>
                    <span class="h-px flex-1 bg-gray-200"></span>
                </div>

                {{-- 4 ADMIN CARDS --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 px-4 sm:px-0">

                    {{-- CARD 1: Pengajuan Urgent --}}
                    <div
                        class="bg-white rounded-xl shadow-sm border border-amber-200 px-5 py-4 flex flex-row items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md cursor-default group">
                        <div class="min-w-0">
                            <p class="text-xs text-amber-600 uppercase tracking-wide font-semibold">Urgent</p>
                            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $urgentBookings->total() }}</p>
                            <p class="text-[10px] text-amber-500 mt-0.5 leading-tight">Berangkat &lt; 1 jam,<br>belum
                                disetujui</p>
                        </div>
                        <div
                            class="w-11 h-11 flex-shrink-0 bg-amber-50 rounded-xl flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- CARD 2: Unit Berjalan (klik → pantau, filter active) --}}
                    <a href="{{ route('admin.active', ['status' => 'active']) }}"
                        class="bg-white rounded-xl shadow-sm border border-blue-200 px-5 py-4 flex flex-row items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-blue-400 cursor-pointer group">
                        <div class="min-w-0">
                            <p class="text-xs text-blue-600 uppercase tracking-wide font-semibold">Unit Berjalan</p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $activeTripsTotal }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 leading-snug">
                                <span class="text-blue-500 font-medium">{{ $activeInternal }}</span> kampus ·
                                <span class="text-indigo-500 font-medium">{{ $activeExternal }}</span> vendor
                            </p>
                        </div>
                        <div
                            class="w-11 h-11 flex-shrink-0 bg-blue-50 rounded-xl flex items-center justify-center text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                    </a>

                    {{-- CARD 3: Kembali Hari Ini (klik → pantau) --}}
                    <a href="{{ route('admin.active') }}"
                        class="bg-white rounded-xl shadow-sm border border-green-200 px-5 py-4 flex flex-row items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-green-400 cursor-pointer group">
                        <div class="min-w-0">
                            <p class="text-xs text-green-600 uppercase tracking-wide font-semibold">Kembali Hari Ini</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ $returningToday }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Unit jadwal kembali hari ini</p>
                        </div>
                        <div
                            class="w-11 h-11 flex-shrink-0 bg-green-50 rounded-xl flex items-center justify-center text-green-400 group-hover:bg-green-500 group-hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                        </div>
                    </a>

                    {{-- CARD 4: Terlambat (klik → pantau, filter late) --}}
                    <a href="{{ route('admin.active', ['status' => 'late']) }}"
                        class="bg-white rounded-xl shadow-sm border border-red-200 px-5 py-4 flex flex-row items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-red-400 cursor-pointer group">
                        <div class="min-w-0">
                            <p class="text-xs text-red-500 uppercase tracking-wide font-semibold">Terlambat</p>
                            <p class="text-2xl font-bold text-red-500 mt-1">{{ $lateCount }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Melewati jadwal kembali</p>
                        </div>
                        <div
                            class="w-11 h-11 flex-shrink-0 bg-red-50 rounded-xl flex items-center justify-center text-red-400 group-hover:bg-red-500 group-hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </a>

                </div>

                {{-- TABEL PENGAJUAN URGENT --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                    <div
                        class="border-b border-amber-100 bg-amber-50 px-4 sm:px-6 py-4 flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-3">
                            <span class="relative flex h-2.5 w-2.5">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                            </span>
                            <h3 class="text-sm font-semibold text-amber-700">Pengajuan Urgent — Belum Disetujui Atasan
                            </h3>
                            @if ($urgentBookings->total() > 0)
                                <span
                                    class="text-[10px] bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full font-semibold border border-amber-200">
                                    {{ $urgentBookings->total() }} pengajuan
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-amber-500">Jadwal berangkat dalam 1 jam ke depan</p>
                    </div>

                    @if ($urgentBookings->isEmpty())
                        <div class="px-6 py-10 text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-400">Tidak ada pengajuan urgent saat ini</p>
                        </div>
                    @else
                        {{-- Desktop Table --}}
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-amber-500 text-white">
                                    <tr>
                                        <th
                                            class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                            No</th>
                                        <th
                                            class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                            Kode</th>
                                        <th
                                            class="px-3 py-2.5 text-left   text-[10px] font-bold uppercase tracking-wider">
                                            Pemohon</th>
                                        <th
                                            class="px-3 py-2.5 text-left   text-[10px] font-bold uppercase tracking-wider">
                                            Tujuan & Keperluan</th>
                                        <th
                                            class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                            Berangkat</th>
                                        <th
                                            class="px-3 py-2.5 text-left   text-[10px] font-bold uppercase tracking-wider">
                                            Atasan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach ($urgentBookings as $i => $ub)
                                        @php
                                            $menit = now()->diffInMinutes($ub->start_time, false);
                                            $sisaLabel =
                                                $menit <= 0
                                                    ? 'Sekarang'
                                                    : ($menit < 60
                                                        ? $menit . ' mnt lagi'
                                                        : round($menit / 60, 1) . ' jam lagi');
                                        @endphp
                                        <tr class="hover:bg-amber-50 transition-colors">
                                            <td class="px-3 py-3 text-xs text-gray-500 text-center">
                                                {{ ($urgentBookings->currentPage() - 1) * $urgentBookings->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span
                                                    class="text-xs font-mono font-semibold text-amber-600">{{ $ub->booking_code }}</span>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $ub->user->name }}
                                                </div>
                                                <div class="text-xs text-gray-400">{{ $ub->user->email }}</div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="text-sm font-semibold text-gray-800">
                                                    {{ $ub->destination }}</div>
                                                <div class="text-xs text-gray-400 italic">
                                                    "{{ Str::limit($ub->purpose, 60) }}"</div>
                                            </td>
                                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                                <div class="text-xs font-medium text-gray-700">
                                                    {{ $ub->start_time->format('d M Y') }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $ub->start_time->format('H:i') }}</div>
                                                <span
                                                    class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                                    {{ $menit <= 30 ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }}">
                                                    {{ $sisaLabel }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3">
                                                @if ($ub->approver)
                                                    <div class="text-sm font-medium text-gray-800">
                                                        {{ $ub->approver->name }}</div>
                                                    <div class="text-xs text-gray-400">{{ $ub->approver->email }}
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div class="sm:hidden divide-y divide-gray-100">
                            @foreach ($urgentBookings as $ub)
                                @php
                                    $menit = now()->diffInMinutes($ub->start_time, false);
                                    $sisaLabel =
                                        $menit <= 0
                                            ? 'Sekarang'
                                            : ($menit < 60
                                                ? $menit . ' mnt lagi'
                                                : round($menit / 60, 1) . ' jam lagi');
                                @endphp
                                <div class="px-4 py-4 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="text-xs font-mono font-bold text-amber-600">{{ $ub->booking_code }}</span>
                                        <span
                                            class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                            {{ $menit <= 30 ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }}">
                                            {{ $sisaLabel }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $ub->destination }}</p>
                                    <p class="text-xs text-gray-500">{{ $ub->user->name }} ·
                                        {{ $ub->start_time->format('d M Y H:i') }}</p>
                                    @if ($ub->approver)
                                        <p class="text-xs text-gray-400">Atasan: {{ $ub->approver->name }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if ($urgentBookings->hasPages())
                            <div class="px-6 py-4 border-t border-amber-100 bg-amber-50">
                                {{ $urgentBookings->links() }}
                            </div>
                        @endif
                    @endif
                </div>

            @endif
            {{-- END ADMIN SECTION --}}

            <div class="flex items-center gap-3 px-4 sm:px-0">
                <span class="h-px flex-1 bg-gray-200"></span>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Data Pengajuan Pribadi</span>
                <span class="h-px flex-1 bg-gray-200"></span>
            </div>

            {{-- STAT CARDS (semua role) --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 px-4 sm:px-0">
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4 flex flex-row items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md group cursor-default">
                    <div class="min-w-0">
                        <p
                            class="text-xs text-gray-500 uppercase tracking-wide group-hover:text-gray-700 transition-colors">
                            Total Pengajuan</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1 truncate">{{ $totalAll }}</p>
                    </div>
                    <div
                        class="w-10 h-10 flex-shrink-0 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm border border-blue-100 px-5 py-4 flex flex-row items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md group cursor-default">
                    <div class="min-w-0">
                        <p class="text-xs text-blue-500 uppercase tracking-wide">Sedang Berjalan</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1 truncate">{{ $totalAktif }}</p>
                    </div>
                    <div
                        class="w-10 h-10 flex-shrink-0 bg-blue-50 rounded-xl flex items-center justify-center text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-all transform group-hover:rotate-12">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm border border-green-100 px-5 py-4 flex flex-row items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md group cursor-default">
                    <div class="min-w-0">
                        <p class="text-xs text-green-500 uppercase tracking-wide">Selesai</p>
                        <p class="text-2xl font-bold text-green-600 mt-1 truncate">{{ $totalDone }}</p>
                    </div>
                    <div
                        class="w-10 h-10 flex-shrink-0 bg-green-50 rounded-xl flex items-center justify-center text-green-400 group-hover:bg-green-500 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm border border-red-100 px-5 py-4 flex flex-row items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md group cursor-default">
                    <div class="min-w-0">
                        <p class="text-xs text-red-400 uppercase tracking-wide">Dibatalkan/Ditolak</p>
                        <p class="text-2xl font-bold text-red-500 mt-1 truncate">{{ $totalClosed }}</p>
                    </div>
                    <div
                        class="w-10 h-10 flex-shrink-0 bg-red-50 rounded-xl flex items-center justify-center text-red-400 group-hover:bg-red-500 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- SHORTCUT --}}
            <div class="flex gap-3 px-4 sm:px-0">
                <a href="{{ route('booking.create') }}"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm transition-all hover:shadow-blue-200 hover:shadow-lg active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Peminjaman
                </a>
                <a href="{{ route('booking.history') }}"
                    class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm transition-all active:scale-95">
                    Lihat Riwayat
                </a>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════
                 SECTION: PENGAJUAN AKTIF
            ════════════════════════════════════════════════════════════════ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-10">
                <div
                    class="border-b border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                        <h3 class="text-sm font-semibold text-gray-700">Pengajuan Aktif</h3>
                        <span class="text-[10px] bg-blue-50 text-blue-500 px-2 py-0.5 rounded-full font-medium">
                            {{ $bookingsAktif->total() }} data
                        </span>
                    </div>
                </div>

                {{-- MOBILE: Card list --}}
                <div class="sm:hidden divide-y divide-gray-100">
                    @forelse($bookingsAktif as $booking)
                        <div class="px-4 py-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span
                                        class="text-xs font-mono font-bold text-blue-600 truncate">{{ $booking->booking_code }}</span>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $booking->status->color() }}-100 text-{{ $booking->status->color() }}-700">
                                    {{ $booking->status->label() }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $booking->destination }}
                            </p>

                            <div class="flex gap-2 pt-1">
                                <button type="button"
                                    class="btn-show-detail flex-1 text-xs font-semibold text-blue-600 border border-blue-200 rounded-lg py-2 hover:bg-blue-50 transition"
                                    data-code="{{ $booking->booking_code }}"
                                    data-destination="{{ $booking->destination }}"
                                    data-purpose="{{ $booking->purpose }}"
                                    data-start="{{ $booking->start_time->format('d M Y H:i') }}"
                                    data-end="{{ $booking->end_time->format('d M Y H:i') }}"
                                    data-passenger="{{ $booking->passenger_count }}"
                                    data-driver="{{ $booking->with_driver ? 'Ya' : 'Tidak' }}"
                                    data-status-label="{{ $booking->status->label() }}"
                                    data-status-color="{{ $booking->status->color() }}">
                                    Lihat Detail
                                </button>

                                @if ($booking->status->value === 'pending')
                                    <form action="{{ route('booking.cancel', $booking) }}" method="POST"
                                        class="cancel-booking-form flex-1">
                                        @csrf
                                        <button type="button"
                                            class="btn-cancel-booking w-full text-xs font-semibold text-red-500 border border-red-200 rounded-lg py-2 hover:bg-red-50 transition">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center"><span class="text-xs text-blue-500 font-medium">Tidak ada
                                pengajuan aktif</span></div>
                    @endforelse
                </div>

                {{-- DESKTOP: Table --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Kode
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Jadwal</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Tujuan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Status</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($bookingsAktif as $booking)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-3 text-xs text-gray-600 text-center">
                                        {{ ($bookingsAktif->currentPage() - 1) * $bookingsAktif->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            class="text-xs font-mono font-semibold text-blue-600">{{ $booking->booking_code }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-center text-sm text-gray-800">
                                        {{ $booking->start_time->format('d M Y') }}
                                    </td>
                                    <td class="px-3 py-3 text-sm font-medium text-gray-900">
                                        {{ $booking->destination }}
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $booking->status->color() }}-100 text-{{ $booking->status->color() }}-700">
                                            {{ $booking->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button"
                                                class="btn-show-detail text-blue-600 hover:text-blue-800 text-xs font-semibold bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition"
                                                data-code="{{ $booking->booking_code }}"
                                                data-destination="{{ $booking->destination }}"
                                                data-purpose="{{ $booking->purpose }}"
                                                data-start="{{ $booking->start_time->format('d M Y H:i') }}"
                                                data-end="{{ $booking->end_time->format('d M Y H:i') }}"
                                                data-passenger="{{ $booking->passenger_count }}"
                                                data-driver="{{ $booking->with_driver ? 'Ya' : 'Tidak' }}"
                                                data-status-label="{{ $booking->status->label() }}"
                                                data-status-color="{{ $booking->status->color() }}">
                                                Detail
                                            </button>

                                            @if ($booking->status->value === 'pending')
                                                <form action="{{ route('booking.cancel', $booking) }}" method="POST"
                                                    class="cancel-booking-form">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn-cancel-booking text-red-500 hover:text-red-700 text-xs font-semibold transition bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md">Batalkan</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="px-3 py-10 text-center text-xs text-blue-500 font-medium">
                                        Tidak ada pengajuan aktif saat ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($bookingsAktif->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $bookingsAktif->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         MODAL BATAL
    ════════════════════════════════════════════════════════════════════════ --}}
    <div id="cancel-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm transition-opacity duration-200 opacity-0 pointer-events-none px-4">
        <div id="cancel-modal-box"
            class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 transform scale-95 transition-transform duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Batalkan Pengajuan?</h3>
            </div>
            <p class="text-sm text-gray-500 leading-relaxed mb-6">Apakah Anda yakin ingin membatalkan pengajuan ini?
                Tindakan ini tidak dapat diurungkan.</p>
            <div class="flex justify-end gap-2">
                <button type="button" id="close-modal-btn"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Kembali</button>
                <button type="button" id="confirm-cancel-btn"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Ya,
                    Batalkan</button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         MODAL DETAIL
    ════════════════════════════════════════════════════════════════════════ --}}
    <div id="booking-detail-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm transition-opacity duration-200 opacity-0 pointer-events-none px-4">
        <div id="booking-detail-box"
            class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-200 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Detail Pengajuan</h3>
                <button type="button" id="close-detail-btn"
                    class="text-gray-400 hover:text-gray-600 transition p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
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
                <button type="button" id="close-detail-btn-bottom"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Tutup</button>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- LOGIKA MODAL BATAL ---
            const cancelModal = document.getElementById('cancel-modal');
            const cancelModalBox = document.getElementById('cancel-modal-box');
            let formToSubmit = null;

            document.querySelectorAll(".btn-cancel-booking").forEach(btn => {
                btn.addEventListener("click", function() {
                    formToSubmit = this.closest(".cancel-booking-form");
                    cancelModal.classList.remove('opacity-0', 'pointer-events-none');
                    cancelModalBox.classList.remove('scale-95');
                });
            });

            function closeCancelModal() {
                cancelModal.classList.add('opacity-0', 'pointer-events-none');
                cancelModalBox.classList.add('scale-95');
                setTimeout(() => {
                    formToSubmit = null;
                }, 200);
            }

            document.getElementById('close-modal-btn').addEventListener('click', closeCancelModal);
            document.getElementById('confirm-cancel-btn').addEventListener('click', function() {
                if (formToSubmit) formToSubmit.submit();
            });
            cancelModal.addEventListener('click', function(e) {
                if (e.target === cancelModal) closeCancelModal();
            });

            // --- LOGIKA MODAL DETAIL ---
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
                    document.getElementById('mdl-status').innerHTML =
                        `<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-${data.statusColor}-100 text-${data.statusColor}-700">${data.statusLabel}</span>`;

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
            detailModal.addEventListener('click', function(e) {
                if (e.target === detailModal) closeDetailModal();
            });
        });
    </script>
</x-app-layout>
