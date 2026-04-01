'use strict';

/* ================================================================
   Custom datetime picker state factory
   ================================================================ */
function createPickerState() {
    const MONTHS_LONG  = ['Januari','Februari','Maret','April','Mei','Juni',
                          'Juli','Agustus','September','Oktober','November','Desember'];
    const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','Mei','Jun',
                          'Jul','Agu','Sep','Okt','Nov','Des'];

    return {
        open : false,
        view : 'cal',
        vy   : new Date().getFullYear(),
        vm   : new Date().getMonth(),
        sd   : null, sm: null, sy: null,
        h    : 8,
        m    : 0,
        raw  : '',

        display() {
            if (!this.raw) return '';
            const [datePart, timePart] = this.raw.split('T');
            const [y, mo, d] = datePart.split('-');
            return `${parseInt(d)} ${MONTHS_SHORT[parseInt(mo) - 1]} ${y}  ·  ${timePart}`;
        },

        monthLabel() {
            return `${MONTHS_LONG[this.vm]} ${this.vy}`;
        },

        calDays() {
            const first  = new Date(this.vy, this.vm, 1).getDay();
            const offset = first === 0 ? 6 : first - 1;
            const total  = new Date(this.vy, this.vm + 1, 0).getDate();
            const days   = [];
            for (let i = 0; i < offset; i++) days.push(null);
            for (let i = 1; i <= total; i++) days.push(i);
            while (days.length % 7 !== 0) days.push(null);
            return days;
        },

        isDisabled(day, minRaw) {
            if (!day) return true;
            if (!minRaw) return false;
            const min    = new Date(minRaw);
            const minDay = new Date(min.getFullYear(), min.getMonth(), min.getDate());
            return new Date(this.vy, this.vm, day) < minDay;
        },

        isSelected(day) {
            return day !== null && day === this.sd && this.vm === this.sm && this.vy === this.sy;
        },

        isToday(day) {
            const t = new Date();
            return day !== null && day === t.getDate()
                && this.vm === t.getMonth() && this.vy === t.getFullYear();
        },

        prevMonth() {
            if (this.vm === 0) { this.vm = 11; this.vy--; } else this.vm--;
        },
        nextMonth() {
            if (this.vm === 11) { this.vm = 0; this.vy++; } else this.vm++;
        },

        clickDay(day, minRaw) {
            if (!day || this.isDisabled(day, minRaw)) return;
            this.sd = day; this.sm = this.vm; this.sy = this.vy;
            if (minRaw) {
                const min    = new Date(minRaw);
                const minDay = new Date(min.getFullYear(), min.getMonth(), min.getDate());
                const selDay = new Date(this.vy, this.vm, day);
                if (selDay.getTime() === minDay.getTime()) {
                    let mh = min.getHours(), mm = min.getMinutes();
                    if (mm > 0 && mm <= 30)  { mm = 30; }
                    else if (mm > 30)        { mm = 0; mh = Math.min(mh + 1, 23); }
                    this.h = mh; this.m = mm;
                }
            }
            this.view = 'time';
        },

        adjH(delta) { this.h = (this.h + delta + 24) % 24; },
        adjM(delta) {
            const steps = [0, 15, 30, 45];
            const idx   = steps.indexOf(this.m);
            this.m      = steps[(idx + delta + steps.length) % steps.length];
        },

        confirm() {
            if (this.sd === null) return;
            const d  = String(this.sd).padStart(2, '0');
            const mo = String(this.sm + 1).padStart(2, '0');
            const h  = String(this.h).padStart(2, '0');
            const m  = String(this.m).padStart(2, '0');
            this.raw  = `${this.sy}-${mo}-${d}T${h}:${m}`;
            this.open = false;
            this.view = 'cal';
        },

        clear() {
            this.raw = ''; this.sd = null; this.sm = null; this.sy = null;
            this.view = 'cal';
        },
    };
}

/* ================================================================
   MAIN ALPINE COMPONENT
   ================================================================ */
