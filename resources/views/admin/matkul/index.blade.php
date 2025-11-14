<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Mata Kuliah') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                <form action="{{ route('admin.matkul.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari nama mata kuliah..."
                        class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cari</button>
                </form>
                <a href="{{ route('admin.matkul.create') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                    Tambah Mata Kuliah
                </a>
            </div>
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-4 py-2 text-left">#</th>
                            <th class="border px-4 py-2 text-left">Kode MK</th>
                            <th class="border px-4 py-2 text-left">Nama Mata Kuliah</th>
                            <th class="border px-4 py-2 text-left">Prodi</th>
                            <th class="border px-4 py-2 text-left">Dosen Pengampu</th>
                            <th class="border px-4 py-2 text-center">SKS</th>
                            <th class="border px-4 py-2 text-center">Semester</th>
                            <th class="border px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($matkul as $index => $m)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $matkul->firstItem() + $index }}</td>
                                <td class="border px-4 py-2">{{ $m->kode_mk }}</td>
                                <td class="border px-4 py-2">{{ $m->name }}</td>
                                <td class="border px-4 py-2">{{ $m->prodi->name ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $m->dosen->name ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $m->sks }}</td>
                                <td class="border px-4 py-2 text-center">{{ $m->semester }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('admin.matkul.edit', $m->id) }}"
                                        class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">Edit</a>
                                    <form action="{{ route('admin.matkul.destroy', $m->id) }}" method="POST"
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
                                <td colspan="8" class="text-center py-3 text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="md:hidden space-y-4">
                @forelse ($matkul as $m)
                    <div class="border rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-gray-800">{{ $m->name }}</h3>
                        <p class="text-sm text-gray-600">Kode MK: {{ $m->kode_mk }}</p>
                        <p class="text-sm text-gray-600">Prodi: {{ $m->prodi->name ?? '-' }}</p>
                        <p class="text-sm text-gray-600">Dosen: {{ $m->dosen->name ?? '-' }}</p>
                        <p class="text-sm text-gray-600">SKS: {{ $m->sks }} | Semester: {{ $m->semester }}</p>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.matkul.edit', $m->id) }}"
                                class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">Edit</a>
                            <form action="{{ route('admin.matkul.destroy', $m->id) }}" method="POST"
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
                {{ $matkul->links() }}
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
