<x-app-layout>
    <div class="py-12 bg-[#FFF5EB] min-h-screen relative overflow-hidden">
        
        <!-- Doodle Background Elements (DNA Keiyi) -->
        <div class="absolute top-10 left-10 opacity-10 animate-pulse pointer-events-none">
            <svg width="200" height="200" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10,50 Q25,25 40,50 T70,50 T100,50" />
            </svg>
        </div>
        <div class="absolute bottom-10 right-10 opacity-10 animate-bounce pointer-events-none">
            <svg width="150" height="150" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="50" cy="50" r="40" stroke-dasharray="5,5" />
            </svg>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
            
            <!-- Hero de la Academia (Estilo "Pop Balloon") -->
            <div class="bg-white rounded-[60px_40px_60px_40px] p-10 mb-16 border-4 border-[#0F172A] shadow-[15px_15px_0_#6366f1] relative overflow-hidden group">
                <div class="relative z-10">
                    <span class="font-['Gloria_Hallelujah'] text-[#FF7F50] text-xl rotate-[-5deg] inline-block mb-4">¡Bienvenido de nuevo! ✌️</span>
                    <h1 class="text-5xl md:text-6xl font-black text-[#0F172A] mb-4 leading-tight">Keiyi <span class="relative z-10">Academy<span class="absolute bottom-2 left-0 w-full h-4 bg-[#FFD700] -z-10 rounded-full"></span></span></h1>
                    <p class="text-xl text-[#0F172A] font-medium max-w-2xl">Transforma tu carrera con los talleres de IA más audaces del mercado. Sin filtros, sin aburrimiento, solo profit.</p>
                </div>
                <!-- Decoración fondo -->
                <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-[#FFC8DD] rounded-full opacity-40 blur-3xl group-hover:scale-125 transition-all duration-700"></div>
                <div class="absolute top-10 right-10 hidden md:block">
                    <div class="w-32 h-32 bg-[#40E0D0] rounded-full flex items-center justify-center border-4 border-[#0F172A] rotate-12 shadow-[8px_8px_0_#0F172A]">
                        <span class="text-4xl">🚀</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center mb-12">
                <h3 class="text-3xl font-black text-[#0F172A] mr-4 uppercase tracking-tighter">Talleres <span class="text-[#FF7F50] italic">Elite</span></h3>
                <div class="h-1 flex-1 bg-[#0F172A] rounded-full opacity-10"></div>
            </div>

            <!-- Listado de Cursos (Estilo "Funky Card") -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($courses as $course)
                <div class="bg-white rounded-[30px_10px_30px_10px] border-4 border-[#0F172A] shadow-[10px_10px_0_#0F172A] p-6 hover:translate-y-[-10px] hover:rotate-[1deg] transition-all duration-300 group flex flex-col">
                    <div class="relative h-48 rounded-[20px] overflow-hidden mb-6 border-2 border-[#0F172A]">
                        <img src="{{ $course['image'] }}" alt="{{ $course['title'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500">
                        <div class="absolute top-3 left-3">
                            <span class="bg-[#FFD700] border-2 border-[#0F172A] px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-[3px_3px_0_#0F172A]">
                                {{ $course['level'] }}
                            </span>
                        </div>
                    </div>
                    
                    <h4 class="text-2xl font-black mb-4 text-[#0F172A] leading-tight">{{ $course['title'] }}</h4>
                    <p class="text-sm font-medium text-gray-600 mb-6 flex-grow">{{ $course['description'] }}</p>
                    
                    <div class="flex items-center justify-between border-t-2 border-[#0F172A] pt-6 mt-auto">
                        <span class="font-black text-xs uppercase tracking-widest text-[#6366f1]">{{ $course['lesson_count'] }} Lecciones</span>
                        <a href="{{ route('academy.lesson', [$course['slug'], 1]) }}" class="w-12 h-12 bg-[#FF7F50] border-2 border-[#0F172A] rounded-full flex items-center justify-center text-white shadow-[4px_4px_0_#0F172A] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7M5 12h16"></path></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Recursos Rápidos (Estilo "Hand-Drawn") -->
            <div class="mt-20 bg-[#FFC8DD] rounded-[40px] p-10 border-4 border-[#0F172A] shadow-[15px_15px_0_#000]">
                <div class="flex flex-col md:flex-row items-center justify-between mb-10">
                    <div>
                        <span class="font-['Gloria_Hallelujah'] text-[#0F172A] text-xl mb-2 inline-block">¡No te pierdas esto! ✨</span>
                        <h3 class="text-4xl font-black text-[#0F172A] uppercase">Kit de Herramientas Maestras</h3>
                    </div>
                    <div class="mt-6 md:mt-0">
                        <div class="bg-white border-2 border-[#0F172A] px-6 py-3 rounded-full font-black text-xs uppercase tracking-widest rotate-2 shadow-[5px_5px_0_#0F172A]">Actualizado: {{ date('M Y') }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <a href="{{ route('academy.resource.download', 'KEIYI_ACADEMY_PROMPT_BANK.md') }}" class="bg-white p-6 rounded-2xl border-4 border-[#0F172A] shadow-[8px_8px_0_#40E0D0] hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none transition-all flex flex-col items-center text-center group">
                        <div class="w-16 h-16 bg-[#40E0D0] rounded-full border-2 border-[#0F172A] flex items-center justify-center text-3xl mb-4 group-hover:animate-bounce">🧠</div>
                        <span class="font-black text-lg text-[#0F172A]">Prompt Bank</span>
                    </a>
                    <a href="{{ route('academy.resource.download', 'Marketing_Elite_Cheat_Sheet.md') }}" class="bg-white p-6 rounded-2xl border-4 border-[#0F172A] shadow-[8px_8px_0_#FF7F50] hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none transition-all flex flex-col items-center text-center group">
                        <div class="w-16 h-16 bg-[#FF7F50] rounded-full border-2 border-[#0F172A] flex items-center justify-center text-3xl mb-4 group-hover:animate-bounce">📈</div>
                        <span class="font-black text-lg text-[#0F172A]">Mkt Elite CS</span>
                    </a>
                    <a href="{{ route('academy.resource.download', 'Contenido_Viral_Cheat_Sheet.md') }}" class="bg-white p-6 rounded-2xl border-4 border-[#0F172A] shadow-[8px_8px_0_#FFC8DD] hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none transition-all flex flex-col items-center text-center group">
                        <div class="w-16 h-16 bg-[#FFC8DD] rounded-full border-2 border-[#0F172A] flex items-center justify-center text-3xl mb-4 group-hover:animate-bounce">🎬</div>
                        <span class="font-black text-lg text-[#0F172A]">Viral CS</span>
                    </a>
                    <a href="{{ route('academy.resource.download', 'Productividad_Pro_Cheat_Sheet.md') }}" class="bg-white p-6 rounded-2xl border-4 border-[#0F172A] shadow-[8px_8px_0_#FFD700] hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none transition-all flex flex-col items-center text-center group">
                        <div class="w-16 h-16 bg-[#FFD700] rounded-full border-2 border-[#0F172A] flex items-center justify-center text-3xl mb-4 group-hover:animate-bounce">⚡</div>
                        <span class="font-black text-lg text-[#0F172A]">Prod Pro CS</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
