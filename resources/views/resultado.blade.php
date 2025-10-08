<!DOCTYPE html>
<html>
<head>
    <title>Resultado COCOMO</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-2xl shadow-md">
        <h1 class="text-xl font-bold mb-4 text-center">Resultados del Cálculo</h1>

        <p><strong>Tamaño (KLOC):</strong> {{ $kloc }}</p>
        <p><strong>Tipo de proyecto:</strong> {{ ucfirst($tipo) }}</p>
        <p><strong>Esfuerzo estimado:</strong> {{ number_format($esfuerzo, 2) }} persona-meses</p>
        <p><strong>Duración estimada:</strong> {{ number_format($duracion, 2) }} meses</p>

        @if($costoTotal)
            <p><strong>Costo total estimado:</strong> ${{ number_format($costoTotal, 2) }}</p>
        @endif

        <a href="/" class="mt-4 block text-center bg-gray-200 rounded p-2">Volver</a>
    </div>
</body>
</html>
