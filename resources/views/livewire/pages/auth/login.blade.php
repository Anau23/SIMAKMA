<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
    }
};
?>

<div class="flex flex-col items-center justify-center min-h-screen bg-white-50">
    <!-- Logo -->
    <div class="mb-6">
        <img src="{{ asset('assets/img/logo2.jpg') }}" alt="Logo" class="w-50 h-50 mx-auto rounded-full shadow-md">
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="w-full max-w-sm bg-white p-6">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full"
                type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4 relative">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input 
                wire:model="form.password" 
                id="password" 
                class="block mt-1 w-full pr-10"
                type="password" 
                name="password" 
                required 
                autocomplete="current-password" 
            />

            <!-- Tombol Show/Hide -->
            <button type="button" 
                onclick="togglePassword()" 
                class="absolute right-3 top-9 text-gray-500 hover:text-gray-700 focus:outline-none">
                👁️
            </button>

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none
                    focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>

<!-- Script Toggle Password -->
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = event.currentTarget;
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        icon.textContent = '👁️';
    }
}
</script>
