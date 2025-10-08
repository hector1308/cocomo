<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('calculos', function (Blueprint $table) {
        $table->id();
        $table->float('kloc');
        $table->string('tipo');
        $table->float('salario');
        $table->float('eaf');
        $table->float('esfuerzo');
        $table->float('duracion');
        $table->float('personas');
        $table->float('costo_total');
        $table->json('factores');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculos');
    }
};
