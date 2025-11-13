<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Mata Kuliah') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.matkul.update', $matkul->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-gray-700">Kode Mata Kuliah</label>
                    <input type="text" name="kode_mk" value="{{ $matkul->kode_mk }}"
                        class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block text-gray-700">Nama Mata Kuliah</label>
                    <input type="text" name="name" value="{{ $matkul->name }}"
                        class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700">SKS</label>
                        <input type="number" name="sks" value="{{ $matkul->sks }}"
                            class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-gray-700">Semester</label>
                        <input type="number" name="semester" value="{{ $matkul->semester }}"
                            class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700">Program Studi</label>
                    <select name="prodi_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach ($prodi as $p)
                            <option value="{{ $p->id }}" @selected($matkul->prodi_id == $p->id)>{{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700">Dosen Pengampu</label>
                    <select name="dosen_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->id }}" @selected($matkul->dosen_id == $d->id)>{{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route('admin.matkul.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 mr-2">Batal</a>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
