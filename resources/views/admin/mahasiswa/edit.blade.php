<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Mata Kuliah') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.matkul.update', $matkul->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Kode Mata Kuliah --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Kode Mata Kuliah</label>
                    <input type="text" name="kode_mk" value="{{ old('kode_mk', $matkul->kode_mk) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('kode_mk')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Mata Kuliah --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Nama Mata Kuliah</label>
                    <input type="text" name="name" value="{{ old('name', $matkul->name) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SKS --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Jumlah SKS</label>
                    <input type="number" name="sks" value="{{ old('sks', $matkul->sks) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('sks')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Semester --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Semester</label>
                    <input type="number" name="semester" value="{{ old('semester', $matkul->semester) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('semester')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prodi --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Program Studi</label>
                    <select name="prodi_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Prodi --</option>
                        @foreach ($prodi as $p)
                            <option value="{{ $p->id }}" {{ old('prodi_id', $matkul->prodi_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('prodi_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dosen Pengampu --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Dosen Pengampu</label>
                    <select name="dosen_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Dosen --</option>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->id }}" {{ old('dosen_id', $matkul->dosen_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->nama_dosen }}
                            </option>
                        @endforeach
                    </select>
                    @error('dosen_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('admin.matkul.index') }}"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Batal</a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
