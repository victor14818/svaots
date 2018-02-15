<?php

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

Auth::routes();

Route::get('/', 'ProyectosController@lst_proyectos');

Route::get('nuevatarea', 'TareasController@showFormularioGenerico');

Route::post('nuevatareaingreso', 'TareasController@ingresoTarea');

Route::get('nvotconfirmacion/email/{email}/seq/{confirm_token}','TareasController@confirmarCorreo');

Route::get('buscartarea/tarea/{tarea}/email/{email}/seq/{confirm_token}', 'BusquedasController@buscarTarea');

Route::post('buscar_tarea', 'BusquedasController@buscar_tarea');

Route::get('download/{filename}', function($filename)
{
    $relative_path = 'tmp_files/'. $filename;
    $absolute_path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $relative_path;
	
    if (file_exists($absolute_path))
    {
        return response()->download($absolute_path);
    }
    else
    {

        exit('Requested file does not exist on our server! ' . $absolute_path);
    }
});

Route::post('/download','AdjuntosController@descargar_adjunto');

Route::get('informacion','ProyectosController@lst_informacion');

Route::get('/home', 'HomeController@index')->name('home');

Route::post('cerrar_tarea', 'BusquedasController@cerrar_tarea');

Route::get('encuesta/tarea/{tarea}/seq/{seq}', 'EncuestaController@showencuesta');

Route::post('nvencuestaingreso', 'EncuestaController@ingreso_encuesta');


Route::get('prueba', 'TareasController@pruebaemail');



