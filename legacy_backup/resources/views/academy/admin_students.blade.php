<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Alumnos: Keiyi Academy') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('status'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-8 font-bold">
                {{ session('status') }}
            </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-2xl font-black text-gray-900">Alumnos Registrados</h3>
                    <span class="bg-gray-100 px-3 py-1 rounded-full text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $students->count() }} Usuarios</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Alumno</th>
                                <th class="px-8 py-4">Correo</th>
                                <th class="px-8 py-4">Estado</th>
                                <th class="px-8 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($students as $student)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-6 font-bold text-gray-900">{{ $student->name }}</td>
                                <td class="px-8 py-6 text-gray-600">{{ $student->email }}</td>
                                <td class="px-8 py-6">
                                    @if($student->is_approved)
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">Aprobado</span>
                                    @else
                                        <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">Pendiente</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    @if(!$student->is_approved)
                                        <form action="{{ route('admin.academy.approve', $student->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                                                Aprobar Acceso
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="bg-gray-100 text-gray-400 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest cursor-not-allowed">
                                            Ya tiene acceso
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @if($students->isEmpty())
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center text-gray-500 italic">No hay alumnos registrados todavía.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('academy.dashboard') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest">
                    &larr; Volver al Dashboard de la Academia
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
