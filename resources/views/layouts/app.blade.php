<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIMAKMA') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo2.jpg') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div x-data="sidebarComponent()" class="min-h-screen bg-gray-100">
        <div class="transition-all duration-200" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
            <livewire:layout.navigation />
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sidebarComponent', () => ({
            sidebarOpen: window.innerWidth >= 1024, 
            isDesktop: window.innerWidth >= 1024,
            activeDropdown: null,
            init() {
                window.addEventListener('resize', () => {
                    this.isDesktop = window.innerWidth >= 1024;

                    if (this.isDesktop) {
                        this.sidebarOpen = true;
                    } else {
                        this.sidebarOpen = false;
                    }
                });
            }
        }))
    })
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '{!! session('error') !!}',
            confirmButtonColor: '#d33',
        });
    </script>
@endif

@if (session('warning'))
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: '{{ session('warning') }}',
            confirmButtonColor: '#f1c40f',
        });
    </script>
@endif

@stack('scripts')

</html>
