<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah User / Driver</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <x-input-label value="Nama Lengkap" />
                        <x-text-input name="name" class="block mt-1 w-full" required />
                    </div>

                    <div>
                        <x-input-label value="Email (Untuk Login)" />
                        <x-text-input name="email" type="email" class="block mt-1 w-full" required />
                    </div>

                    <div>
                        <x-input-label value="Password Default" />
                        <x-text-input name="password" type="text" class="block mt-1 w-full" value="password123" required />
                        <p class="text-xs text-gray-500 mt-1">Password awal diset default, minta user menggantinya nanti.</p>
                    </div>

                    <div>
                        <x-input-label value="Peran (Role)" />
                        <select name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1 bg-yellow-50">
                            <option value="staff">Staff (Pemohon Biasa)</option>
                            <option value="driver">Driver (Pengemudi)</option>
                            <option value="approver">Approver (Atasan/Manajer)</option>
                            <option value="admin_ga">Admin GA (Pengelola)</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Nomor HP" />
                        <x-text-input name="phone_number" class="block mt-1 w-full" />
                    </div>

                    <div class="flex justify-end pt-4">
                        <x-primary-button>Buat User</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>