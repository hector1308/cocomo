<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COCOMO I - Estimador de Costos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-4 sm:p-8">
<div class="max-w-6xl mx-auto bg-white p-6 rounded-2xl shadow-md">
    <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-center text-purple-700">Estimador de Costos - Modelo COCOMO I</h1>

    <form action="{{ route('calcular') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        @csrf
        <div>
            <label class="block mb-2 font-semibold">Tamaño del software (KLOC):</label>
            <input type="number" name="kloc" step="0.01" required class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block mb-2 font-semibold">Salario mensual (por persona):</label>
            <input type="number" name="salario" step="0.01" required class="w-full border rounded p-2">
        </div>
        <div class="col-span-1 md:col-span-2">
            <label class="block mb-2 font-semibold">Tipo de proyecto:</label>
            <select name="tipo" class="w-full border rounded p-2 mb-4">
                <option value="organico">Orgánico</option>
                <option value="semiacoplado">Semiacoplado</option>
                <option value="empotrado">Empotrado</option>
            </select>
        </div>

        <!-- Factores de costo -->
        <div class="col-span-1 md:col-span-2">
            <h2 class="text-xl font-semibold mb-4 text-purple-700">Factores de Costo</h2>
            @php
                $niveles = ['Muy Bajo','Bajo','Nominal','Alto','Muy Alto','Extra Alto'];
                $grupos = [
                    'Atributos del Producto' => ['RELY'=>'Confiabilidad requerida del software','DATA'=>'Tamaño de la base de datos','CPLX'=>'Complejidad del producto'],
                    'Atributos del Hardware' => ['TIME'=>'Restricciones de tiempo de ejecución','STOR'=>'Restricciones de memoria','VIRT'=>'Volatilidad del entorno virtual (HW/SW)','TURN'=>'Tiempo de respuesta requerido'],
                    'Atributos del Personal' => ['ACAP'=>'Capacidad de los analistas','PCAP'=>'Capacidad de los programadores','AEXP'=>'Experiencia en la aplicación','VEXP'=>'Experiencia en la máquina','LTEX'=>'Experiencia en el lenguaje de programación'],
                    'Atributos del Proyecto' => ['MODP'=>'Uso de prácticas modernas','TOOL'=>'Uso de software reutilizable','SCED'=>'Restricciones de cronograma'],
                ];
            @endphp

            @foreach($grupos as $titulo => $factores)
                <h3 class="text-lg font-semibold mt-6 mb-2 text-red-700 border-b border-gray-300 pb-1">{{ $titulo }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    @foreach($factores as $clave => $nombre)
                        <div>
                            <label class="font-semibold block mb-1">{{ $nombre }}</label>
                            <select name="{{ $clave }}" class="w-full border rounded p-1">
                                <option value="">Seleccione nivel</option>
                                @foreach($niveles as $nivel)
                                    <option value="{{ $nivel }}">{{ $nivel }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="col-span-1 md:col-span-2 mt-6">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded w-full" onclick="this.disabled=true; this.form.submit();">Calcular y Guardar</button>
        </div>
    </form>

    <!-- Tabla de resultados -->
    <h2 class="text-xl sm:text-2xl font-semibold mb-2">Historial de cálculos</h2>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border text-sm min-w-max">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">#</th>
                    <th class="border p-2">KLOC</th>
                    <th class="border p-2">Tipo</th>
                    <th class="border p-2">EAF</th>
                    <th class="border p-2">PM</th>
                    <th class="border p-2">Duración</th>
                    <th class="border p-2">Personas</th>
                    <th class="border p-2">Costo Total</th>
                    <th class="border p-2">Procedimiento</th>
                    <th class="border p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($calculos as $c)
                <tr>
                    <td class="border p-2">{{ $c->id }}</td>
                    <td class="border p-2">{{ $c->kloc }}</td>
                    <td class="border p-2">{{ ucfirst($c->tipo) }}</td>
                    <td class="border p-2">{{ number_format($c->eaf, 3) }}</td>
                    <td class="border p-2">{{ number_format($c->esfuerzo, 2) }}</td>
                    <td class="border p-2">{{ number_format($c->duracion, 2) }}</td>
                    <td class="border p-2">{{ number_format($c->personas, 2) }}</td>
                    <td class="border p-2">${{ number_format($c->costo_total, 2) }}</td>
                    <td class="border p-2">
                        <button type="button" onclick="toggleProcedimiento({{ $c->id }})" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm">Ver procedimiento</button>
                    </td>
                    <td class="border p-2 text-center">
                        <form action="{{ route('calculo.eliminar', $c->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este cálculo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <tr id="procedimiento-{{ $c->id }}" class="hidden bg-gray-100">
                    <td colspan="10" class="p-4">
                        <pre>{{ $c->procedimiento ?? 'Procedimiento no generado' }}</pre>
                        @if($c->esfuerzo && $c->duracion && $c->personas && $c->costo_total)
                        <div class="mt-4 p-2 bg-purple-100 text-purple-800 rounded">
                            <strong>Resumen del proyecto:</strong><br>
                            Se necesitan aproximadamente <strong>{{ round($c->personas) }}</strong> personas durante <strong>{{ round($c->duracion) }}</strong> meses para completar el proyecto con un esfuerzo de <strong>{{ round($c->esfuerzo) }}</strong> PM, con un costo total aproximado de <strong>${{ number_format(round($c->costo_total), 0) }}</strong>.
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center p-4">Sin cálculos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleProcedimiento(id) {
    const row = document.getElementById('procedimiento-' + id);
    row.classList.toggle('hidden');
}
</script>
</body>
</html>












