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

<nav x-data="{ sidebarOpen: false, activeDropdown: null }" class="bg-white border-b border-gray-100 relative">

    <!-- TOP BAR -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT -->
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded-md text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    Dashboard
                </x-nav-link>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-gray-800">
                            {{ auth()->user()->name }}
                            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            Profile
                        </x-dropdown-link>

                        <button wire:click="logout" class="w-full text-left">
                            <x-dropdown-link>Logout</x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- SIDEBAR -->
    <div x-show="sidebarOpen" @click.away="sidebarOpen=false"
        class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white z-40">

        <div class="flex items-center px-4 py-3 border-b border-gray-700">
            <img src="{{ asset('assets/img/logo2.jpg') }}" class="w-8 h-8 mr-2 rounded">
            <span class="font-semibold text-lg">SIMAKMA</span>
        </div>

        <nav class="mt-4 space-y-1">

            <!-- DASHBOARD -->
            <a href="{{ route('dashboard') }}"
                class="block px-4 py-2 hover:bg-gray-700">
                Dashboard
            </a>

            <!-- ADMIN -->
            @if(auth()->user()->isAdmin())
                <button @click="activeDropdown==='master'?activeDropdown=null:activeDropdown='master'"
                    class="w-full flex justify-between px-4 py-2 hover:bg-gray-700">
                    Master Data
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="activeDropdown==='master'" class="bg-gray-700">
                    <a href="{{ route('admin.matkul.index') }}" class="block px-6 py-2 hover:bg-gray-600">Matkul</a>
                    <a href="{{ route('admin.prodi.index') }}" class="block px-6 py-2 hover:bg-gray-600">Prodi</a>
                    <a href="{{ route('admin.dosen.index') }}" class="block px-6 py-2 hover:bg-gray-600">Dosen</a>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="block px-6 py-2 hover:bg-gray-600">Mahasiswa</a>
                </div>
            @endif

            <!-- KRS -->
            <button @click="activeDropdown==='krs'?activeDropdown=null:activeDropdown='krs'"
                class="w-full flex justify-between px-4 py-2 hover:bg-gray-700">
                Rencana Studi
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="activeDropdown==='krs'" class="bg-gray-700">
                <a href="#" class="block px-6 py-2 hover:bg-gray-600">Entry KRS</a>
            </div>

            <!-- AKADEMIK -->
            <button @click="activeDropdown==='info'?activeDropdown=null:activeDropdown='info'"
                class="w-full flex justify-between px-4 py-2 hover:bg-gray-700">
                Informasi Akademik
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="activeDropdown==='info'" class="bg-gray-700">
                <a href="#" class="block px-6 py-2 hover:bg-gray-600">Jadwal Kuliah</a>
                <a href="#" class="block px-6 py-2 hover:bg-gray-600">KHS</a>
                <a href="#" class="block px-6 py-2 hover:bg-gray-600">Nilai</a>
            </div>

            <!-- LOGOUT -->
            <button wire:click="logout"
                class="w-full text-left px-4 py-2 hover:bg-gray-700">
                Logout
            </button>
        </nav>
    </div>
</nav>
