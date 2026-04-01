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

                        {{-- Search Input --}}
                        <div class="flex-1 max-w-md">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari kode booking atau pemohon..."
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Tanggal Request --}}
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">

                        {{-- Action Buttons --}}
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

                {{-- COMPACT TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Kode</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Tanggal Request</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Pemohon</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Tujuan & Keperluan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($approvals as $index => $approval)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-600 font-medium text-center">
                                        {{ $approvals->firstItem() + $index }}
                                    </td>

                                    {{-- Kode Booking --}}
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <span class="text-xs font-mono font-semibold text-blue-600">
                                            {{ $approval->booking_code }}
                                        </span>
                                    </td>

                                    {{-- Tanggal Request --}}
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-700">
                                            {{ $approval->created_at->format('d M Y') }}
                                        </div>
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
                                    </td>

                                    {{-- Tindakan --}}
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <div class="flex justify-center items-center gap-2">

                                            {{-- DETAIL --}}
                                            <button type="button" data-approval='@json($approval)'
                                                class="btn-detail bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md text-[10px] uppercase font-bold tracking-wider transition">
                                                Detail
                                            </button>

                                            {{-- APPROVE --}}
                                            <form action="{{ route('approvals.decide', $approval) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-[10px] uppercase font-bold tracking-wider transition">
                                                    Setujui
                                                </button>
                                            </form>

                                            {{-- REJECT --}}
                                            <form action="{{ route('approvals.decide', $approval) }}" method="POST"
                                                class="btn-reject-form">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <input type="hidden" name="comment" class="reject-reason-input">
                                                <button type="button"
                                                    class="btn-reject-trigger border border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-600 px-3 py-1.5 rounded-md text-[10px] uppercase font-bold tracking-wider transition">
                                                    Tolak
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-12 text-center">
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

                {{-- Pagination --}}
                @if($approvals->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $approvals->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/approvals-form.js') }}"></script>
    @endpush

    {{-- MODAL DETAIL --}}
    <div id="detailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div id="detailModalContent" class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">

            <button id="closeDetailModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold">
                &times;
            </button>

            <h3 class="text-lg font-bold mb-4">Detail Peminjaman Kendaraan</h3>

            <div class="space-y-2 text-sm text-gray-700">
                <p><span class="font-semibold">Kode Booking:</span>
                    <span id="d-booking-code" class="font-mono text-blue-600 font-bold"></span></p>
                <p><span class="font-semibold">Pemohon:</span> <span id="d-name"></span></p>
                <p><span class="font-semibold">Email:</span> <span id="d-email"></span></p>
                <p><span class="font-semibold">Tujuan:</span> <span id="d-destination"></span></p>
                <p><span class="font-semibold">Keperluan:</span> <span id="d-purpose"></span></p>
                <p><span class="font-semibold">Tanggal Request:</span> <span id="d-date"></span></p>
                <p><span class="font-semibold">Waktu Mulai:</span> <span id="d-start"></span></p>
                <p><span class="font-semibold">Waktu Selesai:</span> <span id="d-end"></span></p>
            </div>
        </div>
    </div>

    {{-- MODAL REJECT --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">

        <h3 class="text-lg font-bold mb-4 text-red-600">Alasan Penolakan</h3>

        <textarea id="rejectReason"
            class="w-full border-gray-300 rounded-md text-sm p-2 focus:ring-red-500 focus:border-red-500"
            rows="3"
            placeholder="Masukkan alasan penolakan..."></textarea>

        <div class="flex justify-end gap-2 mt-4">
            <button id="cancelReject"
                class="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200">
                Batal
            </button>

            <button id="confirmReject"
                class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">
                Tolak
            </button>
        </div>
    </div>
</div>


    {{-- MODAL CANCEL --}}
<div id="cancelModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-sm rounded-lg shadow-lg p-6">

        <h3 class="text-lg font-bold mb-4 text-red-600">
            Konfirmasi Pembatalan
        </h3>

        <p class="text-sm text-gray-600 mb-4">
            Yakin ingin membatalkan pengajuan ini?
        </p>

        <div class="flex justify-end gap-2">
            <button id="cancelCancel"
                class="px-4 py-2 bg-gray-100 text-sm rounded-md hover:bg-gray-200">
                Tidak
            </button>

            <button id="confirmCancel"
                class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                Ya, Batalkan
            </button>
        </div>
    </div>
</div>

    {{-- SCRIPT MODAL CLOSE ON BACKDROP CLICK --}}
    <script>
        const detailModal = document.getElementById('detailModal');
        const detailModalContent = document.getElementById('detailModalContent');
        const closeDetailModal = document.getElementById('closeDetailModal');

        // Tutup modal ketika klik tombol X
        closeDetailModal.addEventListener('click', () => {
            detailModal.classList.add('hidden');
            detailModal.classList.remove('flex');
        });

        // Tutup modal ketika klik di luar area modal (backdrop)
        detailModal.addEventListener('click', (e) => {
            if (!detailModalContent.contains(e.target)) {
                detailModal.classList.add('hidden');
                detailModal.classList.remove('flex');
            }
        });
    </script>

</x-app-layout>