const INDONESIA_CAR_DATABASE = [
    { id: 1, make: "Toyota", model: "Avanza", capacity: 7 },
    { id: 2, make: "Toyota", model: "Veloz", capacity: 7 },
    { id: 3, make: "Toyota", model: "Innova Reborn", capacity: 7 },
    { id: 4, make: "Toyota", model: "Innova Zenix", capacity: 7 },
    { id: 5, make: "Toyota", model: "Hiace Commuter", capacity: 15 },
    { id: 6, make: "Toyota", model: "Hiace Premio", capacity: 12 },
    { id: 7, make: "Toyota", model: "Alphard", capacity: 7 },
    { id: 8, make: "Toyota", model: "Vellfire", capacity: 7 },
    { id: 9, make: "Toyota", model: "Fortuner", capacity: 7 },
    { id: 10, make: "Toyota", model: "Rush", capacity: 7 },
    { id: 11, make: "Toyota", model: "Calya", capacity: 7 },
    { id: 12, make: "Toyota", model: "Agya", capacity: 5 },
    { id: 13, make: "Toyota", model: "Camry", capacity: 5 },
    { id: 14, make: "Toyota", model: "Voxy", capacity: 7 },
    { id: 15, make: "Toyota", model: "Land Cruiser 300", capacity: 7 },
    { id: 16, make: "Toyota", model: "Kijang Capsule (Classic)", capacity: 8 },
    { id: 17, make: "Mitsubishi", model: "Xpander", capacity: 7 },
    { id: 18, make: "Mitsubishi", model: "Xpander Cross", capacity: 7 },
    { id: 19, make: "Mitsubishi", model: "Pajero Sport", capacity: 7 },
    { id: 20, make: "Mitsubishi", model: "Xforce", capacity: 5 },
    { id: 21, make: "Mitsubishi", model: "L300 (Minibus)", capacity: 10 },
    { id: 22, make: "Daihatsu", model: "Xenia", capacity: 7 },
    { id: 23, make: "Daihatsu", model: "Terios", capacity: 7 },
    { id: 24, make: "Daihatsu", model: "Sigra", capacity: 7 },
    { id: 25, make: "Daihatsu", model: "Ayla", capacity: 5 },
    { id: 26, make: "Daihatsu", model: "GranMax (MB)", capacity: 9 },
    { id: 27, make: "Daihatsu", model: "Luxio", capacity: 8 },
    { id: 28, make: "Suzuki", model: "Ertiga", capacity: 7 },
    { id: 29, make: "Suzuki", model: "XL7", capacity: 7 },
    { id: 30, make: "Suzuki", model: "APV Arena", capacity: 8 },
    { id: 31, make: "Suzuki", model: "Grand Vitara", capacity: 5 },
    { id: 32, make: "Suzuki", model: "Baleno", capacity: 5 },
    { id: 33, make: "Suzuki", model: "Jimny (5-Door)", capacity: 5 },
    { id: 34, make: "Honda", model: "HR-V", capacity: 5 },
    { id: 35, make: "Honda", model: "BR-V", capacity: 7 },
    { id: 36, make: "Honda", model: "CR-V", capacity: 7 },
    { id: 37, make: "Honda", model: "Brio", capacity: 5 },
    { id: 38, make: "Honda", model: "Mobilio", capacity: 7 },
    { id: 39, make: "Isuzu", model: "Elf Short", capacity: 15 },
    { id: 40, make: "Isuzu", model: "Elf Long", capacity: 19 },
    { id: 41, make: "Isuzu", model: "Mu-X", capacity: 7 },
    { id: 42, make: "Isuzu", model: "Panther (Classic)", capacity: 7 },
    { id: 43, make: "Hyundai", model: "Stargazer", capacity: 7 },
    { id: 44, make: "Hyundai", model: "Palisade", capacity: 7 },
    { id: 45, make: "Hyundai", model: "Ioniq 5 (EV)", capacity: 5 },
    { id: 46, make: "Hyundai", model: "Staria", capacity: 9 },
    { id: 47, make: "Kia", model: "Grand Carnival", capacity: 11 },
    { id: 48, make: "Kia", model: "Carens", capacity: 7 },
    { id: 49, make: "Wuling", model: "Confero S", capacity: 8 },
    { id: 50, make: "Wuling", model: "Cortez", capacity: 7 },
    { id: 51, make: "Wuling", model: "Almaz", capacity: 7 },
    { id: 52, make: "Wuling", model: "Binguo EV", capacity: 5 },
    { id: 53, make: "Mercedes-Benz", model: "Sprinter", capacity: 14 },
    { id: 54, make: "Mercedes-Benz", model: "E-Class", capacity: 5 },
    { id: 55, make: "Hino", model: "Medium Bus", capacity: 31 },
    { id: 56, make: "Mercedes-Benz", model: "Big Bus", capacity: 47 },
    { id: 57, make: "BMW", model: "Series 5/7", capacity: 5 },
];

