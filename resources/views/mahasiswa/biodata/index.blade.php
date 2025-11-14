<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Biodata Mahasiswa') }}
        </h2>
    </x-slot>
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <!-- Foto Profil & Info Utama -->
                        <div class="lg:col-span-1">
                            <div class="text-center mb-6">
                                <div class="inline-block rounded-full overflow-hidden border-4 border-gray-200 mb-4">
                                    <img src="{{ asset('assets/img/logo2.jpg') }}" alt="Foto Profil"
                                        class="w-48 h-48 object-cover">
                                </div>
                                <h4 class="text-xl font-bold text-gray-900 mb-1">{{ $mahasiswa->user->name }}</h4>
                                <h5 class="text-gray-600 mb-2">NIM: {{ $mahasiswa->nim }}</h5>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if ($mahasiswa->user->status == 'aktif') bg-green-100 text-green-800
                                @elseif($mahasiswa->user->status == 'non-aktif') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $mahasiswa->user->status }}
                                </span>
                            </div>

                            <!-- Informasi Kontak -->
                            <div class="bg-blue-50 rounded-lg border border-blue-200 mb-6">
                                <div class="px-4 py-3 border-b border-blue-200">
                                    <h3 class="text-lg font-semibold text-blue-800">Informasi Kontak</h3>
                                </div>
                                <div class="p-4">
                                    <div class="space-y-3">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Email</p>
                                                <p class="text-sm text-gray-600">{{ $mahasiswa->user->email }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Telepon</p>
                                                <p class="text-sm text-gray-600">{{ $mahasiswa->no_telp }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Alamat</p>
                                                <p class="text-sm text-gray-600">{{ $mahasiswa->alamat }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Biodata -->
                        <div class="lg:col-span-3">
                            <!-- Data Pribadi -->
                            <div class="bg-indigo-50 rounded-lg border border-indigo-200 mb-6">
                                <div class="px-4 py-3 border-b border-indigo-200">
                                    <h3 class="text-lg font-semibold text-indigo-800">Data Pribadi</h3>
                                </div>
                                <div class="p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-3 gap-2">
                                                <span class="text-sm font-medium text-gray-900">NIM</span>
                                                <span
                                                    class="col-span-2 text-sm text-gray-600">{{ $mahasiswa->nim }}</span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2">
                                                <span class="text-sm font-medium text-gray-900">Nama Lengkap</span>
                                                <span
                                                    class="col-span-2 text-sm text-gray-600">{{ $mahasiswa->user->name }}</span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2">
                                                <span class="text-sm font-medium text-gray-900">Jenis Kelamin</span>
                                                <span class="col-span-2 text-sm text-gray-600">
                                                    @if ($mahasiswa->gender == 'L')
                                                        Laki-laki
                                                    @else
                                                        Perempuan
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2">
                                                <span class="text-sm font-medium text-gray-900">Agama</span>
                                                <span
                                                    class="col-span-2 text-sm text-gray-600">{{ $mahasiswa->religion }}</span>
                                            </div>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-3 gap-2">
                                                <span class="text-sm font-medium text-gray-900">Program Studi</span>
                                                <span
                                                    class="col-span-2 text-sm text-gray-600">{{ $mahasiswa->prodi->name ?? 'Belum diatur' }}</span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2">
                                                <span class="text-sm font-medium text-gray-900">Dosen Wali</span>
                                                <span
                                                    class="col-span-2 text-sm text-gray-600">{{ $mahasiswa->dosen->user->name ?? 'Belum diatur' }}</span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2">
                                                <span class="text-sm font-medium text-gray-900">Angkatan</span>
                                                <span
                                                    class="col-span-2 text-sm text-gray-600">{{ $mahasiswa->angkatan }}</span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2">
                                                <span class="text-sm font-medium text-gray-900">Tahun Akademik</span>
                                                <span
                                                    class="col-span-2 text-sm text-gray-600">{{ $mahasiswa->tahun_akademik }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Akademik -->
                            <div class="bg-green-50 rounded-lg border border-green-200 mb-6">
                                <div class="px-4 py-3 border-b border-green-200">
                                    <h3 class="text-lg font-semibold text-green-800">Informasi Akademik</h3>
                                </div>
                                <div class="p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Program Studi -->
                                        <div
                                            class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l9-5-9-5-9 5 9 5zm0 0l9-5-9-5-9 5 9 5zm-9-5v10l9-5 9 5V9l-9-5-9 5z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium">Program Studi</p>
                                                    <p class="text-lg font-bold">
                                                        {{ $mahasiswa->prodi->name ?? 'Belum diatur' }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dosen Wali -->
                                        <div
                                            class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium">Dosen Wali</p>
                                                    <p class="text-lg font-bold">
                                                        {{ $mahasiswa->dosen->user->name ?? 'Belum diatur' }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Angkatan -->
                                        <div
                                            class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium">Angkatan</p>
                                                    <p class="text-lg font-bold">{{ $mahasiswa->angkatan }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tahun Akademik -->
                                        <div
                                            class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium">Tahun Akademik</p>
                                                    <p class="text-lg font-bold">{{ $mahasiswa->tahun_akademik }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Informasi Sistem -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Status Mahasiswa -->
                                <div class="bg-gray-800 rounded-lg border border-gray-700">
                                    <div class="px-4 py-3 border-b border-gray-700">
                                        <h3 class="text-lg font-semibold text-white">Status Mahasiswa</h3>
                                    </div>
                                    <div class="p-6 text-center">
                                        <div class="status-indicator">
                                            @if ($mahasiswa->user->status == 'aktif')
                                                <div
                                                    class="inline-flex flex-col items-center p-6 bg-green-100 rounded-full">
                                                    <svg class="w-12 h-12 text-green-600 mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="text-lg font-bold text-green-800">AKTIF</span>
                                                </div>
                                            @elseif($mahasiswa->user->status == 'non-aktif')
                                                <div
                                                    class="inline-flex flex-col items-center p-6 bg-red-100 rounded-full">
                                                    <svg class="w-12 h-12 text-red-600 mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="text-lg font-bold text-red-800">NON-AKTIF</span>
                                                </div>
                                            @else
                                                <div
                                                    class="inline-flex flex-col items-center p-6 bg-yellow-100 rounded-full">
                                                    <svg class="w-12 h-12 text-yellow-600 mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    <span
                                                        class="text-lg font-bold text-yellow-800">{{ strtoupper($mahasiswa->user->status) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Sistem -->
                                <div class="bg-gray-100 rounded-lg border border-gray-300">
                                    <div class="px-4 py-3 border-b border-gray-300">
                                        <h3 class="text-lg font-semibold text-gray-800">Informasi Sistem</h3>
                                    </div>
                                    <div class="p-4">
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-900">Dibuat</span>
                                                <span
                                                    class="text-sm text-gray-600">{{ $mahasiswa->created_at->format('d F Y H:i') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-900">Diupdate</span>
                                                <span
                                                    class="text-sm text-gray-600">{{ $mahasiswa->updated_at->format('d F Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center">
                    <small class="text-gray-500">
                        Terakhir update: {{ $mahasiswa->updated_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
