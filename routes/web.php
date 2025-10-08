<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CocomoController;

Route::get('/', [CocomoController::class, 'index']);
Route::post('/calcular', [CocomoController::class, 'calcular'])->name('calcular');