document.addEventListener('alpine:init', () => {

    Alpine.data('bookingForm', ({ vehicles, schedules }) => ({

        /* ---- expose ke template ---- */
        vehicles  : vehicles,
        schedules : schedules,   // format: { "vehicle_id": [{start, end}, ...] }

        /* ---- state ---- */
        mode          : 'self',
        selectedId    : null,    // selalu string setelah selectVehicle()
        selectedLabel : '',
        vehicleOpen   : false,
        search        : '',
        isRental      : false,

        startPicker : createPickerState(),
        endPicker   : createPickerState(),

        submitting        : false,
        serverConflict    : false,
        serverConflictMsg : '',

        /* ---- debug: panggil saat init untuk cek data dari server ---- */
        init() {
            console.group('[BookingForm] init');
            console.log('vehicles :', this.vehicles);
            console.log('schedules:', this.schedules);
            console.log('schedules keys:', Object.keys(this.schedules));
            console.groupEnd();
        },

        /* ================================================================
           COMPUTED
           ================================================================ */
        get minNow() {
            const now = new Date();
            now.setSeconds(0, 0);
            return now.toISOString().slice(0, 16);
        },

        get filteredVehicles() {
            const s = this.search.toLowerCase();
            return this.vehicles.filter(v =>
                v.name.toLowerCase().includes(s) ||
                v.license_plate.toLowerCase().includes(s)
            );
        },

        /*
         * Jadwal untuk kendaraan yang sedang dipilih.
         * Selalu kembalikan array (tidak pernah undefined/null).
         *
         * KEY LOOKUP: selectedId disimpan sebagai string ("1"),
         * schedules keys dari PHP juga string setelah json_encode.
         * Pakai String() untuk memastikan selalu cocok.
         */
        get selectedSchedules() {
            if (!this.selectedId) return [];
            const key = String(this.selectedId);
            const found = this.schedules[key];
            console.log('[BookingForm] selectedSchedules lookup key:', key, '→', found);
            return Array.isArray(found) ? found : [];
        },

        get endIsBeforeStart() {
            if (!this.startPicker.raw || !this.endPicker.raw) return false;
            return new Date(this.endPicker.raw) <= new Date(this.startPicker.raw);
        },

        get canSubmit() {
            if (this.submitting)                       return false;
            if (this.endIsBeforeStart)                 return false;
            if (this.mode === 'self' && this.hasLocalConflict()) return false;
            if (this.serverConflict)                   return false;
            return true;
        },

        /* ================================================================
           METHODS
           ================================================================ */
        selectVehicle(v) {
            // Simpan sebagai string agar selalu cocok dengan key JSON dari PHP
            this.selectedId    = String(v.id);
            this.selectedLabel = `${v.name} — ${v.license_plate}`;
            this.vehicleOpen   = false;
            this.serverConflict = false;
            console.log('[BookingForm] selected vehicle id:', this.selectedId,
                        '| schedules for this id:', this.selectedSchedules);
        },

        hasLocalConflict() {
            if (!this.selectedId || !this.startPicker.raw || !this.endPicker.raw) return false;
            const s = new Date(this.startPicker.raw);
            const e = new Date(this.endPicker.raw);
            return this.selectedSchedules.some(b =>
                s < new Date(b.end) && e > new Date(b.start)
            );
        },

        formatDate(iso) {
            const d  = new Date(iso);
            const mo = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return `${d.getDate()} ${mo[d.getMonth()]} ${d.getFullYear()}, ` +
                   `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
        },

        /* ----------------------------------------------------------------
           Submit: cek live ke server dulu sebelum kirim form
           ---------------------------------------------------------------- */
        async handleSubmit(formEl) {
            if (!this.canSubmit) return;

            if (this.mode === 'self' && this.selectedId
                && this.startPicker.raw && this.endPicker.raw) {

                this.submitting     = true;
                this.serverConflict = false;

                try {
                    const params = new URLSearchParams({
                        vehicle_id : this.selectedId,
                        start_time : this.startPicker.raw,
                        end_time   : this.endPicker.raw,
                    });
                    const res = await fetch(`/booking/check-availability?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (!data.available) {
                            this.serverConflict    = true;
                            this.serverConflictMsg = data.message
                                ?? 'Kendaraan ini baru saja dipesan untuk waktu yang sama. '
                                 + 'Silakan pilih waktu atau unit lain.';
                            this.submitting = false;
                            return;
                        }
                    }
                } catch (_) {
                    // network error — biarkan server yang validasi
                }

                this.submitting = false;
            }

            formEl.submit();
        },
    }));
});