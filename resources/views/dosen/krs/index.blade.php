<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data KRS Mahasiswa Bimbingan') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6">

            {{-- Pencarian --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                <form action="{{ route('dosen.krs.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau NIM mahasiswa..."
                        class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cari</button>
                </form>
            </div>

            {{-- Tabel (Desktop) --}}
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-4 py-2 text-left">#</th>
                            <th class="border px-4 py-2 text-left">Nama Mahasiswa</th>
                            <th class="border px-4 py-2 text-left">NIM</th>
                            <th class="border px-4 py-2 text-left">Jumlah KRS</th>
                            <th class="border px-4 py-2 text-left">Status KRS</th>
                            <th class="border px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($krs->groupBy('mahasiswa_id') as $index => $list)
                            @php $mahasiswa = $list->first()->mahasiswa; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-4 py-2">{{ $mahasiswa->user->name }}</td>
                                <td class="border px-4 py-2">{{ $mahasiswa->nim }}</td>
                                <td class="border px-4 py-2">{{ $list->count() }} matkul</td>
                                <td class="border px-4 py-2 text-center">
                                    @php
                                        $statusList = $list->pluck('status')->unique();
                                        if ($statusList->count() === 1) {
                                            $status = $statusList->first();
                                        } else {
                                            $status = 'campuran';
                                        }
                                    @endphp

                                    @if ($status === 'aktif')
                                        <span
                                            class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                            Disetujui
                                        </span>
                                    @elseif ($status === 'pending')
                                        <span
                                            class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                            Menunggu Persetujuan
                                        </span>
                                    @elseif ($status === 'ditolak')
                                        <span
                                            class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('dosen.krs.show', $mahasiswa->id) }}"
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-gray-500">Tidak ada data KRS</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Card (Mobile) --}}
            <div class="md:hidden space-y-4">
                @forelse ($krs->groupBy('mahasiswa_id') as $list)
                    @php $mahasiswa = $list->first()->mahasiswa; @endphp
                    <div class="border rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800">{{ $mahasiswa->user->name }}</h3>

                            @php
                                $statusList = $list->pluck('status')->unique();
                                if ($statusList->count() === 1) {
                                    $status = $statusList->first();
                                } else {
                                    $status = 'campuran';
                                }
                            @endphp

                            @if ($status === 'aktif')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    Disetujui
                                </span>
                            @elseif ($status === 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    Menunggu Persetujuan
                                </span>
                            @elseif ($status === 'ditolak')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    Sebagian Disetujui
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm mb-1">NIM: {{ $mahasiswa->nim }}</p>
                        <p class="text-gray-600 text-sm mb-1">Jumlah KRS: {{ $list->count() }} matkul</p>
                        <a href="{{ route('dosen.krs.show', $mahasiswa->id) }}"
                            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Detail</a>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada data KRS</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $krs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
