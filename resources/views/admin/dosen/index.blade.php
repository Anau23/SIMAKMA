<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Dosen') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6">

            {{-- Pencarian dan Tombol Tambah --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                <form action="{{ route('admin.dosen.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau NIP..."
                        class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cari</button>
                </form>

                <a href="{{ route('admin.dosen.create') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                    + Tambah Dosen
                </a>
            </div>

            {{-- Tabel (Desktop) --}}
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-4 py-2 text-left">#</th>
                            <th class="border px-4 py-2 text-left">NIP</th>
                            <th class="border px-4 py-2 text-left">Nama</th>
                            <th class="border px-4 py-2 text-left">Prodi</th>
                            <th class="border px-4 py-2 text-left">Email</th>
                            <th class="border px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dosen as $index => $d)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $dosen->firstItem() + $index }}</td>
                                <td class="border px-4 py-2">{{ $d->nip }}</td>
                                <td class="border px-4 py-2">{{ $d->name }}</td>
                                <td class="border px-4 py-2">{{ $d->prodi->nama_prodi ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $d->user->email }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('admin.dosen.edit', $d->id) }}"
                                        class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">Edit</a>
                                    <form action="{{ route('admin.dosen.destroy', $d->id) }}" method="POST"
                                        class="inline-block" id="delete-form-{{ $d->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $d->id }})"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-gray-500">Tidak ada data dosen</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Card (Mobile) --}}
            <div class="md:hidden space-y-4">
                @forelse ($dosen as $d)
                    <div class="border rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-gray-800">{{ $d->name }}</h3>
                        <p class="text-gray-600 text-sm mb-1">NIP: {{ $d->nip }}</p>
                        <p class="text-gray-600 text-sm mb-1">Prodi: {{ $d->prodi->nama_prodi ?? '-' }}</p>
                        <p class="text-gray-600 text-sm mb-3">Email: {{ $d->user->email }}</p>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.dosen.edit', $d->id) }}"
                                class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">Edit</a>
                            <form action="{{ route('admin.dosen.destroy', $d->id) }}" method="POST"
                                id="delete-form-mobile-{{ $d->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $d->id }}, true)"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada data dosen</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $dosen->links() }}
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, mobile = false) {
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
                    const formId = mobile ? `delete-form-mobile-${id}` : `delete-form-${id}`;
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</x-app-layout>
