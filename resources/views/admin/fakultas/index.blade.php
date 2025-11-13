<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Fakultas') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6">

            {{-- Pencarian dan Tombol Tambah --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                <form action="{{ route('admin.fakultas.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama fakultas..."
                        class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cari</button>
                </form>
                <a href="{{ route('admin.fakultas.create') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                    + Tambah Fakultas
                </a>
            </div>

            {{-- Tabel Desktop --}}
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-4 py-2 text-left w-16">#</th>
                            <th class="border px-4 py-2 text-left">Nama Fakultas</th>
                            <th class="border px-4 py-2 text-center w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fakultas as $index => $f)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $fakultas->firstItem() + $index }}</td>
                                <td class="border px-4 py-2">{{ $f->name }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <button href="{{ route('admin.fakultas.edit', $f->id) }}"
                                        class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">
                                        <a href="{{ route('admin.fakultas.edit', $f->id) }}">Edit</a>
                                    </button>
                                    <form action="{{ route('admin.fakultas.destroy', $f->id) }}" method="POST"
                                        class="inline-block" id="delete-form-{{ $f->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $f->id }})"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card --}}
            <div class="md:hidden space-y-4">
                @forelse ($fakultas as $f)
                    <div class="border rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-gray-800">{{ $f->name }}</h3>
                        <div class="flex gap-2 mt-2">
                            <button class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">
                                <a href="{{ route('admin.fakultas.edit', $f->id) }}">Edit</a>
                            </button>
                            <form action="{{ route('admin.fakultas.destroy', $f->id) }}" method="POST"
                                id="delete-form-mobile-{{ $f->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $f->id }}, true)"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada data</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $fakultas->links() }}
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
