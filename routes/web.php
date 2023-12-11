<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\ClienteController;

Route::get('/', function () {
    return view('index');
});
//p
Route::post('/modificarCliente/{cuit}', [ClienteController::class, 'modificarCliente'])->name('modificarCliente');
Route::get('/buscarCliente', function(){
    return view('modificarCliente/Cliente');
});
//Route::post('/modificarCliente', [ClienteController::class, 'modificarCliente'])->name('modificarCliente');

Route::get('/crearCliente', function () {
    return view('crearCliente');
});

Route::post('/import', [CsvController::class, 'procesarCSV'])->name('process.csv');
Route::post('/buscarCliente', [ClienteController::class, 'buscarCliente'])->name('buscarCliente');
Route::post('/crearCliente', [ClienteController::class, 'altaCliente'])->name('altaCliente');
