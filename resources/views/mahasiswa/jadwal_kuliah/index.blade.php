<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Jadwal Kuliah Saya') }}</h2>
</x-slot>

<div class="py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        {{-- Info Semester --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-center gap-4">
            <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
            <div>
                <h3 class="font-semibold text-blue-900">Semester {{ $semester }}</h3>
                <p class="text-sm text-blue-700">Tahun Akademik: {{ $tahunAkademik }}</p>
            </div>
        </div>

        {{-- Search --}}
        <form class="flex gap-2 mb-4" method="GET">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari mata kuliah atau kelas..."
                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-full md:w-1/3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cari</button>
        </form>

        @php
            $hariUrutan = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $jadwalPerHari = [];
            foreach($jadwals as $jadwal){
                $jadwalPerHari[$jadwal->jadwal_matkul->hari][] = $jadwal;
            }
        @endphp

        @if($jadwals->count() > 0)
            {{-- Ringkasan --}}
            <div class="mt-6 bg-white shadow rounded-lg p-6 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <p class="text-sm text-green-600 font-medium">Total Mata Kuliah</p>
                    <p class="text-3xl font-bold text-green-900">{{ $totalJadwal }}</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <p class="text-sm text-blue-600 font-medium">Total SKS</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $totalSKS }}</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                    <p class="text-sm text-purple-600 font-medium">Hari Kuliah</p>
                    <p class="text-3xl font-bold text-purple-900">{{ count($jadwalPerHari) }}</p>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="overflow-x-auto hidden md:block bg-white shadow rounded-lg mb-6">
                <table class="min-w-full border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-4 py-2 text-left">Hari</th>
                            <th class="border px-4 py-2 text-left">Mata Kuliah</th>
                            <th class="border px-4 py-2 text-left">Kode</th>
                            <th class="border px-4 py-2 text-left">Dosen</th>
                            <th class="border px-4 py-2 text-left">Kelas</th>
                            <th class="border px-4 py-2 text-left">SKS</th>
                            <th class="border px-4 py-2 text-left">Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwals as $jadwal)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-2">{{ $jadwal->jadwal_matkul->hari }}</td>
                            <td class="border px-4 py-2">{{ $jadwal->jadwal_matkul->matkul->name }}</td>
                            <td class="border px-4 py-2">{{ $jadwal->jadwal_matkul->matkul->kode_mk }}</td>
                            <td class="border px-4 py-2">{{ $jadwal->jadwal_matkul->matkul->dosen->name ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $jadwal->jadwal_matkul->kela->name ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $jadwal->jadwal_matkul->matkul->sks }}</td>
                            <td class="border px-4 py-2">
                                {{ $jadwal->jadwal_matkul->jam_mulai->format('H:i') }} - {{ $jadwal->jadwal_matkul->jam_selesai->format('H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-4">
                @foreach($jadwals as $jadwal)
                <div class="border border-gray-200 rounded-lg p-4 bg-white shadow hover:shadow-md transition">
                    <h4 class="font-semibold text-gray-900">{{ $jadwal->jadwal_matkul->matkul->name }}</h4>
                    <p class="text-gray-600 text-sm">Kode: {{ $jadwal->jadwal_matkul->matkul->kode_mk }}</p>
                    <p class="text-gray-600 text-sm">Dosen: {{ $jadwal->jadwal_matkul->matkul->dosen->name ?? '-' }}</p>
                    <p class="text-gray-600 text-sm">Kelas: {{ $jadwal->jadwal_matkul->kela->name ?? '-' }}</p>
                    <p class="text-gray-600 text-sm">SKS: {{ $jadwal->jadwal_matkul->matkul->sks }}</p>
                    <p class="text-gray-600 text-sm">Hari: {{ $jadwal->jadwal_matkul->hari }}</p>
                    <p class="text-gray-600 text-sm">Jam: {{ $jadwal->jadwal_matkul->jam_mulai->format('H:i') }} - {{ $jadwal->jadwal_matkul->jam_selesai->format('H:i') }}</p>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                @php
                    $totalPages = ceil($totalJadwal / $perPage);
                @endphp
                @if($totalPages > 1)
                <div class="flex justify-center gap-2">
                    @for($i=1;$i<=$totalPages;$i++)
                        <a href="{{ request()->fullUrlWithQuery(['page'=>$i]) }}"
                           class="px-3 py-1 rounded {{ $currentPage==$i?'bg-blue-600 text-white':'bg-gray-200 text-gray-700' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
                @endif
            </div>

        @else
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Jadwal Kuliah</h3>
                <p class="text-gray-500 mb-6">Jadwal kuliah akan muncul setelah KRS Anda divalidasi oleh dosen wali.</p>
                <a href="{{ route('mahasiswa.krs.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke KRS
                </a>
            </div>
        @endif

    </div>
</div>
</x-app-layout>
