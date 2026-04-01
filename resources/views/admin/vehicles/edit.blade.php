<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Mobil: {{ $vehicle->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form 
                    action="{{ route('admin.vehicles.update', $vehicle) }}" 
                    method="POST" 
                    class="space-y-4"
                >
                    @csrf
                    @method('PUT')

                    {{-- Nama & Plat --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Nama Mobil" />
                            <x-text-input 
                                name="name" 
                                class="block mt-1 w-full"
                                :value="old('name', $vehicle->name)"
                                required 
                            />
                        </div>

                        <div>
                            <x-input-label value="Plat Nomor" />
                            <x-text-input 
                                name="license_plate" 
                                class="block mt-1 w-full"
                                :value="old('license_plate', $vehicle->license_plate)"
                                required 
                            />
                        </div>
                    </div>

                    {{-- Tipe & Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Tipe Mobil" />
                            <select 
                                name="type" 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1"
                            >
                                @foreach (['MPV','SUV','Sedan','Box','LCGC','EV'] as $type)
                                    <option 
                                        value="{{ $type }}" 
                                        {{ old('type', $vehicle->type) === $type ? 'selected' : '' }}
                                    >
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label value="Status Aset" />
                            <select 
                                name="asset_status" 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1 bg-yellow-50"
                            >
                                <option value="available" {{ $vehicle->asset_status === 'available' ? 'selected' : '' }}>
                                    Available (Siap Pakai)
                                </option>
                                <option value="maintenance" {{ $vehicle->asset_status === 'maintenance' ? 'selected' : '' }}>
                                    Maintenance (Bengkel)
                                </option>
                                <option value="disposal" {{ $vehicle->asset_status === 'disposal' ? 'selected' : '' }}>
                                    Disposal (Tidak Aktif)
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <x-input-label value="Catatan Kondisi (Opsional)" />
                        <textarea 
                            name="notes"
                            rows="3"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                        >{{ old('notes', $vehicle->notes) }}</textarea>
                    </div>

                    {{-- Action --}}
                    <div class="flex justify-end pt-4 space-x-2">
                        <a 
                            href="{{ route('admin.vehicles.index') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                        >
                            Batal
                        </a>
                        <x-primary-button>
                            Update Mobil
                        </x-primary-button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
