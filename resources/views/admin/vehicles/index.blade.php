<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">Kelola Unit</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <button onclick="openModal('modal-tambah')"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition mb-4">
                + Tambah Mobil
            </button>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- FILTER BAR --}}
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <form action="{{ route('admin.vehicles.index') }}" method="GET" class="flex items-center gap-3">
                        <div class="flex-1 max-w-md">
                            <label for="filter-search" class="sr-only">Cari kendaraan</label>
                            <input id="filter-search" type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama kendaraan, plat nomor..." autocomplete="off"
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <label for="filter-type" class="sr-only">Tipe</label>
                        <select id="filter-type" name="type"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 pr-8 focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Tipe</option>
                            @foreach (['MPV', 'SUV', 'Sedan', 'Box', 'LCGC', 'EV'] as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>

                        <label for="filter-status" class="sr-only">Status</label>
                        <select id="filter-status" name="asset_status"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 pr-8 focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('asset_status') == 'available' ? 'selected' : '' }}>
                                Tersedia</option>
                            <option value="maintenance"
                                {{ request('asset_status') == 'maintenance' ? 'selected' : '' }}>Perawatan</option>
                            <option value="disposal" {{ request('asset_status') == 'disposal' ? 'selected' : '' }}>
                                Tidak Aktif</option>
                        </select>

                        <div class="flex gap-2 border-l border-gray-300 pl-3">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                                Cari
                            </button>
                            <a href="{{ route('admin.vehicles.index') }}"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-1.5 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Kendaraan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Plat
                                    Nomor</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Tipe
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Status Aset</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Catatan</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($vehicles as $index => $vehicle)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td
                                        class="px-3 py-3 whitespace-nowrap text-xs text-gray-600 font-medium text-center">
                                        {{ $vehicles->firstItem() + $index }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $vehicle->name }}</div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <span class="text-xs font-mono font-semibold text-blue-600">
                                            {{ $vehicle->license_plate }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-700">{{ $vehicle->type }}</div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        @php
                                            $statusConfig = [
                                                'available' => ['bg-green-400 text-white', 'Tersedia'],
                                                'maintenance' => ['bg-red-400 text-white', 'Perawatan'],
                                                'disposal' => ['bg-gray-400 text-white', 'Tidak Aktif'],
                                            ];
                                            [$badgeClass, $label] = $statusConfig[$vehicle->asset_status] ?? [
                                                'bg-gray-300 text-gray-700',
                                                ucfirst($vehicle->asset_status),
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-500 max-w-xs truncate">
                                        {{ $vehicle->notes ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <button
                                            onclick="openEditModal({{ $vehicle->id }}, '{{ addslashes($vehicle->name) }}', '{{ addslashes($vehicle->license_plate) }}', '{{ $vehicle->type }}', '{{ $vehicle->asset_status }}', '{{ addslashes($vehicle->notes ?? '') }}')"
                                            class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Hapus mobil ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
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
                                        <p class="mt-2 text-sm text-gray-500">Tidak ada kendaraan ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($vehicles->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $vehicles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================================ --}}
    {{-- MODAL TAMBAH MOBIL               --}}
    {{-- ================================ --}}
    <div id="modal-tambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Tambah Mobil Baru</h3>
                <button type="button" onclick="closeModal('modal-tambah')"
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            <form action="{{ route('admin.vehicles.store') }}" method="POST" class="px-6 py-4 space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tambah-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Mobil</label>
                        <input id="tambah-name" name="name" type="text" value="{{ old('name') }}"
                            placeholder="Ex: Avanza Veloz" autocomplete="off" required
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                    </div>
                    <div>
                        <label for="tambah-plate" class="block text-sm font-medium text-gray-700 mb-1">Plat
                            Nomor</label>
                        <input id="tambah-plate" name="license_plate" type="text"
                            value="{{ old('license_plate') }}" placeholder="Ex: B 1234 CD" autocomplete="off"
                            required
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tambah-type" class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select id="tambah-type" name="type"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                            @foreach (['MPV', 'SUV', 'Sedan', 'Box', 'LCGC', 'EV'] as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tambah-status" class="block text-sm font-medium text-gray-700 mb-1">Status
                            Awal</label>
                        <select id="tambah-status" name="asset_status"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                            <option value="available" {{ old('asset_status') == 'available' ? 'selected' : '' }}>
                                Tersedia</option>
                            <option value="maintenance" {{ old('asset_status') == 'maintenance' ? 'selected' : '' }}>
                                Perawatan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="tambah-notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan Kondisi <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="tambah-notes" name="notes" rows="2" autocomplete="off"
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-tambah')"
                        class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <x-primary-button>Simpan Mobil</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================ --}}
    {{-- MODAL EDIT MOBIL                 --}}
    {{-- ================================ --}}
    <div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Edit Kendaraan</h3>
                <button type="button" onclick="closeModal('modal-edit')"
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            <form id="form-edit" action="" method="POST" class="px-6 py-4 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Mobil</label>
                        <input id="edit-name" name="name" type="text" autocomplete="off" required
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                    </div>
                    <div>
                        <label for="edit-license-plate" class="block text-sm font-medium text-gray-700 mb-1">Plat
                            Nomor</label>
                        <input id="edit-license-plate" name="license_plate" type="text" autocomplete="off"
                            required
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit-type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Mobil</label>
                        <select id="edit-type" name="type"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                            @foreach (['MPV', 'SUV', 'Sedan', 'Box', 'LCGC', 'EV'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="edit-asset-status" class="block text-sm font-medium text-gray-700 mb-1">Status
                            Aset</label>
                        <select id="edit-asset-status" name="asset_status"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                            <option value="available">Tersedia</option>
                            <option value="maintenance">Perawatan</option>
                            <option value="disposal">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="edit-notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan Kondisi <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="edit-notes" name="notes" rows="3" autocomplete="off"
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-edit')"
                        class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <x-primary-button>Update Mobil</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openEditModal(id, name, licensePlate, type, assetStatus, notes) {
            document.getElementById('form-edit').action = `/admin/vehicles/${id}`;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-license-plate').value = licensePlate;
            document.getElementById('edit-notes').value = notes;

            const typeSelect = document.getElementById('edit-type');
            for (let opt of typeSelect.options) opt.selected = opt.value === type;

            const statusSelect = document.getElementById('edit-asset-status');
            for (let opt of statusSelect.options) opt.selected = opt.value === assetStatus;

            openModal('modal-edit');
        }

        // Tutup modal klik backdrop
        document.querySelectorAll('[id^="modal-"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        @if ($errors->any() && old('_method') === null)
            openModal('modal-tambah');
        @endif

        @if ($errors->any() && old('_method') === 'PUT')
            openModal('modal-edit');
        @endif
    </script>

</x-app-layout>
