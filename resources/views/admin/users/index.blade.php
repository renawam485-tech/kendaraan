<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">Kelola User</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif
            
            <button onclick="openModal('modal-tambah')"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition mb-4">
                + Tambah User
            </button>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- FILTER BAR --}}
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center gap-3">

                        <div class="flex-1 max-w-md">
                            <label for="filter-search" class="sr-only">Cari user</label>
                            <input id="filter-search" type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama, email, atau departemen..." autocomplete="off"
                                class="w-full border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <label for="filter-role" class="sr-only">Role</label>
                        <select id="filter-role" name="role"
                            class="border-gray-300 rounded-md text-sm py-1.5 px-3 pr-8 focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin GA</option>
                            <option value="approver" {{ request('role') == 'approver' ? 'selected' : '' }}>Approver</option>
                            <option value="staff"    {{ request('role') == 'staff'    ? 'selected' : '' }}>Staff</option>
                            <option value="driver"   {{ request('role') == 'driver'   ? 'selected' : '' }}>Driver</option>
                        </select>

                        <div class="flex gap-2 border-l border-gray-300 pl-3">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                                Cari
                            </button>
                            <a href="{{ route('admin.users.index') }}"
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
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Nama</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Email</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Role</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Departemen</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No. HP</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($users as $index => $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-600 font-medium text-center">
                                        {{ $users->firstItem() + $index }}
                                    </td>

                                    {{-- Nama --}}
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </td>

                                    {{-- Email --}}
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $user->email }}</div>
                                    </td>

                                    {{-- Role --}}
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        @php
                                            $roleConfig = [
                                                'admin' => ['bg-blue-400 text-white',  'Admin GA'],
                                                'approver' => ['bg-indigo-400 text-white', 'Approver'],
                                                'driver'   => ['bg-green-400 text-white',  'Driver'],
                                                'staff'    => ['bg-gray-400 text-white',   'Staff'],
                                            ];
                                            [$roleClass, $roleLabel] = $roleConfig[$user->role] ?? ['bg-gray-300 text-gray-700', ucfirst($user->role)];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $roleClass }}">
                                            {{ $roleLabel }}
                                        </span>
                                    </td>

                                    {{-- Departemen --}}
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-600">{{ $user->department ?? '-' }}</div>
                                    </td>

                                    {{-- No HP --}}
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-600">{{ $user->phone_number ?? '-' }}</div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-3 py-3 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <button
                                            onclick="openEditModal(
                                                {{ $user->id }},
                                                '{{ addslashes($user->name) }}',
                                                '{{ addslashes($user->email) }}',
                                                '{{ $user->role }}',
                                                '{{ addslashes($user->department ?? '') }}',
                                                '{{ addslashes($user->phone_number ?? '') }}'
                                            )"
                                            class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Tidak ada user ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================================ --}}
    {{-- MODAL TAMBAH USER                --}}
    {{-- ================================ --}}
    <div id="modal-tambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Tambah User Baru</h3>
                <button type="button" onclick="closeModal('modal-tambah')"
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" class="px-6 py-4 space-y-4">
                @csrf

                <div>
                    <label for="tambah-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input id="tambah-name" name="name" type="text" value="{{ old('name') }}"
                        autocomplete="off" required
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                </div>

                <div>
                    <label for="tambah-email" class="block text-sm font-medium text-gray-700 mb-1">Email (Untuk Login)</label>
                    <input id="tambah-email" name="email" type="email" value="{{ old('email') }}"
                        autocomplete="off" required
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                </div>

                <div>
                    <label for="tambah-password" class="block text-sm font-medium text-gray-700 mb-1">Password Default</label>
                    <input id="tambah-password" name="password" type="text" value="password123"
                        autocomplete="off" required
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                    <p class="text-xs text-gray-400 mt-1">Password awal diset default, minta user menggantinya nanti.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tambah-role" class="block text-sm font-medium text-gray-700 mb-1">Peran (Role)</label>
                        <select id="tambah-role" name="role"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                            <option value="staff"    {{ old('role') == 'staff'    ? 'selected' : '' }}>Staff</option>
                            <option value="driver"   {{ old('role') == 'driver'   ? 'selected' : '' }}>Driver</option>
                            <option value="approver" {{ old('role') == 'approver' ? 'selected' : '' }}>Approver</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin GA</option>
                        </select>
                    </div>
                    <div>
                        <label for="tambah-phone" class="block text-sm font-medium text-gray-700 mb-1">
                            No. HP <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <input id="tambah-phone" name="phone_number" type="text" value="{{ old('phone_number') }}"
                            autocomplete="off"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                    </div>
                </div>

                <div>
                    <label for="tambah-department" class="block text-sm font-medium text-gray-700 mb-1">
                        Departemen <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <input id="tambah-department" name="department" type="text" value="{{ old('department') }}"
                        autocomplete="off"
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-tambah')"
                        class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <x-primary-button>Buat User</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================ --}}
    {{-- MODAL EDIT USER                  --}}
    {{-- ================================ --}}
    <div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Edit User</h3>
                <button type="button" onclick="closeModal('modal-edit')"
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            <form id="form-edit" action="" method="POST" class="px-6 py-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input id="edit-name" name="name" type="text" autocomplete="off" required
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                </div>

                <div>
                    <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="edit-email" name="email" type="email" autocomplete="off" required
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit-role" class="block text-sm font-medium text-gray-700 mb-1">Peran (Role)</label>
                        <select id="edit-role" name="role"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                            <option value="staff">Staff</option>
                            <option value="driver">Driver</option>
                            <option value="approver">Approver</option>
                            <option value="admin">Admin GA</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit-phone" class="block text-sm font-medium text-gray-700 mb-1">
                            No. HP <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <input id="edit-phone" name="phone_number" type="text" autocomplete="off"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                    </div>
                </div>

                <div>
                    <label for="edit-department" class="block text-sm font-medium text-gray-700 mb-1">
                        Departemen <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <input id="edit-department" name="department" type="text" autocomplete="off"
                        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full text-sm">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-edit')"
                        class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <x-primary-button>Update User</x-primary-button>
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

        function openEditModal(id, name, email, role, department, phone) {
            document.getElementById('form-edit').action = `/admin/users/${id}`;
            document.getElementById('edit-name').value       = name;
            document.getElementById('edit-email').value      = email;
            document.getElementById('edit-department').value = department;
            document.getElementById('edit-phone').value      = phone;

            const roleSelect = document.getElementById('edit-role');
            for (let opt of roleSelect.options) opt.selected = opt.value === role;

            openModal('modal-edit');
        }

        // Tutup modal klik backdrop
        document.querySelectorAll('[id^="modal-"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        @if($errors->any() && old('_method') === null)
            openModal('modal-tambah');
        @endif

        @if($errors->any() && old('_method') === 'PUT')
            openModal('modal-edit');
        @endif
    </script>

</x-app-layout>