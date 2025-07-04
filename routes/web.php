<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewerController;
use App\Http\Controllers\AicedronesdiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

//Route::get('/viewer/{id}', [App\Http\Controllers\PublicController::class, 'viewer'])->name('viewer');
//Route::get('/visor/{id}', [App\Http\Controllers\PublicController::class, 'visor'])->name('visor');

Route::get('/visor2D', [App\Http\Controllers\PublicController::class, 'visor2D'])->name('visor2D');
Route::get('/visor2', [App\Http\Controllers\AicedronesdiController::class, 'visor2'])->name('visor2');
Route::get('/ueview',[App\Http\Controllers\HomeController::class, 'ueview'])->name('ueview');
Route::get('/incendio',[App\Http\Controllers\HomeController::class, 'incendio'])->name('incendio');
Route::get('/inundacion',[App\Http\Controllers\HomeController::class, 'inundacion'])->name('inundacion');
Auth::routes();

Route::middleware(['auth'])->group(function() {
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);

    Route::get('/visor', [App\Http\Controllers\AicedronesdiController::class, 'visor'])->name('visor');
    
    Route::get('/visorProyecto/{id}', [App\Http\Controllers\AicedronesdiController::class, 'visorProyecto'])->name('visorProyecto');
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/home', 'index')->name('home');
        Route::redirect('/', '/home');
    });

    /*Route::controller(ViewerController::class)->group(function () {
        Route::get('/viewer', 'index');
    });*/
    Route::get('/proyectos', [App\Http\Controllers\AicedronesdiController::class, 'proyectos_'])->name('proyectos_');
    Route::post('/obtenerCapas', 'App\Http\Controllers\AicedronesdiController@obtenerCapas')->name('obtenerCapas');
    Route::post('/getFeatureInfo', 'App\Http\Controllers\AicedronesdiController@getFeatureInfo')->name('getFeatureInfo');
   // Route::post('/visor2/queryFotovoltaica', 'App\Http\Controllers\AicedronesdiController@queryFotovoltaica')->name('queryFotovoltaica');
    Route::post('/queryFotovoltaica', [AicedronesdiController::class, 'queryFotovoltaica'])->name('queryFotovoltaica');
    Route::post('/isWmsEmpty', 'App\Http\Controllers\AicedronesdiController@isWmsEmpty')->name('isWmsEmpty');

    Route::post('/obtenerProyectos', 'App\Http\Controllers\AicedronesdiController@obtenerProyectos')->name('obtenerProyectos');
    Route::post('/guardarProyecto', 'App\Http\Controllers\AicedronesdiController@guardarProyecto')->name('guardarProyecto');
    Route::post('/actualizarProyecto', 'App\Http\Controllers\AicedronesdiController@actualizarProyecto')->name('actualizarProyecto');
    Route::post('/eliminarProyecto', 'App\Http\Controllers\AicedronesdiController@eliminarProyecto')->name('eliminarProyecto');
    Route::post('/cargarProyecto', 'App\Http\Controllers\AicedronesdiController@cargarProyecto')->name('cargarProyecto');

    Route::post('/anadirCapa', 'App\Http\Controllers\AicedronesdiController@anadirCapa')->name('anadirCapa');
    Route::post('/eliminarCapa', 'App\Http\Controllers\AicedronesdiController@eliminarCapa')->name('eliminarCapa');
    Route::post('/descargarCapa', 'App\Http\Controllers\AicedronesdiController@descargarCapa')->name('descargarCapa');
    Route::post('/obtenerExtensionCapa', 'App\Http\Controllers\AicedronesdiController@obtenerExtensionCapa')->name('obtenerExtensionCapa');
    Route::post('/generarCache', 'App\Http\Controllers\AicedronesdiController@generarCache')->name('generarCache');
    Route::post('/obtenerEstadoGenerarCache', 'App\Http\Controllers\AicedronesdiController@obtenerEstadoGenerarCache')->name('obtenerEstadoGenerarCache');
    Route::post('/anadirProducto', 'App\Http\Controllers\AicedronesdiController@anadirProducto')->name('anadirProducto');
    Route::post('/obtenerTrabajosProyecto', 'App\Http\Controllers\AicedronesdiController@obtenerTrabajosProyecto')->name('obtenerTrabajosProyecto');
    Route::post('/getPotreeStatus', 'App\Http\Controllers\AicedronesdiController@getPotreeStatus')->name('getPotreeStatus');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('elfinder2', [App\Http\Controllers\Elfinder2Controller::class, 'showIndex'])->name('elfinder2.index');
    Route::get('elfinder2/connector', [App\Http\Controllers\Elfinder2Controller::class, 'showConnector'])->name('elfinder2.connector');
    Route::post('elfinder2/connector', [App\Http\Controllers\Elfinder2Controller::class, 'showConnectorPost'])->name('elfinder2.connector');
    Route::get('gestorDeArchivos/{id}', [App\Http\Controllers\FileManagerController::class, 'gestorDeArchivos'])->name('gestorDeArchivos');
    Route::get('fileManager', [App\Http\Controllers\FileManagerController::class, 'index'])->name('fileManager');
    Route::post('getFiles', 'App\Http\Controllers\FileManagerController@getFiles')->name('getFiles');

    Route::resource('capas', App\Http\Controllers\CapasController::class);
    Route::get('obtenerCapas', 'App\Http\Controllers\CapasController@obtenerCapas')->name('obtenerCapas');
    Route::resource('grupos', App\Http\Controllers\GruposController::class);
    Route::get('obtenerGrupos', 'App\Http\Controllers\GruposController@obtenerGrupos')->name('obtenerGrupos');

    // Proyectos
    Route::resource('proyectos', App\Http\Controllers\ProyectosController::class);
    Route::get('obtenerProyectos', 'App\Http\Controllers\ProyectosController@obtenerProyectos')->name('obtenerProyectos');
    Route::get('editarProyecto/{id}', 'App\Http\Controllers\ProyectosController@editar')->name('editarProyecto');
    // FIN - Proyectos

});
