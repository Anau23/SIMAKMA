<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">

                @php
                    $user = Auth::user();
                @endphp

                {{-- Jika Admin --}}
                @if ($user->isAdmin())
                    <h3 class="text-lg font-semibold mb-4">Selamat datang, {{ $user->name }} (Admin)</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-100 p-5 rounded-lg shadow text-center">
                            <h4 class="text-gray-700 font-bold text-lg">Jumlah Mahasiswa</h4>
                            <p class="text-3xl font-extrabold text-blue-600 mt-2">128</p>
                        </div>
                        <div class="bg-green-100 p-5 rounded-lg shadow text-center">
                            <h4 class="text-gray-700 font-bold text-lg">Jumlah Dosen</h4>
                            <p class="text-3xl font-extrabold text-green-600 mt-2">99</p>
                        </div>
                        <div class="bg-purple-100 p-5 rounded-lg shadow text-center">
                            <h4 class="text-gray-700 font-bold text-lg">Jumlah Mata Kuliah</h4>
                            <p class="text-3xl font-extrabold text-purple-600 mt-2">55</p>
                        </div>
                    </div>

                {{-- Jika Mahasiswa --}}
                @elseif ($user->isMahasiswa())
                    @php
                        $mhs = \App\Models\Mahasiswa::with(['dosen', 'prodi'])
                            ->where('user_id', $user->id)
                            ->first();
                    @endphp

                    <h3 class="text-lg font-semibold mb-4">Selamat datang, {{ $user->name }} (Mahasiswa)</h3>

                    @if ($mhs)
                        <table class="table-auto w-full border-collapse border border-gray-300">
                            <tbody>
                                <tr class="border-b">
                                    <th class="text-left p-3 w-1/3 bg-gray-100">NIM</th>
                                    <td class="p-3">{{ $mhs->nim }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left p-3 bg-gray-100">Dosen Wali</th>
                                    <td class="p-3">{{ $mhs->dosen->name ?? '-' }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left p-3 bg-gray-100">Program Studi</th>
                                    <td class="p-3">{{ $mhs->prodi->name ?? '-' }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left p-3 bg-gray-100">Angkatan</th>
                                    <td class="p-3">{{ $mhs->angkatan }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left p-3 bg-gray-100">Alamat</th>
                                    <td class="p-3">{{ $mhs->alamat }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left p-3 bg-gray-100">Gender</th>
                                    <td class="p-3">{{ $mhs->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-left p-3 bg-gray-100">Tahun Akademik</th>
                                    <td class="p-3">{{ $mhs->tahun_akademik }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-600">Data mahasiswa tidak ditemukan.</p>
                    @endif

                {{-- Jika Dosen --}}
                @elseif ($user->isDosen())
                    @php
                        $dsn = \App\Models\Dosen::where('user_id', $user->id)->first();
                    @endphp

                    <h3 class="text-lg font-semibold mb-4">Selamat datang, {{ $user->name }} (Dosen)</h3>

                    @if ($dsn)
                        <table class="table-auto w-full border-collapse border border-gray-300">
                            <tbody>
                                <tr class="border-b">
                                    <th class="text-left p-3 w-1/3 bg-gray-100">NIP</th>
                                    <td class="p-3">{{ $dsn->nip }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left p-3 bg-gray-100">Nama</th>
                                    <td class="p-3">{{ $dsn->name }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left p-3 bg-gray-100">Alamat</th>
                                    <td class="p-3">{{ $dsn->alamat }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left p-3 bg-gray-100">Gender</th>
                                    <td class="p-3">{{ $dsn->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-left p-3 bg-gray-100">Agama</th>
                                    <td class="p-3">{{ $dsn->religion }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-600">Data dosen tidak ditemukan.</p>
                    @endif

                @else
                    <p class="text-gray-600">Role pengguna tidak dikenali.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
