<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div x-data="{
    open: false,
    notifOpen: false,
    notifs: [],
    unread: {{ auth()->user()->unreadNotifications()->count() }},
    expandedId: null,
    loading: false,

    /* --- Task counters (dinamis dari backend) --- */
    prepCount: {{ auth()->user()->role === 'admin_ga'
        ? \App\Models\Booking::where('status', \App\Enums\BookingStatus::Approved)->count()
        : 0 }},

    pantauCount: {{ auth()->user()->role === 'admin_ga'
        ? \App\Models\Booking::whereIn('status', [
            \App\Enums\BookingStatus::Prepared,
            \App\Enums\BookingStatus::Active,
        ])->count()
        : 0 }},

    approvalCount: {{ auth()->user()->role === 'approver'
        ? \App\Models\Booking::where('approver_id', auth()->id())->where('status', \App\Enums\BookingStatus::Pending)->count()
        : 0 }},

    /* --- Helpers warna --- */
    colorCls(c) {
        return {
            blue: 'bg-blue-100 text-blue-600',
            green: 'bg-green-100 text-green-600',
            red: 'bg-red-100 text-red-600',
            yellow: 'bg-yellow-100 text-yellow-600',
            indigo: 'bg-indigo-100 text-indigo-600'
        } [c] ?? 'bg-blue-100 text-blue-600';
    },
    badgeCls(c) {
        return {
            blue: 'bg-blue-50 text-blue-700 border-blue-200',
            green: 'bg-green-50 text-green-700 border-green-200',
            red: 'bg-red-50 text-red-700 border-red-200',
            yellow: 'bg-yellow-50 text-yellow-700 border-yellow-200',
            indigo: 'bg-indigo-50 text-indigo-700 border-indigo-200'
        } [c] ?? 'bg-blue-50 text-blue-700 border-blue-200';
    },
    iconPath(k) {
        const p = {
            bell: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
            check: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            x: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            truck: 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM13 16l2-5h4l2 5H13z',
            info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            file: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            flag: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9',
        };
        return p[k] ?? p.bell;
    },

    csrf() { return document.querySelector('meta[name=csrf-token]')?.content ?? ''; },

    /* ─── Notifikasi ─── */
    async loadNotifs() {
        this.loading = true;
        try {
            const r = await fetch('{{ route('notifications.index') }}', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!r.ok) { console.error('[Notif] HTTP', r.status); return; }
            const d = await r.json();
            if (!d.success) { console.error('[Notif]', d); return; }
            this.notifs = d.notifications ?? [];
            this.unread = d.unread_count ?? 0;
        } catch (e) { console.error('[Notif]', e); } finally { this.loading = false; }
    },

    async pollUnread() {
        try {
            const r = await fetch('{{ route('notifications.unread-count') }}', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!r.ok) return;
            const d = await r.json();
            this.unread = d.count ?? 0;
        } catch (e) {}
    },

    /* ─── Task counters (role-based) ─── */
    async pollTaskCounts() {
        @if (auth()->user()->role === 'approver') try {
            const r = await fetch('{{ route('approvals.pending-count') }}', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (r.ok) { const d = await r.json(); this.approvalCount = d.count ?? 0; }
        } catch(e) {} @endif

        @if (auth()->user()->role === 'admin_ga') try {
            const r = await fetch('{{ route('admin.pending-count') }}', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (r.ok) { const d = await r.json(); this.prepCount = d.count ?? 0; }
        } catch(e) {}

        try {
            const r = await fetch('{{ route('admin.active-count') }}', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (r.ok) { const d = await r.json(); this.pantauCount = d.count ?? 0; }
        } catch(e) {} @endif
    },

    /* ─── Aksi notif ─── */
    async markRead(n) {
        if (n.is_read) return;
        try {
            await fetch('/notifications/' + n.id + '/read', {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': this.csrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            n.is_read = true;
            this.unread = Math.max(0, this.unread - 1);
        } catch (e) {}
    },

    async markAllRead() {
        try {
            await fetch('/notifications/mark-all-read', {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': this.csrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.notifs.forEach(n => n.is_read = true);
            this.unread = 0;
        } catch (e) {}
    },

    async deleteNotif(n, e) {
        if (e) e.stopPropagation();
        try {
            await fetch('/notifications/' + n.id, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': this.csrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!n.is_read) this.unread = Math.max(0, this.unread - 1);
            this.notifs = this.notifs.filter(x => x.id !== n.id);
            if (this.expandedId === n.id) this.expandedId = null;
        } catch (e) {}
    },

    async clearRead() {
        try {
            await fetch('/notifications/clear-read', {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': this.csrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.notifs = this.notifs.filter(n => !n.is_read);
        } catch (e) {}
    },

    toggleExpand(n) {
        this.expandedId = this.expandedId === n.id ? null : n.id;
        this.markRead(n);
    },

    openNotif() {
        this.notifOpen = true;
        this.open = false;
        this.loadNotifs();
        if (window.innerWidth < 640) document.body.style.overflow = 'hidden';
    },

    closeNotif() {
        this.notifOpen = false;
        this.expandedId = null;
        document.body.style.overflow = '';
    },

    isMobile: window.innerWidth < 640,

    init() {
        this.isMobile = window.innerWidth < 640;
        window.addEventListener('resize', () => { this.isMobile = window.innerWidth < 640; });
        /* Jalankan semua polling sekaligus saat init */
        this.pollUnread();
        this.pollTaskCounts();
        setInterval(() => {
            this.pollUnread();
            this.pollTaskCounts();
        }, 10000);
    }
}">

    {{-- ══════════════════════════════════════
     NAVBAR
══════════════════════════════════════ --}}
    <nav class="fixed top-0 left-0 w-full z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                {{-- ═══ LOGO MOBILE (KIRI) - HANYA TAMPIL DI MOBILE ═══ --}}
                <div class="flex items-center sm:hidden">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="font-bold text-gray-800 text-xl">Drivora</span>
                    </a>
                </div>

                {{-- ═══ DESKTOP LOGO + MENU (TAMPIL DI DESKTOP) ═══ --}}
                <div class="flex items-center hidden sm:flex">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0">
                        <span class="font-bold text-gray-800 text-xl">Drivora</span>
                    </a>

                    <div class="hidden sm:flex sm:items-center sm:ml-8 sm:gap-1">
                        @php
                            $lnk = fn($r, $lbl, $m = null) => '<a href="' .
                                route($r) .
                                '"
                            class="px-3 py-2 rounded-md text-sm font-medium transition ' .
                                (request()->routeIs($m ?? $r)
                                    ? 'bg-blue-600 text-white'
                                    : 'text-gray-600 hover:bg-gray-100') .
                                '">' .
                                $lbl .
                                '</a>';
                        @endphp

                        {!! $lnk('dashboard', 'Beranda') !!}
                        {!! $lnk('booking.create', 'Ajukan Sewa') !!}
                        {!! $lnk('booking.history', 'Riwayat') !!}

                        {{-- Approver Menu --}}
                        @if (auth()->user()->role === 'approver')
                            <div class="w-px h-5 bg-gray-300 mx-1"></div>
                            <a href="{{ route('approvals.index') }}"
                                class="relative px-3 py-2 rounded-md text-sm font-medium transition
                              {{ request()->routeIs('approvals.index') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                Persetujuan
                                <span x-cloak x-show="approvalCount > 0"
                                    x-text="approvalCount > 99 ? '99+' : approvalCount"
                                    class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                                     bg-red-500 text-white text-[10px] font-bold rounded-full
                                     flex items-center justify-center leading-none pointer-events-none">
                                </span>
                            </a>
                            {!! $lnk('approvals.history', 'Laporan') !!}
                        @endif

                        {{-- Admin GA Menu --}}
                        @if (auth()->user()->role === 'admin_ga')
                            <div class="w-px h-5 bg-gray-300 mx-1"></div>
                            <a href="{{ route('admin.dispatch') }}"
                                class="relative px-3 py-2 rounded-md text-sm font-medium transition
                              {{ request()->routeIs('admin.dispatch') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                Persiapan
                                <span x-cloak x-show="prepCount > 0" x-text="prepCount > 99 ? '99+' : prepCount"
                                    class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                                     bg-red-500 text-white text-[10px] font-bold rounded-full
                                     flex items-center justify-center leading-none pointer-events-none">
                                </span>
                            </a>
                            <a href="{{ route('admin.active') }}"
                                class="relative px-3 py-2 rounded-md text-sm font-medium transition
                              {{ request()->routeIs('admin.active') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                                Pantau
                                <span x-cloak x-show="pantauCount > 0" x-text="pantauCount > 99 ? '99+' : pantauCount"
                                    class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                                     bg-red-500 text-white text-[10px] font-bold rounded-full
                                     flex items-center justify-center leading-none pointer-events-none">
                                </span>
                            </a>
                            {!! $lnk('admin.trip.history', 'Laporan') !!}
                            {!! $lnk('admin.vehicles.index', 'Unit', 'admin.vehicles.*') !!}
                            {!! $lnk('admin.users.index', 'User', 'admin.users.*') !!}
                        @endif
                    </div>
                </div>

                {{-- ═══ DESKTOP RIGHT: Bell + Avatar ═══ --}}
                <div class="hidden sm:flex sm:items-center sm:gap-2">
                    <button @click="openNotif()"
                        class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-cloak x-show="unread > 0" x-text="unread > 99 ? '99+' : unread"
                            class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1
                             bg-red-500 text-white text-[10px] font-bold rounded-full
                             flex items-center justify-center leading-none pointer-events-none">
                        </span>
                    </button>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200
                                   rounded-lg text-sm font-medium text-gray-700 bg-white
                                   hover:bg-gray-50 transition">
                                <div
                                    class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center
                                    text-white text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                            <x-dropdown-link :href="route('help.index')">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3
                             0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093
                             m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Bantuan
                                </span>
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- ═══ MOBILE TOMBOL (KANAN) ═══ --}}
                <div class="flex items-center gap-1 sm:hidden">
                    {{-- Bell Mobile --}}
                    <button @click="openNotif()"
                        class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-cloak x-show="unread > 0" x-text="unread > 99 ? '99+' : unread"
                            class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-0.5
                             bg-red-500 text-white text-[9px] font-bold rounded-full
                             flex items-center justify-center pointer-events-none">
                        </span>
                    </button>

                    {{-- Hamburger --}}
                    <button @click="open = !open; if(open) closeNotif()"
                        class="relative p-2 rounded-md text-gray-400 hover:text-gray-500
                       hover:bg-gray-100 transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="open ? 'hidden' : 'block'" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="open ? 'block' : 'hidden'" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span x-cloak x-show="(approvalCount > 0 || prepCount > 0 || pantauCount > 0) && !open"
                            class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full
                             border-2 border-white pointer-events-none">
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </nav>

    {{-- ══════════════════════════════════════
     NOTIFIKASI PANEL
══════════════════════════════════════ --}}

    {{-- Backdrop Desktop --}}
    <div x-cloak x-show="notifOpen && !isMobile" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="closeNotif()" class="fixed inset-0 z-40 bg-black/20"
        style="top:64px;">
    </div>

    {{-- Panel Notif Desktop --}}
    <div x-cloak x-show="notifOpen && !isMobile" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-full"
        class="fixed top-16 right-0 z-50 w-[420px] bg-white shadow-2xl
            border-l border-gray-200 flex flex-col"
        style="height:calc(100vh - 64px);">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-3">
                <h2 class="text-base font-bold text-gray-800">Notifikasi</h2>
                <span x-cloak x-show="unread > 0" x-text="unread + ' baru'"
                    class="text-[11px] font-semibold px-2 py-0.5 bg-red-100 text-red-600 rounded-full">
                </span>
            </div>
            <div class="flex items-center gap-1">
                <button x-cloak x-show="unread > 0" @click="markAllRead()"
                    class="text-xs font-medium text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-md transition">
                    Baca Semua
                </button>
                <button @click="clearRead()"
                    class="text-xs font-medium text-gray-400 hover:text-gray-600
                       hover:bg-gray-100 px-2 py-1.5 rounded-md transition">
                    Hapus Terbaca
                </button>
                <button @click="closeNotif()"
                    class="p-1.5 rounded-md hover:bg-gray-100 text-gray-400
                       hover:text-gray-600 transition ml-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="flex items-center justify-center py-12">
            <svg class="w-6 h-6 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
        </div>

        {{-- List --}}
        <div x-show="!loading" class="flex-1 overflow-y-auto">

            {{-- Empty state --}}
            <div x-show="notifs.length === 0"
                class="flex flex-col items-center justify-center h-full text-center px-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">Belum ada notifikasi</p>
                <p class="text-xs text-gray-400 mt-1">Notifikasi muncul setelah ada aktivitas</p>
            </div>

            {{-- Items --}}
            <template x-for="n in notifs" :key="n.id">
                <div :class="n.is_read ? 'bg-white' : 'bg-blue-50/50'" class="border-b border-gray-100 last:border-0">

                    <div @click="toggleExpand(n)"
                        class="flex gap-3 px-5 py-4 cursor-pointer hover:bg-gray-50 transition group">
                        <div class="shrink-0 mt-0.5">
                            <div :class="colorCls(n.color)"
                                class="w-9 h-9 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        :d="iconPath(n.icon)" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-800 leading-snug" x-text="n.title"></p>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <div x-cloak x-show="!n.is_read" class="w-2 h-2 bg-blue-500 rounded-full mt-0.5">
                                    </div>
                                    <button @click="deleteNotif(n, $event)"
                                        class="opacity-0 group-hover:opacity-100 p-1 rounded
                                           hover:bg-red-100 text-gray-300 hover:text-red-500 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed line-clamp-2" x-text="n.message">
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-[10px] text-gray-400 font-medium" x-text="n.created_at"></span>
                                <div class="flex items-center gap-2">
                                    <template x-if="n.booking_code">
                                        <span :class="badgeCls(n.color)"
                                            class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded border"
                                            x-text="n.booking_code"></span>
                                    </template>
                                    <span class="text-[10px] text-blue-400"
                                        x-text="expandedId === n.id ? '▲ Tutup' : '▼ Detail'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Expanded detail --}}
                    <div x-show="expandedId === n.id" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        class="px-5 pb-4 bg-gray-50 border-t border-gray-100">
                        <div class="pt-3 space-y-2">
                            <p class="text-xs text-gray-600 leading-relaxed" x-text="n.message"></p>
                            <template x-if="n.booking_id">
                                <div class="flex items-center gap-2 pt-1">
                                    <span class="text-[10px] text-gray-400">Kode Booking:</span>
                                    <span class="text-[11px] font-mono font-bold text-gray-700"
                                        x-text="n.booking_code"></span>
                                </div>
                            </template>
                            <p class="text-[10px] text-gray-400" x-text="'Diterima: ' + n.created_raw"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div x-show="notifs.length > 0 && !loading"
            class="px-5 py-3 border-t border-gray-100 bg-gray-50 shrink-0 text-center">
            <p class="text-xs text-gray-400">
                Menampilkan <span x-text="notifs.length"></span> notifikasi terbaru
            </p>
        </div>
    </div>

    {{-- Panel Notif Mobile (fullscreen) --}}
    <div x-cloak x-show="notifOpen && isMobile" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 bg-white flex flex-col">

        {{-- Mobile Notif Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 shrink-0 bg-white">
            <div class="flex items-center gap-3">
                <button @click="closeNotif()" class="p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div>
                    <h2 class="text-base font-bold text-gray-800">Notifikasi</h2>
                    <p x-cloak x-show="unread > 0" x-text="unread + ' belum dibaca'"
                        class="text-xs text-red-500 font-medium -mt-0.5"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button x-cloak x-show="unread > 0" @click="markAllRead()"
                    class="text-xs font-medium text-blue-600 px-3 py-1.5 rounded-md
                       hover:bg-blue-50 transition">
                    Baca Semua
                </button>
                <button @click="clearRead()"
                    class="text-xs font-medium text-gray-400 hover:text-gray-600
                       hover:bg-gray-100 px-2 py-1.5 rounded-md transition">
                    Hapus
                </button>
            </div>
        </div>

        {{-- Mobile Notif Loading --}}
        <div x-show="loading" class="flex items-center justify-center py-12">
            <svg class="w-8 h-8 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
        </div>

        {{-- Mobile Notif List --}}
        <div x-show="!loading" class="flex-1 overflow-y-auto">
            <div x-show="notifs.length === 0"
                class="flex flex-col items-center justify-center h-full text-center px-6">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <p class="text-base font-semibold text-gray-500">Belum ada notifikasi</p>
                <p class="text-sm text-gray-400 mt-1">Notifikasi muncul setelah ada aktivitas</p>
            </div>

            <template x-for="n in notifs" :key="n.id">
                <div :class="n.is_read ? 'bg-white' : 'bg-blue-50/50'" class="border-b border-gray-100 last:border-0">

                    <div @click="toggleExpand(n)"
                        class="flex gap-3 px-4 py-4 cursor-pointer active:bg-gray-50 transition">
                        <div class="shrink-0 mt-0.5">
                            <div :class="colorCls(n.color)"
                                class="w-10 h-10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        :d="iconPath(n.icon)" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-800 leading-snug" x-text="n.title"></p>
                                <div class="flex items-center gap-2 shrink-0">
                                    <div x-cloak x-show="!n.is_read"
                                        class="w-2.5 h-2.5 bg-blue-500 rounded-full mt-0.5"></div>
                                    <button @click="deleteNotif(n, $event)"
                                        class="p-1.5 rounded-full hover:bg-red-100 text-gray-300
                                           hover:text-red-500 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed line-clamp-2" x-text="n.message"></p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-[11px] text-gray-400" x-text="n.created_at"></span>
                                <template x-if="n.booking_code">
                                    <span :class="badgeCls(n.color)"
                                        class="text-[10px] font-mono font-bold px-2 py-0.5 rounded border"
                                        x-text="n.booking_code"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Notif Expanded --}}
                    <div x-show="expandedId === n.id" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        class="mx-4 mb-4 p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                            Detail
                        </h4>
                        <p class="text-sm text-gray-700 leading-relaxed" x-text="n.message"></p>
                        <template x-if="n.booking_id">
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xs text-gray-400">Kode Booking</span>
                                <span class="text-xs font-mono font-bold text-gray-700"
                                    x-text="n.booking_code"></span>
                            </div>
                        </template>
                        <p class="text-xs text-gray-400 mt-2" x-text="'Diterima: ' + n.created_raw"></p>
                        <button @click="deleteNotif(n, $event)"
                            class="mt-3 w-full text-xs font-medium text-red-500 hover:bg-red-50
                               py-2 rounded-lg border border-red-200 transition">
                            Hapus Notifikasi
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="notifs.length > 0 && !loading"
            class="px-4 py-3 border-t border-gray-100 bg-gray-50 shrink-0 text-center">
            <p class="text-xs text-gray-400">
                <span x-text="notifs.length"></span> notifikasi ditampilkan
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════
     SIDEBAR MOBILE
══════════════════════════════════════ --}}

    {{-- Backdrop --}}
    <div x-cloak x-show="open" @click="open = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/40 z-40 sm:hidden" style="top:64px;">
    </div>

    {{-- Drawer --}}
    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-16 right-0 z-50 h-[calc(100vh-64px)] w-72 bg-white shadow-2xl
            sm:hidden flex flex-col overflow-y-auto">

        @php
            $roleMap = [
                'admin_ga' => ['Admin GA', 'bg-blue-100 text-blue-700'],
                'approver' => ['Approver', 'bg-indigo-100 text-indigo-700'],
                'staff' => ['Staff', 'bg-gray-100 text-gray-600'],
            ];
            [$roleLabel, $roleBadge] = $roleMap[auth()->user()->role] ?? ['User', 'bg-gray-100 text-gray-600'];
        @endphp

        {{-- Profile Header --}}
        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-5 flex items-center gap-3 shrink-0">
            <div
                class="w-11 h-11 rounded-full bg-white/20 border-2 border-white/30
                    flex items-center justify-center text-white font-bold text-base shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-blue-200 truncate">{{ Auth::user()->email }}</div>
                <span
                    class="inline-block mt-1.5 text-[10px] font-semibold px-2 py-0.5 rounded-full
                         bg-white/20 text-white border border-white/30">
                    {{ $roleLabel }}
                </span>
            </div>
        </div>

        {{-- Nav Items --}}
        <div class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            {{-- Umum --}}
            <p class="px-3 pt-1 pb-2 text-[10px] uppercase tracking-wider text-gray-400 font-semibold">
                Menu Utama
            </p>

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                  {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Beranda
            </a>

            <a href="{{ route('booking.create') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('booking.create') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajukan Sewa
            </a>

            <a href="{{ route('booking.history') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('booking.history') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat
            </a>

            {{-- Approver Section --}}
            @if (auth()->user()->role === 'approver')
                <div class="pt-3">
                    <p class="px-3 pb-2 text-[10px] uppercase tracking-wider text-gray-400 font-semibold">
                        Approver
                    </p>

                    {{-- Persetujuan + badge --}}
                    <a href="{{ route('approvals.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('approvals.index') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="flex-1">Persetujuan</span>
                        <span x-cloak x-show="approvalCount > 0" x-text="approvalCount > 99 ? '99+' : approvalCount"
                            class="min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-[10px]
                                 font-bold rounded-full flex items-center justify-center
                                 leading-none shrink-0">
                        </span>
                    </a>

                    <a href="{{ route('approvals.history') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('approvals.history') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan
                    </a>
                </div>
            @endif

            {{-- Admin GA Section --}}
            @if (auth()->user()->role === 'admin_ga')
                <div class="pt-3">
                    <p class="px-3 pb-2 text-[10px] uppercase tracking-wider text-gray-400 font-semibold">
                        Admin GA
                    </p>

                    {{-- Persiapan + badge --}}
                    <a href="{{ route('admin.dispatch') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.dispatch') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="flex-1">Persiapan</span>
                        <span x-cloak x-show="prepCount > 0" x-text="prepCount > 99 ? '99+' : prepCount"
                            class="min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-[10px]
                                 font-bold rounded-full flex items-center justify-center
                                 leading-none shrink-0">
                        </span>
                    </a>

                    {{-- Pantau + badge pantauCount --}}
                    <a href="{{ route('admin.active') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.active') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <span class="flex-1">Pantau</span>
                        <span x-cloak x-show="pantauCount > 0" x-text="pantauCount > 99 ? '99+' : pantauCount"
                            class="min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-[10px]
                                 font-bold rounded-full flex items-center justify-center
                                 leading-none shrink-0">
                        </span>
                    </a>

                    <a href="{{ route('admin.trip.history') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.trip.history') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan
                    </a>

                    <a href="{{ route('admin.vehicles.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.vehicles.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM18 17a2 2 0 11-4 0 2 2 0 014 0zM3 9h18M7 3h10l2 6H5L7 3z" />
                        </svg>
                        Unit
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        User
                    </a>
                </div>
            @endif
        </div>

        {{-- Footer: Profile + Logout --}}
        <div class="border-t border-gray-100 px-3 py-3 space-y-0.5 shrink-0 bg-gray-50">
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil Saya
            </a>
            <a href="{{ route('help.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
              {{ request()->routeIs('help.index') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}
              transition">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3
                         0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093
                         m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Bantuan
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                       text-red-600 hover:bg-red-50 transition">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </div>

    {{-- ══ Progress Bar Navigasi ══ --}}
    <div id="nav-progress" class="fixed top-0 left-0 h-0.5 bg-blue-600 z-[9999] transition-all duration-200 ease-out"
        style="width:0;display:none;box-shadow:0 0 6px #2563eb;">
    </div>

    <script>
        (function() {
            const bar = document.getElementById('nav-progress');
            let t;

            function start() {
                bar.style.display = 'block';
                bar.style.width = '0';
                let w = 0;
                clearInterval(t);
                t = setInterval(() => {
                    w = Math.min(w + (80 - w) * 0.12 + 0.5, 85);
                    bar.style.width = w + '%';
                }, 40);
            }

            function done() {
                clearInterval(t);
                bar.style.width = '100%';
                setTimeout(() => {
                    bar.style.display = 'none';
                    bar.style.width = '0';
                }, 250);
            }
            document.addEventListener('click', e => {
                const a = e.target.closest('a[href]');
                if (!a) return;
                const h = a.getAttribute('href') ?? '';
                if (!h || h.startsWith('#') || h.startsWith('mailto') || a.target === '_blank') return;
                if (h.startsWith('http') && !h.includes(location.hostname)) return;
                start();
            });
            window.addEventListener('pageshow', done);
        })();
    </script>

</div>{{-- /x-data --}}
