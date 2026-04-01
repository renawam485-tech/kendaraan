<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <x-input-label value="Nama Lengkap" />
                        <x-text-input name="name" class="block mt-1 w-full" :value="$user->name" required />
                    </div>

                    <div>
                        <x-input-label value="Email" />
                        <x-text-input name="email" type="email" class="block mt-1 w-full" :value="$user->email" required />
                    </div>

                    <div>
                        <x-input-label value="Peran (Role)" />
                        <select name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1 bg-yellow-50">
                            <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="driver" {{ $user->role == 'driver' ? 'selected' : '' }}>Driver</option>
                            <option value="approver" {{ $user->role == 'approver' ? 'selected' : '' }}>Approver</option>
                            <option value="admin_ga" {{ $user->role == 'admin_ga' ? 'selected' : '' }}>Admin GA</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Departemen (Opsional)" />
                            <x-text-input name="department" class="block mt-1 w-full" :value="$user->department" />
                        </div>
                        <div>
                            <x-input-label value="Nomor HP (Opsional)" />
                            <x-text-input name="phone_number" class="block mt-1 w-full" :value="$user->phone_number" />
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 space-x-2">
                        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Batal</a>
                        <x-primary-button>Update User</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>