<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Mahasiswa') }}
        </h2>
    </x-slot>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto bg-white shadow rounded-lg p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                <form action="{{ route('admin.mahasiswa.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama/NIM/Prodi..."
                        class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cari</button>
                </form>
                <a href="{{ route('admin.mahasiswa.create') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                    Tambah Mahasiswa
                </a>
            </div>
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full border border-gray-200 text-sm text-gray-700">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left">#</th>
                            <th class="border px-3 py-2 text-left">Nama</th>
                            <th class="border px-3 py-2 text-left">NIM</th>
                            <th class="border px-3 py-2 text-left">Prodi</th>
                            <th class="border px-3 py-2 text-left">Dosen Wali</th>
                            <th class="border px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswa as $index => $m)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2">{{ $mahasiswa->firstItem() + $index }}</td>
                                <td class="border px-3 py-2">{{ $m->user->name }}</td>
                                <td class="border px-3 py-2">{{ $m->nim }}</td>
                                <td class="border px-3 py-2">{{ $m->prodi->name ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $m->dosen->name ?? '-' }}</td>
                                <td class="border px-3 py-2 text-center">
                                    <button
                                        class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">
                                        <a href="{{ route('admin.mahasiswa.edit', $m->id) }}">Edit</a>
                                    </button>
                                    <form action="{{ route('admin.mahasiswa.destroy', $m->id) }}" method="POST"
                                        class="inline-block" id="delete-form-{{ $m->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $m->id }})"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="md:hidden space-y-4">
                @forelse ($mahasiswa as $m)
                    <div class="border rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-gray-800">{{ $m->user->name }}</h3>
                        <p class="text-sm text-gray-600">NIM: {{ $m->nim }}</p>
                        <p class="text-sm text-gray-600">Prodi: {{ $m->prodi->name ?? '-' }}</p>
                        <p class="text-sm text-gray-600 mb-2">Dosen Wali: {{ $m->dosenWali->nama_dosen ?? '-' }}</p>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">
                                <a href="{{ route('admin.mahasiswa.edit', $m->id) }}">Edit</a>
                            </button>
                            <form action="{{ route('admin.mahasiswa.destroy', $m->id) }}" method="POST"
                                id="delete-form-mobile-{{ $m->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $m->id }}, true)"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada data</p>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $mahasiswa->appends(['search' => $search])->links() }}
            </div>
        </div>
    </div>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

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
