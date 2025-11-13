<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/login', navigate: true);
    }
};
?>

<nav x-data="{ open: false, sidebarOpen: false, activeDropdown: null }" class="bg-white border-b border-gray-100 relative">

    <!-- Top Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-3">
                <!-- Sidebar Toggle Button -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Top Navigation -->
                <div class="hidden sm:flex space-x-8 sm:-my-px sm:ms-10">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"></div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Logout -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div x-show="sidebarOpen" @click.away="sidebarOpen = false"
        class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white shadow-lg z-40 transform transition-transform duration-200"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">

        <!-- Sidebar Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('assets/img/logo2.jpg') }}" alt="Logo SIMAKMA" class="w-8 h-8 rounded-full shadow-md">
                <h2 class="text-lg font-semibold">SIMAKMA</h2>
            </div>

            <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white focus:outline-none">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>


        <!-- Sidebar Links -->
        <nav class="mt-4 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200"
                :class="{ 'bg-gray-700 text-white': window.location.pathname.includes('dashboard') }">
                <!-- Dashboard Icon -->
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            @if (Auth::user()->isAdmin())
                <div>
                    <button @click="activeDropdown === 'master-data' ? activeDropdown = null : activeDropdown = 'master-data'"
                        class="flex items-center justify-between w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none transition-colors duration-200">
                        <div class="flex items-center">
                            <!-- Rencana Studi Icon -->
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 2C7.03 2 3 4 3 7v10c0 3 4.03 5 9 5s9-2 9-5V7c0-3-4.03-5-9-5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7c0 2.5 4.03 4.5 9 4.5S21 9.5 21 7" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 11c0 .83 4.03 2.5 9 2.5s9-1.67 9-2.5" />
                            </svg>
                            Master Data
                        </div>
                        <!-- Dropdown Arrow -->
                        <svg :class="{ 'rotate-180': activeDropdown === 'master-data' }"
                            class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeDropdown === 'master-data'" class="space-y-1 bg-gray-700" x-transition>
                        <a href="{{ route('admin.matkul.index') }}"
                            class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                            <!-- Matkul Icon (book) -->
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v10" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7v10" />
                            </svg>
                            Matkul
                        </a>
                        <a href="{{ route('admin.fakultas.index') }}"
                            class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                            <!-- Fakultas Icon (building) -->
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 6l9 4.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v11.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.5v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13v2M12 13v2M17 13v2" />
                            </svg>
                            Fakultas
                        </a>
                        <a href="{{ route('admin.prodi.index') }}"
                            class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                            <!-- Prodi Icon -->
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0118.825 17.057 11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.825-2.999 12.083 12.083 0 01.665-6.479L12 14z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6" />
                            </svg>
                            Prodi
                        </a>
                        <a href="{{ route('admin.dosen.index') }}"
                            class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                            <!-- Dosen Icon -->
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Dosen
                        </a>
                        <a href="{{ route('admin.mahasiswa.index') }}"
                            class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                            <!-- Mahasiswa Icon (graduation cap) -->
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l6.16-3.422A12.083 12.083 0 0118.825 17.057 11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.825-2.999A12.083 12.083 0 015.84 10.578L12 14z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20v-6" />
                            </svg>
                            Mahasiswa
                        </a>
                    </div>
                </div>
            @endif

            <!-- Rencana Studi -->
            <div>
                <button @click="activeDropdown === 'rencana' ? activeDropdown = null : activeDropdown = 'rencana'"
                    class="flex items-center justify-between w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none transition-colors duration-200">
                    <div class="flex items-center">
                        <!-- Rencana Studi Icon -->
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Rencana Studi
                    </div>
                    <!-- Dropdown Arrow -->
                    <svg :class="{'rotate-180': activeDropdown === 'rencana'}"
                        class="w-4 h-4 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="activeDropdown === 'rencana'" class="space-y-1 bg-gray-700" x-transition>
                    <a href="{{ route('mahasiswa.krs.index') }}"
                        class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                        <!-- Entry KRS Icon -->
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Entry KRS
                    </a>
                </div>
            </div>

            <!-- Informasi Akademik -->
            <div>
                <button @click="activeDropdown === 'info' ? activeDropdown = null : activeDropdown = 'info'"
                    class="flex items-center justify-between w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none transition-colors duration-200">
                    <div class="flex items-center">
                        <!-- Informasi Akademik Icon -->
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                        </svg>
                        Informasi Akademik
                    </div>
                    <!-- Dropdown Arrow -->
                    <svg :class="{'rotate-180': activeDropdown === 'info'}"
                        class="w-4 h-4 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="activeDropdown === 'info'" class="space-y-1 bg-gray-700" x-transition>
                    <a href="#"
                        class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                        <!-- Jadwal Kuliah Icon -->
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Jadwal Kuliah
                    </a>
                    <a href="#"
                        class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                        <!-- DPA Icon -->
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Daftar Prestasi Akademik
                    </a>
                    <a href="#"
                        class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                        <!-- KHS Icon -->
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Kartu Hasil Studi
                    </a>
                    <a href="#"
                        class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 transition-colors duration-200">
                        <!-- Nilai Icon -->
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        Nilai Mata Kuliah
                    </a>
                </div>
            </div>

            <!-- Biodata Mahasiswa -->
            <a href="#"
                class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                <!-- Biodata Icon -->
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Biodata Mahasiswa
            </a>

            <!-- Logout -->
            <button wire:click="logout"
                class="flex items-center w-full text-left px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </nav>
    </div>
</nav>
