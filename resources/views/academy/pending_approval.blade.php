<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-10 text-center shadow-xl border border-indigo-100">
                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-8 animate-bounce">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Registro Recibido</h1>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Hola <strong>{{ Auth::user()->name }}</strong>, gracias por unirte a la <strong>Keiyi Academy</strong>.
                </p>
                <div class="bg-indigo-50 rounded-2xl p-6 mb-8 border border-indigo-100">
                    <p class="text-indigo-800 text-sm font-bold">
                        Tu cuenta está actualmente en revisión por nuestro equipo de élite. Recibirás acceso completo en cuanto sea aprobada.
                    </p>
                </div>
                
                <div class="flex flex-col gap-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">
                        Volver a la Home
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 font-bold text-xs uppercase tracking-widest transition-colors">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
