<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail KRS Mahasiswa: ') . $mahasiswa->name }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6">
            <table class="min-w-full border border-gray-200 text-sm text-gray-600">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border text-left">Kode MK</th>
                        <th class="px-4 py-2 border text-left">Nama MK</th>
                        <th class="px-4 py-2 border text-left">SKS</th>
                        <th class="px-4 py-2 border text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($krs as $i => $k)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-2">{{ $i + 1 }}</td>
                            <td class="border px-4 py-2">{{ $k->matkul->kode_mk ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $k->matkul->name ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $k->matkul->sks ?? '-' }}</td>
                            <td class="border px-4 py-2 text-center">
                                <span
                                    class="px-2 py-1 rounded text-white
                                    {{ $k->status === 'aktif' ? 'bg-green-600' : ($k->status === 'ditolak' ? 'bg-red-500' : 'bg-gray-500') }}">
                                    {{ ucfirst($k->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 flex justify-end space-x-3">
                <form action="{{ route('dosen.krs.reject', $mahasiswa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" @disabled($k->status == 'aktif')
                        class="px-4 py-2 rounded text-white transition duration-150
                {{ $k->status == 'aktif' ? 'bg-gray-400 cursor-not-allowed opacity-70' : 'bg-red-600 hover:bg-red-700' }}">
                        <i class="fas fa-times mr-1"></i> Tolak KRS
                    </button>
                </form>

                <form action="{{ route('dosen.krs.approve', $mahasiswa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" @disabled($k->status == 'aktif')
                        class="px-4 py-2 rounded text-white transition duration-150
                {{ $k->status == 'aktif' ? 'bg-gray-400 cursor-not-allowed opacity-70' : 'bg-green-600 hover:bg-green-700' }}">
                        <i class="fas fa-check mr-1"></i> Validasi KRS
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
