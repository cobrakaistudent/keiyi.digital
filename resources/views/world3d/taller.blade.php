<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl leading-tight" style="font-family: 'Space Grotesk', sans-serif;">
            The Print Lab (Zona Cliente)
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: var(--color-bg, #f4f4f0); min-height: calc(100vh - 73px);">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">
            
            <!-- Nueva Solicitud (Upload) -->
            <div class="bg-white overflow-hidden" style="border: 2px solid black; border-radius: 12px; box-shadow: 6px 6px 0 0 rgba(0,0,0,1);">
                <div class="p-6">
                    <h3 class="font-bold text-xl mb-6" style="font-family: 'Space Grotesk', sans-serif; color: #1976D2;">Nueva Solicitud de Fabricación 3D</h3>
                    
                    @if(session('upload_sent'))
                        <div style="background: #e8f5e9; border: 2px solid #000; border-radius: 8px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; font-family: 'Space Grotesk', sans-serif; font-weight: 600;">
                            {{ session('upload_sent') }}
                        </div>
                    @endif

                    <form action="{{ route('taller.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-8">
                        @csrf
                        <!-- Drag and Drop Zone -->
                        <div class="flex-1">
                            <label class="block mb-2 font-bold" style="font-family: 'Space Grotesk', sans-serif;">Archivo Modelo (STL, OBJ, 3MF - Max 50MB)</label>
                            <label class="w-full flex items-center justify-center border-2 border-dashed border-black rounded-lg p-12 text-center bg-gray-50 hover:bg-gray-100 transition cursor-pointer" style="box-shadow: inset 2px 2px 0 0 rgba(0,0,0,0.05); min-height: 250px;" for="file-input">
                                <div>
                                    <span class="text-4xl mb-2 block">📁</span>
                                    <p class="font-bold font-mono">Arrastra tu archivo aquí</p>
                                    <p class="text-sm text-gray-500 mt-2">o haz click para seleccionar desde tu equipo</p>
                                    <input type="file" id="file-input" name="file" class="hidden" accept=".stl,.obj,.3mf" required>
                                </div>
                            </label>
                            @error('file')<p style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                        </div>

                        <!-- Formulario de Specs -->
                        <div class="flex-1 flex flex-col gap-4">
                            <div>
                                <label class="block font-bold mb-1" style="font-family: 'Space Grotesk', sans-serif;">Material Base</label>
                                <select name="material" class="w-full border-2 border-black rounded-lg p-3" style="box-shadow: 2px 2px 0 0 rgba(0,0,0,1);" required>
                                    <option value="PLA">PLA (Recomendado, Biodegradable)</option>
                                    <option value="PETG">PETG (Alta Resistencia Mecánica)</option>
                                    <option value="TPU">TPU (Goma Flexible)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold mb-1" style="font-family: 'Space Grotesk', sans-serif;">Color Principal (Filamentos en Stock)</label>
                                <select name="color" class="w-full border-2 border-black rounded-lg p-3" style="box-shadow: 2px 2px 0 0 rgba(0,0,0,1);" required>
                                    <option value="Negro">Negro</option>
                                    <option value="Café">Café</option>
                                    <option value="Hueso">Color Hueso</option>
                                    <option value="Apiñonado">Color Apiñonado</option>
                                    <option value="Azul">Azul</option>
                                    <option value="Amarillo">Amarillo</option>
                                    <option value="Rojo">Rojo</option>
                                    <option value="Verde Militar">Verde Militar</option>
                                    <option value="Transparente">Transparente</option>
                                    <option value="Carne">Color Carne</option>
                                    <option value="Rosa">Rosa</option>
                                    <option value="Fiusha">Fiusha</option>
                                    <option value="True Color AMS">True Color AMS (Sujeto a Evaluación)</option>
                                </select>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block font-bold mb-1" style="font-family: 'Space Grotesk', sans-serif;">Cantidad a imprimir</label>
                                    <input type="number" name="quantity" min="1" value="1" class="w-full border-2 border-black rounded-lg p-3" style="box-shadow: 2px 2px 0 0 rgba(0,0,0,1);" required>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold mb-1" style="font-family: 'Space Grotesk', sans-serif;">Notas para el operario (Opcional)</label>
                                <textarea name="notes" rows="2" class="w-full border-2 border-black rounded-lg p-3" placeholder="Ej. Porcentaje de relleno sugerido, prioridad de entrega..." style="box-shadow: 2px 2px 0 0 rgba(0,0,0,1); resize: none;"></textarea>
                            </div>

                            <button type="submit" class="mt-4 font-bold p-3 text-center border-2 border-black rounded-lg w-full transform hover:-translate-y-1 transition" style="background-color: #FF9800; box-shadow: 4px 4px 0 0 rgba(0,0,0,1); color: black;">Subir Archivo y Solicitar Cotización</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de mis solicitudes -->
            <div class="bg-white overflow-hidden" style="border: 2px solid black; border-radius: 12px; box-shadow: 6px 6px 0 0 rgba(0,0,0,1);">
                <div class="p-6">
                    <h3 class="font-bold text-xl mb-4" style="font-family: 'Space Grotesk', sans-serif;">Mi Historial de Impresiones</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0 10px;">
                            <thead>
                                <tr>
                                    <th class="p-3 font-bold border-b-2 border-black" style="font-family: 'Space Grotesk', sans-serif;">Cod.</th>
                                    <th class="p-3 font-bold border-b-2 border-black" style="font-family: 'Space Grotesk', sans-serif;">Archivo 3D</th>
                                    <th class="p-3 font-bold border-b-2 border-black" style="font-family: 'Space Grotesk', sans-serif;">Material</th>
                                    <th class="p-3 font-bold border-b-2 border-black text-center" style="font-family: 'Space Grotesk', sans-serif;">Cant.</th>
                                    <th class="p-3 font-bold border-b-2 border-black" style="font-family: 'Space Grotesk', sans-serif;">Status</th>
                                    <th class="p-3 font-bold border-b-2 border-black text-right" style="font-family: 'Space Grotesk', sans-serif;">Cotización</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                @php
                                    $statusLabel = match($order->status) {
                                        'received'  => ['label' => 'RECIBIDA',       'class' => 'bg-blue-200 text-blue-800'],
                                        'quoting'   => ['label' => 'EN COTIZACIÓN',  'class' => 'bg-yellow-200 text-yellow-800'],
                                        'approved'  => ['label' => 'APROBADA',       'class' => 'bg-green-200 text-green-800'],
                                        'printing'  => ['label' => 'IMPRIMIENDO',    'class' => 'bg-green-200 text-green-800'],
                                        'delivered' => ['label' => 'ENTREGADA',      'class' => 'bg-gray-200 text-gray-700'],
                                        'cancelled' => ['label' => 'CANCELADA',      'class' => 'bg-red-200 text-red-800'],
                                        default     => ['label' => $order->status,   'class' => 'bg-gray-200 text-gray-700'],
                                    };
                                    $itemName = $order->type === 'catalog'
                                        ? ($order->catalogItem?->title ?? 'Catálogo')
                                        : ($order->file_name ?? 'Archivo custom');
                                @endphp
                                <tr class="bg-gray-50" style="outline: 2px solid black; outline-offset: -2px;">
                                    <td class="p-3 font-mono text-sm {{ $order->status === 'cancelled' ? 'line-through text-gray-400' : '' }}">#ORD-{{ $order->id }}</td>
                                    <td class="p-3 font-medium {{ $order->status === 'cancelled' ? 'text-gray-400' : '' }}">{{ $itemName }}</td>
                                    <td class="p-3"><span class="bg-gray-200 px-2 py-1 rounded text-xs font-bold border border-black {{ $order->status === 'cancelled' ? 'opacity-50' : '' }}">{{ $order->material }} {{ $order->color }}</span></td>
                                    <td class="p-3 text-center {{ $order->status === 'cancelled' ? 'opacity-50' : '' }}">{{ $order->quantity }}</td>
                                    <td class="p-3">
                                        <span class="{{ $statusLabel['class'] }} px-3 py-1 rounded-full text-xs font-bold border border-black {{ $order->status === 'printing' ? 'animate-pulse' : '' }}">
                                            {{ $statusLabel['label'] }}{{ $order->status === 'printing' ? ' 🖨️' : '' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-right font-bold {{ $order->status === 'cancelled' ? 'text-gray-400' : 'text-green-700' }}">
                                        @if($order->quoted_price)
                                            ${{ number_format($order->quoted_price, 0) }} MXN
                                        @elseif($order->status === 'cancelled')
                                            Cancelada
                                        @else
                                            Calculando...
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500" style="font-family: 'Space Grotesk', sans-serif;">
                                        Aún no tienes solicitudes. Sube tu primer archivo arriba.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
