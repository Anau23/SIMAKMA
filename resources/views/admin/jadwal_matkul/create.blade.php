<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Jadwal Mata Kuliah') }}</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">

            <form action="{{ route('admin.jadwal_matkul.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700">Kelas</label>
                    <select name="kelas_id" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->name }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Mata Kuliah</label>
                    <select name="matkul_id" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach ($matkuls as $m)
                            <option value="{{ $m->id }}" {{ old('matkul_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}</option>
                        @endforeach
                    </select>
                    @error('matkul_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Hari</label>
                    <input type="text" name="hari" value="{{ old('hari') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('hari')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('jam_mulai')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('jam_selesai')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end mt-4 gap-2">
                    <a href="{{ route('admin.jadwal_matkul.index') }}"
                        class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</a>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
