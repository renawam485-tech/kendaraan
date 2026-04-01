<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Profil Saya</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER CARD USER --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-md px-6 py-5 flex items-center gap-5">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-white text-lg font-bold">{{ auth()->user()->name }}</h2>
                    <p class="text-blue-200 text-sm">{{ auth()->user()->email }}</p>
                    @php
                        $roleLabel = [
                            'admin_ga' => 'Admin GA',
                            'approver' => 'Approver',
                            'driver'   => 'Driver',
                            'staff'    => 'Staff',
                        ][auth()->user()->role] ?? auth()->user()->role;
                    @endphp
                    <span class="inline-block mt-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-white/20 text-white border border-white/30">
                        {{ $roleLabel }}
                    </span>
                </div>
            </div>

            {{-- INFORMASI PROFIL --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">Informasi Profil</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Perbarui data pribadi dan email akun Anda</p>
                    </div>
                </div>
                <div class="px-6 py-6 max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- KEAMANAN AKUN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">Keamanan Akun</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Ganti password secara berkala untuk menjaga keamanan</p>
                    </div>
                </div>
                <div class="px-6 py-6 max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- HAPUS AKUN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                <div class="border-b border-red-100 bg-red-50 px-6 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-red-700">Hapus Akun</h3>
                        <p class="text-xs text-red-400 mt-0.5">Tindakan ini permanen dan tidak dapat dibatalkan</p>
                    </div>
                </div>
                <div class="px-6 py-6 max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>