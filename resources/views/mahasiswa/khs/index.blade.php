<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit KHS') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Edit KHS Semester {{ $kh->semester }}</h3>
                    <p class="text-sm text-gray-600">Update data KHS dan file jika diperlukan</p>
                </div>

                <form action="{{ route('mahasiswa.khs.update', $kh->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tahun Akademik --}}
                        <div>
                            <label for="tahun_akademik" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Akademik <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="tahun_akademik" 
                                   id="tahun_akademik"
                                   value="{{ old('tahun_akademik', $kh->tahun_akademik) }}"
                                   min="2000" 
                                   max="2030"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            @error('tahun_akademik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Semester --}}
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                                Semester <span class="text-red-500">*</span>
                            </label>
                            <select name="semester" 
                                    id="semester"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">Pilih Semester</option>
                                @for($i = 1; $i <= 14; $i++)
                                <option value="{{ $i }}" {{ old('semester', $kh->semester) == $i ? 'selected' : '' }}>
                                    Semester {{ $i }}
                                </option>
                                @endfor
                            </select>
                            @error('semester')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="ips" class="block text-sm font-medium text-gray-700 mb-2">
                            Indeks Prestasi Semester (IPS) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="ips" 
                               id="ips"
                               value="{{ old('ips', $kh->ips) }}"
                               step="0.01"
                               min="0" 
                               max="4"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: 3.75"
                               required>
                        @error('ips')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="ips_ocr" class="block text-sm font-medium text-gray-700 mb-2">
                            IPS OCR (Opsional)
                        </label>
                        <input type="number" 
                               name="ips_ocr" 
                               id="ips_ocr"
                               value="{{ old('ips_ocr', $kh->ips_ocr) }}"
                               step="0.01"
                               min="0" 
                               max="4"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nilai IPS dari OCR (jika ada)">
                        @error('ips_ocr')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="khs_file" class="block text-sm font-medium text-gray-700 mb-2">
                            File KHS (Opsional)
                        </label>
                        
                        {{-- Current File Info --}}
                        @if($kh->khs_file)
                        <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-file-pdf text-green-500 text-xl"></i>
                                    <div>
                                        <p class="text-sm font-medium text-green-900">File saat ini: {{ basename($kh->khs_file) }}</p>
                                        <p class="text-xs text-green-700">
                                            <a href="{{ route('mahasiswa.khs.download', $kh->id) }}" class="hover:underline">
                                                Download file saat ini
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <input type="file" 
                               name="khs_file" 
                               id="khs_file"
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-sm text-gray-500">
                            Kosongkan jika tidak ingin mengubah file. Format: PDF, JPG, JPEG, PNG (Maksimal 2MB)
                        </p>
                        @error('khs_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New File Preview --}}
                    <div id="file-preview" class="mt-4 hidden">
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-file-pdf text-blue-500 text-xl"></i>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900" id="file-name">File baru akan menggantikan file lama</p>
                                        <p class="text-xs text-blue-700" id="file-size"></p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearFile()" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('mahasiswa.khs.show', $kh->id) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-150 font-medium">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 font-medium">
                            <i class="fas fa-save mr-2"></i>Update KHS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // File preview functionality
        document.getElementById('khs_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');

            if (file) {
                fileName.textContent = 'File baru: ' + file.name;
                fileSize.textContent = formatFileSize(file.size);
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        });

        function clearFile() {
            document.getElementById('khs_file').value = '';
            document.getElementById('file-preview').classList.add('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // IPS validation
        document.getElementById('ips').addEventListener('input', function(e) {
            let value = parseFloat(e.target.value);
            if (value < 0) e.target.value = 0;
            if (value > 4) e.target.value = 4;
        });
    </script>
</x-app-layout>