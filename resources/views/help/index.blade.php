{{--
 | resources/views/help/index.blade.php
 | Halaman Pusat Bantuan — Drivora
 | Role-aware: staff / approver / admin
--}}

<x-app-layout>

    <x-slot name="title">Pusat Bantuan — Drivora</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/help.css') }}">
    @endpush

    {{-- Data sections dioper ke JS --}}
    <script>
        window.HELP_SECTIONS = @json($sections);
        window.HELP_ROLE = "{{ $role }}";
    </script>

    <div class="min-h-screen bg-gray-50 pb-16" x-data="helpPage()" x-init="init()">

        {{-- ══ HEADER ══════════════════════════════════════════════ --}}
        <div class="help-header">
            <div class="max-w-3xl mx-auto px-4 py-10 text-center">

                <div class="help-header-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3
                                 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093
                                 m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h1 class="mt-4 text-2xl font-bold text-white tracking-tight">Pusat Bantuan</h1>
                <p class="mt-1 text-blue-100 text-sm">Temukan jawaban seputar penggunaan <strong>Drivora</strong></p>

                {{-- Role badge --}}
                <div
                    class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full
                            bg-white/20 border border-white/30 text-white text-xs font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-300 animate-pulse"></span>
                    Masuk sebagai:
                    <span class="font-semibold">{{ $roleLabel }}</span>
                </div>

                {{-- Search --}}
                <div class="mt-6 relative max-w-lg mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input id="search_help" name="search_help" aria-label="Cari pertanyaan bantuan"
                        x-model.debounce.200ms="search" type="text" placeholder="Cari pertanyaan..."
                        class="help-search-input" @keydown.escape="search = ''">
                    <button x-cloak x-show="search" @click="search = ''"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Result count when searching --}}
                <p x-cloak x-show="search" class="mt-3 text-blue-100 text-xs">
                    <span x-text="totalVisible()"></span> hasil ditemukan
                </p>
            </div>
        </div>

        {{-- ══ BODY ═════════════════════════════════════════════════ --}}
        <div class="max-w-3xl mx-auto px-4 mt-8">

            {{-- Category nav --}}
            <div class="help-tabs" x-show="!search">
                <button @click="activeSection = 'all'"
                    :class="activeSection === 'all' ? 'help-tab-active' : 'help-tab'" class="help-tab-base">
                    Semua
                </button>
                <template x-for="sec in sections" :key="sec.id">
                    <button @click="activeSection = sec.id"
                        :class="activeSection === sec.id ? 'help-tab-active' : 'help-tab'" class="help-tab-base"
                        x-text="sec.title">
                    </button>
                </template>
            </div>

            {{-- Sections --}}
            <div class="space-y-8 mt-4">
                <template x-for="sec in filteredSections()" :key="sec.id">
                    <div :id="'sec-' + sec.id">

                        {{-- Section header --}}
                        <div class="flex items-center gap-3 mb-3">
                            <div :class="'help-sec-icon help-sec-icon--' + sec.color">
                                {{-- Icon rendered via JS --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        :d="iconPath(sec.icon)" />
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider" x-text="sec.title">
                            </h2>
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-xs text-gray-400" x-text="sec.items.length + ' pertanyaan'"></span>
                        </div>

                        {{-- FAQ items --}}
                        <div class="space-y-2">
                            <template x-for="(item, idx) in sec.items" :key="sec.id + '-' + idx">
                                <div class="help-faq-item" :class="isOpen(sec.id, idx) ? 'help-faq-item--open' : ''">

                                    {{-- Question --}}
                                    <button @click="toggle(sec.id, idx)" class="help-faq-q group">
                                        <span
                                            class="flex-1 text-left text-sm font-medium text-gray-800
                                                     group-hover:text-blue-700 leading-relaxed"
                                            x-html="highlight(item.q)">
                                        </span>
                                        <span class="help-faq-chevron" :class="isOpen(sec.id, idx) ? 'rotate-180' : ''">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </span>
                                    </button>

                                    {{-- Answer --}}
                                    <div x-show="isOpen(sec.id, idx)"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 -translate-y-1" class="help-faq-a">
                                        <div class="help-faq-a-inner" x-html="item.a"></div>
                                    </div>

                                </div>
                            </template>
                        </div>

                    </div>
                </template>

                {{-- Empty state --}}
                <div x-cloak x-show="filteredSections().length === 0" class="text-center py-16">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01
                                     M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">Tidak ada hasil yang cocok</p>
                    <p class="text-xs text-gray-400 mt-1">Coba kata kunci lain</p>
                    <button @click="search = ''" class="mt-4 text-xs font-medium text-blue-600 hover:text-blue-700">
                        Hapus pencarian
                    </button>
                </div>

            </div>

            {{-- Contact footer --}}
            <div class="help-footer mt-12">
                <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949
                             L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-700">Masih butuh bantuan?</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Jika pertanyaan Anda tidak ada di sini, hubungi Admin GA secara langsung.
                    </p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/help.js') }}"></script>
    @endpush

</x-app-layout>
