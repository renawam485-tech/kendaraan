<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Kelola Persiapan
            </h2>
        </div>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('css/dispatcher-form.css') }}">

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 space-y-2">

            {{-- Search Form --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <form method="GET" action="{{ route('admin.dispatch') }}" class="flex flex-col sm:flex-row gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Cari kode booking, nama, tujuan, atau keperluan..."
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    >
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Cari</button>
                        @if($search)
                            <a href="{{ route('admin.dispatch') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Task List --}}
            @forelse ($tasks as $task)
                <div x-data="{
                    isOpen: false,
                    mode: '{{ !empty($task->preferred_vehicle_type) ? 'external' : 'internal' }}',
                    openVehicle: false,
                    openDriver: false,
                    searchVehicle: '',
                    searchDriver: '',
                    selectedVehicle: {{ $task->vehicle_id ? $vehicles->firstWhere('id', $task->vehicle_id) : 'null' }},
                    selectedDriver: null,
                    vehicles: {{ $vehicles->toJson() }},
                    drivers: {{ $drivers->toJson() }},
                    get filteredVehicles() {
                        return this.vehicles.filter(v =>
                            v.name.toLowerCase().includes(this.searchVehicle.toLowerCase()) ||
                            v.license_plate.toLowerCase().includes(this.searchVehicle.toLowerCase())
                        );
                    },
                    get filteredDrivers() {
                        return this.drivers.filter(d =>
                            d.name.toLowerCase().includes(this.searchDriver.toLowerCase())
                        );
                    },
                    closeDrawer() {
                        this.isOpen = false;
                        this.openVehicle = false;
                        this.openDriver = false;
                    }
                }"
                x-effect="isOpen ? $dispatch('drawer-opened') : $dispatch('drawer-closed')"
            >

                    {{-- Slim Card --}}
                    <div
                        class="task-card"
                        @click="isOpen = true"
                        role="button"
                        tabindex="0"
                        @keydown.enter="isOpen = true"
                    >
                        <div class="task-num">{{ $loop->iteration }}</div>

                        <div class="task-booking">
                            <span class="booking-code">{{ $task->booking_code }}</span>
                        </div>

                        <div class="task-person">
                            <div class="person-name">{{ $task->user->name }}</div>
                            <div class="person-email">{{ $task->user->email }}</div>
                        </div>

                        <div class="task-schedule">
                            <div class="schedule-item">
                                <span class="schedule-label">Berangkat</span>
                                <span class="schedule-value">{{ $task->start_time->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="schedule-item">
                                <span class="schedule-label">Kembali</span>
                                <span class="schedule-value">{{ $task->end_time->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="task-arrow">›</div>
                    </div>

                    {{-- Overlay --}}
                    <div
                        x-show="isOpen"
                        x-cloak
                        class="drawer-overlay"
                        @click="closeDrawer()"
                        x-transition:enter="overlay-enter"
                        x-transition:enter-start="overlay-from"
                        x-transition:enter-end="overlay-to"
                        x-transition:leave="overlay-leave"
                        x-transition:leave-start="overlay-to"
                        x-transition:leave-end="overlay-from"
                    ></div>

                    {{-- Side Drawer --}}
                    <div
                        x-show="isOpen"
                        x-cloak
                        class="drawer-panel"
                        x-transition:enter="drawer-enter"
                        x-transition:enter-start="drawer-from"
                        x-transition:enter-end="drawer-to"
                        x-transition:leave="drawer-leave"
                        x-transition:leave-start="drawer-to"
                        x-transition:leave-end="drawer-from"
                        @keydown.escape.window="closeDrawer()"
                    >
                        {{-- Drawer Header --}}
                        <div class="drawer-header">
                            <div>
                                <div class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Detail Permintaan</div>
                                <div class="font-mono font-bold text-blue-600 text-sm">{{ $task->booking_code }}</div>
                            </div>
                            <button @click="closeDrawer()" class="drawer-close" aria-label="Tutup">✕</button>
                        </div>

                        {{-- Drawer Body --}}
                        <div class="drawer-body">

                            {{-- Pemohon --}}
                            <div class="drawer-section">
                                <div class="drawer-section-title">Pemohon</div>
                                <div class="text-sm font-semibold text-gray-800">{{ $task->user->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $task->user->email }}</div>
                            </div>

                            {{-- Jadwal --}}
                            <div class="drawer-section">
                                <div class="drawer-section-title">Jadwal Penggunaan</div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="schedule-box">
                                        <div class="schedule-box-label">Berangkat</div>
                                        <div class="schedule-box-value">{{ $task->start_time->format('d/m/Y') }}</div>
                                        <div class="schedule-box-time">{{ $task->start_time->format('H:i') }}</div>
                                    </div>
                                    <div class="schedule-box">
                                        <div class="schedule-box-label">Kembali</div>
                                        <div class="schedule-box-value">{{ $task->end_time->format('d/m/Y') }}</div>
                                        <div class="schedule-box-time">{{ $task->end_time->format('H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Detail Perjalanan --}}
                            <div class="drawer-section">
                                <div class="drawer-section-title">Detail Perjalanan</div>
                                <div class="text-sm font-semibold text-gray-800 mb-2">{{ $task->destination }}</div>

                                <div class="flex flex-wrap gap-1.5 mb-3">
                                    @if (!empty($task->preferred_vehicle_type))
                                        <span class="badge badge-rental">Req Sewa: <strong>{{ $task->preferred_vehicle_type }}</strong></span>
                                    @elseif($task->vehicle_id)
                                        <span class="badge badge-vehicle">Mobil: <strong>{{ $task->vehicle->name }}</strong></span>
                                    @else
                                        <span class="badge badge-neutral">Bantu Carikan</span>
                                    @endif

                                    @if (!empty($task->passenger_count) && $task->passenger_count > 0)
                                        <span class="badge badge-count">{{ $task->passenger_count }} Orang</span>
                                    @endif

                                    <span class="badge badge-neutral">
                                        {{ $task->with_driver ? 'Dengan Driver' : 'Lepas Kunci' }}
                                    </span>
                                </div>

                                @if ($task->purpose)
                                    <div class="text-xs bg-gray-50 border border-gray-200 rounded p-2.5 text-gray-600 leading-relaxed">
                                        <span class="font-semibold text-gray-700">Keperluan: </span>{{ $task->purpose }}
                                    </div>
                                @endif
                            </div>

                            {{-- Form Assign --}}
                            <div class="drawer-section">
                                <div class="flex justify-between items-center mb-3">
                                    <span
                                        class="mode-badge"
                                        :class="mode === 'internal' ? 'mode-internal' : 'mode-external'"
                                        x-text="mode === 'internal' ? 'Mobil Dinas' : 'Sewa Luar'"
                                    ></span>
                                    <button
                                        type="button"
                                        class="text-xs underline text-blue-600 hover:text-blue-800"
                                        @click="mode = (mode === 'internal' ? 'external' : 'internal'); selectedVehicle = null; selectedDriver = null;"
                                    >
                                        Ganti Mode
                                    </button>
                                </div>

                                {{-- Form Internal --}}
                                <form x-show="mode === 'internal'" method="POST" action="{{ route('admin.assign.internal', $task->id) }}">
                                    @csrf
                                    <input type="hidden" name="vehicle_id" :value="selectedVehicle ? selectedVehicle.id : ''">
                                    <input type="hidden" name="driver_id" :value="selectedDriver ? selectedDriver.id : ''">

                                    <div class="relative-wrapper mb-2">
                                        <button type="button" class="dropdown-trigger w-full"
                                            @click="openVehicle = !openVehicle; openDriver = false">
                                            <span x-text="selectedVehicle ? selectedVehicle.name + ' [' + selectedVehicle.license_plate + ']' : 'Pilih Mobil Dinas...'"></span>
                                        </button>
                                        <div x-show="openVehicle" x-cloak class="dropdown-panel" @click.outside="openVehicle = false">
                                            <input type="text"
                                                id="search-vehicle-{{ $task->id }}"
                                                name="search_vehicle_{{ $task->id }}"
                                                x-model="searchVehicle"
                                                class="dropdown-search"
                                                placeholder="Cari mobil..."
                                                @click.stop
                                                autocomplete="off">
                                            <div class="dropdown-list">
                                                <template x-for="v in filteredVehicles" :key="v.id">
                                                    <div class="dropdown-item" @click="selectedVehicle = v; openVehicle = false">
                                                        <div class="font-bold text-xs" x-text="v.name"></div>
                                                        <div class="text-xs text-gray-500" x-text="v.license_plate"></div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="relative-wrapper mb-3">
                                        <button type="button" class="dropdown-trigger w-full"
                                            @click="openDriver = !openDriver; openVehicle = false">
                                            <span x-text="selectedDriver ? selectedDriver.name : 'Pilih Driver (Opsional)'"></span>
                                        </button>
                                        <div x-show="openDriver" x-cloak class="dropdown-panel" @click.outside="openDriver = false">
                                            <input type="text"
                                                id="search-driver-internal-{{ $task->id }}"
                                                name="search_driver_internal_{{ $task->id }}"
                                                x-model="searchDriver"
                                                class="dropdown-search"
                                                placeholder="Cari driver..."
                                                @click.stop
                                                autocomplete="off">
                                            <div class="dropdown-list">
                                                <div class="dropdown-item italic text-gray-400"
                                                    @click="selectedDriver = null; openDriver = false">-- Tanpa Driver --</div>
                                                <template x-for="d in filteredDrivers" :key="d.id">
                                                    <div class="dropdown-item" @click="selectedDriver = d; openDriver = false">
                                                        <span x-text="d.name"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn-primary w-full" :disabled="!selectedVehicle">
                                        Siapkan Unit
                                    </button>
                                </form>

                                {{-- Form External --}}
                                <form x-show="mode === 'external'" x-cloak method="POST" action="{{ route('admin.assign.external', $task->id) }}">
                                    @csrf
                                    <input type="hidden" name="driver_id" :value="selectedDriver ? selectedDriver.id : ''">
                                    <div class="space-y-2">
                                        <input type="text" name="vendor_name"
                                            placeholder="Nama Vendor (cth: TRAC / Grab)"
                                            class="input w-full" autocomplete="organization" required>
                                        <input type="text" name="external_vehicle_detail"
                                            placeholder="Detail Unit (cth: Avanza B 1234 ABC)"
                                            class="input w-full" autocomplete="off" required>

                                        <div class="relative-wrapper">
                                            <button type="button" class="dropdown-trigger w-full"
                                                @click="openDriver = !openDriver">
                                                <span x-text="selectedDriver ? selectedDriver.name : 'Pilih Driver (Opsional)'"></span>
                                            </button>
                                            <div x-show="openDriver" x-cloak class="dropdown-panel" @click.outside="openDriver = false">
                                                <input type="text"
                                                    id="search-driver-external-{{ $task->id }}"
                                                    name="search_driver_external_{{ $task->id }}"
                                                    x-model="searchDriver"
                                                    class="dropdown-search"
                                                    placeholder="Cari driver..."
                                                    @click.stop
                                                    autocomplete="off">
                                                <div class="dropdown-list">
                                                    <div class="dropdown-item italic text-gray-400"
                                                        @click="selectedDriver = null; openDriver = false">-- Tanpa Driver --</div>
                                                    <template x-for="d in filteredDrivers" :key="d.id">
                                                        <div class="dropdown-item" @click="selectedDriver = d; openDriver = false">
                                                            <span x-text="d.name"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn-external w-full">
                                            Siapkan Unit Sewa
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>{{-- /drawer-body --}}
                    </div>{{-- /drawer-panel --}}

                </div>{{-- /x-data --}}
            @empty
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 border-b border-gray-200 px-4 py-2">
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Status Antrian</span>
                    </div>
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        @if($search ?? false)
                            <p class="text-base font-semibold text-gray-700">Tidak ada hasil untuk "{{ $search }}"</p>
                            <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain atau <a href="{{ route('admin.dispatch') }}" class="text-blue-600 underline">lihat semua permintaan</a>.</p>
                        @else
                            <p class="text-base font-semibold text-gray-700">Semua Permintaan Sudah Diproses</p>
                            <p class="text-sm text-gray-400 mt-1">Tidak ada permintaan yang menunggu penugasan unit.</p>
                        @endif
                    </div>
                </div>
            @endforelse

        </div>
    </div>

    <script src="{{ asset('js/dispatcher-form.js') }}"></script>
</x-app-layout>