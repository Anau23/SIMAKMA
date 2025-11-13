<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Unggah Kartu Hasil Studi (KHS)') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
            <form action="{{ route('mahasiswa.khs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">IP Semester</label>
                    <input type="number" name="ips" step="0.01" min="0" max="4"
                        value="{{ old('ips') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                    <p class="text-sm text-gray-500 mt-1">Masukkan IP Semester Anda (contoh: 3.75)</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Upload File KHS</label>
                    <input type="file" name="khs_file" accept="image/png,image/jpeg,image/jpg"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i>
                        Upload screenshot/foto KHS dalam format <strong>PNG, JPG, atau JPEG</strong> (Max: 5MB)
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        💡 Tips: Pastikan foto jelas dan IP Semester terlihat dengan jelas untuk verifikasi otomatis
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('mahasiswa.krs.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 mr-2">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-upload mr-1"></i>Unggah KHS
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
