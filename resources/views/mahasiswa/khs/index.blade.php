<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar KHS Saya') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6">

            <div class="flex justify-between mb-4">
                <form method="GET" action="{{ route('mahasiswa.khs.index') }}" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari tahun akademik..."
                        class="border-gray-300 rounded-md shadow-sm" value="{{ request('search') }}">
                    <button class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cari</button>
                </form>

                <a href="{{ route('mahasiswa.khs.create') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">+ Upload
                    KHS</a>
            </div>

            {{-- Tabel Desktop --}}
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-4 py-2">#</th>
                            <th class="border px-4 py-2">Tahun Akademik</th>
                            <th class="border px-4 py-2">Semester</th>
                            <th class="border px-4 py-2">IPS Manual</th>
                            <th class="border px-4 py-2">IPS OCR</th>
                            <th class="border px-4 py-2">Status</th>
                            <th class="border px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($khs as $index => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $khs->firstItem() + $index }}</td>
                                <td class="border px-4 py-2">{{ $row->tahun_akademik }}</td>
                                <td class="border px-4 py-2">{{ $row->semester }}</td>
                                <td class="border px-4 py-2">{{ $row->ips }}</td>
                                <td class="border px-4 py-2">{{ $row->ips_ocr ?? '-' }}</td>
                                <td class="border px-4 py-2">
                                    @if($row->status_verifikasi == 'valid')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Valid</span>
                                    @elseif($row->status_verifikasi == 'invalid')
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Perlu Verifikasi</span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">Pending</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ asset('storage/' . $row->khs_file) }}" target="_blank"
                                        class="text-blue-600 hover:underline">Lihat</a>
                                    <form action="{{ route('mahasiswa.khs.destroy', $row->id) }}" method="POST"
                                        class="inline-block" id="delete-form-{{ $row->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $row->id }})"
                                            class="text-red-600 ml-2 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">Belum ada data KHS</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">{{ $khs->links() }}</div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>
</x-app-layout>
