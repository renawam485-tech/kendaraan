<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Mobil Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Nama Mobil" />
                            <x-text-input name="name" class="block mt-1 w-full" placeholder="Ex: Avanza Veloz" required />
                        </div>
                        <div>
                            <x-input-label value="Plat Nomor" />
                            <x-text-input name="license_plate" class="block mt-1 w-full" placeholder="Ex: B 1234 CD" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Tipe" />
                            <select name="type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                <option value="MPV">MPV (Mobil Keluarga)</option>
                                <option value="SUV">SUV (Sport)</option>
                                <option value="Sedan">Sedan</option>
                                <option value="Box">Mobil Box</option>
                                <option value="LCGC">LCGC</option>
                                <option value="EV">EV (Mobil Listrik)</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Status Awal" />
                            <select name="asset_status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                <option value="available">Available (Siap Pakai)</option>
                                <option value="maintenance">Maintenance (Bengkel)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Catatan Kondisi (Opsional)" />
                        <textarea name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" rows="2"></textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <x-primary-button>Simpan Mobil</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>