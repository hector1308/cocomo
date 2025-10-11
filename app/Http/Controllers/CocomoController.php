<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calculo;

class CocomoController extends Controller
{
    // Mostrar la vista con los cálculos guardados
    public function index()
    {
        $calculos = Calculo::all();
        return view('cocomo', compact('calculos'));
    }

    // Calcular COCOMO y guardar en la base de datos
    public function calcular(Request $request)
    {
        // Validación básica
        $request->validate([
            'kloc' => 'required|numeric|min:0.1',
            'salario' => 'required|numeric|min:0.1',
            'tipo' => 'required|in:organico,semiacoplado,empotrado',
        ]);

        $kloc = $request->kloc;
        $tipo = $request->tipo;
        $salario = $request->salario;

        // Constantes COCOMO
        $constantes = [
            'organico' => ['a' => 3.2, 'b' => 1.05],
            'semiacoplado' => ['a' => 3.0, 'b' => 1.12],
            'empotrado' => ['a' => 3.6, 'b' => 1.20],
        ];

        // Factores de costo
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

        // Capturar solo los factores seleccionados por el usuario (no vacíos ni guiones)
        $factoresInput = collect($request->except(['_token', 'kloc', 'tipo', 'salario']))
            ->filter(function ($valor) {
                return !in_array($valor, ['-', null, '']);
            })
            ->toArray();

        // Calcular EAF y armar procedimiento de factores
        $eaf = 1.0;
        $detalleFactores = "";
        foreach ($factoresInput as $clave => $nivel) {
            if (isset($factores[$clave][$nivel])) {
                $valor = $factores[$clave][$nivel];
                $detalleFactores .= "$clave ($nivel) = $valor\n";
                $eaf *= $valor;
            }
        }

        $a = $constantes[$tipo]['a'];
        $b = $constantes[$tipo]['b'];

        // Calcular esfuerzo (PM)
        $esfuerzo = $a * pow($kloc, $b) * $eaf;

        // Calcular duración en meses
        $duracion = 2.5 * pow($esfuerzo, 0.35);

        // Calcular número de personas
        $personas = $esfuerzo / $duracion;

        // Calcular costo total
        $costo_total = $personas * $salario * $duracion;

        // Crear procedimiento detallado
        $procedimiento = "=== Procedimiento COCOMO I ===\n";
        $procedimiento .= "Tipo de proyecto: $tipo\n";
        $procedimiento .= "KLOC: $kloc\n";
        $procedimiento .= "Constantes: a = $a, b = $b\n\n";
        $procedimiento .= "--- Factores seleccionados ---\n";
        $procedimiento .= $detalleFactores . "\n";
        $procedimiento .= "EAF calculado = $eaf\n";
        $procedimiento .= "\n--- Cálculos ---\n";
        $procedimiento .= "Esfuerzo (PM) = a * (KLOC ^ b) * EAF = $a * ($kloc ^ $b) * $eaf = $esfuerzo PM\n";
        $procedimiento .= "Duración (meses) = 2.5 * (PM ^ 0.35) = $duracion\n";
        $procedimiento .= "Personas = PM / Duración = $personas\n";
        $procedimiento .= "Costo total = Personas * Salario * Duración = $costo_total\n";

        // Guardar en BD
        Calculo::create([
            'kloc' => $kloc,
            'tipo' => $tipo,
            'salario' => $salario,
            'eaf' => $eaf,
            'esfuerzo' => $esfuerzo,
            'duracion' => $duracion,
            'personas' => $personas,
            'costo_total' => $costo_total,
            'factores' => json_encode($factoresInput),
            'procedimiento' => $procedimiento, // ✅ guardamos el procedimiento
        ]);

        return redirect('/')->with('success', 'Cálculo guardado correctamente.');
    }

    public function eliminar($id)
{
    $calculo = Calculo::findOrFail($id);
    $calculo->delete();

    return redirect('/')->with('success', 'Cálculo eliminado correctamente.');
}

}







