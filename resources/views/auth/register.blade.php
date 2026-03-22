<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nombres -->
        <div>
            <x-input-label for="name" value="Nombre(s)" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Apellido Paterno -->
        <div class="mt-4">
            <x-input-label for="apellido_paterno" value="Apellido Paterno" />
            <x-text-input id="apellido_paterno" class="block mt-1 w-full" type="text" name="apellido_paterno" :value="old('apellido_paterno')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('apellido_paterno')" class="mt-2" />
        </div>

        <!-- Apellido Materno -->
        <div class="mt-4">
            <x-input-label for="apellido_materno" value="Apellido Materno (Opcional)" />
            <x-text-input id="apellido_materno" class="block mt-1 w-full" type="text" name="apellido_materno" :value="old('apellido_materno')" autocomplete="family-name" />
            <x-input-error :messages="$errors->get('apellido_materno')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Aceptación legal -->
        <div class="mt-4">
            <label class="flex items-start gap-2">
                <input type="checkbox" name="accepts_terms" value="1" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" required>
                <span class="text-sm text-gray-600">
                    He leído y acepto el
                    <a href="{{ route('privacidad') }}" target="_blank" class="underline text-indigo-600 hover:text-indigo-800">Aviso de Privacidad</a>
                    y los
                    <a href="{{ route('terminos') }}" target="_blank" class="underline text-indigo-600 hover:text-indigo-800">Términos y Condiciones</a>.
                </span>
            </label>
            <x-input-error :messages="$errors->get('accepts_terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                ¿Ya tienes cuenta?
            </a>

            <x-primary-button class="ms-4">
                Crear cuenta
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