("use strict");

/* ================================================================
   Custom datetime picker state factory
   ================================================================ */
function createPickerState() {
    const MONTHS_LONG = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];
    const MONTHS_SHORT = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "Mei",
        "Jun",
        "Jul",
        "Agu",
        "Sep",
        "Okt",
        "Nov",
        "Des",
    ];

    return {
        open: false,
        view: "cal",
        vy: new Date().getFullYear(),
        vm: new Date().getMonth(),
        sd: null,
        sm: null,
        sy: null,
        h: 8,
        m: 0,
        raw: "",

        display() {
            if (!this.raw) return "";
            const [datePart, timePart] = this.raw.split("T");
            const [y, mo, d] = datePart.split("-");
            return `${parseInt(d)} ${MONTHS_SHORT[parseInt(mo) - 1]} ${y}  ·  ${timePart}`;
        },

        monthLabel() {
            return `${MONTHS_LONG[this.vm]} ${this.vy}`;
        },

        calDays() {
            const first = new Date(this.vy, this.vm, 1).getDay();
            const offset = first === 0 ? 6 : first - 1;
            const total = new Date(this.vy, this.vm + 1, 0).getDate();
            const days = [];
            for (let i = 0; i < offset; i++) days.push(null);
            for (let i = 1; i <= total; i++) days.push(i);
            while (days.length % 7 !== 0) days.push(null);
            return days;
        },

        isDisabled(day, minRaw) {
            if (!day) return true;
            if (!minRaw) return false;
            const min = new Date(minRaw);
            const minDay = new Date(
                min.getFullYear(),
                min.getMonth(),
                min.getDate(),
            );
            return new Date(this.vy, this.vm, day) < minDay;
        },

        isSelected(day) {
            return (
                day !== null &&
                day === this.sd &&
                this.vm === this.sm &&
                this.vy === this.sy
            );
        },

        isToday(day) {
            const t = new Date();
            return (
                day !== null &&
                day === t.getDate() &&
                this.vm === t.getMonth() &&
                this.vy === t.getFullYear()
            );
        },

        prevMonth() {
            if (this.vm === 0) {
                this.vm = 11;
                this.vy--;
            } else this.vm--;
        },
        nextMonth() {
            if (this.vm === 11) {
                this.vm = 0;
                this.vy++;
            } else this.vm++;
        },

        clickDay(day, minRaw) {
            if (!day || this.isDisabled(day, minRaw)) return;
            this.sd = day;
            this.sm = this.vm;
            this.sy = this.vy;
            if (minRaw) {
                const min = new Date(minRaw);
                const minDay = new Date(
                    min.getFullYear(),
                    min.getMonth(),
                    min.getDate(),
                );
                const selDay = new Date(this.vy, this.vm, day);
                if (selDay.getTime() === minDay.getTime()) {
                    let mh = min.getHours(),
                        mm = min.getMinutes();
                    if (mm > 0 && mm <= 30) {
                        mm = 30;
                    } else if (mm > 30) {
                        mm = 0;
                        mh = Math.min(mh + 1, 23);
                    }
                    this.h = mh;
                    this.m = mm;
                }
            }
            this.view = "time";
        },

        adjH(delta) {
            this.h = (this.h + delta + 24) % 24;
        },
        adjM(delta) {
            const steps = [0, 15, 30, 45];
            const idx = steps.indexOf(this.m);
            this.m = steps[(idx + delta + steps.length) % steps.length];
        },

        confirm() {
            if (this.sd === null) return;
            const d = String(this.sd).padStart(2, "0");
            const mo = String(this.sm + 1).padStart(2, "0");
            const h = String(this.h).padStart(2, "0");
            const m = String(this.m).padStart(2, "0");
            this.raw = `${this.sy}-${mo}-${d}T${h}:${m}`;
            this.open = false;
            this.view = "cal";
        },

        clear() {
            this.raw = "";
            this.sd = null;
            this.sm = null;
            this.sy = null;
            this.view = "cal";
        },
    };
}

