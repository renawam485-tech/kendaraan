<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/booking-form.css') }}">

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Form Peminjaman Kendaraan</h2>
    </x-slot>

    <div class="form-wrapper">
        <form
            action="{{ route('booking.store') }}"
            method="POST"
            class="minimal-form"
            x-data="bookingForm({
                vehicles  : {{ $vehicles->toJson() }},
                schedules : {{ json_encode($schedules) }}
            })"
            @submit.prevent="handleSubmit($el)"
        >
            @csrf

            {{-- ── HEADER ── --}}
            <div class="form-header">
                <h1>Pemesanan Kendaraan</h1>
                <p>Silakan isi detail peminjaman armada di bawah ini.</p>
            </div>

            {{-- ── SERVER CONFLICT ALERT ── --}}
            <div x-show="serverConflict" x-transition class="alert-danger" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" aria-hidden="true" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p x-text="serverConflictMsg"></p>
            </div>

            {{-- Laravel validation errors --}}
            @if($errors->any())
            <div class="alert-danger mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" aria-hidden="true" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>
                    @foreach($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ══════════════════════════════════════════
                 BAGIAN 1: PILIHAN ARMADA
                 FIX: pakai <fieldset> + <legend> untuk radio group
            ══════════════════════════════════════════ --}}
            <div class="form-group">
                <fieldset class="border-0 p-0 m-0">
                    <legend class="label-title mb-2">Pilihan Armada</legend>
                    <div class="radio-stack">
                        <label class="radio-box" for="mode_self">
                            <input type="radio" id="mode_self" name="booking_mode" value="self" x-model="mode">
                            <div class="radio-info">
                                <span class="font-bold">Pilih Mobil Kampus</span>
                                <p>Pilih unit dari daftar kendaraan kampus.</p>
                            </div>
                        </label>
                        <label class="radio-box" for="mode_dispatch">
                            <input type="radio" id="mode_dispatch" name="booking_mode" value="dispatch" x-model="mode">
                            <div class="radio-info">
                                <span class="font-bold">Sewa Luar</span>
                                <p>Admin akan mengatur unit sesuai kebutuhan Anda.</p>
                            </div>
                        </label>
                    </div>
                </fieldset>
            </div>

            {{-- ── Pilih Mobil Kampus ── --}}
            <div class="form-group" x-show="mode === 'self'" x-cloak>
                {{-- FIX: label pakai for="vehicle_trigger", button pakai id --}}
                <label class="label-title" for="vehicle_trigger">Pilih Unit Kampus</label>

                <div class="search-select" @click.away="vehicleOpen = false">
                    <input type="hidden" name="vehicle_id" :value="selectedId">

                    <button
                        type="button"
                        id="vehicle_trigger"
                        aria-haspopup="listbox"
                        :aria-expanded="vehicleOpen"
                        aria-controls="vehicle_listbox"
                        @click="vehicleOpen = !vehicleOpen"
                        class="select-trigger">
                        <span x-text="selectedLabel || '— Cari & Pilih Kendaraan —'"></span>
                        <svg class="icon-sm" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <div class="select-dropdown" x-show="vehicleOpen" id="vehicle_listbox" role="listbox">
                        {{-- FIX: tambah id + name + aria-label pada search input --}}
                        <label for="vehicle_search" class="sr-only">Cari kendaraan</label>
                        <input
                            type="text"
                            id="vehicle_search"
                            name="vehicle_search"
                            autocomplete="off"
                            x-model="search"
                            x-ref="searchInput"
                            class="search-field"
                            placeholder="Ketik nama atau plat nomor...">
                        <ul class="options-list" role="listbox" aria-label="Daftar kendaraan">
                            <template x-for="v in filteredVehicles" :key="v.id">
                                <li role="option" :aria-selected="selectedId === String(v.id)"
                                    @click="selectVehicle(v)"
                                    x-text="v.name + ' — ' + v.license_plate"></li>
                            </template>
                            <li x-show="filteredVehicles.length === 0" role="option" aria-disabled="true"
                                class="text-center text-gray-400 text-sm py-3 cursor-default">
                                Tidak ditemukan
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Jadwal existing --}}
                <div x-show="selectedId" class="warning-box mt-3" x-transition role="status" aria-live="polite">

                    {{-- Ada jadwal → tampilkan list --}}
                    <template x-if="selectedSchedules.length > 0">
                        <div>
                            <p class="font-bold text-red-700 mb-1 text-sm">⚠ Jadwal Terisi (sudah disetujui):</p>
                            <ul class="text-red-600 text-sm pl-4 list-disc space-y-0.5">
                                <template x-for="(b, i) in selectedSchedules" :key="i">
                                    <li x-text="formatDate(b.start) + ' s/d ' + formatDate(b.end)"></li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    {{-- Tidak ada jadwal → tampilkan info hijau --}}
                    <template x-if="selectedSchedules.length === 0">
                        <p class="text-green-600 text-sm font-medium">✓ Unit ini belum memiliki jadwal aktif.</p>
                    </template>

                    {{-- Konflik waktu lokal --}}
                    <div x-show="hasLocalConflict()" class="mt-2 pt-2 border-t border-red-200" role="alert">
                        <p class="font-bold text-red-800 text-sm bg-red-100 p-2 rounded">
                            🚫 Waktu yang Anda pilih <strong>BENTROK</strong> dengan jadwal di atas!
                            Silakan ubah waktu atau pilih kendaraan lain.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Sewa Luar ── --}}
            <div class="form-group" x-show="mode === 'dispatch'" x-cloak>
                <div class="checkbox-item bg-orange-50">
                    <label class="checkbox-container" for="is_rental">
                        <input type="checkbox" id="is_rental" name="is_rental" value="1"
                            x-model="isRental" class="custom-check">
                        <div class="checkbox-text">
                            <span class="font-bold">Request Sewa Luar Spesifik?</span>
                            <p>Centang jika memerlukan jenis armada tertentu.</p>
                        </div>
                    </label>
                </div>

                <div class="form-group mt-4" x-show="isRental" x-transition>
                    <label class="label-title" for="preferred_vehicle_type">Jenis Mobil Rental</label>
                    <input type="text" id="preferred_vehicle_type" name="preferred_vehicle_type"
                        class="text-field" placeholder="cth. Hiace, Elf, Innova...">
                </div>

                <div class="form-group mt-4">
                    <label class="label-title" for="passenger_count">Jumlah Penumpang</label>
                    <input type="number" id="passenger_count" name="passenger_count"
                        class="text-field" min="1" placeholder="0">
                </div>
            </div>

            <div class="form-divider"></div>

            {{-- ══════════════════════════════════════════
                 BAGIAN 2: CUSTOM DATETIME PICKERS
                 FIX: label pakai id, trigger button pakai aria-labelledby
                 (label tidak bisa pakai for= ke button, gunakan aria-labelledby)
            ══════════════════════════════════════════ --}}

            {{-- ── WAKTU MULAI ── --}}
            <div class="form-group">
                <span id="label_start_time" class="label-title">
                    Waktu Mulai <span class="text-red-500" aria-hidden="true">*</span>
                </span>

                <input type="hidden" name="start_time" id="start_time" :value="startPicker.raw">

                <div class="dt-wrapper" @click.away="startPicker.open = false">
                    <button
                        type="button"
                        id="start_time_trigger"
                        aria-labelledby="label_start_time"
                        aria-haspopup="dialog"
                        :aria-expanded="startPicker.open"
                        class="dt-trigger"
                        :class="{ 'dt-trigger--filled': startPicker.raw, 'dt-trigger--active': startPicker.open }"
                        @click="startPicker.open = !startPicker.open">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" aria-hidden="true" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>

                        <span class="dt-trigger-text"
                            x-text="startPicker.display() || 'Pilih tanggal & jam mulai'"></span>

                        <button x-show="startPicker.raw" type="button" class="dt-clear-btn"
                            aria-label="Hapus waktu mulai"
                            @click.stop="startPicker.clear(); endPicker.clear()">✕</button>
                    </button>

                    <div class="dt-panel" x-show="startPicker.open" x-transition.origin.top.left
                        role="dialog" aria-labelledby="label_start_time" aria-modal="false">

                        {{-- Calendar view --}}
                        <div x-show="startPicker.view === 'cal'">
                            <div class="dt-cal-header">
                                <button type="button" class="dt-nav-btn" aria-label="Bulan sebelumnya"
                                    @click="startPicker.prevMonth()">‹</button>
                                <span x-text="startPicker.monthLabel()" aria-live="polite"></span>
                                <button type="button" class="dt-nav-btn" aria-label="Bulan berikutnya"
                                    @click="startPicker.nextMonth()">›</button>
                            </div>

                            <div class="dt-dow-row" aria-hidden="true">
                                <template x-for="d in ['Sen','Sel','Rab','Kam','Jum','Sab','Min']">
                                    <div class="dt-dow" x-text="d"></div>
                                </template>
                            </div>

                            <div class="dt-days-grid" role="grid" aria-label="Kalender pilih tanggal mulai">
                                <template x-for="(day, i) in startPicker.calDays()" :key="i">
                                    <button
                                        type="button"
                                        role="gridcell"
                                        class="dt-day-btn"
                                        :aria-label="day ? day + ' ' + startPicker.monthLabel() : ''"
                                        :aria-selected="startPicker.isSelected(day)"
                                        :aria-disabled="startPicker.isDisabled(day, minNow)"
                                        :class="{
                                            'dt-day--empty'   : !day,
                                            'dt-day--today'   : startPicker.isToday(day),
                                            'dt-day--selected': startPicker.isSelected(day),
                                            'dt-day--disabled': startPicker.isDisabled(day, minNow)
                                        }"
                                        :disabled="startPicker.isDisabled(day, minNow)"
                                        @click="startPicker.clickDay(day, minNow)"
                                        x-text="day ?? ''">
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Time view --}}
                        <div x-show="startPicker.view === 'time'" class="dt-time-panel">
                            <p class="dt-time-label" id="start_time_picker_label">Pilih Jam Mulai</p>

                            <div class="dt-time-row" role="group" aria-labelledby="start_time_picker_label">
                                <div class="dt-stepper">
                                    <button type="button" class="dt-step-btn" aria-label="Tambah jam"
                                        @click="startPicker.adjH(1)">▲</button>
                                    <span class="dt-time-val" aria-live="polite" aria-label="Jam"
                                        x-text="String(startPicker.h).padStart(2,'0')"></span>
                                    <button type="button" class="dt-step-btn" aria-label="Kurang jam"
                                        @click="startPicker.adjH(-1)">▼</button>
                                </div>
                                <span class="dt-colon" aria-hidden="true">:</span>
                                <div class="dt-stepper">
                                    <button type="button" class="dt-step-btn" aria-label="Tambah menit"
                                        @click="startPicker.adjM(1)">▲</button>
                                    <span class="dt-time-val" aria-live="polite" aria-label="Menit"
                                        x-text="String(startPicker.m).padStart(2,'0')"></span>
                                    <button type="button" class="dt-step-btn" aria-label="Kurang menit"
                                        @click="startPicker.adjM(-1)">▼</button>
                                </div>
                            </div>
                            <p class="dt-time-hint">Menit tersedia: 00, 15, 30, 45</p>

                            <div class="dt-time-actions">
                                <button type="button" class="dt-btn-back"
                                    @click="startPicker.view = 'cal'">← Kalender</button>
                                <button type="button" class="dt-btn-confirm"
                                    @click="startPicker.confirm()">Konfirmasi</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── WAKTU SELESAI ── --}}
            <div class="form-group">
                <span id="label_end_time" class="label-title">
                    Waktu Selesai <span class="text-red-500" aria-hidden="true">*</span>
                </span>

                <input type="hidden" name="end_time" id="end_time" :value="endPicker.raw">

                <div class="dt-wrapper" @click.away="endPicker.open = false">
                    <button
                        type="button"
                        id="end_time_trigger"
                        aria-labelledby="label_end_time"
                        aria-haspopup="dialog"
                        :aria-expanded="endPicker.open"
                        class="dt-trigger"
                        :class="{ 'dt-trigger--filled': endPicker.raw, 'dt-trigger--active': endPicker.open }"
                        @click="endPicker.open = !endPicker.open">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" aria-hidden="true" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>

                        <span class="dt-trigger-text"
                            x-text="endPicker.display() || 'Pilih tanggal & jam selesai'"></span>

                        <button x-show="endPicker.raw" type="button" class="dt-clear-btn"
                            aria-label="Hapus waktu selesai"
                            @click.stop="endPicker.clear()">✕</button>
                    </button>

                    <div class="dt-panel dt-panel--right" x-show="endPicker.open" x-transition.origin.top.right
                        role="dialog" aria-labelledby="label_end_time" aria-modal="false">

                        {{-- Calendar view --}}
                        <div x-show="endPicker.view === 'cal'">
                            <div class="dt-cal-header">
                                <button type="button" class="dt-nav-btn" aria-label="Bulan sebelumnya"
                                    @click="endPicker.prevMonth()">‹</button>
                                <span x-text="endPicker.monthLabel()" aria-live="polite"></span>
                                <button type="button" class="dt-nav-btn" aria-label="Bulan berikutnya"
                                    @click="endPicker.nextMonth()">›</button>
                            </div>

                            <div class="dt-dow-row" aria-hidden="true">
                                <template x-for="d in ['Sen','Sel','Rab','Kam','Jum','Sab','Min']">
                                    <div class="dt-dow" x-text="d"></div>
                                </template>
                            </div>

                            <div class="dt-days-grid" role="grid" aria-label="Kalender pilih tanggal selesai">
                                <template x-for="(day, i) in endPicker.calDays()" :key="i">
                                    <button
                                        type="button"
                                        role="gridcell"
                                        class="dt-day-btn"
                                        :aria-label="day ? day + ' ' + endPicker.monthLabel() : ''"
                                        :aria-selected="endPicker.isSelected(day)"
                                        :aria-disabled="endPicker.isDisabled(day, startPicker.raw || minNow)"
                                        :class="{
                                            'dt-day--empty'   : !day,
                                            'dt-day--today'   : endPicker.isToday(day),
                                            'dt-day--selected': endPicker.isSelected(day),
                                            'dt-day--disabled': endPicker.isDisabled(day, startPicker.raw || minNow)
                                        }"
                                        :disabled="endPicker.isDisabled(day, startPicker.raw || minNow)"
                                        @click="endPicker.clickDay(day, startPicker.raw || minNow)"
                                        x-text="day ?? ''">
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Time view --}}
                        <div x-show="endPicker.view === 'time'" class="dt-time-panel">
                            <p class="dt-time-label" id="end_time_picker_label">Pilih Jam Selesai</p>

                            <div class="dt-time-row" role="group" aria-labelledby="end_time_picker_label">
                                <div class="dt-stepper">
                                    <button type="button" class="dt-step-btn" aria-label="Tambah jam"
                                        @click="endPicker.adjH(1)">▲</button>
                                    <span class="dt-time-val" aria-live="polite" aria-label="Jam"
                                        x-text="String(endPicker.h).padStart(2,'0')"></span>
                                    <button type="button" class="dt-step-btn" aria-label="Kurang jam"
                                        @click="endPicker.adjH(-1)">▼</button>
                                </div>
                                <span class="dt-colon" aria-hidden="true">:</span>
                                <div class="dt-stepper">
                                    <button type="button" class="dt-step-btn" aria-label="Tambah menit"
                                        @click="endPicker.adjM(1)">▲</button>
                                    <span class="dt-time-val" aria-live="polite" aria-label="Menit"
                                        x-text="String(endPicker.m).padStart(2,'0')"></span>
                                    <button type="button" class="dt-step-btn" aria-label="Kurang menit"
                                        @click="endPicker.adjM(-1)">▼</button>
                                </div>
                            </div>
                            <p class="dt-time-hint">Menit tersedia: 00, 15, 30, 45</p>

                            <div class="dt-time-actions">
                                <button type="button" class="dt-btn-back"
                                    @click="endPicker.view = 'cal'">← Kalender</button>
                                <button type="button" class="dt-btn-confirm"
                                    @click="endPicker.confirm()">Konfirmasi</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="endIsBeforeStart" class="dt-hint dt-hint--error" x-transition role="alert">
                    ⛔ Waktu selesai tidak boleh sebelum atau sama dengan waktu mulai.
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 BAGIAN 3: DETAIL PERJALANAN
            ══════════════════════════════════════════ --}}
            <div class="form-group">
                <label class="label-title" for="destination">
                    Tujuan <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text" id="destination" name="destination" class="text-field"
                    placeholder="cth. Kantor Pusat, Kampus A..." required>
            </div>

            <div class="form-group">
                <label class="label-title" for="purpose">
                    Keperluan <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <textarea id="purpose" name="purpose" class="text-field" rows="3"
                    placeholder="Jelaskan keperluan perjalanan Anda..." required></textarea>
            </div>

            <div class="form-group">
                <div class="checkbox-item bg-gray-50 border-gray-200">
                    <label class="checkbox-container" for="with_driver">
                        <input type="checkbox" id="with_driver" name="with_driver"
                            value="1" class="custom-check">
                        <div class="checkbox-text">
                            <span class="font-medium text-gray-800">Sertakan Pengemudi</span>
                            <p>Centang jika memerlukan bantuan pengemudi.</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- ── SUBMIT ── --}}
            <button type="submit" id="btn_submit" class="submit-btn" :disabled="!canSubmit">
                <span x-show="submitting">
                    <span class="spinner" aria-hidden="true"></span>
                    Memeriksa ketersediaan...
                </span>
                <span x-show="!submitting">Ajukan Peminjaman</span>
            </button>

        </form>
    </div>

    <script src="{{ asset('js/booking-form.js') }}"></script>
</x-app-layout>