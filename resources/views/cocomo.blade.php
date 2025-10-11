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

    <form id="cocomoForm" action="{{ route('calcular') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        @csrf
        <div>
            <label class="block mb-2 font-semibold">Tamaño del software (KLOC):</label>
            <input type="number" id="kloc" name="kloc" step="0.01" required class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block mb-2 font-semibold">Salario mensual (por persona):</label>
            <input type="number" id="salario" name="salario" step="0.01" required class="w-full border rounded p-2">
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

                $factores = [
                    'RELY' => ['Muy Bajo'=>0.75,'Bajo'=>0.88,'Nominal'=>1.00,'Alto'=>1.15,'Muy Alto'=>1.40,'Extra Alto'=>1.00],
                    'DATA' => ['Muy Bajo'=>1.00,'Bajo'=>0.94,'Nominal'=>1.00,'Alto'=>1.08,'Muy Alto'=>1.16,'Extra Alto'=>1.00],
                    'CPLX' => ['Muy Bajo'=>0.70,'Bajo'=>0.85,'Nominal'=>1.00,'Alto'=>1.15,'Muy Alto'=>1.30,'Extra Alto'=>1.65],
                    'TIME' => ['Muy Bajo'=>1.00,'Bajo'=>1.00,'Nominal'=>1.00,'Alto'=>1.11,'Muy Alto'=>1.30,'Extra Alto'=>1.66],
                    'STOR' => ['Muy Bajo'=>1.00,'Bajo'=>1.00,'Nominal'=>1.00,'Alto'=>1.06,'Muy Alto'=>1.21,'Extra Alto'=>1.56],
                    'VIRT' => ['Muy Bajo'=>1.00,'Bajo'=>0.87,'Nominal'=>1.00,'Alto'=>1.15,'Muy Alto'=>1.30,'Extra Alto'=>1.00],
                    'TURN' => ['Muy Bajo'=>1.00,'Bajo'=>0.87,'Nominal'=>1.00,'Alto'=>1.07,'Muy Alto'=>1.15,'Extra Alto'=>1.00],
                    'ACAP' => ['Muy Bajo'=>1.46,'Bajo'=>1.19,'Nominal'=>1.00,'Alto'=>0.86,'Muy Alto'=>0.71,'Extra Alto'=>1.00],
                    'PCAP' => ['Muy Bajo'=>1.42,'Bajo'=>1.17,'Nominal'=>1.00,'Alto'=>0.86,'Muy Alto'=>0.70,'Extra Alto'=>1.00],
                    'AEXP' => ['Muy Bajo'=>1.29,'Bajo'=>1.13,'Nominal'=>1.00,'Alto'=>0.91,'Muy Alto'=>0.82,'Extra Alto'=>1.00],
                    'VEXP' => ['Muy Bajo'=>1.21,'Bajo'=>1.10,'Nominal'=>1.00,'Alto'=>0.90,'Muy Alto'=>0.80,'Extra Alto'=>1.00],
                    'LTEX' => ['Muy Bajo'=>1.14,'Bajo'=>1.07,'Nominal'=>1.00,'Alto'=>0.95,'Muy Alto'=>0.91,'Extra Alto'=>1.00],
                    'MODP' => ['Muy Bajo'=>1.24,'Bajo'=>1.10,'Nominal'=>1.00,'Alto'=>0.91,'Muy Alto'=>0.82,'Extra Alto'=>1.00],
                    'TOOL' => ['Muy Bajo'=>1.00,'Bajo'=>0.95,'Nominal'=>1.00,'Alto'=>1.07,'Muy Alto'=>1.15,'Extra Alto'=>1.00],
                    'SCED' => ['Muy Bajo'=>1.00,'Bajo'=>1.00,'Nominal'=>1.00,'Alto'=>1.04,'Muy Alto'=>1.10,'Extra Alto'=>1.23],
                ];
            @endphp

            @foreach($grupos as $titulo => $factoresGrupo)
                <h3 class="text-lg font-semibold mt-6 mb-2 text-red-700 border-b border-gray-300 pb-1">{{ $titulo }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    @foreach($factoresGrupo as $clave => $nombre)
                        <div>
                            <label class="font-semibold block mb-1">{{ $nombre }} ({{ $clave }})</label>
                            <select name="{{ $clave }}" class="w-full border rounded p-1">
                                <option value="">Seleccione nivel</option>
                                @foreach($niveles as $nivel)
                                    <option value="{{ $nivel }}">{{ $nivel }} ({{ number_format($factores[$clave][$nivel],2) }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- Botón centrado -->
        <div class="col-span-1 md:col-span-2 mt-6 flex justify-center">
            <button id="btnGuardar" type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded">Calcular y Guardar</button>
        </div>
    </form>

    <!-- Tabla de resultados -->
    <h2 class="text-xl sm:text-2xl font-semibold mb-2">Historial de cálculos</h2>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border text-sm min-w-max">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">#N°</th>
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

document.getElementById('cocomoForm').addEventListener('submit', function(event) {
    const kloc = document.getElementById('kloc').value.trim();
    const salario = document.getElementById('salario').value.trim();

    if (kloc === "" || salario === "" || kloc <= 0 || salario <= 0) {
        alert("Por favor, complete correctamente los campos KLOC y Salario antes de guardar.");
        event.preventDefault();
        return false;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.textContent = "Guardando...";
});
</script>
</body>
</html>
















