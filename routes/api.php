<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\DestinatarioController;
use App\Http\Controllers\Api\DireccionController;
use App\Http\Controllers\Api\SolicitudController;
use App\Http\Controllers\Api\CommonController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// clientes - auth
Route::post('/auth/login', [ClienteController::class, 'login']);
Route::post('/auth/registrarse', [ClienteController::class, 'register']);

Route::middleware('hastoken')->group(function () {
    
    // empresas
    Route::get('/empresas', [EmpresaController::class, 'index']);
    Route::post('/empresas', [EmpresaController::class, 'agregar']);
    Route::get('/empresas/{empresa_id}', [EmpresaController::class, 'detalles']);
    Route::put('/empresas/{empresa_id}', [EmpresaController::class, 'editar']);
    Route::delete('/empresas/{empresa_id}', [EmpresaController::class, 'eliminar']);
    
    // sucursales
    Route::get('/sucursales', [SucursalController::class, 'index']);
    Route::post('/sucursales', [SucursalController::class, 'agregar']);
    Route::get('/sucursales/{sucursal_id}', [SucursalController::class, 'detalles']);
    Route::put('/sucursales/{sucursal_id}', [SucursalController::class, 'editar']);
    Route::delete('/sucursales/{sucursal_id}', [SucursalController::class, 'eliminar']);
    
    // destinatarios
    Route::get('/destinatarios', [DestinatarioController::class, 'index']);
    Route::post('/destinatarios', [DestinatarioController::class, 'agregar']);
    Route::get('/destinatarios/{destinatario_id}', [DestinatarioController::class, 'detalles']);
    Route::put('/destinatarios/{destinatario_id}', [DestinatarioController::class, 'editar']);
    Route::delete('/destinatarios/{destinatario_id}', [DestinatarioController::class, 'eliminar']);
    
    // direcciones
    Route::post('/direcciones', [DireccionController::class, 'agregar']);
    Route::put('/direcciones/{direccion_id}', [DireccionController::class, 'editar']);
    Route::delete('/direcciones/{direccion_id}', [DireccionController::class, 'eliminar']);
    
    // solicitudes
    Route::get('/solicitudes', [SolicitudController::class, 'index']);
    Route::post('/solicitudes', [SolicitudController::class, 'agregar']);
    Route::get('/solicitudes/{solicitud_id}', [SolicitudController::class, 'detalles']);
    Route::delete('/solicitudes/{solicitud_id}', [SolicitudController::class, 'eliminar']);

    // common
    Route::get('/departamentos', [CommonController::class, 'listarDepartamentos']);
    Route::get('/municipios', [CommonController::class, 'listarMunicipios']);

    Route::get('/rutas', [CommonController::class, 'listarRutas']);
    Route::get('/departamentos-por-ruta/{id}', [CommonController::class, 'listarDepartamentosPorRuta']);
    Route::get('/municipios-por-ruta-departamento/{ruta_id}/{dep_id}', [CommonController::class, 'listarMunicipiosPorRutaYDepartamento']);

});
