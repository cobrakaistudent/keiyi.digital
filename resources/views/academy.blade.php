<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keiyi Academy | Marketing & IA Elite</title>
    <meta name="description" content="Domina las herramientas y sistemas que usan las agencias top.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&family=Space+Grotesk:wght@400;500;700;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (Vía CDN temporal para la estructura de la Academia) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Estilos Originales -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <!-- Tailwind Config Integrada del Original -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Space Grotesk', 'sans-serif'],
                        hand: ['Gloria Hallelujah', 'cursive']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-[#FFF5EB] font-sans text-[#0F172A] selection:bg-[#40E0D0] selection:text-[#0F172A]">

    <!-- Navbar -->
    <nav class="fixed w-full top-0 z-50 bg-[#FFF5EB] border-b-4 border-[#0F172A] shadow-[0_4px_0_#0F172A]">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-3xl font-bold tracking-tighter">keiyi<span class="text-[#FF7F50]">.</span></a>
            <div class="hidden md:flex items-center gap-8 font-bold">
                <a href="{{ url('/#value-prop') }}" class="hover:-translate-y-1 transition-transform">Filosofía</a>
                <a href="{{ url('/#pricing') }}" class="hover:-translate-y-1 transition-transform">Servicios</a>
                <a href="{{ url('/academy') }}" class="text-indigo-600 underline decoration-wavy decoration-[#FF7F50]">Academy</a>
                <a href="{{ url('/3d-world') }}" class="hover:-translate-y-1 transition-transform">3D-World</a>
                <a href="{{ url('/blog') }}" class="hover:-translate-y-1 transition-transform">Blog</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-[#0F172A] text-white px-6 py-2 rounded-full border-2 border-[#0F172A] shadow-[4px_4px_0_#40E0D0] hover:translate-y-1 hover:shadow-[2px_2px_0_#40E0D0] transition-all">Panel Alumno</a>
                    @else
                        <a href="{{ route('login') }}" class="bg-[#0F172A] text-white px-6 py-2 rounded-full border-2 border-[#0F172A] shadow-[4px_4px_0_#FF7F50] hover:translate-y-1 hover:shadow-[2px_2px_0_#FF7F50] transition-all">Entrar</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Header The Hub -->
    <div class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
        <div class="bg-indigo-600 text-white rounded-[40px] p-12 relative overflow-hidden border-4 border-[#0F172A] shadow-[15px_15px_0_#0F172A]">
            <div class="relative z-10 max-w-2xl">
                <span class="font-hand text-[#FFD700] text-2xl mb-4 inline-block -rotate-3">The Hub ⚡</span>
                <h1 class="text-5xl md:text-7xl font-black leading-tight mb-6 tracking-tight">Keiyi <br><span class="text-[#40E0D0]">Academy.</span></h1>
                <p class="text-xl md:text-2xl font-medium mb-10 opacity-90">Aprende a montar los sistemas, embudos y automatizaciones de IA que usamos para vender a diario.</p>
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-[#FF7F50] text-[#0F172A] px-8 py-4 rounded-full font-bold text-lg border-2 border-[#0F172A] shadow-[6px_6px_0_#0F172A] hover:translate-y-1 hover:shadow-[3px_3px_0_#0F172A] transition-all inline-block">Ver mis clases</a>
                    @else
                        <a href="{{ route('register') }}" class="bg-[#FF7F50] text-[#0F172A] px-8 py-4 rounded-full font-bold text-lg border-2 border-[#0F172A] shadow-[6px_6px_0_#0F172A] hover:translate-y-1 hover:shadow-[3px_3px_0_#0F172A] transition-all inline-block">Reservar Lugar</a>
                    @endauth
                </div>
            </div>
            <div class="absolute top-10 right-10 hidden md:block">
                <div class="w-32 h-32 bg-[#40E0D0] rounded-full flex items-center justify-center border-4 border-[#0F172A] rotate-12 shadow-[8px_8px_0_#0F172A]">
                    <span class="text-4xl">🚀</span>
                </div>
            </div>
        </div>

        <!-- Cursos Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16 mt-16">
            <!-- IA Origins -->
            <div class="bg-white rounded-[30px_10px_30px_10px] border-4 border-[#0F172A] shadow-[10px_10px_0_#40E0D0] p-6 hover:translate-y-[-10px] transition-all group col-span-1 md:col-span-2 lg:col-span-1 border-indigo-500">
                <div class="relative h-40 bg-indigo-50 rounded-2xl mb-4 overflow-hidden border-2 border-[#0F172A]">
                    <img src="https://images.unsplash.com/photo-1504221507732-5246c045949b?auto=format&fit=crop&q=80&w=2532" class="w-full h-full object-cover">
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-[#40E0D0] border-2 border-[#0F172A] px-2 py-0.5 rounded-lg text-[8px] font-black uppercase">Obligatorio</span>
                    <h3 class="text-2xl font-black text-[#0F172A]">IA Origins</h3>
                </div>
                <p class="text-sm text-gray-600 mb-6">De zero a Power User en 7 días.</p>
                <div class="flex justify-between items-center border-t-2 border-[#0F172A] pt-4">
                    <span class="font-black text-[10px] uppercase text-indigo-600">7 Lecciones</span>
                    <a href="{{ auth()->check() ? route('academia.dashboard') : route('register') }}" class="w-10 h-10 bg-[#FF7F50] border-2 border-[#0F172A] rounded-full text-white shadow-[3px_3px_0_#0F172A] flex items-center justify-center text-sm font-bold no-underline hover:translate-x-1 transition-transform">→</a>
                </div>
            </div>
            <!-- Notion Mastery -->
            <div class="bg-white rounded-[30px_10px_30px_10px] border-4 border-[#0F172A] shadow-[10px_10px_0_#FF7F50] p-6 hover:translate-y-[-10px] transition-all group border-orange-500">
                <div class="relative h-40 bg-white rounded-2xl mb-4 overflow-hidden border-2 border-[#0F172A] flex items-center justify-center p-6">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/45/Notion_app_logo.png" class="h-full object-contain">
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-[#FF7F50] border-2 border-[#0F172A] px-2 py-0.5 rounded-lg text-[8px] font-black uppercase text-white">AI Ready</span>
                    <h3 class="text-2xl font-black text-[#0F172A]">Notion Mastery</h3>
                </div>
                <p class="text-sm text-gray-600 mb-6">Wikis, Proyectos y Agentes IA.</p>
                <div class="flex justify-between items-center border-t-2 border-[#0F172A] pt-4">
                    <span class="font-black text-[10px] uppercase text-orange-600">7 Lecciones</span>
                    <a href="{{ auth()->check() ? route('academia.dashboard') : route('register') }}" class="w-10 h-10 bg-[#FF7F50] border-2 border-[#0F172A] rounded-full text-white shadow-[3px_3px_0_#0F172A] flex items-center justify-center text-sm font-bold no-underline hover:translate-x-1 transition-transform">→</a>
                </div>
            </div>
            <!-- Marketing Elite -->
            <div class="bg-white rounded-[30px_10px_30px_10px] border-4 border-[#0F172A] shadow-[10px_10px_0_#0F172A] p-6 hover:translate-y-[-10px] transition-all group">
                <div class="relative h-40 bg-indigo-100 rounded-2xl mb-4 overflow-hidden border-2 border-[#0F172A]">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=2426" class="w-full h-full object-cover">
                </div>
                <h3 class="text-2xl font-black mb-2 text-[#0F172A]">Marketing Elite</h3>
                <p class="text-sm text-gray-600 mb-6">Sistemas de venta con IA.</p>
                <div class="flex justify-between items-center border-t-2 border-[#0F172A] pt-4">
                    <span class="font-black text-[10px] uppercase text-indigo-600">7 Lecciones</span>
                    <a href="{{ auth()->check() ? route('academia.dashboard') : route('register') }}" class="w-10 h-10 bg-[#FF7F50] border-2 border-[#0F172A] rounded-full text-white shadow-[3px_3px_0_#0F172A] flex items-center justify-center text-sm font-bold no-underline hover:translate-x-1 transition-transform">→</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <!-- Viral Contenido -->
            <div class="bg-white rounded-[30px_10px_30px_10px] border-4 border-[#0F172A] shadow-[10px_10px_0_#0F172A] p-6 hover:translate-y-[-10px] transition-all group">
                <div class="relative h-40 bg-pink-100 rounded-2xl mb-4 overflow-hidden border-2 border-[#0F172A]">
                    <img src="https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?auto=format&fit=crop&q=80&w=2574" class="w-full h-full object-cover">
                </div>
                <h3 class="text-2xl font-black mb-2 text-[#0F172A]">Viral Contenido</h3>
                <p class="text-sm text-gray-600 mb-6">Reels y TikTok masivos.</p>
                <div class="flex justify-between items-center border-t-2 border-[#0F172A] pt-4">
                    <span class="font-black text-[10px] uppercase text-pink-600">4 Lecciones</span>
                    <a href="{{ auth()->check() ? route('academia.dashboard') : route('register') }}" class="w-10 h-10 bg-[#FF7F50] border-2 border-[#0F172A] rounded-full text-white shadow-[3px_3px_0_#0F172A] flex items-center justify-center text-sm font-bold no-underline hover:translate-x-1 transition-transform">→</a>
                </div>
            </div>
            <!-- 3D World -->
            <div class="bg-white rounded-[30px_10px_30px_10px] border-4 border-[#0F172A] shadow-[10px_10px_0_#0F172A] p-6 hover:translate-y-[-10px] transition-all group">
                <div class="relative h-40 bg-blue-100 rounded-2xl mb-4 overflow-hidden border-2 border-[#0F172A]">
                    <img src="https://images.unsplash.com/photo-1633356122544-f134324a6cee?auto=format&fit=crop&q=80&w=2670" class="w-full h-full object-cover">
                </div>
                <h3 class="text-2xl font-black mb-2 text-[#0F172A]">3D World</h3>
                <p class="text-sm text-gray-600 mb-6">IA + Impresión 3D.</p>
                <div class="flex justify-between items-center border-t-2 border-[#0F172A] pt-4">
                    <span class="font-black text-[10px] uppercase text-blue-600">4 Lecciones</span>
                    <a href="{{ auth()->check() ? route('academia.dashboard') : route('register') }}" class="w-10 h-10 bg-[#FF7F50] border-2 border-[#0F172A] rounded-full text-white shadow-[3px_3px_0_#0F172A] flex items-center justify-center text-sm font-bold no-underline hover:translate-x-1 transition-transform">→</a>
                </div>
            </div>
            <!-- Productividad Pro -->
            <div class="bg-white rounded-[30px_10px_30px_10px] border-4 border-[#0F172A] shadow-[10px_10px_0_#0F172A] p-6 hover:translate-y-[-10px] transition-all group">
                <div class="relative h-40 bg-yellow-100 rounded-2xl mb-4 overflow-hidden border-2 border-[#0F172A]">
                    <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=2672" class="w-full h-full object-cover">
                </div>
                <h3 class="text-2xl font-black mb-2 text-[#0F172A]">Productividad</h3>
                <p class="text-sm text-gray-600 mb-6">Trabaja solo 4 horas.</p>
                <div class="flex justify-between items-center border-t-2 border-[#0F172A] pt-4">
                    <span class="font-black text-[10px] uppercase text-yellow-600">4 Lecciones</span>
                    <a href="{{ auth()->check() ? route('academia.dashboard') : route('register') }}" class="w-10 h-10 bg-[#FF7F50] border-2 border-[#0F172A] rounded-full text-white shadow-[3px_3px_0_#0F172A] flex items-center justify-center text-sm font-bold no-underline hover:translate-x-1 transition-transform">→</a>
                </div>
            </div>
        </div>

        <!-- Recursos -->
        <div class="bg-[#FFC8DD] rounded-[40px] p-10 border-4 border-[#0F172A] shadow-[15px_15px_0_#000]">
            <span class="hand-note text-[#0F172A] text-xl mb-2 inline-block">¡No te pierdas esto! ✨</span>
            <h3 class="text-4xl font-black text-[#0F172A] uppercase mb-10">Kit de Herramientas Maestras</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-4 rounded-2xl border-4 border-[#0F172A] shadow-[5px_5px_0_#40E0D0] text-center">
                    <span class="text-2xl">🧠</span><br><span class="font-black">Prompt Bank</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border-4 border-[#0F172A] shadow-[5px_5px_0_#FF7F50] text-center">
                    <span class="text-2xl">📈</span><br><span class="font-black">Elite CS</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border-4 border-[#0F172A] shadow-[5px_5px_0_#FFC8DD] text-center">
                    <span class="text-2xl">🎬</span><br><span class="font-black">Viral CS</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border-4 border-[#0F172A] shadow-[5px_5px_0_#FFD700] text-center">
                    <span class="text-2xl">⚡</span><br><span class="font-black">Prod Pro CS</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
