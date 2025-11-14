<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kartu Rencana Studi (KRS)') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- Statistik KRS --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-book text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Mata Kuliah</p>
                            <p class="text-2xl font-bold text-blue-800">{{ $krs->count() + $tempKRS->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-600">Total SKS</p>
                            <p class="text-2xl font-bold text-green-800">{{ $totalSKS }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-yellow-600">Menunggu</p>
                            <p class="text-2xl font-bold text-yellow-800">
                                {{ $krs->where('status', 'pending')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg mr-3">
                            <i class="fas fa-user-graduate text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-lg text-black">
                                Tahun Akademik: {{ auth()->user()->mahasiswas->tahun_akademik ?? '-' }}
                            </p>
                            <p class="text-2xl font-bold text-purple-800">
                                Semester {{ $semester }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Form Input KRS --}}
                <div class="lg:col-span-1">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Mata Kuliah</h3>
                        @if ($khsValid)
                            <div class="mb-4 bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">
                                <svg class="w-4 h-4 mr-2 inline-block text-green-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0 1 1 0 002 0zm-1 3a1 1 0 00-1 1v4a1 1 0 102 0v-4a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                IP Semester Anda: <strong>{{ number_format($ips, 2) }}</strong>.
                                Anda dapat mengambil maksimal
                                <strong>{{ $maxSKS }} SKS</strong> pada semester ini.
                            </div>
                        @else
                            <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-6">
                                <strong>Perhatian!</strong> Anda belum memiliki KHS semester ini yang
                                <b>terverifikasi</b>.<br>
                                Silakan unggah KHS terlebih dahulu agar dapat mengisi KRS.
                                <div class="mt-3">
                                    <a href="{{ route('mahasiswa.khs.create') }}"
                                        class="inline-block px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
                                        <i class="fas fa-upload mr-2"></i>Unggah KHS Semester Ini
                                    </a>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('mahasiswa.krs.addTemp') }}" method="POST"
                            @if (!$khsValid || $hasFinalized) class="opacity-50 pointer-events-none" @endif>
                            @csrf

                            <div class="mb-4">
                                <label for="matkul_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Mata Kuliah <span class="text-red-500">*</span>
                                </label>
                                <select name="matkul_id" id="matkul_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach ($availableMatkul as $matkul)
                                        <option value="{{ $matkul->id }}" data-sks="{{ $matkul->sks }}">
                                            {{ $matkul->kode_mk }} - {{ $matkul->name }} ({{ $matkul->sks }} SKS)
                                        </option>
                                    @endforeach
                                </select>
                                @error('matkul_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">
                                    <span id="selected-sks-info" class="hidden">
                                        Mata kuliah terpilih: <span id="sks-value" class="font-semibold">0 SKS</span>
                                    </span>
                                    <span id="no-selection-info">Pilih mata kuliah untuk melihat detail</span>
                                </p>
                            </div>

                            <button type="submit" @disabled(!$khsValid || $hasFinalized)
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 font-medium">
                                <i class="fas fa-plus mr-2"></i>Tambah ke Daftar
                            </button>
                        </form>

                        {{-- Informasi Mahasiswa --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Mahasiswa</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nama:</span>
                                    <span class="font-medium">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">NIM:</span>
                                    <span>{{ auth()->user()->mahasiswas->nim }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Program Studi:</span>
                                    <span>{{ auth()->user()->mahasiswas->prodi->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dosen Wali:</span>
                                    <span>{{ auth()->user()->mahasiswas->dosen->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Daftar KRS --}}
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Daftar Mata Kuliah KRS</h3>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                Total: {{ $totalSKS }} SKS
                            </span>
                        </div>

                        @if ($tempKRS->count() > 0 && !$hasFinalized)
                            <div class="mb-4 bg-blue-50 border border-blue-300 text-blue-800 px-4 py-3 rounded flex justify-between items-center">
                                <span>
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Anda memiliki <strong>{{ $tempKRS->count() }}</strong> mata kuliah yang belum difinalisasi
                                </span>
                                <form action="{{ route('mahasiswa.krs.finalize') }}" method="POST" id="finalize-form">
                                    @csrf
                                    <button type="button" onclick="confirmFinalize()"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                        <i class="fas fa-check-double mr-1"></i>Finalisasi KRS
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($krs->count() > 0 || $tempKRS->count() > 0)
                            {{-- Tabel (Desktop) --}}
                            <div class="overflow-x-auto hidden md:block">
                                <table class="min-w-full border border-gray-200 text-sm text-gray-600">
                                    <thead class="bg-gray-100 text-gray-700">
                                        <tr>
                                            <th class="border px-4 py-3 text-left">#</th>
                                            <th class="border px-4 py-3 text-left">Mata Kuliah</th>
                                            <th class="border px-4 py-3 text-left">Dosen</th>
                                            <th class="border px-4 py-3 text-center">SKS</th>
                                            <th class="border px-4 py-3 text-left">Status</th>
                                            <th class="border px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- KRS Temporary (Belum Finalisasi) --}}
                                        @foreach ($tempKRS as $index => $item)
                                            <tr class="hover:bg-gray-50 bg-yellow-50">
                                                <td class="border px-4 py-3">{{ $index + 1 }}</td>
                                                <td class="border px-4 py-3">
                                                    <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                                    <div class="text-xs text-gray-500">Kode: {{ $item->kode_mk }}</div>
                                                    <div class="text-xs text-gray-500">Semester: {{ $item->semester }}</div>
                                                </td>
                                                <td class="border px-4 py-3">
                                                    {{ $item->dosen->name ?? '-' }}
                                                </td>
                                                <td class="border px-4 py-3 text-center">
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                        {{ $item->sks }}
                                                    </span>
                                                </td>
                                                <td class="border px-4 py-3">
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                                        Belum Finalisasi
                                                    </span>
                                                </td>
                                                <td class="border px-4 py-3 text-center">
                                                    <form action="{{ route('mahasiswa.krs.removeTemp', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                                                            <i class="fas fa-trash mr-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- KRS Tersimpan (Sudah Finalisasi) --}}
                                        @foreach ($krs as $index => $item)
                                            <tr class="hover:bg-gray-50">
                                                <td class="border px-4 py-3">{{ $tempKRS->count() + $index + 1 }}</td>
                                                <td class="border px-4 py-3">
                                                    <div class="font-medium text-gray-900">{{ $item->matkul->name }}</div>
                                                    <div class="text-xs text-gray-500">Kode: {{ $item->matkul->kode_mk }}</div>
                                                    <div class="text-xs text-gray-500">Semester: {{ $item->matkul->semester }}</div>
                                                </td>
                                                <td class="border px-4 py-3">
                                                    {{ $item->matkul->dosen->name ?? '-' }}
                                                </td>
                                                <td class="border px-4 py-3 text-center">
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                        {{ $item->matkul->sks }}
                                                    </span>
                                                </td>
                                                <td class="border px-4 py-3">
                                                    @if ($item->status == 'pending')
                                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                            Terkirim
                                                        </span>
                                                    @elseif($item->status == 'aktif')
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                            Disetujui
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                                            Ditolak
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="border px-4 py-3 text-center">
                                                    <span class="text-gray-400 text-sm">-</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Card (Mobile) --}}
                            <div class="md:hidden space-y-4">
                                {{-- Temporary KRS --}}
                                @foreach ($tempKRS as $item)
                                    <div class="border rounded-lg p-4 shadow-sm bg-yellow-50">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-800">{{ $item->name }}</h4>
                                                <p class="text-gray-600 text-sm">Kode: {{ $item->kode_mk }}</p>
                                            </div>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                {{ $item->sks }} SKS
                                            </span>
                                        </div>

                                        <div class="space-y-2 text-sm mb-3">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Dosen:</span>
                                                <span>{{ $item->dosen->name ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Semester:</span>
                                                <span>{{ $item->semester }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Status:</span>
                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                                    Belum Finalisasi
                                                </span>
                                            </div>
                                        </div>

                                        <form action="{{ route('mahasiswa.krs.removeTemp', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endforeach

                                {{-- Saved KRS --}}
                                @foreach ($krs as $item)
                                    <div class="border rounded-lg p-4 shadow-sm">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-800">{{ $item->matkul->name }}</h4>
                                                <p class="text-gray-600 text-sm">Kode: {{ $item->matkul->kode_mk }}</p>
                                            </div>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                {{ $item->matkul->sks }} SKS
                                            </span>
                                        </div>

                                        <div class="space-y-2 text-sm mb-3">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Dosen:</span>
                                                <span>{{ $item->matkul->dosen->name ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Semester:</span>
                                                <span>{{ $item->matkul->semester }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Status:</span>
                                                @if ($item->status == 'pending')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                        Terkirim
                                                    </span>
                                                @elseif($item->status == 'aktif')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                        Disetujui
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                                        Ditolak
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-book-open text-4xl text-gray-300 mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-500 mb-2">Belum ada mata kuliah dalam KRS</h4>
                                <p class="text-gray-400 text-sm">Silakan tambah mata kuliah menggunakan form di samping</p>
                            </div>
                        @endif

                        {{-- Informasi Tambahan --}}
                        @if ($krs->count() > 0 || $tempKRS->count() > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:justify-between gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600">
                                            <span class="font-medium">Catatan:</span>
                                            Mata kuliah yang belum difinalisasi masih dapat dihapus. <br> Setelah finalisasi, KRS akan terkirim ke dosen wali.
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-gray-800 font-medium">
                                            Total SKS: {{ $totalSKS }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show SKS information when selecting a course
        document.getElementById('matkul_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const sks = selectedOption.getAttribute('data-sks');
            const sksValue = document.getElementById('sks-value');
            const selectedSksInfo = document.getElementById('selected-sks-info');
            const noSelectionInfo = document.getElementById('no-selection-info');

            if (sks) {
                sksValue.textContent = sks + ' SKS';
                selectedSksInfo.classList.remove('hidden');
                noSelectionInfo.classList.add('hidden');
            } else {
                selectedSksInfo.classList.add('hidden');
                noSelectionInfo.classList.remove('hidden');
            }
        });

        function confirmFinalize() {
            Swal.fire({
                title: 'Finalisasi KRS?',
                text: "Setelah finalisasi, Anda tidak dapat menghapus mata kuliah. KRS akan dikirim ke dosen wali untuk disetujui.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Finalisasi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('finalize-form').submit();
                }
            });
        }
    </script>
</x-app-layout>
