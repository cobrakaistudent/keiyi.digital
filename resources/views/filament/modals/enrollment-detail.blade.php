<div class="space-y-4 p-2">
    @if($course)
        <div class="flex items-center gap-3 mb-4">
            <span class="text-3xl">{{ $course->emoji }}</span>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500">
                    Alumno: <strong>{{ $record->user?->name }} {{ $record->user?->apellido_paterno }}</strong>
                </p>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600 dark:text-gray-400">Progreso general</span>
                <span class="font-semibold {{ $record->progress_percent >= 100 ? 'text-green-600' : 'text-amber-600' }}">
                    {{ $record->progress_percent }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="h-3 rounded-full transition-all {{ $record->progress_percent >= 100 ? 'bg-green-500' : 'bg-amber-500' }}"
                     style="width: {{ min($record->progress_percent, 100) }}%"></div>
            </div>
        </div>

        {{-- Lesson list --}}
        <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">#</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Lección</th>
                        <th class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">Tipo</th>
                        <th class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">Estado</th>
                        <th class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">Nota</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    @foreach($lessons as $i => $lesson)
                        @php
                            $completion = $completions->get($lesson->id);
                            $isCompleted = !is_null($completion);
                        @endphp
                        <tr class="{{ $isCompleted ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                            <td class="px-3 py-2 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                {{ $lesson->title }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($lesson->type === 'quiz')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Quiz</span>
                                @elseif($lesson->type === 'interactive')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Interactivo</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">Lectura</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($isCompleted)
                                    <span class="text-green-600 dark:text-green-400" title="Completada {{ $completion->completed_at?->format('d/m/Y H:i') }}">
                                        <svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">
                                        <svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">
                                @if($isCompleted && $completion->score !== null)
                                    <span class="{{ $completion->score >= 70 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                        {{ $completion->score }}%
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-400 mt-2">
            Inscrito el {{ $record->enrolled_at?->format('d/m/Y') ?? 'N/A' }}
        </p>
    @else
        <p class="text-gray-500">Curso no encontrado (slug: {{ $record->course_id }})</p>
    @endif
</div>
