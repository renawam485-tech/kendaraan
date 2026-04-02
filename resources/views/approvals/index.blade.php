<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Tugas Persetujuan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- FILTER BAR --}}
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <form action="{{ route('approvals.index') }}" method="GET" class="flex items-center gap-3">
                        <div class="flex-1 max-w-md">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari kode booking atau pemohon..."
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <div class="flex gap-2 border-l border-gray-300 pl-3">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                                Cari
                            </button>
                            <a href="{{ route('approvals.index') }}"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- BANNER URGENT --}}
                @php
                    $urgentCount = $approvals->getCollection()->where('is_urgent', true)->count();
                @endphp
                @if ($urgentCount > 0)
                    <div class="bg-amber-50 border-b border-amber-200 px-6 py-3 flex items-center gap-3">
                        <span class="relative flex h-2.5 w-2.5 flex-shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                        </span>
                        <p class="text-sm font-semibold text-amber-700">
                            ⚡ Terdapat
                            <span class="bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded font-bold">{{ $urgentCount }}</span>
                            permohonan URGENT — jam berangkat kurang dari 1 jam, harap segera diproses!
                        </p>
                    </div>
                @endif

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Kode</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Tgl Request</th>
                                <th class="px-3 py-2.5 text-left   text-[10px] font-bold uppercase tracking-wider">Pemohon</th>
                                <th class="px-3 py-2.5 text-left   text-[10px] font-bold uppercase tracking-wider">Tujuan & Keperluan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Batas Approval</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($approvals as $index => $approval)
                                <tr class="transition-colors
                                    {{ $approval->is_urgent ? 'bg-amber-50 hover:bg-amber-100' : 'hover:bg-gray-50' }}">

                                    <td class="px-3 py-3 text-xs text-gray-600 font-medium text-center">
                                        {{ $approvals->firstItem() + $index }}
                                    </td>

                                    {{-- Kode + badge --}}
                                    <td class="px-3 py-3 text-center">
                                        <span class="text-xs font-mono font-semibold
                                            {{ $approval->is_urgent ? 'text-amber-600' : 'text-blue-600' }}">
                                            {{ $approval->booking_code }}
                                        </span>

                                        @if ($approval->is_urgent)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700
                                                             border border-amber-300 rounded-full px-2 py-0.5
                                                             text-[10px] font-bold uppercase tracking-wider">
                                                    <span class="relative flex h-1.5 w-1.5">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span>
                                                    </span>
                                                    ⚡ URGENT
                                                </span>
                                            </div>
                                        @endif

                                        @if ($approval->escalated_to_admin)
                                            <div class="mt-1">
                                                <span class="inline-block bg-red-100 text-red-700 border border-red-300
                                                             rounded-full px-2 py-0.5 text-[10px] font-bold uppercase">
                                                    🚨 ESKALASI
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Tanggal Request --}}
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-700">{{ $approval->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $approval->created_at->format('H:i') }}</div>
                                    </td>

                                    {{-- Pemohon --}}
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $approval->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $approval->user->email }}</div>
                                    </td>

                                    {{-- Tujuan & Keperluan --}}
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $approval->destination }}</div>
                                        <div class="text-xs text-gray-500 italic mt-0.5">"{{ $approval->purpose }}"</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            🕐 Berangkat: {{ $approval->start_time->format('d M Y, H:i') }}
                                        </div>
                                    </td>

                                    {{-- Batas Approval --}}
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        @if ($approval->approval_deadline)
                                            @php
                                                $deadline         = $approval->approval_deadline;
                                                $isExpired        = $deadline->isPast();
                                                $diffMins         = abs((int) now()->diffInMinutes($deadline));
                                                $diffLabel        = $diffMins >= 60
                                                    ? round($diffMins / 60, 1) . ' jam'
                                                    : $diffMins . ' mnt';
                                            @endphp

                                            <div class="text-xs text-gray-600 font-medium">{{ $deadline->format('H:i') }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $deadline->format('d M Y') }}</div>

                                            @if ($isExpired)
                                                <span class="inline-block mt-1 bg-red-100 text-red-600
                                                             border border-red-200 rounded-full px-2 py-0.5 text-[10px] font-bold">
                                                    Terlambat {{ $diffLabel }}
                                                </span>
                                            @elseif ($diffMins <= 10)
                                                <span class="inline-block mt-1 bg-red-50 text-red-500
                                                             border border-red-200 rounded-full px-2 py-0.5 text-[10px] font-bold"
                                                      data-deadline="{{ $deadline->toIso8601String() }}"
                                                      data-countdown>
                                                    Sisa {{ $diffLabel }}
                                                </span>
                                            @elseif ($diffMins <= 30)
                                                <span class="inline-block mt-1 bg-amber-50 text-amber-600
                                                             border border-amber-200 rounded-full px-2 py-0.5 text-[10px] font-bold"
                                                      data-deadline="{{ $deadline->toIso8601String() }}"
                                                      data-countdown>
                                                    Sisa {{ $diffLabel }}
                                                </span>
                                            @else
                                                <span class="inline-block mt-1 bg-green-50 text-green-600
                                                             border border-green-200 rounded-full px-2 py-0.5 text-[10px] font-bold">
                                                    Sisa {{ $diffLabel }}
                                                </span>
                                            @endif

                                            <div class="text-[10px] mt-0.5 {{ $approval->is_urgent ? 'text-amber-500 font-semibold' : 'text-gray-400' }}">
                                                {{ $approval->is_urgent ? '⚡ Urgent' : 'Normal' }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>

                                    {{-- Tindakan --}}
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <div class="flex justify-center items-center gap-2">

                                            {{-- DETAIL --}}
                                            <button type="button"
                                                data-approval='@json($approval->load("user"))'
                                                class="btn-detail bg-gray-100 hover:bg-gray-200 text-gray-700
                                                       px-3 py-1.5 rounded-md text-[10px] uppercase font-bold tracking-wider transition">
                                                Detail
                                            </button>

                                            {{-- SETUJUI --}}
                                            <form action="{{ route('approvals.decide', $approval) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-md text-[10px] uppercase font-bold tracking-wider transition
                                                        {{ $approval->is_urgent
                                                            ? 'bg-amber-500 hover:bg-amber-600 text-white ring-2 ring-amber-300 ring-offset-1'
                                                            : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                                                    {{ $approval->is_urgent ? '⚡ Setujui' : 'Setujui' }}
                                                </button>
                                            </form>

                                            {{-- TOLAK --}}
                                            <form action="{{ route('approvals.decide', $approval) }}" method="POST"
                                                class="btn-reject-form">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <input type="hidden" name="comment" class="reject-reason-input">
                                                <button type="button"
                                                    class="btn-reject-trigger border border-gray-300 text-gray-600
                                                           hover:bg-red-50 hover:text-red-600 px-3 py-1.5 rounded-md
                                                           text-[10px] uppercase font-bold tracking-wider transition">
                                                    Tolak
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Tidak ada permohonan tertunda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($approvals->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $approvals->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/approvals-form.js') }}"></script>
        <script>
            // Update countdown "Sisa X mnt" setiap 30 detik tanpa full refresh
            function updateCountdowns() {
                document.querySelectorAll('[data-countdown]').forEach(function (el) {
                    const deadline = new Date(el.getAttribute('data-deadline'));
                    const diffMs   = deadline - new Date();

                    if (diffMs <= 0) {
                        el.textContent = 'Deadline Lewat';
                        el.classList.add('bg-red-100', 'text-red-600');
                        return;
                    }

                    const mins  = Math.floor(diffMs / 60000);
                    const label = mins >= 60 ? (Math.round(mins / 60 * 10) / 10) + ' jam' : mins + ' mnt';
                    el.textContent = 'Sisa ' + label;
                });
            }
            updateCountdowns();
            setInterval(updateCountdowns, 30000);
        </script>
    @endpush

    {{-- MODAL DETAIL --}}
    <div id="detailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div id="detailModalContent" class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
            <button id="closeDetailModal"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>

            <h3 class="text-lg font-bold mb-1">Detail Peminjaman Kendaraan</h3>

            <div id="d-urgent-badge" class="hidden mb-3">
                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 border border-amber-300
                             rounded-full px-3 py-1 text-xs font-bold uppercase">
                    ⚡ URGENT — Approver wajib merespons dalam 30 menit
                </span>
            </div>

            <div class="space-y-2 text-sm text-gray-700">
                <p><span class="font-semibold">Kode Booking:</span>
                   <span id="d-booking-code" class="font-mono text-blue-600 font-bold"></span></p>
                <p><span class="font-semibold">Pemohon:</span> <span id="d-name"></span></p>
                <p><span class="font-semibold">Email:</span> <span id="d-email"></span></p>
                <p><span class="font-semibold">Tujuan:</span> <span id="d-destination"></span></p>
                <p><span class="font-semibold">Keperluan:</span> <span id="d-purpose"></span></p>
                <p><span class="font-semibold">Tgl Request:</span> <span id="d-date"></span></p>
                <p><span class="font-semibold">Waktu Mulai:</span> <span id="d-start"></span></p>
                <p><span class="font-semibold">Waktu Selesai:</span> <span id="d-end"></span></p>
                <p id="d-deadline-row" class="hidden">
                    <span class="font-semibold">Batas Approval:</span>
                    <span id="d-deadline" class="text-amber-600 font-semibold"></span>
                </p>
            </div>
        </div>
    </div>

    {{-- MODAL REJECT --}}
    <div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
            <h3 class="text-lg font-bold mb-4 text-red-600">Alasan Penolakan</h3>
            <textarea id="rejectReason"
                class="w-full border-gray-300 rounded-md text-sm p-2 focus:ring-red-500 focus:border-red-500"
                rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
            <div class="flex justify-end gap-2 mt-4">
                <button id="cancelReject" class="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200">Batal</button>
                <button id="confirmReject" class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">Tolak</button>
            </div>
        </div>
    </div>

    {{-- MODAL CANCEL --}}
    <div id="cancelModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-sm rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold mb-4 text-red-600">Konfirmasi Pembatalan</h3>
            <p class="text-sm text-gray-600 mb-4">Yakin ingin membatalkan pengajuan ini?</p>
            <div class="flex justify-end gap-2">
                <button id="cancelCancel" class="px-4 py-2 bg-gray-100 text-sm rounded-md hover:bg-gray-200">Tidak</button>
                <button id="confirmCancel" class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Ya, Batalkan</button>
            </div>
        </div>
    </div>

    <script>
        const detailModal        = document.getElementById('detailModal');
        const detailModalContent = document.getElementById('detailModalContent');
        const closeDetailModal   = document.getElementById('closeDetailModal');
        const dUrgentBadge       = document.getElementById('d-urgent-badge');
        const dDeadlineRow       = document.getElementById('d-deadline-row');

        function openDetailModal() {
            detailModal.classList.remove('hidden');
            detailModal.classList.add('flex');
        }

        function closeModal() {
            detailModal.classList.add('hidden');
            detailModal.classList.remove('flex');
        }

        closeDetailModal.addEventListener('click', closeModal);
        detailModal.addEventListener('click', function (e) {
            if (!detailModalContent.contains(e.target)) closeModal();
        });

        document.querySelectorAll('.btn-detail').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const data = JSON.parse(this.getAttribute('data-approval'));

                // Badge urgent
                data.is_urgent
                    ? dUrgentBadge.classList.remove('hidden')
                    : dUrgentBadge.classList.add('hidden');

                // Deadline row
                if (data.approval_deadline) {
                    dDeadlineRow.classList.remove('hidden');
                    document.getElementById('d-deadline').textContent =
                        new Date(data.approval_deadline)
                            .toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                } else {
                    dDeadlineRow.classList.add('hidden');
                }

                document.getElementById('d-booking-code').textContent = data.booking_code ?? '—';
                document.getElementById('d-name').textContent         = data.user?.name    ?? '—';
                document.getElementById('d-email').textContent        = data.user?.email   ?? '—';
                document.getElementById('d-destination').textContent  = data.destination   ?? '—';
                document.getElementById('d-purpose').textContent      = data.purpose       ?? '—';
                document.getElementById('d-date').textContent         = data.created_at
                    ? new Date(data.created_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) : '—';
                document.getElementById('d-start').textContent = data.start_time
                    ? new Date(data.start_time).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : '—';
                document.getElementById('d-end').textContent = data.end_time
                    ? new Date(data.end_time).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : '—';

                openDetailModal();
            });
        });
    </script>

</x-app-layout>