/* ================================================================
   MAIN ALPINE COMPONENT
   ================================================================ */
document.addEventListener("alpine:init", () => {
    Alpine.data("bookingForm", ({ vehicles, schedules }) => ({
        /* ---- expose ke template ---- */
        vehicles: vehicles,
        schedules: schedules, // format: { "vehicle_id": [{start, end}, ...] }

        /* ---- state ---- */
        mode: "self",
        selectedId: null,
        selectedLabel: "",
        vehicleOpen: false,
        search: "",

        // --- POIN NO. 2: STATE RENTAL (TAMBAHKAN/GANTI DI SINI) ---
        isRental: false,
        rentalSearchOpen: false,
        rentalQuery: "",
        rentalResults: [],
        selectedRentalLabel: "",
        selectedRentalCapacity: null,
        passengers: 1,

        startPicker: createPickerState(),
        endPicker: createPickerState(),

        submitting: false,
        serverConflict: false,
        serverConflictMsg: "",

        /* ---- debug: panggil saat init untuk cek data dari server ---- */
        init() {
            console.group("[BookingForm] init");
            console.log("vehicles :", this.vehicles);
            console.log("schedules:", this.schedules);
            console.log("schedules keys:", Object.keys(this.schedules));
            console.groupEnd();

            this.rentalResults = INDONESIA_CAR_DATABASE.slice(0, 10);
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
            return this.vehicles.filter(
                (v) =>
                    v.name.toLowerCase().includes(s) ||
                    v.license_plate.toLowerCase().includes(s),
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
            console.log(
                "[BookingForm] selectedSchedules lookup key:",
                key,
                "→",
                found,
            );
            return Array.isArray(found) ? found : [];
        },

        get endIsBeforeStart() {
            if (!this.startPicker.raw || !this.endPicker.raw) return false;
            return (
                new Date(this.endPicker.raw) <= new Date(this.startPicker.raw)
            );
        },

        get canSubmit() {
            if (this.submitting) return false;
            if (this.endIsBeforeStart) return false;

            // Validasi Mode Pilih Mobil Kampus
            if (this.mode === "self") {
                if (!this.selectedId || this.hasLocalConflict()) return false;
            }

            // Validasi Mode Sewa Luar (Dispatch)
            else {
                // Jika user mencentang "Sewa Spesifik", wajib pilih mobil & cek kapasitas
                if (this.isRental) {
                    if (!this.selectedRentalLabel || this.isOverCapacity)
                        return false;
                }
                // Jumlah penumpang minimal 1
                if (!this.passengers || this.passengers < 1) return false;
            }

            return !this.serverConflict;
        },

        /* ================================================================
           METHODS
           ================================================================ */
        selectVehicle(v) {
            // Simpan sebagai string agar selalu cocok dengan key JSON dari PHP
            this.selectedId = String(v.id);
            this.selectedLabel = `${v.name} — ${v.license_plate}`;
            this.vehicleOpen = false;
            this.serverConflict = false;
            console.log(
                "[BookingForm] selected vehicle id:",
                this.selectedId,
                "| schedules for this id:",
                this.selectedSchedules,
            );
        },

        /* --- LOGIKA RENTAL (TAMBAHKAN DI BAWAH selectVehicle) --- */
        searchRental() {
            const q = this.rentalQuery.toLowerCase();
            if (q.length < 1) {
                // Tampilkan 10 mobil pertama sebagai saran jika input kosong
                this.rentalResults = INDONESIA_CAR_DATABASE.slice(0, 10);
                return;
            }
            // Filter berdasarkan merek atau model
            this.rentalResults = INDONESIA_CAR_DATABASE.filter((car) =>
                (
                    car.make.toLowerCase() +
                    " " +
                    car.model.toLowerCase()
                ).includes(q),
            );
        },

        selectRental(car) {
            this.selectedRentalLabel = `${car.make} ${car.model}`;
            this.selectedRentalCapacity = car.capacity;

            // Jika jumlah penumpang saat ini kosong atau lebih besar dari kapasitas mobil baru,
            // maka otomatis set ke kapasitas maksimal mobil tersebut.
            if (!this.passengers || this.passengers > car.capacity) {
                this.passengers = car.capacity;
            }

            this.rentalSearchOpen = false;
            this.rentalQuery = "";
            this.serverConflict = false;
        },

        // Fungsi pembantu untuk mengecek apakah penumpang melebihi kursi
        get isOverCapacity() {
            if (
                this.mode === "dispatch" &&
                this.isRental &&
                this.selectedRentalCapacity
            ) {
                return this.passengers > this.selectedRentalCapacity;
            }
            return false;
        },

        hasLocalConflict() {
            if (
                !this.selectedId ||
                !this.startPicker.raw ||
                !this.endPicker.raw
            )
                return false;
            const s = new Date(this.startPicker.raw);
            const e = new Date(this.endPicker.raw);
            return this.selectedSchedules.some(
                (b) => s < new Date(b.end) && e > new Date(b.start),
            );
        },

        formatDate(iso) {
            const d = new Date(iso);
            const mo = [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "Mei",
                "Jun",
                "Jul",
                "Agu",
                "Sep",
                "Okt",
                "Nov",
                "Des",
            ];
            return (
                `${d.getDate()} ${mo[d.getMonth()]} ${d.getFullYear()}, ` +
                `${String(d.getHours()).padStart(2, "0")}:${String(d.getMinutes()).padStart(2, "0")}`
            );
        },

        /* ----------------------------------------------------------------
           Submit: cek live ke server dulu sebelum kirim form
           ---------------------------------------------------------------- */
        async handleSubmit(formEl) {
            if (!this.canSubmit) return;

            if (
                this.mode === "self" &&
                this.selectedId &&
                this.startPicker.raw &&
                this.endPicker.raw
            ) {
                this.submitting = true;
                this.serverConflict = false;

                try {
                    const params = new URLSearchParams({
                        vehicle_id: this.selectedId,
                        start_time: this.startPicker.raw,
                        end_time: this.endPicker.raw,
                    });
                    const res = await fetch(
                        `/booking/check-availability?${params}`,
                        {
                            headers: { "X-Requested-With": "XMLHttpRequest" },
                        },
                    );
                    if (res.ok) {
                        const data = await res.json();
                        if (!data.available) {
                            this.serverConflict = true;
                            this.serverConflictMsg =
                                data.message ??
                                "Kendaraan ini baru saja dipesan untuk waktu yang sama. " +
                                    "Silakan pilih waktu atau unit lain.";
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
