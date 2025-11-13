<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.mahasiswa.update', $mahasiswa->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- NIM --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                    @error('nim') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nama --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Nama Mahasiswa</label>
                    <input type="text" name="name" value="{{ old('name', $mahasiswa->name) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                    @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $mahasiswa->email) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                    @error('email') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Program Studi --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Program Studi</label>
                    <select name="prodi_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">-- Pilih Prodi --</option>
                        @foreach ($prodi as $p)
                            <option value="{{ $p->id }}" {{ old('prodi_id', $mahasiswa->prodi_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('prodi_id') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tombol --}}
                <div class="flex justify-end mt-6">
                    <a href="{{ route('admin.mahasiswa.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 mr-2">Batal</a>
                    <button type="submit"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
