<x-app-layout>
    <div class="flex flex-col lg:flex-row min-h-screen bg-gray-100">
        
        <!-- Barra Lateral de Lecciones (Escritorio) -->
        <div class="w-full lg:w-80 bg-white border-r border-gray-200 flex flex-col h-screen sticky top-0 overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <a href="{{ route('academy.dashboard') }}" class="text-xs font-bold text-indigo-600 uppercase tracking-widest flex items-center mb-2 hover:text-indigo-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver al Dashboard
                </a>
                <h3 class="font-black text-xl text-gray-900 leading-tight">{{ $courseTitle }}</h3>
            </div>
            
            <nav class="flex-1 overflow-y-auto p-4 space-y-2">
                @foreach($courseLessons as $lesson)
                    <a href="{{ $lesson['url'] }}" class="flex items-center p-3 rounded-xl transition-all {{ $lesson['number'] == $currentLessonNumber ? 'bg-indigo-50 border-indigo-100 shadow-sm' : 'hover:bg-gray-50' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 font-black text-sm {{ $lesson['number'] == $currentLessonNumber ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-500' }}">
                            {{ $lesson['number'] }}
                        </div>
                        <span class="text-sm font-bold {{ $lesson['number'] == $currentLessonNumber ? 'text-indigo-900' : 'text-gray-700' }}">
                            {{ $lesson['title'] }}
                        </span>
                        @if($lesson['number'] == $currentLessonNumber)
                            <svg class="w-4 h-4 ml-auto text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path></svg>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        <!-- Contenido de la Lección -->
        <div class="flex-1 flex flex-col min-h-screen">
            
            <!-- Cabecera de Lección -->
            <div class="bg-white border-b border-gray-200 px-8 py-4 flex flex-col md:flex-row items-center justify-between sticky top-0 z-10 shadow-sm">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-black text-gray-900">{{ $lessonTitle }}</h1>
                    <div class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-tighter mt-1">
                        <span class="bg-gray-100 px-2 py-0.5 rounded mr-2">Lección {{ $currentLessonNumber }}</span>
                        <span>{{ $courseTitle }}</span>
                    </div>
                </div>
                
                <!-- Selector de Tipo de Contenido (Guía vs Script) -->
                <div class="flex bg-gray-100 p-1 rounded-2xl shadow-inner border border-gray-200">
                    <a href="{{ route('academy.lesson', [$courseSlug, $currentLessonNumber, 'guia']) }}" 
                       class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $type === 'guia' ? 'bg-white text-indigo-700 shadow-md transform scale-105' : 'text-gray-500 hover:text-gray-700' }}">
                        Guía Estudio
                    </a>
                    @if($hasScript)
                    <a href="{{ route('academy.lesson', [$courseSlug, $currentLessonNumber, 'script']) }}" 
                       class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $type === 'script' ? 'bg-white text-indigo-700 shadow-md transform scale-105' : 'text-gray-500 hover:text-gray-700' }}">
                        Script Vídeo
                    </a>
                    @endif
                </div>
            </div>

            <!-- Área de Lectura / Reproductor -->
            <div class="flex-1 p-8 lg:p-12 overflow-y-auto">
                <div class="max-w-4xl mx-auto">
                    
                    @if($type === 'script')
                        <div class="bg-indigo-900 rounded-3xl p-6 mb-12 shadow-2xl flex flex-col items-center justify-center text-center text-white min-h-[400px] relative overflow-hidden group border-4 border-indigo-700">
                            <div class="z-10 relative">
                                <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center border border-white/20 mb-6 mx-auto group-hover:scale-110 transition-all duration-500 cursor-pointer">
                                    <svg class="w-12 h-12 text-white ml-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                </div>
                                <h3 class="text-3xl font-black mb-2 leading-tight">Reproductor de Vídeo Beta</h3>
                                <p class="text-indigo-200 max-w-sm mx-auto mb-6">El contenido del vídeo se encuentra en fase de producción. Lee el script técnico abajo para preparar tu contenido.</p>
                                <span class="bg-indigo-500/50 px-4 py-2 rounded-full text-xs font-bold border border-indigo-400">Próximamente v1.0</span>
                            </div>
                            <!-- Decoración fondo -->
                            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-indigo-600 rounded-full opacity-20 blur-3xl group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-0 right-0 p-8">
                                <span class="bg-indigo-400/20 text-indigo-300 border border-indigo-400/30 px-3 py-1 rounded text-[10px] font-bold tracking-widest uppercase">4K High Res Ready</span>
                            </div>
                        </div>
                    @endif

                    <!-- Renderizado de Markdown -->
                    <div id="lesson-content" class="prose prose-indigo prose-lg max-w-none prose-headings:font-black prose-headings:text-gray-900 prose-p:text-gray-700 prose-strong:text-indigo-700 prose-li:text-gray-700 prose-blockquote:border-l-indigo-600 prose-blockquote:bg-indigo-50 prose-blockquote:p-4 prose-blockquote:rounded-r-xl prose-img:rounded-3xl prose-img:shadow-lg">
                        {!! $content !!}
                    </div>

                    <!-- Navegación entre lecciones -->
                    <div class="mt-20 pt-8 border-t border-gray-200 flex items-center justify-between">
                        @php
                            $prev = $courseLessons->firstWhere('number', (int)$currentLessonNumber - 1);
                            $next = $courseLessons->firstWhere('number', (int)$currentLessonNumber + 1);
                        @endphp
                        
                        <div>
                            @if($prev)
                            <a href="{{ $prev['url'] }}" class="flex flex-col group">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 group-hover:text-indigo-600">Anterior</span>
                                <span class="text-lg font-black text-gray-900 group-hover:text-indigo-600 transition-colors flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                                    {{ $prev['title'] }}
                                </span>
                            </a>
                            @endif
                        </div>

                        <div class="text-right">
                            @if($next)
                            <a href="{{ $next['url'] }}" class="flex flex-col group">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 group-hover:text-indigo-600">Siguiente</span>
                                <span class="text-lg font-black text-gray-900 group-hover:text-indigo-600 transition-colors flex items-center">
                                    {{ $next['title'] }}
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                </span>
                            </a>
                            @else
                            <a href="{{ route('academy.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-900 text-white font-black rounded-2xl hover:bg-indigo-600 transition-all shadow-lg hover:shadow-indigo-200 uppercase tracking-widest text-sm">
                                Finalizar Taller
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para renderizar Markdown (Simple y efectivo para la beta) -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentElement = document.getElementById('lesson-content');
            const rawMarkdown = contentElement.textContent;
            contentElement.innerHTML = marked.parse(rawMarkdown);
        });
    </script>
</x-app-layout>
